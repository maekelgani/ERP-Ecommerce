<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;
use PDO;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    if (!$customerId) {
        throw new Exception('Session tidak valid');
    }

    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT profile_image FROM customers WHERE id_customer = :id");
    $stmt->execute([':id' => $customerId]);
    $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentData || empty($currentData['profile_image'])) {
        throw new Exception('Tidak ada foto profil untuk dihapus');
    }

    $uploadDir = __DIR__ . '/../../uploads/customers/';
    $oldFile = $uploadDir . $currentData['profile_image'];
    
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }

    $stmt = $db->prepare("UPDATE customers SET profile_image = NULL, updated_at = NOW() WHERE id_customer = :id");
    $result = $stmt->execute([':id' => $customerId]);

    if (!$result) {
        throw new Exception('Gagal menghapus dari database');
    }

    $response['success'] = true;
    $response['message'] = 'Foto profil berhasil dihapus';

} catch (Exception $e) {
    error_log('Remove photo error: ' . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
