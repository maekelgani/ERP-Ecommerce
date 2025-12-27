<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CrmRepository.php';
require_once __DIR__ . '/../../app/Repository/CustomerActivityRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CrmRepository;
use App\Repository\CustomerActivityRepository;

AuthMiddleware::requireAdminLoginFromView();

if (!PermissionHelper::canViewCustomers() && !PermissionHelper::canManageCustomers()) {
    header('Location: ../../view/403.php');
    exit;
}

$customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($customerId <= 0) {
    header('Location: CustomerList.php');
    exit;
}

$crmRepo = new CrmRepository();
$activityRepo = new CustomerActivityRepository();

$customer = $crmRepo->getCustomerById($customerId);
if (!$customer) {
    header('Location: CustomerList.php');
    exit;
}

$addresses = $crmRepo->getCustomerAddresses($customerId);
$orders = $crmRepo->getCustomerOrders($customerId, 10);
$reviews = $crmRepo->getCustomerReviews($customerId);
$voucherUsage = $crmRepo->getCustomerVoucherUsage($customerId);
$orderStats = $crmRepo->getOrderStatsByCustomer($customerId);
$activities = $activityRepo->getCustomerActivities($customerId, 20);

$pageTitle = "Detail Pelanggan - " . $customer['nama_lengkap'];
include '../../components/admin/head.php';
?>

