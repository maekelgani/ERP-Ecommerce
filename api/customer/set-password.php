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

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $customerRepo = new CustomerRepository();
    $customer = $customerRepo->getById($customerId);

    if (!$customer) {
        throw new Exception('Customer tidak ditemukan');
    }

    if ($customer['login_type'] !== 'google') {
        throw new Exception('Fitur ini hanya untuk akun Google');
    }

    if (!empty($customer['password_hash'])) {
        throw new Exception('Anda sudah memiliki password. Gunakan fitur ubah password.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    $newPassword = $input['new_password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';

    if (empty($newPassword)) {
        throw new Exception('Password tidak boleh kosong');
    }

    if (strlen($newPassword) < 8) {
        throw new Exception('Password minimal 8 karakter');
    }

    if ($newPassword !== $confirmPassword) {
        throw new Exception('Konfirmasi password tidak cocok');
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $success = $customerRepo->setPasswordForGoogleUser($customerId, $hashedPassword);

    if (!$success) {
        throw new Exception('Gagal menyimpan password. Silakan coba lagi.');
    }

    $response['success'] = true;
    $response['message'] = 'Password berhasil ditambahkan! Sekarang Anda bisa login dengan email dan password.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
