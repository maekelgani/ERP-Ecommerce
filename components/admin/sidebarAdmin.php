<?php

/**
 * ============================================================================
 * ADMIN SIDEBAR NAVIGATION
 * ============================================================================
 * File        : sidebarAdmin.php
 * Description : Komponen sidebar navigasi untuk admin dashboard
 * Version     : 1.0.0
 * Author      : Nano Komputer Development Team
 * 
 * STRUKTUR MENU:
 * - Beranda (Dashboard)
 * - Analitik
 * - Produk (Inventori, Kategori, Merek)
 * - Pesanan (Pesanan Masuk, Semua Pesanan)
 * - Promosi & Diskon (Kampanye, Diskon Produk, Voucher, Monitoring, Laporan)
 * - Pengembalian Produk
 * - Manajemen Pelanggan (Dashboard CRM, Data Pelanggan, Aktivitas, Feedback)
 * - Kelola Akses (Dashboard Akses, Manajemen User, Role & Izin)
 * - Manajemen Website
 * - Laporan
 * ============================================================================
 */

$current_page = basename($_SERVER['PHP_SELF']);

use App\Auth\PermissionHelper;
use App\Auth\SessionManager;

$currentAdmin = SessionManager::getCurrentAdmin();
$isSuperAdmin = PermissionHelper::isSuperAdmin();

// Definisi halaman untuk setiap grup menu
$product_pages = ['ProductAdmin.php', 'CategoryAdmin.php', 'BrandAdmin.php', 'add-product.php', 'edit-product.php', 'product-details.php', 'add-category.php', 'edit-category.php', 'add-brand.php', 'edit-brand.php'];
$order_pages = ['IncomingOrdersAdmin.php', 'OrderAdmin.php'];
$promo_pages = ['CampaignAdmin.php', 'PromoDiskonAdmin.php', 'VoucherAdmin.php', 'PromoMonitoringAdmin.php', 'PromoReportAdmin.php'];
$access_pages = ['HakAksesAdmin.php', 'UserManagementAdmin.php', 'RoleManagementAdmin.php'];
$crm_pages = ['CrmAdmin.php', 'CrmDashboard.php', 'CustomerList.php', 'CustomerDetail.php', 'CustomerActivity.php', 'CustomerFeedback.php'];

// Menentukan menu mana yang harus expand
$should_expand_products = in_array($current_page, $product_pages);
$should_expand_orders = in_array($current_page, $order_pages);
$should_expand_promo = in_array($current_page, $promo_pages);
$should_expand_access = in_array($current_page, $access_pages);
$should_expand_crm = in_array($current_page, $crm_pages);

// Menentukan menu aktif berdasarkan halaman saat ini
$active_menu = null;
if ($should_expand_products) {
    if (in_array($current_page, ['ProductAdmin.php', 'add-product.php', 'edit-product.php', 'product-details.php'])) {
        $active_menu = ['parent' => 'Produk', 'submenu' => 'Inventori'];
    } elseif (in_array($current_page, ['CategoryAdmin.php', 'add-category.php', 'edit-category.php'])) {
        $active_menu = ['parent' => 'Produk', 'submenu' => 'Kategori'];
    } elseif (in_array($current_page, ['BrandAdmin.php', 'add-brand.php', 'edit-brand.php'])) {
        $active_menu = ['parent' => 'Produk', 'submenu' => 'Merek'];
    }
}
if ($should_expand_orders) {
    if ($current_page === 'IncomingOrdersAdmin.php') {
        $active_menu = ['parent' => 'Pesanan', 'submenu' => 'Pesanan Masuk'];
    } elseif ($current_page === 'OrderAdmin.php') {
        $active_menu = ['parent' => 'Pesanan', 'submenu' => 'Semua Pesanan'];
    }
}
if ($should_expand_crm) {
    if (in_array($current_page, ['CrmAdmin.php', 'CrmDashboard.php'])) {
        $active_menu = ['parent' => 'Pelanggan', 'submenu' => 'Dashboard CRM'];
    } elseif (in_array($current_page, ['CustomerList.php', 'CustomerDetail.php'])) {
        $active_menu = ['parent' => 'Pelanggan', 'submenu' => 'Data Pelanggan'];
    } elseif ($current_page === 'CustomerActivity.php') {
        $active_menu = ['parent' => 'Pelanggan', 'submenu' => 'Aktivitas'];
    } elseif ($current_page === 'CustomerFeedback.php') {
        $active_menu = ['parent' => 'Pelanggan', 'submenu' => 'Umpan Balik'];
    }
}
if ($should_expand_promo) {
    if ($current_page === 'CampaignAdmin.php') {
        $active_menu = ['parent' => 'Promosi', 'submenu' => 'Kampanye'];
    } elseif ($current_page === 'PromoDiskonAdmin.php') {
        $active_menu = ['parent' => 'Promosi', 'submenu' => 'Diskon Produk'];
    } elseif ($current_page === 'VoucherAdmin.php') {
        $active_menu = ['parent' => 'Promosi', 'submenu' => 'Voucher'];
    } elseif ($current_page === 'PromoMonitoringAdmin.php') {
        $active_menu = ['parent' => 'Promosi', 'submenu' => 'Pemantauan'];
    } elseif ($current_page === 'PromoReportAdmin.php') {
        $active_menu = ['parent' => 'Promosi', 'submenu' => 'Laporan Promo'];
    }
}
if ($should_expand_access) {
    if ($current_page === 'HakAksesAdmin.php') {
        $active_menu = ['parent' => 'Akses', 'submenu' => 'Dashboard Akses'];
    } elseif ($current_page === 'UserManagementAdmin.php') {
        $active_menu = ['parent' => 'Akses', 'submenu' => 'Manajemen Pengguna'];
    } elseif ($current_page === 'RoleManagementAdmin.php') {
        $active_menu = ['parent' => 'Akses', 'submenu' => 'Role & Izin'];
    }
}

