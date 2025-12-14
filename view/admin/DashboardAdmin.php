<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\ProductRepository;

// Check admin authentication and session expiration
AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Dashboard";
include '../../components/admin/head.php';

class DashboardData
{
    private $data;

    public function __construct($jsonFilePath)
    {
        if (file_exists($jsonFilePath)) {
            $this->data = json_decode(file_get_contents($jsonFilePath), true);
        } else {
            $this->data = [
                'stats' => [
                    'monthlyRevenue' => ['value' => 129412521, 'change' => 12.5, 'isIncrease' => true],
                    'totalUsers' => ['value' => 1092, 'change' => 8.3, 'isIncrease' => false],
                    'totalOrders' => ['value' => 12029, 'change' => null, 'isIncrease' => true],
                    'returnReq' => ['value' => 21]
                ],
                'chartData' => [100000, 120000, 150000, 140000, 160000, 180000, 200000, 190000, 210000, 220000, 230000, 240000],
                'recentOrders' => [
                    ['id' => '#ORD001', 'customer' => 'Ahmad Rizky', 'amount' => 2, 'items' => 'Monitor Gaming, Keyboard Mechanical', 'paymentStatus' => 'paid', 'paymentMethod' => 'Bank Transfer', 'status' => 'shipping'],
                    ['id' => '#ORD002', 'customer' => 'Siti Nurhaliza', 'amount' => 1, 'items' => 'Laptop ASUS ROG', 'paymentStatus' => 'paid', 'paymentMethod' => 'E-Wallet', 'status' => 'completed'],
                    ['id' => '#ORD003', 'customer' => 'Budi Santoso', 'amount' => 3, 'items' => 'CPU Ryzen 9, RAM 64GB, SSD 2TB', 'paymentStatus' => 'unpaid', 'paymentMethod' => 'COD', 'status' => 'pending'],
                ]
            ];
        }
    }

    public function getStats()
    {
        return $this->data['stats'];
    }
    public function getChartData()
    {
        return $this->data['chartData'];
    }
    public function getRecentOrders()
    {
        return $this->data['recentOrders'];
    }
}

$dashboard = new DashboardData('dashboardData.json');
$stats = $dashboard->getStats();
$chartData = $dashboard->getChartData();
$recentOrders = $dashboard->getRecentOrders();

// Get low stock products from database
$productRepo = new ProductRepository();
$lowStock = $productRepo->getLowStockProducts(threshold: 9, limit: 5);
?>

