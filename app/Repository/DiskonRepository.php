<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class DiskonRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'DSK';
    private const ID_LENGTH = 10;
    private const TABLE_NAME = 'promo_diskon';

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT d.*, p.nama_product, p.harga as harga_produk, p.gambar as gambar_produk,
                       pk.judul as nama_kampanye
                FROM " . self::TABLE_NAME . " d 
                LEFT JOIN products p ON d.id_produk = p.id_product
                LEFT JOIN promo_kampanye pk ON d.id_kampanye = pk.id_kampanye
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['jenis'])) {
            $sql .= " AND d.jenis = :jenis";
            $params['jenis'] = $filters['jenis'];
        }

        if (!empty($filters['id_kampanye'])) {
            $sql .= " AND d.id_kampanye = :id_kampanye";
            $params['id_kampanye'] = $filters['id_kampanye'];
        }

        if (!empty($filters['id_produk'])) {
            $sql .= " AND d.id_produk = :id_produk";
            $params['id_produk'] = $filters['id_produk'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(d.label) ILIKE :search OR LOWER(p.nama_product) ILIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        $orderBy = $filters['sort'] ?? 'newest';
        switch ($orderBy) {
            case 'oldest':
                $sql .= " ORDER BY d.dibuat_pada ASC";
                break;
            case 'nilai':
                $sql .= " ORDER BY d.nilai DESC";
                break;
            default:
                $sql .= " ORDER BY d.dibuat_pada DESC";
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('DiskonRepository::getAll - ' . $e->getMessage());
            return [];
        }
    }

    public function getById(string $id): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT d.*, p.nama_product, p.harga as harga_produk, p.gambar as gambar_produk,
                       pk.judul as nama_kampanye
                FROM " . self::TABLE_NAME . " d 
                LEFT JOIN products p ON d.id_produk = p.id_product
                LEFT JOIN promo_kampanye pk ON d.id_kampanye = pk.id_kampanye
                WHERE d.id_diskon = :id
            ");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log('DiskonRepository::getById - ' . $e->getMessage());
            return null;
        }
    }

    public function getActiveDiscountsByProduct(string $idProduct): array
    {
        try {
            // Gunakan waktu PHP (sudah di-set ke Asia/Jakarta) bukan NOW() PostgreSQL
            $currentTime = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                SELECT d.* FROM " . self::TABLE_NAME . " d 
                WHERE d.id_produk = :id_produk 
                  AND d.status = 'aktif'
                  AND (d.mulai_pada IS NULL OR :current_time >= d.mulai_pada)
                  AND (d.selesai_pada IS NULL OR :current_time2 <= d.selesai_pada)
                  AND (d.stok_promo IS NULL OR d.stok_terpakai < d.stok_promo)
                ORDER BY d.nilai DESC
            ");
            $stmt->execute([
                'id_produk' => $idProduct,
                'current_time' => $currentTime,
                'current_time2' => $currentTime
            ]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('DiskonRepository::getActiveDiscountsByProduct - ' . $e->getMessage());
            return [];
        }
    }

    public function getBestDiscountForProduct(string $idProduct): ?array
    {
        $discounts = $this->getActiveDiscountsByProduct($idProduct);
        if (empty($discounts)) {
            return null;
        }

        $best = null;
        $bestValue = 0;

        foreach ($discounts as $discount) {
            $value = $this->calculateDiscountValue($discount);
            if ($value > $bestValue) {
                $bestValue = $value;
                $best = $discount;
            }
        }

        return $best;
    }

    public function isStockAvailable(string $idDiskon): bool
    {
        try {
            $stmt = $this->db->prepare("
                SELECT stok_promo, stok_terpakai 
                FROM " . self::TABLE_NAME . " 
                WHERE id_diskon = :id
            ");
            $stmt->execute(['id' => $idDiskon]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            if ($result['stok_promo'] === null) {
                return true;
            }

            return $result['stok_terpakai'] < $result['stok_promo'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::isStockAvailable - ' . $e->getMessage());
            return false;
        }
    }

    public function create(array $data): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $id = $this->generateId();

        $hargaAwal = null;
        $hargaDiskon = null;
        if (!empty($data['id_produk'])) {
            $productPrice = $this->getProductPrice($data['id_produk']);
            if ($productPrice) {
                $hargaAwal = $productPrice;
                $hargaDiskon = $this->calculateFinalPrice($productPrice, $data['jenis'], $data['nilai']);
            }
        }

        $mulaiPada = !empty($data['mulai_pada']) ? $this->formatDatetime($data['mulai_pada']) : null;
        $selesaiPada = !empty($data['selesai_pada']) ? $this->formatDatetime($data['selesai_pada']) : null;

        if (!empty($data['status']) && $data['status'] === 'nonaktif') {
            $status = 'nonaktif';
        } else {
            $status = $this->determineInitialStatus($mulaiPada, $selesaiPada);
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO " . self::TABLE_NAME . " (
                    id_diskon, id_produk, id_kampanye, label, harga_awal, harga_diskon,
                    jenis, nilai, stok_promo, maks_qty_per_pengguna,
                    mulai_pada, selesai_pada, status, stok_terpakai
                ) VALUES (
                    :id, :id_produk, :id_kampanye, :label, :harga_awal, :harga_diskon,
                    :jenis, :nilai, :stok_promo, :maks_qty,
                    :mulai_pada, :selesai_pada, :status, 0
                )
            ");

            $success = $stmt->execute([
                'id' => $id,
                'id_produk' => !empty($data['id_produk']) ? $data['id_produk'] : null,
                'id_kampanye' => !empty($data['id_kampanye']) ? $data['id_kampanye'] : null,
                'label' => !empty($data['label']) ? $data['label'] : null,
                'harga_awal' => $hargaAwal,
                'harga_diskon' => $hargaDiskon,
                'jenis' => $data['jenis'],
                'nilai' => (float)$data['nilai'],
                'stok_promo' => !empty($data['stok_promo']) ? (int)$data['stok_promo'] : null,
                'maks_qty' => !empty($data['maks_qty_per_pengguna']) ? (int)$data['maks_qty_per_pengguna'] : null,
                'mulai_pada' => $mulaiPada,
                'selesai_pada' => $selesaiPada,
                'status' => $status
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Diskon berhasil ditambahkan',
                    'id' => $id
                ];
            }

            return ['success' => false, 'message' => 'Gagal menambahkan diskon'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::create - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    public function update(string $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Diskon tidak ditemukan'];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $hargaAwal = $existing['harga_awal'];
        $hargaDiskon = $existing['harga_diskon'];
        if (!empty($data['id_produk'])) {
            $productPrice = $this->getProductPrice($data['id_produk']);
            if ($productPrice) {
                $hargaAwal = $productPrice;
                $hargaDiskon = $this->calculateFinalPrice($productPrice, $data['jenis'], $data['nilai']);
            }
        }

        $mulaiPada = !empty($data['mulai_pada']) ? $this->formatDatetime($data['mulai_pada']) : null;
        $selesaiPada = !empty($data['selesai_pada']) ? $this->formatDatetime($data['selesai_pada']) : null;

        if (!empty($data['status']) && $data['status'] === 'nonaktif') {
            $status = 'nonaktif';
        } else {
            $status = $this->determineInitialStatus($mulaiPada, $selesaiPada);
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE " . self::TABLE_NAME . " SET
                    id_produk = :id_produk,
                    id_kampanye = :id_kampanye,
                    label = :label,
                    harga_awal = :harga_awal,
                    harga_diskon = :harga_diskon,
                    jenis = :jenis,
                    nilai = :nilai,
                    stok_promo = :stok_promo,
                    maks_qty_per_pengguna = :maks_qty,
                    mulai_pada = :mulai_pada,
                    selesai_pada = :selesai_pada,
                    status = :status,
                    diperbarui_pada = CURRENT_TIMESTAMP
                WHERE id_diskon = :id
            ");

            $success = $stmt->execute([
                'id' => $id,
                'id_produk' => !empty($data['id_produk']) ? $data['id_produk'] : null,
                'id_kampanye' => !empty($data['id_kampanye']) ? $data['id_kampanye'] : null,
                'label' => !empty($data['label']) ? $data['label'] : null,
                'harga_awal' => $hargaAwal,
                'harga_diskon' => $hargaDiskon,
                'jenis' => $data['jenis'],
                'nilai' => (float)$data['nilai'],
                'stok_promo' => !empty($data['stok_promo']) ? (int)$data['stok_promo'] : null,
                'maks_qty' => !empty($data['maks_qty_per_pengguna']) ? (int)$data['maks_qty_per_pengguna'] : null,
                'mulai_pada' => $mulaiPada,
                'selesai_pada' => $selesaiPada,
                'status' => $status
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Diskon berhasil diperbarui'];
            }

            return ['success' => false, 'message' => 'Gagal memperbarui diskon'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::update - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    public function delete(string $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Diskon tidak ditemukan'];
        }

        if (isset($existing['stok_terpakai']) && $existing['stok_terpakai'] > 0) {
            return ['success' => false, 'message' => 'Diskon tidak dapat dihapus karena sudah pernah digunakan'];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id_diskon = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Diskon berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus diskon'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::delete - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    public function decrementStock(string $id): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT stok_promo, stok_terpakai 
                FROM " . self::TABLE_NAME . " 
                WHERE id_diskon = :id 
                FOR UPDATE
            ");
            $stmt->execute(['id' => $id]);
            $discount = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$discount) {
                $this->db->rollBack();
                return false;
            }

            if ($discount['stok_promo'] !== null && $discount['stok_terpakai'] >= $discount['stok_promo']) {
                $this->db->rollBack();
                return false;
            }

            $updateStmt = $this->db->prepare("
                UPDATE " . self::TABLE_NAME . " 
                SET stok_terpakai = stok_terpakai + 1 
                WHERE id_diskon = :id
            ");
            $updateStmt->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('DiskonRepository::decrementStock - ' . $e->getMessage());
            return false;
        }
    }

    public function massCreate(array $productIds, array $discountData): array
    {
        $successCount = 0;
        $errors = [];

        foreach ($productIds as $productId) {
            $data = array_merge($discountData, ['id_produk' => $productId]);
            $result = $this->create($data);

            if ($result['success']) {
                $successCount++;
            } else {
                $errors[] = "Produk {$productId}: " . $result['message'];
            }
        }

        if ($successCount === count($productIds)) {
            return ['success' => true, 'message' => "Berhasil menambahkan diskon untuk {$successCount} produk"];
        }

        return [
            'success' => $successCount > 0,
            'message' => "Berhasil: {$successCount}, Gagal: " . count($errors),
            'errors' => $errors
        ];
    }

    public function count(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM " . self::TABLE_NAME);
            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::count - ' . $e->getMessage());
            return 0;
        }
    }

    public function countByStatus(string $status): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . self::TABLE_NAME . " WHERE status = :status");
            $stmt->execute(['status' => $status]);
            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::countByStatus - ' . $e->getMessage());
            return 0;
        }
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

    public function computeStatus(?string $mulaiPada, ?string $selesaiPada, string $currentStatus = 'terjadwal'): string
    {
        // 1. Status nonaktif adalah manual override - tidak berubah otomatis
        if ($currentStatus === 'nonaktif') {
            return 'nonaktif';
        }

        // Gunakan presisi menit (set detik ke 0)
        $now = new \DateTime();
        $now->setTime((int)$now->format('H'), (int)$now->format('i'), 0);

        // 2. Jika mulai_pada IS NULL → status = 'aktif' (promo langsung aktif)
        if (empty($mulaiPada)) {
            // Cek apakah sudah berakhir
            if (!empty($selesaiPada)) {
                $endTime = new \DateTime($selesaiPada);
                $endTime->setTime((int)$endTime->format('H'), (int)$endTime->format('i'), 0);
                if ($now > $endTime) {
                    return 'berakhir';
                }
            }
            return 'aktif';
        }

        $startTime = new \DateTime($mulaiPada);
        $startTime->setTime((int)$startTime->format('H'), (int)$startTime->format('i'), 0);

        // 3. Jika mulai_pada <= now
        if ($now >= $startTime) {
            // 3a. Jika selesai_pada IS NULL → status = 'aktif'
            if (empty($selesaiPada)) {
                return 'aktif';
            }

            $endTime = new \DateTime($selesaiPada);
            $endTime->setTime((int)$endTime->format('H'), (int)$endTime->format('i'), 0);

            // 3b. Jika now <= selesai_pada → status = 'aktif'
            if ($now <= $endTime) {
                return 'aktif';
            }

            // 3c. Jika now > selesai_pada → status = 'berakhir'
            return 'berakhir';
        }

        // 4. Jika mulai_pada > now → status = 'terjadwal'
        // PENTING: Promo yang sudah 'aktif' atau 'berakhir' TIDAK boleh kembali ke 'terjadwal'
        // kecuali di-edit manual (ubah mulai_pada)
        if ($currentStatus === 'aktif' || $currentStatus === 'berakhir') {
            // Jika selesai_pada sudah lewat, tetap berakhir
            if (!empty($selesaiPada)) {
                $endTime = new \DateTime($selesaiPada);
                $endTime->setTime((int)$endTime->format('H'), (int)$endTime->format('i'), 0);
                if ($now > $endTime) {
                    return 'berakhir';
                }
            }
            // Jika tidak, kembalikan status saat ini (aktif/berakhir tetap)
            return $currentStatus;
        }

        return 'terjadwal';
    }

    public function determineInitialStatus(?string $mulaiPada, ?string $selesaiPada): string
    {
        // Gunakan presisi menit (set detik ke 0)
        $now = new \DateTime();
        $now->setTime((int)$now->format('H'), (int)$now->format('i'), 0);

        if (!empty($selesaiPada)) {
            $endTime = new \DateTime($selesaiPada);
            $endTime->setTime((int)$endTime->format('H'), (int)$endTime->format('i'), 0);
            if ($now > $endTime) {
                return 'berakhir';
            }
        }

        if (empty($mulaiPada)) {
            return 'aktif';
        }

        $startTime = new \DateTime($mulaiPada);
        $startTime->setTime((int)$startTime->format('H'), (int)$startTime->format('i'), 0);

        if ($now >= $startTime) {
            return 'aktif';
        }

        return 'terjadwal';
    }

    public function getAllWithComputedStatus(array $filters = []): array
    {
        $discounts = $this->getAll($filters);

        foreach ($discounts as &$discount) {
            $discount['computed_status'] = $this->computeStatus(
                $discount['mulai_pada'],
                $discount['selesai_pada'],
                $discount['status']
            );
        }

        return $discounts;
    }

    public function syncAllStatuses(): array
    {
        $updated = 0;
        $errors = [];

        try {
            $stmt = $this->db->query("SELECT id_diskon, mulai_pada, selesai_pada, status FROM " . self::TABLE_NAME . " WHERE status NOT IN ('nonaktif')");
            $discounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($discounts as $discount) {
                $computedStatus = $this->computeStatus(
                    $discount['mulai_pada'],
                    $discount['selesai_pada'],
                    $discount['status']
                );

                if ($computedStatus !== $discount['status']) {
                    $updateStmt = $this->db->prepare("UPDATE " . self::TABLE_NAME . " SET status = :status, diperbarui_pada = CURRENT_TIMESTAMP WHERE id_diskon = :id");
                    $updateStmt->execute(['status' => $computedStatus, 'id' => $discount['id_diskon']]);
                    $updated++;
                }
            }

            return ['success' => true, 'updated' => $updated];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::syncAllStatuses - ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'updated' => $updated];
        }
    }

    public function updateStatus(string $id, string $newStatus): array
    {
        $validStatuses = ['terjadwal', 'aktif', 'berakhir', 'nonaktif'];
        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'message' => 'Status tidak valid'];
        }

        try {
            $existing = $this->getById($id);
            if (!$existing) {
                return ['success' => false, 'message' => 'Diskon tidak ditemukan'];
            }

            if ($newStatus === 'aktif' && $existing['status'] === 'nonaktif') {
                $newStatus = $this->determineInitialStatus($existing['mulai_pada'], $existing['selesai_pada']);
            }

            $stmt = $this->db->prepare("UPDATE " . self::TABLE_NAME . " SET status = :status, diperbarui_pada = CURRENT_TIMESTAMP WHERE id_diskon = :id");
            $stmt->execute(['status' => $newStatus, 'id' => $id]);

            return ['success' => true, 'message' => 'Status berhasil diperbarui', 'new_status' => $newStatus];
        } catch (\PDOException $e) {
            error_log('DiskonRepository::updateStatus - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    private function validateData(array $data): array
    {
        $validJenis = ['persen', 'nominal'];
        if (empty($data['jenis']) || !in_array($data['jenis'], $validJenis)) {
            return ['success' => false, 'message' => 'Jenis diskon tidak valid'];
        }

        if (!isset($data['nilai']) || !is_numeric($data['nilai']) || $data['nilai'] <= 0) {
            return ['success' => false, 'message' => 'Nilai diskon harus berupa angka positif'];
        }

        if ($data['jenis'] === 'persen' && $data['nilai'] > 100) {
            return ['success' => false, 'message' => 'Diskon persen maksimal 100%'];
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
        $maxAttempts = 10;
        $attempt = 0;

        do {
            // Generate random hex string (8 characters) with prefix DSK
            $randomHex = strtoupper(bin2hex(random_bytes(4)));
            $newId = self::ID_PREFIX . $randomHex;

            // Check if ID already exists
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE id_diskon = :id");
                $stmt->execute(['id' => $newId]);
                $exists = (int) $stmt->fetchColumn() > 0;
            } catch (\PDOException $e) {
                error_log('DiskonRepository::generateId - Check error: ' . $e->getMessage());
                $exists = false; // Assume not exists if check fails
            }

            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        return $newId;
    }

    private function getProductPrice(string $productId): ?float
    {
        try {
            $stmt = $this->db->prepare("SELECT harga FROM products WHERE id_product = :id");
            $stmt->execute(['id' => $productId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? (float)$result['harga'] : null;
        } catch (\PDOException $e) {
            error_log('DiskonRepository::getProductPrice - ' . $e->getMessage());
            return null;
        }
    }

    private function calculateFinalPrice(float $originalPrice, string $jenis, float $nilai): float
    {
        if ($jenis === 'persen') {
            return $originalPrice - ($originalPrice * $nilai / 100);
        }
        return max(0, $originalPrice - $nilai);
    }

    private function calculateDiscountValue(array $discount): float
    {
        if (!$discount['harga_awal']) {
            return 0;
        }

        if ($discount['jenis'] === 'persen') {
            return $discount['harga_awal'] * $discount['nilai'] / 100;
        }

        return min($discount['nilai'], $discount['harga_awal']);
    }

    private function formatDatetime(?string $datetime): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        $datetime = str_replace('T', ' ', $datetime);

        if (strlen($datetime) === 16) {
            $datetime .= ':00';
        }

        return $datetime;
    }
}
