<?php

/**
 * Logout Handler
 * File ini menangani proses logout admin/super admin dengan benar
 * Session dihapus, remember token dihapus dari database, dan user diarahkan ke login-admin.php
 */

require_once __DIR__ . '/../../config/config.php';

use App\Auth\SessionManager;
use App\Auth\AdminRepository;

// Validasi bahwa user yang akan logout adalah admin yang login
if (!SessionManager::isAdminLoggedIn()) {
    // Jika sudah logout, langsung redirect ke login
    header('Location: ../../view/login-admin.php');
    exit;
}

try {
    $adminRepo = new AdminRepository();
    $admin = SessionManager::getCurrentAdmin();

    // Clear remember token dari database jika ada
    if ($admin && isset($admin['id_admin'])) {
        $adminRepo->clearRememberToken($admin['id_admin']);
    }

    // Destroy session dan clear semua data login
    SessionManager::destroySession();

    // Set header untuk cegah caching
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Redirect ke login page dengan success message
    header('Location: ../../view/login-admin.php?logout=success');
    exit;
} catch (\Exception $e) {
    // Jika ada error, tetap hapus session dan redirect
    session_destroy();
    header('Location: ../../view/login-admin.php?logout=error');
    exit;
}
