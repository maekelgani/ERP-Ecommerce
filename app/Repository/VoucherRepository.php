<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class VoucherRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'VCH';
    private const ID_LENGTH = 10;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT v.*, 
                       (SELECT COUNT(*) FROM penggunaan_voucher pv WHERE pv.id_voucher = v.id_voucher) as total_penggunaan
                FROM voucher v WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND v.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['jenis'])) {
            $sql .= " AND v.jenis = :jenis";
            $params['jenis'] = $filters['jenis'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(v.kode) LIKE :search OR LOWER(v.judul) LIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        $orderBy = $filters['sort'] ?? 'newest';
        switch ($orderBy) {
            case 'oldest':
                $sql .= " ORDER BY v.dibuat_pada ASC";
                break;
            case 'code':
                $sql .= " ORDER BY v.kode ASC";
                break;
            case 'usage':
                $sql .= " ORDER BY total_penggunaan DESC";
                break;
            default:
                $sql .= " ORDER BY v.dibuat_pada DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   (SELECT COUNT(*) FROM penggunaan_voucher pv WHERE pv.id_voucher = v.id_voucher) as total_penggunaan
            FROM voucher v 
            WHERE v.id_voucher = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   (SELECT COUNT(*) FROM penggunaan_voucher pv WHERE pv.id_voucher = v.id_voucher) as total_penggunaan
            FROM voucher v 
            WHERE UPPER(v.kode) = UPPER(:code)
        ");
        $stmt->execute(['code' => $code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getActiveVouchers(): array
    {
        $stmt = $this->db->prepare("
            SELECT v.* FROM voucher v 
            WHERE v.status = 'aktif'
              AND (v.mulai_pada IS NULL OR NOW() >= v.mulai_pada)
              AND (v.selesai_pada IS NULL OR NOW() <= v.selesai_pada)
              AND (v.kuota_total IS NULL OR v.kuota_terpakai < v.kuota_total)
            ORDER BY v.nilai DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function validateVoucher(string $code, ?int $userId, float $cartTotal, array $productIds = [], array $categoryIds = []): array
    {
        $voucher = $this->getByCode($code);

        if (!$voucher) {
            return ['valid' => false, 'message' => 'Kode voucher tidak ditemukan'];
        }

        if ($voucher['status'] !== 'aktif') {
            return ['valid' => false, 'message' => 'Voucher tidak aktif'];
        }

        $now = time();
        if ($voucher['mulai_pada'] && strtotime($voucher['mulai_pada']) > $now) {
            return ['valid' => false, 'message' => 'Voucher belum dapat digunakan'];
        }

        if ($voucher['selesai_pada'] && strtotime($voucher['selesai_pada']) < $now) {
            return ['valid' => false, 'message' => 'Voucher sudah kadaluarsa'];
        }

        if ($voucher['kuota_total'] !== null && $voucher['kuota_terpakai'] >= $voucher['kuota_total']) {
            return ['valid' => false, 'message' => 'Kuota voucher sudah habis'];
        }

        if ($userId && $voucher['kuota_per_pengguna']) {
            $userUsage = $this->getUserUsageCount($voucher['id_voucher'], $userId);
            if ($userUsage >= $voucher['kuota_per_pengguna']) {
                return ['valid' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini'];
            }
        }

        if ($voucher['minimal_belanja'] > 0 && $cartTotal < $voucher['minimal_belanja']) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja Rp ' . number_format($voucher['minimal_belanja'], 0, ',', '.') . ' untuk menggunakan voucher ini'
            ];
        }

        if ($voucher['terbatas_produk']) {
            $allowedProducts = json_decode($voucher['terbatas_produk'], true) ?? [];
            if (!empty($allowedProducts) && empty(array_intersect($productIds, $allowedProducts))) {
                return ['valid' => false, 'message' => 'Voucher tidak berlaku untuk produk yang dipilih'];
            }
        }

        if ($voucher['terbatas_kategori']) {
            $allowedCategories = json_decode($voucher['terbatas_kategori'], true) ?? [];
            if (!empty($allowedCategories) && empty(array_intersect($categoryIds, $allowedCategories))) {
                return ['valid' => false, 'message' => 'Voucher tidak berlaku untuk kategori produk yang dipilih'];
            }
        }

        $discountAmount = $this->calculateDiscount($voucher, $cartTotal);

        return [
            'valid' => true,
            'voucher' => $voucher,
            'discount_amount' => $discountAmount,
            'message' => 'Voucher berhasil diterapkan'
        ];
    }

    public function calculateDiscount(array $voucher, float $cartTotal): float
    {
        $discount = 0;

        switch ($voucher['jenis']) {
            case 'diskon_persen':
                $discount = $cartTotal * ($voucher['nilai'] / 100);
                break;
            case 'diskon_nominal':
                $discount = $voucher['nilai'];
                break;
            case 'gratis_ongkir':
                $discount = 0;
                break;
            case 'cashback':
                $discount = min($cartTotal * ($voucher['nilai'] / 100), $voucher['maksimal_diskon'] ?? $cartTotal);
                break;
        }

        if ($voucher['maksimal_diskon'] && $discount > $voucher['maksimal_diskon']) {
            $discount = $voucher['maksimal_diskon'];
        }

        return min($discount, $cartTotal);
    }

    public function create(array $data): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        if ($this->codeExists($data['kode'])) {
            return ['success' => false, 'message' => 'Kode voucher sudah digunakan'];
        }

        $id = $this->generateId();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO voucher (
                    id_voucher, kode, judul, deskripsi, jenis, nilai,
                    minimal_belanja, maksimal_diskon, mulai_pada, selesai_pada,
                    kuota_total, kuota_per_pengguna, terbatas_produk, terbatas_kategori, status
                ) VALUES (
                    :id, :kode, :judul, :deskripsi, :jenis, :nilai,
                    :minimal_belanja, :maksimal_diskon, :mulai_pada, :selesai_pada,
                    :kuota_total, :kuota_per_pengguna, :terbatas_produk, :terbatas_kategori, :status
                )
            ");

            $success = $stmt->execute([
                'id' => $id,
                'kode' => strtoupper(trim($data['kode'])),
                'judul' => $data['judul'] ?? null,
                'deskripsi' => $data['deskripsi'] ?? null,
                'jenis' => $data['jenis'],
                'nilai' => $data['nilai'],
                'minimal_belanja' => $data['minimal_belanja'] ?? 0,
                'maksimal_diskon' => !empty($data['maksimal_diskon']) ? $data['maksimal_diskon'] : null,
                'mulai_pada' => $data['mulai_pada'] ?? null,
                'selesai_pada' => $data['selesai_pada'] ?? null,
                'kuota_total' => !empty($data['kuota_total']) ? (int)$data['kuota_total'] : null,
                'kuota_per_pengguna' => !empty($data['kuota_per_pengguna']) ? (int)$data['kuota_per_pengguna'] : null,
                'terbatas_produk' => !empty($data['terbatas_produk']) ? json_encode($data['terbatas_produk']) : null,
                'terbatas_kategori' => !empty($data['terbatas_kategori']) ? json_encode($data['terbatas_kategori']) : null,
                'status' => $data['status'] ?? 'terjadwal'
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Voucher berhasil ditambahkan', 'id' => $id];
            }

            return ['success' => false, 'message' => 'Gagal menambahkan voucher'];
        } catch (\PDOException $e) {
            error_log('VoucherRepository::create - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan voucher. Silakan coba lagi.'];
        }
    }

    public function update(string $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Voucher tidak ditemukan'];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        if (strtoupper($data['kode']) !== strtoupper($existing['kode']) && $this->codeExists($data['kode'])) {
            return ['success' => false, 'message' => 'Kode voucher sudah digunakan'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE voucher SET
                    kode = :kode,
                    judul = :judul,
                    deskripsi = :deskripsi,
                    jenis = :jenis,
                    nilai = :nilai,
                    minimal_belanja = :minimal_belanja,
                    maksimal_diskon = :maksimal_diskon,
                    mulai_pada = :mulai_pada,
                    selesai_pada = :selesai_pada,
                    kuota_total = :kuota_total,
                    kuota_per_pengguna = :kuota_per_pengguna,
                    terbatas_produk = :terbatas_produk,
                    terbatas_kategori = :terbatas_kategori,
                    status = :status
                WHERE id_voucher = :id
            ");

            $success = $stmt->execute([
                'id' => $id,
                'kode' => strtoupper(trim($data['kode'])),
                'judul' => $data['judul'] ?? null,
                'deskripsi' => $data['deskripsi'] ?? null,
                'jenis' => $data['jenis'],
                'nilai' => $data['nilai'],
                'minimal_belanja' => $data['minimal_belanja'] ?? 0,
                'maksimal_diskon' => !empty($data['maksimal_diskon']) ? $data['maksimal_diskon'] : null,
                'mulai_pada' => $data['mulai_pada'] ?? null,
                'selesai_pada' => $data['selesai_pada'] ?? null,
                'kuota_total' => !empty($data['kuota_total']) ? (int)$data['kuota_total'] : null,
                'kuota_per_pengguna' => !empty($data['kuota_per_pengguna']) ? (int)$data['kuota_per_pengguna'] : null,
                'terbatas_produk' => !empty($data['terbatas_produk']) ? json_encode($data['terbatas_produk']) : null,
                'terbatas_kategori' => !empty($data['terbatas_kategori']) ? json_encode($data['terbatas_kategori']) : null,
                'status' => $data['status'] ?? 'terjadwal'
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Voucher berhasil diperbarui'];
            }

            return ['success' => false, 'message' => 'Gagal memperbarui voucher'];
        } catch (\PDOException $e) {
            error_log('VoucherRepository::update - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui voucher. Silakan coba lagi.'];
        }
    }

    public function delete(string $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Voucher tidak ditemukan'];
        }

        if ($existing['kuota_terpakai'] > 0) {
            return ['success' => false, 'message' => 'Voucher tidak dapat dihapus karena sudah pernah digunakan'];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM voucher WHERE id_voucher = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Voucher berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus voucher'];
        } catch (\PDOException $e) {
            error_log('VoucherRepository::delete - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus voucher. Silakan coba lagi.'];
        }
    }

    public function decrementQuota(string $id): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT kuota_total, kuota_terpakai 
                FROM voucher 
                WHERE id_voucher = :id 
                FOR UPDATE
            ");
            $stmt->execute(['id' => $id]);
            $voucher = $stmt->fetch();

            if (!$voucher) {
                $this->db->rollBack();
                return false;
            }

            if ($voucher['kuota_total'] !== null && $voucher['kuota_terpakai'] >= $voucher['kuota_total']) {
                $this->db->rollBack();
                return false;
            }

            $updateStmt = $this->db->prepare("
                UPDATE voucher 
                SET kuota_terpakai = kuota_terpakai + 1 
                WHERE id_voucher = :id
            ");
            $updateStmt->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getUserUsageCount(string $voucherId, int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM penggunaan_voucher 
            WHERE id_voucher = :voucher_id AND id_pengguna = :user_id
        ");
        $stmt->execute(['voucher_id' => $voucherId, 'user_id' => $userId]);
        return (int) $stmt->fetch()['total'];
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM voucher");
        return (int) $stmt->fetch()['total'];
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM voucher WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetch()['total'];
    }

    public function generateCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    public function getStatusInfo(string $status): array
    {
        $statusMap = [
            'terjadwal' => ['label' => 'Terjadwal', 'class' => 'bg-blue-100 text-blue-700', 'dot_class' => 'bg-blue-500'],
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-emerald-100 text-emerald-700', 'dot_class' => 'bg-emerald-500'],
            'berakhir' => ['label' => 'Berakhir', 'class' => 'bg-red-100 text-red-700', 'dot_class' => 'bg-red-500'],
            'nonaktif' => ['label' => 'Nonaktif', 'class' => 'bg-gray-100 text-gray-600', 'dot_class' => 'bg-gray-400']
        ];
        return $statusMap[$status] ?? $statusMap['terjadwal'];
    }

    public function getJenisInfo(string $jenis): array
    {
        $jenisMap = [
            'diskon_persen' => ['label' => 'Diskon Persen', 'icon' => 'percent', 'class' => 'bg-blue-100 text-blue-700'],
            'diskon_nominal' => ['label' => 'Diskon Nominal', 'icon' => 'payments', 'class' => 'bg-green-100 text-green-700'],
            'gratis_ongkir' => ['label' => 'Gratis Ongkir', 'icon' => 'local_shipping', 'class' => 'bg-teal-100 text-teal-700'],
            'cashback' => ['label' => 'Cashback', 'icon' => 'account_balance_wallet', 'class' => 'bg-purple-100 text-purple-700']
        ];
        return $jenisMap[$jenis] ?? $jenisMap['diskon_persen'];
    }

    private function validateData(array $data): array
    {
        if (empty($data['kode']) || trim($data['kode']) === '') {
            return ['success' => false, 'message' => 'Kode voucher wajib diisi'];
        }

        if (strlen(trim($data['kode'])) < 4) {
            return ['success' => false, 'message' => 'Kode voucher minimal 4 karakter'];
        }

        $validJenis = ['diskon_persen', 'diskon_nominal', 'gratis_ongkir', 'cashback'];
        if (empty($data['jenis']) || !in_array($data['jenis'], $validJenis)) {
            return ['success' => false, 'message' => 'Jenis voucher tidak valid'];
        }

        if ($data['jenis'] !== 'gratis_ongkir') {
            if (!isset($data['nilai']) || !is_numeric($data['nilai']) || $data['nilai'] <= 0) {
                return ['success' => false, 'message' => 'Nilai voucher harus berupa angka positif'];
            }

            if ($data['jenis'] === 'diskon_persen' && $data['nilai'] > 100) {
                return ['success' => false, 'message' => 'Diskon persen maksimal 100%'];
            }
        }

        if (!empty($data['mulai_pada']) && !empty($data['selesai_pada'])) {
            if (strtotime($data['selesai_pada']) <= strtotime($data['mulai_pada'])) {
                return ['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai'];
            }
        }

        return ['success' => true];
    }

    private function generateId(): string
    {
        $stmt = $this->db->query("SELECT id_voucher FROM voucher ORDER BY id_voucher DESC LIMIT 1");
        $last = $stmt->fetch();

        if ($last) {
            $num = (int) substr($last['id_voucher'], strlen(self::ID_PREFIX)) + 1;
        } else {
            $num = 1;
        }

        return self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);
    }

    private function codeExists(string $code): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM voucher WHERE UPPER(kode) = UPPER(:code)");
        $stmt->execute(['code' => $code]);
        return (int) $stmt->fetch()['total'] > 0;
    }
}
