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

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <a href="CustomerList.php" class="inline-flex items-center gap-1 text-gray-500 hover:text-gray-700 mb-2">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Kembali ke Daftar Pelanggan
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Pelanggan</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-start gap-4 mb-6">
                        <?php if (!empty($customer['profile_image'])): ?>
                            <img src="../../uploads/customers/<?= htmlspecialchars($customer['profile_image']) ?>" alt="<?= htmlspecialchars($customer['nama_lengkap']) ?>" class="w-16 h-16 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-2xl">person</span>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($customer['nama_lengkap']) ?></h2>
                            <p class="text-gray-500"><?= htmlspecialchars($customer['email']) ?></p>
                            <?php if ($customer['is_active']): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">ID Pelanggan</span>
                            <span class="font-medium text-gray-800">#<?= $customer['id_customer'] ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">No. Telepon</span>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($customer['no_telp'] ?? '-') ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Tipe Login</span>

                            <?php if (($customer['login_type'] ?? 'regular') === 'google'): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-xs">
                                    <div class="w-5 h-5 rounded-xl bg-white flex items-center justify-center shadow-sm flex-shrink-0">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                        </svg>
                                    </div>
                                    Google
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    <span class="material-symbols-outlined text-xs">mail</span>
                                    Regular
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Bergabung</span>
                            <span class="font-medium text-gray-800"><?= date('d M Y', strtotime($customer['created_at'])) ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-500">Terakhir Update</span>
                            <span class="font-medium text-gray-800"><?= date('d M Y H:i', strtotime($customer['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Total Orders Card -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-blue-500" style="font-size: 20px;">shopping_bag</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-800 mb-1"><?= $orderStats['total_orders'] ?? 0 ?></p>
                        <p class="text-sm text-gray-500">Total Orders</p>
                    </div>

                    <!-- Selesai Card -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-green-500" style="font-size: 20px;">check_circle</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-800 mb-1"><?= $orderStats['completed_orders'] ?? 0 ?></p>
                        <p class="text-sm text-gray-500">Selesai</p>
                    </div>

                    <!-- Dibatalkan Card -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-red-500" style="font-size: 20px;">cancel</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-800 mb-1"><?= $orderStats['cancelled_orders'] ?? 0 ?></p>
                        <p class="text-sm text-gray-500">Dibatalkan</p>
                    </div>

                    <!-- Total Belanja Card -->
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-purple-500" style="font-size: 20px;">payments</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">Rp <?= number_format($orderStats['total_completed_value'] ?? 0, 0, ',', '.') ?></p>
                        <p class="text-sm text-gray-500">Total Belanja</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Alamat Tersimpan</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-64 overflow-y-auto">
                        <?php if (empty($addresses)): ?>
                            <p class="text-center text-gray-500 py-4">Belum ada alamat tersimpan</p>
                        <?php else: ?>
                            <?php foreach ($addresses as $address): ?>
                                <div class="p-3 border border-gray-200 rounded-lg <?= $address['default_alamat'] ? 'border-blue-300 bg-blue-50' : '' ?>">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($address['label_alamat'] ?? 'Alamat') ?></p>
                                        <?php if ($address['default_alamat']): ?>
                                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">UTAMA</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($address['nama_penerima']) ?> - <?= htmlspecialchars($address['nomor_hp']) ?></p>
                                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($address['alamat_lengkap']) ?></p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?= htmlspecialchars($address['kelurahan']) ?>, <?= htmlspecialchars($address['kecamatan']) ?>,
                                        <?= htmlspecialchars($address['kota']) ?>, <?= htmlspecialchars($address['provinsi']) ?> <?= htmlspecialchars($address['kode_pos']) ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <?php if (empty($activities)): ?>
                            <p class="text-center text-gray-500 py-4">Belum ada aktivitas</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach (array_slice($activities, 0, 10) as $activity): ?>
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            <?php if ($activity['type'] === 'order'): ?>bg-blue-100 text-blue-600
                                            <?php elseif ($activity['type'] === 'review'): ?>bg-yellow-100 text-yellow-600
                                            <?php elseif ($activity['type'] === 'voucher'): ?>bg-purple-100 text-purple-600
                                            <?php else: ?>bg-gray-100 text-gray-600<?php endif; ?>">
                                            <span class="material-symbols-outlined text-sm">
                                                <?php if ($activity['type'] === 'order'): ?>shopping_bag
                                                <?php elseif ($activity['type'] === 'review'): ?>star
                                                <?php elseif ($activity['type'] === 'voucher'): ?>confirmation_number
                                                <?php else: ?>history<?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800"><?= htmlspecialchars($activity['description']) ?></p>
                                            <p class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($activity['date'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Riwayat Pesanan</h3>
                    <span class="text-sm text-gray-500"><?= count($orders) ?> pesanan terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status Order</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada pesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800">#<?= htmlspecialchars($order['id_order']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= date('d M Y H:i', strtotime($order['tanggal_order'])) ?></td>
                                        <td class="px-4 py-3 text-center"><?= $order['total_items'] ?? 1 ?> item</td>
                                        <td class="px-4 py-3 text-right font-medium">Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <?php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'diproses' => 'bg-blue-100 text-blue-700',
                                                'dikirim' => 'bg-purple-100 text-purple-700',
                                                'selesai' => 'bg-green-100 text-green-700',
                                                'dibatalkan' => 'bg-red-100 text-red-700'
                                            ];
                                            $color = $statusColors[$order['status_order']] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $color ?>">
                                                <?= ucfirst($order['status_order']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php
                                            $payColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'success' => 'bg-green-100 text-green-700',
                                                'failed' => 'bg-red-100 text-red-700'
                                            ];
                                            $payColor = $payColors[$order['status_pembayaran'] ?? 'pending'] ?? 'bg-gray-100 text-gray-700';
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $payColor ?>">
                                                <?= ucfirst($order['status_pembayaran'] ?? 'pending') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (!empty($reviews)): ?>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Review Produk</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <?php foreach ($reviews as $review): ?>
                            <div class="flex items-start gap-4 p-3 border border-gray-100 rounded-lg">
                                <img src="../../uploads/products/<?= htmlspecialchars($review['product_image'] ?? 'default.jpg') ?>" alt="" class="w-12 h-12 rounded object-cover">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($review['nama_product']) ?></p>
                                    <div class="flex items-center gap-1 my-1">
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
                                        <span class="text-xs text-gray-500 ml-1"><?= date('d M Y', strtotime($review['tanggal_review'])) ?></span>
                                    </div>
                                    <?php if (!empty($review['komentar'])): ?>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($review['komentar']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="inline-flex px-2 py-0.5 rounded text-xs <?= $review['status_review'] === 'approved' ? 'bg-green-100 text-green-700' : ($review['status_review'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                    <?= ucfirst($review['status_review']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>