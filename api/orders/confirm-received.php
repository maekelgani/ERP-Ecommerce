<?php
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request');
    }

    $orderId = $_POST['order_id'] ?? null;
    if (!$orderId) {
        throw new Exception('Order ID tidak valid');
    }

    $db = DatabaseConnection::getInstance()->getConnection();

    $stmt = $db->prepare("
        UPDATE orders 
        SET status_order = 'selesai'
        WHERE id_order = :id_order
    ");
    $stmt->execute([':id_order' => $orderId]);

    echo json_encode([
        'success' => true,
        'message' => 'Pesanan berhasil dikonfirmasi'
    ]);
    exit;
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
