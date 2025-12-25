<?php
$pageTitle = "Detail Pesanan";
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

\App\Auth\CustomerAuthMiddleware::requireLogin('detailOrder.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

$orderId = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

if (empty($orderId)) {
    header('Location: myOrder.php');
    exit;
}

function formatRupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatTanggal($date)
{
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $timestamp = strtotime($date);
    $hari = date('d', $timestamp);
    $bulanNum = (int)date('m', $timestamp);
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    return $hari . ' ' . $bulan[$bulanNum] . ' ' . $tahun . ', ' . $jam;
}

function getStatusBadge($status)
{
    $badges = [
        'pending' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Menunggu Pembayaran'],
        'dikonfirmasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Dikonfirmasi'],
        'diproses' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Diproses'],
        'dikirim' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'label' => 'Dikirim'],
        'selesai' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Selesai'],
        'dibatalkan' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Dibatalkan']
    ];
    return $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($status)];
}

function getStatusSteps($status)
{
    $steps = [
        'pending' => 0,
        'dikonfirmasi' => 1,
        'diproses' => 2,
        'dikirim' => 3,
        'selesai' => 4,
        'dibatalkan' => -1
    ];
    return $steps[$status] ?? 0;
}

function parseCancellationInfo($notes)
{
    $info = [
        'cancelled_by' => 'Pembeli',
        'reason' => 'Pesanan dibatalkan oleh pembeli',
        'datetime' => null,
        'is_system' => false
    ];

    if (!$notes) return $info;

    // Check if cancelled by system (timeout)
    if (strpos($notes, '[SISTEM]') !== false) {
        $info['is_system'] = true;
        $info['cancelled_by'] = 'Sistem (Nano Komputer)';
        $info['reason'] = 'Pesanan otomatis dibatalkan oleh Nano Komputer karena waktu pembayaran habis';

        // Extract datetime from note
        if (preg_match('/\[SISTEM\].*?pada\s+(\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2})/', $notes, $matches)) {
            $info['datetime'] = $matches[1];
        }
    } else if (strpos($notes, 'Alasan pembatalan:') !== false) {
        // Parse customer cancellation reason
        if (preg_match('/Alasan pembatalan:\s*(.+?)(\n|$)/i', $notes, $matches)) {
            $reason = trim($matches[1]);
            $info['reason'] = $reason ?: 'Alasan tidak disebutkan';
        }
    }

    return $info;
}

