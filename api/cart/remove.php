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

    if (empty($cartId)) {
        throw new Exception('Cart ID diperlukan');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmt = $db->prepare("DELETE FROM cart WHERE id_cart = :cart_id AND id_customer = :customer_id");
    $stmt->execute([':cart_id' => $cartId, ':customer_id' => $customerId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Item keranjang tidak ditemukan');
    }

    $stmtTotals = $db->prepare("
        SELECT 
            COALESCE(SUM(COALESCE(d.harga_setelah_diskon, p.harga) * c.jumlah), 0) as subtotal,
            COALESCE(SUM(c.jumlah), 0) as total_items,
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
    $response['message'] = 'Produk dihapus dari keranjang';
    $response['subtotal'] = $subtotal;
    $response['tax'] = $tax;
    $response['grand_total'] = $grandTotal;
    $response['total_items'] = (int)($totals['total_items'] ?? 0);
    $response['cart_count'] = (int)($totals['cart_count'] ?? 0);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
