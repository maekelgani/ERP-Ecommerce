<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\OrderRepository;

AuthMiddleware::requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$orderId = $input['order_id'] ?? '';
$status = $input['status'] ?? '';

if (empty($orderId) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $orderRepo = new OrderRepository();
    $result = $orderRepo->updateOrderStatus($orderId, $status);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Status order berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status order']);
    }
} catch (Exception $e) {
    error_log('Update order status error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
}