// Permission checks
$canViewDashboard = $isSuperAdmin || PermissionHelper::canViewDashboard();
$canViewAnalytics = $isSuperAdmin || PermissionHelper::canViewAnalytics();
$canManageProducts = $isSuperAdmin || PermissionHelper::canManageProducts();
$canManageCategories = $isSuperAdmin || PermissionHelper::canManageCategories();
$canManageBrands = $isSuperAdmin || PermissionHelper::canManageBrands();
$canViewOrders = $isSuperAdmin || PermissionHelper::canViewOrders();
$canManageOrders = $isSuperAdmin || PermissionHelper::canManageOrders();
$canManageCampaigns = $isSuperAdmin || PermissionHelper::canManageCampaigns();
$canManageProductDiscounts = $isSuperAdmin || PermissionHelper::canManageProductDiscounts();
$canManageVouchers = $isSuperAdmin || PermissionHelper::canManageVouchers();
$canViewPromoMonitoring = $isSuperAdmin || PermissionHelper::canViewPromoMonitoring();
$canViewPromoReports = $isSuperAdmin || PermissionHelper::canViewPromoReports();
$canManageReturnProducts = $isSuperAdmin || PermissionHelper::canManageReturnProducts();
$canManageCrm = $isSuperAdmin || PermissionHelper::canManageCrm();
$canViewCrmDashboard = $isSuperAdmin || PermissionHelper::canViewCrmDashboard();
$canViewCustomers = $isSuperAdmin || PermissionHelper::canViewCustomers();
$canManageCustomers = $isSuperAdmin || PermissionHelper::canManageCustomers();
$canViewCustomerActivity = $isSuperAdmin || PermissionHelper::canViewCustomerActivity();
$canViewCustomerFeedback = $isSuperAdmin || PermissionHelper::canViewCustomerFeedback();
$canManageUsers = $isSuperAdmin || PermissionHelper::canManageUsers();
$canManageRoles = $isSuperAdmin || PermissionHelper::canManageRoles();
$canAssignRolePermissions = $isSuperAdmin || PermissionHelper::canAssignRolePermissions();
$canManageWebManagement = $isSuperAdmin || PermissionHelper::canManageWebManagement();
$canViewReports = $isSuperAdmin || PermissionHelper::canViewReports();

// Menentukan visibility menu group
$showProductsMenu = $canManageProducts || $canManageCategories || $canManageBrands;
$showOrdersMenu = $canViewOrders || $canManageOrders;
$showPromoMenu = $canManageCampaigns || $canManageProductDiscounts || $canManageVouchers || $canViewPromoMonitoring || $canViewPromoReports;
$showAccessMenu = $canManageUsers || $canManageRoles || $canAssignRolePermissions;
$showCrmMenu = $canManageCrm || $canViewCrmDashboard || $canViewCustomers || $canManageCustomers || $canViewCustomerActivity || $canViewCustomerFeedback;
?>

