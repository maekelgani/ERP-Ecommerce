<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;
use App\Auth\CustomerRepository;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    $noTelp = trim($input['no_telp'] ?? '');

    if (empty($noTelp)) {
        throw new Exception('Nomor telepon tidak boleh kosong');
    }

    if (!preg_match('/^[0-9]{10,15}$/', $noTelp)) {
        throw new Exception('Format nomor telepon tidak valid (10-15 digit angka)');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $customerRepo = new CustomerRepository();
    
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $stmtCheck = $db->prepare("SELECT id_customer FROM customers WHERE no_telp = :phone AND id_customer != :id");
    $stmtCheck->execute([':phone' => $noTelp, ':id' => $customerId]);
    if ($stmtCheck->fetch()) {
        throw new Exception('Nomor telepon sudah digunakan oleh akun lain');
    }

    $success = $customerRepo->updatePhone($customerId, $noTelp);

    if (!$success) {
        throw new Exception('Gagal menyimpan nomor telepon. Silakan coba lagi.');
    }

    $response['success'] = true;
    $response['message'] = 'Nomor telepon berhasil disimpan';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
