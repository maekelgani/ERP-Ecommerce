<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class CategoryRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'KTG';
    private const ID_LENGTH = 3;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                k.*,
                COALESCE(COUNT(p.id_product), 0) as total_products
            FROM kategori k
            LEFT JOIN products p ON k.id_kategori = p.id_kategori
        ";

        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(k.nama_kategori) LIKE :search_nama OR LOWER(k.deskripsi_kategori) LIKE :search_desc)";
            $params['search_nama'] = $search;
            $params['search_desc'] = $search;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " GROUP BY k.id_kategori ORDER BY k.nama_kategori ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                k.*,
                COALESCE(COUNT(p.id_product), 0) as total_products
            FROM kategori k
            LEFT JOIN products p ON k.id_kategori = p.id_kategori
            WHERE k.id_kategori = :id
            GROUP BY k.id_kategori
        ");
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

        if ($this->isDuplicateName($sanitizedData['nama_kategori'])) {
            return [
                'success' => false,
                'message' => 'Nama kategori sudah digunakan. Silakan gunakan nama lain.'
            ];
        }

        $id = $this->generateId();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO kategori (id_kategori, nama_kategori, deskripsi_kategori, icon_kategori)
                VALUES (:id, :nama, :deskripsi, :icon)
            ");
            $success = $stmt->execute([
                'id' => $id,
                'nama' => $sanitizedData['nama_kategori'],
                'deskripsi' => $sanitizedData['deskripsi_kategori'],
                'icon' => $data['icon_kategori'] ?? null
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil ditambahkan',
                    'id' => $id
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menambahkan kategori ke database'
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
                'message' => 'Kategori tidak ditemukan'
            ];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        if ($this->isDuplicateName($sanitizedData['nama_kategori'], $id)) {
            return [
                'success' => false,
                'message' => 'Nama kategori sudah digunakan. Silakan gunakan nama lain.'
            ];
        }

        try {
            $updateFields = "nama_kategori = :nama, deskripsi_kategori = :deskripsi";
            $params = [
                'id' => $id,
                'nama' => $sanitizedData['nama_kategori'],
                'deskripsi' => $sanitizedData['deskripsi_kategori']
            ];

            if (!empty($data['icon_kategori'])) {
                $updateFields .= ", icon_kategori = :icon";
                $params['icon'] = $data['icon_kategori'];
            }

            $stmt = $this->db->prepare("
                UPDATE kategori 
                SET {$updateFields}
                WHERE id_kategori = :id
            ");
            $success = $stmt->execute($params);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil diperbarui'
                ];
            }

            if ($success && $stmt->rowCount() === 0) {
                return [
                    'success' => true,
                    'message' => 'Tidak ada perubahan yang dilakukan'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui kategori'
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
                'message' => 'Kategori tidak ditemukan'
            ];
        }

        $productCount = $this->getProductCount($id);
        if ($productCount > 0) {
            return [
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $productCount . ' produk. Hapus atau pindahkan produk terlebih dahulu.'
            ];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM kategori WHERE id_kategori = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus kategori - tidak ada data yang dihapus'
            ];
        } catch (\PDOException $e) {
            $sqlState = $e->getCode();
            if ($sqlState === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return [
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk lain'
                ];
            }
            return [
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ];
        }
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kategori");
        return (int) $stmt->fetch()['total'];
    }

    public function isUsedByProducts(string $id): bool
    {
        return $this->getProductCount($id) > 0;
    }

    public function getProductCount(string $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE id_kategori = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetch()['total'];
    }

    public function isDuplicateName(string $name, ?string $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM kategori WHERE LOWER(nama_kategori) = LOWER(:nama)";
        $params = ['nama' => $name];

        if ($excludeId) {
            $sql .= " AND id_kategori != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'] > 0;
    }

    public function getNextId(): string
    {
        return $this->generateId();
    }

    private function generateId(): string
    {
        $stmt = $this->db->query("SELECT id_kategori FROM kategori ORDER BY id_kategori DESC LIMIT 1");
        $last = $stmt->fetch();

        if ($last) {
            $num = (int) substr($last['id_kategori'], strlen(self::ID_PREFIX)) + 1;
        } else {
            $num = 1;
        }

        return self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);
    }

    private function validateData(array $data): array
    {
        if (empty($data['nama_kategori']) || trim($data['nama_kategori']) === '') {
            return [
                'success' => false,
                'message' => 'Nama kategori wajib diisi'
            ];
        }

        $name = trim($data['nama_kategori']);

        if (strlen($name) < 2) {
            return [
                'success' => false,
                'message' => 'Nama kategori minimal 2 karakter'
            ];
        }

        if (strlen($name) > 100) {
            return [
                'success' => false,
                'message' => 'Nama kategori maksimal 100 karakter'
            ];
        }

        if ($this->containsHtmlOrScript($name)) {
            return [
                'success' => false,
                'message' => 'Nama kategori tidak boleh mengandung HTML atau script'
            ];
        }

        $description = $data['deskripsi_kategori'] ?? '';
        if (!empty($description) && $this->containsHtmlOrScript($description)) {
            return [
                'success' => false,
                'message' => 'Deskripsi kategori tidak boleh mengandung HTML atau script berbahaya'
            ];
        }

        return ['success' => true];
    }

    private function sanitizeData(array $data): array
    {
        return [
            'nama_kategori' => $this->sanitizeString($data['nama_kategori']),
            'deskripsi_kategori' => isset($data['deskripsi_kategori'])
                ? $this->sanitizeString($data['deskripsi_kategori'])
                : null
        ];
    }

    private function sanitizeString(string $input): string
    {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return $sanitized;
    }

    private function containsHtmlOrScript(string $input): bool
    {
        $patterns = [
            '/<script\b[^>]*>/i',
            '/<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<form/i',
            '/<input/i',
            '/<button/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
