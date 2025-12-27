<?php

namespace App\Auth;

class SessionManager
{
    private const SESSION_LIFETIME = 86400;
    private const ADMIN_SESSION_LIFETIME = 3600;
    private const ADMIN_SESSION_WITH_REMEMBER = 86400;
    private const CUSTOMER_SESSION_LIFETIME = 10;
    private const CUSTOMER_SESSION_WITH_REMEMBER = 86400;
    private const REMEMBER_ME_LIFETIME = 2592000;
    private const REMEMBER_ME_COOKIE_NAME = 'remember_token';

    public static function createCustomerSession(array $customer, bool $rememberMe = false): void
    {
        $_SESSION['customer_id'] = $customer['id_customer'];
        $_SESSION['email'] = $customer['email'];
        $_SESSION['name'] = $customer['nama_lengkap'];
        $_SESSION['login_type'] = $customer['login_type'];
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'customer';
        $_SESSION['login_time'] = time();
        $_SESSION['customer_remember_me_active'] = $rememberMe;

        session_regenerate_id(true);
    }

    public static function createAdminSession(array $admin, bool $rememberMe = false): void
    {
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['name'] = $admin['nama_lengkap'];
        $_SESSION['id_role'] = $admin['id_role'] ?? null;
        $_SESSION['role_name'] = $admin['role_name'] ?? 'admin';
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['login_time'] = time();
        $_SESSION['remember_me_active'] = $rememberMe;

        $adminRepo = new AdminRepository();
        if (isset($admin['id_role'])) {
            $permissions = $adminRepo->getPermissionKeysByRoleId((int)$admin['id_role']);
            self::storePermissionsToSession($permissions);
        }

        session_regenerate_id(true);
    }

    public static function storePermissionsToSession(array $permissions): void
    {
        $_SESSION['permissions'] = $permissions;
    }

    public static function getSessionPermissions(): array
    {
        return $_SESSION['permissions'] ?? [];
    }

    public static function hasSessionPermission(string $permissionKey): bool
    {
        $permissions = self::getSessionPermissions();
        return in_array($permissionKey, $permissions);
    }

    public static function refreshSessionPermissions(): void
    {
        if (!self::isAdminLoggedIn()) {
            return;
        }

        $adminId = $_SESSION['admin_id'] ?? null;
        if (!$adminId) {
            return;
        }

        $adminRepo = new AdminRepository();
        $roleId = $adminRepo->getAdminRoleId($adminId);

        if ($roleId) {
            $permissions = $adminRepo->getPermissionKeysByRoleId($roleId);
            self::storePermissionsToSession($permissions);
        }
    }

    public static function getSessionRoleId(): ?int
    {
        return isset($_SESSION['id_role']) ? (int)$_SESSION['id_role'] : null;
    }

    public static function getSessionRoleName(): ?string
    {
        return $_SESSION['role_name'] ?? null;
    }

    public static function setCustomerRememberMe(int $customerId, CustomerRepository $repo): void
    {
        $token = self::generateRememberToken();
        $expiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);

        $repo->updateRememberToken($customerId, $token, $expiresAt);

