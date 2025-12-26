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

// Helper function untuk render star rating dengan fractional fill
function renderStarRating($rating, $size = 'text-lg', $showNumber = true)
{
    $fullStars = floor($rating);
    $fractionalPart = $rating - $fullStars;
    $uniqueId = uniqid('star_');

    $html = '<div class="flex items-center gap-1">';

    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            // Full star
            $html .= '<svg class="' . $size . ' text-amber-400" fill="currentColor" viewBox="0 0 24 24" width="1em" height="1em"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
        } elseif ($i == $fullStars + 1 && $fractionalPart > 0) {
            // Partial star with gradient
            $percentage = round($fractionalPart * 100);
            $html .= '<svg class="' . $size . '" viewBox="0 0 24 24" width="1em" height="1em">';
            $html .= '<defs><linearGradient id="grad_' . $uniqueId . '_' . $i . '">';
            $html .= '<stop offset="' . $percentage . '%" stop-color="#FBBF24"/>';
            $html .= '<stop offset="' . $percentage . '%" stop-color="#D1D5DB"/>';
            $html .= '</linearGradient></defs>';
            $html .= '<path fill="url(#grad_' . $uniqueId . '_' . $i . ')" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>';
            $html .= '</svg>';
        } else {
            // Empty star
            $html .= '<svg class="' . $size . ' text-gray-300" fill="currentColor" viewBox="0 0 24 24" width="1em" height="1em"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
        }
    }

    if ($showNumber) {
        $html .= '<span class="text-sm text-gray-500 ml-1">(' . number_format($rating, 1) . ')</span>';
    }

    $html .= '</div>';
    return $html;
}
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto">
            <div class="p-4 md:p-8 space-y-6">
                <?php include '../../components/admin/breadcrumb.php'; ?>

                <!-- Hero Section -->
                <div class="relative overflow-hidden rounded-2xl bg-[#882426] shadow-xl">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="absolute right-0 top-0 h-full w-1/2" viewBox="0 0 400 400" fill="none">
                            <circle cx="300" cy="100" r="150" fill="white" />
                            <circle cx="350" cy="300" r="100" fill="white" />
                        </svg>
                    </div>
                    <div class="relative p-8 md:p-12">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="p-3 bg-white/20 backdrop-blur rounded-lg">
                                        <span class="material-symbols-outlined text-white text-2xl">support_agent</span>
                                    </div>
                                    <h1 class="text-3xl md:text-4xl font-bold text-white">CRM Dashboard</h1>
                                </div>
                                <p class="text-white text-lg">Analisis lengkap pelanggan, review, dan statistik kepuasan</p>
                            </div>
                            <div class="flex gap-3 flex-wrap">
                                <a href="CustomerList.php" class="px-6 py-2 bg-white text-[#882426] font-semibold rounded-lg hover:bg-red-50 transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl">
                                    <span class="material-symbols-outlined text-xl">groups</span>
                                    <span>Lihat Pelanggan</span>
                                </a>
                                <a href="CustomerFeedback.php" class="px-6 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-all duration-300 flex items-center gap-2 backdrop-blur border border-white/30">
                                    <span class="material-symbols-outlined text-xl">reviews</span>
                                    <span>Kelola Review</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Pelanggan -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl group-hover:from-blue-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-blue-600 text-3xl">groups</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-blue-50 text-blue-600 font-semibold rounded-full">Total</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Pelanggan Terdaftar</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= number_format($stats['total_customers']) ?></h2>
                                <span class="text-xs text-gray-400">pelanggan</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Total pelanggan yang terdaftar</p>
                        </div>
                    </div>

                    <!-- Pelanggan Aktif -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl group-hover:from-emerald-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-emerald-600 text-3xl">person_check</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-emerald-50 text-emerald-600 font-semibold rounded-full">Aktif</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Pelanggan Aktif</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= number_format($stats['active_customers']) ?></h2>
                                <span class="text-xs text-gray-400">aktif</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Pelanggan dengan transaksi aktif</p>
                        </div>
                    </div>

                    <!-- Pelanggan Baru -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl group-hover:from-purple-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-purple-600 text-3xl">person_add</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-purple-50 text-purple-600 font-semibold rounded-full">Bulan Ini</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Pelanggan Baru</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= number_format($stats['new_this_month']) ?></h2>
                                <span class="text-xs text-gray-400">baru</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Bergabung bulan ini</p>
                        </div>
                    </div>

                    <!-- Rating Rata-rata dengan Star Fill -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl group-hover:from-amber-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-amber-600 text-3xl">star</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-amber-50 text-amber-600 font-semibold rounded-full">Rating</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Rating Rata-rata</h3>
                            <div class="flex items-baseline gap-2 mb-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= number_format($stats['average_rating'], 1) ?></h2>
                                <span class="text-lg text-gray-400">/ 5</span>
                            </div>
                            <?= renderStarRating($stats['average_rating'], 'w-5 h-5', false) ?>
                            <p class="text-xs text-gray-400 mt-2">Kepuasan pelanggan keseluruhan</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Pelanggan Paling Aktif -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg">
                                    <span class="material-symbols-outlined text-blue-600 text-xl">trending_up</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Pelanggan Paling Aktif</h2>
                                    <p class="text-sm text-gray-500">Berdasarkan total transaksi</p>
                                </div>
                            </div>
                            <a href="CustomerList.php" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
                                Lihat Semua
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pelanggan</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaksi</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Belanja</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Terakhir Order</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (empty($mostActiveCustomers)): ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <span class="material-symbols-outlined text-gray-300 text-5xl mb-3">person_off</span>
                                                    <p class="text-gray-500">Belum ada data pelanggan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($mostActiveCustomers as $index => $customer): ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="relative">
                                                            <?php if (!empty($customer['profile_image'])): ?>
                                                                <img src="../../uploads/customers/<?= htmlspecialchars($customer['profile_image']) ?>" alt="" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100">
                                                            <?php else: ?>
                                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center ring-2 ring-gray-100">
                                                                    <span class="material-symbols-outlined text-blue-600">person</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($index < 3): ?>
                                                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-400 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm">
                                                                    <?= $index + 1 ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="font-semibold text-gray-800"><?= htmlspecialchars($customer['nama_lengkap']) ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600 text-sm"><?= htmlspecialchars($customer['email']) ?></td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                                        <?= $customer['total_orders'] ?>x
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right font-bold text-gray-800">Rp <?= number_format($customer['total_spending'], 0, ',', '.') ?></td>
                                                <td class="px-6 py-4 text-gray-500 text-sm">
                                                    <?= $customer['last_order_date'] ? date('d M Y', strtotime($customer['last_order_date'])) : '-' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rating Produk -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-amber-100 to-amber-50 rounded-lg">
                                    <span class="material-symbols-outlined text-amber-600 text-xl">workspace_premium</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Rating Produk</h2>
                                    <p class="text-sm text-gray-500">Produk dengan rating tertinggi</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                            <?php if (empty($productRatings)): ?>
                                <div class="flex flex-col items-center py-8">
                                    <span class="material-symbols-outlined text-gray-300 text-5xl mb-3">star_border</span>
                                    <p class="text-gray-500">Belum ada review</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($productRatings as $index => $product): ?>
                                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-all duration-200 border border-transparent hover:border-gray-100 group">
                                        <div class="relative">
                                            <img src="../../uploads/products/<?= htmlspecialchars($product['gambar'] ?? 'default.jpg') ?>" alt="" class="w-14 h-14 rounded-xl object-cover ring-2 ring-gray-100 group-hover:ring-amber-200 transition-all">
                                            <?php if ($index === 0): ?>
                                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center shadow-sm">
                                                    <span class="material-symbols-outlined text-white text-sm">emoji_events</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($product['nama_product']) ?></p>
                                            <div class="mt-1">
                                                <?= renderStarRating($product['avg_rating'], 'w-4 h-4', false) ?>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1"><?= $product['total_reviews'] ?> review</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-2xl font-bold text-gray-800"><?= number_format($product['avg_rating'], 1) ?></span>
                                            <p class="text-xs text-gray-400">/5</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section: Reviews & Statistics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Review Terbaru -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-lg">
                                    <span class="material-symbols-outlined text-indigo-600 text-xl">rate_review</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Review Terbaru</h2>
                                    <p class="text-sm text-gray-500">Review produk dari pelanggan</p>
                                </div>
                            </div>
                            <a href="CustomerFeedback.php" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition-colors">
                                Semua
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                        <div class="p-4 space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                            <?php if (empty($recentReviews)): ?>
                                <div class="flex flex-col items-center py-8">
                                    <span class="material-symbols-outlined text-gray-300 text-5xl mb-3">reviews</span>
                                    <p class="text-gray-500">Belum ada review</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentReviews as $review): ?>
                                    <div class="p-4 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all duration-200">
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($review['profile_image'])): ?>
                                                    <img src="../../uploads/customers/<?= htmlspecialchars($review['profile_image']) ?>"
                                                        alt=""
                                                        class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50
                flex items-center justify-center ring-2 ring-gray-100">
                                                        <span class="material-symbols-outlined text-blue-600">person</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($review['customer_name']) ?></p>
                                                    <p class="text-xs text-gray-400"><?= date('d M Y, H:i', strtotime($review['tanggal_review'])) ?></p>
                                                </div>
                                            </div>
                                            <?= renderStarRating($review['rating'], 'w-4 h-4', false) ?>
                                        </div>
                                        <div class="pl-13">
                                            <p class="text-sm font-medium text-indigo-600 mb-1"><?= htmlspecialchars($review['nama_product']) ?></p>
                                            <?php if (!empty($review['komentar'])): ?>
                                                <p class="text-sm text-gray-600 line-clamp-2">"<?= htmlspecialchars($review['komentar']) ?>"</p>
                                            <?php else: ?>
                                                <p class="text-sm text-gray-400 italic">Tidak ada komentar</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Statistik Review -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-lg">
                                    <span class="material-symbols-outlined text-emerald-600 text-xl">analytics</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Statistik Review</h2>
                                    <p class="text-sm text-gray-500">Distribusi rating pelanggan</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Summary Cards -->
                            <div class="grid grid-cols-3 gap-4 mb-8">
                                <div class="text-center p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                                    <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="material-symbols-outlined text-white text-xl">reviews</span>
                                    </div>
                                    <p class="text-2xl font-bold text-amber-700"><?= $reviewStats['total'] ?? 0 ?></p>
                                    <p class="text-xs text-amber-600 font-medium">Total Review</p>
                                </div>
                                <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="material-symbols-outlined text-white text-xl">pending</span>
                                    </div>
                                    <p class="text-2xl font-bold text-orange-700"><?= $reviewStats['pending'] ?? 0 ?></p>
                                    <p class="text-xs text-orange-600 font-medium">Pending</p>
                                </div>
                                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="material-symbols-outlined text-white text-xl">check_circle</span>
                                    </div>
                                    <p class="text-2xl font-bold text-green-700"><?= $reviewStats['approved'] ?? 0 ?></p>
                                    <p class="text-xs text-green-600 font-medium">Approved</p>
                                </div>
                            </div>

                            <!-- Rating Distribution -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-500 text-lg">bar_chart</span>
                                    Distribusi Rating
                                </h4>
                                <?php
                                $ratingBars = [
                                    5 => $reviewStats['rating_5'] ?? 0,
                                    4 => $reviewStats['rating_4'] ?? 0,
                                    3 => $reviewStats['rating_3'] ?? 0,
                                    2 => $reviewStats['rating_2'] ?? 0,
                                    1 => $reviewStats['rating_1'] ?? 0
                                ];
                                $maxRating = max($ratingBars) ?: 1;
                                $totalRatings = array_sum($ratingBars) ?: 1;
                                $ratingColors = [
                                    5 => 'from-emerald-400 to-emerald-500',
                                    4 => 'from-lime-400 to-lime-500',
                                    3 => 'from-amber-400 to-amber-500',
                                    2 => 'from-orange-400 to-orange-500',
                                    1 => 'from-red-400 to-red-500'
                                ];
                                foreach ($ratingBars as $star => $count):
                                    $percentage = ($count / $maxRating) * 100;
                                    $percentOfTotal = round(($count / $totalRatings) * 100);
                                ?>
                                    <div class="flex items-center gap-3 group">
                                        <div class="flex items-center gap-1 w-12">
                                            <span class="text-sm font-bold text-gray-700"><?= $star ?></span>
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r <?= $ratingColors[$star] ?> rounded-full transition-all duration-500 group-hover:opacity-80" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <div class="flex items-center gap-2 w-20 justify-end">
                                            <span class="text-sm font-bold text-gray-700"><?= $count ?></span>
                                            <span class="text-xs text-gray-400">(<?= $percentOfTotal ?>%)</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pl-13 {
            padding-left: 52px;
        }
    </style>
</body>

</html>