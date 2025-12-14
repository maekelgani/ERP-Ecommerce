<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'] ?? null;
    $quantity = (int)($input['quantity'] ?? 1);

    if (empty($productId)) {
        throw new Exception('Product ID diperlukan');
    }

    if ($quantity < 1) {
        throw new Exception('Jumlah tidak valid');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    // Cek produk dan stok
    $stmtProduct = $db->prepare("
        SELECT stok, status_produk, nama_product, harga 
        FROM products 
        WHERE id_product = :id
    ");
    $stmtProduct->execute([':id' => $productId]);
    $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception('Produk tidak ditemukan');
    }

    if ($product['status_produk'] !== 'tersedia') {
        throw new Exception('Produk tidak tersedia');
    }

    if ($product['stok'] < 1) {
        throw new Exception('Stok produk habis');
    }

    // Cek apakah produk sudah ada di cart
    $stmtCheck = $db->prepare("
        SELECT id_cart, jumlah 
        FROM cart 
        WHERE id_customer = :customer_id AND id_product = :product_id
    ");
    $stmtCheck->execute([
        ':customer_id' => $customerId,
        ':product_id' => $productId
    ]);
    $existingCart = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingCart) {
        // Update quantity jika sudah ada
        $newQty = $existingCart['jumlah'] + $quantity;

        if ($newQty > $product['stok']) {
            throw new Exception('Jumlah melebihi stok tersedia (Stok: ' . $product['stok'] . ')');
        }

        // tgl_diubah akan otomatis ter-update karena ON UPDATE CURRENT_TIMESTAMP
        $stmtUpdate = $db->prepare("
            UPDATE cart 
            SET jumlah = :qty 
            WHERE id_cart = :id
        ");
        $stmtUpdate->execute([
            ':qty' => $newQty,
            ':id' => $existingCart['id_cart']
        ]);

        $response['message'] = 'Jumlah produk di keranjang diperbarui';
    } else {
        // Insert baru jika belum ada
        if ($quantity > $product['stok']) {
            throw new Exception('Jumlah melebihi stok tersedia (Stok: ' . $product['stok'] . ')');
        }

        // Generate ID cart
        $stmtMaxId = $db->query("SELECT id_cart FROM cart ORDER BY id_cart DESC LIMIT 1");
        $lastId = $stmtMaxId->fetch(PDO::FETCH_ASSOC);

        if ($lastId) {
            $num = (int)substr($lastId['id_cart'], 3) + 1;
            $newId = 'CRT' . str_pad($num, 7, '0', STR_PAD_LEFT);
        } else {
            $newId = 'CRT0000001';
        }

        // tanggal_ditambahkan dan tgl_diubah akan otomatis diisi
        $stmtInsert = $db->prepare("
            INSERT INTO cart (id_cart, id_customer, id_product, jumlah, harga_satuan) 
            VALUES (:id_cart, :customer_id, :product_id, :qty, :harga)
        ");
        $stmtInsert->execute([
            ':id_cart' => $newId,
            ':customer_id' => $customerId,
            ':product_id' => $productId,
            ':qty' => $quantity,
            ':harga' => $product['harga']
        ]);

        $response['message'] = 'Produk berhasil ditambahkan ke keranjang';
    }

    // Hitung total item di cart
    $stmtCount = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE id_customer = :customer_id");
    $stmtCount->execute([':customer_id' => $customerId]);
    $response['cart_count'] = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['count'];

    $response['success'] = true;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