        setcookie(
            self::REMEMBER_ME_COOKIE_NAME,
            $token,
            time() + self::REMEMBER_ME_LIFETIME,
            '/',
            '',
            true,
            true
        );
    }

    public static function setAdminRememberMe(int $adminId, AdminRepository $repo): void
    {
        $token = self::generateRememberToken();
        $expiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);

        $repo->updateRememberToken($adminId, $token, $expiresAt);

        setcookie(
            self::REMEMBER_ME_COOKIE_NAME,
            $token,
            time() + self::REMEMBER_ME_LIFETIME,
            '/',
            '',
            true,
            true
        );
    }

    public static function restoreCustomerFromRememberToken(CustomerRepository $repo): ?array
    {
        if (!isset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME])) {
            return null;
        }

        $token = $_COOKIE[self::REMEMBER_ME_COOKIE_NAME];
        $customer = $repo->findByRememberToken($token);

        if ($customer) {
            $newToken = self::generateRememberToken();
            $newExpiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);

            $repo->updateRememberToken($customer['id_customer'], $newToken, $newExpiresAt);

            setcookie(
                self::REMEMBER_ME_COOKIE_NAME,
                $newToken,
                time() + self::REMEMBER_ME_LIFETIME,
                '/',
                '',
                true,
                true
            );

            return $customer;
        }

        self::clearRememberMe();
        return null;
    }

    public static function restoreAdminFromRememberToken(AdminRepository $repo): ?array
    {
        if (!isset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME])) {
            return null;
        }

        $token = $_COOKIE[self::REMEMBER_ME_COOKIE_NAME];
        $admin = $repo->findByRememberToken($token);

        if ($admin) {
            $newToken = self::generateRememberToken();
            $newExpiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);

            $repo->updateRememberToken($admin['id_admin'], $newToken, $newExpiresAt);

            setcookie(
                self::REMEMBER_ME_COOKIE_NAME,
                $newToken,
                time() + self::REMEMBER_ME_LIFETIME,
                '/',
                '',
                true,
                true
            );

            return $admin;
        }

        self::clearRememberMe();
        return null;
    }

    public static function tryRestoreAdminSessionFromRemember(AdminRepository $repo): bool
    {
        if (self::isAdminLoggedIn() && !self::isAdminSessionExpired()) {
            return true;
        }

        if (!isset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME])) {
            return false;
        }

        $token = $_COOKIE[self::REMEMBER_ME_COOKIE_NAME];
        $admin = $repo->findByRememberToken($token);

        if (!$admin) {
            self::clearRememberMe();
            return false;
        }

        $newToken = self::generateRememberToken();
        $newExpiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);
        $repo->updateRememberToken($admin['id_admin'], $newToken, $newExpiresAt);

        setcookie(
            self::REMEMBER_ME_COOKIE_NAME,
            $newToken,
            time() + self::REMEMBER_ME_LIFETIME,
            '/',
            '',
            true,
            true
        );

        self::createAdminSession($admin, true);
        return true;
    }

    public static function isCustomerLoggedIn(): bool
    {
        return isset($_SESSION['logged_in'])
            && $_SESSION['logged_in'] === true
            && $_SESSION['user_type'] === 'customer'
            && isset($_SESSION['customer_id']);
    }

    public static function isAdminLoggedIn(): bool
    {
        return isset($_SESSION['logged_in'])
            && $_SESSION['logged_in'] === true
            && $_SESSION['user_type'] === 'admin'
            && isset($_SESSION['admin_id']);
    }

    public static function isAdminSessionExpired(): bool
    {
        if (!self::isAdminLoggedIn()) {
            return false;
        }

        if (!isset($_SESSION['login_time'])) {
            return false;
        }

        $loginTime = $_SESSION['login_time'];
        $currentTime = time();
        $sessionAge = $currentTime - $loginTime;

        if (isset($_SESSION['remember_me_active']) && $_SESSION['remember_me_active'] === true) {
            $lifetime = self::ADMIN_SESSION_WITH_REMEMBER;
        } else {
            $lifetime = self::ADMIN_SESSION_LIFETIME;
        }

        return $sessionAge > $lifetime;
    }

    public static function checkAdminSessionStatus(): string
    {
        if (!self::isAdminLoggedIn()) {
            return 'not_logged_in';
        }

        if (self::isAdminSessionExpired()) {
            return 'expired';
        }

        return 'valid';
    }

    public static function refreshAdminSession(): void
    {
        if (self::isAdminLoggedIn()) {
            $_SESSION['login_time'] = time();
        }
    }

    public static function getAdminSessionRemainingTime(): int
    {
        if (!self::isAdminLoggedIn() || !isset($_SESSION['login_time'])) {
            return 0;
        }

        $loginTime = $_SESSION['login_time'];

        if (isset($_SESSION['remember_me_active']) && $_SESSION['remember_me_active'] === true) {
            $lifetime = self::ADMIN_SESSION_WITH_REMEMBER;
        } else {
            $lifetime = self::ADMIN_SESSION_LIFETIME;
        }

        $expiresAt = $loginTime + $lifetime;
        $remaining = $expiresAt - time();

        return max(0, $remaining);
    }

    public static function getAdminSessionLifetime(): int
    {
        return self::ADMIN_SESSION_LIFETIME;
    }

    public static function getCurrentCustomer(): ?array
    {
        if (!self::isCustomerLoggedIn()) {
            return null;
        }

        return [
            'id_customer' => $_SESSION['customer_id'],
            'email' => $_SESSION['email'],
            'name' => $_SESSION['name'],
            'login_type' => $_SESSION['login_type']
        ];
    }

    public static function getCurrentAdmin(): ?array
    {
        if (!self::isAdminLoggedIn()) {
            return null;
        }

        return [
            'id_admin' => $_SESSION['admin_id'],
            'email' => $_SESSION['email'],
            'name' => $_SESSION['name'],
            'id_role' => $_SESSION['id_role'] ?? null,
            'role_name' => $_SESSION['role_name'] ?? 'admin',
            'permissions' => $_SESSION['permissions'] ?? []
        ];
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroySession(): void
    {
        $_SESSION = [];

        if (session_id() !== '') {
            session_destroy();
        }

        self::clearRememberMe();
    }

    public static function clearRememberMe(): void
    {
        if (isset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME])) {
            setcookie(self::REMEMBER_ME_COOKIE_NAME, '', time() - 3600, '/');
            unset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME]);
        }
    }

    public static function destroyCustomerSession(CustomerRepository $repo): void
    {
        if (self::isCustomerLoggedIn()) {
            $customerId = $_SESSION['customer_id'] ?? null;
            if ($customerId) {
                $repo->clearRememberToken((int)$customerId);
            }
        }

        $_SESSION = [];

        if (session_id() !== '') {
            session_destroy();
        }

        self::clearRememberMe();
    }

    public static function isRememberMeActive(): bool
    {
        if (!self::isAdminLoggedIn()) {
            return false;
        }
        return isset($_SESSION['remember_me_active']) && $_SESSION['remember_me_active'] === true;
    }

    public static function isCustomerRememberMeActive(): bool
    {
        if (!self::isCustomerLoggedIn()) {
            return false;
        }
        return isset($_SESSION['customer_remember_me_active']) && $_SESSION['customer_remember_me_active'] === true;
    }

    public static function isCustomerSessionExpired(): bool
    {
        if (!self::isCustomerLoggedIn()) {
            return false;
        }

        if (!isset($_SESSION['login_time'])) {
            return false;
        }

        $loginTime = $_SESSION['login_time'];
        $currentTime = time();
        $sessionAge = $currentTime - $loginTime;

        if (isset($_SESSION['customer_remember_me_active']) && $_SESSION['customer_remember_me_active'] === true) {
            $lifetime = self::CUSTOMER_SESSION_WITH_REMEMBER;
        } else {
            $lifetime = self::CUSTOMER_SESSION_LIFETIME;
        }

        return $sessionAge > $lifetime;
    }

    public static function checkCustomerSessionStatus(): string
    {
        if (!self::isCustomerLoggedIn()) {
            return 'not_logged_in';
        }

        if (self::isCustomerSessionExpired()) {
            return 'expired';
        }

        return 'valid';
    }

    public static function refreshCustomerSession(): void
    {
        if (self::isCustomerLoggedIn()) {
            $_SESSION['login_time'] = time();
        }
    }

    public static function getCustomerSessionRemainingTime(): int
    {
        if (!self::isCustomerLoggedIn() || !isset($_SESSION['login_time'])) {
            return 0;
        }

        $loginTime = $_SESSION['login_time'];

        if (isset($_SESSION['customer_remember_me_active']) && $_SESSION['customer_remember_me_active'] === true) {
            $lifetime = self::CUSTOMER_SESSION_WITH_REMEMBER;
        } else {
            $lifetime = self::CUSTOMER_SESSION_LIFETIME;
        }

        $expiresAt = $loginTime + $lifetime;
        $remaining = $expiresAt - time();

        return max(0, $remaining);
    }

    public static function getCustomerSessionLifetime(): int
    {
        return self::CUSTOMER_SESSION_LIFETIME;
    }

    public static function getCustomerSessionWithRememberLifetime(): int
    {
        return self::CUSTOMER_SESSION_WITH_REMEMBER;
    }

    public static function tryRestoreCustomerSessionFromRemember(CustomerRepository $repo): bool
    {
        if (self::isCustomerLoggedIn() && !self::isCustomerSessionExpired()) {
            return true;
        }

        if (!isset($_COOKIE[self::REMEMBER_ME_COOKIE_NAME])) {
            return false;
        }

        $token = $_COOKIE[self::REMEMBER_ME_COOKIE_NAME];
        $customer = $repo->findByRememberToken($token);

        if (!$customer) {
            self::clearRememberMe();
            return false;
        }

        $newToken = self::generateRememberToken();
        $newExpiresAt = date('Y-m-d H:i:s', time() + self::REMEMBER_ME_LIFETIME);
        $repo->updateRememberToken($customer['id_customer'], $newToken, $newExpiresAt);

        setcookie(
            self::REMEMBER_ME_COOKIE_NAME,
            $newToken,
            time() + self::REMEMBER_ME_LIFETIME,
            '/',
            '',
            true,
            true
        );

        self::createCustomerSession($customer, true);
        return true;
    }

    public static function getRememberMeLifetime(): int
    {
        return self::REMEMBER_ME_LIFETIME;
    }

    private static function generateRememberToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