<style>
    /* ============================================
       SIDEBAR SCROLLBAR
       ============================================ */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* ============================================
       DROPDOWN ANIMATION
       ============================================ */
    .sidebar-dropdown {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 500px;
        overflow: hidden;
    }

    .sidebar-dropdown.hidden {
        max-height: 0;
        opacity: 0;
    }

    .arrow-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .arrow-icon.rotate-180 {
        transform: rotate(180deg);
    }

    /* ============================================
       MENU ITEM ACTIVE STATE
       ============================================ */
    .menu-item.active {
        background-color: #c94449;
        color: white;
        font-weight: 500;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .menu-item.active .material-symbols-outlined {
        font-weight: 400;
        color: white;
    }

    .submenu-item.active {
        background-color: #c94449;
        color: white;
        font-weight: 500;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* ============================================
       ICON SIZING
       ============================================ */
    #sidebarDesktop .material-symbols-outlined:not(.arrow-icon),
    #mobileDrawer .material-symbols-outlined:not(.arrow-icon) {
        font-size: 24px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #sidebarDesktop .submenu-item .material-symbols-outlined,
    #mobileDrawer .submenu-item .material-symbols-outlined {
        font-size: 20px;
        width: 20px;
        height: 20px;
    }

    /* ============================================
       SIDEBAR FOOTER
       ============================================ */
    .sidebar-footer {
        padding: 0.625rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.1) 100%);
        width: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .sidebar-footer-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
        width: 100%;
    }

    .sidebar-footer-content {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 0.625rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .sidebar-footer-content:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2);
    }

    .sidebar-footer-brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
    }

    .sidebar-footer-brand .footer-icon-wrapper {
        width: 26px;
        height: 26px;
        min-width: 26px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .sidebar-footer-brand .footer-icon-wrapper .material-symbols-outlined {
        font-size: 14px !important;
        width: 14px !important;
        height: 14px !important;
        color: white;
    }

    .sidebar-footer-brand-text {
        display: flex;
        flex-direction: column;
        gap: 0;
        flex: 1;
        min-width: 0;
    }

    .sidebar-footer-brand-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        line-height: 1.2;
    }

    .sidebar-footer-brand-tagline {
        font-size: 0.6rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .sidebar-footer-version {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.375rem;
        width: 100%;
    }

    .sidebar-footer-version-info {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .sidebar-footer-version-info .material-symbols-outlined {
        font-size: 12px !important;
        width: 12px !important;
        height: 12px !important;
        color: rgba(255, 255, 255, 0.5);
    }

    .sidebar-footer-version-text {
        font-size: 0.6rem;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 500;
    }

    .sidebar-footer-status {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(34, 197, 94, 0.2);
        padding: 0.125rem 0.375rem;
        border-radius: 12px;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .sidebar-footer-status-dot {
        width: 5px;
        height: 5px;
        background: #22c55e;
        border-radius: 50%;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }

    .sidebar-footer-status-text {
        font-size: 0.55rem;
        color: #22c55e;
        font-weight: 500;
        text-transform: uppercase;
    }

    /* Status Tidak Aktif */
    .sidebar-footer-status.idle {
        background: rgba(255, 197, 15, 0.2);
        border-color: rgba(255, 197, 15, 0.3);
    }

    .sidebar-footer-status.idle .sidebar-footer-status-dot {
        background: #FFC50F;
        animation: none;
        opacity: 0.8;
    }

    .sidebar-footer-status.idle .sidebar-footer-status-text {
        color: #FFC50F;
        opacity: 0.9;
    }

    .sidebar-footer-middle-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.15) 50%, transparent 100%);
        width: 100%;
        margin: 0;
    }

    .sidebar-footer-copyright {
        font-size: 0.65rem;
        color: rgba(255, 255, 255, 0.5);
        text-align: center;
        padding: 0.375rem 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    /* ============================================
       SIDEBAR COLLAPSED STATE
       ============================================ */
    #sidebarDesktop {
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #sidebarDesktop.collapsed {
        width: 80px;
    }

    #sidebarDesktop.collapsed .menu-text,
    #sidebarDesktop.collapsed .logo-text,
    #sidebarDesktop.collapsed .arrow-icon {
        opacity: 0;
        width: 0;
        overflow: hidden;
        display: none;
    }

    #sidebarDesktop.collapsed .logo-section {
        justify-content: center;
        padding: 1rem;
    }

    #sidebarDesktop.collapsed .logo-container {
        justify-content: center;
        width: auto;
    }

    #sidebarDesktop.collapsed .menu-item {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 11px !important;
        gap: 0 !important;
        width: 46px !important;
        height: 46px !important;
        margin: 0 auto !important;
        border-radius: 0.5rem !important;
    }

    #sidebarDesktop.collapsed .toggle-btn {
        display: flex !important;
        justify-content: center !important;
        padding: 11px !important;
        gap: 0 !important;
        width: 46px !important;
        height: 46px !important;
        margin: 0 auto !important;
        border-radius: 0.5rem !important;
    }

    #sidebarDesktop.collapsed .sidebar-dropdown {
        display: none !important;
    }

    #sidebarDesktop.collapsed nav {
        padding: 1rem 17px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    #sidebarDesktop.collapsed .sidebar-footer {
        padding: 0.5rem 0.375rem;
        gap: 0.25rem;
    }

    #sidebarDesktop.collapsed .sidebar-footer-divider,
    #sidebarDesktop.collapsed .sidebar-footer-middle-divider,
    #sidebarDesktop.collapsed .sidebar-footer-copyright {
        display: none;
    }

    #sidebarDesktop.collapsed .sidebar-footer-content {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.375rem;
        gap: 0.375rem;
    }

    #sidebarDesktop.collapsed .sidebar-footer-brand {
        width: auto;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    #sidebarDesktop.collapsed .sidebar-footer-brand-text {
        display: none;
    }

    #sidebarDesktop.collapsed .sidebar-footer-version {
        width: auto;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    #sidebarDesktop.collapsed .sidebar-footer-version-info {
        display: none;
    }

    #sidebarDesktop.collapsed .sidebar-footer-status {
        padding: 0.25rem 0.5rem;
        font-size: 0.5rem;
    }

    #sidebarDesktop.collapsed .sidebar-footer-status-text {
        font-size: 0.45rem;
    }

    .menu-text,
    .logo-text,
    .arrow-icon {
        transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============================================
       MOBILE DRAWER OVERLAY
       ============================================ */
    #mobileDrawerOverlay {
        transition: opacity 0.3s ease-in-out;
        opacity: 0;
    }

    #mobileDrawerOverlay:not(.hidden) {
        opacity: 1;
    }

    @media (max-width: 1023px) {
        .sidebar-content {
            width: 100%;
        }
    }

    /* ============================================
       MENU SECTION LABELS
       ============================================ */
    .menu-section-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.4);
        padding: 0.75rem 1rem 0.5rem;
        margin-top: 0.5rem;
    }

    #sidebarDesktop.collapsed .menu-section-label {
        display: none;
    }

    /* ============================================
       TOOLTIP FOR COLLAPSED STATE
       ============================================ */
    .sidebar-tooltip {
        position: absolute;
        left: calc(100% + 10px);
        top: 50%;
        transform: translateY(-50%);
        background: #1f2937;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 100;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .sidebar-tooltip::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 50%;
        transform: translateY(-50%);
        border: 6px solid transparent;
        border-right-color: #1f2937;
        border-left: none;
    }

    #sidebarDesktop.collapsed .menu-item:hover .sidebar-tooltip,
    #sidebarDesktop.collapsed .toggle-btn:hover .sidebar-tooltip {
        opacity: 1;
        visibility: visible;
    }
