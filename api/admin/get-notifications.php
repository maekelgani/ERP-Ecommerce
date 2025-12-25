<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

use App\Auth\SessionManager;
use App\Repository\NotificationRepository;

// Validasi admin login
$currentAdmin = SessionManager::getCurrentAdmin();
if (!$currentAdmin) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $notificationRepo = new NotificationRepository();
    
    // Get parameters
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 20) : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $action = $_GET['action'] ?? null;

    // Handle different actions
    if ($action === 'mark-as-read' && isset($_POST['notification_id'])) {
        // Mark single notification as read
        $notificationId = $_POST['notification_id'];
        $result = $notificationRepo->markAsRead($notificationId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notifikasi ditandai sebagai dibaca' : 'Gagal menandai notifikasi',
            'unread_count' => $notificationRepo->getUnreadCount()
        ]);
    } else if ($action === 'mark-all-as-read') {
        // Mark all notifications as read
        $result = $notificationRepo->markAllAsRead();
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Semua notifikasi ditandai sebagai dibaca' : 'Gagal menandai notifikasi',
            'unread_count' => 0
        ]);
    } else if ($action === 'delete' && isset($_POST['notification_id'])) {
        // Delete notification
        $notificationId = $_POST['notification_id'];
        $result = $notificationRepo->deleteNotification($notificationId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notifikasi dihapus' : 'Gagal menghapus notifikasi'
        ]);
    } else {
        // Get notifications list (default action)
        $notifications = $notificationRepo->getAdminNotifications($limit, $offset);
        $unreadCount = $notificationRepo->getUnreadCount();

        // Format notifications with relative time
        $formattedNotifications = array_map(function ($notif) {
            return [
                'id_notifikasi' => $notif['id_notifikasi'],
                'id_order' => $notif['id_order'],
                'tipe_notifikasi' => $notif['tipe_notifikasi'],
                'judul_pesan' => htmlspecialchars((string)$notif['judul_pesan']),
                'isi_pesan' => htmlspecialchars((string)$notif['isi_pesan']),
                'status_baca' => $notif['status_baca'],
                'tanggal_dikirim' => $notif['tanggal_dikirim'],
                'waktu_relatif' => getRelativeTime($notif['tanggal_dikirim']),
                'nama_customer' => htmlspecialchars((string)($notif['nama_customer'] ?? 'Customer')),
                'icon' => getNotificationIcon($notif['tipe_notifikasi']),
                'color' => getNotificationColor($notif['tipe_notifikasi'])
            ];
        }, $notifications);

        echo json_encode([
            'success' => true,
            'data' => $formattedNotifications,
            'unread_count' => $unreadCount,
            'total_count' => count($formattedNotifications)
        ]);
    }
} catch (\Exception $e) {
    error_log('API get-notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server'
    ]);
}

/**
 * Hitung waktu relatif dari timestamp
 */
function getRelativeTime($timestamp): string
{
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $mins = (int)round($diff / 60);
        return $mins === 1 ? '1 menit lalu' : $mins . ' menit lalu';
    } elseif ($diff < 86400) {
        $hours = (int)round($diff / 3600);
        return $hours === 1 ? '1 jam lalu' : $hours . ' jam lalu';
    } elseif ($diff < 604800) {
        $days = (int)round($diff / 86400);
        return $days === 1 ? '1 hari lalu' : $days . ' hari lalu';
    } else {
        return date('d M Y H:i', $time);
    }
}

/**
 * Dapatkan icon untuk tipe notifikasi
 */
function getNotificationIcon($type): string
{
    $icons = [
        'order' => 'shopping_cart',
        'payment' => 'payment',
        'shipment' => 'local_shipping',
        'promo' => 'local_offer',
        'system' => 'info'
    ];
    return $icons[$type] ?? 'notifications';
}

/**
 * Dapatkan warna untuk tipe notifikasi
 */
function getNotificationColor($type): string
{
    $colors = [
        'order' => 'orange',
        'payment' => 'green',
        'shipment' => 'blue',
        'promo' => 'purple',
        'system' => 'gray'
    ];
    return $colors[$type] ?? 'gray';
}
