<?php

namespace App\Auth;

class AuthMiddleware
{
    public static function requireCustomerLogin(): void
    {
        if (!SessionManager::isCustomerLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public static function requireAdminLogin(): void
    {
        $adminRepo = new AdminRepository();
        $sessionStatus = SessionManager::checkAdminSessionStatus();

        if ($sessionStatus === 'expired') {
            if (SessionManager::tryRestoreAdminSessionFromRemember($adminRepo)) {
                return;
            }

            SessionManager::destroySession();
            header('Location: ../../view/login-admin.php?session=expired');
            exit;
        }

        if ($sessionStatus === 'not_logged_in') {
            if (!SessionManager::tryRestoreAdminSessionFromRemember($adminRepo)) {
                header('Location: ../../view/login-admin.php');
                exit;
            }
        }

        // Enforce role requirement - admin MUST have a role
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin || !isset($admin['id_role']) || $admin['id_role'] === null) {
            SessionManager::destroySession();
            header('Location: ../../view/login-admin.php?error=no_role');
            exit;
        }

        if (SessionManager::isRememberMeActive()) {
            SessionManager::refreshAdminSession();
        }
    }

    public static function requireAdminLoginFromView(): void
    {
        $adminRepo = new AdminRepository();
        $sessionStatus = SessionManager::checkAdminSessionStatus();

        if ($sessionStatus === 'expired') {
            if (SessionManager::tryRestoreAdminSessionFromRemember($adminRepo)) {
                return;
            }

            SessionManager::destroySession();
            header('Location: ../login-admin.php?session=expired');
            exit;
        }

        if ($sessionStatus === 'not_logged_in') {
            if (!SessionManager::tryRestoreAdminSessionFromRemember($adminRepo)) {
                header('Location: ../login-admin.php');
                exit;
            }
        }

        // Enforce role requirement - admin MUST have a role
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin || !isset($admin['id_role']) || $admin['id_role'] === null) {
            SessionManager::destroySession();
            header('Location: ../login-admin.php?error=no_role');
            exit;
        }

        if (SessionManager::isRememberMeActive()) {
            SessionManager::refreshAdminSession();
        }
    }

    public static function requirePermission(string $permissionKey): void
    {
        self::requireAdminLogin();

        if (!self::hasPermission($permissionKey)) {
            self::denyAccess();
        }
    }

    public static function requirePermissionFromView(string $permissionKey): void
    {
        self::requireAdminLoginFromView();

        if (!self::hasPermission($permissionKey)) {
            self::denyAccessFromView();
        }
    }

    public static function hasPermission(string $permissionKey): bool
    {
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin) {
            return false;
        }

        $roleName = $admin['role_name'] ?? null;
        if ($roleName === 'super_admin') {
            return true;
        }

        if (SessionManager::hasSessionPermission($permissionKey)) {
            return true;
        }

        $roleId = $admin['id_role'] ?? null;
        if ($roleId) {
            $adminRepo = new AdminRepository();
            return $adminRepo->hasPermissionByRoleId((int)$roleId, $permissionKey);
        }

        return false;
    }

    public static function requireRole(string $roleName): void
    {
        self::requireAdminLogin();

        if (!self::hasRole($roleName)) {
            self::denyAccess();
        }
    }

    public static function requireRoleFromView(string $roleName): void
    {
        self::requireAdminLoginFromView();

        if (!self::hasRole($roleName)) {
            self::denyAccessFromView();
        }
    }

    public static function hasRole(string $roleName): bool
    {
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin) {
            return false;
        }

        return ($admin['role_name'] ?? '') === $roleName;
    }

    public static function requireRoles(array $roleNames): void
    {
        self::requireAdminLogin();

        $admin = SessionManager::getCurrentAdmin();
        if (!$admin || !in_array($admin['role_name'] ?? '', $roleNames)) {
            self::denyAccess();
        }
    }

    public static function requireRolesFromView(array $roleNames): void
    {
        self::requireAdminLoginFromView();

        $admin = SessionManager::getCurrentAdmin();
        if (!$admin || !in_array($admin['role_name'] ?? '', $roleNames)) {
            self::denyAccessFromView();
        }
    }

    public static function isSuperAdmin(): bool
    {
        $admin = SessionManager::getCurrentAdmin();
        return $admin && ($admin['role_name'] ?? '') === 'super_admin';
    }

    public static function requireSuperAdmin(): void
    {
        self::requireAdminLogin();

        if (!self::isSuperAdmin()) {
            self::denyAccess();
        }
    }

    public static function requireSuperAdminFromView(): void
    {
        self::requireAdminLoginFromView();

        if (!self::isSuperAdmin()) {
            self::denyAccessFromView();
        }
    }

    public static function denyAccess(string $redirectPath = '../../view/403.php'): void
    {
        http_response_code(403);
        header('Location: ' . $redirectPath);
        exit;
    }

    public static function denyAccessFromView(string $redirectPath = '../403.php'): void
    {
        http_response_code(403);
        header('Location: ' . $redirectPath);
        exit;
    }

    public static function denyAccessWithMessage(string $message = 'Akses Ditolak'): void
    {
        http_response_code(403);
        die('<div style="text-align:center;padding:50px;font-family:Arial,sans-serif;">
            <h1 style="color:#dc3545;">403 - Akses Ditolak</h1>
            <p>' . htmlspecialchars($message) . '</p>
            <a href="javascript:history.back()" style="color:#007bff;">Kembali</a>
        </div>');
    }

    public static function requireAnyPermission(array $permissionKeys): void
    {
        self::requireAdminLogin();

        foreach ($permissionKeys as $key) {
            if (self::hasPermission($key)) {
                return;
            }
        }

        self::denyAccess();
    }

    public static function requireAnyPermissionFromView(array $permissionKeys): void
    {
        self::requireAdminLoginFromView();

        foreach ($permissionKeys as $key) {
            if (self::hasPermission($key)) {
                return;
            }
        }

        self::denyAccessFromView();
    }

    public static function requireAllPermissions(array $permissionKeys): void
    {
        self::requireAdminLogin();

        foreach ($permissionKeys as $key) {
            if (!self::hasPermission($key)) {
                self::denyAccess();
            }
        }
    }

    public static function requireAllPermissionsFromView(array $permissionKeys): void
    {
        self::requireAdminLoginFromView();

        foreach ($permissionKeys as $key) {
            if (!self::hasPermission($key)) {
                self::denyAccessFromView();
            }
        }
    }

    public static function requireAdminPermission(string $permission): void
    {
        self::requirePermission($permission);
    }

    public static function requireAdminRole(array $roles): void
    {
        self::requireAdminLogin();

        $admin = SessionManager::getCurrentAdmin();

        if (!in_array($admin['role_name'] ?? '', $roles)) {
            http_response_code(403);
            die('Access Denied: Invalid role.');
        }
    }

    public static function requireNotLoggedIn(): void
    {
        if (SessionManager::isCustomerLoggedIn()) {
            header('Location: index.php');
            exit;
        }

        if (SessionManager::isAdminLoggedIn()) {
            header('Location: admin/DashboardAdmin.php');
            exit;
        }
    }

    public static function checkPermissionJson(string $permissionKey): void
    {
        if (!self::hasPermission($permissionKey)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
            ]);
            exit;
        }
    }

    public static function checkAdminLoginJson(): void
    {
        if (!SessionManager::isAdminLoggedIn()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
            ]);
            exit;
        }
    }
}