</style>

<!-- ============================================
     SIDEBAR DESKTOP
     ============================================ -->
<aside id="sidebarDesktop" class="hidden lg:flex left-0 top-0 h-screen sticky border-r border-[#a83236] z-40 w-[250px] bg-gradient-to-b from-[#882426] to-[#6d1a1c] text-white flex-col shadow-lg">
    <!-- Logo Section -->
    <div class="logo-section flex items-center justify-between p-5 border-b border-[#a83236]">
        <div class="logo-container flex items-center gap-3 overflow-hidden w-full">
            <img class="h-8 w-8 rounded-lg flex-shrink-0 bg-white" src="../../assets/img/logo-nano.png" alt="Logo Nano Komputer">
            <span class="logo-text font-bold text-md text-white whitespace-nowrap">NANO KOMPUTER</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav id="sidebarMenuDesktop" class="flex-1 overflow-y-auto p-4 space-y-1 scrollbar-hide">

        <!-- ========== MENU UTAMA ========== -->
        <div class="menu-section-label">Menu Utama</div>

        <?php if ($canViewDashboard): ?>
            <a href="../../view/admin/DashboardAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'DashboardAdmin.php' ? 'active' : '' ?> relative" data-permission="view_dashboard">
                <span class="material-symbols-outlined text-lg flex-shrink-0">home</span>
                <span class="menu-text font-medium whitespace-nowrap">Dashboard</span>
                <span class="sidebar-tooltip">Dashboard</span>
            </a>
        <?php endif; ?>

        <?php if ($canViewAnalytics): ?>
            <a href="../../view/admin/AnalitikAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'AnalitikAdmin.php' ? 'active' : '' ?> relative" data-permission="view_analytics">
                <span class="material-symbols-outlined text-lg flex-shrink-0">analytics</span>
                <span class="menu-text font-medium whitespace-nowrap">Analitik</span>
                <span class="sidebar-tooltip">Analitik</span>
            </a>
        <?php endif; ?>

        <!-- ========== MANAJEMEN PRODUK ========== -->
        <div class="menu-section-label">Manajemen Produk</div>

        <?php if ($showProductsMenu): ?>
            <div class="dropdown-section" data-dropdown="products">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white relative" type="button">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="material-symbols-outlined text-lg flex-shrink-0">inventory_2</span>
                        <span class="menu-text font-medium whitespace-nowrap">Produk</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 flex-shrink-0 <?= $should_expand_products ? 'rotate-180' : '' ?>">expand_more</span>
                    <span class="sidebar-tooltip">Produk</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_products ? '' : 'hidden' ?>">
                    <?php if ($canManageProducts): ?>
                        <a href="../../view/admin/ProductAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Inventori') ? 'active' : '' ?>" data-permission="manage_products">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">box_add</span>
                            <span class="menu-text text-sm whitespace-nowrap">Inventori</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageCategories): ?>
                        <a href="../../view/admin/CategoryAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Kategori') ? 'active' : '' ?>" data-permission="manage_categories">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">category</span>
                            <span class="menu-text text-sm whitespace-nowrap">Kategori</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageBrands): ?>
                        <a href="../../view/admin/BrandAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Merek') ? 'active' : '' ?>" data-permission="manage_brands">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">handshake</span>
                            <span class="menu-text text-sm whitespace-nowrap">Brand</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== TRANSAKSI ========== -->
        <div class="menu-section-label">Transaksi</div>

        <?php if ($showOrdersMenu): ?>
            <div class="dropdown-section" data-dropdown="orders">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white relative" type="button">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="material-symbols-outlined text-lg flex-shrink-0">shopping_cart</span>
                        <span class="menu-text font-medium whitespace-nowrap">Pesanan</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 flex-shrink-0 <?= $should_expand_orders ? 'rotate-180' : '' ?>">expand_more</span>
                    <span class="sidebar-tooltip">Pesanan</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_orders ? '' : 'hidden' ?>">
                    <?php if ($canViewOrders || $canManageOrders): ?>
                        <a href="../../view/admin/IncomingOrdersAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Pesanan Masuk') ? 'active' : '' ?>" data-permission="view_orders">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">inbox</span>
                            <span class="menu-text text-sm whitespace-nowrap">Pesanan Masuk</span>
                        </a>
                        <a href="../../view/admin/OrderAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Semua Pesanan') ? 'active' : '' ?>" data-permission="view_orders">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">list_alt</span>
                            <span class="menu-text text-sm whitespace-nowrap">Semua Pesanan</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($canManageReturnProducts): ?>
            <a href="../../view/admin/ReturnAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'ReturnAdmin.php' ? 'active' : '' ?> relative" data-permission="manage_return_products">
                <span class="material-symbols-outlined text-lg flex-shrink-0">keyboard_return</span>
                <span class="menu-text font-medium whitespace-nowrap">Pengembalian</span>
                <span class="sidebar-tooltip">Pengembalian</span>
            </a>
        <?php endif; ?>

        <!-- ========== PEMASARAN ========== -->
        <div class="menu-section-label">Pemasaran</div>

        <?php if ($showPromoMenu): ?>
            <div class="dropdown-section" data-dropdown="promo">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white relative" type="button">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="material-symbols-outlined text-lg flex-shrink-0">local_offer</span>
                        <span class="menu-text font-medium whitespace-nowrap">Promosi & Diskon</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 flex-shrink-0 <?= $should_expand_promo ? 'rotate-180' : '' ?>">expand_more</span>
                    <span class="sidebar-tooltip">Promosi & Diskon</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_promo ? '' : 'hidden' ?>">
                    <?php if ($canManageCampaigns): ?>
                        <a href="../../view/admin/CampaignAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Kampanye') ? 'active' : '' ?>" data-permission="manage_campaigns">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">campaign</span>
                            <span class="menu-text text-sm whitespace-nowrap">Kampanye</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageProductDiscounts): ?>
                        <a href="../../view/admin/PromoDiskonAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Diskon Produk') ? 'active' : '' ?>" data-permission="manage_product_discounts">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">percent</span>
                            <span class="menu-text text-sm whitespace-nowrap">Diskon Produk</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageVouchers): ?>
                        <a href="../../view/admin/VoucherAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Voucher') ? 'active' : '' ?>" data-permission="manage_vouchers">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">confirmation_number</span>
                            <span class="menu-text text-sm whitespace-nowrap">Voucher</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewPromoMonitoring): ?>
                        <a href="../../view/admin/PromoMonitoringAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Pemantauan') ? 'active' : '' ?>" data-permission="view_promo_monitoring">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">monitoring</span>
                            <span class="menu-text text-sm whitespace-nowrap">Pemantauan</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewPromoReports): ?>
                        <a href="../../view/admin/PromoReportAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Laporan Promo') ? 'active' : '' ?>" data-permission="view_promo_reports">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">summarize</span>
                            <span class="menu-text text-sm whitespace-nowrap">Laporan Promo</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== MANAJEMEN PELANGGAN ========== -->
        <div class="menu-section-label">Manajemen Pelanggan</div>

        <?php if ($showCrmMenu): ?>
            <div class="dropdown-section" data-dropdown="crm">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white relative" type="button">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="material-symbols-outlined text-lg flex-shrink-0">support_agent</span>
                        <span class="menu-text font-medium whitespace-nowrap">Pelanggan</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 flex-shrink-0 <?= $should_expand_crm ? 'rotate-180' : '' ?>">expand_more</span>
                    <span class="sidebar-tooltip">Pelanggan</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_crm ? '' : 'hidden' ?>">
                    <?php if ($canViewCrmDashboard || $canManageCrm): ?>
                        <a href="../../view/admin/CrmDashboard.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Dashboard CRM') ? 'active' : '' ?>" data-permission="view_crm_dashboard">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">dashboard</span>
                            <span class="menu-text text-sm whitespace-nowrap">Dashboard CRM</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomers || $canManageCustomers): ?>
                        <a href="../../view/admin/CustomerList.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Data Pelanggan') ? 'active' : '' ?>" data-permission="view_customers">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">group</span>
                            <span class="menu-text text-sm whitespace-nowrap">Data Pelanggan</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomerActivity): ?>
                        <a href="../../view/admin/CustomerActivity.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Aktivitas') ? 'active' : '' ?>" data-permission="view_customer_activity">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">history</span>
                            <span class="menu-text text-sm whitespace-nowrap">Aktivitas</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomerFeedback): ?>
                        <a href="../../view/admin/CustomerFeedback.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Umpan Balik') ? 'active' : '' ?>" data-permission="view_customer_feedback">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">rate_review</span>
                            <span class="menu-text text-sm whitespace-nowrap">Umpan Balik</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== PENGATURAN SISTEM ========== -->
        <div class="menu-section-label">Pengaturan Sistem</div>

        <?php if ($showAccessMenu): ?>
            <div class="dropdown-section" data-dropdown="access">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white relative" type="button">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <span class="material-symbols-outlined text-lg flex-shrink-0">admin_panel_settings</span>
                        <span class="menu-text font-medium whitespace-nowrap">Kelola Akses</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 flex-shrink-0 <?= $should_expand_access ? 'rotate-180' : '' ?>">expand_more</span>
                    <span class="sidebar-tooltip">Kelola Akses</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_access ? '' : 'hidden' ?>">
                    <a href="../../view/admin/HakAksesAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Dashboard Akses') ? 'active' : '' ?>" data-permission="manage_access">
                        <span class="material-symbols-outlined text-sm flex-shrink-0">dashboard</span>
                        <span class="menu-text text-sm whitespace-nowrap">Dashboard Akses</span>
                    </a>
                    <?php if ($canManageUsers): ?>
                        <a href="../../view/admin/UserManagementAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Manajemen Pengguna') ? 'active' : '' ?>" data-permission="manage_admin_users">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">people</span>
                            <span class="menu-text text-sm whitespace-nowrap">Manajemen Pengguna</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageRoles || $canAssignRolePermissions): ?>
                        <a href="../../view/admin/RoleManagementAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Role & Izin') ? 'active' : '' ?>" data-permission="manage_roles">
                            <span class="material-symbols-outlined text-sm flex-shrink-0">security</span>
                            <span class="menu-text text-sm whitespace-nowrap">Role & Izin</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($canManageWebManagement): ?>
            <a href="../../view/admin/WebManagement.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'WebManagement.php' ? 'active' : '' ?> relative" data-permission="manage_web_management">
                <span class="material-symbols-outlined text-lg flex-shrink-0">construction</span>
                <span class="menu-text font-medium whitespace-nowrap">Manajemen Website</span>
                <span class="sidebar-tooltip">Manajemen Website</span>
            </a>
        <?php endif; ?>

        <?php if ($canViewReports): ?>
            <a href="../../view/admin/ReportAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'ReportAdmin.php' ? 'active' : '' ?> relative" data-permission="view_reports">
                <span class="material-symbols-outlined text-lg flex-shrink-0">picture_as_pdf</span>
                <span class="menu-text font-medium whitespace-nowrap">Laporan</span>
                <span class="sidebar-tooltip">Laporan</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-divider"></div>
        <div class="sidebar-footer-content">
            <div class="sidebar-footer-brand">
                <div class="footer-icon-wrapper">
                    <img class="h-8 w-8 rounded-lg flex-shrink-0 object-contain" src="../../assets/img/logo-nano.png" alt="Logo Nano Komputer">
                </div>
                <div class="sidebar-footer-brand-text menu-text">
                    <span class="sidebar-footer-brand-name">Nano Komputer</span>
                    <span class="sidebar-footer-brand-tagline">Panel Admin</span>
                </div>
            </div>
            <div class="sidebar-footer-middle-divider"></div>
            <div class="sidebar-footer-version">
                <div class="sidebar-footer-version-info">
                    <span class="material-symbols-outlined">code</span>
                    <span class="sidebar-footer-version-text menu-text">v1.0.0</span>
                </div>
                <div class="sidebar-footer-status">
                    <span class="sidebar-footer-status-dot"></span>
                    <span class="sidebar-footer-status-text">Online</span>
                </div>
            </div>
        </div>
        <div class="sidebar-footer-copyright">© 2024 Nano Komputer. Hak cipta dilindungi.</div>
    </div>
