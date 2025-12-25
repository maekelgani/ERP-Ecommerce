<?php
// Suppress ALL warnings/notices that might interfere with JSON response
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

// Start clean buffer before anything
ob_start();

// Set JSON response headers BEFORE any includes
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../../config/config.php';

// Clear all output buffers completely
while (ob_get_level() > 0) {
    ob_end_clean();
}

use App\Database\DatabaseConnection;

// Only call session_start if session not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

$customerId = $_SESSION['customer_id'];

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['order_id'] ?? null;
$reason = trim($input['reason'] ?? 'Dibatalkan oleh pelanggan');

if (!$orderId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Order ID diperlukan'
    ]);
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("
        SELECT o.*, p.id_payment, p.status_pembayaran, p.metode_pembayaran
        FROM orders o
        LEFT JOIN payment p ON o.id_order = p.id_order
        WHERE o.id_order = :order_id AND o.id_customer = :customer_id
    ");
    $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak ditemukan'
        ]);
        exit;
    }

    $allowedOrderStatuses = ['pending', 'dikonfirmasi', 'diproses'];
    $allowedPaymentStatuses = ['pending', 'verifikasi', 'berhasil'];

    if (!in_array($order['status_order'], $allowedOrderStatuses)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak dapat dibatalkan. Status pesanan saat ini: ' . ucfirst($order['status_order'])
        ]);
        exit;
    }

    if ($order['status_pembayaran'] && !in_array($order['status_pembayaran'], $allowedPaymentStatuses)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Pembayaran tidak dapat dibatalkan. Status pembayaran: ' . ucfirst($order['status_pembayaran'])
        ]);
        exit;
    }

    $db->beginTransaction();

    try {
        $cancelTimestamp = date('Y-m-d H:i:s');
        $cancelNote = "\n[Dibatalkan: {$cancelTimestamp}] {$reason}";

        $newCatatanOrder = ($order['catatan_order'] ?? '') . $cancelNote;

        $updateOrder = $db->prepare("
            UPDATE orders 
            SET status_order = 'dibatalkan',
                catatan_order = :catatan
            WHERE id_order = :order_id
        ");
        $updateOrder->execute([
            ':order_id' => $orderId,
            ':catatan' => $newCatatanOrder
        ]);

        if ($order['id_payment']) {
            $paymentQuery = $db->prepare("SELECT catatan_pembayaran FROM payment WHERE id_payment = :payment_id");
            $paymentQuery->execute([':payment_id' => $order['id_payment']]);
            $paymentData = $paymentQuery->fetch(PDO::FETCH_ASSOC);
            $newCatatanPembayaran = ($paymentData['catatan_pembayaran'] ?? '') . $cancelNote;

            $updatePayment = $db->prepare("
                UPDATE payment 
                SET status_pembayaran = 'gagal',
                    catatan_pembayaran = :catatan
                WHERE id_payment = :payment_id
            ");
            $updatePayment->execute([
                ':payment_id' => $order['id_payment'],
                ':catatan' => $newCatatanPembayaran
            ]);
        }

        $checkShipment = $db->prepare("SELECT id_shipment FROM shipment WHERE id_order = :order_id");
        $checkShipment->execute([':order_id' => $orderId]);
        if ($checkShipment->fetch()) {
            $updateShipment = $db->prepare("
                UPDATE shipment 
                SET status_pengiriman = 'pending'
                WHERE id_order = :order_id
            ");
            $updateShipment->execute([':order_id' => $orderId]);
        }

        $checkVoucherUsage = $db->prepare("
            SELECT pv.id_penggunaan, pv.id_voucher, pv.jumlah_diskon
            FROM penggunaan_voucher pv
            WHERE pv.id_pesanan = :order_id
        ");
        $checkVoucherUsage->execute([':order_id' => $orderId]);
        $voucherUsage = $checkVoucherUsage->fetch(PDO::FETCH_ASSOC);

        if ($voucherUsage) {
            $deleteVoucherUsage = $db->prepare("
                DELETE FROM penggunaan_voucher 
                WHERE id_penggunaan = :id_penggunaan
            ");
            $deleteVoucherUsage->execute([':id_penggunaan' => $voucherUsage['id_penggunaan']]);

            $restoreVoucherQuota = $db->prepare("
                UPDATE voucher 
                SET kuota_terpakai = GREATEST(0, kuota_terpakai - 1)
                WHERE id_voucher = :id_voucher
            ");
            $restoreVoucherQuota->execute([':id_voucher' => $voucherUsage['id_voucher']]);
        }

        try {
            $checkTable = $db->query("SELECT 1 FROM order_cancellations LIMIT 1");
            if ($checkTable !== false) {
                $insertCancellation = $db->prepare("
                    INSERT INTO order_cancellations (id_order, id_customer, alasan_pembatalan, dibatalkan_oleh, cancelled_at)
                    VALUES (:order_id, :customer_id, :reason, 'customer', NOW())
                ");
                $insertCancellation->execute([
                    ':order_id' => $orderId,
                    ':customer_id' => $customerId,
                    ':reason' => $reason
                ]);
            }
        } catch (PDOException $e) {
        }

        $notificationId = 'NOTIF' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 8));
        $insertNotification = $db->prepare("
            INSERT INTO notification (id_notifikasi, id_customer, id_order, tipe_notifikasi, judul_pesan, isi_pesan, status_baca, tanggal_dikirim)
            VALUES (:id_notif, :customer_id, :order_id, 'order', 'Pesanan Dibatalkan', :message, 'belum_dibaca', NOW())
        ");
        $insertNotification->execute([
            ':id_notif' => $notificationId,
            ':customer_id' => $customerId,
            ':order_id' => $orderId,
            ':message' => "Pesanan #{$orderId} telah dibatalkan. Alasan: {$reason}"
        ]);

        $db->commit();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pembayaran berhasil dibatalkan',
            'data' => [
                'order_id' => $orderId,
                'status_order' => 'dibatalkan',
                'status_pembayaran' => 'gagal',
                'cancelled_at' => $cancelTimestamp,
                'voucher_restored' => $voucherUsage ? true : false
            ]
        ]);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
} catch (PDOException $e) {
    error_log('Cancel Payment PDO Error: ' . $e->getMessage() . ' | Code: ' . $e->getCode());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan database. Silakan coba lagi.',
        'debug' => defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : null
    ]);
    exit;
} catch (Exception $e) {
    error_log('Cancel Payment Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan. Silakan coba lagi.',
        'debug' => defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : null
    ]);
    exit;
}
