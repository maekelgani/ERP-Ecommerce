<?php

namespace App\Auth;

class PermissionHelper
{
    private static $adminRepo;
    private static $permissions = null;

    private static function init(): void
    {
        if (self::$adminRepo === null) {
            self::$adminRepo = new AdminRepository();
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

        self::init();
        $roleId = $admin['id_role'] ?? null;
        if ($roleId) {
            return self::$adminRepo->hasPermissionByRoleId((int)$roleId, $permissionKey);
        }

        return false;
    }

    public static function hasAnyPermission(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $key) {
            if (self::hasPermission($key)) {
                return true;
            }
        }
        return false;
    }

    public static function hasAllPermissions(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $key) {
            if (!self::hasPermission($key)) {
                return false;
            }
        }
        return true;
    }

    public static function hasRole(string $roleName): bool
    {
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin) {
            return false;
        }

        return ($admin['role_name'] ?? '') === $roleName;
    }

    public static function hasAnyRole(array $roleNames): bool
    {
        $admin = SessionManager::getCurrentAdmin();
        if (!$admin) {
            return false;
        }

        return in_array($admin['role_name'] ?? '', $roleNames);
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super_admin');
    }

    public static function isAdmin(): bool
    {
        return self::hasAnyRole(['super_admin', 'admin']);
    }

    public static function getCurrentPermissions(): array
    {
        $sessionPermissions = SessionManager::getSessionPermissions();
        if (!empty($sessionPermissions)) {
            return $sessionPermissions;
        }

        self::init();

        $admin = SessionManager::getCurrentAdmin();
        if (!$admin) {
            return [];
        }

        $roleId = $admin['id_role'] ?? null;
        if ($roleId) {
            return self::$adminRepo->getPermissionKeysByRoleId((int)$roleId);
        }

        return [];
    }

    public static function getRoleName(): ?string
    {
        $admin = SessionManager::getCurrentAdmin();
        return $admin['role_name'] ?? null;
    }

    public static function getRoleId(): ?int
    {
        $admin = SessionManager::getCurrentAdmin();
        return isset($admin['id_role']) ? (int)$admin['id_role'] : null;
    }

    public static function canAccessMenu(string $permissionKey): bool
    {
        if (self::isSuperAdmin()) {
            return true;
        }

        return self::hasPermission($permissionKey);
    }

    public static function canManageUsers(): bool
    {
        return self::canAccessMenu('manage_admin_users');
    }

    public static function canManageRoles(): bool
    {
        return self::canAccessMenu('manage_roles');
    }

    public static function canManagePermissions(): bool
    {
        return self::canAccessMenu('manage_permissions');
    }

    public static function canAssignRolePermissions(): bool
    {
        return self::canAccessMenu('assign_role_permissions');
    }

    public static function canManageProducts(): bool
    {
        return self::canAccessMenu('manage_products');
    }

    public static function canManageCategories(): bool
    {
        return self::canAccessMenu('manage_categories');
    }

    public static function canManageBrands(): bool
    {
        return self::canAccessMenu('manage_brands');
    }

    public static function canViewOrders(): bool
    {
        return self::canAccessMenu('view_orders');
    }

    public static function canManageOrders(): bool
    {
        return self::canAccessMenu('manage_orders');
    }

    public static function canManageCampaigns(): bool
    {
        return self::canAccessMenu('manage_campaigns');
    }

    public static function canManageProductDiscounts(): bool
    {
        return self::canAccessMenu('manage_product_discounts');
    }

    public static function canManageVouchers(): bool
    {
        return self::canAccessMenu('manage_vouchers');
    }

    public static function canViewPromoMonitoring(): bool
    {
        return self::canAccessMenu('view_promo_monitoring');
    }

    public static function canViewPromoReports(): bool
    {
        return self::canAccessMenu('view_promo_reports');
    }

    public static function canManageReturnProducts(): bool
    {
        return self::canAccessMenu('manage_return_products');
    }

    public static function canManageCrm(): bool
    {
        return self::canAccessMenu('manage_crm');
    }

    public static function canViewCrmDashboard(): bool
    {
        return self::canAccessMenu('view_crm_dashboard');
    }

    public static function canViewCustomers(): bool
    {
        return self::canAccessMenu('view_customers');
    }

    public static function canManageCustomers(): bool
    {
        return self::canAccessMenu('manage_customers');
    }

    public static function canViewCustomerActivity(): bool
    {
        return self::canAccessMenu('view_customer_activity');
    }

    public static function canViewCustomerFeedback(): bool
    {
        return self::canAccessMenu('view_customer_feedback');
    }

    public static function canManageWebManagement(): bool
    {
        return self::canAccessMenu('manage_web_management');
    }

    public static function canViewReports(): bool
    {
        return self::canAccessMenu('view_reports');
    }

    public static function canViewDashboard(): bool
    {
        return self::canAccessMenu('view_dashboard');
    }

    public static function canViewAnalytics(): bool
    {
        return self::canAccessMenu('view_analytics');
    }

    public static function getPermissionDisplayData(): array
    {
        return [
            'view_dashboard' => ['name' => 'Lihat Dashboard', 'group' => 'Dashboard'],
            'view_analytics' => ['name' => 'Lihat Analitik', 'group' => 'Dashboard'],
            'manage_products' => ['name' => 'Kelola Produk', 'group' => 'Products'],
            'manage_categories' => ['name' => 'Kelola Kategori', 'group' => 'Products'],
            'manage_brands' => ['name' => 'Kelola Brand', 'group' => 'Products'],
            'view_orders' => ['name' => 'Lihat Pesanan', 'group' => 'Orders'],
            'manage_orders' => ['name' => 'Kelola Pesanan', 'group' => 'Orders'],
            'manage_campaigns' => ['name' => 'Kelola Campaign', 'group' => 'Promo'],
            'manage_product_discounts' => ['name' => 'Kelola Diskon Produk', 'group' => 'Promo'],
            'manage_vouchers' => ['name' => 'Kelola Voucher', 'group' => 'Promo'],
            'view_promo_monitoring' => ['name' => 'Lihat Monitoring Promo', 'group' => 'Promo'],
            'view_promo_reports' => ['name' => 'Lihat Laporan Promo', 'group' => 'Promo'],
            'manage_return_products' => ['name' => 'Kelola Return Produk', 'group' => 'Orders'],
            'manage_crm' => ['name' => 'Kelola CRM', 'group' => 'CRM'],
            'view_crm_dashboard' => ['name' => 'Lihat Dashboard CRM', 'group' => 'CRM'],
            'view_customers' => ['name' => 'Lihat Daftar Pelanggan', 'group' => 'CRM'],
            'manage_customers' => ['name' => 'Kelola Pelanggan', 'group' => 'CRM'],
            'view_customer_activity' => ['name' => 'Lihat Aktivitas Pelanggan', 'group' => 'CRM'],
            'view_customer_feedback' => ['name' => 'Lihat Feedback Pelanggan', 'group' => 'CRM'],
            'manage_roles' => ['name' => 'Kelola Role', 'group' => 'Akses'],
            'manage_permissions' => ['name' => 'Kelola Permission', 'group' => 'Akses'],
            'assign_role_permissions' => ['name' => 'Atur Hak Akses Role', 'group' => 'Akses'],
            'manage_admin_users' => ['name' => 'Kelola Pengguna Administrator', 'group' => 'Akses'],
            'manage_web_management' => ['name' => 'Kelola Web Management', 'group' => 'Settings'],
            'view_reports' => ['name' => 'Lihat Laporan', 'group' => 'Reports'],
        ];
    }

    public static function getAllPermissionGroups(): array
    {
        $displayData = self::getPermissionDisplayData();
        $groups = [];
        
        foreach ($displayData as $key => $data) {
            $group = $data['group'];
            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }
            $groups[$group][$key] = $data['name'];
        }
        
        return $groups;
    }
}
