<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class VoucherUsageRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT pv.*, v.kode, v.judul as nama_voucher, v.jenis as jenis_voucher, c.nama_lengkap as nama_pengguna
                FROM penggunaan_voucher pv
                LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
                LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
                WHERE 1=1";
        $params = [];

        if (!empty($filters['id_voucher'])) {
            $sql .= " AND pv.id_voucher = :id_voucher";
            $params['id_voucher'] = $filters['id_voucher'];
        }

        if (!empty($filters['id_pengguna'])) {
            $sql .= " AND pv.id_pengguna = :id_pengguna";
            $params['id_pengguna'] = $filters['id_pengguna'];
        }

        if (!empty($filters['tanggal_dari'])) {
            $sql .= " AND pv.digunakan_pada >= :tanggal_dari";
            $params['tanggal_dari'] = $filters['tanggal_dari'];
        }

        if (!empty($filters['tanggal_sampai'])) {
            $sql .= " AND pv.digunakan_pada <= :tanggal_sampai";
            $params['tanggal_sampai'] = $filters['tanggal_sampai'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(v.kode) LIKE :search OR LOWER(c.nama_lengkap) LIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        $sql .= " ORDER BY pv.digunakan_pada DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, v.kode, v.judul as nama_voucher, c.nama_lengkap as nama_pengguna
            FROM penggunaan_voucher pv
            LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
            LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
            WHERE pv.id_penggunaan = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO penggunaan_voucher (id_voucher, id_pengguna, id_pesanan, jumlah_diskon)
                VALUES (:id_voucher, :id_pengguna, :id_pesanan, :jumlah_diskon)
                RETURNING id_penggunaan
            ");

            $stmt->execute([
                'id_voucher' => $data['id_voucher'],
                'id_pengguna' => $data['id_pengguna'] ?? null,
                'id_pesanan' => $data['id_pesanan'] ?? null,
                'jumlah_diskon' => $data['jumlah_diskon'] ?? 0
            ]);

            $result = $stmt->fetch();

            return [
                'success' => true,
                'message' => 'Penggunaan voucher berhasil dicatat',
                'id' => $result['id_penggunaan']
            ];
        } catch (\PDOException $e) {
            error_log('VoucherUsageRepository::create - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mencatat penggunaan voucher. Silakan coba lagi.'];
        }
    }

    public function getUsageByVoucher(string $voucherId): array
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, c.nama_lengkap as nama_pengguna
            FROM penggunaan_voucher pv
            LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
            WHERE pv.id_voucher = :id_voucher
            ORDER BY pv.digunakan_pada DESC
        ");
        $stmt->execute(['id_voucher' => $voucherId]);
        return $stmt->fetchAll();
    }

    public function getUsageByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, v.kode, v.judul as nama_voucher
            FROM penggunaan_voucher pv
            LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
            WHERE pv.id_pengguna = :id_pengguna
            ORDER BY pv.digunakan_pada DESC
        ");
        $stmt->execute(['id_pengguna' => $userId]);
        return $stmt->fetchAll();
    }

    public function countByVoucher(string $voucherId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM penggunaan_voucher WHERE id_voucher = :id");
        $stmt->execute(['id' => $voucherId]);
        return (int) $stmt->fetch()['total'];
    }

    public function countByUser(int $userId, ?string $voucherId = null): int
    {
        $sql = "SELECT COUNT(*) as total FROM penggunaan_voucher WHERE id_pengguna = :user_id";
        $params = ['user_id' => $userId];

        if ($voucherId) {
            $sql .= " AND id_voucher = :voucher_id";
            $params['voucher_id'] = $voucherId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function getTotalDiscountByVoucher(string $voucherId): float
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah_diskon), 0) as total FROM penggunaan_voucher WHERE id_voucher = :id");
        $stmt->execute(['id' => $voucherId]);
        return (float) $stmt->fetch()['total'];
    }

    public function getRecentUsage(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, v.kode, v.judul as nama_voucher, c.nama_lengkap as nama_pengguna
            FROM penggunaan_voucher pv
            LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
            LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
            ORDER BY pv.digunakan_pada DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsageStats(string $period = 'daily'): array
    {
        $dateFormat = match($period) {
            'monthly' => "TO_CHAR(digunakan_pada, 'YYYY-MM')",
            'weekly' => "TO_CHAR(digunakan_pada, 'IYYY-IW')",
            default => "TO_CHAR(digunakan_pada, 'YYYY-MM-DD')"
        };

        $stmt = $this->db->prepare("
            SELECT {$dateFormat} as periode,
                   COUNT(*) as total_penggunaan,
                   COALESCE(SUM(jumlah_diskon), 0) as total_diskon
            FROM penggunaan_voucher
            GROUP BY {$dateFormat}
            ORDER BY periode DESC
            LIMIT 30
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopVouchers(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT v.id_voucher, v.kode, v.judul,
                   COUNT(pv.id_penggunaan) as total_penggunaan,
                   COALESCE(SUM(pv.jumlah_diskon), 0) as total_diskon
            FROM voucher v
            LEFT JOIN penggunaan_voucher pv ON v.id_voucher = pv.id_voucher
            GROUP BY v.id_voucher, v.kode, v.judul
            ORDER BY total_penggunaan DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