$order = null;
$orderDetails = [];
$address = null;
$payment = null;
$shipment = null;
$voucherUsage = null;
$voucherData = null;
$storePickup = null;

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("
        SELECT o.* 
        FROM orders o
        WHERE o.id_order = :order_id AND o.id_customer = :customer_id
    ");
    $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: myOrder.php');
        exit;
    }

    // Fetch store location data jika shipping method adalah pickup
    if ($order['shipping_method'] === 'pickup' && !empty($order['store_pickup_id'])) {
        $storeQuery = $db->prepare("
            SELECT * FROM store_locations 
            WHERE id_toko = :store_id AND is_active = 1
        ");
        $storeQuery->execute([':store_id' => $order['store_pickup_id']]);
        $storePickup = $storeQuery->fetch(PDO::FETCH_ASSOC);
    }

    $detailQuery = $db->prepare("
        SELECT od.*, pr.gambar as foto_produk,
               pd.nilai as diskon_persen, pd.jenis as diskon_jenis
        FROM order_detail od
        LEFT JOIN products pr ON od.id_product = pr.id_product
        LEFT JOIN promo_diskon pd ON od.id_product = pd.id_produk 
            AND pd.status = 'aktif'
            AND NOW() BETWEEN pd.mulai_pada AND pd.selesai_pada
        WHERE od.id_order = :order_id
    ");
    $detailQuery->execute([':order_id' => $orderId]);
    $orderDetails = $detailQuery->fetchAll(PDO::FETCH_ASSOC);

    $paymentQuery = $db->prepare("SELECT * FROM payment WHERE id_order = :order_id LIMIT 1");
    $paymentQuery->execute([':order_id' => $orderId]);
    $payment = $paymentQuery->fetch(PDO::FETCH_ASSOC);

    // Query untuk mendapatkan data penggunaan voucher
    $voucherUsageQuery = $db->prepare("
        SELECT pv.*, v.kode, v.judul, v.jenis, v.nilai as voucher_nilai, v.maksimal_diskon
        FROM penggunaan_voucher pv
        LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
        WHERE pv.id_pesanan = :order_id
        LIMIT 1
    ");
    $voucherUsageQuery->execute([':order_id' => $orderId]);
    $voucherUsage = $voucherUsageQuery->fetch(PDO::FETCH_ASSOC);

    $originalSubtotal = 0;
    $subtotalAfterDiscount = 0;
    $productDiscount = 0;

    foreach ($orderDetails as $item) {
        $hargaAsli = floatval($item['harga_satuan'] ?? 0);
        $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
        $hargaFinal = floatval($item['harga_setelah_diskon'] ?? $hargaAsli);
        $jumlah = intval($item['jumlah'] ?? 1);

        $originalSubtotal += $hargaAsli * $jumlah;
        $subtotalAfterDiscount += floatval($item['subtotal'] ?? ($hargaFinal * $jumlah));
        $productDiscount += $diskonSatuan * $jumlah;
    }

    $taxRate = 0.11;
    $taxAmount = $subtotalAfterDiscount * $taxRate;
    $shippingCost = floatval($order['total_ongkir'] ?? 0);
    $packingCost = floatval($order['biaya_packing'] ?? 0);
    $totalDiskonOrder = floatval($order['total_diskon'] ?? 0);
    // Gunakan jumlah_diskon dari penggunaan_voucher jika ada
    $voucherDiscount = $voucherUsage ? floatval($voucherUsage['jumlah_diskon'] ?? 0) : 0;
    $grandTotal = floatval($payment['total_bayar'] ?? $order['total_bayar'] ?? 0);

    $shipmentQuery = $db->prepare("SELECT * FROM shipment WHERE id_order = :order_id LIMIT 1");
    $shipmentQuery->execute([':order_id' => $orderId]);
    $shipment = $shipmentQuery->fetch(PDO::FETCH_ASSOC);

    // Otomatis membatalkan pesanan jika status pembayaran 'gagal' tapi status order masih 'pending'
    if ($payment && $payment['status_pembayaran'] === 'gagal' && $order['status_order'] === 'pending') {
        $updateOrderQuery = $db->prepare("
            UPDATE orders 
            SET status_order = 'dibatalkan',
                catatan_order = CONCAT(
                    IFNULL(catatan_order, ''),
                    CASE WHEN catatan_order IS NOT NULL AND catatan_order != '' THEN '\n\n' ELSE '' END,
                    '[SISTEM] Pesanan otomatis dibatalkan oleh Nano Komputer karena waktu pembayaran habis pada ', DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i')
                )
            WHERE id_order = :order_id AND status_order = 'pending'
        ");
        $updateOrderQuery->execute([':order_id' => $orderId]);

        // Refresh data order setelah pembatalan
        $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
        $order = $orderQuery->fetch(PDO::FETCH_ASSOC);
    }

    if ($shipment && !empty($shipment['id_alamat'])) {
        $addressQuery = $db->prepare("SELECT * FROM address_book WHERE id_alamat = :id_alamat");
        $addressQuery->execute([':id_alamat' => $shipment['id_alamat']]);
        $address = $addressQuery->fetch(PDO::FETCH_ASSOC);
    }

    if (!$address) {
        $defaultAddressQuery = $db->prepare("SELECT * FROM address_book WHERE id_customer = :customer_id AND default_alamat = 1 LIMIT 1");
        $defaultAddressQuery->execute([':customer_id' => $customerId]);
        $address = $defaultAddressQuery->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log('DetailOrder Error: ' . $e->getMessage());
    header('Location: myOrder.php');
    exit;
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Pesanan Saya', 'url' => 'myOrder.php'],
    ['label' => 'Detail Pesanan', 'url' => null]
];

$currentStep = getStatusSteps($order['status_order']);
$statusBadge = getStatusBadge($order['status_order']);

include '../../components/users/head.php';
?>

<!-- Modern UI Styles -->
<style>
    :root {
        --primary: #882426;
        --primary-light: #a82e31;
        --primary-dark: #6a1c1e;
        --primary-50: rgba(136, 36, 38, 0.05);
        --primary-100: rgba(136, 36, 38, 0.1);
        --primary-200: rgba(136, 36, 38, 0.2);
    }

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

    @keyframes pulse-ring {
        0% {
            transform: scale(0.95);
            opacity: 1;
        }

        50% {
            transform: scale(1.05);
            opacity: 0.7;
        }

        100% {
            transform: scale(0.95);
            opacity: 1;
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .animate-delay-100 {
        animation-delay: 0.1s;
    }

    .animate-delay-200 {
        animation-delay: 0.2s;
    }

    .animate-delay-300 {
        animation-delay: 0.3s;
    }

    /* Modern Card Styles */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .card-header {
        background: var(--primary);
        color: white;
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.05);
        transform: rotate(25deg);
        pointer-events: none;
    }

    .card-header-light {
        background: var(--primary-50);
        border-bottom: 2px solid var(--primary);
    }

    /* Status Timeline */
    .timeline-step {
        position: relative;
        z-index: 10;
    }

    .timeline-step-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
    }

    .timeline-step-icon.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 15px rgba(136, 36, 38, 0.35);
    }

    .timeline-step-icon.current {
        animation: pulse-ring 2s infinite;
    }

    .timeline-step-icon.current::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid var(--primary);
        opacity: 0.3;
    }

    .timeline-step-icon.inactive {
        background: #e5e7eb;
        color: #9ca3af;
    }

    .timeline-connector {
        flex: 1;
        height: 4px;
        border-radius: 2px;
        margin: 0 8px;
        transition: background 0.3s ease;
    }

    .timeline-connector.active {
        background: var(--primary);
    }

    .timeline-connector.inactive {
        background: #e5e7eb;
    }

    /* Shipment Timeline */
    .shipment-step {
        position: relative;
        padding-left: 2.5rem;
    }

    .shipment-step::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 20px;
        bottom: -20px;
        width: 2px;
        background: #e5e7eb;
    }

    .shipment-step:last-child::before {
        display: none;
    }

    .shipment-step.active::before {
        background: var(--primary);
    }

    .shipment-dot {
        position: absolute;
        left: 0;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #e5e7eb;
        background: white;
        transition: all 0.3s ease;
    }

    .shipment-dot.active {
        border-color: var(--primary);
        background: var(--primary);
    }

    .shipment-dot.current {
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(136, 36, 38, 0.2);
    }

    /* Product Card */
    .product-card {
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .product-card:hover {
        background: #fafafa;
        border-color: var(--primary-100);
    }

    /* Action Buttons */
    .btn-primary {
        background: var(--primary);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(136, 36, 38, 0.25);
    }

    .btn-primary:hover {
        background: var(--primary-light);
        box-shadow: 0 6px 20px rgba(136, 36, 38, 0.35);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        color: var(--primary);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid var(--primary);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-secondary:hover {
        background: var(--primary-50);
    }

    .btn-outline {
        background: white;
        color: #4b5563;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Modal Styles */
    .modern-modal {
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        background: var(--primary);
        color: white;
        padding: 1.5rem;
        text-align: center;
        position: relative;
    }

    .modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        position: relative;
        z-index: 1;
    }

    /* Badge Styles */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-badge-dikonfirmasi {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-badge-diproses {
        background: #fefce8;
        color: #a16207;
    }

    .status-badge-dikirim {
        background: #eef2ff;
        color: #4338ca;
    }

    .status-badge-selesai {
        background: #f0fdf4;
        color: #15803d;
    }

    .status-badge-dibatalkan {
        background: #fef2f2;
        color: #b91c1c;
    }

    /* Price Tag */
    .price-tag {
        color: var(--primary);
        font-weight: 700;
    }

    .discount-badge {
        background: #fef2f2;
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Info Grid */
    .info-item {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 12px;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: #111827;
    }

    /* Cancelled Order Alert */
    .cancelled-alert {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        border: 1px solid #fecaca;
        border-left: 4px solid #dc2626;
        padding: 1.5rem;
        border-radius: 12px;
    }

    .cancelled-alert.system {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-color: #fcd34d;
        border-left-color: #f59e0b;
    }
</style>

<body class="w-full bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <div id="navbarSpacer" class="transition-all duration-300 pt-16 md:pt-40 lg:pt-[172px]"></div>
    <main class="max-w-full mb-10">
        <div class="mt-6 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <!-- Enhanced Back Button -->
            <a href="myOrder.php" class="group inline-flex items-center gap-3 text-gray-600 hover:text-[#882426] mb-8 transition-all duration-300 animate-fade-in">
                <span class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:bg-[#882426] group-hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </span>
                <span class="font-semibold">Kembali ke Pesanan Saya</span>
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Order Header Card -->
                    <div class="modern-card animate-fade-in">
                        <div class="card-header">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
                                <div>
                                    <p class="text-white/70 text-sm mb-1">No. Pesanan</p>
                                    <p class="text-2xl font-bold text-white">#<?= htmlspecialchars($order['id_order']) ?></p>
                                    <p class="text-white/70 text-sm mt-1"><?= formatTanggal($order['tanggal_order']) ?></p>
                                </div>
                                <span class="status-badge status-badge-<?= $order['status_order'] ?> self-start sm:self-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?php if ($order['status_order'] === 'selesai'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        <?php elseif ($order['status_order'] === 'dibatalkan'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        <?php elseif ($order['status_order'] === 'dikirim'): ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        <?php else: ?>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        <?php endif; ?>
                                    </svg>
                                    <?= $statusBadge['label'] ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($order['status_order'] !== 'dibatalkan'): ?>
                            <!-- Enhanced Status Timeline -->
                            <div class="p-6 bg-gray-50/50">
                                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Status Pesanan
                                </h3>
                                <div class="relative">
                                    <!-- Timeline -->
                                    <div class="flex items-start justify-between relative">
                                        <?php
                                        $statusSteps = [
                                            ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Dikonfirmasi', 'desc' => 'Pesanan dikonfirmasi'],
                                            ['icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'label' => 'Diproses', 'desc' => 'Sedang dikemas'],
                                            ['icon' => 'M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h4a1 1 0 011 1v7a1 1 0 01-1 1h-.05a2.5 2.5 0 00-4.9 0H12V8a1 1 0 011-1z', 'label' => 'Dikirim', 'desc' => 'Dalam pengiriman'],
                                            ['icon' => 'M5 13l4 4L19 7', 'label' => 'Selesai', 'desc' => 'Pesanan selesai']
                                        ];
                                        ?>
                                        <?php foreach ($statusSteps as $index => $step): ?>
                                            <?php
                                            $stepNum = $index + 1;
                                            $isActive = $currentStep >= $stepNum;
                                            $isCurrent = $currentStep === $stepNum;
                                            $iconClass = $isActive ? 'active' : 'inactive';
                                            if ($isCurrent) $iconClass .= ' current';
                                            ?>
                                            <div class="timeline-step flex flex-col items-center flex-1">
                                                <div class="timeline-step-icon <?= $iconClass ?>">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $step['icon'] ?>" />
                                                    </svg>
                                                </div>
                                                <p class="mt-3 text-sm font-semibold text-center <?= $isActive ? 'text-[#882426]' : 'text-gray-400' ?>"><?= $step['label'] ?></p>
                                                <p class="text-xs text-center <?= $isActive ? 'text-gray-600' : 'text-gray-400' ?> hidden sm:block"><?= $step['desc'] ?></p>
                                            </div>
                                            <?php if ($index < count($statusSteps) - 1): ?>
                                                <div class="timeline-connector <?= $currentStep > $stepNum ? 'active' : 'inactive' ?> mt-7 hidden sm:block"></div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Enhanced Cancelled Order Section -->
                            <div class="p-6">
                                <?php
                                $cancellationInfo = parseCancellationInfo($order['catatan_order']);
                                $alertClass = $cancellationInfo['is_system'] ? 'system' : '';
                                ?>
                                <div class="cancelled-alert <?= $alertClass ?>">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 <?= $cancellationInfo['is_system'] ? 'bg-orange-100' : 'bg-red-100' ?>">
                                            <svg class="w-6 h-6 <?= $cancellationInfo['is_system'] ? 'text-orange-600' : 'text-red-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <?php if ($cancellationInfo['is_system']): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <?php else: ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <?php endif; ?>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold <?= $cancellationInfo['is_system'] ? 'text-orange-800' : 'text-red-800' ?> mb-3">Pesanan Dibatalkan</h4>
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium <?= $cancellationInfo['is_system'] ? 'text-orange-700' : 'text-red-700' ?>">Dibatalkan oleh:</span>
                                                    <span class="text-sm <?= $cancellationInfo['is_system'] ? 'text-orange-600' : 'text-red-600' ?>"><?= htmlspecialchars($cancellationInfo['cancelled_by']) ?></span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="text-sm font-medium <?= $cancellationInfo['is_system'] ? 'text-orange-700' : 'text-red-700' ?>">Alasan:</span>
                                                    <span class="text-sm <?= $cancellationInfo['is_system'] ? 'text-orange-600' : 'text-red-600' ?>"><?= htmlspecialchars($cancellationInfo['reason']) ?></span>
                                                </div>
                                                <?php if ($cancellationInfo['datetime']): ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium <?= $cancellationInfo['is_system'] ? 'text-orange-700' : 'text-red-700' ?>">Waktu:</span>
                                                        <span class="text-sm <?= $cancellationInfo['is_system'] ? 'text-orange-600' : 'text-red-600' ?>"><?= htmlspecialchars($cancellationInfo['datetime']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($cancellationInfo['is_system']): ?>
                                                <div class="mt-4 p-3 bg-white/60 rounded-lg">
                                                    <p class="text-xs text-orange-700 flex items-start gap-2">
                                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Sistem secara otomatis membatalkan pesanan karena waktu pembayaran berakhir tanpa ada tindakan pembayaran
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Enhanced Product List Card -->
                    <div class="modern-card animate-fade-in animate-delay-100">
                        <div class="card-header-light px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Daftar Produk
                            </h3>
                            <span class="px-3 py-1 bg-[#882426]/10 text-[#882426] text-sm font-medium rounded-full"><?= count($orderDetails) ?> Item</span>
                        </div>
                        <div class="p-6">
                            <?php if (!empty($orderDetails)): ?>
                                <div class="space-y-4">
                                    <?php foreach ($orderDetails as $index => $item): ?>
                                        <?php
                                        $imagePath = '../../uploads/products/' . ($item['foto_produk'] ?? '');
                                        $imageUrl = (!empty($item['foto_produk']) && file_exists($imagePath))
                                            ? $imagePath
                                            : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23CBD5E1'%3E%3Cpath d='M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E";
                                        ?>
                                        <div class="product-card flex flex-col sm:flex-row gap-4 <?= $order['status_order'] === 'dibatalkan' ? 'opacity-60' : '' ?>">
                                            <div class="relative">
                                                <img alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                                    src="<?= $imageUrl ?>"
                                                    class="object-cover w-24 h-24 rounded-xl bg-gray-100 flex-shrink-0">
                                                <?php if ($item['diskon_satuan'] > 0): ?>
                                                    <?php
                                                    $diskonPersen = floatval($item['diskon_persen'] ?? 0);
                                                    $diskonJenis = $item['diskon_jenis'] ?? 'nominal';
                                                    if ($diskonJenis === 'persen' && $diskonPersen > 0) {
                                                        $badgeText = round($diskonPersen) . '%';
                                                    } elseif ($diskonPersen > 0) {
                                                        $badgeText = 'Diskon';
                                                    } else {
                                                        $badgeText = '%';
                                                    }
                                                    ?>
                                                    <span class="absolute -top-2 -right-2 w-10 h-10 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg"><?= htmlspecialchars($badgeText) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($item['nama_product']) ?></h4>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">Qty: <?= $item['jumlah'] ?></span>
                                                </div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <?php if ($item['diskon_satuan'] > 0): ?>
                                                        <p class="text-sm text-gray-400 line-through"><?= formatRupiah($item['harga_satuan']) ?></p>
                                                        <p class="text-lg font-bold text-[#882426]"><?= formatRupiah($item['harga_setelah_diskon']) ?></p>
                                                        <span class="discount-badge">Hemat <?= formatRupiah($item['diskon_satuan']) ?></span>
                                                    <?php else: ?>
                                                        <p class="text-lg font-bold text-[#882426]"><?= formatRupiah($item['harga_satuan']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0 self-center">
                                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Subtotal</p>
                                                <p class="text-lg font-bold text-gray-900"><?= formatRupiah($item['subtotal']) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-gray-500">Detail produk tidak tersedia</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($address || $shipment): ?>
                        <!-- Enhanced Shipping Info Card -->
                        <div class="modern-card animate-fade-in animate-delay-200">
                            <div class="card-header-light px-6 py-4">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Informasi Pengiriman
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php if ($address): ?>
                                        <div class="info-item">
                                            <p class="info-label">Penerima</p>
                                            <p class="info-value"><?= htmlspecialchars($address['nama_penerima']) ?></p>
                                            <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($address['nomor_hp']) ?></p>
                                        </div>
                                        <div class="info-item">
                                            <p class="info-label">Alamat Pengiriman</p>
                                            <p class="info-value text-sm"><?= htmlspecialchars($address['alamat_lengkap']) ?></p>
                                            <p class="text-gray-600 text-sm mt-1">
                                                <?= htmlspecialchars($address['kecamatan']) ?>, <?= htmlspecialchars($address['kota']) ?><br>
                                                <?= htmlspecialchars($address['provinsi']) ?> <?= htmlspecialchars($address['kode_pos']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($order['shipping_method'] === 'pickup' && $storePickup): ?>
                                        <!-- Store Pickup Section -->
                                        <div class="col-span-1 md:col-span-2">
                                            <div class="info-item border-l-4 border-[#882426] bg-[#882426]/5 p-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-12 h-12 bg-[#882426] rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="info-label text-[#882426] font-bold">Ambil di Toko</p>
                                                        <p class="text-lg font-bold text-gray-900 mt-1"><?= htmlspecialchars($storePickup['nama_toko']) ?></p>
                                                        <div class="mt-3 space-y-2 text-sm">
                                                            <div class="flex items-start gap-2">
                                                                <svg class="w-4 h-4 text-[#882426] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                                <div class="text-gray-600">
                                                                    <p><?= htmlspecialchars($storePickup['alamat']) ?></p>
                                                                    <p class="text-xs mt-1"><?= htmlspecialchars($storePickup['kecamatan'] ?? '') ?>, <?= htmlspecialchars($storePickup['kota_kabupaten']) ?></p>
                                                                    <p class="text-xs"><?= htmlspecialchars($storePickup['provinsi']) ?> <?= htmlspecialchars($storePickup['kode_pos']) ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($storePickup['no_telepon'])): ?>
                                                                <div class="flex items-center gap-2">
                                                                    <svg class="w-4 h-4 text-[#882426] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                    </svg>
                                                                    <span class="text-gray-600"><?= htmlspecialchars($storePickup['no_telepon']) ?></span>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (!empty($storePickup['jam_buka']) && !empty($storePickup['jam_tutup'])): ?>
                                                                <div class="flex items-center gap-2">
                                                                    <svg class="w-4 h-4 text-[#882426] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <span class="text-gray-600"><?= htmlspecialchars(substr($storePickup['jam_buka'], 0, 5)) ?> - <?= htmlspecialchars(substr($storePickup['jam_tutup'], 0, 5)) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($shipment): ?>
                                        <!-- Regular Shipping Method -->
                                        <div class="info-item">
                                            <p class="info-label">Jasa Pengiriman</p>
                                            <p class="info-value"><?= htmlspecialchars(strtoupper($shipment['jasa_pengiriman'] ?? '-')) ?></p>
                                            <?php if (!empty($shipment['courier_service'])): ?>
                                                <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($shipment['courier_service']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($shipment['estimasi_hari'])): ?>
                                                <p class="text-[#882426] text-sm font-medium mt-1">Estimasi: <?= $shipment['estimasi_hari'] ?> hari</p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($shipment['no_resi'])): ?>
                                            <div class="info-item bg-[#882426]/5">
                                                <p class="info-label">No. Resi</p>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <p class="info-value text-[#882426]"><?= htmlspecialchars($shipment['no_resi']) ?></p>
                                                    <button onclick="copyResi('<?= htmlspecialchars($shipment['no_resi']) ?>')" class="p-2 bg-[#882426] text-white rounded-lg hover:bg-[#a82e31] transition-colors" title="Salin No. Resi">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if ($shipment && in_array($order['status_order'], ['dikirim', 'selesai'])): ?>
                                    <!-- Enhanced Shipment Timeline -->
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <h4 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Lacak Pengiriman
                                        </h4>
                                        <?php
                                        $shipmentStatusSteps = [
                                            'pending' => 0,
                                            'dikemas' => 1,
                                            'dikirim' => 2,
                                            'dalam_perjalanan' => 3,
                                            'tiba' => 4,
                                            'diterima' => 5
                                        ];
                                        $currentShipmentStep = $shipmentStatusSteps[$shipment['status_pengiriman']] ?? 0;
                                        $shipmentTimeline = [
                                            ['status' => 'dikemas', 'label' => 'Dikemas', 'desc' => 'Pesanan sedang dikemas', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                                            ['status' => 'dikirim', 'label' => 'Dikirim', 'desc' => 'Pesanan telah dikirim', 'icon' => 'M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z'],
                                            ['status' => 'dalam_perjalanan', 'label' => 'Dalam Perjalanan', 'desc' => 'Paket dalam perjalanan', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                                            ['status' => 'tiba', 'label' => 'Tiba di Tujuan', 'desc' => 'Paket tiba di kota tujuan', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                                            ['status' => 'diterima', 'label' => 'Diterima', 'desc' => 'Paket diterima', 'icon' => 'M5 13l4 4L19 7']
                                        ];
                                        ?>
                                        <div class="space-y-1">
                                            <?php foreach ($shipmentTimeline as $index => $step): ?>
                                                <?php
                                                $stepNum = $index + 1;
                                                $isActive = $currentShipmentStep >= $stepNum;
                                                $isCurrent = $currentShipmentStep === $stepNum;
                                                $dotClass = $isActive ? ($isCurrent ? 'current' : 'active') : '';
                                                ?>
                                                <div class="shipment-step <?= $isActive ? 'active' : '' ?>">
                                                    <div class="shipment-dot <?= $dotClass ?>"></div>
                                                    <div class="pb-4">
                                                        <p class="font-semibold <?= $isActive ? 'text-gray-900' : 'text-gray-400' ?>"><?= $step['label'] ?></p>
                                                        <p class="text-sm <?= $isActive ? 'text-gray-600' : 'text-gray-400' ?>"><?= $step['desc'] ?></p>
                                                        <?php if ($isCurrent && !empty($shipment['tanggal_dikirim'])): ?>
                                                            <p class="text-xs text-[#882426] font-medium mt-1"><?= formatTanggal($shipment['tanggal_dikirim']) ?></p>
                                                        <?php endif; ?>
                                                        <?php if ($step['status'] === 'diterima' && !empty($shipment['tanggal_diterima'])): ?>
                                                            <p class="text-xs text-green-600 font-medium mt-1"><?= formatTanggal($shipment['tanggal_diterima']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if (!empty($order['catatan_order'])): ?>
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900">Catatan Pesanan</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-700"><?= nl2br(htmlspecialchars($order['catatan_order'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($order['status_order'] === 'selesai'): ?>
                        <?php
                        $reviewedProducts = [];
                        $reviewError = false;
                        try {
                            $reviewQuery = $db->prepare("SELECT id_review, id_product, rating, komentar, foto_review, status_review FROM review WHERE id_order = :order_id AND id_customer = :customer_id");
                            $reviewQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
                            $existingReviews = $reviewQuery->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($existingReviews as $rev) {
                                $reviewedProducts[$rev['id_product']] = $rev;
                            }
                        } catch (PDOException $e) {
                            error_log('Review Query Error: ' . $e->getMessage());
                            $reviewError = true;
                        }

                        $returnRequest = null;
                        $returnError = false;
                        try {
                            $returnQuery = $db->prepare("SELECT * FROM return_request WHERE id_order = :order_id AND id_customer = :customer_id ORDER BY tanggal_pengajuan DESC LIMIT 1");
                            $returnQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
                            $returnRequest = $returnQuery->fetch(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            error_log('Return Query Error: ' . $e->getMessage());
                            $returnError = true;
                        }

                        $canReturn = true;
                        $completionTimestamp = $shipment['tanggal_diterima'] ?? $order['updated_at'] ?? $order['tanggal_order'];
                        $orderCompleteDate = new DateTime($completionTimestamp);
                        $now = new DateTime();
                        $daysSinceComplete = $now->diff($orderCompleteDate)->days;
                        $daysRemaining = max(0, 7 - $daysSinceComplete);
                        if ($daysSinceComplete > 7) {
                            $canReturn = false;
                        }
                        ?>

                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    Review Produk
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <?php foreach ($orderDetails as $item): ?>
                                        <?php
                                        $hasReview = isset($reviewedProducts[$item['id_product']]);
                                        $reviewData = $reviewedProducts[$item['id_product']] ?? null;
                                        $imagePath = '../../uploads/products/' . ($item['foto_produk'] ?? '');
                                        $imageUrl = (!empty($item['foto_produk']) && file_exists($imagePath))
                                            ? $imagePath
                                            : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23CBD5E1'%3E%3Cpath d='M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E";
                                        ?>
                                        <div class="flex flex-col sm:flex-row gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($item['nama_product']) ?>" class="w-16 h-16 object-cover rounded-lg bg-gray-100 flex-shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 line-clamp-2 mb-2"><?= htmlspecialchars($item['nama_product']) ?></h4>
                                                <?php if ($hasReview): ?>
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <svg class="w-4 h-4 <?= $i <= $reviewData['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                            </svg>
                                                        <?php endfor; ?>
                                                        <span class="text-xs text-gray-500 ml-1">
                                                            (<?= $reviewData['status_review'] === 'approved' ? 'Ditampilkan' : ($reviewData['status_review'] === 'pending' ? 'Menunggu persetujuan' : 'Ditolak') ?>)
                                                        </span>
                                                    </div>
                                                    <?php if (!empty($reviewData['komentar'])): ?>
                                                        <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($reviewData['komentar']) ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($reviewData['foto_review'])): ?>
                                                        <div class="mt-2 flex gap-2 flex-wrap">
                                                            <button type="button" onclick="openLightbox('../../uploads/reviews/<?= htmlspecialchars($reviewData['foto_review']) ?>')" class="relative group">
                                                                <img src="../../uploads/reviews/<?= htmlspecialchars($reviewData['foto_review']) ?>" alt="Foto Review" class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:border-[#882426] transition-all group-hover:shadow-md">
                                                                <div class="absolute inset-0 bg-black/40 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex gap-2 mt-2 flex-wrap">
                                                        <button type="button"
                                                            class="edit-review-btn inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors"
                                                            data-product-id="<?= htmlspecialchars($item['id_product']) ?>"
                                                            data-product-name="<?= htmlspecialchars($item['nama_product']) ?>"
                                                            data-review-id="<?= htmlspecialchars($reviewData['id_review'] ?? '') ?>"
                                                            data-rating="<?= (int)$reviewData['rating'] ?>"
                                                            data-komentar="<?= htmlspecialchars($reviewData['komentar'] ?? '') ?>"
                                                            data-foto="<?= htmlspecialchars($reviewData['foto_review'] ?? '') ?>">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit Review
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <button type="button"
                                                        onclick="openReviewModal('<?= htmlspecialchars($item['id_product']) ?>', '<?= htmlspecialchars(addslashes($item['nama_product'])) ?>')"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Tulis Review
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                    </svg>
                                    Pengajuan Pengembalian
                                </h3>
                            </div>
                            <div class="p-6">
                                <?php if ($returnRequest): ?>
                                    <?php
                                    $returnStatusBadge = match ($returnRequest['status_return']) {
                                        'pending' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Menunggu Review'],
                                        'diproses' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Sedang Diproses'],
                                        'disetujui' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Disetujui'],
                                        'ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Ditolak'],
                                        'selesai' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Selesai'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($returnRequest['status_return'])]
                                    };
                                    ?>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <p class="text-sm text-gray-500">ID Pengajuan</p>
                                                <p class="font-medium text-gray-900"><?= htmlspecialchars($returnRequest['id_return']) ?></p>
                                            </div>
                                            <span class="px-3 py-1 <?= $returnStatusBadge['bg'] ?> <?= $returnStatusBadge['text'] ?> text-sm font-medium rounded-full">
                                                <?= $returnStatusBadge['label'] ?>
                                            </span>
                                        </div>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Alasan</span>
                                                <span class="text-gray-900"><?= htmlspecialchars($returnRequest['alasan_return']) ?></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Tanggal Pengajuan</span>
                                                <span class="text-gray-900"><?= formatTanggal($returnRequest['tanggal_pengajuan']) ?></span>
                                            </div>
                                            <?php if (!empty($returnRequest['deskripsi_return'])): ?>
                                                <div class="pt-2 border-t border-gray-200">
                                                    <p class="text-gray-500 mb-1">Deskripsi:</p>
                                                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($returnRequest['deskripsi_return'])) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($returnRequest['catatan_admin']) && in_array($returnRequest['status_return'], ['disetujui', 'ditolak'])): ?>
                                                <div class="pt-2 border-t border-gray-200">
                                                    <p class="text-gray-500 mb-1">Catatan Admin:</p>
                                                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($returnRequest['catatan_admin'])) ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($canReturn): ?>
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-medium text-gray-900 mb-2">Ada masalah dengan pesanan Anda?</h4>
                                        <p class="text-sm text-gray-500 mb-4">Anda bisa mengajukan pengembalian dalam <?= $daysRemaining ?> hari lagi</p>
                                        <button type="button" onclick="openReturnModal()"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3" />
                                            </svg>
                                            Ajukan Pengembalian
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500">Periode pengajuan pengembalian telah berakhir</p>
                                        <p class="text-sm text-gray-400 mt-1">Pengajuan hanya bisa dilakukan dalam 7 hari setelah pesanan selesai</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Enhanced Payment Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="modern-card sticky top-32 animate-fade-in animate-delay-300">
                        <!-- Payment Header -->
                        <div class="card-header">
                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Rincian Pembayaran</h3>
                                    <p class="text-white/70 text-sm">Order #<?= htmlspecialchars($order['id_order']) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <?php if ($payment): ?>
                                <!-- Payment Method Info -->
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Metode</p>
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $payment['metode_pembayaran'] ?? '-'))) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Status -->
                                <div class="flex items-center justify-between p-3 rounded-xl <?php
                                                                                                $paymentStatus = $payment['status_pembayaran'] ?? 'pending';
                                                                                                echo match ($paymentStatus) {
                                                                                                    'settlement', 'capture', 'success' => 'bg-green-50',
                                                                                                    'pending' => 'bg-orange-50',
                                                                                                    'deny', 'cancel', 'expire', 'failure' => 'bg-red-50',
                                                                                                    default => 'bg-gray-50'
                                                                                                };
                                                                                                ?>">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center <?php
                                                                                                            echo match ($paymentStatus) {
                                                                                                                'settlement', 'capture', 'success' => 'bg-green-100',
                                                                                                                'pending' => 'bg-orange-100',
                                                                                                                'deny', 'cancel', 'expire', 'failure' => 'bg-red-100',
                                                                                                                default => 'bg-gray-100'
                                                                                                            };
                                                                                                            ?>">
                                            <svg class="w-5 h-5 <?php
                                                                echo match ($paymentStatus) {
                                                                    'settlement', 'capture', 'success' => 'text-green-600',
                                                                    'pending' => 'text-orange-600',
                                                                    'deny', 'cancel', 'expire', 'failure' => 'text-red-600',
                                                                    default => 'text-gray-600'
                                                                };
                                                                ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <?php if (in_array($paymentStatus, ['settlement', 'capture', 'success'])): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <?php elseif ($paymentStatus === 'pending'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <?php else: ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                <?php endif; ?>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Status Pembayaran</p>
                                            <p class="font-semibold <?php
                                                                    echo match ($paymentStatus) {
                                                                        'settlement', 'capture', 'success' => 'text-green-700',
                                                                        'pending' => 'text-orange-700',
                                                                        'deny', 'cancel', 'expire', 'failure' => 'text-red-700',
                                                                        default => 'text-gray-700'
                                                                    };
                                                                    ?>"><?= ucfirst($paymentStatus) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($payment['tanggal_pembayaran'])): ?>
                                    <div class="text-center py-2">
                                        <p class="text-xs text-gray-500">Dibayar pada</p>
                                        <p class="text-sm font-medium text-gray-700"><?= formatTanggal($payment['tanggal_pembayaran']) ?></p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Price Breakdown -->
                            <div class="border-t border-gray-100 pt-4 space-y-3">
                                <?php if ($productDiscount > 0): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Subtotal Produk</span>
                                        <span class="text-gray-400 line-through"><?= formatRupiah($originalSubtotal) ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-green-600 font-medium">Diskon Produk</span>
                                        <span class="text-green-600 font-medium">- <?= formatRupiah($productDiscount) ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-semibold text-gray-900"><?= formatRupiah($subtotalAfterDiscount) ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal (<?= count($orderDetails) ?> Barang)</span>
                                        <span class="font-semibold text-gray-900"><?= formatRupiah($subtotalAfterDiscount) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Pajak (11%)</span>
                                    <span class="font-semibold text-gray-900"><?= formatRupiah($taxAmount) ?></span>
                                </div>

                                <?php if ($order['shipping_method'] === 'pickup'): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Ongkos Kirim</span>
                                        <span class="text-green-600 font-medium">Gratis (Ambil di Toko)</span>
                                    </div>
                                <?php elseif ($shippingCost > 0): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Ongkos Kirim</span>
                                        <span class="font-semibold text-gray-900"><?= formatRupiah($shippingCost) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($packingCost > 0): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Biaya Pengemasan</span>
                                        <span class="font-semibold text-gray-900"><?= formatRupiah($packingCost) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($voucherDiscount > 0 && $voucherUsage): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-green-600 font-medium">Diskon Voucher</span>
                                        <span class="font-medium text-green-600">- <?= formatRupiah($voucherDiscount) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Total -->
                            <div class="border-t-2 border-dashed border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total Belanja</span>
                                    <span class="text-2xl font-bold <?= $order['status_order'] === 'dibatalkan' ? 'text-gray-400 line-through' : 'text-[#882426]' ?>"><?= formatRupiah($grandTotal) ?></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3 pt-4">
                                <?php if ($order['status_order'] === 'pending' && ($payment['status_pembayaran'] ?? 'pending') === 'pending'): ?>
                                    <a href="processPayment.php?order_id=<?= urlencode($order['id_order']) ?>" class="btn-primary w-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Bayar Sekarang
                                    </a>
                                <?php elseif ($order['status_order'] === 'dikirim'): ?>
                                    <?php if (!empty($shipment['no_resi'])): ?>
                                        <button onclick="trackOrder('<?= htmlspecialchars($shipment['no_resi']) ?>')" class="btn-primary w-full">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Lacak Paket
                                        </button>
                                    <?php endif; ?>
                                <?php elseif ($order['status_order'] === 'selesai'): ?>
                                    <a href="../../api/orders/generate-invoice.php?order_id=<?= urlencode($order['id_order']) ?>" target="_blank" class="btn-primary w-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download Invoice
                                    </a>
                                <?php endif; ?>

                                <a href="myOrder.php" class="btn-outline w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeReviewModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="modern-modal relative max-w-md w-full">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button onclick="closeReviewModal()" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors z-20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="relative z-10">
                        <h3 id="reviewModalTitle" class="text-xl font-bold text-white">Tulis Review</h3>
                        <p id="reviewModalSubtitle" class="text-white/80 text-xs mt-1">Bagikan pengalaman Anda</p>
                    </div>
                    <p id="reviewProductName" class="text-white/80 text-sm line-clamp-2 mt-2 relative z-10 font-medium"></p>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        <input type="hidden" id="reviewProductId" name="product_id">
                        <input type="hidden" id="reviewOrderId" name="order_id" value="<?= htmlspecialchars($orderId) ?>">

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Berikan Rating</label>
                            <div class="flex items-center justify-center gap-2 p-4 bg-gray-50 rounded-xl" id="ratingStars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" onclick="setRating(<?= $i ?>)" class="star-btn p-1 transition-all duration-200 hover:scale-110" data-rating="<?= $i ?>">
                                        <svg class="w-10 h-10 text-gray-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                        </svg>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" id="ratingInput" name="rating" value="0" required>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Komentar (Opsional)</label>
                            <textarea name="komentar" id="reviewComment" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] resize-none transition-all"
                                placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Foto Review <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="foto_review" id="reviewPhoto"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    onchange="previewReviewPhoto(this)">
                                <label for="reviewPhoto"
                                    class="flex items-center justify-center gap-3 w-full px-4 py-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#882426] hover:bg-[#882426]/5 transition-all">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-500" id="reviewPhotoName">Tambahkan foto produk</span>
                                </label>
                            </div>
                            <div id="reviewPhotoPreview" class="mt-3 hidden">
                                <div class="space-y-2">
                                    <p class="text-xs text-gray-500 font-medium">Preview Foto Baru:</p>
                                    <img src="" alt="Preview" class="w-24 h-24 object-cover rounded-xl border-2 border-[#882426]/20">
                                </div>
                            </div>
                            <div id="existingReviewPhotoPreview" class="mt-3 hidden">
                                <div class="space-y-2">
                                    <p class="text-xs text-gray-500 font-medium">Foto Review Saat Ini:</p>
                                    <button type="button" onclick="openLightbox(this.querySelector('img').src)" class="relative group">
                                        <img src="" alt="Existing Review" class="w-24 h-24 object-cover rounded-xl border-2 border-gray-200 hover:border-[#882426] transition-all">
                                        <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="reviewId" name="review_id" value="">
                        <input type="hidden" id="isEditMode" name="is_edit" value="0">
                        <input type="hidden" id="existingFotoReview" value="">

                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="closeReviewModal()" class="btn-outline flex-1">
                                Batal
                            </button>
                            <button type="submit" id="btnSubmitReview" class="btn-primary flex-1">
                                <span id="reviewBtnText">Kirim Review</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Return Modal -->
    <div id="returnModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeReturnModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="modern-modal relative max-w-md w-full max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button onclick="closeReturnModal()" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors z-20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h3 class="text-xl font-bold text-white relative z-10">Ajukan Pengembalian</h3>
                    <p class="text-white/80 text-sm relative z-10">Pesanan #<?= htmlspecialchars($orderId) ?></p>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="returnForm" onsubmit="submitReturn(event)">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId) ?>">

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Pengembalian <span class="text-red-500">*</span></label>
                            <select name="alasan_return" id="returnReason" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                                <option value="">Pilih alasan...</option>
                                <option value="Produk rusak/cacat">Produk rusak/cacat</option>
                                <option value="Produk tidak sesuai deskripsi">Produk tidak sesuai deskripsi</option>
                                <option value="Produk salah/berbeda">Produk salah/berbeda</option>
                                <option value="Produk tidak lengkap">Produk tidak lengkap</option>
                                <option value="Berubah pikiran">Berubah pikiran</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Detail</label>
                            <textarea name="deskripsi_return" id="returnDescription" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] resize-none transition-all"
                                placeholder="Jelaskan masalah yang Anda alami..."></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Invoice <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="file_invoice" id="returnInvoice" required
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="hidden"
                                    onchange="previewInvoice(this)">
                                <label for="returnInvoice"
                                    class="flex items-center justify-center gap-3 w-full px-4 py-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#882426] hover:bg-[#882426]/5 transition-all">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-500" id="invoiceFileName">Pilih file invoice (PDF, JPG, PNG)</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Invoice wajib disertakan sebagai bukti pembelian
                            </p>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Foto Bukti <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="foto_bukti" id="returnPhoto"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    onchange="previewReturnPhoto(this)">
                                <label for="returnPhoto"
                                    class="flex items-center justify-center gap-3 w-full px-4 py-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#882426] hover:bg-[#882426]/5 transition-all">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-500" id="photoFileName">Foto kerusakan/masalah produk</span>
                                </label>
                            </div>
                            <div id="returnPhotoPreview" class="mt-3 hidden">
                                <img src="" alt="Preview" class="w-24 h-24 object-cover rounded-xl border-2 border-[#882426]/20">
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="closeReturnModal()" class="btn-outline flex-1">
                                Batal
                            </button>
                            <button type="submit" id="btnSubmitReturn" class="btn-primary flex-1">
                                <span>Kirim Pengajuan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with Animated Checkmark -->
    <div id="successModal" class="fixed inset-0 z-[70] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center transform transition-all duration-300"
                style="animation: successModalIn 0.4s ease-out;">

                <!-- Clear Animated Checkmark - Modern Design -->
                <div class="success-checkmark-wrapper mx-auto mb-6">
                    <!-- Outer Ring Animation -->
                    <div class="success-ring"></div>
                    <!-- Circle Background -->
                    <div class="success-circle">
                        <!-- Clear Bold Checkmark SVG -->
                        <svg class="success-checkmark-icon" viewBox="0 0 52 52">
                            <path class="checkmark-path" fill="none" d="M14 27l7.5 7.5L38 18" />
                        </svg>
                    </div>
                </div>

                <!-- Success Title -->
                <h3 id="successTitle" class="text-2xl font-bold text-gray-900 mb-2">Berhasil!</h3>

                <!-- Success Message -->
                <p id="successMessage" class="text-gray-600 mb-6">Aksi berhasil dilakukan.</p>

                <!-- Confetti Effect -->
                <div class="confetti-container absolute inset-0 pointer-events-none overflow-hidden rounded-3xl">
                    <div class="confetti confetti-1"></div>
                    <div class="confetti confetti-2"></div>
                    <div class="confetti confetti-3"></div>
                    <div class="confetti confetti-4"></div>
                    <div class="confetti confetti-5"></div>
                    <div class="confetti confetti-6"></div>
                </div>

                <!-- Close Button -->
                <button onclick="closeSuccessModal()" class="w-full px-6 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all shadow-lg shadow-[#882426]/25">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Success Modal Animation */
        @keyframes successModalIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Modern Clear Checkmark Animation Styles */
        .success-checkmark-wrapper {
            width: 100px;
            height: 100px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Outer Pulse Ring */
        .success-ring {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(34, 197, 94, 0.3);
            animation: ring-pulse 1.5s ease-out forwards;
        }

        @keyframes ring-pulse {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            30% {
                opacity: 1;
            }

            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        /* Circle Background with Scale Animation */
        .success-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.35);
            animation: circle-appear 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            position: relative;
        }

        @keyframes circle-appear {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            60% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Clear Checkmark SVG Icon */
        .success-checkmark-icon {
            width: 50px;
            height: 50px;
        }

        .checkmark-path {
            stroke: white;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: checkmark-draw 0.5s ease-out 0.3s forwards;
        }

        @keyframes checkmark-draw {
            0% {
                stroke-dashoffset: 48;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        /* Confetti Animation */
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            opacity: 0;
            animation: confetti-fall 3s ease-out forwards;
        }

        .confetti-1 {
            background: #882426;
            left: 20%;
            animation-delay: 0.1s;
            border-radius: 50%;
        }

        .confetti-2 {
            background: #22c55e;
            left: 40%;
            animation-delay: 0.2s;
            border-radius: 2px;
        }

        .confetti-3 {
            background: #eab308;
            left: 60%;
            animation-delay: 0.15s;
            border-radius: 50%;
        }

        .confetti-4 {
            background: #6366f1;
            left: 80%;
            animation-delay: 0.25s;
            border-radius: 2px;
        }

        .confetti-5 {
            background: #f97316;
            left: 30%;
            animation-delay: 0.3s;
            width: 8px;
            height: 8px;
        }

        .confetti-6 {
            background: #ec4899;
            left: 70%;
            animation-delay: 0.35s;
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        @keyframes confetti-fall {
            0% {
                opacity: 1;
                top: -10px;
                transform: translateX(0) rotate(0deg);
            }

            100% {
                opacity: 0;
                top: 100%;
                transform: translateX(20px) rotate(720deg);
            }
        }
    </style>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <!-- Lightbox Modal for Image Preview -->
    <div id="lightbox" class="fixed inset-0 z-[60] hidden bg-black/95 flex items-center justify-center p-4" onclick="closeLightbox(event)">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10 p-2 hover:bg-white/10 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-lg shadow-2xl">
    </div>

    <script>
        let currentRating = 0;

        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = src;
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            if (e && e.target !== document.getElementById('lightbox')) return;
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('lightbox').classList.contains('hidden')) {
                closeLightbox();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-review-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;
                    const reviewId = this.dataset.reviewId;
                    const rating = parseInt(this.dataset.rating) || 0;
                    const komentar = this.dataset.komentar || '';
                    const foto = this.dataset.foto || '';

                    openReviewModal(productId, productName, {
                        id: reviewId,
                        rating: rating,
                        komentar: komentar,
                        foto: foto
                    });
                });
            });
        });

        function copyResi(resi) {
            navigator.clipboard.writeText(resi).then(() => {
                showToast('No. Resi berhasil disalin', 'success');
            }).catch(() => {
                const textArea = document.createElement('textarea');
                textArea.value = resi;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('No. Resi berhasil disalin', 'success');
            });
        }

        function trackOrder(noResi) {
            const courierUrls = {
                'jne': 'https://www.jne.co.id/id/tracking/trace',
                'jnt': 'https://www.jet.co.id/track',
                'sicepat': 'https://www.sicepat.com/checkAwb',
                'anteraja': 'https://anteraja.id/tracking',
                'pos': 'https://www.posindonesia.co.id/id/tracking'
            };
            window.open('https://cekresi.com/?noresi=' + encodeURIComponent(noResi), '_blank');
        }

        function openReviewModal(productId, productName, existingReview = null) {
            document.getElementById('reviewProductId').value = productId;
            document.getElementById('reviewProductName').textContent = productName;
            document.getElementById('reviewPhoto').value = '';
            document.getElementById('reviewPhotoName').textContent = 'Tambahkan foto produk';
            document.getElementById('reviewPhotoPreview').classList.add('hidden');
            document.getElementById('existingReviewPhotoPreview').classList.add('hidden');

            if (existingReview) {
                document.getElementById('reviewModalTitle').textContent = 'Edit Review';
                document.getElementById('reviewModalSubtitle').textContent = 'Perbarui pengalaman Anda';
                document.getElementById('reviewId').value = existingReview.id || '';
                document.getElementById('isEditMode').value = '1';
                document.getElementById('ratingInput').value = existingReview.rating || 0;
                document.getElementById('reviewComment').value = existingReview.komentar || '';
                currentRating = existingReview.rating || 0;
                updateStars(currentRating);
                document.getElementById('reviewBtnText').textContent = 'Update Review';

                // Show existing photo if available
                if (existingReview.foto) {
                    document.getElementById('existingFotoReview').value = existingReview.foto;
                    const existingPreview = document.getElementById('existingReviewPhotoPreview');
                    existingPreview.querySelector('img').src = '../../uploads/reviews/' + existingReview.foto;
                    existingPreview.classList.remove('hidden');
                }
            } else {
                document.getElementById('reviewModalTitle').textContent = 'Tulis Review';
                document.getElementById('reviewModalSubtitle').textContent = 'Bagikan pengalaman Anda';
                document.getElementById('reviewId').value = '';
                document.getElementById('isEditMode').value = '0';
                document.getElementById('ratingInput').value = 0;
                document.getElementById('reviewComment').value = '';
                document.getElementById('existingFotoReview').value = '';
                currentRating = 0;
                updateStars(0);
                document.getElementById('reviewBtnText').textContent = 'Kirim Review';
            }

            document.getElementById('reviewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function setRating(rating) {
            currentRating = rating;
            document.getElementById('ratingInput').value = rating;
            updateStars(rating);
        }

        function updateStars(rating) {
            const stars = document.querySelectorAll('.star-btn svg');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        async function submitReview(e) {
            e.preventDefault();

            const rating = parseInt(document.getElementById('ratingInput').value);
            if (rating < 1 || rating > 5) {
                showToast('Silakan pilih rating 1-5', 'error');
                return;
            }

            const btn = document.getElementById('btnSubmitReview');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const formData = new FormData(document.getElementById('reviewForm'));
                const response = await fetch('../../api/reviews/submit-review.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    closeReviewModal();
                    showSuccessModal('Review Berhasil!', result.message || 'Review Anda telah berhasil dikirim. Terima kasih atas feedback Anda!');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function openReturnModal() {
            document.getElementById('returnReason').value = '';
            document.getElementById('returnDescription').value = '';
            document.getElementById('returnInvoice').value = '';
            document.getElementById('returnPhoto').value = '';
            document.getElementById('invoiceFileName').textContent = 'Pilih file invoice (PDF, JPG, PNG)';
            document.getElementById('photoFileName').textContent = 'Foto kerusakan/masalah produk';
            document.getElementById('returnPhotoPreview').classList.add('hidden');
            document.getElementById('returnModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function previewInvoice(input) {
            if (input.files && input.files[0]) {
                document.getElementById('invoiceFileName').textContent = input.files[0].name;
            }
        }

        function previewReturnPhoto(input) {
            if (input.files && input.files[0]) {
                document.getElementById('photoFileName').textContent = input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('returnPhotoPreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewReviewPhoto(input) {
            if (input.files && input.files[0]) {
                document.getElementById('reviewPhotoName').textContent = input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('reviewPhotoPreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function submitReturn(e) {
            e.preventDefault();

            const reason = document.getElementById('returnReason').value;
            if (!reason) {
                showToast('Silakan pilih alasan pengembalian', 'error');
                return;
            }

            const btn = document.getElementById('btnSubmitReturn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const formData = new FormData(document.getElementById('returnForm'));
                const response = await fetch('../../api/returns/submit-return.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    closeReturnModal();
                    showSuccessModal('Pengajuan Berhasil!', result.message || 'Pengajuan pengembalian Anda telah berhasil dikirim. Tim kami akan segera meninjau permintaan Anda.');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function showSuccessModal(title, message) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            document.body.style.overflow = '';
            location.reload();
        }

        function showToast(message, type = 'info') {
            const existingToasts = document.querySelectorAll('.toast-notification');
            existingToasts.forEach(t => t.remove());

            const toast = document.createElement('div');
            toast.className = `toast-notification fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-[60] transition-all transform ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>

</html>