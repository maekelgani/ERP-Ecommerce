<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CustomerActivityRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CustomerActivityRepository;

AuthMiddleware::requireAdminLoginFromView();

if (!PermissionHelper::canViewCustomerActivity()) {
    header('Location: ../../view/403.php');
    exit;
}

$activityRepo = new CustomerActivityRepository();

$filters = [
    'customer_id' => $_GET['customer_id'] ?? null,
    'date_from' => $_GET['date_from'] ?? null,
    'date_to' => $_GET['date_to'] ?? null,
    'limit' => 100
];
$filters = array_filter($filters, function ($v) {
    return $v !== null && $v !== '';
});

$activities = $activityRepo->getAllActivities($filters);
$stats = $activityRepo->getActivityStats();

$pageTitle = "Aktivitas Pelanggan";
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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Aktivitas Pelanggan</h1>
                <p class="text-gray-500 mt-1">Monitoring aktivitas dan interaksi pelanggan</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-blue-500">today</span>
                        <span class="text-xs text-gray-400">Hari Ini</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_today'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Aktivitas</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-green-500">date_range</span>
                        <span class="text-xs text-gray-400">Minggu Ini</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['total_this_week'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Aktivitas</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-purple-500">shopping_bag</span>
                        <span class="text-xs text-gray-400">Bulan Ini</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['orders']['this_month'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Pesanan</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-yellow-500">star</span>
                        <span class="text-xs text-gray-400">Bulan Ini</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $stats['reviews']['this_month'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Review</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <form method="GET" class="p-4 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                Filter
                            </button>
                            <?php if (!empty($filters['date_from']) || !empty($filters['date_to'])): ?>
                                <a href="CustomerActivity.php" class="px-6 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <div class="p-4">
                    <?php if (empty($activities)): ?>
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">history</span>
                            <p class="text-gray-500">Belum ada aktivitas</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php
                            $currentDate = '';
                            foreach ($activities as $activity):
                                $activityDate = date('Y-m-d', strtotime($activity['date']));
                                if ($currentDate !== $activityDate):
                                    $currentDate = $activityDate;
                            ?>
                                    <div class="flex items-center gap-2 py-2">
                                        <div class="h-px flex-1 bg-gray-200"></div>
                                        <span class="text-sm font-medium text-gray-500 px-2">
                                            <?php
                                            if ($activityDate === date('Y-m-d')) {
                                                echo 'Hari Ini';
                                            } elseif ($activityDate === date('Y-m-d', strtotime('-1 day'))) {
                                                echo 'Kemarin';
                                            } else {
                                                echo date('d F Y', strtotime($activityDate));
                                            }
                                            ?>
                                        </span>
                                        <div class="h-px flex-1 bg-gray-200"></div>
                                    </div>
                                <?php endif; ?>

                                <div class="flex items-start gap-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        <?php if ($activity['type'] === 'order'): ?>bg-blue-100 text-blue-600
                                        <?php elseif ($activity['type'] === 'review'): ?>bg-yellow-100 text-yellow-600
                                        <?php elseif ($activity['type'] === 'voucher'): ?>bg-purple-100 text-purple-600
                                        <?php else: ?>bg-gray-100 text-gray-600<?php endif; ?>">
                                        <span class="material-symbols-outlined">
                                            <?php if ($activity['type'] === 'order'): ?>shopping_bag
                                            <?php elseif ($activity['type'] === 'review'): ?>star
                                            <?php elseif ($activity['type'] === 'voucher'): ?>confirmation_number
                                            <?php else: ?>history<?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-medium text-gray-800"><?= htmlspecialchars($activity['customer_name'] ?? 'Unknown') ?></p>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($activity['description']) ?></p>
                                            </div>
                                            <span class="text-xs text-gray-400 whitespace-nowrap"><?= date('H:i', strtotime($activity['date'])) ?></span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-xs text-gray-400"><?= htmlspecialchars($activity['customer_email'] ?? '') ?></span>
                                            <a href="CustomerDetail.php?id=<?= $activity['customer_id'] ?>" class="text-xs text-blue-600 hover:underline">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>