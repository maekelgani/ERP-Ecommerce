    <?php
    // Class OOP untuk mengambil data dari JSON
    class DashboardData {
        private $data;

        public function __construct($jsonFilePath) {
            if (file_exists($jsonFilePath)) {
                $this->data = json_decode(file_get_contents($jsonFilePath), true);
            } else {
                // Dummy data jika JSON tidak ada
                $this->data = [
                    'stats' => [
                        'monthlyRevenue' => ['value' => 129412521, 'change' => 100, 'isIncrease' => true],
                        'totalUsers' => ['value' => 1092, 'change' => 100, 'isIncrease' => false],
                        'totalOrders' => ['value' => 12029, 'change' => null, 'isIncrease' => true],
                        'returnReq' => ['value' => 21]
                    ],
                    'chartData' => [100000, 120000, 150000, 140000, 160000, 180000, 200000, 190000, 210000, 220000, 230000, 240000], // 12 bulan
                    'lowStock' => [
                        ['image' => 'https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg', 'percentage' => 10],
                        ['image' => 'https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg', 'percentage' => 20],
                        // Tambahkan lebih banyak jika perlu
                    ],
                    'recentOrders' => [
                        ['id' => '#ORD69', 'customer' => 'Juan Kamil', 'amount' => 2, 'items' => 'Monitor ZOWIE xl2546x, CPU i9 14th 14900k', 'paymentStatus' => 'paid', 'paymentMethod' => 'Bank Transfer', 'status' => 'shipping']
                    ]
                ];
            }
        }

        public function getStats() {
            return $this->data['stats'];
        }

        public function getChartData() {
            return $this->data['chartData'];
        }

        public function getLowStock() {
            return $this->data['lowStock'];
        }

        public function getRecentOrders() {
            return $this->data['recentOrders'];
        }
    }

    // Inisialisasi data
    $dashboard = new DashboardData('dashboardData.json'); // Path ke file JSON
    $stats = $dashboard->getStats();
    $chartData = $dashboard->getChartData();
    $lowStock = $dashboard->getLowStock();
    $recentOrders = $dashboard->getRecentOrders();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Admin</title>
        <link rel="stylesheet" href="../../src/output.css">
        <link rel="icon" href="../assets/img/Nanocomp.png">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    </head>

    <body class="bg-no-repeat h-screen flex">
        <!-- Leftside: Sidebar (Responsive: Collapsible di mobile) -->
        <aside id="sidebar" class="w-[250px] md:w-[250px] flex items-center sticky top-0 h-screen transition-all duration-300">
            <?php include '../../components/admin/sidebarAdmin.php'; ?>
        </aside>

        <!-- Rightside -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Navbar -->
            <header class="h-[60px] sticky top-0 z-10">
                <?php include '../../components/admin/NavbarAdmin.php'; ?>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div class="mb-4">
                    <h1 class="text-2xl md:text-3xl font-bold">Dashboard</h1>
                    <p class="text-gray-400">Selamat Datang kembali, bagaimana penjualan hari ini?</p>
                </div>

                <!-- Stats Cards (Responsive Grid) -->
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <!-- Card 1: Pendapatan Bulanan -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                        <h2 class="text-base text-gray-600 mb-3">Pendapatan Bulanan</h2>
                        <p class="font-bold text-2xl">Rp. <?= number_format($stats['monthlyRevenue']['value'], 0, ',', '.') ?></p>
                        <p class="text-sm <?= $stats['monthlyRevenue']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            Pendapatan <?= $stats['monthlyRevenue']['isIncrease'] ? 'naik' : 'turun' ?> <?= $stats['monthlyRevenue']['change'] ?> dari bulan lalu
                        </p>
                    </div>

                    <!-- Card 2: Jumlah Users -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                        <h2 class="text-base text-gray-600 mb-3">Jumlah Users Terdaftar</h2>
                        <p class="font-bold text-2xl"><?= $stats['totalUsers']['value'] ?></p>
                        <p class="text-sm <?= $stats['totalUsers']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            User terdaftar <?= $stats['totalUsers']['isIncrease'] ? 'naik' : 'turun' ?> <?= $stats['totalUsers']['change'] ?> dari bulan lalu
                        </p>
                    </div>

                    <!-- Card 3: Total Orders -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                        <h2 class="text-base text-gray-600 mb-3">Total Orders</h2>
                        <p class="font-bold text-2xl"><?= $stats['totalOrders']['value'] ?></p>
                        <p class="text-sm <?= $stats['totalOrders']['isIncrease'] ? 'text-green-600' : 'text-red-600' ?>">
                            Pembelian <?= $stats['totalOrders']['isIncrease'] ? 'naik' : 'turun' ?> dari bulan lalu
                        </p>
                    </div>

                    <!-- Card 4: Pesanan Pending -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                        <h2 class="text-base text-gray-600 mb-3">Permintaan Pengembalian</h2>
                        <p class="font-bold text-2xl"><?= $stats['returnReq']['value'] ?></p>
                    </div>
                </div>

                <!-- Charts and Low Stock (Responsive Grid) -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Chart Pendapatan -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 col-span-1 lg:col-span-3">
                        <h2 class="text-xl md:text-2xl font-bold">Grafik Pendapatan Bulanan</h2>
                        <p class="text-gray-400">Pantau pendapatan setiap bulannya</p>
                        <canvas id="revenueChart" class="max-w-full h-64 md:h-80 p-4"></canvas>
                    </div>

                    <!-- Low Stock -->
                    <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                        <h2 class="text-lg font-bold mb-4">Stok Menipis</h2>
                        <p class="text-gray-400 text-sm mb-5">Jumlah Stok sudah semakin menipis, buruan isi</p>
                        <div class="space-y-2">
                            <?php foreach ($lowStock as $item): ?>
                                <?php 
                                    // Logika pewarnaan stok otomatis
                                    if ($item['percentage'] < 30) {
                                        $barColor = 'bg-red-500';
                                    } elseif ($item['percentage'] < 60) {
                                        $barColor = 'bg-yellow-400';
                                    } else {
                                        $barColor = 'bg-green-500';
                                    }
                                ?>
                                
                                <div class="flex items-center justify-between bg-white p-3 rounded-xl shadow-sm mb-2">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= $item['image'] ?>" class="w-12 h-12 rounded-full object-cover" alt="Product">
                                        <div>
                                            <div class="w-40 bg-gray-200 rounded-full h-3 overflow-hidden">
                                                <div class="h-2 <?= $barColor ?> transition-all duration-500" style="width: <?= $item['percentage'] ?>%;"></div>
                                            </div>
                                            <p class="text-xs mt-1 text-gray-600">
                                                Stok tersisa: <strong><?= $item['percentage'] ?>%</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/view/admin/ProductAdmin.php">
                            <button class="bg-gray-800 text-white font-semibold text-base w-full py-2 rounded-lg hover:bg-gray-600 mt-4 ">Lihat Stok</button>
                        </a>
                    </div>
                </div>


        <!--  Tabel pembelian terbaru -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Pembelian Terbaru</h2>
                    <p class="text-gray-400">Menampilkan 5 pembelian yang baru saja terjadi</p>
                </div>
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Amount</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Items</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Payment Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Payment Method</th>
                                    <th class="h-12 px-3 text-center align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th> 
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3 text-xs text-gray-500"># ORD69</td>
                                    <td class="p-3">Juan Kamil</td>
                                    <td class="p-3">2</td>
                                    <td class="p-3">Monitor ZOWIE xl2546x, CPU i9 14th 14900k</td>
                                    <td class="p-3">
                                        <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200 ">Sudah Membayar</div>
                                        <div class="px-2 text-gray-500 border border-gray-400 rounded-lg bg-gray-100 hidden">Belum Membayar</div>
                                    </td>
                                    <td class="p-3">Bank Transfer</td>
                                    <td class="p-3">
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-full rounded-lg hidden">Selesai</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-amber-300 w-full rounded-lg ">Pengiriman</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-full rounded-lg hidden">Batal/dikembalikan</div> <!--Buat Status nya-->
                                    </td>
                                    <td class="p-3">
                                        <button class="px-2 font-semibold cursor-pointer text-blue-900 hover:brightness-100"> Lihat Detail</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- CHART JS -->
    <!-- JavaScript untuk Interaktivitas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.chartData = <?= json_encode($chartData) ?>;
    </script>
    <script src="/assets/js/chart.js"></script> 

</body>
</html>