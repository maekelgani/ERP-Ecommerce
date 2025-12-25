<?php
// Suppress ALL warnings/notices that might interfere with JSON response
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

// Start clean buffer before anything
ob_start();

// Set JSON response headers FIRST
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

require_once __DIR__ . '/../config/config.php';

// Clear all output buffers completely
while (ob_get_level() > 0) {
    ob_end_clean();
}

use App\Database\DatabaseConnection;

// Only call session_start if session not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

\App\Auth\CustomerAuthMiddleware::requireLogin('processPayment.php');

$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Order ID diperlukan']);
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $checkQuery = $db->prepare("
        SELECT o.id_order, o.status_order, p.status_pembayaran, p.id_payment
        FROM orders o
        LEFT JOIN payment p ON o.id_order = p.id_order
        WHERE o.id_order = :order_id AND o.id_customer = :customer_id
    ");
    $checkQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $checkQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }

    if ($order['status_pembayaran'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Status pembayaran sudah diproses']);
        exit;
    }

    $db->beginTransaction();

    $updatePayment = $db->prepare("
        UPDATE payment 
        SET status_pembayaran = 'berhasil',
            settlement_time = NOW()
        WHERE id_order = :order_id
    ");
    $updatePayment->execute([':order_id' => $orderId]);

    $updateOrder = $db->prepare("
        UPDATE orders 
        SET status_order = 'diproses'
        WHERE id_order = :order_id
    ");
    $updateOrder->execute([':order_id' => $orderId]);

    $notifId = 'NOTIF' . date('YmdHis') . strtoupper(substr(uniqid(), -6));
    $insertNotif = $db->prepare("
        INSERT INTO notification (id_notifikasi, id_customer, id_order, tipe_notifikasi, judul_pesan, isi_pesan, status_baca, tanggal_dikirim)
        VALUES (:id_notif, :customer_id, :order_id, 'payment', 'Pembayaran Berhasil', :isi_pesan, 'belum_dibaca', NOW())
    ");
    $insertNotif->execute([
        ':id_notif' => $notifId,
        ':customer_id' => $customerId,
        ':order_id' => $orderId,
        ':isi_pesan' => "Pembayaran untuk pesanan #{$orderId} telah berhasil. Pesanan Anda sedang diproses."
    ]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pembayaran berhasil! Pesanan sedang diproses.',
        'new_payment_status' => 'berhasil',
        'new_order_status' => 'diproses'
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
