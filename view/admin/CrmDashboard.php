<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CrmRepository.php';
require_once __DIR__ . '/../../app/Repository/CustomerFeedbackRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CrmRepository;
use App\Repository\CustomerFeedbackRepository;

AuthMiddleware::requireAdminLoginFromView();

if (!PermissionHelper::canViewCrmDashboard() && !PermissionHelper::canManageCrm()) {
    header('Location: ../../view/403.php');
    exit;
}

$crmRepo = new CrmRepository();
$feedbackRepo = new CustomerFeedbackRepository();

$stats = $crmRepo->getCustomerStats();
$mostActiveCustomers = $crmRepo->getMostActiveCustomers(5);
$recentReviews = $crmRepo->getRecentReviews(5);
$productRatings = $crmRepo->getProductRatings(5);
$reviewStats = $feedbackRepo->getReviewStats();

$pageTitle = "CRM Dashboard";
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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">CRM Dashboard</h1>
                <p class="text-gray-500 mt-1">Analisis dan statistik pelanggan</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-blue-500 text-2xl">groups</span>
                        <span class="text-xs text-gray-400">Total</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['total_customers']) ?></p>
                    <p class="text-sm text-gray-500">Pelanggan Terdaftar</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-green-500 text-2xl">person_check</span>
                        <span class="text-xs text-gray-400">Aktif</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['active_customers']) ?></p>
                    <p class="text-sm text-gray-500">Pelanggan Aktif</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-purple-500 text-2xl">person_add</span>
                        <span class="text-xs text-gray-400">Bulan Ini</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['new_this_month']) ?></p>
                    <p class="text-sm text-gray-500">Pelanggan Baru</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-yellow-500 text-2xl">star</span>
                        <span class="text-xs text-gray-400">Rating</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['average_rating'] ?>/5</p>
                    <p class="text-sm text-gray-500">Rating Rata-rata</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Pelanggan Paling Aktif</h2>
                        <p class="text-sm text-gray-500">Berdasarkan total transaksi</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Transaksi</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Belanja</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Terakhir Order</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($mostActiveCustomers)): ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data pelanggan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mostActiveCustomers as $customer): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <?php if (!empty($customer['profile_image'])): ?>
                                                        <img src="../../uploads/profiles/<?= htmlspecialchars($customer['profile_image']) ?>" alt="" class="w-8 h-8 rounded-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400 text-sm">person</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($customer['nama_lengkap']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 text-sm"><?= htmlspecialchars($customer['email']) ?></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?= $customer['total_orders'] ?>x
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium text-gray-800">Rp <?= number_format($customer['total_spending'], 0, ',', '.') ?></td>
                                            <td class="px-4 py-3 text-gray-500 text-sm">
                                                <?= $customer['last_order_date'] ? date('d M Y', strtotime($customer['last_order_date'])) : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <a href="CustomerList.php" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua Pelanggan &rarr;</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Rating Produk</h2>
                        <p class="text-sm text-gray-500">Produk dengan rating tertinggi</p>
                    </div>
                    <div class="p-4 space-y-3">
                        <?php if (empty($productRatings)): ?>
                            <p class="text-center text-gray-500 py-4">Belum ada review</p>
                        <?php else: ?>
                            <?php foreach ($productRatings as $product): ?>
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                                    <img src="../../uploads/products/<?= htmlspecialchars($product['gambar'] ?? 'default.jpg') ?>" alt="" class="w-10 h-10 rounded object-cover">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($product['nama_product']) ?></p>
                                        <div class="flex items-center gap-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="material-symbols-outlined text-xs <?= $i <= round($product['avg_rating']) ? 'text-yellow-400' : 'text-gray-300' ?>">star</span>
                                            <?php endfor; ?>
                                            <span class="text-xs text-gray-500 ml-1">(<?= $product['total_reviews'] ?>)</span>
                                        </div>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-800"><?= number_format($product['avg_rating'], 1) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Review Terbaru</h2>
                        <p class="text-sm text-gray-500">Review produk dari pelanggan</p>
                    </div>
                    <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                        <?php if (empty($recentReviews)): ?>
                            <p class="text-center text-gray-500 py-4">Belum ada review</p>
                        <?php else: ?>
                            <?php foreach ($recentReviews as $review): ?>
                                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-gray-400 text-sm">person</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($review['customer_name']) ?></p>
                                                <p class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($review['tanggal_review'])) ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="material-symbols-outlined text-xs <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>">star</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1"><?= htmlspecialchars($review['nama_product']) ?></p>
                                    <?php if (!empty($review['komentar'])): ?>
                                        <p class="text-sm text-gray-500 line-clamp-2"><?= htmlspecialchars($review['komentar']) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <a href="CustomerFeedback.php" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua Review &rarr;</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Statistik Review</h2>
                        <p class="text-sm text-gray-500">Distribusi rating pelanggan</p>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                <p class="text-2xl font-bold text-yellow-600"><?= $reviewStats['total'] ?? 0 ?></p>
                                <p class="text-xs text-gray-500">Total Review</p>
                            </div>
                            <div class="text-center p-3 bg-orange-50 rounded-lg">
                                <p class="text-2xl font-bold text-orange-600"><?= $reviewStats['pending'] ?? 0 ?></p>
                                <p class="text-xs text-gray-500">Pending</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600"><?= $reviewStats['approved'] ?? 0 ?></p>
                                <p class="text-xs text-gray-500">Approved</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <?php 
                            $ratingBars = [
                                5 => $reviewStats['rating_5'] ?? 0,
                                4 => $reviewStats['rating_4'] ?? 0,
                                3 => $reviewStats['rating_3'] ?? 0,
                                2 => $reviewStats['rating_2'] ?? 0,
                                1 => $reviewStats['rating_1'] ?? 0
                            ];
                            $maxRating = max($ratingBars) ?: 1;
                            foreach ($ratingBars as $star => $count): 
                                $percentage = ($count / $maxRating) * 100;
                            ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600 w-6"><?= $star ?></span>
                                    <span class="material-symbols-outlined text-yellow-400 text-sm">star</span>
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-yellow-400 rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500 w-8 text-right"><?= $count ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
