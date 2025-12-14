<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;

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
    
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';

    if (empty($currentPassword)) {
        throw new Exception('Password saat ini tidak boleh kosong');
    }

    if (empty($newPassword)) {
        throw new Exception('Password baru tidak boleh kosong');
    }

    if (strlen($newPassword) < 8) {
        throw new Exception('Password baru minimal 8 karakter');
    }

    if ($newPassword !== $confirmPassword) {
        throw new Exception('Konfirmasi password tidak cocok');
    }

    if ($currentPassword === $newPassword) {
        throw new Exception('Password baru tidak boleh sama dengan password saat ini');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmtGet = $db->prepare("SELECT password_hash FROM customers WHERE id_customer = :id");
    $stmtGet->execute([':id' => $customerId]);
    $customer = $stmtGet->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        throw new Exception('Customer tidak ditemukan');
    }

    if (empty($customer['password_hash'])) {
        throw new Exception('Akun Anda belum memiliki password. Silakan set password terlebih dahulu.');
    }

    if (!password_verify($currentPassword, $customer['password_hash'])) {
        throw new Exception('Password saat ini tidak benar');
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $stmtUpdate = $db->prepare("
        UPDATE customers 
        SET password_hash = :password, 
            updated_at = NOW()
        WHERE id_customer = :id
    ");
    
    $stmtUpdate->execute([
        ':password' => $hashedPassword,
        ':id' => $customerId
    ]);

    $response['success'] = true;
    $response['message'] = 'Password berhasil diubah';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