</aside>

<!-- ============================================
     MOBILE DRAWER OVERLAY
     ============================================ -->
<div id="mobileDrawerOverlay" class="fixed inset-0 bg-black/50 hidden z-30 lg:hidden"></div>

<!-- ============================================
     MOBILE DRAWER
     ============================================ -->
<aside id="mobileDrawer" class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-[#882426] to-[#6d1a1c] text-white transform -translate-x-full transition-transform duration-300 z-30 lg:hidden flex flex-col shadow-lg">
    <!-- Mobile Header -->
    <div class="flex items-center justify-between p-5 py-4 border-b border-[#a83236]">
        <div class="flex items-center gap-3">
            <img class="h-8 w-8 rounded-lg bg-white" src="../../assets/img/logo-nano.png" alt="Logo Nano Komputer">
            <span class="font-bold text-sm">NANO KOMPUTER</span>
        </div>
        <button id="closeDrawerBtn" type="button" class="h-8 w-8 flex items-center justify-center text-white/90 hover:bg-white/10 rounded-md transition-colors" aria-label="Tutup menu">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <nav id="sidebarMenuMobile" class="flex-1 overflow-y-auto p-4 space-y-1 scrollbar-hide">

        <!-- ========== MENU UTAMA ========== -->
        <div class="menu-section-label">Menu Utama</div>

        <?php if ($canViewDashboard): ?>
            <a href="../../view/admin/DashboardAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'DashboardAdmin.php' ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-lg">home</span>
                <span class="font-medium">Beranda</span>
            </a>
        <?php endif; ?>

        <?php if ($canViewAnalytics): ?>
            <a href="../../view/admin/AnalitikAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'AnalitikAdmin.php' ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-lg">analytics</span>
                <span class="font-medium">Analitik</span>
            </a>
        <?php endif; ?>

        <!-- ========== MANAJEMEN PRODUK ========== -->
        <div class="menu-section-label">Manajemen Produk</div>

        <?php if ($showProductsMenu): ?>
            <div class="dropdown-section" data-dropdown="products">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white" type="button">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">inventory_2</span>
                        <span class="font-medium">Produk</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 <?= $should_expand_products ? 'rotate-180' : '' ?>">expand_more</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_products ? '' : 'hidden' ?>">
                    <?php if ($canManageProducts): ?>
                        <a href="../../view/admin/ProductAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Inventori') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">box_add</span>
                            <span class="text-sm">Inventori</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageCategories): ?>
                        <a href="../../view/admin/CategoryAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Kategori') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">category</span>
                            <span class="text-sm">Kategori</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageBrands): ?>
                        <a href="../../view/admin/BrandAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Merek') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">handshake</span>
                            <span class="text-sm">Merek</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== TRANSAKSI ========== -->
        <div class="menu-section-label">Transaksi</div>

        <?php if ($showOrdersMenu): ?>
            <div class="dropdown-section" data-dropdown="orders">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white" type="button">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                        <span class="font-medium">Pesanan</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 <?= $should_expand_orders ? 'rotate-180' : '' ?>">expand_more</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_orders ? '' : 'hidden' ?>">
                    <?php if ($canViewOrders || $canManageOrders): ?>
                        <a href="../../view/admin/IncomingOrdersAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Pesanan Masuk') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">inbox</span>
                            <span class="text-sm">Pesanan Masuk</span>
                        </a>
                        <a href="../../view/admin/OrderAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Semua Pesanan') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">list_alt</span>
                            <span class="text-sm">Semua Pesanan</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($canManageReturnProducts): ?>
            <a href="../../view/admin/ReturnAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'ReturnAdmin.php' ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-lg">keyboard_return</span>
                <span class="font-medium">Pengembalian</span>
            </a>
        <?php endif; ?>

        <!-- ========== PEMASARAN ========== -->
        <div class="menu-section-label">Pemasaran</div>

        <?php if ($showPromoMenu): ?>
            <div class="dropdown-section" data-dropdown="promo">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white" type="button">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">local_offer</span>
                        <span class="font-medium">Promosi & Diskon</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 <?= $should_expand_promo ? 'rotate-180' : '' ?>">expand_more</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_promo ? '' : 'hidden' ?>">
                    <?php if ($canManageCampaigns): ?>
                        <a href="../../view/admin/CampaignAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Kampanye') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">campaign</span>
                            <span class="text-sm">Kampanye</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageProductDiscounts): ?>
                        <a href="../../view/admin/PromoDiskonAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Diskon Produk') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">percent</span>
                            <span class="text-sm">Diskon Produk</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageVouchers): ?>
                        <a href="../../view/admin/VoucherAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Voucher') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">confirmation_number</span>
                            <span class="text-sm">Voucher</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewPromoMonitoring): ?>
                        <a href="../../view/admin/PromoMonitoringAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Pemantauan') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">monitoring</span>
                            <span class="text-sm">Pemantauan</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewPromoReports): ?>
                        <a href="../../view/admin/PromoReportAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Laporan Promo') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">summarize</span>
                            <span class="text-sm">Laporan Promo</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== MANAJEMEN PELANGGAN ========== -->
        <div class="menu-section-label">Manajemen Pelanggan</div>

        <?php if ($showCrmMenu): ?>
            <div class="dropdown-section" data-dropdown="crm">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white" type="button">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        <span class="font-medium">Pelanggan</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 <?= $should_expand_crm ? 'rotate-180' : '' ?>">expand_more</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_crm ? '' : 'hidden' ?>">
                    <?php if ($canViewCrmDashboard || $canManageCrm): ?>
                        <a href="../../view/admin/CrmDashboard.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Dashboard CRM') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">dashboard</span>
                            <span class="text-sm">Dashboard CRM</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomers || $canManageCustomers): ?>
                        <a href="../../view/admin/CustomerList.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Data Pelanggan') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">group</span>
                            <span class="text-sm">Data Pelanggan</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomerActivity): ?>
                        <a href="../../view/admin/CustomerActivity.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Aktivitas') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">history</span>
                            <span class="text-sm">Aktivitas</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canViewCustomerFeedback): ?>
                        <a href="../../view/admin/CustomerFeedback.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Umpan Balik') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">rate_review</span>
                            <span class="text-sm">Umpan Balik</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ========== PENGATURAN SISTEM ========== -->
        <div class="menu-section-label">Pengaturan Sistem</div>

        <?php if ($showAccessMenu): ?>
            <div class="dropdown-section" data-dropdown="access">
                <button class="toggle-btn flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white" type="button">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
                        <span class="font-medium">Kelola Akses</span>
                    </div>
                    <span class="arrow-icon material-symbols-outlined transition-transform duration-300 <?= $should_expand_access ? 'rotate-180' : '' ?>">expand_more</span>
                </button>
                <div class="sidebar-dropdown ml-8 mt-1 space-y-2 <?= $should_expand_access ? '' : 'hidden' ?>">
                    <a href="../../view/admin/HakAksesAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Dashboard Akses') ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-sm">dashboard</span>
                        <span class="text-sm">Dashboard Akses</span>
                    </a>
                    <?php if ($canManageUsers): ?>
                        <a href="../../view/admin/UserManagementAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Manajemen Pengguna') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">people</span>
                            <span class="text-sm">Manajemen Pengguna</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($canManageRoles || $canAssignRolePermissions): ?>
                        <a href="../../view/admin/RoleManagementAdmin.php" class="menu-item submenu-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-200 hover:bg-[#a83236] hover:text-white transition-all <?= ($active_menu && $active_menu['submenu'] === 'Role & Izin') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined text-sm">security</span>
                            <span class="text-sm">Role & Izin</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($canManageWebManagement): ?>
            <a href="../../view/admin/WebManagement.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'WebManagement.php' ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-lg">construction</span>
                <span class="font-medium">Manajemen Website</span>
            </a>
        <?php endif; ?>

        <?php if ($canViewReports): ?>
            <a href="../../view/admin/ReportAdmin.php" class="menu-item flex items-center gap-3 px-4 py-3 rounded-lg transition-all text-gray-100 hover:bg-[#a83236] hover:text-white <?= $current_page === 'ReportAdmin.php' ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                <span class="font-medium">Laporan</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Mobile Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-divider"></div>
        <div class="sidebar-footer-content">
            <div class="sidebar-footer-brand">
                <div class="footer-icon-wrapper">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
                <div class="sidebar-footer-brand-text menu-text">
                    <span class="sidebar-footer-brand-name">Nano Komputer</span>
                    <span class="sidebar-footer-brand-tagline">Panel Admin</span>
                </div>
            </div>
            <div class="sidebar-footer-middle-divider"></div>
            <div class="sidebar-footer-version">
                <div class="sidebar-footer-version-info">
                    <span class="material-symbols-outlined">code</span>
                    <span class="sidebar-footer-version-text menu-text">v1.0.0</span>
                </div>
                <div class="sidebar-footer-status">
                    <span class="sidebar-footer-status-dot"></span>
                    <span class="sidebar-footer-status-text">Online</span>
                </div>
            </div>
        </div>
        <div class="sidebar-footer-copyright">© 2024 Nano Komputer. Hak cipta dilindungi.</div>
    </div>
