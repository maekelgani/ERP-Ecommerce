<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class CustomerActivityRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function tableExists(string $tableName): bool
    {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['table' => $tableName]);
        return $stmt->fetch() !== false;
    }

    public function getCustomerActivities(int $customerId, int $limit = 50): array
    {
        $activities = [];

        $stmt = $this->db->prepare("
            SELECT 
                o.id_order,
                o.tanggal_order as activity_date,
                o.status_order,
                o.total_bayar,
                'order' as activity_type,
                CONCAT('Membuat pesanan #', o.id_order, ' senilai Rp ', FORMAT(o.total_bayar, 0, 'id_ID')) as description
            FROM orders o
            WHERE o.id_customer = :id
            ORDER BY o.tanggal_order DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':id', $customerId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();
        
        foreach ($orders as $order) {
            $activities[] = [
                'type' => 'order',
                'date' => $order['activity_date'],
                'description' => $order['description'],
                'details' => $order
            ];
        }

        $stmt = $this->db->prepare("
            SELECT 
                r.id_review,
                r.tanggal_review as activity_date,
                r.rating,
                r.komentar,
                p.nama_product,
                'review' as activity_type,
                CONCAT('Memberikan review ', r.rating, ' bintang untuk ', p.nama_product) as description
            FROM review r
            JOIN products p ON r.id_product = p.id_product
            WHERE r.id_customer = :id
            ORDER BY r.tanggal_review DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':id', $customerId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $reviews = $stmt->fetchAll();
        
        foreach ($reviews as $review) {
            $activities[] = [
                'type' => 'review',
                'date' => $review['activity_date'],
                'description' => $review['description'],
                'details' => $review
            ];
        }

        $stmt = $this->db->prepare("
            SELECT 
                pv.id_penggunaan,
                pv.digunakan_pada as activity_date,
                pv.jumlah_diskon,
                v.kode,
                v.judul,
                'voucher' as activity_type,
                CONCAT('Menggunakan voucher ', v.kode) as description
            FROM penggunaan_voucher pv
            JOIN voucher v ON pv.id_voucher = v.id_voucher
            WHERE pv.id_pengguna = :id
            ORDER BY pv.digunakan_pada DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':id', $customerId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $vouchers = $stmt->fetchAll();
        
        foreach ($vouchers as $voucher) {
            $activities[] = [
                'type' => 'voucher',
                'date' => $voucher['activity_date'],
                'description' => $voucher['description'],
                'details' => $voucher
            ];
        }

        if ($this->tableExists('customer_activity')) {
            $stmt = $this->db->prepare("
                SELECT 
                    id_activity,
                    activity_type as type,
                    activity_description as description,
                    created_at as activity_date,
                    ip_address
                FROM customer_activity
                WHERE id_customer = :id
                ORDER BY created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':id', $customerId, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $loggedActivities = $stmt->fetchAll();
            
            foreach ($loggedActivities as $activity) {
                $activities[] = [
                    'type' => $activity['type'],
                    'date' => $activity['activity_date'],
                    'description' => $activity['description'],
                    'details' => $activity
                ];
            }
        }

        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, $limit);
    }

    public function getAllActivities(array $filters = []): array
    {
        $activities = [];
        $limit = $filters['limit'] ?? 100;

        $sql = "
            SELECT 
                o.id_order,
                o.tanggal_order as activity_date,
                o.status_order,
                o.total_bayar,
                'order' as activity_type,
                c.id_customer,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                CONCAT('Membuat pesanan #', o.id_order) as description
            FROM orders o
            JOIN customers c ON o.id_customer = c.id_customer
        ";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['customer_id'])) {
            $where[] = "o.id_customer = :customer_id";
            $params['customer_id'] = (int) $filters['customer_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(o.tanggal_order) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(o.tanggal_order) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY o.tanggal_order DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        foreach ($orders as $order) {
            $activities[] = [
                'type' => 'order',
                'date' => $order['activity_date'],
                'customer_id' => $order['id_customer'],
                'customer_name' => $order['customer_name'],
                'customer_email' => $order['customer_email'],
                'description' => $order['description'],
                'details' => $order
            ];
        }

        $sql = "
            SELECT 
                r.id_review,
                r.tanggal_review as activity_date,
                r.rating,
                'review' as activity_type,
                c.id_customer,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                p.nama_product,
                CONCAT('Review ', r.rating, ' bintang untuk ', p.nama_product) as description
            FROM review r
            JOIN customers c ON r.id_customer = c.id_customer
            JOIN products p ON r.id_product = p.id_product
        ";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['customer_id'])) {
            $where[] = "r.id_customer = :customer_id";
            $params['customer_id'] = (int) $filters['customer_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(r.tanggal_review) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(r.tanggal_review) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY r.tanggal_review DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $reviews = $stmt->fetchAll();
        
        foreach ($reviews as $review) {
            $activities[] = [
                'type' => 'review',
                'date' => $review['activity_date'],
                'customer_id' => $review['id_customer'],
                'customer_name' => $review['customer_name'],
                'customer_email' => $review['customer_email'],
                'description' => $review['description'],
                'details' => $review
            ];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, $limit);
    }

    public function getActivityStats(): array
    {
        $today = date('Y-m-d');
        $thisWeek = date('Y-m-d', strtotime('-7 days'));
        $thisMonth = date('Y-m-d', strtotime('-30 days'));

        $orderStats = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN DATE(tanggal_order) = '$today' THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN DATE(tanggal_order) >= '$thisWeek' THEN 1 ELSE 0 END) as this_week,
                SUM(CASE WHEN DATE(tanggal_order) >= '$thisMonth' THEN 1 ELSE 0 END) as this_month
            FROM orders
        ")->fetch();

        $reviewStats = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN DATE(tanggal_review) = '$today' THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN DATE(tanggal_review) >= '$thisWeek' THEN 1 ELSE 0 END) as this_week,
                SUM(CASE WHEN DATE(tanggal_review) >= '$thisMonth' THEN 1 ELSE 0 END) as this_month
            FROM review
        ")->fetch();

        return [
            'orders' => $orderStats,
            'reviews' => $reviewStats,
            'total_today' => ($orderStats['today'] ?? 0) + ($reviewStats['today'] ?? 0),
            'total_this_week' => ($orderStats['this_week'] ?? 0) + ($reviewStats['this_week'] ?? 0),
            'total_this_month' => ($orderStats['this_month'] ?? 0) + ($reviewStats['this_month'] ?? 0)
        ];
    }

    public function logActivity(int $customerId, string $type, string $description, ?string $ipAddress = null, ?string $userAgent = null): bool
    {
        if (!$this->tableExists('customer_activity')) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO customer_activity (id_customer, activity_type, activity_description, ip_address, user_agent)
                VALUES (:customer_id, :type, :description, :ip, :ua)
            ");
            return $stmt->execute([
                'customer_id' => $customerId,
                'type' => $type,
                'description' => $description,
                'ip' => $ipAddress,
                'ua' => $userAgent
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getRecentLoginActivity(int $limit = 20): array
    {
        if (!$this->tableExists('customer_activity')) {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT 
                ca.*,
                c.nama_lengkap as customer_name,
                c.email as customer_email
            FROM customer_activity ca
            JOIN customers c ON ca.id_customer = c.id_customer
            WHERE ca.activity_type = 'login'
            ORDER BY ca.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
