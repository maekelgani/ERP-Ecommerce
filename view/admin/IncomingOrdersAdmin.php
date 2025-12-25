<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\OrderRepository;

AuthMiddleware::requireAdminLoginFromView();

$orderRepo = new OrderRepository();

// Modified: Added perPage variable like CustomerList.php
$perPage = (int)($_GET['per_page'] ?? 10);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = $perPage;

$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'payment_status' => $_GET['payment_status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

$result = $orderRepo->getIncomingOrders($filters, $page, $limit);
$orders = $result['data'];
$totalPages = $result['total_pages'];
$totalOrders = $result['total'];

$stats = $orderRepo->getOrderStats();

// Added: Calculate startEntry and endEntry early
$startEntry = $totalOrders > 0 ? (($page - 1) * $limit) + 1 : 0;
$endEntry = min($page * $limit, $totalOrders);

$pageTitle = "Incoming Orders";
include '../../components/admin/head.php';

function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function getStatusOrderBadge($status)
{
    $badges = [
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'dikonfirmasi' => 'bg-blue-100 text-blue-800 border-blue-300',
        'diproses' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
        'dikirim' => 'bg-purple-100 text-purple-800 border-purple-300',
        'selesai' => 'bg-green-100 text-green-800 border-green-300',
        'dibatalkan' => 'bg-red-100 text-red-800 border-red-300'
    ];
    $labels = [
        'pending' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan'
    ];
    $class = $badges[$status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    $label = $labels[$status] ?? ucfirst($status);
    return "<span class='px-2.5 py-1 text-xs font-medium rounded-full border {$class}'>{$label}</span>";
}

function getPaymentStatusBadge($status)
{
    $badges = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-300',
        'verifikasi' => 'bg-blue-50 text-blue-700 border-blue-300',
        'berhasil' => 'bg-green-50 text-green-700 border-green-300',
        'gagal' => 'bg-red-50 text-red-700 border-red-300'
    ];
    $labels = [
        'pending' => 'Belum Bayar',
        'verifikasi' => 'Verifikasi',
        'berhasil' => 'Lunas',
        'gagal' => 'Gagal'
    ];
    $class = $badges[$status] ?? 'bg-gray-50 text-gray-700 border-gray-300';
    $label = $labels[$status] ?? ucfirst($status ?? 'Tidak Ada');
    return "<span class='px-2.5 py-1 text-xs font-medium rounded-full border {$class}'>{$label}</span>";
}

function getShipmentStatusBadge($status)
{
    $badges = [
        'pending' => 'bg-gray-100 text-gray-700',
        'dikemas' => 'bg-blue-100 text-blue-700',
        'dikirim' => 'bg-purple-100 text-purple-700',
        'dalam_perjalanan' => 'bg-indigo-100 text-indigo-700',
        'tiba' => 'bg-teal-100 text-teal-700',
        'diterima' => 'bg-green-100 text-green-700'
    ];
    $labels = [
        'pending' => 'Pending',
        'dikemas' => 'Dikemas',
        'dikirim' => 'Dikirim',
        'dalam_perjalanan' => 'Dalam Perjalanan',
        'tiba' => 'Tiba',
        'diterima' => 'Diterima'
    ];
    $class = $badges[$status] ?? 'bg-gray-100 text-gray-700';
    $label = $labels[$status] ?? ucfirst($status ?? 'N/A');
    return "<span class='px-2 py-0.5 text-xs font-medium rounded {$class}'>{$label}</span>";
}
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Pesanan Masuk</h1>
                <p class="text-gray-500 mt-1">Kelola pesanan yang masuk dan perlu diproses</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-yellow-600">pending_actions</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $stats['pending_orders'] ?? 0 ?></p>
                            <p class="text-xs text-gray-500">Menunggu</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $stats['processing_orders'] ?? 0 ?></p>
                            <p class="text-xs text-gray-500">Diproses</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600">local_shipping</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $stats['shipping_orders'] ?? 0 ?></p>
                            <p class="text-xs text-gray-500">Dikirim</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $stats['completed_orders'] ?? 0 ?></p>
                            <p class="text-xs text-gray-500">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <!-- Modified: Header section like CustomerList.php with entries per page -->
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Pesanan Masuk</h2>
                            <p class="text-sm text-gray-500 mt-1">Menampilkan <?= $startEntry ?> - <?= $endEntry ?> dari <?= $totalOrders ?> pesanan</p>
                        </div>
                        <!-- KONTROL -->
                        <div class="flex items-center justify-between gap-4">
                            <!-- LEFT: Entries per page -->
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">view_list</span>
                                <select onchange="window.location.href='?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>&per_page=' + this.value"
                                    class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <?php foreach ([5, 10, 20, 50] as $option): ?>
                                        <option value="<?= $option ?>" <?= $perPage === $option ? 'selected' : '' ?>>
                                            <?= $option ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="GET" class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <!-- Hidden input to preserve per_page when filtering -->
                    <input type="hidden" name="per_page" value="<?= $perPage ?>">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Pesanan</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                                <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
                                    placeholder="ID Order atau nama customer..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none">
                            </div>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Status Order</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="dikonfirmasi" <?= $filters['status'] === 'dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                <option value="diproses" <?= $filters['status'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                            </select>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Status Pembayaran</label>
                            <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none">
                                <option value="">Semua</option>
                                <option value="pending" <?= $filters['payment_status'] === 'pending' ? 'selected' : '' ?>>Belum Bayar</option>
                                <option value="berhasil" <?= $filters['payment_status'] === 'berhasil' ? 'selected' : '' ?>>Lunas</option>
                            </select>
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-[#882426] text-white rounded-lg text-sm font-medium hover:bg-[#6d1d1f] transition flex items-center gap-1">
                                <span class="material-symbols-outlined text-lg">filter_alt</span>
                                Filter
                            </button>
                            <a href="IncomingOrdersAdmin.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status Order</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pengiriman</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-3">inbox</span>
                                            <p class="text-gray-500 font-medium">Tidak ada pesanan masuk</p>
                                            <p class="text-gray-400 text-sm">Pesanan baru akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = (($page - 1) * $limit) + 1;
                                foreach ($orders as $order): ?>
                                    <tr class="hover:bg-orange-50 transition-colors" data-order-id="<?= htmlspecialchars($order['id_order']) ?>">
                                        <td class="px-4 py-3 text-center font-medium text-gray-700">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#882426]/10 text-[#882426]"><?= $no++ ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-mono font-medium text-[#882426]">#<?= htmlspecialchars(substr($order['id_order'], -8)) ?></span>
                                                <?php if ($order['shipping_method'] === 'pickup'): ?>
                                                    <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-medium rounded">PICKUP</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($order['nama_lengkap'] ?? 'Guest') ?></p>
                                                <p class="text-xs text-gray-500"><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                                <?= $order['total_items'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <?php
                                            $productDiscount = floatval($order['product_discount'] ?? 0);
                                            $voucherDiscount = floatval($order['voucher_discount'] ?? 0);
                                            $totalDiskon = floatval($order['total_diskon'] ?? 0);
                                            $originalSubtotal = floatval($order['original_subtotal'] ?? 0);
                                            ?>
                                            <?php if ($totalDiskon > 0 && $originalSubtotal > 0): ?>
                                                <p class="text-xs text-gray-400 line-through"><?= formatRupiah($originalSubtotal + floatval($order['total_ongkir']) + floatval($order['biaya_packing'])) ?></p>
                                            <?php endif; ?>
                                            <p class="text-sm font-semibold text-gray-800"><?= formatRupiah($order['total_bayar']) ?></p>
                                            <?php if ($totalDiskon > 0): ?>
                                                <div class="flex flex-col items-end gap-0.5 mt-1">
                                                    <?php if ($productDiscount > 0): ?>
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] bg-green-50 text-green-600 px-1.5 py-0.5 rounded">
                                                            <span class="material-symbols-outlined text-xs">sell</span>
                                                            -<?= formatRupiah($productDiscount) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($voucherDiscount > 0): ?>
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded">
                                                            <span class="material-symbols-outlined text-xs">confirmation_number</span>
                                                            -<?= formatRupiah($voucherDiscount) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="text-sm text-gray-800"><?= date('d M Y', strtotime($order['tanggal_order'])) ?></p>
                                                <p class="text-xs text-gray-500"><?= date('H:i', strtotime($order['tanggal_order'])) ?> WIB</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?= getPaymentStatusBadge($order['status_pembayaran']) ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <select class="order-status-select px-2 py-1 text-xs border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none cursor-pointer"
                                                data-order-id="<?= htmlspecialchars($order['id_order']) ?>">
                                                <option value="pending" <?= $order['status_order'] === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                                <option value="dikonfirmasi" <?= $order['status_order'] === 'dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                                <option value="diproses" <?= $order['status_order'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                                <option value="dikirim" <?= $order['status_order'] === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?= getShipmentStatusBadge($order['status_pengiriman']) ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-1">
                                                <button class="btn-view-detail p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                    data-order-id="<?= htmlspecialchars($order['id_order']) ?>" title="Lihat Detail">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </button>
                                                <button class="btn-update-shipment p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                                    data-order-id="<?= htmlspecialchars($order['id_order']) ?>" title="Update Pengiriman">
                                                    <span class="material-symbols-outlined text-lg">local_shipping</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modified: Pagination Info & Controls with per_page support -->
                <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalOrders ?></span> entries
                        </div>
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                            </button>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        if ($startPage > 1):
                        ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                            <?php if ($startPage > 2): ?>
                                <span class="px-2 py-2 text-gray-400">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if ($i === $page): ?>
                                <button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;"><?= $i ?></button>
                            <?php else: ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="px-2 py-2 text-gray-400">...</span>
                            <?php endif; ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $totalPages ?></a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced Detail Modal -->
    <div id="order-detail-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeOrderDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="sticky top-0 bg-[#882426] px-6 py-5 flex items-center justify-between z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">shopping_bag</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Detail Pesanan</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="modal-order-id"></p>
                        </div>
                    </div>
                    <button onclick="closeOrderDetailModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <!-- Content with Custom Scrollbar -->
                <div class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-5" id="modal-content">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#882426]"></div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button onclick="closeOrderDetailModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Shipment Modal -->
    <div id="shipment-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeShipmentModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modal Header with Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">local_shipping</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Update Pengiriman</h3>
                            <p class="text-white/70 text-sm mt-0.5">Perbarui status pengiriman pesanan</p>
                        </div>
                    </div>
                    <button onclick="closeShipmentModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Status Info Alert -->
                    <div class="mb-5 p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                            <div>
                                <p class="text-sm text-gray-700 font-medium">Perbarui status pengiriman dan nomor resi untuk pesanan ini.</p>
                            </div>
                        </div>
                    </div>

                    <form id="shipment-form" class="space-y-5">
                        <input type="hidden" name="order_id" id="shipment-order-id">

                        <!-- Status Field -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">package_2</span>
                                Status Pengiriman
                            </label>
                            <select name="status" id="shipment-status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200">
                                <option value="pending">Pending</option>
                                <option value="dikemas">Dikemas</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="dalam_perjalanan">Dalam Perjalanan</option>
                                <option value="tiba">Tiba di Tujuan</option>
                                <option value="diterima">Diterima</option>
                            </select>
                        </div>

                        <!-- Resi Field -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">qr_code</span>
                                Nomor Resi
                            </label>
                            <input type="text" name="no_resi" id="shipment-resi" placeholder="Masukkan nomor resi..."
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400">
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">lightbulb</span>
                                Opsional - dapat ditambahkan nanti
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="closeShipmentModal()"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                                <span class="material-symbols-outlined text-lg">close</span>
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#882426] text-white font-bold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal Styles -->
    <style>
        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-modal-in {
            animation: modal-in 0.3s ease-out forwards;
        }

        /* Custom Scrollbar for Modal */
        #modal-content::-webkit-scrollbar {
            width: 6px;
        }

        #modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #modal-content::-webkit-scrollbar-thumb {
            background: #882426;
            border-radius: 10px;
        }

        #modal-content::-webkit-scrollbar-thumb:hover {
            background: #6d1a1c;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.order-status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const orderId = this.dataset.orderId;
                    const status = this.value;
                    updateOrderStatus(orderId, status);
                });
            });

            document.querySelectorAll('.btn-view-detail').forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    openOrderDetailModal(orderId);
                });
            });

            document.querySelectorAll('.btn-update-shipment').forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;
                    openShipmentModal(orderId);
                });
            });

            document.getElementById('shipment-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const orderId = document.getElementById('shipment-order-id').value;
                const status = document.getElementById('shipment-status').value;
                const noResi = document.getElementById('shipment-resi').value;
                updateShipmentStatus(orderId, status, noResi);
            });
        });

        async function updateOrderStatus(orderId, status) {
            try {
                const response = await fetch('../../api/admin/update-order-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: status
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Status order berhasil diupdate', 'success');
                } else {
                    showToast(result.message || 'Gagal update status', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        }

        async function openOrderDetailModal(orderId) {
            document.getElementById('order-detail-modal').classList.remove('hidden');
            document.getElementById('modal-order-id').textContent = '#' + orderId;

            try {
                const response = await fetch(`../../api/admin/get-order-detail.php?order_id=${orderId}`);
                const result = await response.json();
                if (result.success) {
                    renderOrderDetail(result.data);
                } else {
                    document.getElementById('modal-content').innerHTML = '<p class="text-center text-red-500">Gagal memuat data</p>';
                }
            } catch (error) {
                document.getElementById('modal-content').innerHTML = '<p class="text-center text-red-500">Terjadi kesalahan</p>';
            }
        }

        function renderOrderDetail(order) {
            const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

            let originalSubtotal = 0;
            let subtotalAfterDiscount = 0;
            let productDiscount = 0;

            order.items.forEach(item => {
                const hargaAsli = parseFloat(item.harga_satuan || 0);
                const diskonSatuan = parseFloat(item.diskon_satuan || 0);
                const hargaFinal = parseFloat(item.harga_setelah_diskon || hargaAsli);
                const jumlah = parseInt(item.jumlah || 1);

                originalSubtotal += hargaAsli * jumlah;
                subtotalAfterDiscount += parseFloat(item.subtotal || (hargaFinal * jumlah));
                productDiscount += diskonSatuan * jumlah;
            });

            const voucherDiscount = Math.max(0, parseFloat(order.total_diskon || 0) - productDiscount);
            const taxRate = 0.11;
            const taxAmount = subtotalAfterDiscount * taxRate;

            let itemsHtml = order.items.map(item => {
                const hargaAsli = parseFloat(item.harga_satuan || 0);
                const diskonSatuan = parseFloat(item.diskon_satuan || 0);
                const hargaFinal = parseFloat(item.harga_setelah_diskon || hargaAsli);
                const jumlah = parseInt(item.jumlah || 1);
                const hasDiscount = diskonSatuan > 0;

                return `
                <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                        ${item.gambar ? 
                            `<img src="../../uploads/products/${item.gambar}" class="w-full h-full object-cover">` : 
                            `<span class="material-symbols-outlined text-gray-400">image</span>`}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${item.nama_product}</p>
                        <p class="text-xs text-gray-500">${jumlah} x ${formatRupiah(hargaFinal)}</p>
                        ${hasDiscount ? `<span class="text-xs text-green-600 font-medium">Hemat ${formatRupiah(diskonSatuan * jumlah)}</span>` : ''}
                    </div>
                    <div class="text-right">
                        ${hasDiscount ? `<p class="text-xs text-gray-400 line-through">${formatRupiah(hargaAsli * jumlah)}</p>` : ''}
                        <p class="text-sm font-semibold text-gray-800">${formatRupiah(item.subtotal)}</p>
                    </div>
                </div>`;
            }).join('');

            const isPickup = order.shipping_method === 'pickup';

            document.getElementById('modal-content').innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Customer</p>
                        <p class="font-medium text-gray-800">${order.nama_lengkap || 'Guest'}</p>
                        <p class="text-sm text-gray-500">${order.customer_phone || '-'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Order</p>
                        <p class="font-medium text-gray-800">${new Date(order.tanggal_order).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                        <p class="text-sm text-gray-500">${new Date(order.tanggal_order).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})} WIB</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-2">Alamat Pengiriman</p>
                    <p class="font-medium text-gray-800">${order.nama_penerima || '-'}</p>
                    <p class="text-sm text-gray-600">${order.alamat_pengiriman || '-'}</p>
                    <p class="text-sm text-gray-500">${order.jasa_pengiriman || '-'}</p>
                    ${order.no_resi ? `<p class="text-sm font-mono text-[#882426] mt-1">Resi: ${order.no_resi}</p>` : ''}
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Item Pesanan</p>
                    <div class="bg-gray-50 rounded-xl p-3">
                        ${itemsHtml}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    ${productDiscount > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal Produk</span>
                        <span class="text-gray-400 line-through">${formatRupiah(originalSubtotal)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600 font-medium">Diskon Produk</span>
                        <span class="text-green-600 font-medium">- ${formatRupiah(productDiscount)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-800 font-medium">${formatRupiah(subtotalAfterDiscount)}</span>
                    </div>
                    ` : `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-800">${formatRupiah(subtotalAfterDiscount)}</span>
                    </div>`}
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pajak (11%)</span>
                        <span class="text-gray-800">${formatRupiah(taxAmount)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ongkir</span>
                        ${isPickup ? 
                            `<span class="text-green-600 font-medium">Gratis (Ambil di Toko)</span>` :
                            `<span class="text-gray-800">${formatRupiah(order.total_ongkir)}</span>`}
                    </div>
                    ${order.biaya_packing > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Biaya Packing</span>
                        <span class="text-gray-800">${formatRupiah(order.biaya_packing)}</span>
                    </div>` : ''}
                    ${voucherDiscount > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600 font-medium">Diskon Voucher</span>
                        <span class="text-green-600 font-medium">- ${formatRupiah(voucherDiscount)}</span>
                    </div>` : ''}
                    <div class="flex justify-between text-base font-semibold border-t border-gray-200 pt-2 mt-2">
                        <span class="text-gray-800">Total Bayar</span>
                        <span class="text-[#882426]">${formatRupiah(order.payment_total || order.total_bayar)}</span>
                    </div>
                </div>

                ${order.catatan_order ? `
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-xs text-yellow-700 font-medium mb-1">Catatan Pesanan</p>
                    <p class="text-sm text-yellow-800">${order.catatan_order}</p>
                </div>` : ''}
            </div>
        `;
        }

        function closeOrderDetailModal() {
            document.getElementById('order-detail-modal').classList.add('hidden');
        }

        function openShipmentModal(orderId) {
            document.getElementById('shipment-order-id').value = orderId;
            document.getElementById('shipment-modal').classList.remove('hidden');
        }

        function closeShipmentModal() {
            document.getElementById('shipment-modal').classList.add('hidden');
        }

        async function updateShipmentStatus(orderId, status, noResi) {
            try {
                const response = await fetch('../../api/admin/update-shipment-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: status,
                        no_resi: noResi
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Status pengiriman berhasil diupdate', 'success');
                    closeShipmentModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Gagal update status', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            toast.innerHTML = `<span class="material-symbols-outlined text-lg">${type === 'success' ? 'check_circle' : 'error'}</span>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>

</html>