<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class BrandRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'BRD';
    private const ID_LENGTH = 3;
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    private const MAX_FILE_SIZE = 2097152;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                b.*,
                COALESCE(COUNT(p.id_product), 0) as total_products
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand
        ";

        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(b.nama_brand) LIKE :search_nama OR LOWER(b.desc_brand) LIKE :search_desc)";
            $params['search_nama'] = $search;
            $params['search_desc'] = $search;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " GROUP BY b.id_brand ORDER BY b.nama_brand ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                b.*,
                COALESCE(COUNT(p.id_product), 0) as total_products
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand
            WHERE b.id_brand = :id
            GROUP BY b.id_brand
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data, ?array $logoFile = null): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        if ($this->isDuplicateName($sanitizedData['nama_brand'])) {
            return [
                'success' => false,
                'message' => 'Nama brand sudah digunakan. Silakan gunakan nama lain.'
            ];
        }

        $logoPath = null;
        if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadLogo($logoFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            $logoPath = $uploadResult['filename'];
        }

        $id = $this->generateId();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO brand (id_brand, nama_brand, desc_brand, logo_brand, website)
                VALUES (:id, :nama, :desc, :logo, :website)
            ");
            $success = $stmt->execute([
                'id' => $id,
                'nama' => $sanitizedData['nama_brand'],
                'desc' => $sanitizedData['desc_brand'],
                'logo' => $logoPath,
                'website' => $sanitizedData['website']
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Brand berhasil ditambahkan',
                    'id' => $id
                ];
            }

            if ($logoPath) {
                $this->deleteLogo($logoPath);
            }

            return [
                'success' => false,
                'message' => 'Gagal menambahkan brand ke database'
            ];
        } catch (\PDOException $e) {
            if ($logoPath) {
                $this->deleteLogo($logoPath);
            }
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function update(string $id, array $data, ?array $logoFile = null): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Brand tidak ditemukan'
            ];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        if ($this->isDuplicateName($sanitizedData['nama_brand'], $id)) {
            return [
                'success' => false,
                'message' => 'Nama brand sudah digunakan. Silakan gunakan nama lain.'
            ];
        }

        $logoPath = $existing['logo_brand'];
        if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadLogo($logoFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            if ($existing['logo_brand']) {
                $this->deleteLogo($existing['logo_brand']);
            }
            $logoPath = $uploadResult['filename'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE brand 
                SET nama_brand = :nama, 
                    desc_brand = :desc,
                    logo_brand = :logo,
                    website = :website
                WHERE id_brand = :id
            ");
            $success = $stmt->execute([
                'id' => $id,
                'nama' => $sanitizedData['nama_brand'],
                'desc' => $sanitizedData['desc_brand'],
                'logo' => $logoPath,
                'website' => $sanitizedData['website']
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Brand berhasil diperbarui'
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
                'message' => 'Gagal memperbarui brand'
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
                'message' => 'Brand tidak ditemukan'
            ];
        }

        $productCount = $this->getProductCount($id);
        if ($productCount > 0) {
            return [
                'success' => false,
                'message' => 'Brand tidak dapat dihapus karena masih digunakan oleh ' . $productCount . ' produk. Hapus atau pindahkan produk terlebih dahulu.'
            ];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM brand WHERE id_brand = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                if ($existing['logo_brand']) {
                    $this->deleteLogo($existing['logo_brand']);
                }
                return [
                    'success' => true,
                    'message' => 'Brand berhasil dihapus'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus brand - tidak ada data yang dihapus'
            ];
        } catch (\PDOException $e) {
            $sqlState = $e->getCode();
            if ($sqlState === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return [
                    'success' => false,
                    'message' => 'Brand tidak dapat dihapus karena masih digunakan oleh produk lain'
                ];
            }
            return [
                'success' => false,
                'message' => 'Gagal menghapus brand: ' . $e->getMessage()
            ];
        }
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM brand");
        return (int) $stmt->fetch()['total'];
    }

    public function isUsedByProducts(string $id): bool
    {
        return $this->getProductCount($id) > 0;
    }

    public function getProductCount(string $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE id_brand = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetch()['total'];
    }

    public function isDuplicateName(string $name, ?string $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM brand WHERE LOWER(nama_brand) = LOWER(:nama)";
        $params = ['nama' => $name];

        if ($excludeId) {
            $sql .= " AND id_brand != :id";
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
        $stmt = $this->db->query("SELECT id_brand FROM brand ORDER BY id_brand DESC LIMIT 1");
        $last = $stmt->fetch();

        if ($last) {
            $num = (int) substr($last['id_brand'], strlen(self::ID_PREFIX)) + 1;
        } else {
            $num = 1;
        }

        return self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);
    }

    private function validateData(array $data): array
    {
        if (empty($data['nama_brand']) || trim($data['nama_brand']) === '') {
            return [
                'success' => false,
                'message' => 'Nama brand wajib diisi'
            ];
        }

        $name = trim($data['nama_brand']);

        if (strlen($name) < 2) {
            return [
                'success' => false,
                'message' => 'Nama brand minimal 2 karakter'
            ];
        }

        if (strlen($name) > 100) {
            return [
                'success' => false,
                'message' => 'Nama brand maksimal 100 karakter'
            ];
        }

        if ($this->containsHtmlOrScript($name)) {
            return [
                'success' => false,
                'message' => 'Nama brand tidak boleh mengandung HTML atau script'
            ];
        }

        $website = $data['website'] ?? '';
        if (!empty($website)) {
            if (!filter_var($website, FILTER_VALIDATE_URL)) {
                return [
                    'success' => false,
                    'message' => 'Format URL website tidak valid'
                ];
            }
        }

        $description = $data['desc_brand'] ?? '';
        if (!empty($description) && $this->containsHtmlOrScript($description)) {
            return [
                'success' => false,
                'message' => 'Deskripsi brand tidak boleh mengandung HTML atau script berbahaya'
            ];
        }

        return ['success' => true];
    }

    private function sanitizeData(array $data): array
    {
        return [
            'nama_brand' => $this->sanitizeString($data['nama_brand']),
            'desc_brand' => isset($data['desc_brand']) && !empty($data['desc_brand'])
                ? $this->sanitizeString($data['desc_brand'])
                : null,
            'website' => isset($data['website']) && !empty($data['website'])
                ? filter_var(trim($data['website']), FILTER_SANITIZE_URL)
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

    private function uploadLogo(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Error saat upload file: ' . $this->getUploadErrorMessage($file['error'])
            ];
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return [
                'success' => false,
                'message' => 'Ukuran file maksimal 2MB'
            ];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return [
                'success' => false,
                'message' => 'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_EXTENSIONS)
            ];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return [
                'success' => false,
                'message' => 'Tipe file tidak valid. Pastikan file adalah gambar yang benar.'
            ];
        }

        if ($extension !== 'svg') {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return [
                    'success' => false,
                    'message' => 'File bukan gambar yang valid'
                ];
            }
        }

        $uploadDir = __DIR__ . '/../../uploads/brands/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat direktori upload'
                ];
            }
        }

        $safeFilename = 'brand_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $safeFilename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $safeFilename
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal menyimpan file'
        ];
    }

    private function getUploadErrorMessage(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi batas server)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi batas form)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];

        return $errors[$errorCode] ?? 'Error tidak diketahui';
    }

    private function deleteLogo(string $filename): bool
    {
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../uploads/brands/' . $filename;
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
