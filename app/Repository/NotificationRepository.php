<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class NotificationRepository
{
    private \PDO $db;
    private const TABLE_NAME = 'notification';

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Dapatkan notifikasi untuk admin dashboard
     * Notifikasi admin berasal dari aktivitas pelanggan dan pesanan
     */
    public function getAdminNotifications(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT 
                    n.id_notifikasi,
                    n.id_order,
                    n.tipe_notifikasi,
                    n.judul_pesan,
                    n.isi_pesan,
                    n.status_baca,
                    n.tanggal_dikirim,
                    n.id_customer,
                    c.nama_lengkap as nama_customer,
                    c.email as email_customer
                FROM " . self::TABLE_NAME . " n
                LEFT JOIN customers c ON n.id_customer = c.id_customer
                ORDER BY n.tanggal_dikirim DESC
                LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('NotificationRepository::getAdminNotifications - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung notifikasi yang belum dibaca
     */
    public function getUnreadCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM " . self::TABLE_NAME . " 
                WHERE status_baca = 'belum_dibaca'";
        
        try {
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log('NotificationRepository::getUnreadCount - ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead(string $notificationId): bool
    {
        $sql = "UPDATE " . self::TABLE_NAME . " 
                SET status_baca = 'dibaca'
                WHERE id_notifikasi = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $notificationId]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('NotificationRepository::markAsRead - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead(): bool
    {
        $sql = "UPDATE " . self::TABLE_NAME . " 
                SET status_baca = 'dibaca'
                WHERE status_baca = 'belum_dibaca'";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('NotificationRepository::markAllAsRead - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus notifikasi
     */
    public function deleteNotification(string $notificationId): bool
    {
        $sql = "DELETE FROM " . self::TABLE_NAME . " 
                WHERE id_notifikasi = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $notificationId]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('NotificationRepository::deleteNotification - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buat notifikasi baru
     */
    public function createNotification(array $data): bool
    {
        $sql = "INSERT INTO " . self::TABLE_NAME . " 
                (id_notifikasi, id_customer, id_order, tipe_notifikasi, judul_pesan, isi_pesan, status_baca, tanggal_dikirim)
                VALUES (:id, :id_customer, :id_order, :tipe, :judul, :isi, :status, NOW())";
        
        try {
            // Generate UUID v4 if not provided
            $id = $data['id_notifikasi'] ?? $this->generateUUID();
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':id_customer' => $data['id_customer'],
                ':id_order' => $data['id_order'] ?? null,
                ':tipe' => $data['tipe_notifikasi'] ?? 'system',
                ':judul' => $data['judul_pesan'],
                ':isi' => $data['isi_pesan'],
                ':status' => $data['status_baca'] ?? 'belum_dibaca'
            ]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('NotificationRepository::createNotification - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Dapatkan notifikasi berdasarkan tipe untuk statistik admin
     */
    public function getNotificationsByType(): array
    {
        $sql = "SELECT 
                    tipe_notifikasi,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_baca = 'belum_dibaca' THEN 1 ELSE 0 END) as unread
                FROM " . self::TABLE_NAME . "
                GROUP BY tipe_notifikasi";
        
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('NotificationRepository::getNotificationsByType - ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
