<?php
// Class OOP untuk mengambil data dari JSON
class OrderData {
    private $data;

    public function __construct($jsonFilePath) {
        if (file_exists($jsonFilePath)) {
            $jsonContent = file_get_contents($jsonFilePath);
            $decoded = json_decode($jsonContent, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['orders'])) {
                $this->data = $decoded;
            } else {
                // Fallback ke dummy data jika JSON invalid
                $this->data = $this->getDummyData();
            }
        } else {
            // Dummy data jika JSON tidak ada
            $this->data = $this->getDummyData();
        }
    }

    private function getDummyData() {
        return [
            'orders' => [
                [
                    'id' => '#ORD69',
                    'customer' => 'Juan Kamila',
                    'amount' => 2,
                    'totalHarga' => 120000000,
                    'orderDate' => '23-10-2025',
                    'receivedDate' => '26-10-2025',
                    'status' => 'selesai',
                    'details' => [
                        'name' => 'Juan Kamila',
                        'address' => 'Jl. Contoh No. 123',
                        'date' => '23-10-2025',
                        'recieve-date' => '26-10-2025',
                        'payment' => 'Sudah Dibayar',
                        'method' => 'Bank Transfer',
                        'status' => 'selesai',
                        'items' => [['name' => 'Monitor ZOWIE xl2546x', 'qty' => 1, 'price' => 5000000], ['name' => 'CPU i9 14th 14900k', 'qty' => 1, 'price' => 7000000]],
                        'total' => 120000000,
                        'paymentId' => '#PAY69', // Placeholder untuk ID pembayaran
                        'paymentDate' => '23-10-2025' // Placeholder untuk tanggal pembayaran
                    ]
                ]
                // Tambahkan lebih banyak orders jika perlu
            ]
        ];
    }

    public function getOrders() {
        return $this->data['orders'];
    }
}

