<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class CustomerFeedbackRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAllReviews(array $filters = []): array
    {
        $sql = "
            SELECT 
                r.id_review,
                r.rating,
                r.komentar,
                r.foto_review,
                r.tanggal_review,
                r.status_review,
                r.id_order,
                c.id_customer,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                c.profile_image as customer_image,
                p.id_product,
                p.nama_product,
                p.gambar as product_image
            FROM review r
            JOIN customers c ON r.id_customer = c.id_customer
            JOIN products p ON r.id_product = p.id_product
        ";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(c.nama_lengkap) LIKE :search_nama OR LOWER(p.nama_product) LIKE :search_product OR LOWER(r.komentar) LIKE :search_komentar)";
            $params['search_nama'] = $search;
            $params['search_product'] = $search;
            $params['search_komentar'] = $search;
        }
        
        if (!empty($filters['status'])) {
            $where[] = "r.status_review = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['rating'])) {
            $where[] = "r.rating = :rating";
            $params['rating'] = (int) $filters['rating'];
        }
        
        if (!empty($filters['customer_id'])) {
            $where[] = "r.id_customer = :customer_id";
            $params['customer_id'] = (int) $filters['customer_id'];
        }
        
        if (!empty($filters['product_id'])) {
            $where[] = "r.id_product = :product_id";
            $params['product_id'] = $filters['product_id'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY r.tanggal_review DESC";
        
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

    public function countReviews(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM review r JOIN customers c ON r.id_customer = c.id_customer JOIN products p ON r.id_product = p.id_product";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(c.nama_lengkap) LIKE :search_nama OR LOWER(p.nama_product) LIKE :search_product OR LOWER(r.komentar) LIKE :search_komentar)";
            $params['search_nama'] = $search;
            $params['search_product'] = $search;
            $params['search_komentar'] = $search;
        }
        
        if (!empty($filters['status'])) {
            $where[] = "r.status_review = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['rating'])) {
            $where[] = "r.rating = :rating";
            $params['rating'] = (int) $filters['rating'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function getReviewById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                c.profile_image as customer_image,
                p.nama_product,
                p.gambar as product_image
            FROM review r
            JOIN customers c ON r.id_customer = c.id_customer
            JOIN products p ON r.id_product = p.id_product
            WHERE r.id_review = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateReviewStatus(string $reviewId, string $status): array
    {
        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Status tidak valid'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE review SET status_review = :status WHERE id_review = :id
            ");
            $success = $stmt->execute(['status' => $status, 'id' => $reviewId]);
            
            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Status review berhasil diperbarui'];
            }
            return ['success' => false, 'message' => 'Review tidak ditemukan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function deleteReview(string $reviewId): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM review WHERE id_review = :id");
            $success = $stmt->execute(['id' => $reviewId]);
            
            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Review berhasil dihapus'];
            }
            return ['success' => false, 'message' => 'Review tidak ditemukan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getReviewStats(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_review = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status_review = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status_review = 'rejected' THEN 1 ELSE 0 END) as rejected,
                COALESCE(AVG(rating), 0) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
            FROM review
        ");
        return $stmt->fetch() ?: [
            'total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0,
            'avg_rating' => 0, 'rating_5' => 0, 'rating_4' => 0, 
            'rating_3' => 0, 'rating_2' => 0, 'rating_1' => 0
        ];
    }

    public function tableExists(string $tableName): bool
    {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['table' => $tableName]);
        return $stmt->fetch() !== false;
    }

    public function getAllFeedback(array $filters = []): array
    {
        if (!$this->tableExists('customer_feedback')) {
            return [];
        }

        $sql = "
            SELECT 
                cf.*,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                a.nama_lengkap as admin_name
            FROM customer_feedback cf
            JOIN customers c ON cf.id_customer = c.id_customer
            LEFT JOIN administrators a ON cf.responded_by = a.id_admin
        ";
        
        $params = [];
        $where = [];
        
        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(c.nama_lengkap) LIKE :search_nama OR LOWER(cf.subject) LIKE :search_subject OR LOWER(cf.message) LIKE :search_message)";
            $params['search_nama'] = $search;
            $params['search_subject'] = $search;
            $params['search_message'] = $search;
        }
        
        if (!empty($filters['type'])) {
            $where[] = "cf.feedback_type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "cf.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY cf.created_at DESC";
        
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

    public function getFeedbackById(int $id): ?array
    {
        if (!$this->tableExists('customer_feedback')) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT 
                cf.*,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                a.nama_lengkap as admin_name
            FROM customer_feedback cf
            JOIN customers c ON cf.id_customer = c.id_customer
            LEFT JOIN administrators a ON cf.responded_by = a.id_admin
            WHERE cf.id_feedback = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function respondToFeedback(int $feedbackId, string $response, int $adminId): array
    {
        if (!$this->tableExists('customer_feedback')) {
            return ['success' => false, 'message' => 'Tabel feedback belum tersedia'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE customer_feedback 
                SET admin_response = :response, 
                    responded_by = :admin_id, 
                    responded_at = NOW(),
                    status = 'ditanggapi'
                WHERE id_feedback = :id
            ");
            $success = $stmt->execute([
                'response' => $response,
                'admin_id' => $adminId,
                'id' => $feedbackId
            ]);
            
            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Balasan berhasil disimpan'];
            }
            return ['success' => false, 'message' => 'Feedback tidak ditemukan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateFeedbackStatus(int $feedbackId, string $status): array
    {
        if (!$this->tableExists('customer_feedback')) {
            return ['success' => false, 'message' => 'Tabel feedback belum tersedia'];
        }

        $validStatuses = ['pending', 'dibaca', 'ditanggapi', 'selesai'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Status tidak valid'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE customer_feedback SET status = :status WHERE id_feedback = :id
            ");
            $success = $stmt->execute(['status' => $status, 'id' => $feedbackId]);
            
            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Status feedback berhasil diperbarui'];
            }
            return ['success' => false, 'message' => 'Feedback tidak ditemukan'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getFeedbackStats(): array
    {
        if (!$this->tableExists('customer_feedback')) {
            return [
                'total' => 0, 'pending' => 0, 'dibaca' => 0, 
                'ditanggapi' => 0, 'selesai' => 0,
                'saran' => 0, 'komplain' => 0, 'pertanyaan' => 0
            ];
        }

        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'dibaca' THEN 1 ELSE 0 END) as dibaca,
                SUM(CASE WHEN status = 'ditanggapi' THEN 1 ELSE 0 END) as ditanggapi,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN feedback_type = 'saran' THEN 1 ELSE 0 END) as saran,
                SUM(CASE WHEN feedback_type = 'komplain' THEN 1 ELSE 0 END) as komplain,
                SUM(CASE WHEN feedback_type = 'pertanyaan' THEN 1 ELSE 0 END) as pertanyaan
            FROM customer_feedback
        ");
        return $stmt->fetch() ?: [
            'total' => 0, 'pending' => 0, 'dibaca' => 0, 
            'ditanggapi' => 0, 'selesai' => 0,
            'saran' => 0, 'komplain' => 0, 'pertanyaan' => 0
        ];
    }
}