<body class="bg-gray-50 h-screen flex">
    <!-- Sidebar Component -->
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <!-- Navbar -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500">Selamat datang kembali, bagaimana penjualan hari ini?</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <!-- Card 1: Pendapatan Bulanan -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h2 class="text-sm text-gray-500 mb-2 font-medium">Pendapatan Bulanan</h2>
                    <p class="font-bold text-3xl text-gray-800 mb-2">Rp. <?= number_format($stats['monthlyRevenue']['value'], 0, ',', '.') ?></p>
                    <p class="text-sm flex items-center <?= $stats['monthlyRevenue']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                        <span class="material-symbols-outlined text-base mr-1">
                            <?= $stats['monthlyRevenue']['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                        </span>
                        <?= $stats['monthlyRevenue']['change'] ?>% dari bulan lalu
                    </p>
                </div>

                <!-- Card 2: Jumlah Users -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h2 class="text-sm text-gray-500 mb-2 font-medium">Total Users Terdaftar</h2>
                    <p class="font-bold text-3xl text-gray-800 mb-2"><?= number_format($stats['totalUsers']['value']) ?></p>
                    <p class="text-sm flex items-center <?= $stats['totalUsers']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                        <span class="material-symbols-outlined text-base mr-1">
                            <?= $stats['totalUsers']['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                        </span>
                        <?= $stats['totalUsers']['change'] ?>% dari bulan lalu
                    </p>
                </div>

                <!-- Card 3: Total Orders -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h2 class="text-sm text-gray-500 mb-2 font-medium">Total Orders</h2>
                    <p class="font-bold text-3xl text-gray-800 mb-2"><?= number_format($stats['totalOrders']['value']) ?></p>
                    <p class="text-sm flex items-center <?= $stats['totalOrders']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                        <span class="material-symbols-outlined text-base mr-1">
                            <?= $stats['totalOrders']['isIncrease'] ? 'trending_up' : 'trending_down' ?>
                        </span>
                        Pembelian <?= $stats['totalOrders']['isIncrease'] ? 'naik' : 'turun' ?>
                    </p>
                </div>

                <!-- Card 4: Return Requests -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow">
                    <h2 class="text-sm text-gray-500 mb-2 font-medium">Permintaan Pengembalian</h2>
                    <p class="font-bold text-3xl text-gray-800 mb-2"><?= $stats['returnReq']['value'] ?></p>
                    <p class="text-sm text-gray-500">Perlu ditindaklanjuti</p>
                </div>
            </div>

            <!-- Charts and Low Stock -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Chart Area -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6 col-span-1 lg:col-span-3">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Grafik Pendapatan Bulanan</h2>
                    <p class="text-gray-500 text-sm mb-4">Pantau pendapatan setiap bulannya</p>
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <p class="text-gray-400">Chart visualization akan ditampilkan di sini</p>
                    </div>
                </div>

                <!-- Low Stock -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2">Stok Menipis</h2>
                    <p class="text-gray-500 text-sm mb-4">Perlu segera diisi ulang</p>
                    <div class="space-y-3">
                        <?php if (empty($lowStock)): ?>
                            <p class="text-center text-gray-400 text-sm py-4">Semua produk memiliki stok yang cukup</p>
                        <?php else: ?>
                            <?php foreach ($lowStock as $item): ?>
                                <?php
                                $stockPercentage = intval($item['stock_percentage'] ?? 0);
                                if ($stockPercentage < 30) {
                                    $barColor = 'bg-red-500';
                                } elseif ($stockPercentage < 60) {
                                    $barColor = 'bg-yellow-400';
                                } else {
                                    $barColor = 'bg-green-500';
                                }
                                $imagePath = !empty($item['gambar']) ? '../../uploads/products/' . $item['gambar'] : '../../assets/img/products/default-product.jpg';
                                ?>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="<?= $imagePath ?>" class="w-10 h-10 rounded object-cover" alt="<?= htmlspecialchars($item['nama_product']) ?>">
                                        <span class="text-sm font-medium text-gray-700 flex-1"><?= htmlspecialchars($item['nama_product'] ?? 'Product') ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 <?= $barColor ?> transition-all duration-500" style="width: <?= $stockPercentage ?>%;"></div>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600">Stok tersisa: <strong><?= $item['stok'] ?? 0 ?> unit</strong> (<?= $stockPercentage ?>%)</p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="ProductAdmin.php" class="block bg-[#882426] hover:bg-[#6d1a1c] text-white font-semibold text-sm w-full py-2 rounded-lg mt-4 transition-colors text-center">
                        Lihat Semua Stok
                    </a>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Pembelian Terbaru</h2>
                    <p class="text-gray-500 text-sm">Menampilkan pembelian yang baru saja terjadi</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentOrders as $order): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= $order['id'] ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= $order['customer'] ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><?= $order['items'] ?></td>
                                    <td class="px-4 py-3">
                                        <?php if ($order['paymentStatus'] === 'paid'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Lunas
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Belum Bayar
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <?php if ($order['status'] === 'completed'): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-green-400 text-gray-900">Selesai</span>
                                        <?php elseif ($order['status'] === 'shipping'): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-amber-300 text-gray-900">Pengiriman</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-gray-200 text-gray-700">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('login') === 'success') {
                window.history.replaceState({}, document.title, window.location.pathname);

                Swal.fire({
                    icon: 'success',
                    title: '<span style="color: #1f2937; font-weight: 700;">Login Berhasil!</span>',
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3); animation: bounceIn 0.6s ease;">
                                <i class="fas fa-user-check" style="font-size: 32px; color: white;"></i>
                            </div>
                            <p style="color: #4b5563; font-size: 15px; margin: 0; text-align: center; line-height: 1.6;">
                                Selamat datang di Admin Panel!<br>
                                <span style="color: #6b7280; font-size: 13px;">Anda telah berhasil masuk ke sistem</span>
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 8px 16px; border-radius: 20px; border: 1px solid #bbf7d0;">
                                <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                <span style="color: #15803d; font-size: 12px; font-weight: 500;">Sesi aktif dan aman</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-tachometer-alt mr-2"></i>Lanjut ke Dashboard',
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
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (popup) => {
                        createLoginConfetti();
                    }
                });
            }
        });

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
    </script>

    <style>
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
            background: linear-gradient(90deg, #10b981, #059669) !important;
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
    </style>
</body>

</html>