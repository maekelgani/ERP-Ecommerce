<?php
/**
 * Legacy Function Helper untuk backward compatibility
 * Gunakan App\Auth classes untuk functionality baru
 */

require_once __DIR__ . '/config.php';

use App\Auth\SessionManager;
use App\Auth\CustomerRepository;

// Legacy function untuk check login
function cekLogin() {
    if (!SessionManager::isCustomerLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Legacy function untuk get current user
function getCurrentUser() {
    $customer = SessionManager::getCurrentCustomer();
    
    if ($customer) {
        return [
            'id' => $customer['id_customer'],
            'nama' => $customer['name'],
            'email' => $customer['email'],
            'login_type' => $customer['login_type']
        ];
    }
    
    return null;
}

// Database connection legacy (untuk backward compatibility)
// Gunakan DatabaseConnection::getInstance()->getConnection() untuk production
if (!function_exists('getConnection')) {
    function getConnection() {
        try {
            return \App\Database\DatabaseConnection::getInstance()->getConnection();
        } catch (\Exception $e) {
            error_log('Connection Error: ' . $e->getMessage());
            return null;
        }
    }
}

// Alias untuk backward compatibility
$conn = getConnection();
?>
