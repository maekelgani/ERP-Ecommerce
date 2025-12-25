<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Monitoring";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Monitoring Promo</h1>
                <p class="text-gray-500 mt-1">Pantau performa kampanye dan voucher secara real-time</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-2xl text-[#882426]">campaign</span>
                        <span class="text-xs text-gray-400">Kampanye</span>
                    </div>
                    <p id="statCampaigns" class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-500 mt-1"><span id="statCampaignsActive" class="text-emerald-600 font-medium">0</span> aktif</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-2xl text-purple-500">percent</span>
                        <span class="text-xs text-gray-400">Diskon</span>
                    </div>
                    <p id="statDiscounts" class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-500 mt-1"><span id="statDiscountsActive" class="text-emerald-600 font-medium">0</span> aktif</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-2xl text-teal-500">confirmation_number</span>
                        <span class="text-xs text-gray-400">Voucher</span>
                    </div>
                    <p id="statVouchers" class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-500 mt-1"><span id="statVouchersActive" class="text-emerald-600 font-medium">0</span> aktif</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="material-symbols-outlined text-2xl text-orange-500">redeem</span>
                        <span class="text-xs text-gray-400">Total Redemption</span>
                    </div>
                    <p id="statRedemptions" class="text-3xl font-bold text-gray-800">0</p>
                    <p class="text-sm text-gray-500 mt-1">penggunaan hari ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-gray-800">Tren Penggunaan Voucher</h2>
                        <select id="chartPeriod" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5">
                            <option value="daily">Harian</option>
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>
                    <canvas id="usageChart" height="200"></canvas>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                    <h2 class="font-semibold text-gray-800 mb-4">Voucher Terpopuler</h2>
                    <div id="topVouchers" class="space-y-3">
                        <p class="text-gray-500 text-center py-4">Memuat data...</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">Aktivitas Terbaru</h2>
                    <button onclick="loadRecentActivity()" class="text-sm text-[#882426] hover:underline">Refresh</button>
                </div>
                <div id="recentActivity" class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                    <p class="text-gray-500 text-center py-8">Memuat aktivitas...</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        let usageChart;

        document.addEventListener('DOMContentLoaded', () => {
            loadDashboard();
            loadUsageChart('daily');
            loadRecentActivity();

            document.getElementById('chartPeriod').addEventListener('change', (e) => {
                loadUsageChart(e.target.value);
            });

            setInterval(loadDashboard, 60000);
        });

        async function loadDashboard() {
            try {
                const response = await fetch('../../app/controllers/promoReportController.php?action=dashboard');
                const data = await response.json();

                if (data.success) {
                    const stats = data.data;

                    document.getElementById('statCampaigns').textContent = stats.campaigns.total;
                    document.getElementById('statCampaignsActive').textContent = stats.campaigns.active;
                    document.getElementById('statDiscounts').textContent = stats.discounts.total;
                    document.getElementById('statDiscountsActive').textContent = stats.discounts.active;
                    document.getElementById('statVouchers').textContent = stats.vouchers.total;
                    document.getElementById('statVouchersActive').textContent = stats.vouchers.active;

                    const todayRedemptions = stats.recent_usage.filter(u => {
                        const today = new Date().toDateString();
                        return new Date(u.digunakan_pada).toDateString() === today;
                    }).length;
                    document.getElementById('statRedemptions').textContent = todayRedemptions;

                    renderTopVouchers(stats.top_vouchers);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderTopVouchers(vouchers) {
            const container = document.getElementById('topVouchers');

            if (!vouchers || vouchers.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada data</p>';
                return;
            }

            const maxUsage = Math.max(...vouchers.map(v => v.total_penggunaan || 0));

            container.innerHTML = vouchers.map((v, i) => `
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600">${i + 1}</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-mono text-sm font-medium">${v.kode}</span>
                            <span class="text-sm text-gray-500">${v.total_penggunaan || 0}x</span>
                        </div>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full">
                            <div class="h-1.5 bg-[#882426] rounded-full" style="width: ${maxUsage > 0 ? ((v.total_penggunaan || 0) / maxUsage * 100) : 0}%"></div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function loadUsageChart(period) {
            try {
                const response = await fetch(`../../app/controllers/promoReportController.php?action=usage_stats&period=${period}`);
                const data = await response.json();

                if (data.success) {
                    const stats = data.data.reverse();

                    const labels = stats.map(s => s.periode);
                    const usageData = stats.map(s => s.total_penggunaan);
                    const discountData = stats.map(s => parseFloat(s.total_diskon) / 1000);

                    if (usageChart) {
                        usageChart.destroy();
                    }

                    const ctx = document.getElementById('usageChart').getContext('2d');
                    usageChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Penggunaan',
                                    data: usageData,
                                    borderColor: '#882426',
                                    backgroundColor: 'rgba(136, 36, 38, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Diskon (ribu)',
                                    data: discountData,
                                    borderColor: '#10b981',
                                    backgroundColor: 'transparent',
                                    borderDash: [5, 5],
                                    tension: 0.3,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    beginAtZero: true
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    beginAtZero: true,
                                    grid: {
                                        drawOnChartArea: false
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadRecentActivity() {
            try {
                const response = await fetch('../../app/controllers/promoReportController.php?action=redemption_log&limit=20');
                const data = await response.json();

                const container = document.getElementById('recentActivity');

                if (data.success && data.data.length > 0) {
                    container.innerHTML = data.data.map(a => `
                        <div class="flex items-center gap-4 p-4 hover:bg-gray-50">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600">confirmation_number</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">
                                    <span class="font-mono text-[#882426]">${a.kode}</span> 
                                    digunakan oleh ${a.nama_pelanggan || 'Guest'}
                                </p>
                                <p class="text-sm text-gray-500">${a.nama_voucher || '-'}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-[#882426]">-Rp ${Number(a.jumlah_diskon).toLocaleString('id-ID')}</p>
                                <p class="text-xs text-gray-400">${new Date(a.digunakan_pada).toLocaleString('id-ID')}</p>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-center py-8">Belum ada aktivitas</p>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>

</html>