</aside>

<script>
    /**
     * ============================================================================
     * MOBILE DRAWER FUNCTIONALITY
     * ============================================================================
     */
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menuBtn');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileDrawerOverlay = document.getElementById('mobileDrawerOverlay');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');

        function openDrawer() {
            mobileDrawer.classList.remove('-translate-x-full');
            mobileDrawerOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            mobileDrawer.classList.add('-translate-x-full');
            mobileDrawerOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        if (menuBtn) menuBtn.addEventListener('click', openDrawer);
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
        if (mobileDrawerOverlay) mobileDrawerOverlay.addEventListener('click', closeDrawer);

        // Mobile dropdown toggle
        document.querySelectorAll('#mobileDrawer .dropdown-section .toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                const arrow = this.querySelector('.arrow-icon');
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                    if (arrow) arrow.classList.toggle('rotate-180');
                }
            });
        });
    });

    /**
     * ============================================================================
     * REAL-TIME ONLINE/IDLE STATUS TRACKER
     * ============================================================================
     * Melacak aktivitas pengguna dan menampilkan status online/tidak aktif
     */
    (function() {
        const IDLE_TIMEOUT = 1 * 60 * 1000; // 1 menit
        const STATUS_ELEMENTS = document.querySelectorAll('.sidebar-footer-status');
        const STATUS_TEXT_ELEMENTS = document.querySelectorAll('.sidebar-footer-status-text');

        let lastActivityTime = Date.now();
        let idleCheckInterval = null;
        let isIdle = false;

        // Update status di UI
        function updateStatus(idle) {
            STATUS_ELEMENTS.forEach(el => {
                if (idle) {
                    el.classList.add('idle');
                } else {
                    el.classList.remove('idle');
                }
            });

            STATUS_TEXT_ELEMENTS.forEach(el => {
                el.textContent = idle ? 'IDLE' : 'ONLINE';
            });

            isIdle = idle;
        }

        // Handle aktivitas pengguna
        function recordActivity() {
            lastActivityTime = Date.now();

            // Jika sedang tidak aktif, kembalikan ke aktif
            if (isIdle) {
                updateStatus(false);
            }
        }

        // Cek status tidak aktif
        function checkIdleStatus() {
            const timeSinceLastActivity = Date.now() - lastActivityTime;

            if (timeSinceLastActivity >= IDLE_TIMEOUT && !isIdle) {
                updateStatus(true);
            } else if (timeSinceLastActivity < IDLE_TIMEOUT && isIdle) {
                updateStatus(false);
            }
        }

        // Event listeners untuk mendeteksi aktivitas
        const activityEvents = [
            'mousemove',
            'mousedown',
            'keydown',
            'scroll',
            'touchstart',
            'click'
        ];

        // Tambah event listeners dengan debounce untuk performa
        let debounceTimer = null;

        function handleActivityWithDebounce() {
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            debounceTimer = setTimeout(() => {
                recordActivity();
            }, 100);
        }

        activityEvents.forEach(event => {
            document.addEventListener(event, handleActivityWithDebounce, true);
        });

        // Cek status tidak aktif setiap 1 detik
        idleCheckInterval = setInterval(checkIdleStatus, 1000);

        // Inisialisasi status sebagai AKTIF
        updateStatus(false);

        // Cleanup saat halaman ditutup
        window.addEventListener('beforeunload', () => {
            if (idleCheckInterval) {
                clearInterval(idleCheckInterval);
            }
            activityEvents.forEach(event => {
                document.removeEventListener(event, handleActivityWithDebounce, true);
            });
        });
    })();
</script>