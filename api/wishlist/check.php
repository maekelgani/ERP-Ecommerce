<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;

$response = ['success' => false, 'wishlisted' => [], 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['success'] = true;
        $response['wishlisted'] = [];
        echo json_encode($response);
        exit;
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $input = json_decode(file_get_contents('php://input'), true);
    $productIds = $input['product_ids'] ?? [];

    if (empty($productIds)) {
        $stmt = $db->prepare("SELECT id_product FROM wishlist WHERE id_customer = :customer_id");
        $stmt->execute([':customer_id' => $customerId]);
    } else {
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $db->prepare("SELECT id_product FROM wishlist WHERE id_customer = ? AND id_product IN ($placeholders)");
        $params = array_merge([$customerId], $productIds);
        $stmt->execute($params);
    }

    $wishlistedProducts = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $response['success'] = true;
    $response['wishlisted'] = $wishlistedProducts;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
