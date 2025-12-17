<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class StoreLocationRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'TKO';
    private const ID_LENGTH = 4;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM store_locations";
        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(nama_toko) LIKE :search OR LOWER(alamat) LIKE :search2 OR LOWER(kota_kabupaten) LIKE :search3)";
            $params['search'] = $search;
            $params['search2'] = $search;
            $params['search3'] = $search;
        }

        if (isset($filters['is_active'])) {
            $where[] = "is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM store_locations WHERE id_toko = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);
        $id = $this->generateId();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO store_locations 
                (id_toko, nama_toko, no_telepon, alamat, provinsi, kota_kabupaten, kecamatan, kelurahan, kode_pos, jam_buka, jam_tutup, is_active)
                VALUES 
                (:id, :nama_toko, :no_telepon, :alamat, :provinsi, :kota_kabupaten, :kecamatan, :kelurahan, :kode_pos, :jam_buka, :jam_tutup, :is_active)
            ");

            $success = $stmt->execute([
                'id' => $id,
                'nama_toko' => $sanitizedData['nama_toko'],
                'no_telepon' => $sanitizedData['no_telepon'],
                'alamat' => $sanitizedData['alamat'],
                'provinsi' => $sanitizedData['provinsi'],
                'kota_kabupaten' => $sanitizedData['kota_kabupaten'],
                'kecamatan' => $sanitizedData['kecamatan'],
                'kelurahan' => $sanitizedData['kelurahan'],
                'kode_pos' => $sanitizedData['kode_pos'],
                'jam_buka' => $sanitizedData['jam_buka'],
                'jam_tutup' => $sanitizedData['jam_tutup'],
                'is_active' => $sanitizedData['is_active'] ?? 1
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Lokasi toko berhasil ditambahkan',
                    'id' => $id
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menambahkan lokasi toko ke database'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function update(string $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Lokasi toko tidak ditemukan'
            ];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        try {
            $stmt = $this->db->prepare("
                UPDATE store_locations 
                SET nama_toko = :nama_toko,
                    no_telepon = :no_telepon,
                    alamat = :alamat,
                    provinsi = :provinsi,
                    kota_kabupaten = :kota_kabupaten,
                    kecamatan = :kecamatan,
                    kelurahan = :kelurahan,
                    kode_pos = :kode_pos,
                    jam_buka = :jam_buka,
                    jam_tutup = :jam_tutup,
                    is_active = :is_active
                WHERE id_toko = :id
            ");

            $success = $stmt->execute([
                'id' => $id,
                'nama_toko' => $sanitizedData['nama_toko'],
                'no_telepon' => $sanitizedData['no_telepon'],
                'alamat' => $sanitizedData['alamat'],
                'provinsi' => $sanitizedData['provinsi'],
                'kota_kabupaten' => $sanitizedData['kota_kabupaten'],
                'kecamatan' => $sanitizedData['kecamatan'],
                'kelurahan' => $sanitizedData['kelurahan'],
                'kode_pos' => $sanitizedData['kode_pos'],
                'jam_buka' => $sanitizedData['jam_buka'],
                'jam_tutup' => $sanitizedData['jam_tutup'],
                'is_active' => $sanitizedData['is_active'] ?? 1
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Lokasi toko berhasil diperbarui'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui lokasi toko'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function delete(string $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Lokasi toko tidak ditemukan'
            ];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM store_locations WHERE id_toko = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Lokasi toko berhasil dihapus'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus lokasi toko'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM store_locations");
        return (int) $stmt->fetch()['total'];
    }

    public function getActiveStores(): array
    {
        $stmt = $this->db->prepare("
            SELECT id_toko, nama_toko, no_telepon, alamat, provinsi, kota_kabupaten, 
                    kecamatan, kelurahan, kode_pos, jam_buka, jam_tutup
            FROM store_locations 
            WHERE is_active = 1 
            ORDER BY nama_toko ASC
        ");
        $stmt->execute();
        $stores = $stmt->fetchAll();

        foreach ($stores as &$store) {
            $store['is_open'] = $this->isStoreOpen($store['jam_buka'], $store['jam_tutup']);
            $store['jam_operasional'] = $this->formatOperationalHours($store['jam_buka'], $store['jam_tutup']);
        }

        return $stores;
    }

    private function isStoreOpen(?string $jamBuka, ?string $jamTutup): bool
    {
        if (empty($jamBuka) || empty($jamTutup)) {
            return false;
        }

        $timezone = new \DateTimeZone('Asia/Jakarta');
        $now = new \DateTime('now', $timezone);
        $currentTime = $now->format('H:i:s');

        return $currentTime >= $jamBuka && $currentTime <= $jamTutup;
    }

    private function formatOperationalHours(?string $jamBuka, ?string $jamTutup): string
    {
        if (empty($jamBuka) || empty($jamTutup)) {
            return '-';
        }

        $buka = substr($jamBuka, 0, 5);
        $tutup = substr($jamTutup, 0, 5);

        return $buka . ' - ' . $tutup;
    }

    public function getNextId(): string
    {
        return $this->generateId();
    }

    private function generateId(): string
    {
        $maxRetries = 5;
        for ($i = 0; $i < $maxRetries; $i++) {
            $stmt = $this->db->query("SELECT id_toko FROM store_locations ORDER BY id_toko DESC LIMIT 1 FOR UPDATE");
            $last = $stmt->fetch();

            if ($last) {
                $num = (int) substr($last['id_toko'], strlen(self::ID_PREFIX)) + 1;
            } else {
                $num = 1;
            }

            $newId = self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);

            $checkStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM store_locations WHERE id_toko = :id");
            $checkStmt->execute(['id' => $newId]);
            if ((int)$checkStmt->fetch()['cnt'] === 0) {
                return $newId;
            }
        }

        return self::ID_PREFIX . bin2hex(random_bytes(4));
    }

    private function validateData(array $data): array
    {
        if (empty($data['nama_toko']) || trim($data['nama_toko']) === '') {
            return ['success' => false, 'message' => 'Nama toko wajib diisi'];
        }

        if (empty($data['alamat']) || trim($data['alamat']) === '') {
            return ['success' => false, 'message' => 'Alamat wajib diisi'];
        }

        if (empty($data['provinsi']) || trim($data['provinsi']) === '') {
            return ['success' => false, 'message' => 'Provinsi wajib diisi'];
        }

        if (empty($data['kota_kabupaten']) || trim($data['kota_kabupaten']) === '') {
            return ['success' => false, 'message' => 'Kota/Kabupaten wajib diisi'];
        }

        if (strlen(trim($data['nama_toko'])) > 100) {
            return ['success' => false, 'message' => 'Nama toko maksimal 100 karakter'];
        }

        if (strlen(trim($data['alamat'])) > 500) {
            return ['success' => false, 'message' => 'Alamat maksimal 500 karakter'];
        }

        if (!empty($data['no_telepon'])) {
            $phone = preg_replace('/[^0-9\-\+\s\(\)]/', '', $data['no_telepon']);
            if (strlen($phone) < 8 || strlen($phone) > 20) {
                return ['success' => false, 'message' => 'Format nomor telepon tidak valid'];
            }
        }

        if (!empty($data['kode_pos'])) {
            if (!preg_match('/^[0-9]{5}$/', trim($data['kode_pos']))) {
                return ['success' => false, 'message' => 'Format kode pos tidak valid (harus 5 digit)'];
            }
        }

        if (!empty($data['jam_buka']) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data['jam_buka'])) {
            return ['success' => false, 'message' => 'Format jam buka tidak valid'];
        }

        if (!empty($data['jam_tutup']) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data['jam_tutup'])) {
            return ['success' => false, 'message' => 'Format jam tutup tidak valid'];
        }

        return ['success' => true];
    }

    private function sanitizeData(array $data): array
    {
        return [
            'nama_toko' => $this->sanitizeString($data['nama_toko']),
            'no_telepon' => !empty($data['no_telepon']) ? $this->sanitizeString($data['no_telepon']) : null,
            'alamat' => $this->sanitizeString($data['alamat']),
            'provinsi' => $this->sanitizeString($data['provinsi']),
            'kota_kabupaten' => $this->sanitizeString($data['kota_kabupaten']),
            'kecamatan' => !empty($data['kecamatan']) ? $this->sanitizeString($data['kecamatan']) : null,
            'kelurahan' => !empty($data['kelurahan']) ? $this->sanitizeString($data['kelurahan']) : null,
            'kode_pos' => !empty($data['kode_pos']) ? $this->sanitizeString($data['kode_pos']) : null,
            'jam_buka' => !empty($data['jam_buka']) ? $data['jam_buka'] : null,
            'jam_tutup' => !empty($data['jam_tutup']) ? $data['jam_tutup'] : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];
    }

    private function sanitizeString(string $input): string
    {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return $sanitized;
    }
}
