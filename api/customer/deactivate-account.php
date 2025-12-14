<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

use App\Auth\CustomerAuthMiddleware;
use App\Auth\SessionManager;
use App\Auth\CustomerRepository;

if (!CustomerAuthMiddleware::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$customerId = CustomerAuthMiddleware::getCustomerId();
$data = json_decode(file_get_contents('php://input'), true);

$confirmText = $data['confirm_text'] ?? '';
if ($confirmText !== 'NONAKTIFKAN') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Konfirmasi tidak valid. Ketik "NONAKTIFKAN" untuk melanjutkan.']);
    exit;
}

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $customerRepo = new CustomerRepository();

    $stmt = $db->prepare("UPDATE customers SET is_active = false, updated_at = NOW() WHERE id_customer = :id");
    $stmt->execute([':id' => $customerId]);

    $customerRepo->clearRememberToken($customerId);

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    setcookie('remember_token', '', time() - 42000, '/', '', true, true);

    if (session_id() !== '') {
        session_destroy();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Akun Anda telah dinonaktifkan. Anda akan diarahkan ke halaman utama.',
        'redirect' => 'landingPage.php'
    ]);
} catch (Exception $e) {
    error_log('Deactivate account error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menonaktifkan akun']);
}
