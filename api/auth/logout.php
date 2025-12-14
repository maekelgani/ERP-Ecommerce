<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../../config/config.php';

use App\Auth\SessionManager;
use App\Auth\CustomerRepository;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!SessionManager::isCustomerLoggedIn()) {
        $response['success'] = true;
        $response['message'] = 'Sudah logout';
        $response['redirect'] = '/view/users/landingPage.php';
        echo json_encode($response);
        exit;
    }

    $customer = SessionManager::getCurrentCustomer();

    if ($customer && isset($customer['id_customer'])) {
        try {
            $customerRepo = new CustomerRepository();
            $customerRepo->clearRememberToken($customer['id_customer']);
        } catch (Exception $e) {
            error_log('Failed to clear remember token: ' . $e->getMessage());
        }
    }

    $response['success'] = true;
    $response['message'] = 'Logout berhasil';
    $response['redirect'] = '../../view/users/landingPage.php';

    echo json_encode($response);

    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();
    }

    SessionManager::destroySession();
    exit;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
