<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\ProductRepository;
use App\Repository\DashboardRepository;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Dashboard";
include '../../components/admin/head.php';

$dashboardRepo = new DashboardRepository();
$productRepo = new ProductRepository();

$monthlyRevenue = $dashboardRepo->getMonthlyRevenue();
$totalCustomers = $dashboardRepo->getTotalCustomers();
$totalOrders = $dashboardRepo->getTotalOrders();
$pendingReturns = $dashboardRepo->getPendingReturns();
$revenueChart = $dashboardRepo->getRevenueChart(6);
$ordersChart = $dashboardRepo->getOrdersChart(6);
$recentOrders = $dashboardRepo->getRecentOrders(5);
$orderStatus = $dashboardRepo->getOrderStatusDistribution();
$topProducts = $dashboardRepo->getTopSellingProducts(5);
$paymentMethods = $dashboardRepo->getPaymentMethodDistribution();
$todayStats = $dashboardRepo->getTodayStats();
$lowStock = $productRepo->getLowStockProducts(threshold: 9, limit: 5);

$chartDataJson = json_encode([
    'revenue' => $revenueChart,
    'orders' => $ordersChart,
    'orderStatus' => $orderStatus,
    'paymentMethods' => $paymentMethods
]);
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500">Selamat datang kembali, bagaimana penjualan hari ini?</p>
            </div>

            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-lg bg-[#882426]/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#882426] text-2xl">payments</span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full <?= $monthlyRevenue['isIncrease'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $monthlyRevenue['isIncrease'] ? '+' : '' ?><?= $monthlyRevenue['change'] ?>%
                        </span>
                    </div>
                    <h2 class="text-sm text-gray-500 mb-1 font-medium">Pendapatan Bulan Ini</h2>
                    <p class="font-bold text-2xl text-gray-800">Rp <?= number_format($monthlyRevenue['value'], 0, ',', '.') ?></p>
                    <p class="text-xs text-gray-400 mt-1">Dibanding bulan lalu</p>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 text-2xl">group</span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full <?= $totalCustomers['isIncrease'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $totalCustomers['isIncrease'] ? '+' : '' ?><?= $totalCustomers['change'] ?>%
                        </span>
                    </div>
                    <h2 class="text-sm text-gray-500 mb-1 font-medium">Total Pelanggan</h2>
                    <p class="font-bold text-2xl text-gray-800"><?= number_format($totalCustomers['value']) ?></p>
                    <p class="text-xs text-gray-400 mt-1">Pelanggan terdaftar</p>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-600 text-2xl">shopping_cart</span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full <?= $totalOrders['isIncrease'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $totalOrders['isIncrease'] ? '+' : '' ?><?= $totalOrders['change'] ?> bulan ini
                        </span>
                    </div>
                    <h2 class="text-sm text-gray-500 mb-1 font-medium">Total Pesanan</h2>
                    <p class="font-bold text-2xl text-gray-800"><?= number_format($totalOrders['value']) ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= $totalOrders['thisMonth'] ?> pesanan bulan ini</p>
                </div>

                <a href="ReturnAdmin.php" class="block">
                    <div
                        class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 
               hover:shadow-md transition-shadow cursor-pointer
               hover:ring-2 hover:ring-orange-200">

                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-600 text-2xl">
                                    assignment_return
                                </span>
                            </div>

                            <?php if ($pendingReturns > 0): ?>
                                <span
                                    class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 animate-pulse">
                                    Perlu Tindakan
                                </span>
                            <?php endif; ?>
                        </div>

                        <h2 class="text-sm text-gray-500 mb-1 font-medium">Permintaan Return</h2>
                        <p class="font-bold text-2xl text-gray-800"><?= $pendingReturns ?></p>
                        <p class="text-xs text-gray-400 mt-1">Menunggu diproses</p>

                    </div>
                </a>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="rounded-xl bg-[#882426] text-white p-5 shadow-md">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-symbols-outlined text-white/80">today</span>
                        <span class="text-white/80 text-sm font-medium">Hari Ini</span>
                    </div>
                    <p class="text-3xl font-bold"><?= $todayStats['orders'] ?></p>
                    <p class="text-white/70 text-sm">pesanan baru</p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-[#882426] to-[#a62d30] text-white p-5 shadow-md">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-symbols-outlined text-white/80">account_balance_wallet</span>
                        <span class="text-white/80 text-sm font-medium">Pendapatan Hari Ini</span>
                    </div>
                    <p class="text-2xl font-bold">Rp <?= number_format($todayStats['revenue'], 0, ',', '.') ?></p>
                    <p class="text-white/70 text-sm">dari pembayaran berhasil</p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-[#6d1d1f] to-[#882426] text-white p-5 shadow-md">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-symbols-outlined text-white/80">person_add</span>
                        <span class="text-white/80 text-sm font-medium">Pelanggan Baru</span>
                    </div>
                    <p class="text-3xl font-bold"><?= $todayStats['newCustomers'] ?></p>
                    <p class="text-white/70 text-sm">bergabung hari ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 col-span-1 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Grafik Pendapatan</h2>
                            <p class="text-gray-500 text-sm">Pendapatan 6 bulan terakhir</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="switchChart('revenue')" id="btnRevenue" class="px-3 py-1.5 text-sm rounded-lg bg-[#882426] text-white transition-colors">Pendapatan</button>
                            <button onclick="switchChart('orders')" id="btnOrders" class="px-3 py-1.5 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Pesanan</button>
                        </div>
                    </div>
                    <div class="h-72">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2">Stok Menipis</h2>
                    <p class="text-gray-500 text-sm mb-4">Perlu segera diisi ulang</p>
                    <div class="space-y-3 max-h-[280px] overflow-y-auto">
                        <?php if (empty($lowStock)): ?>
                            <p class="text-center text-gray-400 text-sm py-4">Semua produk memiliki stok cukup</p>
                        <?php else: ?>
                            <?php foreach ($lowStock as $item): ?>
                                <?php
                                $stockPercentage = intval($item['stock_percentage'] ?? 0);
                                $barColor = $stockPercentage < 30 ? 'bg-red-500' : ($stockPercentage < 60 ? 'bg-yellow-400' : 'bg-green-500');
                                $imagePath = !empty($item['gambar']) ? '../../uploads/products/' . $item['gambar'] : '../../assets/img/products/default-product.jpg';
                                ?>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="<?= $imagePath ?>" class="w-10 h-10 rounded object-cover" alt="<?= htmlspecialchars($item['nama_product']) ?>">
                                        <span class="text-sm font-medium text-gray-700 flex-1 line-clamp-1"><?= htmlspecialchars($item['nama_product'] ?? 'Product') ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 <?= $barColor ?> transition-all duration-500" style="width: <?= $stockPercentage ?>%;"></div>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600">Stok: <strong><?= $item['stok'] ?? 0 ?> unit</strong> (<?= $stockPercentage ?>%)</p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="ProductAdmin.php" class="block bg-[#882426] hover:bg-[#6d1d1f] text-white font-semibold text-sm w-full py-2.5 rounded-lg mt-4 transition-colors text-center">
                        Lihat Semua Stok
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Status Pesanan</h2>
                    <div class="h-48">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Metode Pembayaran</h2>
                    <div class="h-48">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Produk Terlaris</h2>
                    <div class="space-y-3 max-h-[200px] overflow-y-auto">
                        <?php if (empty($topProducts)): ?>
                            <p class="text-center text-gray-400 text-sm py-4">Belum ada data penjualan</p>
                        <?php else: ?>
                            <?php foreach ($topProducts as $index => $product): ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-[#882426] text-white text-xs font-bold flex items-center justify-center"><?= $index + 1 ?></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($product['nama_product']) ?></p>
                                        <p class="text-xs text-gray-500"><?= number_format($product['total_sold']) ?> terjual</p>
                                    </div>
                                    <span class="text-sm font-bold text-[#882426]">Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Pesanan Terbaru</h2>
                            <p class="text-gray-500 text-sm">Pesanan yang baru saja masuk</p>
                        </div>
                        <a href="orderAdmin.php" class="text-sm text-[#882426] hover:underline font-medium">Lihat Semua</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Pembayaran</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-gray-100 text-gray-700',
                                        'dikonfirmasi' => 'bg-blue-100 text-blue-700',
                                        'diproses' => 'bg-amber-100 text-amber-700',
                                        'dikirim' => 'bg-purple-100 text-purple-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'dibatalkan' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pending',
                                        'dikonfirmasi' => 'Dikonfirmasi',
                                        'diproses' => 'Diproses',
                                        'dikirim' => 'Dikirim',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan'
                                    ];
                                    $paymentColors = [
                                        'pending' => 'bg-gray-100 text-gray-700',
                                        'verifikasi' => 'bg-amber-100 text-amber-700',
                                        'berhasil' => 'bg-green-100 text-green-700',
                                        'gagal' => 'bg-red-100 text-red-700'
                                    ];
                                    ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-mono font-medium text-[#882426]"><?= htmlspecialchars($order['id_order']) ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($order['customer_name']) ?></p>
                                            <p class="text-xs text-gray-500"><?= date('d M Y, H:i', strtotime($order['tanggal_order'])) ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-600 max-w-xs truncate"><?= htmlspecialchars($order['product_names'] ?? '-') ?></p>
                                            <p class="text-xs text-gray-400"><?= $order['item_count'] ?> item</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-bold text-gray-800">Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $paymentColors[$order['status_pembayaran']] ?? 'bg-gray-100 text-gray-700' ?>">
                                                <?= ucfirst($order['status_pembayaran'] ?? 'Pending') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$order['status_order']] ?? 'bg-gray-100 text-gray-700' ?>">
                                                <?= $statusLabels[$order['status_order']] ?? $order['status_order'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="ReturnOrderDetailPage.php?id=<?= urlencode($order['id_order']) ?>" class="text-[#882426] hover:text-[#6d1d1f] font-medium text-sm transition-colors">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = <?= $chartDataJson ?>;
        let mainChart;
        let currentChartType = 'revenue';

        const primaryColor = '#882426';
        const primaryColorLight = 'rgba(136, 36, 38, 0.1)';

        document.addEventListener('DOMContentLoaded', function() {
            initMainChart();
            initOrderStatusChart();
            initPaymentChart();
            handleLoginSuccess();
        });

        function initMainChart() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            mainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.revenue.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: chartData.revenue.data,
                        borderColor: primaryColor,
                        backgroundColor: primaryColorLight,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    if (currentChartType === 'revenue') {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                    return context.raw + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                color: '#6b7280',
                                callback: function(value) {
                                    if (currentChartType === 'revenue') {
                                        if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                        if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        return 'Rp ' + value;
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        function switchChart(type) {
            currentChartType = type;
            const data = type === 'revenue' ? chartData.revenue : chartData.orders;

            mainChart.data.labels = data.labels;
            mainChart.data.datasets[0].data = data.data;
            mainChart.data.datasets[0].label = type === 'revenue' ? 'Pendapatan' : 'Pesanan';
            mainChart.update();

            document.getElementById('btnRevenue').className = type === 'revenue' ?
                'px-3 py-1.5 text-sm rounded-lg bg-[#882426] text-white transition-colors' :
                'px-3 py-1.5 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
            document.getElementById('btnOrders').className = type === 'orders' ?
                'px-3 py-1.5 text-sm rounded-lg bg-[#882426] text-white transition-colors' :
                'px-3 py-1.5 text-sm rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors';
        }

        function initOrderStatusChart() {
            const ctx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.orderStatus.labels,
                    datasets: [{
                        data: chartData.orderStatus.data,
                        backgroundColor: ['#9ca3af', '#3b82f6', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        function initPaymentChart() {
            const ctx = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.paymentMethods.labels,
                    datasets: [{
                        data: chartData.paymentMethods.data,
                        backgroundColor: [primaryColor, '#10b981', '#f59e0b', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        function handleLoginSuccess() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('login') === 'success') {
                window.history.replaceState({}, document.title, window.location.pathname);
                createLoginConfetti();
                Swal.fire({
                    icon: 'success',
                    title: '<span style="color: #1f2937; font-weight: 700;">Login Berhasil!</span>',
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3); animation: bounceIn 0.6s ease;">
                                <i class="fas fa-sign-in-alt" style="font-size: 32px; color: white;"></i>
                            </div>
                            <p style="color: #4b5563; font-size: 15px; margin: 0; text-align: center; line-height: 1.6;">
                                Selamat datang di Admin Panel Nano Komputer.<br>
                                <span style="color: #6b7280; font-size: 13px;">Anda telah berhasil masuk ke sistem</span>
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 8px 16px; border-radius: 20px; border: 1px solid #bbf7d0;">
                                <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                <span style="color: #15803d; font-size: 12px; font-weight: 500;">Status: Terkoneksi</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-arrow-right mr-2"></i>Lanjut ke Dashboard',
                    confirmButtonColor: '#10b981',
                    background: '#ffffff',
                    backdrop: `
                        rgba(0,0,0,0.5)
                        left top
                        no-repeat
                    `,
                    customClass: {
                        popup: 'login-popup-custom',
                        confirmButton: 'login-confirm-btn'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    timer: 6000,
                    timerProgressBar: true,
                    didOpen: (popup) => {
                        createLoginConfetti();
                    }
                });
            }
        }

        function createLoginConfetti() {
            const colors = ['#10b981', '#059669', '#34d399', '#6ee7b7', '#a7f3d0'];
            const confettiContainer = document.createElement('div');
            confettiContainer.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; overflow: hidden;';
            document.body.appendChild(confettiContainer);

            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    const size = Math.random() * 10 + 5;
                    const color = colors[Math.floor(Math.random() * colors.length)];

                    confetti.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        background: ${color};
                        border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                        left: ${Math.random() * 100}%;
                        top: -20px;
                        opacity: ${Math.random() * 0.5 + 0.5};
                        animation: confettiFall ${Math.random() * 2 + 2}s linear forwards;
                        transform: rotate(${Math.random() * 360}deg);
                    `;
                    confettiContainer.appendChild(confetti);

                    setTimeout(() => confetti.remove(), 4000);
                }, i * 30);
            }

            setTimeout(() => confettiContainer.remove(), 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            handleLoginSuccess();
        });
    </script>

    <style>
        /* Login Success Modal Custom Styles */
        .login-popup-custom {
            border-radius: 20px !important;
            padding: 24px 24px 0 24px !important;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2) !important;
            overflow: hidden !important;
        }

        .login-popup-custom .swal2-html-container {
            margin-bottom: 20px !important;
        }

        .login-popup-custom .swal2-actions {
            margin-bottom: 24px !important;
        }

        .login-popup-custom .swal2-timer-progress-bar-container {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 6px !important;
            background: rgba(16, 185, 129, 0.15) !important;
            border-radius: 0 !important;
            overflow: hidden !important;
        }

        .login-popup-custom .swal2-timer-progress-bar {
            height: 100% !important;
            border-radius: 0 !important;
        }

        .login-confirm-btn {
            border-radius: 10px !important;
            padding: 12px 28px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .login-confirm-btn:hover {
            transform: scale(1.02) !important;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.2);
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* SweetAlert2 icon animation override */
        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: #10b981 !important;
        }

        /* Timer progress bar */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #10b981, #059669) !important;
        }

        /* Animate.css library integration for smooth animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -30px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate__animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        .animate__fadeInDown {
            animation-name: fadeInDown;
        }

        .animate__faster {
            animation-duration: 0.5s;
        }

        @keyframes fadeOutUp {
            from {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }

            to {
                opacity: 0;
                transform: translate3d(0, -30px, 0);
            }
        }

        .animate__fadeOutUp {
            animation-name: fadeOutUp;
        }
    </style>
</body>

</html>