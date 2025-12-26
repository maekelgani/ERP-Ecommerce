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
    $stmt = $db->prepare("
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
    $stmt->execute([$orderId]);
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('ReturnOrderDetailPage Error: ' . $e->getMessage() . ' | Order ID: ' . $orderId);
    $errorMsg = 'Gagal memuat data order: ' . htmlspecialchars($e->getMessage());
}

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
                <div class="max-w-md w-full mx-4 bg-white rounded-xl shadow-lg border border-red-100 p-6 text-center">
                    <div class="mb-4">
                        <span class="material-symbols-outlined text-5xl text-red-600">error</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Terjadi Kesalahan</h2>
                    <p class="text-gray-600 mb-6"><?= $errorMsg ?></p>
                    <a href="ReturnAdmin.php" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg hover:bg-[#6d1a1c] transition-all">
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
        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu'],
        'dikonfirmasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Dikonfirmasi'],
        'diproses' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Diproses'],
        'dikirim' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Dikirim'],
        'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Selesai'],
        'dibatalkan' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Dibatalkan']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($status)];
}

function getPaymentStatusBadge($status)
{
    $badges = [
        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Belum Bayar'],
        'verifikasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Verifikasi'],
        'berhasil' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Lunas'],
        'gagal' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Gagal']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($status)];
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

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#882426] to-[#6d1a1c] text-white rounded-xl">
                <div class="px-4 md:px-6 py-6">
                    <div class="flex items-center gap-3 mb-4">
                        <a href="<?= $backUrl ?>"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold">Detail Pesanan</h1>
                            <p class="text-white/80 text-sm mt-1">
                                Order ID:
                                <span class="font-mono font-semibold">
                                    <?= htmlspecialchars($order['id_order']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order & Customer Info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">shopping_bag</span>
                        Informasi Pesanan
                    </h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">ID Pesanan</p>
                                <p class="text-sm font-mono font-bold text-gray-900"><?= htmlspecialchars($order['id_order']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal</p>
                                <p class="text-sm font-bold text-gray-900"><?= formatDate($order['tanggal_order']) ?></p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                                <?php $sb = getOrderStatusBadge($order['status_order']); ?>
                                <span class="px-3 py-1.5 rounded-full text-xs font-bold <?= $sb['bg'] ?> <?= $sb['text'] ?>"><?= $sb['label'] ?></span>
                            </div>
                        </div>
                        <?php if (!empty($order['catatan_order'])): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                                <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Catatan</p>
                                <p class="text-sm text-blue-900"><?= htmlspecialchars(substr($order['catatan_order'], 0, 200)) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">person</span>
                        Pelanggan
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Nama</p>
                            <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($order['nama_lengkap'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                            <p class="text-sm text-gray-700 break-all"><?= htmlspecialchars($order['email'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">No. HP</p>
                            <p class="text-sm text-gray-700"><?= htmlspecialchars($order['no_telp'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#882426]">receipt_long</span>
                    Daftar Produk (<?= count($items) ?> Item)
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Diskon</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($item['nama_product']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($item['id_product']) ?></p>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700"><?= formatRupiah($item['harga_satuan']) ?></td>
                                    <td class="px-4 py-3 text-right text-red-600 font-medium"><?= formatRupiah($item['diskon_satuan']) ?></td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900"><?= intval($item['jumlah']) ?></td>
                                    <td class="px-4 py-3 text-right font-bold text-[#882426]"><?= formatRupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pricing Summary & Payment -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">calculate</span>
                        Ringkasan Biaya
                    </h2>
                    <div class="space-y-2.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Harga</span>
                            <span class="font-medium text-gray-900"><?= formatRupiah($order['total_harga']) ?></span>
                        </div>
                        <?php if ((float)$order['total_diskon'] > 0): ?>
                            <div class="flex justify-between text-sm text-red-600">
                                <span>Diskon</span>
                                <span class="font-medium">-<?= formatRupiah($order['total_diskon']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ((float)$order['biaya_packing'] > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Packing</span>
                                <span class="font-medium text-gray-900"><?= formatRupiah($order['biaya_packing']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ((float)$order['total_ongkir'] > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ongkir</span>
                                <span class="font-medium text-gray-900"><?= formatRupiah($order['total_ongkir']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="font-bold text-gray-900">Total Bayar</span>
                                <span class="font-bold text-lg text-[#882426]"><?= formatRupiah($order['total_bayar']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($payment): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">credit_card</span>
                            Pembayaran
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</p>
                                <?php $pb = getPaymentStatusBadge($payment['status_pembayaran']); ?>
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold <?= $pb['bg'] ?> <?= $pb['text'] ?>"><?= $pb['label'] ?></span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Metode</p>
                                <p class="text-sm font-medium text-gray-900"><?= ucwords(str_replace('_', ' ', $payment['metode_pembayaran'])) ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total</p>
                                <p class="text-sm font-bold text-gray-900"><?= formatRupiah($payment['total_bayar']) ?></p>
                            </div>
                            <?php if (!empty($payment['tanggal_pembayaran'])): ?>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Bayar</p>
                                    <p class="text-sm text-gray-700"><?= formatDate($payment['tanggal_pembayaran']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipment -->
            <?php if ($shipment): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">local_shipping</span>
                        Pengiriman
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Status</p>
                                <?php $sh = getShipmentStatusBadge($shipment['status_pengiriman']); ?>
                                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg <?= $sh['bg'] ?>">
                                    <span class="material-symbols-outlined text-sm <?= $sh['text'] ?>"><?= $sh['icon'] ?></span>
                                    <span class="font-bold <?= $sh['text'] ?>"><?= $sh['label'] ?></span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Kurir</p>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($shipment['jasa_pengiriman']) ?></p>
                                </div>
                                <?php if (!empty($shipment['no_resi'])): ?>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">No. Resi</p>
                                        <p class="text-sm font-mono font-bold text-gray-900"><?= htmlspecialchars($shipment['no_resi']) ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($shipment['estimasi_hari'])): ?>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Estimasi</p>
                                        <p class="text-sm text-gray-900"><?= intval($shipment['estimasi_hari']) ?> hari kerja</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Alamat Pengiriman</p>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Penerima</p>
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($shipment['nama_penerima'] ?? $shipment['nama_pengiriman'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">No. HP</p>
                                    <p class="text-gray-700"><?= htmlspecialchars($shipment['no_telp'] ?? $shipment['nomor_hp_penerima'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Alamat</p>
                                    <p class="text-gray-700"><?= htmlspecialchars($shipment['alamat_lengkap'] ?? $shipment['alamat_pengiriman'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">Kota</p>
                                    <p class="text-gray-700"><?= htmlspecialchars($shipment['kota'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($shipment['tanggal_dikirim']) || !empty($shipment['tanggal_diterima'])): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Timeline</p>
                            <div class="space-y-2">
                                <?php if (!empty($shipment['tanggal_dikirim'])): ?>
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-green-600">done</span>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase">Dikirim</p>
                                            <p class="text-sm font-semibold text-gray-900"><?= formatDate($shipment['tanggal_dikirim']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($shipment['tanggal_diterima'])): ?>
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-blue-600">check_circle</span>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase">Diterima</p>
                                            <p class="text-sm font-semibold text-gray-900"><?= formatDate($shipment['tanggal_diterima']) ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

    </div>
    </main>
    </div>
</body>

</html>