// Inisialisasi data
$orderData = new OrderData('ordersData.json'); // Path ke file JSON
$orders = $orderData->getOrders();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../assets/img/Nanocomp.png">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="bg-no-repeat h-screen flex">

    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] md:w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../../components/admin/sidebarAdmin.php'; ?>
    </aside>

    <!-- Rightside: -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- Navbar -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl md:text-3xl font-bold">Order List</h1>
                    <p class="text-gray-400">Detail Semua order yang telah terjadi</p>
                </div>
            </div>

            <!-- Container Tabel Orders -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Order List</h2>
                    <p class="text-base text-gray-400">Daftar order yang sedang dalam proses pengemasan</p>
                </div>

                <!-- Filter (Responsive: Stack di mobile) -->
                <div id="filter-field" class="mb-4 flex flex-wrap items-center gap-4 pb-3 pt-0">
                    <!-- Filter Cari Produk -->
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <input type="text" id="search-input" placeholder="Cari Product" class="border border-gray-300 rounded-lg px-3 w-full md:w-md py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <!-- Filter by Status -->
                    <div class="w-full md:w-auto">
                        <select id="status-filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-full md:w-auto">
                            <option value="">Semua Status</option>
                            <option value="dikemas">Dikemas</option>
                            <option value="pengiriman">Pengiriman</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <!-- Filter by Tanggal -->
                    <div class="w-full md:w-auto">
                        <input type="date" id="date-filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-full md:w-auto">
                    </div>
                    <button id="apply-filter" class="px-10 bg-gray-800 text-white py-2 rounded-md font-semibold text-sm hover:bg-gray-500 cursor-pointer transition w-full md:w-auto">Terapkan Filter</button>
                </div>

                <!-- Bagian Tabel Orders -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-x-auto">
                        <!-- Tabel -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg">
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Amount</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Total Harga</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Tanggal Pemesanan</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Tanggal Diterima</th>
                                    <th class="h-12 px-3 text-center align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orders-tbody" class="text-sm">
                                <?php foreach ($orders as $order): ?>
                                <tr class="border-t">
                                    <td class="p-3 text-xs text-gray-500"><?= $order['id'] ?></td>
                                    <td class="p-3"><?= $order['customer'] ?></td>
                                    <td class="p-3"><?= $order['amount'] ?></td>
                                    <td class="p-3">Rp <?= number_format($order['totalHarga'], 0, ',', '.') ?></td>
                                    <td class="p-3"><?= $order['orderDate'] ?></td>
                                    <td class="p-3"><?= $order['receivedDate'] ?></td>
                                    <td class="p-3">
                                        <div class="text-center text-xs font-semibold rounded-lg px-2 py-1
                                            <?= $order['status'] === 'selesai' ? 'text-green-800 bg-green-500/10 border border-green-800' : ($order['status'] === 'pengiriman' ? 'text-amber-600 bg-amber-50 border border-amber-600' : 'text-red-800 bg-amber-500/10 border border-red-800') ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <button class="detailOrder px-2 font-semibold cursor-pointer text-blue-900 hover:brightness-100"
                                                data-order-id="<?= $order['id'] ?>"
                                                data-name="<?= $order['details']['name'] ?>"
                                                data-address="<?= $order['details']['address'] ?>"
                                                data-date="<?= $order['details']['date'] ?>"
                                                data-recieve-date="<?= $order['details']['recieve-date'] ?>"
                                                data-payment="<?= $order['details']['payment'] ?>"
                                                data-method="<?= $order['details']['method'] ?>"
                                                data-status="<?= $order['details']['status'] ?>"
                                                data-items="<?= implode(';', array_map(fn($item) => $item['name'] . '|' . $item['qty'] . '|' . $item['price'], $order['details']['items'])) ?>"
                                                data-total="<?= $order['details']['total'] ?>"
                                                data-payment-id="<?= $order['details']['paymentId'] ?? '' ?>"
                                                data-payment-date="<?= $order['details']['paymentDate'] ?? '' ?>">
                                            Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Lihat Detail Pesanan -->
    <div id="order-details" class="fixed inset-0 z-50 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>

        <!-- Gunakan flex-col dan overflow-y-auto di sini -->
        <div class="relative z-10 flex justify-center items-start p-4 overflow-y-auto h-full">
            <div
            id="modal-card"
            class="w-[50%] md:w-1/2 bg-white rounded-lg shadow-md p-6 mt-10 mb-10 max-h-[90vh] overflow-y-auto transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4"
            >

                <div class="mb-4 flex justify-between">
                    <div>
                        <h2 class="tracking-wide text-2xl font-bold">Detail Pesanan</h2>
                        <p class="text-sm text-gray-400">Informasi lengkap pemesanan</p>
                    </div>
                    <button class="cancel cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- informasi pemesanan -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <!-- gambar disiini -->Informasi Pemesanan
                        </h2>
                        <!-- ISI INFORMASINYA -->
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-lg">
                            <!-- order id -->
                            <div>
                                <p class="text-sm text-gray-400">Order ID</p>
                                <span id="d-order-id" class="font-semibold"></span>
                            </div>
                            <!-- Nama Pemesan -->
                            <div>
                                <p class="text-sm text-gray-400">Nama Pemesanan</p>
                                <span id="d-name" class="font-semibold"></span>
                            </div>
                            <!-- Tanggal Pemesanan -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Pemesanan</p>
                                <span id="d-date" class="font-semibold"></span>
                            </div>
                            <!-- tanggal Diterima -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Diterima</p>
                                <span id="d-recieveDate" class="font-semibold"></span>
                            </div>
                            <!-- Alamat -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Alamat</p>
                                <span id="d-address" class="font-semibold"></span>
                            </div>
                        </div>
                        <!-- Bagian pemesanan -->
                        <div class="gap-4 space-y-4 p-4">
                            <!-- Status Pemesanan -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Status Pemesanan</p>
                                <span id="d-status"></span>
                            </div>
                            <!-- Bar status -->
                            <div class="col-span-2">
                                <div class="mb-4">
                                    <label class="font-semibold text-sm">Status Pengiriman</label>
                                    <div class="flex items-center mt-2">
                                        <!-- Step 1 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-packed" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Dikemas</span>
                                        </div>
                                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                                        <!-- Step 2 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-shipped" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Dikirim</span>
                                        </div>
                                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                                        <!-- Step 3 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-done" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-orientation="horizontal" role="none" class="shrink-0 bg-gray-200 h-px w-full"></div>
                    <!-- informasi Detail Produk-->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <!-- gambar disini --> Daftar produk (<span id="d-item-count"></span> items) 
                        </h2>
                        <!-- List produknya di sini -->
                        <div id="d-items-list" class="space-y-3">
                            <!-- Items akan diisi secara dinamis oleh JS berdasarkan data-items -->
                        </div>
                        <!-- Total Harga -->
                        <div class="flex items-center justify-between p-4 rounded-lg">
                            <p class="text-lg font-bold">Total Harga:</p>
                            <p class="text-xl font-bold text-blue-500">Rp <span id="d-total"></span></p>
                        </div>
                    </div>
                    <div data-orientation="horizontal" role="none" class="shrink-0 bg-gray-200 h-px w-full"></div>
                    <!-- informasi Detail Pembayaran -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                        <!--gambar disini--> Detail Pembayaran</h2>
                        <div class="grid grid-cols-2 gap-4 bg-muted/30 p-4 rounded-lg">
                            <!-- ID Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">ID Pembayaran</p>
                                <span id="d-payment-id" class="font-semibold"></span>
                            </div>
                            <!-- Metode Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Metode Pembayaran</p>
                                <span id="d-method" class="font-semibold"></span>
                            </div>
                            <!-- Tanggal Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Pembayaran</p>
                                <span id="d-payment-date" class="font-semibold"></span>
                            </div>
                            <!-- Status Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Status Pembayaran</p>
                                <span id="d-payment-status" class="font-semibold"></span>
                            </div>
                            <!-- Bukti Pembayaran -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Bukti Pembayaran</p>
                                <a class="inline-flex items-center gap-2 text-blue-500 hover:underline" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>Lihat bukti pembayaran</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="/assets/js/admin/order.js" defer></script>
</body>
</html>