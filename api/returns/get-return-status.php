<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

\App\Auth\CustomerAuthMiddleware::requireLogin();

$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();
$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Order ID diperlukan']);
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $query = $db->prepare("
        SELECT r.*, o.id_order
        FROM return_request r
        JOIN orders o ON r.id_order = o.id_order
        WHERE r.id_order = :order_id AND r.id_customer = :customer_id
        ORDER BY r.tanggal_pengajuan DESC
        LIMIT 1
    ");
    $query->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $returnRequest = $query->fetch(PDO::FETCH_ASSOC);

    if ($returnRequest) {
        echo json_encode([
            'success' => true,
            'has_return' => true,
            'data' => $returnRequest
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'has_return' => false,
            'data' => null
        ]);
    }
} catch (PDOException $e) {
    error_log('Get Return Status Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
