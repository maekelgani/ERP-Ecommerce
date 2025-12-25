<?php
$pageTitle = "Notifikasi";
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

\App\Auth\CustomerAuthMiddleware::requireLogin('notifications.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Notifikasi', 'url' => null]
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
    $jam = date('H:i', $timestamp);
    return $hari . ' ' . $bulan[$bulanNum] . ' ' . $tahun . ', ' . $jam;
}

function formatTimeAgo($datetime)
{
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}

function getNotificationIcon($type)
{
    $icons = [
        'order' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        'payment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        'shipment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7h4a1 1 0 011 1v7a1 1 0 01-1 1h-.05a2.5 2.5 0 00-4.9 0H12V8a1 1 0 011-1z"/>',
        'promo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
        'system' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    ];
    return $icons[$type] ?? $icons['system'];
}

function getNotificationColor($type)
{
    $colors = [
        'order' => 'bg-blue-100 text-blue-600',
        'payment' => 'bg-green-100 text-green-600',
        'shipment' => 'bg-indigo-100 text-indigo-600',
        'promo' => 'bg-orange-100 text-orange-600',
        'system' => 'bg-gray-100 text-gray-600'
    ];
    return $colors[$type] ?? $colors['system'];
}

function getNotificationTypeLabel($type)
{
    $labels = [
        'order' => 'Pesanan',
        'payment' => 'Pembayaran',
        'shipment' => 'Pengiriman',
        'promo' => 'Promo',
        'system' => 'Sistem'
    ];
    return $labels[$type] ?? 'Lainnya';
}

$currentTab = $_GET['tab'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$notifications = [];
$totalNotifications = 0;
$totalPages = 0;
$unreadCount = 0;

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $typeFilter = '';
    $params = [':customer_id' => $customerId];

    if ($currentTab !== 'all') {
        $typeFilter = " AND tipe_notifikasi = :type";
        $params[':type'] = $currentTab;
    }

    $countQuery = $db->prepare("SELECT COUNT(*) as total FROM notification WHERE id_customer = :customer_id" . $typeFilter);
    $countQuery->execute($params);
    $totalNotifications = (int) $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = $totalNotifications > 0 ? ceil($totalNotifications / $perPage) : 0;

    $unreadQuery = $db->prepare("SELECT COUNT(*) as count FROM notification WHERE id_customer = :customer_id AND status_baca = 'belum_dibaca'");
    $unreadQuery->execute([':customer_id' => $customerId]);
    $unreadCount = (int) $unreadQuery->fetch(PDO::FETCH_ASSOC)['count'];

    $notifQuery = $db->prepare("
        SELECT * FROM notification 
        WHERE id_customer = :customer_id {$typeFilter}
        ORDER BY tanggal_dikirim DESC
        LIMIT {$perPage} OFFSET {$offset}
    ");
    $notifQuery->execute($params);
    $notifications = $notifQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Notifications Error: ' . $e->getMessage());
}

include '../../components/users/head.php';
?>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]">
        <div class="mt-6 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifikasi</h1>
                    <p class="text-gray-600">
                        <?php if ($unreadCount > 0): ?>
                            Anda memiliki <span class="font-semibold text-primary"><?= $unreadCount ?></span> notifikasi belum dibaca
                        <?php else: ?>
                            Semua notifikasi sudah dibaca
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <button type="button" onclick="markAllAsRead()"
                        class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tandai Semua Dibaca
                    </button>
                <?php endif; ?>
            </div>

            <div class="flex flex-wrap gap-2 mb-6">
                <a href="?tab=all"
                    class="px-4 py-2 rounded-lg font-medium transition-all <?= $currentTab === 'all' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-primary hover:text-white' ?>">
                    Semua
                </a>
                <a href="?tab=order"
                    class="px-4 py-2 rounded-lg font-medium transition-all <?= $currentTab === 'order' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-primary hover:text-white' ?>">
                    Pesanan
                </a>
                <a href="?tab=payment"
                    class="px-4 py-2 rounded-lg font-medium transition-all <?= $currentTab === 'payment' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-primary hover:text-white' ?>">
                    Pembayaran
                </a>
                <a href="?tab=shipment"
                    class="px-4 py-2 rounded-lg font-medium transition-all <?= $currentTab === 'shipment' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-primary hover:text-white' ?>">
                    Pengiriman
                </a>
                <a href="?tab=promo"
                    class="px-4 py-2 rounded-lg font-medium transition-all <?= $currentTab === 'promo' ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-primary hover:text-white' ?>">
                    Promo
                </a>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-500">Belum ada notifikasi untuk ditampilkan.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notification-item relative <?= $notif['status_baca'] === 'belum_dibaca' ? 'bg-primary/5' : '' ?>"
                                data-id="<?= $notif['id_notifikasi'] ?>">
                                <a href="<?= !empty($notif['id_order']) ? 'detailOrder.php?id=' . urlencode($notif['id_order']) : '#' ?>"
                                    onclick="markAsRead('<?= $notif['id_notifikasi'] ?>')"
                                    class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full <?= getNotificationColor($notif['tipe_notifikasi']) ?> flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?= getNotificationIcon($notif['tipe_notifikasi']) ?>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="px-2 py-0.5 text-[10px] font-medium <?= getNotificationColor($notif['tipe_notifikasi']) ?> rounded">
                                                        <?= getNotificationTypeLabel($notif['tipe_notifikasi']) ?>
                                                    </span>
                                                    <?php if ($notif['status_baca'] === 'belum_dibaca'): ?>
                                                        <span class="w-2 h-2 bg-primary rounded-full"></span>
                                                    <?php endif; ?>
                                                </div>
                                                <h3 class="text-base font-semibold text-gray-900 mb-1"><?= htmlspecialchars($notif['judul_pesan']) ?></h3>
                                                <p class="text-sm text-gray-600 line-clamp-2"><?= htmlspecialchars($notif['isi_pesan']) ?></p>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                <p class="text-xs text-gray-400"><?= formatTimeAgo($notif['tanggal_dikirim']) ?></p>
                                                <p class="text-[10px] text-gray-300 mt-1"><?= formatTanggal($notif['tanggal_dikirim']) ?></p>
                                            </div>
                                        </div>
                                        <?php if (!empty($notif['id_order'])): ?>
                                            <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                                <span>Order:</span>
                                                <span class="font-mono font-medium text-primary">#<?= htmlspecialchars($notif['id_order']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
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

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script>
        function showToast(message, type = 'success') {
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

        async function markAsRead(notificationId) {
            try {
                await fetch('../../api/notifications/mark-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        notification_id: notificationId
                    })
                });
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        async function markAllAsRead() {
            try {
                const response = await fetch('../../api/notifications/mark-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mark_all: true
                    })
                });
                const result = await response.json();

                if (result.success) {
                    showToast('Semua notifikasi ditandai dibaca');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Gagal menandai notifikasi', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        }
    </script>
</body>

</html>