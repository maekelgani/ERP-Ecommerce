<?php

/**
 * Logout Handler
 * File ini menangani proses logout customer dengan benar
 * Session dihapus, remember token dihapus dari database, dan user diarahkan ke landingPage.php
 * Mengikuti pattern yang sama dengan sistem logout admin
 */

require_once __DIR__ . '/../config/config.php';

use App\Auth\SessionManager;
use App\Auth\CustomerRepository;

if (!SessionManager::isCustomerLoggedIn()) {
    header('Location: users/landingPage.php');
    exit;
}

try {
    $customerRepo = new CustomerRepository();

    SessionManager::destroyCustomerSession($customerRepo);

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    header('Location: users/landingPage.php?logout=success');
    exit;
} catch (\Exception $e) {
    session_destroy();
    header('Location: users/landingPage.php?logout=error');
    exit;
}
