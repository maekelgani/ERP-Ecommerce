<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

session_start();

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
        SELECT o.*, s.id_shipment, s.status_pengiriman
        FROM orders o
        LEFT JOIN shipment s ON o.id_order = s.id_order
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
    
    if ($order['status_order'] !== 'dikirim') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak dapat dikonfirmasi. Status: ' . ucfirst($order['status_order'])
        ]);
        exit;
    }
    
    $db->beginTransaction();
    
    try {
        $updateOrder = $db->prepare("
            UPDATE orders 
            SET status_order = 'selesai'
            WHERE id_order = :order_id
        ");
        $updateOrder->execute([':order_id' => $orderId]);
        
        if ($order['id_shipment']) {
            $updateShipment = $db->prepare("
                UPDATE shipment 
                SET status_pengiriman = 'diterima',
                    tanggal_diterima = NOW()
                WHERE id_shipment = :shipment_id
            ");
            $updateShipment->execute([':shipment_id' => $order['id_shipment']]);
        }
        
        $notificationId = 'NOTIF' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 8));
        $insertNotification = $db->prepare("
            INSERT INTO notification (id_notifikasi, id_customer, id_order, tipe_notifikasi, judul_pesan, isi_pesan, status_baca, tanggal_dikirim)
            VALUES (:id_notif, :customer_id, :order_id, 'order', 'Pesanan Selesai', :message, 'belum_dibaca', NOW())
        ");
        $insertNotification->execute([
            ':id_notif' => $notificationId,
            ':customer_id' => $customerId,
            ':order_id' => $orderId,
            ':message' => "Pesanan #{$orderId} telah selesai. Terima kasih telah berbelanja!"
        ]);
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Pesanan dikonfirmasi selesai',
            'data' => [
                'order_id' => $orderId,
                'status_order' => 'selesai',
                'confirmed_at' => date('Y-m-d H:i:s')
            ]
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log('Confirm Received Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan database. Silakan coba lagi.'
    ]);
} catch (Exception $e) {
    error_log('Confirm Received Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan. Silakan coba lagi.'
    ]);
}
