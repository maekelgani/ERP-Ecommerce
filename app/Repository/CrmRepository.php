<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class CrmRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getTotalCustomers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM customers");
        return (int) $stmt->fetch()['total'];
    }

    public function getActiveCustomers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM customers WHERE is_active = 1");
        return (int) $stmt->fetch()['total'];
    }

    public function getInactiveCustomers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM customers WHERE is_active = 0");
        return (int) $stmt->fetch()['total'];
    }

    public function getNewCustomersThisMonth(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total FROM customers 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        return (int) $stmt->fetch()['total'];
    }

    public function getMostActiveCustomers(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.id_customer,
                c.nama_lengkap,
                c.email,
                c.profile_image,
                COUNT(o.id_order) as total_orders,
                COALESCE(SUM(o.total_bayar), 0) as total_spending,
                MAX(o.tanggal_order) as last_order_date
            FROM customers c
            LEFT JOIN orders o ON c.id_customer = o.id_customer AND o.status_order != 'dibatalkan'
            WHERE c.is_active = 1
            GROUP BY c.id_customer, c.nama_lengkap, c.email, c.profile_image
            HAVING total_orders > 0
            ORDER BY total_orders DESC, total_spending DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentReviews(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id_review,
                r.rating,
                r.komentar,
                r.tanggal_review,
                r.status_review,
                r.foto_review,
                c.id_customer,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                p.id_product,
                p.nama_product,
                p.gambar as product_image
            FROM review r
            JOIN customers c ON r.id_customer = c.id_customer
            JOIN products p ON r.id_product = p.id_product
            ORDER BY r.tanggal_review DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductRatings(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.id_product,
                p.nama_product,
                p.gambar,
                COUNT(r.id_review) as total_reviews,
                COALESCE(AVG(r.rating), 0) as avg_rating
            FROM products p
            LEFT JOIN review r ON p.id_product = r.id_product AND r.status_review = 'approved'
            GROUP BY p.id_product, p.nama_product, p.gambar
            HAVING total_reviews > 0
            ORDER BY avg_rating DESC, total_reviews DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAverageRating(): float
    {
        $stmt = $this->db->query("
            SELECT COALESCE(AVG(rating), 0) as avg_rating 
            FROM review 
            WHERE status_review = 'approved'
        ");
        return (float) $stmt->fetch()['avg_rating'];
    }

    public function getTotalReviews(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM review");
        return (int) $stmt->fetch()['total'];
    }

    public function getPendingReviews(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM review WHERE status_review = 'pending'");
        return (int) $stmt->fetch()['total'];
    }

    public function getCustomerFeedback(int $limit = 10): array
    {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_feedback'";
        $stmt = $this->db->query($sql);
        if ($stmt->fetch()) {
            $stmt = $this->db->prepare("
                SELECT 
                    cf.*,
                    c.nama_lengkap as customer_name,
                    c.email as customer_email
                FROM customer_feedback cf
                JOIN customers c ON cf.id_customer = c.id_customer
                ORDER BY cf.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        return [];
    }

    public function getCustomerStats(): array
    {
        return [
            'total_customers' => $this->getTotalCustomers(),
            'active_customers' => $this->getActiveCustomers(),
            'inactive_customers' => $this->getInactiveCustomers(),
            'new_this_month' => $this->getNewCustomersThisMonth(),
            'total_reviews' => $this->getTotalReviews(),
            'pending_reviews' => $this->getPendingReviews(),
            'average_rating' => round($this->getAverageRating(), 1)
        ];
    }

    public function getAllCustomers(array $filters = []): array
    {
        $sql = "
            SELECT 
                c.*,
                COUNT(DISTINCT o.id_order) as total_orders,
                COALESCE(SUM(CASE WHEN o.status_order != 'dibatalkan' THEN o.total_bayar ELSE 0 END), 0) as total_spending,
                MAX(o.tanggal_order) as last_order_date
            FROM customers c
            LEFT JOIN orders o ON c.id_customer = o.id_customer
        ";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(c.nama_lengkap) LIKE :search_nama OR LOWER(c.email) LIKE :search_email OR c.no_telp LIKE :search_telp)";
            $params['search_nama'] = $search;
            $params['search_email'] = $search;
            $params['search_telp'] = $search;
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = "c.is_active = :is_active";
            $params['is_active'] = (int) $filters['is_active'];
        }
        
        if (!empty($filters['login_type'])) {
            $where[] = "c.login_type = :login_type";
            $params['login_type'] = $filters['login_type'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " GROUP BY c.id_customer ORDER BY c.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . (int)$filters['offset'];
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countCustomers(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM customers c";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(c.nama_lengkap) LIKE :search_nama OR LOWER(c.email) LIKE :search_email OR c.no_telp LIKE :search_telp)";
            $params['search_nama'] = $search;
            $params['search_email'] = $search;
            $params['search_telp'] = $search;
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = "c.is_active = :is_active";
            $params['is_active'] = (int) $filters['is_active'];
        }
        
        if (!empty($filters['login_type'])) {
            $where[] = "c.login_type = :login_type";
            $params['login_type'] = $filters['login_type'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function getCustomerById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.*,
                COUNT(DISTINCT o.id_order) as total_orders,
                COALESCE(SUM(CASE WHEN o.status_order != 'dibatalkan' THEN o.total_bayar ELSE 0 END), 0) as total_spending
            FROM customers c
            LEFT JOIN orders o ON c.id_customer = o.id_customer
            WHERE c.id_customer = :id
            GROUP BY c.id_customer
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getCustomerAddresses(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM address_book 
            WHERE id_customer = :id
            ORDER BY default_alamat DESC, id_alamat ASC
        ");
        $stmt->execute(['id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function getCustomerOrders(int $customerId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.*,
                COUNT(od.id_detail) as total_items,
                p.status_pembayaran
            FROM orders o
            LEFT JOIN order_detail od ON o.id_order = od.id_order
            LEFT JOIN payment p ON o.id_order = p.id_order
            WHERE o.id_customer = :id
            GROUP BY o.id_order
            ORDER BY o.tanggal_order DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':id', $customerId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCustomerReviews(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                p.nama_product,
                p.gambar as product_image
            FROM review r
            JOIN products p ON r.id_product = p.id_product
            WHERE r.id_customer = :id
            ORDER BY r.tanggal_review DESC
        ");
        $stmt->execute(['id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function getCustomerVoucherUsage(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                pv.*,
                v.kode,
                v.judul as voucher_title,
                v.jenis as voucher_type
            FROM penggunaan_voucher pv
            JOIN voucher v ON pv.id_voucher = v.id_voucher
            WHERE pv.id_pengguna = :id
            ORDER BY pv.digunakan_pada DESC
        ");
        $stmt->execute(['id' => $customerId]);
        return $stmt->fetchAll();
    }

    public function updateCustomerStatus(int $customerId, bool $isActive): bool
    {
        $stmt = $this->db->prepare("
            UPDATE customers SET is_active = :is_active, updated_at = NOW() 
            WHERE id_customer = :id
        ");
        return $stmt->execute([
            'is_active' => $isActive ? 1 : 0,
            'id' => $customerId
        ]);
    }

    public function createCustomer(array $data): array
    {
        if (empty($data['nama_lengkap']) || empty($data['email'])) {
            return ['success' => false, 'message' => 'Nama dan email wajib diisi'];
        }

        $checkStmt = $this->db->prepare("SELECT id_customer FROM customers WHERE email = :email");
        $checkStmt->execute(['email' => $data['email']]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO customers (nama_lengkap, email, no_telp, password_hash, login_type, is_active)
                VALUES (:nama, :email, :telp, :password, 'regular', :is_active)
            ");
            
            $password = !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
            
            $success = $stmt->execute([
                'nama' => trim($data['nama_lengkap']),
                'email' => trim($data['email']),
                'telp' => $data['no_telp'] ?? null,
                'password' => $password,
                'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Pelanggan berhasil ditambahkan',
                    'id' => $this->db->lastInsertId()
                ];
            }
            return ['success' => false, 'message' => 'Gagal menambahkan pelanggan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateCustomer(int $id, array $data): array
    {
        $existing = $this->getCustomerById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Pelanggan tidak ditemukan'];
        }

        if (!empty($data['email']) && $data['email'] !== $existing['email']) {
            $checkStmt = $this->db->prepare("SELECT id_customer FROM customers WHERE email = :email AND id_customer != :id");
            $checkStmt->execute(['email' => $data['email'], 'id' => $id]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah digunakan pelanggan lain'];
            }
        }

        try {
            $sql = "UPDATE customers SET updated_at = NOW()";
            $params = ['id' => $id];

            if (isset($data['nama_lengkap'])) {
                $sql .= ", nama_lengkap = :nama";
                $params['nama'] = trim($data['nama_lengkap']);
            }
            if (isset($data['email'])) {
                $sql .= ", email = :email";
                $params['email'] = trim($data['email']);
            }
            if (isset($data['no_telp'])) {
                $sql .= ", no_telp = :telp";
                $params['telp'] = $data['no_telp'];
            }
            if (isset($data['is_active'])) {
                $sql .= ", is_active = :is_active";
                $params['is_active'] = (int)$data['is_active'];
            }
            if (!empty($data['password'])) {
                $sql .= ", password_hash = :password";
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id_customer = :id";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if ($success) {
                return ['success' => true, 'message' => 'Data pelanggan berhasil diperbarui'];
            }
            return ['success' => false, 'message' => 'Gagal memperbarui data pelanggan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getOrderStatsByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status_order = 'selesai' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status_order = 'dibatalkan' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(CASE WHEN status_order NOT IN ('selesai', 'dibatalkan') THEN 1 ELSE 0 END) as pending_orders,
                COALESCE(SUM(CASE WHEN status_order = 'selesai' THEN total_bayar ELSE 0 END), 0) as total_completed_value
            FROM orders
            WHERE id_customer = :id
        ");
        $stmt->execute(['id' => $customerId]);
        return $stmt->fetch() ?: [
            'total_orders' => 0,
            'completed_orders' => 0,
            'cancelled_orders' => 0,
            'pending_orders' => 0,
            'total_completed_value' => 0
        ];
    }
}
