<?php
$pageTitle = "Pesanan Saya";
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

\App\Auth\CustomerAuthMiddleware::requireLogin('myOrder.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Pesanan Saya', 'url' => null]
];

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
    return $hari . ' ' . $bulan[$bulanNum] . ' ' . $tahun;
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
    $badge = $badges[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($status)];
    return '<span class="px-4 py-2 ' . $badge['bg'] . ' ' . $badge['text'] . ' text-sm font-semibold rounded-full">' . $badge['label'] . '</span>';
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

    if (strpos($notes, '[SISTEM]') !== false) {
        $info['is_system'] = true;
        $info['cancelled_by'] = 'Sistem (Nano Komputer)';
        $info['reason'] = 'Waktu pembayaran habis (Otomatis dibatalkan oleh Nano Komputer)';

        if (preg_match('/\[SISTEM\].*?pada\s+(\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2})/', $notes, $matches)) {
            $info['datetime'] = $matches[1];
        }
    } elseif (strpos($notes, 'Alasan pembatalan:') !== false) {
        if (preg_match('/Alasan pembatalan:\s*(.+?)(\n|$)/i', $notes, $matches)) {
            $reason = trim($matches[1]);
            $info['reason'] = $reason ?: 'Alasan tidak disebutkan';
        }
    }

    return $info;
}

