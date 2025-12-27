<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Database\DatabaseConnection;

AuthMiddleware::requireAdminLoginFromView();

$orderId = $_GET['id'] ?? '';

if (empty($orderId)) {
    header('Location: ReturnAdmin.php');
    exit;
}

$order = null;
$items = [];
$payment = null;
$shipment = null;
$errorMsg = null;

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    // Fetch order with customer info
    $query = "
        SELECT 
            o.id_order,
            o.id_customer,
            o.tanggal_order,
            o.total_harga,
            o.total_diskon,
            o.total_ongkir,
            o.total_bayar,
            o.biaya_packing,
            o.status_order,
            o.catatan_order,
            o.shipping_method,
            c.nama_lengkap,
            c.email,
            c.no_telp
        FROM orders o
        LEFT JOIN customers c ON o.id_customer = c.id_customer
        WHERE o.id_order = ?
        LIMIT 1
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order tidak ditemukan di database');
    }

    // Fetch order items
    $stmt = $db->prepare("SELECT * FROM order_detail WHERE id_order = ? ORDER BY id_detail ASC");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch payment info (paling baru)
    $stmt = $db->prepare("
        SELECT * FROM payment 
        WHERE id_order = ? 
        ORDER BY tanggal_pembayaran DESC, id_payment DESC
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch shipment info
    $shipmentQuery = $db->prepare("
        SELECT 
            s.*,
            ab.nama_penerima,
            ab.nomor_hp,
            ab.alamat_lengkap
        FROM shipment s
        LEFT JOIN address_book ab ON s.id_alamat = ab.id_alamat
        WHERE s.id_order = ?
        LIMIT 1
    ");
    $shipmentQuery->execute([$orderId]);
    $shipment = $shipmentQuery->fetch(PDO::FETCH_ASSOC);

    // Fetch voucher usage info
    $voucherUsageQuery = $db->prepare("
        SELECT pv.*, v.kode, v.judul, v.jenis, v.nilai as voucher_nilai, v.maksimal_diskon
        FROM penggunaan_voucher pv
        LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
        WHERE pv.id_pesanan = :order_id
        LIMIT 1
    ");
    $voucherUsageQuery->execute([':order_id' => $orderId]);
    $voucherUsage = $voucherUsageQuery->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('ReturnOrderDetailPage Error: ' . $e->getMessage() . ' | Order ID: ' . $orderId);
    $errorMsg = 'Gagal memuat data order: ' . htmlspecialchars($e->getMessage());
}

// ================= RINGKASAN BIAYA =================
$originalSubtotal = 0;
$subtotalAfterDiscount = 0;
$productDiscount = 0;

foreach ($items as $item) {
    $hargaAsli = floatval($item['harga_satuan'] ?? 0);
    $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
    $hargaFinal = floatval($item['harga_setelah_diskon'] ?? ($hargaAsli - $diskonSatuan));
    $jumlah = intval($item['jumlah'] ?? 1);

    $originalSubtotal += $hargaAsli * $jumlah;
    $subtotalAfterDiscount += floatval($item['subtotal'] ?? ($hargaFinal * $jumlah));
    $productDiscount += $diskonSatuan * $jumlah;
}

$taxRate = 0.11;
$taxAmount = $subtotalAfterDiscount * $taxRate;
$shippingCost = (float)($order['total_ongkir'] ?? 0);
$packingCost = (float)($order['biaya_packing'] ?? 0);
$voucherDiscount = $voucherUsage ? floatval($voucherUsage['jumlah_diskon'] ?? 0) : 0;
$grandTotal = (float)($order['total_bayar'] ?? 0);


if ($errorMsg) {
    // Show error page
    $pageTitle = "Error - Detail Order Return";
    include '../../components/admin/head.php';
?>

    <body class="bg-gray-50 min-h-screen flex">
        <?php include '../../components/admin/sidebarAdmin.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-[60px] sticky top-0 z-10">
                <?php include '../../components/admin/NavbarAdmin.php'; ?>
            </header>
            <main class="flex-1 overflow-y-auto flex items-center justify-center">
                <div class="max-w-md w-full mx-4 bg-white rounded-2xl shadow-xl border border-red-100 p-8 text-center">
                    <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl text-red-600">error</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Terjadi Kesalahan</h2>
                    <p class="text-gray-600 mb-8"><?= $errorMsg ?></p>
                    <a href="ReturnAdmin.php" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white rounded-xl hover:bg-[#6d1a1c] transition-all font-semibold shadow-lg shadow-[#882426]/20">
                        <span class="material-symbols-outlined">arrow_back</span>
                        <span>Kembali ke List Return</span>
                    </a>
                </div>
            </main>
        </div>
    </body>

    </html>
<?php
    exit;
}

function formatRupiah($amount)
{
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

function formatDate($date)
{
    if (!$date) return '-';
    return date('d M Y H:i', strtotime($date));
}

function getOrderStatusBadge($status)
{
    $badges = [
        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu', 'icon' => 'hourglass_empty'],
        'dikonfirmasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Dikonfirmasi', 'icon' => 'verified'],
        'diproses' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Diproses', 'icon' => 'sync'],
        'dikirim' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Dikirim', 'icon' => 'local_shipping'],
        'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Selesai', 'icon' => 'check_circle'],
        'dibatalkan' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Dibatalkan', 'icon' => 'cancel']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($status), 'icon' => 'help'];
}

function getPaymentStatusBadge($status)
{
    $badges = [
        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Belum Bayar', 'icon' => 'schedule'],
        'verifikasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Verifikasi', 'icon' => 'pending'],
        'berhasil' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Lunas', 'icon' => 'check_circle'],
        'gagal' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Gagal', 'icon' => 'cancel']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($status), 'icon' => 'help'];
}

function getShipmentStatusBadge($status)
{
    $badges = [
        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'package', 'label' => 'Menunggu Dikemas'],
        'dikemas' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'inventory_2', 'label' => 'Sedang Dikemas'],
        'dikirim' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'local_shipping', 'label' => 'Dikirim'],
        'dalam_perjalanan' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'icon' => 'directions_car', 'label' => 'Dalam Perjalanan'],
        'tiba' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'location_on', 'label' => 'Tiba'],
        'diterima' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'done_all', 'label' => 'Diterima']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'help', 'label' => ucfirst($status)];
}