<style>
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse-ring {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }

        100% {
            transform: scale(1.3);
            opacity: 0;
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .delay-100 {
        animation-delay: 0.1s;
    }

    .delay-200 {
        animation-delay: 0.2s;
    }

    .delay-300 {
        animation-delay: 0.3s;
    }

    .delay-400 {
        animation-delay: 0.4s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(136, 36, 38, 0.15);
    }

    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .profile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .activity-item:hover {
        background: linear-gradient(90deg, rgba(136, 36, 38, 0.02) 0%, transparent 100%);
        border-left-color: #882426;
    }

    .address-card:hover {
        border-color: #882426;
        box-shadow: 0 4px 12px -4px rgba(136, 36, 38, 0.1);
    }

    .order-row:hover {
        background: linear-gradient(90deg, rgba(136, 36, 38, 0.02) 0%, transparent 100%);
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <!-- Header with Gradient -->
            <div class="bg-gradient-to-r from-[#882426] to-[#a62d30] text-white rounded-2xl mb-6 overflow-hidden animate-fade-in-up">
                <div class="px-6 py-6 relative">
                    <!-- Decorative elements -->
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="absolute -right-4 -bottom-12 w-48 h-48 bg-white/5 rounded-full"></div>

                    <div class="relative flex flex-col md:flex-row md:items-center gap-4">
                        <a href="CustomerList.php" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition-all backdrop-blur-sm self-start">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                        <div class="flex items-center gap-4 flex-1">
                            <?php if (!empty($customer['profile_image'])): ?>
                                <img src="../../uploads/customers/<?= htmlspecialchars($customer['profile_image']) ?>" alt="<?= htmlspecialchars($customer['nama_lengkap']) ?>"
                                    class="w-20 h-20 rounded-2xl object-cover border-4 border-white/20 shadow-lg">
                            <?php else: ?>
                                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white/10">
                                    <span class="material-symbols-outlined text-white/80 text-3xl">person</span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold mb-1"><?= htmlspecialchars($customer['nama_lengkap']) ?></h1>
                                <p class="text-white/80 mb-2"><?= htmlspecialchars($customer['email']) ?></p>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <?php if ($customer['is_active']): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-100 border border-emerald-400/30 backdrop-blur-sm">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-500/20 text-gray-200 border border-gray-400/30 backdrop-blur-sm">
                                            <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>

                                    <?php if (($customer['login_type'] ?? 'regular') === 'google'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs font-medium border border-white/20">
                                            <!-- <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                                                <path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                                <path fill="#fff" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                                <path fill="#fff" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                                <path fill="#fff" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                            </svg> -->
                                            <svg class="w-5 h-5 bg-white rounded-full py-1" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                            </svg>
                                            Google
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs font-medium border border-white/20">
                                            <span class="material-symbols-outlined text-sm">mail</span>
                                            Email
                                        </span>
                                    <?php endif; ?>

                                    <span class="text-white/60 text-xs">ID: #<?= $customer['id_customer'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Total Orders Card -->
                <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-fade-in-up delay-100 opacity-0">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-symbols-outlined text-white" style="font-size: 24px;">shopping_bag</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400 font-medium">Total</span>
                            <span class="text-xs text-blue-500 font-medium">Pesanan</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1"><?= $orderStats['total_orders'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Semua pesanan</p>
                </div>

                <!-- Selesai Card -->
                <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-fade-in-up delay-200 opacity-0">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <span class="material-symbols-outlined text-white" style="font-size: 24px;">check_circle</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400 font-medium">Pesanan</span>
                            <span class="text-xs text-emerald-500 font-medium">Selesai</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1"><?= $orderStats['completed_orders'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Berhasil dikirim</p>
                </div>

                <!-- Dibatalkan Card -->
                <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-fade-in-up delay-300 opacity-0">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg shadow-red-500/20">
                            <span class="material-symbols-outlined text-white" style="font-size: 24px;">cancel</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400 font-medium">Pesanan</span>
                            <span class="text-xs text-red-500 font-medium">Batal</span>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-1"><?= $orderStats['cancelled_orders'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Dibatalkan</p>
                </div>

                <!-- Total Belanja Card -->
                <div class="stat-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-fade-in-up delay-400 opacity-0">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#882426] to-[#a62d30] flex items-center justify-center shadow-lg shadow-[#882426]/20">
                            <span class="material-symbols-outlined text-white" style="font-size: 24px;">payments</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400 font-medium">Total</span>
                            <span class="text-xs text-[#882426] font-medium">Belanja</span>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900 mb-1">Rp <?= number_format($orderStats['total_completed_value'] ?? 0, 0, ',', '.') ?></p>
                    <p class="text-sm text-gray-500">Nilai transaksi</p>
                </div>
            </div>

            <!-- Customer Info & Timeline -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Customer Details -->
                <div class="profile-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">badge</span>
                            Informasi Pelanggan
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-[#882426]/10 transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 text-sm group-hover:text-[#882426] transition-colors">tag</span>
                                    </div>
                                    <span class="text-gray-500 text-sm">ID Pelanggan</span>
                                </div>
                                <span class="font-bold text-gray-900 font-mono">#<?= $customer['id_customer'] ?></span>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b border-gray-100 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-[#882426]/10 transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 text-sm group-hover:text-[#882426] transition-colors">phone</span>
                                    </div>
                                    <span class="text-gray-500 text-sm">No. Telepon</span>
                                </div>
                                <span class="font-semibold text-gray-900"><?= htmlspecialchars($customer['no_telp'] ?? '-') ?></span>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b border-gray-100 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-[#882426]/10 transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 text-sm group-hover:text-[#882426] transition-colors">calendar_month</span>
                                    </div>
                                    <span class="text-gray-500 text-sm">Bergabung</span>
                                </div>
                                <span class="font-semibold text-gray-900"><?= date('d M Y', strtotime($customer['created_at'])) ?></span>
                            </div>

                            <div class="flex items-center justify-between py-3 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-[#882426]/10 transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 text-sm group-hover:text-[#882426] transition-colors">update</span>
                                    </div>
                                    <span class="text-gray-500 text-sm">Terakhir Update</span>
                                </div>
                                <span class="font-semibold text-gray-900 text-sm"><?= date('d M Y H:i', strtotime($customer['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">location_on</span>
                            Alamat Tersimpan
                        </h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><?= count($addresses) ?> alamat</span>
                    </div>
                    <div class="p-4 max-h-[320px] overflow-y-auto custom-scrollbar">
                        <?php if (empty($addresses)): ?>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                    <span class="material-symbols-outlined text-gray-400 text-2xl">location_off</span>
                                </div>
                                <p class="text-gray-500 text-sm">Belum ada alamat tersimpan</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($addresses as $address): ?>
                                    <div class="address-card p-4 border-2 rounded-xl transition-all cursor-default <?= $address['default_alamat'] ? 'border-[#882426] bg-[#882426]/5' : 'border-gray-100 hover:border-gray-200' ?>">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm <?= $address['default_alamat'] ? 'text-[#882426]' : 'text-gray-400' ?>">home</span>
                                                <p class="font-bold text-gray-900"><?= htmlspecialchars($address['label_alamat'] ?? 'Alamat') ?></p>
                                            </div>
                                            <?php if ($address['default_alamat']): ?>
                                                <span class="text-[10px] bg-[#882426] text-white px-2 py-0.5 rounded-full font-bold">UTAMA</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm font-medium text-gray-800 mb-1"><?= htmlspecialchars($address['nama_penerima']) ?></p>
                                        <p class="text-xs text-gray-500 mb-1"><?= htmlspecialchars($address['nomor_hp']) ?></p>
                                        <p class="text-xs text-gray-600 leading-relaxed"><?= htmlspecialchars($address['alamat_lengkap']) ?></p>
                                        <p class="text-xs text-gray-400 mt-2">
                                            <?= htmlspecialchars($address['kelurahan']) ?>, <?= htmlspecialchars($address['kecamatan']) ?>,
                                            <?= htmlspecialchars($address['kota']) ?>, <?= htmlspecialchars($address['provinsi']) ?> <?= htmlspecialchars($address['kode_pos']) ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Activities -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">timeline</span>
                            Aktivitas Terbaru
                        </h3>
                    </div>
                    <div class="max-h-[320px] overflow-y-auto custom-scrollbar">
                        <?php if (empty($activities)): ?>
                            <div class="text-center py-8">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                    <span class="material-symbols-outlined text-gray-400 text-2xl">history</span>
                                </div>
                                <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-50">
                                <?php foreach (array_slice($activities, 0, 10) as $activity): ?>
                                    <div class="activity-item flex items-start gap-3 p-4 border-l-2 border-transparent transition-all">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                            <?php if ($activity['type'] === 'order'): ?>bg-blue-100 text-blue-600
                                            <?php elseif ($activity['type'] === 'review'): ?>bg-amber-100 text-amber-600
                                            <?php elseif ($activity['type'] === 'voucher'): ?>bg-purple-100 text-purple-600
                                            <?php else: ?>bg-gray-100 text-gray-600<?php endif; ?>">
                                            <span class="material-symbols-outlined text-lg">
                                                <?php if ($activity['type'] === 'order'): ?>shopping_bag
                                                <?php elseif ($activity['type'] === 'review'): ?>star
                                                <?php elseif ($activity['type'] === 'voucher'): ?>confirmation_number
                                                <?php else: ?>history<?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 leading-relaxed"><?= htmlspecialchars($activity['description']) ?></p>
                                            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">schedule</span>
                                                <?= date('d M Y H:i', strtotime($activity['date'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">receipt_long</span>
                        Riwayat Pesanan
                    </h3>
                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full"><?= count($orders) ?> pesanan terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status Order</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                            <span class="material-symbols-outlined text-gray-400 text-2xl">shopping_cart</span>
                                        </div>
                                        <p class="text-gray-500">Belum ada pesanan</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="order-row hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-gray-900 font-mono">#<?= htmlspecialchars($order['id_order']) ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-sm"><?= date('d M Y H:i', strtotime($order['tanggal_order'])) ?></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[60px] px-2.5 py-1 bg-gray-100 rounded-full text-sm font-medium text-gray-700">
                                                <?= $order['total_items'] ?? 1 ?> item
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900">Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4 text-center">
                                            <?php
                                            $statusConfig = [
                                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'hourglass_empty'],
                                                'diproses' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'sync'],
                                                'dikirim' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'local_shipping'],
                                                'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'check_circle'],
                                                'dibatalkan' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'cancel']
                                            ];
                                            $config = $statusConfig[$order['status_order']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'help'];
                                            ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold <?= $config['bg'] ?> <?= $config['text'] ?>">
                                                <span class="material-symbols-outlined text-sm"><?= $config['icon'] ?></span>
                                                <?= ucfirst($order['status_order']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php
                                            $payConfig = [
                                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                                'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                                'berhasil' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                                'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700']
                                            ];
                                            $payStatus = $order['status_pembayaran'] ?? 'pending';
                                            $pConfig = $payConfig[$payStatus] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                            ?>
                                            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold <?= $pConfig['bg'] ?> <?= $pConfig['text'] ?>">
                                                <?= ucfirst($payStatus) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reviews Section -->
            <?php if (!empty($reviews)): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">reviews</span>
                            Review Produk
                        </h3>
                        <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full"><?= count($reviews) ?> review</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($reviews as $review): ?>
                                <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-xl hover:border-gray-200 hover:shadow-sm transition-all">
                                    <img src="../../uploads/products/<?= htmlspecialchars($review['product_image'] ?? 'default.jpg') ?>" alt="" class="w-16 h-16 rounded-xl object-cover flex-shrink-0 border border-gray-100">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 mb-1 truncate"><?= htmlspecialchars($review['nama_product']) ?></p>
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex items-center">
                                                <?php
                                                $rating = (float)$review['rating'];
                                                $fullStars = floor($rating);
                                                $fractionalPart = $rating - $fullStars;
                                                $uniqueId = uniqid('star_cd_');
                                                for ($i = 1; $i <= 5; $i++):
                                                    if ($i <= $fullStars): ?>
                                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                        </svg>
                                                    <?php elseif ($i == $fullStars + 1 && $fractionalPart > 0):
                                                        $percentage = round($fractionalPart * 100); ?>
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                                                            <defs>
                                                                <linearGradient id="grad_<?= $uniqueId ?>_<?= $i ?>">
                                                                    <stop offset="<?= $percentage ?>%" stop-color="#FBBF24" />
                                                                    <stop offset="<?= $percentage ?>%" stop-color="#D1D5DB" />
                                                                </linearGradient>
                                                            </defs>
                                                            <path fill="url(#grad_<?= $uniqueId ?>_<?= $i ?>)" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                        </svg>
                                                <?php endif;
                                                endfor; ?>
                                            </div>
                                            <span class="text-xs text-gray-400"><?= date('d M Y', strtotime($review['tanggal_review'])) ?></span>
                                        </div>
                                        <?php if (!empty($review['komentar'])): ?>
                                            <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($review['komentar']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $review['status_review'] === 'approved' ? 'bg-green-100 text-green-700' : ($review['status_review'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                        <?php if ($review['status_review'] === 'approved'): ?>
                                            <span class="material-symbols-outlined text-sm">check</span>
                                        <?php elseif ($review['status_review'] === 'rejected'): ?>
                                            <span class="material-symbols-outlined text-sm">close</span>
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-sm">schedule</span>
                                        <?php endif; ?>
                                        <?= ucfirst($review['status_review']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>