$currentTab = $_GET['tab'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$orders = [];
$orderDetails = [];
$totalOrders = 0;
$totalPages = 0;

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $statusConditions = [];

    if ($currentTab === 'diproses') {
        $statusConditions = ['dikonfirmasi', 'diproses'];
    } elseif ($currentTab === 'dikirim') {
        $statusConditions = ['dikirim'];
    } elseif ($currentTab === 'selesai') {
        $statusConditions = ['selesai'];
    } elseif ($currentTab === 'dibatalkan') {
        $statusConditions = ['dibatalkan'];
    } elseif ($currentTab === 'pending') {
        $statusConditions = ['pending'];
    }

    $statusFilter = '';
    if (!empty($statusConditions)) {
        $placeholders = [];
        foreach ($statusConditions as $i => $status) {
            $placeholders[] = ':status' . $i;
        }
        $statusFilter = " AND o.status_order IN (" . implode(',', $placeholders) . ")";
    }

    $countSql = "SELECT COUNT(*) as total FROM orders o WHERE o.id_customer = :customer_id" . $statusFilter;
    $countQuery = $db->prepare($countSql);
    $countQuery->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
    foreach ($statusConditions as $i => $status) {
        $countQuery->bindValue(':status' . $i, $status, PDO::PARAM_STR);
    }
    $countQuery->execute();
    $totalOrders = (int) $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = $totalOrders > 0 ? ceil($totalOrders / $perPage) : 0;

    $ordersSql = "
        SELECT o.*, 
               p.id_payment, p.status_pembayaran, p.metode_pembayaran, p.expiry_time, p.total_bayar as payment_total,
               s.id_shipment, s.jasa_pengiriman, s.no_resi, s.status_pengiriman, s.estimasi_hari
        FROM orders o
        LEFT JOIN payment p ON o.id_order = p.id_order
        LEFT JOIN shipment s ON o.id_order = s.id_order
        WHERE o.id_customer = :customer_id {$statusFilter}
        ORDER BY o.tanggal_order DESC
        LIMIT {$perPage} OFFSET {$offset}
    ";

    $ordersQuery = $db->prepare($ordersSql);
    $ordersQuery->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
    foreach ($statusConditions as $i => $status) {
        $ordersQuery->bindValue(':status' . $i, $status, PDO::PARAM_STR);
    }
    $ordersQuery->execute();
    $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

    $voucherUsageData = [];
    foreach ($orders as $order) {
        $detailQuery = $db->prepare("
            SELECT od.*, pr.gambar as foto_produk
            FROM order_detail od
            LEFT JOIN products pr ON od.id_product = pr.id_product
            WHERE od.id_order = :order_id
        ");
        $detailQuery->bindValue(':order_id', $order['id_order'], PDO::PARAM_STR);
        $detailQuery->execute();
        $orderDetails[$order['id_order']] = $detailQuery->fetchAll(PDO::FETCH_ASSOC);

        // Query untuk mendapatkan data penggunaan voucher
        $voucherUsageQuery = $db->prepare("
            SELECT pv.*, v.kode, v.judul, v.jenis, v.nilai as voucher_nilai, v.maksimal_diskon
            FROM penggunaan_voucher pv
            LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
            WHERE pv.id_pesanan = :order_id
            LIMIT 1
        ");
        $voucherUsageQuery->execute([':order_id' => $order['id_order']]);
        $voucherUsageData[$order['id_order']] = $voucherUsageQuery->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log('MyOrder Error: ' . $e->getMessage());
}

include '../../components/users/head.php';
?>

<!-- Custom Styles for Modern UI with Solid Colors -->
<style>
    :root {
        --primary: #882426;
        --primary-light: #a82e31;
        --primary-dark: #6a1c1e;
        --primary-glow: rgba(136, 36, 38, 0.15);
    }

    .order-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .order-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -15px rgba(136, 36, 38, 0.15);
    }

    .tab-filter {
        position: relative;
        overflow: hidden;
    }

    .tab-filter::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--primary);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .tab-filter:hover::before {
        opacity: 0.08;
    }

    .tab-filter.active {
        background: var(--primary);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(136, 36, 38, 0.3);
    }

    .status-badge {
        animation: pulse-subtle 2s infinite;
    }

    @keyframes pulse-subtle {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.85;
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-in {
        animation: slideInUp 0.4s ease-out forwards;
    }

    .product-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }

    .product-image-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(136, 36, 38, 0.03);
        pointer-events: none;
    }

    .info-pill {
        background: #f8f9fa;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .action-btn-primary {
        background: var(--primary);
        box-shadow: 0 4px 15px rgba(136, 36, 38, 0.25);
        transition: all 0.3s ease;
    }

    .action-btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 6px 20px rgba(136, 36, 38, 0.35);
        transform: translateY(-2px);
    }

    .action-btn-secondary {
        background: white;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .action-btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(136, 36, 38, 0.02);
    }

    .hero-bg {
        background: var(--primary);
    }

    /* Status-based solid backgrounds */
    .status-bg-pending {
        background: #fff7ed;
    }

    .status-bg-dikonfirmasi {
        background: #eff6ff;
    }

    .status-bg-diproses {
        background: #fefce8;
    }

    .status-bg-dikirim {
        background: #eef2ff;
    }

    .status-bg-selesai {
        background: #f0fdf4;
    }

    .status-bg-dibatalkan {
        background: #fef2f2;
    }

    /* Alert solid backgrounds */
    .alert-pending {
        background: #fff7ed;
        border-color: rgba(251, 146, 60, 0.3);
    }

    .alert-dikirim {
        background: #eef2ff;
        border-color: rgba(99, 102, 241, 0.3);
    }

    .alert-diproses {
        background: #fefce8;
        border-color: rgba(234, 179, 8, 0.3);
    }

    .alert-dibatalkan {
        background: #fef2f2;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .alert-selesai {
        background: #f0fdf4;
        border-color: rgba(34, 197, 94, 0.3);
    }

    /* Icon backgrounds with solid colors */
    .icon-bg-pending {
        background: #f97316;
    }

    .icon-bg-dikirim {
        background: #6366f1;
    }

    .icon-bg-diproses {
        background: #eab308;
    }

    .icon-bg-dibatalkan {
        background: #ef4444;
    }

    .icon-bg-selesai {
        background: #22c55e;
    }

    .icon-bg-primary {
        background: var(--primary);
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

            <!-- Enhanced Page Header -->
            <div class="mb-10 relative">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="animate-slide-in">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#882426]/10 rounded-full text-[#882426] text-sm font-medium mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Riwayat Belanja
                        </div>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-2 tracking-tight">
                            Pesanan Saya
                        </h1>
                        <p class="text-gray-600 text-lg">Kelola dan lacak semua pesanan Anda dengan mudah</p>
                    </div>
                    <div class="flex items-center gap-4 bg-white/80 backdrop-blur-sm rounded-2xl p-4 border border-gray-100 shadow-sm">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-[#882426]"><?= $totalOrders ?></p>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Pesanan</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= count(array_filter($orders, fn($o) => $o['status_order'] === 'selesai')) ?></p>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Tab Navigation -->
            <div class="mb-8 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <div class="inline-flex gap-2 p-1.5 bg-white rounded-2xl shadow-sm border border-gray-100 min-w-max">
                    <a href="?tab=all"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'all' ? 'active' : 'text-gray-600 hover:text-[#882426]' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Semua
                    </a>
                    <a href="?tab=pending"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'pending' ? 'active' : 'text-gray-600 hover:text-orange-600' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Belum Bayar
                    </a>
                    <a href="?tab=diproses"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'diproses' ? 'active' : 'text-gray-600 hover:text-yellow-600' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        Diproses
                    </a>
                    <a href="?tab=dikirim"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'dikirim' ? 'active' : 'text-gray-600 hover:text-indigo-600' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Dikirim
                    </a>
                    <a href="?tab=selesai"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'selesai' ? 'active' : 'text-gray-600 hover:text-green-600' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Selesai
                    </a>
                    <a href="?tab=dibatalkan"
                        class="tab-filter relative px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 flex items-center gap-2
                        <?= $currentTab === 'dibatalkan' ? 'active' : 'text-gray-600 hover:text-red-600' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Dibatalkan
                    </a>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Enhanced Empty State -->
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-12 text-center relative overflow-hidden">
                    <!-- Decorative Background -->
                    <div class="absolute inset-0 opacity-5">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <pattern id="emptyPattern" patternUnits="userSpaceOnUse" width="20" height="20">
                                <circle cx="10" cy="10" r="1" fill="#882426" />
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#emptyPattern)" />
                        </svg>
                    </div>
                    <div class="relative">
                        <div class="w-28 h-28 bg-gradient-to-br from-[#882426]/10 to-[#882426]/5 rounded-full flex items-center justify-center mx-auto mb-6 ring-4 ring-[#882426]/5">
                            <svg class="w-14 h-14 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Pesanan</h3>
                        <p class="text-gray-500 mb-8 max-w-sm mx-auto">Anda belum memiliki pesanan. Yuk mulai berbelanja dan temukan produk favorit Anda!</p>
                        <a href="landingPage.php" class="inline-flex items-center gap-2 px-8 py-4 action-btn-primary text-white font-semibold rounded-2xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $index => $order): ?>
                        <?php
                        $details = $orderDetails[$order['id_order']] ?? [];
                        $isExpired = false;
                        if ($order['status_order'] === 'pending' && !empty($order['expiry_time'])) {
                            $isExpired = strtotime($order['expiry_time']) < time();
                        }
                        $cancInfo = parseCancellationInfo($order['catatan_order']);

                        // Status-based styling with solid colors
                        $statusConfig = [
                            'pending' => ['bg' => 'status-bg-pending', 'accent' => 'orange'],
                            'dikonfirmasi' => ['bg' => 'status-bg-dikonfirmasi', 'accent' => 'blue'],
                            'diproses' => ['bg' => 'status-bg-diproses', 'accent' => 'yellow'],
                            'dikirim' => ['bg' => 'status-bg-dikirim', 'accent' => 'indigo'],
                            'selesai' => ['bg' => 'status-bg-selesai', 'accent' => 'green'],
                            'dibatalkan' => ['bg' => 'status-bg-dibatalkan', 'accent' => 'red']
                        ];
                        $currentStatusConfig = $statusConfig[$order['status_order']] ?? $statusConfig['pending'];
                        ?>

                        <!-- Enhanced Order Card -->
                        <div class="order-card bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden animate-slide-in"
                            style="animation-delay: <?= $index * 0.05 ?>s;">

                            <!-- Modern Card Header -->
                            <div class="<?= $currentStatusConfig['bg'] ?> px-6 py-5 border-b border-gray-100/50">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <!-- Order Info -->
                                    <div class="flex items-center gap-6 flex-wrap">
                                        <!-- Order ID with Icon -->
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Order ID</p>
                                                <p class="font-bold text-gray-900 text-lg">#<?= htmlspecialchars($order['id_order']) ?></p>
                                            </div>
                                        </div>

                                        <div class="hidden sm:block w-px h-12 bg-gray-200/80"></div>

                                        <!-- Date -->
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Tanggal</p>
                                                <p class="font-semibold text-gray-900"><?= formatTanggal($order['tanggal_order']) ?></p>
                                            </div>
                                        </div>

                                        <?php if (!empty($order['shipping_method']) && $order['shipping_method'] === 'pickup'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                Ambil di Toko
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Enhanced Status Badge -->
                                    <div class="flex items-center gap-3">
                                        <?php
                                        $statusLabels = [
                                            'pending' => ['label' => 'Menunggu Pembayaran', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'orange'],
                                            'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue'],
                                            'diproses' => ['label' => 'Diproses', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'color' => 'yellow'],
                                            'dikirim' => ['label' => 'Dikirim', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'indigo'],
                                            'selesai' => ['label' => 'Selesai', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
                                            'dibatalkan' => ['label' => 'Dibatalkan', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'red']
                                        ];
                                        $statusData = $statusLabels[$order['status_order']] ?? $statusLabels['pending'];
                                        ?>
                                        <span class="status-badge inline-flex items-center gap-2 px-4 py-2 bg-<?= $statusData['color'] ?>-100 text-<?= $statusData['color'] ?>-700 text-sm font-bold rounded-full shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $statusData['icon'] ?>" />
                                            </svg>
                                            <?= $statusData['label'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <!-- Product Items -->
                                <?php if (!empty($details)): ?>
                                    <div class="space-y-4 mb-6">
                                        <?php foreach ($details as $item): ?>
                                            <?php
                                            $imagePath = '../../uploads/products/' . ($item['foto_produk'] ?? '');
                                            $imageUrl = (!empty($item['foto_produk']) && file_exists($imagePath))
                                                ? $imagePath
                                                : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23CBD5E1'%3E%3Cpath d='M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E";
                                            $hargaAsli = floatval($item['harga_satuan'] ?? 0);
                                            $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
                                            $hargaFinal = floatval($item['harga_setelah_diskon'] ?? $hargaAsli);
                                            $jumlah = intval($item['jumlah'] ?? 1);
                                            $hasDiscount = $diskonSatuan > 0;
                                            ?>
                                            <div class="group flex flex-col md:flex-row gap-4 p-4 rounded-2xl bg-gray-50/50 hover:bg-gray-100/50 transition-all duration-300 <?= $order['status_order'] === 'dibatalkan' ? 'opacity-60' : '' ?>">
                                                <!-- Product Image -->
                                                <div class="product-image-wrapper w-full md:w-28 h-28 flex-shrink-0">
                                                    <img alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                                        src="<?= $imageUrl ?>"
                                                        class="object-cover w-full h-full rounded-xl bg-white shadow-sm group-hover:shadow-md transition-shadow">
                                                </div>

                                                <!-- Product Details -->
                                                <div class="flex-1 flex flex-col justify-between">
                                                    <div>
                                                        <h4 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#882426] transition-colors">
                                                            <?= htmlspecialchars($item['nama_product']) ?>
                                                        </h4>
                                                        <div class="flex items-center gap-3 flex-wrap">
                                                            <span class="info-pill px-3 py-1 rounded-full text-sm font-medium text-gray-600">
                                                                Qty: <?= $jumlah ?>
                                                            </span>
                                                            <?php if ($hasDiscount): ?>
                                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    Hemat <?= formatRupiah($diskonSatuan * $jumlah) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Price -->
                                                    <div class="flex items-baseline gap-2 mt-3">
                                                        <?php if ($hasDiscount): ?>
                                                            <span class="text-sm text-gray-400 line-through"><?= formatRupiah($hargaAsli) ?></span>
                                                        <?php endif; ?>
                                                        <span class="text-xl font-extrabold text-[#882426]"><?= formatRupiah($hasDiscount ? $hargaFinal : $hargaAsli) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-500 text-center py-4">Detail produk tidak tersedia</p>
                                <?php endif; ?>

                                <!-- Enhanced Status Alerts with Solid Colors -->
                                <?php if ($order['status_order'] === 'pending' && ($order['status_pembayaran'] ?? 'pending') === 'pending' && !$isExpired): ?>
                                    <div class="relative overflow-hidden rounded-2xl mb-6 alert-pending border p-5">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="relative flex items-start gap-4">
                                            <div class="flex-shrink-0 w-12 h-12 icon-bg-pending rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-orange-900 text-lg mb-1">Menunggu Pembayaran</h4>
                                                <p class="text-orange-700">Segera selesaikan pembayaran sebelum batas waktu berakhir.</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($order['status_order'] === 'dikirim'): ?>
                                    <div class="relative overflow-hidden rounded-2xl mb-6 alert-dikirim border p-5">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="relative flex items-start gap-4">
                                            <div class="flex-shrink-0 w-12 h-12 icon-bg-dikirim rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-indigo-900 text-lg mb-1">Paket Dalam Pengiriman</h4>
                                                <p class="text-indigo-700">
                                                    <?= htmlspecialchars($order['jasa_pengiriman'] ?? 'Kurir') ?>
                                                    <?php if (!empty($order['estimasi_hari'])): ?>
                                                        <span class="mx-2">•</span> Estimasi: <?= $order['estimasi_hari'] ?> hari
                                                    <?php endif; ?>
                                                </p>
                                                <?php if (!empty($order['no_resi'])): ?>
                                                    <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-white/80 rounded-lg text-sm">
                                                        <span class="text-gray-500">No. Resi:</span>
                                                        <span class="font-bold text-indigo-700"><?= htmlspecialchars($order['no_resi']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif (in_array($order['status_order'], ['dikonfirmasi', 'diproses'])): ?>
                                    <div class="relative overflow-hidden rounded-2xl mb-6 alert-diproses border p-5">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="relative flex items-start gap-4">
                                            <div class="flex-shrink-0 w-12 h-12 icon-bg-diproses rounded-2xl flex items-center justify-center shadow-lg shadow-yellow-500/20">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-yellow-900 text-lg mb-1">Pesanan Sedang Diproses</h4>
                                                <p class="text-yellow-700">Penjual sedang menyiapkan pesanan Anda.</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($order['status_order'] === 'dibatalkan'): ?>
                                    <div class="relative overflow-hidden rounded-2xl mb-6 alert-dibatalkan border p-5">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-red-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="relative flex items-start gap-4">
                                            <div class="flex-shrink-0 w-12 h-12 <?= $cancInfo['is_system'] ? 'icon-bg-pending' : 'icon-bg-dibatalkan' ?> rounded-2xl flex items-center justify-center shadow-lg">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <?php if ($cancInfo['is_system']): ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    <?php else: ?>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    <?php endif; ?>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-red-900 text-lg mb-2">Pesanan Dibatalkan</h4>
                                                <div class="space-y-1 text-sm">
                                                    <p class="text-red-700">
                                                        <span class="font-semibold">Oleh:</span> <?= htmlspecialchars($cancInfo['cancelled_by']) ?>
                                                    </p>
                                                    <p class="text-red-700">
                                                        <span class="font-semibold">Alasan:</span> <?= htmlspecialchars($cancInfo['reason']) ?>
                                                    </p>
                                                    <?php if ($cancInfo['datetime']): ?>
                                                        <p class="text-red-600 text-xs mt-1">
                                                            <?= htmlspecialchars($cancInfo['datetime']) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($order['status_order'] === 'selesai'): ?>
                                    <div class="relative overflow-hidden rounded-2xl mb-6 alert-selesai border p-5">
                                        <div class="absolute top-0 right-0 w-32 h-32 bg-green-200/20 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                        <div class="relative flex items-start gap-4">
                                            <div class="flex-shrink-0 w-12 h-12 icon-bg-selesai rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/20">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-green-900 text-lg mb-1">Pesanan Selesai</h4>
                                                <p class="text-green-700">Terima kasih telah berbelanja di Nano Komputer!</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $orderOriginalSubtotal = 0;
                                $orderSubtotalAfterDiscount = 0;
                                $orderProductDiscount = 0;

                                foreach ($details as $det) {
                                    $detHargaAsli = floatval($det['harga_satuan'] ?? 0);
                                    $detDiskonSatuan = floatval($det['diskon_satuan'] ?? 0);
                                    $detHargaFinal = floatval($det['harga_setelah_diskon'] ?? $detHargaAsli);
                                    $detJumlah = intval($det['jumlah'] ?? 1);

                                    $orderOriginalSubtotal += $detHargaAsli * $detJumlah;
                                    $orderSubtotalAfterDiscount += floatval($det['subtotal'] ?? ($detHargaFinal * $detJumlah));
                                    $orderProductDiscount += $detDiskonSatuan * $detJumlah;
                                }

                                $orderTaxRate = 0.11;
                                $orderTaxAmount = $orderSubtotalAfterDiscount * $orderTaxRate;
                                $orderShippingCost = floatval($order['total_ongkir'] ?? 0);
                                $orderPackingCost = floatval($order['biaya_packing'] ?? 0);
                                $orderTotalDiskon = floatval($order['total_diskon'] ?? 0);
                                $orderVoucherDiscount = max(0, $orderTotalDiskon - $orderProductDiscount);
                                ?>
                                <!-- Enhanced Payment Summary -->
                                <div class="mt-6 pt-6 border-t-2 border-gray-100 rounded-2xl bg-gray-50/50 p-5 space-y-3">
                                    <?php if ($orderProductDiscount > 0): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Subtotal Produk</span>
                                            <span class="text-gray-400 line-through"><?= formatRupiah($orderOriginalSubtotal) ?></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-green-600 font-medium flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5z" clip-rule="evenodd" />
                                                </svg>
                                                Diskon
                                            </span>
                                            <span class="text-green-600 font-semibold">-<?= formatRupiah($orderProductDiscount) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Subtotal</span>
                                        <span class="font-semibold text-gray-800"><?= formatRupiah($orderSubtotalAfterDiscount) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Pajak (11%)</span>
                                        <span class="font-semibold text-gray-800"><?= formatRupiah($orderTaxAmount) ?></span>
                                    </div>
                                    <?php if ($order['shipping_method'] === 'pickup'): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Ongkos Kirim</span>
                                            <span class="text-green-600 font-medium">Gratis</span>
                                        </div>
                                    <?php elseif ($orderShippingCost > 0): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Ongkos Kirim</span>
                                            <span class="font-semibold text-gray-800"><?= formatRupiah($orderShippingCost) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($orderPackingCost > 0): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-500">Biaya Pengemasan</span>
                                            <span class="font-semibold text-gray-800"><?= formatRupiah($orderPackingCost) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    $voucherUsage = $voucherUsageData[$order['id_order']] ?? null;
                                    $voucherDiscount = $voucherUsage ? floatval($voucherUsage['jumlah_diskon'] ?? 0) : 0;
                                    ?>

                                    <?php if ($voucherDiscount > 0 && $voucherUsage): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-green-600 font-medium flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" />
                                                </svg>
                                                Diskon Voucher
                                            </span>
                                            <span class="text-green-600 font-semibold">- <?= formatRupiah($voucherDiscount) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="pt-4 mt-4 border-t-2 border-dashed border-gray-200 flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                                        <span class="text-2xl font-extrabold <?= $order['status_order'] === 'dibatalkan' ? 'text-gray-400 line-through' : 'text-[#882426]' ?>">
                                            <?= formatRupiah($order['payment_total'] ?? $order['total_bayar']) ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Enhanced Action Buttons -->
                                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                                    <?php if ($order['status_order'] === 'pending' && ($order['status_pembayaran'] ?? 'pending') === 'pending' && !$isExpired): ?>
                                        <a href="processPayment.php?order_id=<?= urlencode($order['id_order']) ?>"
                                            class="flex-1 text-center action-btn-primary px-6 py-3.5 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Bayar Sekarang
                                        </a>
                                        <a href="detailOrder.php?id=<?= urlencode($order['id_order']) ?>" class="flex-1 text-center action-btn-secondary px-6 py-3.5 text-gray-700 font-semibold rounded-xl">
                                            Lihat Detail
                                        </a>
                                        <button type="button" onclick="cancelOrder('<?= htmlspecialchars($order['id_order']) ?>')"
                                            class="flex-1 px-6 py-3.5 bg-white border-2 border-red-200 text-red-600 font-semibold rounded-xl hover:bg-red-50 hover:border-red-300 transition-all">
                                            Batalkan
                                        </button>
                                    <?php elseif ($order['status_order'] === 'dikirim'): ?>
                                        <button type="button" onclick="confirmReceived('<?= $order['id_order'] ?>')"
                                            class="flex-1 action-btn-primary px-6 py-3.5 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Pesanan Diterima
                                        </button>
                                        <?php if (!empty($order['no_resi'])): ?>
                                            <button type="button" onclick="trackOrder('<?= htmlspecialchars($order['no_resi']) ?>', '<?= htmlspecialchars($order['jasa_pengiriman'] ?? '') ?>')"
                                                class="flex-1 action-btn-secondary px-6 py-3.5 text-gray-700 font-semibold rounded-xl flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                Lacak
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($order['status_order'] === 'selesai'): ?>
                                        <button type="button" onclick="buyAgain('<?= $order['id_order'] ?>')"
                                            class="flex-1 action-btn-primary px-6 py-3.5 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Beli Lagi
                                        </button>
                                        <a href="detailOrder.php?id=<?= urlencode($order['id_order']) ?>" class="flex-1 text-center action-btn-secondary px-6 py-3.5 text-gray-700 font-semibold rounded-xl">
                                            Lihat Detail
                                        </a>
                                        <a href="detailOrder.php?id=<?= urlencode($order['id_order']) ?>"
                                            class="flex-1 text-center px-6 py-3.5 bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 text-yellow-700 font-semibold rounded-xl hover:from-yellow-100 hover:to-amber-100 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                            </svg>
                                            Review
                                        </a>
                                    <?php elseif ($order['status_order'] === 'dibatalkan'): ?>
                                        <button type="button" onclick="buyAgain('<?= $order['id_order'] ?>')"
                                            class="flex-1 action-btn-primary px-6 py-3.5 text-white font-semibold rounded-xl flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Beli Lagi
                                        </button>
                                        <a href="detailOrder.php?id=<?= urlencode($order['id_order']) ?>" class="flex-1 text-center action-btn-secondary px-6 py-3.5 text-gray-700 font-semibold rounded-xl">
                                            Lihat Detail
                                        </a>
                                    <?php else: ?>
                                        <a href="detailOrder.php?id=<?= urlencode($order['id_order']) ?>" class="flex-1 text-center action-btn-secondary px-6 py-3.5 text-gray-700 font-semibold rounded-xl">
                                            Lihat Detail
                                        </a>
                                        <?php if (in_array($order['status_order'], ['dikonfirmasi', 'diproses'])): ?>
                                            <button type="button" onclick="cancelOrder('<?= $order['id_order'] ?>')"
                                                class="flex-1 px-6 py-3.5 bg-white border-2 border-red-200 text-red-600 font-semibold rounded-xl hover:bg-red-50 transition-all">
                                                Batalkan Pesanan
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?tab=<?= $currentTab ?>&page=<?= $page - 1 ?>"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Sebelumnya
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?tab=<?= $currentTab ?>&page=<?= $i ?>"
                                    class="px-4 py-2 rounded-lg transition-colors <?= $i === $page ? 'bg-primary text-white' : 'bg-white border border-gray-300 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?tab=<?= $currentTab ?>&page=<?= $page + 1 ?>"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Selanjutnya
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <div id="cancelOrderModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCancelModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <button onclick="closeCancelModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Batalkan Pesanan?</h3>
                    <p class="text-gray-500 text-sm">Pesanan <span id="cancelOrderId" class="font-semibold text-gray-700"></span> akan dibatalkan.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan pembatalan</label>
                    <select id="cancelReason" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="Berubah pikiran">Berubah pikiran</option>
                        <option value="Ingin mengubah pesanan">Ingin mengubah pesanan</option>
                        <option value="Menemukan harga lebih murah">Menemukan harga lebih murah</option>
                        <option value="Waktu pengiriman terlalu lama">Waktu pengiriman terlalu lama</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Kembali
                    </button>
                    <button type="button" onclick="confirmCancelOrder()" id="btnConfirmCancel"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                        <span>Ya, Batalkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmReceivedModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeConfirmReceivedModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <button onclick="closeConfirmReceivedModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Pesanan Diterima</h3>
                    <p class="text-gray-500 text-sm">Apakah Anda yakin pesanan sudah diterima dengan baik?</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeConfirmReceivedModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Belum
                    </button>
                    <button type="button" onclick="submitConfirmReceived()" id="btnConfirmReceived"
                        class="flex-1 px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <span>Ya, Sudah Diterima</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Buy Again Modal -->
    <div id="buyAgainModal" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop with blur effect -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity duration-300" onclick="closeBuyAgainModal()"></div>

        <!-- Modal Container with animation -->
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden transform transition-all duration-300 scale-100"
                style="animation: modalSlideIn 0.3s ease-out;">

                <!-- Header with Primary Solid Color -->
                <div class="relative bg-[#882426] px-6 py-5">
                    <!-- Header Content -->
                    <div class="relative flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-2xl backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold text-white">Tambah ke Keranjang</h3>
                            <p class="text-white/80 text-sm mt-0.5">Produk tersedia untuk ditambahkan kembali</p>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button onclick="closeBuyAgainModal()"
                        class="absolute top-4 right-4 p-2 bg-white/20 hover:bg-white/30 rounded-xl text-white transition-all duration-200 hover:rotate-90 backdrop-blur-sm group"
                        title="Tutup">
                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body with Scrollable Content -->
                <div class="overflow-y-auto max-h-[calc(85vh-180px)] p-6" id="buyAgainContentWrapper">
                    <div id="buyAgainContent" class="space-y-3">
                        <!-- Loading State -->
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="relative">
                                <div class="w-16 h-16 border-4 border-[#882426]/20 rounded-full"></div>
                                <div class="absolute top-0 left-0 w-16 h-16 border-4 border-[#882426] border-t-transparent rounded-full animate-spin"></div>
                            </div>
                            <p class="mt-4 text-gray-500 text-sm font-medium">Memuat produk...</p>
                        </div>
                    </div>
                </div>

                <!-- Footer with Action Buttons -->
                <div class="border-t border-gray-100 bg-gray-50/80 backdrop-blur-sm px-6 py-4">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeBuyAgainModal()"
                            class="flex-1 px-5 py-3 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 flex items-center justify-center gap-2 group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Tutup</span>
                        </button>
                        <button type="button" onclick="proceedBuyAgain()" id="btnProceedBuyAgain"
                            class="flex-1 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-[#882426]/25 hover:shadow-xl hover:shadow-[#882426]/30 hover:-translate-y-0.5 group">
                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Masukkan Keranjang</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script>
        let currentCancelOrderId = null;
        let currentReceivedOrderId = null;
        let currentBuyAgainOrderId = null;

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

        function cancelOrder(orderId) {
            currentCancelOrderId = orderId;
            document.getElementById('cancelOrderId').textContent = '#' + orderId;
            document.getElementById('cancelOrderModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('cancelOrderModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentCancelOrderId = null;
        }

        async function confirmCancelOrder() {
            if (!currentCancelOrderId) return;

            const btn = document.getElementById('btnConfirmCancel');
            const reason = document.getElementById('cancelReason').value;

            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('../../api/checkout/cancel-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: currentCancelOrderId,
                        reason: reason
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Pesanan berhasil dibatalkan', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Gagal membatalkan pesanan', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Ya, Batalkan</span>';
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span>Ya, Batalkan</span>';
            }
        }

        function confirmReceived(orderId) {
            currentReceivedOrderId = orderId;
            document.getElementById('confirmReceivedModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmReceivedModal() {
            document.getElementById('confirmReceivedModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentReceivedOrderId = null;
        }

        async function submitConfirmReceived() {
            if (!currentReceivedOrderId) return;

            const btn = document.getElementById('btnConfirmReceived');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('../../api/orders/confirm-received.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: currentReceivedOrderId
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Pesanan dikonfirmasi selesai', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Gagal mengkonfirmasi pesanan', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Ya, Sudah Diterima</span>';
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span>Ya, Sudah Diterima</span>';
            }
        }

        async function buyAgain(orderId) {
            currentBuyAgainOrderId = orderId;
            const modal = document.getElementById('buyAgainModal');
            const content = document.getElementById('buyAgainContent');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            content.innerHTML = '<div class="flex items-center justify-center py-8"><svg class="animate-spin h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            try {
                const response = await fetch('../../api/orders/buy-again.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        action: 'preview'
                    })
                });
                const result = await response.json();

                if (result.success || result.available_items.length > 0) {
                    renderBuyAgainModal(result);
                } else if (result.has_unavailable && result.available_items.length === 0) {
                    renderUnavailableModal(result.unavailable_items);
                } else {
                    content.innerHTML = '<div class="text-center py-8"><p class="text-gray-600">Gagal memuat data pesanan</p></div>';
                }
            } catch (error) {
                content.innerHTML = '<div class="text-center py-8"><p class="text-red-600">Terjadi kesalahan: ' + error.message + '</p></div>';
            }
        }

        function renderBuyAgainModal(data) {
            const content = document.getElementById('buyAgainContent');
            let html = '';

            if (data.available_items.length > 0) {
                // Available products header
                html += `
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">${data.available_items.length} Produk Tersedia</span>
                    </div>
                `;

                html += '<div class="space-y-3">';
                data.available_items.forEach((item, index) => {
                    html += `
                        <div class="group relative flex gap-4 p-4 bg-white border-2 border-gray-100 rounded-2xl hover:border-[#882426]/20 hover:shadow-lg hover:shadow-[#882426]/5 transition-all duration-300" style="animation: fadeInUp 0.3s ease-out ${index * 0.1}s both;">
                            <!-- Product Image -->
                            <div class="relative flex-shrink-0">
                                ${item.gambar 
                                    ? `<img src="../../uploads/products/${item.gambar}" alt="${item.nama_product}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl ring-2 ring-gray-100 group-hover:ring-[#882426]/20 transition-all">` 
                                    : `<div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl flex items-center justify-center ring-2 ring-gray-100">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>`
                                }
                                <!-- Stock Badge -->
                                <div class="absolute -top-2 -right-2 px-2 py-0.5 bg-green-500 text-white text-xs font-bold rounded-full shadow-sm">
                                    Tersedia
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm sm:text-base line-clamp-2 group-hover:text-[#882426] transition-colors">${item.nama_product}</h4>
                                
                                <div class="mt-2 space-y-1.5">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="flex items-center gap-1.5 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                            Qty sebelumnya:
                                        </span>
                                        <span class="font-semibold text-gray-800">${item.jumlah_sebelumnya}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="flex items-center gap-1.5 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                            Stok tersedia:
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 font-bold rounded-md text-xs">
                                            ${item.stok_tersedia} unit
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';

                // Unavailable products section
                if (data.unavailable_items.length > 0) {
                    html += `
                        <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-200">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">${data.unavailable_items.length} Produk Tidak Tersedia</span>
                            </div>
                            <div class="space-y-2">
                    `;
                    data.unavailable_items.forEach(item => {
                        html += `
                            <div class="flex items-start gap-3 p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-100">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 line-clamp-1">${item.nama_product}</p>
                                    <p class="text-xs text-red-600 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        ${item.alasan}
                                    </p>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div></div>';
                }
            } else {
                // Empty state
                html = `
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Produk Tidak Tersedia</h4>
                        <p class="text-gray-500 text-sm max-w-xs mx-auto">Maaf, semua produk dari pesanan ini sudah tidak tersedia untuk dibeli kembali.</p>
                    </div>
                `;
            }

            // Add animation keyframes
            html += `
                <style>
                    @keyframes fadeInUp {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
            `;

            content.innerHTML = html;
        }

        function renderUnavailableModal(unavailableItems) {
            const modal = document.getElementById('buyAgainModal');
            const content = document.getElementById('buyAgainContent');
            const btnProceed = document.getElementById('btnProceedBuyAgain');

            let html = `
                <div class="text-center py-6 mb-4">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 mb-1">Produk Tidak Tersedia</h4>
                    <p class="text-gray-500 text-sm">Beberapa produk sudah tidak dapat dibeli</p>
                </div>
            `;

            html += '<div class="space-y-3">';
            unavailableItems.forEach((item, index) => {
                html += `
                    <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-100" style="animation: fadeInUp 0.3s ease-out ${index * 0.1}s both;">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 line-clamp-2">${item.nama_product}</p>
                            <p class="text-sm text-red-600 mt-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                ${item.alasan}
                            </p>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            html += `
                <style>
                    @keyframes fadeInUp {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
            `;

            content.innerHTML = html;
            btnProceed.style.display = 'none';
        }

        function closeBuyAgainModal() {
            document.getElementById('buyAgainModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentBuyAgainOrderId = null;
        }

        async function proceedBuyAgain() {
            if (!currentBuyAgainOrderId) return;

            const btn = document.getElementById('btnProceedBuyAgain');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch('../../api/orders/buy-again.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: currentBuyAgainOrderId,
                        action: 'add'
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showToast(result.message || 'Produk berhasil ditambahkan ke keranjang', 'success');

                    // Update cart count if available
                    if (result.cart_count !== undefined) {
                        const cartCountElements = document.querySelectorAll('.cart-count-badge');
                        cartCountElements.forEach(el => {
                            el.textContent = result.cart_count;
                            el.classList.remove('hidden');
                            el.style.transform = 'scale(1.2)';
                            setTimeout(() => {
                                el.style.transform = 'scale(1)';
                            }, 300);
                        });
                    }

                    closeBuyAgainModal();
                    setTimeout(() => window.location.href = 'cart.php', 1500);
                } else {
                    showToast(result.message || 'Gagal menambahkan produk', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Masukkan Keranjang</span>';
                }
            } catch (error) {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span>Masukkan Keranjang</span>';
            }
        }

        function trackOrder(noResi, courier) {
            showToast('Fitur lacak pesanan akan segera tersedia', 'info');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCancelModal();
                closeConfirmReceivedModal();
                closeBuyAgainModal();
            }
        });
    </script>
</body>

</html>