$pageTitle = "Detail Order - Return";
include '../../components/admin/head.php';

$backUrl = 'ReturnAdmin.php'; // default fallback

if (!empty($_SERVER['HTTP_REFERER'])) {
    if (str_contains($_SERVER['HTTP_REFERER'], 'DashboardAdmin.php')) {
        $backUrl = 'DashboardAdmin.php';
    } elseif (str_contains($_SERVER['HTTP_REFERER'], 'ReturnAdmin.php')) {
        $backUrl = 'ReturnAdmin.php';
    }
}

?>

<style>
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .animate-slide-in-right {
        animation: slideInRight 0.5s ease-out forwards;
    }

    .delay-100 {
        animation-delay: 0.1s;
    }

    .delay-200 {
        animation-delay: 0.2s;
    }

    .delay-300 {
        animation-delay: 0.3s;
    }

    .info-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.1);
    }

    .product-row:hover {
        background: linear-gradient(90deg, rgba(136, 36, 38, 0.02) 0%, transparent 100%);
    }

    .timeline-step::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background: linear-gradient(to bottom, #e5e7eb, transparent);
    }

    .timeline-step:last-child::before {
        display: none;
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6 custom-scrollbar">
            <!-- Header with Gradient -->
            <div class="bg-gradient-to-r from-[#882426] to-[#a62d30] text-white rounded-2xl overflow-hidden animate-fade-in-up">
                <div class="px-6 py-6 relative">
                    <!-- Decorative elements -->
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="absolute -right-4 -bottom-12 w-48 h-48 bg-white/5 rounded-full"></div>

                    <div class="relative flex flex-col md:flex-row md:items-center gap-4">
                        <a href="<?= $backUrl ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition-all backdrop-blur-sm self-start">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl">receipt_long</span>
                                </div>
                                <div>
                                    <h1 class="text-2xl md:text-3xl font-bold">Detail Pesanan</h1>
                                    <p class="text-white/70 text-sm">Halaman Return Order</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/20">
                                <p class="text-white/60 text-xs uppercase font-medium mb-1">Order ID</p>
                                <p class="font-mono font-bold text-lg"><?= htmlspecialchars($order['id_order']) ?></p>
                            </div>
                            <?php $sb = getOrderStatusBadge($order['status_order']); ?>
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/20">
                                <p class="text-white/60 text-xs uppercase font-medium mb-1">Status</p>
                                <span class="inline-flex items-center gap-1.5 text-white font-bold">
                                    <span class="material-symbols-outlined text-sm"><?= $sb['icon'] ?></span>
                                    <?= $sb['label'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order & Customer Info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Information -->
                <div class="lg:col-span-2 info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up delay-100 opacity-0">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">shopping_bag</span>
                            Informasi Pesanan
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-gray-400 text-sm">tag</span>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">ID Pesanan</p>
                                </div>
                                <p class="text-lg font-mono font-bold text-gray-900"><?= htmlspecialchars($order['id_order']) ?></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-gray-400 text-sm">calendar_month</span>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Order</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900"><?= formatDate($order['tanggal_order']) ?></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-gray-400 text-sm">local_shipping</span>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Metode Kirim</p>
                                </div>
                                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($order['shipping_method'] ?? 'Standard') ?></p>
                            </div>
                        </div>

                        <?php if (!empty($order['catatan_order'])): ?>
                            <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-blue-600 text-sm">notes</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-blue-700 uppercase mb-1">Catatan Pesanan</p>
                                        <p class="text-sm text-blue-900"><?= htmlspecialchars(substr($order['catatan_order'], 0, 300)) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-in-right delay-200 opacity-0">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">person</span>
                            Pelanggan
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#882426] to-[#a62d30] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">person</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($order['nama_lengkap'] ?? '-') ?></p>
                                <p class="text-sm text-gray-500">Customer</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-500 text-sm">mail</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400 uppercase font-medium">Email</p>
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($order['email'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-500 text-sm">phone</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400 uppercase font-medium">No. HP</p>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['no_telp'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up delay-200 opacity-0">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">receipt_long</span>
                        Daftar Produk
                    </h2>
                    <span class="inline-flex items-center gap-1.5 bg-[#882426]/10 text-[#882426] px-3 py-1.5 rounded-full text-sm font-bold">
                        <span class="material-symbols-outlined text-sm">inventory_2</span>
                        <?= count($items) ?> Item
                    </span>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Diskon</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($items as $item): ?>
                                <tr class="product-row transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-gray-400">inventory_2</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900"><?= htmlspecialchars($item['nama_product']) ?></p>
                                                <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($item['id_product']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-700 font-medium"><?= formatRupiah($item['harga_satuan']) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <?php if (floatval($item['diskon_satuan']) > 0): ?>
                                            <span class="text-red-600 font-bold">-<?= formatRupiah($item['diskon_satuan']) ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1.5 bg-gray-100 rounded-full font-bold text-gray-900">
                                            <?= intval($item['jumlah']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-[#882426] text-lg"><?= formatRupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pricing Summary & Payment -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pricing Summary -->
                <div class="info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up delay-300 opacity-0">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">calculate</span>
                            Ringkasan Biaya
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-gray-400">shopping_cart</span>
                                    Subtotal (<?= count($items) ?> Barang)
                                </span>
                                <span class="font-semibold text-gray-900"><?= formatRupiah($originalSubtotal) ?></span>
                            </div>

                            <?php if ($productDiscount > 0): ?>
                                <div class="flex justify-between items-center py-2 text-red-600">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">sell</span>
                                        Diskon Produk
                                    </span>
                                    <span class="font-semibold">-<?= formatRupiah($productDiscount) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-gray-400">account_balance</span>
                                    Pajak (11%)
                                </span>
                                <span class="font-semibold text-gray-900"><?= formatRupiah($taxAmount) ?></span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-gray-400">local_shipping</span>
                                    Ongkos Kirim
                                </span>
                                <span class="font-semibold text-gray-900"><?= formatRupiah($shippingCost) ?></span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-gray-400">inventory_2</span>
                                    Biaya Pengemasan
                                </span>
                                <span class="font-semibold text-gray-900"><?= formatRupiah($packingCost) ?></span>
                            </div>

                            <?php if ($voucherDiscount > 0): ?>
                                <div class="flex justify-between items-center py-2 text-green-600">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">confirmation_number</span>
                                        Voucher <?= $voucherUsage ? '(' . htmlspecialchars($voucherUsage['kode']) . ')' : '' ?>
                                    </span>
                                    <span class="font-semibold">-<?= formatRupiah($voucherDiscount) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t-2 border-dashed border-gray-200 pt-4 mt-4">
                            <div class="bg-gradient-to-r from-[#882426]/5 to-[#882426]/10 rounded-xl p-4 border border-[#882426]/20">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total Bayar</span>
                                    <span class="text-2xl font-bold text-[#882426]"><?= formatRupiah($payment['total_bayar']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Card -->
                <?php if ($payment): ?>
                    <div class="info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-in-right delay-300 opacity-0">
                        <!-- Header dengan gradient -->
                        <div class="bg-gradient-to-r from-[#882426] to-[#a62d30] px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white">credit_card</span>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold text-white">Pembayaran</h2>
                                        <p class="text-white/70 text-xs">Detail transaksi</p>
                                    </div>
                                </div>
                                <?php $pb = getPaymentStatusBadge($payment['status_pembayaran']); ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                    <span class="w-2 h-2 rounded-full <?= $payment['status_pembayaran'] === 'berhasil' ? 'bg-green-400' : ($payment['status_pembayaran'] === 'pending' ? 'bg-yellow-400' : 'bg-red-400') ?> animate-pulse"></span>
                                    <?= $pb['label'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Payment Method Card -->
                            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl p-5 mb-5 relative overflow-hidden">
                                <!-- Decorative circles -->
                                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full"></div>
                                <div class="absolute -right-2 -bottom-8 w-32 h-32 bg-white/5 rounded-full"></div>

                                <div class="relative">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-gray-400 text-xs font-medium uppercase tracking-wider">Metode Pembayaran</span>
                                        <div class="flex gap-1">
                                            <div class="w-6 h-4 bg-red-500 rounded-sm opacity-80"></div>
                                            <div class="w-6 h-4 bg-yellow-500 rounded-sm opacity-80 -ml-2"></div>
                                        </div>
                                    </div>
                                    <p class="text-white text-lg font-bold tracking-wide">
                                        <?= ucwords(str_replace('_', ' ', $payment['metode_pembayaran'])) ?>
                                    </p>
                                    <?php if (!empty($payment['tanggal_pembayaran'])): ?>
                                        <p class="text-gray-400 text-xs mt-3 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">schedule</span>
                                            <?= formatDate($payment['tanggal_pembayaran']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Total Payment -->
                            <div class="bg-gradient-to-r from-[#882426]/5 to-[#882426]/10 rounded-xl p-5 border border-[#882426]/20">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Pembayaran</p>
                                        <p class="text-2xl font-bold text-[#882426]"><?= formatRupiah($payment['total_bayar']) ?></p>
                                    </div>
                                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[#882426] text-2xl">payments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipment -->
            <?php if ($shipment): ?>
                <div class="info-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">local_shipping</span>
                            Pengiriman
                        </h2>
                        <?php $sh = getShipmentStatusBadge($shipment['status_pengiriman']); ?>
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold <?= $sh['bg'] ?> <?= $sh['text'] ?>">
                            <span class="material-symbols-outlined text-sm"><?= $sh['icon'] ?></span>
                            <?= $sh['label'] ?>
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Shipping Details -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-purple-600">local_shipping</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase font-medium">Jasa Kurir</p>
                                        <p class="font-bold text-gray-900"><?= htmlspecialchars($shipment['jasa_pengiriman']) ?></p>
                                    </div>
                                </div>

                                <?php if (!empty($shipment['no_resi'])): ?>
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-blue-600">qr_code</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-400 uppercase font-medium">No. Resi</p>
                                            <p class="font-mono font-bold text-gray-900"><?= htmlspecialchars($shipment['no_resi']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($shipment['estimasi_hari'])): ?>
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-amber-600">schedule</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase font-medium">Estimasi Pengiriman</p>
                                            <p class="font-bold text-gray-900"><?= intval($shipment['estimasi_hari']) ?> hari kerja</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Shipping Address -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl p-5 border border-gray-200">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="material-symbols-outlined text-[#882426]">location_on</span>
                                    <p class="text-sm font-bold text-gray-700 uppercase">Alamat Pengiriman</p>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Penerima</p>
                                        <p class="font-bold text-gray-900"><?= htmlspecialchars($shipment['nama_penerima'] ?? $shipment['nama_pengiriman'] ?? '-') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">No. HP</p>
                                        <p class="text-gray-700"><?= htmlspecialchars($shipment['no_telp'] ?? $shipment['nomor_hp_penerima'] ?? '-') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Alamat Lengkap</p>
                                        <p class="text-gray-700 leading-relaxed"><?= htmlspecialchars($shipment['alamat_lengkap'] ?? $shipment['alamat_pengiriman'] ?? '-') ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 mb-1">Kota</p>
                                        <p class="text-gray-700"><?= htmlspecialchars($shipment['kota'] ?? '-') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <?php if (!empty($shipment['tanggal_dikirim']) || !empty($shipment['tanggal_diterima'])): ?>
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="material-symbols-outlined text-[#882426]">timeline</span>
                                    <p class="text-sm font-bold text-gray-700 uppercase">Timeline Pengiriman</p>
                                </div>
                                <div class="relative pl-8">
                                    <?php if (!empty($shipment['tanggal_dikirim'])): ?>
                                        <div class="timeline-step relative pb-6">
                                            <div class="absolute left-0 top-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center -translate-x-1/2">
                                                <span class="material-symbols-outlined text-green-600 text-sm">check</span>
                                            </div>
                                            <div class="ml-6">
                                                <p class="text-xs text-gray-400 uppercase font-medium">Dikirim</p>
                                                <p class="font-bold text-gray-900"><?= formatDate($shipment['tanggal_dikirim']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($shipment['tanggal_diterima'])): ?>
                                        <div class="timeline-step relative">
                                            <div class="absolute left-0 top-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center -translate-x-1/2">
                                                <span class="material-symbols-outlined text-blue-600 text-sm">done_all</span>
                                            </div>
                                            <div class="ml-6">
                                                <p class="text-xs text-gray-400 uppercase font-medium">Diterima</p>
                                                <p class="font-bold text-gray-900"><?= formatDate($shipment['tanggal_diterima']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>

</html>