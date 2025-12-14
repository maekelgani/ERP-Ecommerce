<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
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
    $cartId = $input['cart_id'] ?? null;
    $quantity = (int)($input['quantity'] ?? 0);

    if (empty($cartId)) {
        throw new Exception('Cart ID diperlukan');
    }

    if ($quantity < 1) {
        throw new Exception('Jumlah minimal adalah 1');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmtCart = $db->prepare("
        SELECT c.id_cart, c.id_product, c.jumlah, p.stok, p.status_produk, p.harga,
               COALESCE(d.harga_setelah_diskon, p.harga) as harga_final
        FROM cart c 
        JOIN products p ON c.id_product = p.id_product 
        LEFT JOIN diskon d ON p.id_product = d.id_product 
            AND d.status = 'aktif' 
            AND NOW() BETWEEN d.tanggal_mulai AND d.tanggal_berakhir
        WHERE c.id_cart = :cart_id AND c.id_customer = :customer_id
    ");
    $stmtCart->execute([':cart_id' => $cartId, ':customer_id' => $customerId]);
    $cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        throw new Exception('Item keranjang tidak ditemukan');
    }

    if ($cart['stok'] <= 0 || $cart['status_produk'] === 'habis' || $cart['status_produk'] === 'nonaktif') {
        throw new Exception('Produk tidak tersedia');
    }

    $adjusted = false;
    if ($quantity > $cart['stok']) {
        $quantity = $cart['stok'];
        $adjusted = true;
    }

    $stmtUpdate = $db->prepare("UPDATE cart SET jumlah = :qty, harga_satuan = :harga, tgl_diubah = NOW() WHERE id_cart = :cart_id");
    $stmtUpdate->execute([':qty' => $quantity, ':harga' => $cart['harga_final'], ':cart_id' => $cartId]);

    $itemTotal = $cart['harga_final'] * $quantity;

    $stmtTotals = $db->prepare("
        SELECT 
            SUM(COALESCE(d.harga_setelah_diskon, p.harga) * c.jumlah) as subtotal,
            SUM(c.jumlah) as total_items,
            COUNT(*) as cart_count
        FROM cart c
        JOIN products p ON c.id_product = p.id_product
        LEFT JOIN diskon d ON p.id_product = d.id_product 
            AND d.status = 'aktif' 
            AND NOW() BETWEEN d.tanggal_mulai AND d.tanggal_berakhir
        WHERE c.id_customer = :customer_id
            AND p.stok > 0 
            AND p.status_produk NOT IN ('habis', 'nonaktif')
    ");
    $stmtTotals->execute([':customer_id' => $customerId]);
    $totals = $stmtTotals->fetch(PDO::FETCH_ASSOC);

    $subtotal = (float)($totals['subtotal'] ?? 0);
    $tax = $subtotal * 0.11;
    $grandTotal = $subtotal + $tax;

    $response['success'] = true;
    $response['message'] = $adjusted ? 'Jumlah disesuaikan dengan stok tersedia' : 'Jumlah diperbarui';
    $response['adjusted'] = $adjusted;
    $response['quantity'] = $quantity;
    $response['item_total'] = $itemTotal;
    $response['price'] = $cart['harga_final'];
    $response['max_stock'] = $cart['stok'];
    $response['subtotal'] = $subtotal;
    $response['tax'] = $tax;
    $response['grand_total'] = $grandTotal;
    $response['total_items'] = (int)($totals['total_items'] ?? 0);
    $response['cart_count'] = (int)($totals['cart_count'] ?? 0);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
