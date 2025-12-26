<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Database\DatabaseConnection;

AuthMiddleware::requireAdminLogin();

$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Order ID diperlukan']);
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();
    
    // Fetch order with customer info
    $orderQuery = $db->prepare("
        SELECT 
            o.id_order,
            o.id_customer,
            o.tanggal_order,
            o.total_harga,
            o.total_diskon,
            o.total_ongkir,
            o.total_bayar,
            o.status_order,
            o.catatan_order,
            o.bubble_wrap,
            o.packing_kayu,
            o.biaya_packing,
            o.shipping_method,
            c.nama_lengkap as customer_name,
            c.email as customer_email,
            c.nomor_hp as customer_phone
        FROM orders o
        LEFT JOIN customers c ON o.id_customer = c.id_customer
        WHERE o.id_order = :order_id
    ");
    $orderQuery->bindValue(':order_id', $orderId);
    $orderQuery->execute();
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
        exit;
    }

    // Fetch order items/details
    $itemsQuery = $db->prepare("
        SELECT 
            id_detail,
            id_product,
            nama_product,
            harga_satuan,
            diskon_satuan,
            harga_setelah_diskon,
            jumlah,
            subtotal
        FROM order_detail
        WHERE id_order = :order_id
        ORDER BY id_detail ASC
    ");
    $itemsQuery->bindValue(':order_id', $orderId);
    $itemsQuery->execute();
    $items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch payment info
    $paymentQuery = $db->prepare("
        SELECT 
            id_payment,
            status_pembayaran,
            metode_pembayaran,
            nominal_pembayaran,
            tanggal_pembayaran,
            bukti_pembayaran
        FROM payment
        WHERE id_order = :order_id
        ORDER BY tanggal_pembayaran DESC
        LIMIT 1
    ");
    $paymentQuery->bindValue(':order_id', $orderId);
    $paymentQuery->execute();
    $payment = $paymentQuery->fetch(PDO::FETCH_ASSOC);

    $response = [
        'success' => true,
        'data' => [
            'order' => $order,
            'items' => $items,
            'payment' => $payment
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    error_log('Get order detail error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data']);
}
