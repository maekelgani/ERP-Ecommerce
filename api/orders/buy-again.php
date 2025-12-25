<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;

$response = [
    'success' => false,
    'message' => '',
    'available_items' => [],
    'unavailable_items' => [],
    'has_unavailable' => false
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $input['order_id'] ?? null;
    $action = $input['action'] ?? 'add'; // 'preview' untuk hanya tampil, 'add' untuk masukkan ke cart

    if (empty($orderId)) {
        throw new Exception('Order ID diperlukan');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    // Verify order ownership
    $stmtOrder = $db->prepare("
        SELECT id_order FROM orders 
        WHERE id_order = :order_id AND id_customer = :customer_id
    ");
    $stmtOrder->execute([
        ':order_id' => $orderId,
        ':customer_id' => $customerId
    ]);

    if (!$stmtOrder->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Order tidak ditemukan atau tidak memiliki akses');
    }

    // Get order items with current stock information
    $stmtItems = $db->prepare("
        SELECT 
            od.id_product,
            od.nama_product,
            od.jumlah,
            od.harga_satuan,
            od.diskon_satuan,
            od.harga_setelah_diskon,
            p.stok,
            p.status_produk,
            p.gambar
        FROM order_detail od
        LEFT JOIN products p ON od.id_product = p.id_product
        WHERE od.id_order = :order_id
        ORDER BY od.id_detail ASC
    ");
    $stmtItems->execute([':order_id' => $orderId]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        throw new Exception('Detail pesanan tidak ditemukan');
    }

    // Process items and separate available from unavailable
    foreach ($items as $item) {
        $itemData = [
            'id_product' => $item['id_product'],
            'nama_product' => $item['nama_product'],
            'jumlah_sebelumnya' => (int)$item['jumlah'],
            'harga_satuan' => (float)$item['harga_satuan'],
            'diskon_satuan' => (float)$item['diskon_satuan'],
            'harga_setelah_diskon' => (float)$item['harga_setelah_diskon'],
            'gambar' => $item['gambar'],
            'stok_tersedia' => (int)$item['stok'] ?? 0,
            'status_produk' => $item['status_produk'] ?? 'habis'
        ];

        // Check if product is available (has stock and status is tersedia)
        if ($item['status_produk'] === 'tersedia' && (int)$item['stok'] > 0) {
            // Product is available, add to cart in background
            $response['available_items'][] = $itemData;
        } else {
            // Product is not available
            $itemData['alasan'] = $item['status_produk'] !== 'tersedia'
                ? 'Produk sudah ' . $item['status_produk']
                : 'Stok produk habis';
            $response['unavailable_items'][] = $itemData;
            $response['has_unavailable'] = true;
        }
    }

    // If preview mode, just return data without adding to cart
    if ($action === 'preview') {
        $response['success'] = true;
        echo json_encode($response);
        exit;
    }

    // If all items available, add them to cart
    if (!$response['has_unavailable'] && !empty($response['available_items'])) {
        foreach ($response['available_items'] as $item) {
            // Check if product already in cart
            $stmtCheck = $db->prepare("
                SELECT id_cart, jumlah 
                FROM cart 
                WHERE id_customer = :customer_id AND id_product = :product_id
            ");
            $stmtCheck->execute([
                ':customer_id' => $customerId,
                ':product_id' => $item['id_product']
            ]);
            $existingCart = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingCart) {
                // Update existing cart
                $newQty = $existingCart['jumlah'] + $item['jumlah_sebelumnya'];

                // Check stock constraint
                if ($newQty > $item['stok_tersedia']) {
                    $newQty = $item['stok_tersedia'];
                }

                $stmtUpdate = $db->prepare("
                    UPDATE cart 
                    SET jumlah = :qty 
                    WHERE id_cart = :id_cart
                ");
                $stmtUpdate->execute([
                    ':qty' => $newQty,
                    ':id_cart' => $existingCart['id_cart']
                ]);
            } else {
                // Insert new cart item
                // Generate cart ID
                $stmtMaxId = $db->query("SELECT id_cart FROM cart ORDER BY id_cart DESC LIMIT 1");
                $lastId = $stmtMaxId->fetch(PDO::FETCH_ASSOC);

                if ($lastId) {
                    $num = (int)substr($lastId['id_cart'], 3) + 1;
                    $newId = 'CRT' . str_pad($num, 7, '0', STR_PAD_LEFT);
                } else {
                    $newId = 'CRT0000001';
                }

                $quantity = min($item['jumlah_sebelumnya'], $item['stok_tersedia']);

                $stmtInsert = $db->prepare("
                    INSERT INTO cart (id_cart, id_customer, id_product, jumlah, harga_satuan) 
                    VALUES (:id_cart, :customer_id, :product_id, :qty, :harga)
                ");
                $stmtInsert->execute([
                    ':id_cart' => $newId,
                    ':customer_id' => $customerId,
                    ':product_id' => $item['id_product'],
                    ':qty' => $quantity,
                    ':harga' => $item['harga_satuan']
                ]);
            }
        }

        // Get updated cart count
        $stmtCount = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE id_customer = :customer_id");
        $stmtCount->execute([':customer_id' => $customerId]);
        $response['cart_count'] = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['count'];

        $response['success'] = true;
        $response['message'] = 'Semua produk berhasil ditambahkan ke keranjang';
    } else if (!empty($response['available_items'])) {
        // Some items available, some not
        foreach ($response['available_items'] as $item) {
            // Process available items
            $stmtCheck = $db->prepare("
                SELECT id_cart, jumlah 
                FROM cart 
                WHERE id_customer = :customer_id AND id_product = :product_id
            ");
            $stmtCheck->execute([
                ':customer_id' => $customerId,
                ':product_id' => $item['id_product']
            ]);
            $existingCart = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existingCart) {
                $newQty = $existingCart['jumlah'] + $item['jumlah_sebelumnya'];
                if ($newQty > $item['stok_tersedia']) {
                    $newQty = $item['stok_tersedia'];
                }

                $stmtUpdate = $db->prepare("
                    UPDATE cart 
                    SET jumlah = :qty 
                    WHERE id_cart = :id_cart
                ");
                $stmtUpdate->execute([
                    ':qty' => $newQty,
                    ':id_cart' => $existingCart['id_cart']
                ]);
            } else {
                $stmtMaxId = $db->query("SELECT id_cart FROM cart ORDER BY id_cart DESC LIMIT 1");
                $lastId = $stmtMaxId->fetch(PDO::FETCH_ASSOC);

                if ($lastId) {
                    $num = (int)substr($lastId['id_cart'], 3) + 1;
                    $newId = 'CRT' . str_pad($num, 7, '0', STR_PAD_LEFT);
                } else {
                    $newId = 'CRT0000001';
                }

                $quantity = min($item['jumlah_sebelumnya'], $item['stok_tersedia']);

                $stmtInsert = $db->prepare("
                    INSERT INTO cart (id_cart, id_customer, id_product, jumlah, harga_satuan) 
                    VALUES (:id_cart, :customer_id, :product_id, :qty, :harga)
                ");
                $stmtInsert->execute([
                    ':id_cart' => $newId,
                    ':customer_id' => $customerId,
                    ':product_id' => $item['id_product'],
                    ':qty' => $quantity,
                    ':harga' => $item['harga_satuan']
                ]);
            }
        }

        $stmtCount = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE id_customer = :customer_id");
        $stmtCount->execute([':customer_id' => $customerId]);
        $response['cart_count'] = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['count'];

        $response['success'] = true;
        $response['message'] = count($response['available_items']) . ' dari ' . count($items) . ' produk berhasil ditambahkan ke keranjang';
    } else {
        // All items unavailable
        $response['message'] = 'Semua produk tidak tersedia';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
