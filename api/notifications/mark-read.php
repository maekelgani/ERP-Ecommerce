<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

session_start();

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

$input = json_decode(file_get_contents('php://input'), true);
$notificationId = $input['notification_id'] ?? null;
$markAll = $input['mark_all'] ?? false;

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    if ($markAll) {
        $stmt = $db->prepare("UPDATE notification SET status_baca = 'dibaca' WHERE id_customer = :customer_id AND status_baca = 'belum_dibaca'");
        $stmt->execute([':customer_id' => $customerId]);
        
        echo json_encode(['success' => true, 'message' => 'Semua notifikasi ditandai dibaca']);
    } elseif ($notificationId) {
        $stmt = $db->prepare("UPDATE notification SET status_baca = 'dibaca' WHERE id_notifikasi = :id AND id_customer = :customer_id");
        $stmt->execute([':id' => $notificationId, ':customer_id' => $customerId]);
        
        echo json_encode(['success' => true, 'message' => 'Notifikasi ditandai dibaca']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    }
} catch (PDOException $e) {
    error_log('Mark notification read error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
}
