<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\AnalyticsRepository;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Analitik";
include '../../components/admin/head.php';

$period = $_GET['period'] ?? 'all';
$validPeriods = ['today', '7', '30', '90', 'all'];
if (!in_array($period, $validPeriods)) {
    $period = 'all';
}

$analyticsRepo = new AnalyticsRepository();

$totalRevenue = $analyticsRepo->getTotalRevenue($period);
$avgPurchase = $analyticsRepo->getAveragePurchasePrice($period);
$productsSold = $analyticsRepo->getProductsSold($period);
$avgRating = $analyticsRepo->getAverageProductRating();

$monthlyRevenue = $analyticsRepo->getMonthlyRevenue(6);
$ordersAndUsers = $analyticsRepo->getMonthlyOrdersAndUsers(6);
$categorySales = $analyticsRepo->getCategorySales($period, 5);
$topProducts = $analyticsRepo->getTopSellingProducts($period, 5);

$chartDataJson = json_encode([
    'monthlyRevenue' => $monthlyRevenue,
    'ordersAndUsers' => $ordersAndUsers,
    'categorySales' => $categorySales
]);
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Analitik</h1>
                    <p class="text-gray-500">Laporan komprehensif tentang performa website dan penjualan</p>
                </div>
                <div class="flex items-center gap-2">
                    <select id="periodFilter" onchange="changePeriod(this.value)" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#882426] focus:border-transparent cursor-pointer">
                        <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Hari ini</option>
                        <option value="7" <?= $period === '7' ? 'selected' : '' ?>>7 Hari terakhir</option>
                        <option value="30" <?= $period === '30' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                        <option value="90" <?= $period === '90' ? 'selected' : '' ?>>90 Hari Terakhir</option>
                        <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>Semua Waktu</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm text-gray-500 font-medium">Total Pendapatan</h2>
                        <span class="material-symbols-outlined text-[#882426]">payments</span>
                    </div>
                    <p class="font-bold text-2xl md:text-3xl text-gray-800 mb-2">Rp. <?= number_format($totalRevenue['value'], 0, ',', '.') ?></p>
                    <?php if ($period !== 'all' && $totalRevenue['change'] != 0): ?>
                        <p class="text-sm flex items-center <?= $totalRevenue['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            <span class="material-symbols-outlined text-base mr-1">
                                <?= $totalRevenue['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                            </span>
                            <?= abs($totalRevenue['change']) ?>% dari periode sebelumnya
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">Total keseluruhan</p>
                    <?php endif; ?>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm text-gray-500 font-medium">Rata-rata Harga Pembelian</h2>
                        <span class="material-symbols-outlined text-blue-600">shopping_cart</span>
                    </div>
                    <p class="font-bold text-2xl md:text-3xl text-gray-800 mb-2">Rp. <?= number_format($avgPurchase['value'], 0, ',', '.') ?></p>
                    <?php if ($period !== 'all' && $avgPurchase['change'] != 0): ?>
                        <p class="text-sm flex items-center <?= $avgPurchase['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            <span class="material-symbols-outlined text-base mr-1">
                                <?= $avgPurchase['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                            </span>
                            <?= abs($avgPurchase['change']) ?>% dari periode sebelumnya
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">Rata-rata per transaksi</p>
                    <?php endif; ?>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm text-gray-500 font-medium">Produk Terjual</h2>
                        <span class="material-symbols-outlined text-green-600">inventory_2</span>
                    </div>
                    <p class="font-bold text-2xl md:text-3xl text-gray-800 mb-2"><?= number_format($productsSold['value']) ?></p>
                    <?php if ($period !== 'all' && $productsSold['change'] != 0): ?>
                        <p class="text-sm flex items-center <?= $productsSold['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            <span class="material-symbols-outlined text-base mr-1">
                                <?= $productsSold['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                            </span>
                            <?= abs($productsSold['change']) ?>% dari periode sebelumnya
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">Total unit terjual</p>
                    <?php endif; ?>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm text-gray-500 font-medium">Rata-rata Rating Produk</h2>
                        <span class="material-symbols-outlined text-amber-500">star</span>
                    </div>
                    <div class="flex items-baseline gap-2 mb-2">
                        <p class="font-bold text-2xl md:text-3xl text-gray-800"><?= $avgRating['value'] ?></p>
                        <span class="text-lg text-gray-500">/ 5</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= floor($avgRating['value'])): ?>
                                <span class="material-symbols-outlined text-amber-400 text-lg">star</span>
                            <?php elseif ($i - 0.5 <= $avgRating['value']): ?>
                                <span class="material-symbols-outlined text-amber-400 text-lg">star_half</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-gray-300 text-lg">star</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span class="text-sm text-gray-400 ml-1">(<?= $avgRating['total_reviews'] ?> ulasan)</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap mb-6">
                <nav class="bg-gray-100 rounded-lg p-1 font-semibold text-sm gap-2 flex">
                    <button id="tab-pendapatan" onclick="switchTab('pendapatan')" class="tab-btn-analitik p-2 px-4 rounded-lg bg-white text-gray-800 shadow-sm cursor-pointer transition-all">Pendapatan</button>
                    <button id="tab-produk" onclick="switchTab('produk')" class="tab-btn-analitik p-2 px-4 rounded-lg text-gray-500 hover:text-gray-700 cursor-pointer transition-all">Analisis Produk</button>
                </nav>
            </div>

            <div id="content-pendapatan" class="tab-content-analitik">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Pendapatan Toko</h2>
                            <p class="text-gray-500 text-sm">Grafik pendapatan bulanan selama 6 bulan terakhir</p>
                        </div>
                        <div class="h-72">
                            <canvas id="chartPendapatan"></canvas>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Order dan Users</h2>
                            <p class="text-gray-500 text-sm">Grafik perbandingan total order dan total users</p>
                        </div>
                        <div class="h-72">
                            <canvas id="chartUserVsOrder"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div id="content-produk" class="tab-content-analitik hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Kategori Terpopuler</h2>
                            <p class="text-gray-500 text-sm">Distribusi penjualan berdasarkan kategori</p>
                        </div>
                        <div class="h-72 flex items-center justify-center">
                            <canvas id="chartCategorySales"></canvas>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Produk Terpopuler</h2>
                            <p class="text-gray-500 text-sm">Produk dengan penjualan tertinggi</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Terjual</th>
                                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if (empty($topProducts)): ?>
                                        <tr>
                                            <td colspan="3" class="px-3 py-8 text-center text-gray-400">
                                                <span class="material-symbols-outlined text-4xl mb-2 block">inventory_2</span>
                                                Belum ada data penjualan
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($topProducts as $index => $product): ?>
                                            <?php
                                            $imagePath = !empty($product['gambar'])
                                                ? '../../uploads/products/' . $product['gambar']
                                                : '../../assets/img/products/default-product.jpg';
                                            ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-3 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#882426] text-white text-xs font-bold flex items-center justify-center">
                                                            <?= $index + 1 ?>
                                                        </span>
                                                        <img src="<?= htmlspecialchars($imagePath) ?>"
                                                            alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                                            class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                                        <div class="min-w-0">
                                                            <h3 class="text-sm font-medium text-gray-800 truncate max-w-xs" title="<?= htmlspecialchars($product['nama_product']) ?>">
                                                                <?= htmlspecialchars($product['nama_product']) ?>
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <?= number_format($product['total_sold']) ?> unit
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 text-right font-semibold text-gray-800">
                                                    Rp. <?= number_format($product['total_revenue'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = <?= $chartDataJson ?>;

        function changePeriod(period) {
            window.location.href = '?period=' + period;
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-content-analitik').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.tab-btn-analitik').forEach(btn => {
                btn.classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
                btn.classList.add('text-gray-500');
            });

            document.getElementById('content-' + tab).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.add('bg-white', 'text-gray-800', 'shadow-sm');
            activeBtn.classList.remove('text-gray-500');
        }

        document.addEventListener("DOMContentLoaded", () => {
            const ctxPendapatan = document.getElementById('chartPendapatan');
            if (ctxPendapatan) {
                new Chart(ctxPendapatan, {
                    type: 'line',
                    data: {
                        labels: chartData.monthlyRevenue.labels,
                        datasets: [{
                            label: 'Total Pendapatan per Bulan',
                            data: chartData.monthlyRevenue.data,
                            borderColor: '#882426',
                            backgroundColor: 'rgba(136, 36, 38, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#882426',
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
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp. ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp. ' + (value / 1000000).toFixed(1) + 'jt';
                                        } else if (value >= 1000) {
                                            return 'Rp. ' + (value / 1000).toFixed(0) + 'rb';
                                        }
                                        return 'Rp. ' + value;
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const ctxUserVsOrder = document.getElementById('chartUserVsOrder');
            if (ctxUserVsOrder) {
                new Chart(ctxUserVsOrder, {
                    type: 'bar',
                    data: {
                        labels: chartData.ordersAndUsers.labels,
                        datasets: [{
                                label: 'User Terdaftar',
                                data: chartData.ordersAndUsers.users,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            },
                            {
                                label: 'Total Order',
                                data: chartData.ordersAndUsers.orders,
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const ctxCategory = document.getElementById('chartCategorySales');
            if (ctxCategory) {
                const categoryColors = [
                    'rgba(136, 36, 38, 0.85)',
                    'rgba(54, 162, 235, 0.85)',
                    'rgba(255, 206, 86, 0.85)',
                    'rgba(75, 192, 192, 0.85)',
                    'rgba(153, 102, 255, 0.85)',
                    'rgba(255, 159, 64, 0.85)',
                    'rgba(199, 199, 199, 0.85)'
                ];

                new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.categorySales.labels,
                        datasets: [{
                            data: chartData.categorySales.data,
                            backgroundColor: categoryColors.slice(0, chartData.categorySales.labels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 12,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${context.raw} unit (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        });
    </script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>