<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\OrderRepository;

AuthMiddleware::requireAdminLogin();

$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Order ID diperlukan']);
    exit;
}

try {
    $orderRepo = new OrderRepository();
    $order = $orderRepo->getOrderById($orderId);

    if ($order) {
        echo json_encode(['success' => true, 'data' => $order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
    }
} catch (Exception $e) {
    error_log('Get order detail error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
}
