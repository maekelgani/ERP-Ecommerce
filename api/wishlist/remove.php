<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\SessionManager;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!SessionManager::isCustomerLoggedIn()) {
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'] ?? null;

    if (empty($productId)) {
        throw new Exception('Product ID diperlukan');
    }

    $customer = SessionManager::getCurrentCustomer();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmt = $db->prepare("DELETE FROM wishlist WHERE id_customer = :customer_id AND id_product = :product_id");
    $stmt->execute([
        ':customer_id' => $customer['id_customer'],
        ':product_id' => $productId
    ]);

    $response['success'] = true;
    $response['message'] = 'Produk berhasil dihapus dari wishlist';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
