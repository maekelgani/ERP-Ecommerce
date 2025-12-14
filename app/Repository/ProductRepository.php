<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class ProductRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'PRD';
    private const ID_LENGTH = 3;

    private const STOCK_THRESHOLD_LOW = 9;
    private const STOCK_THRESHOLD_HIGH = 10;

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_FILE_SIZE = 5242880;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT p.*, k.nama_kategori, b.nama_brand 
                FROM products p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN brand b ON p.id_brand = b.id_brand 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['kategori'])) {
            $sql .= " AND p.id_kategori = :kategori";
            $params['kategori'] = $filters['kategori'];
        }

        if (!empty($filters['brand'])) {
            $sql .= " AND p.id_brand = :brand";
            $params['brand'] = $filters['brand'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status_produk = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(p.nama_product) LIKE :search_product OR LOWER(b.nama_brand) LIKE :search_brand)";
            $params['search_product'] = $search;
            $params['search_brand'] = $search;
        }

        $orderBy = $filters['sort'] ?? 'newest';
        switch ($orderBy) {
            case 'oldest':
                $sql .= " ORDER BY p.tanggal_ditambahkan ASC";
                break;
            case 'price-asc':
                $sql .= " ORDER BY p.harga ASC";
                break;
            case 'price-desc':
                $sql .= " ORDER BY p.harga DESC";
                break;
            case 'stock-asc':
                $sql .= " ORDER BY p.stok ASC";
                break;
            case 'stock-desc':
                $sql .= " ORDER BY p.stok DESC";
                break;
            default:
                $sql .= " ORDER BY p.tanggal_ditambahkan DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, k.nama_kategori, b.nama_brand 
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_product = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByKategori(string $idKategori): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, k.nama_kategori, b.nama_brand 
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_kategori = :id_kategori
            ORDER BY p.tanggal_ditambahkan DESC
        ");
        $stmt->execute(['id_kategori' => $idKategori]);
        return $stmt->fetchAll();
    }

    public function create(array $data, ?array $imageFile = null): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        if (!empty($sanitizedData['id_kategori'])) {
            if (!$this->categoryExists($sanitizedData['id_kategori'])) {
                return [
                    'success' => false,
                    'message' => 'Kategori yang dipilih tidak valid atau tidak ditemukan'
                ];
            }
        }

        if (!empty($sanitizedData['id_brand'])) {
            if (!$this->brandExists($sanitizedData['id_brand'])) {
                return [
                    'success' => false,
                    'message' => 'Brand yang dipilih tidak valid atau tidak ditemukan'
                ];
            }
        }

        $imagePath = null;
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImage($imageFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            $imagePath = $uploadResult['filename'];
        }

        $statusProduk = $this->determineProductStatus(
            $sanitizedData['stok'],
            $sanitizedData['status_produk'] ?? 'tersedia'
        );

        $id = $this->generateId();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO products (
                    id_product, nama_product, deskripsi_speksifikasi, harga, stok,
                    id_kategori, id_brand, gambar, berat_gram, status_produk
                ) VALUES (
                    :id, :nama, :deskripsi, :harga, :stok,
                    :kategori, :brand, :gambar, :berat, :status
                )
            ");

            $success = $stmt->execute([
                'id' => $id,
                'nama' => $sanitizedData['nama_product'],
                'deskripsi' => $sanitizedData['deskripsi_speksifikasi'],
                'harga' => $sanitizedData['harga'],
                'stok' => $sanitizedData['stok'],
                'kategori' => $sanitizedData['id_kategori'] ?: null,
                'brand' => $sanitizedData['id_brand'] ?: null,
                'gambar' => $imagePath,
                'berat' => $sanitizedData['berat_gram'],
                'status' => $statusProduk
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan',
                    'id' => $id
                ];
            }

            if ($imagePath) {
                $this->deleteImage($imagePath);
            }

            return [
                'success' => false,
                'message' => 'Gagal menambahkan produk ke database'
            ];
        } catch (\PDOException $e) {
            if ($imagePath) {
                $this->deleteImage($imagePath);
            }
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function update(string $id, array $data, ?array $imageFile = null): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);

        if (!empty($sanitizedData['id_kategori'])) {
            if (!$this->categoryExists($sanitizedData['id_kategori'])) {
                return [
                    'success' => false,
                    'message' => 'Kategori yang dipilih tidak valid atau tidak ditemukan'
                ];
            }
        }

        if (!empty($sanitizedData['id_brand'])) {
            if (!$this->brandExists($sanitizedData['id_brand'])) {
                return [
                    'success' => false,
                    'message' => 'Brand yang dipilih tidak valid atau tidak ditemukan'
                ];
            }
        }

        $imagePath = $existing['gambar'];
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImage($imageFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            if ($existing['gambar']) {
                $this->deleteImage($existing['gambar']);
            }
            $imagePath = $uploadResult['filename'];
        }

        $statusProduk = $this->determineProductStatus(
            $sanitizedData['stok'],
            $sanitizedData['status_produk'] ?? $existing['status_produk']
        );

        try {
            $stmt = $this->db->prepare("
                UPDATE products SET 
                    nama_product = :nama,
                    deskripsi_speksifikasi = :deskripsi,
                    harga = :harga,
                    stok = :stok,
                    id_kategori = :kategori,
                    id_brand = :brand,
                    gambar = :gambar,
                    berat_gram = :berat,
                    status_produk = :status
                WHERE id_product = :id
            ");

            $success = $stmt->execute([
                'id' => $id,
                'nama' => $sanitizedData['nama_product'],
                'deskripsi' => $sanitizedData['deskripsi_speksifikasi'],
                'harga' => $sanitizedData['harga'],
                'stok' => $sanitizedData['stok'],
                'kategori' => $sanitizedData['id_kategori'] ?: null,
                'brand' => $sanitizedData['id_brand'] ?: null,
                'gambar' => $imagePath,
                'berat' => $sanitizedData['berat_gram'],
                'status' => $statusProduk
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui produk'
            ];
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateStock(string $id, int $stok): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ];
        }

        $statusProduk = $this->determineProductStatus($stok, $existing['status_produk']);

        try {
            $stmt = $this->db->prepare("
                UPDATE products SET stok = :stok, status_produk = :status 
                WHERE id_product = :id
            ");
            $success = $stmt->execute([
                'id' => $id,
                'stok' => $stok,
                'status' => $statusProduk
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Stok berhasil diperbarui',
                    'new_status' => $statusProduk,
                    'stock_status' => $this->getStockStatus($stok)
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui stok'
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
                'message' => 'Produk tidak ditemukan'
            ];
        }

        if ($this->isUsedInOrders($id)) {
            return [
                'success' => false,
                'message' => 'Produk tidak dapat dihapus karena sudah pernah digunakan dalam pesanan'
            ];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id_product = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                if ($existing['gambar']) {
                    $this->deleteImage($existing['gambar']);
                }
                return [
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus produk - tidak ada data yang dihapus'
            ];
        } catch (\PDOException $e) {
            $sqlState = $e->getCode();
            if ($sqlState === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return [
                    'success' => false,
                    'message' => 'Produk tidak dapat dihapus karena masih digunakan di data lain'
                ];
            }
            return [
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ];
        }
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products");
        return (int) $stmt->fetch()['total'];
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE status_produk = :status");
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetch()['total'];
    }

    public function countLowStock(int $threshold = 9): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE stok > 0 AND stok <= :threshold");
        $stmt->execute(['threshold' => $threshold]);
        return (int) $stmt->fetch()['total'];
    }

    public function countOutOfStock(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE stok = 0");
        return (int) $stmt->fetch()['total'];
    }

    public function getStockStatus(int $stok): array
    {
        if ($stok == 0) {
            return [
                'level' => 'habis',
                'label' => 'Stok Habis',
                'class' => 'bg-red-100 text-red-700',
                'dot_class' => 'bg-red-500'
            ];
        } elseif ($stok <= self::STOCK_THRESHOLD_LOW) {
            return [
                'level' => 'menipis',
                'label' => 'Stok Menipis',
                'class' => 'bg-amber-100 text-amber-700',
                'dot_class' => 'bg-amber-500'
            ];
        } else {
            return [
                'level' => 'banyak',
                'label' => 'Stok Banyak',
                'class' => 'bg-emerald-100 text-emerald-700',
                'dot_class' => 'bg-emerald-500'
            ];
        }
    }

    public function getProductStatus(string $status): array
    {
        switch ($status) {
            case 'tersedia':
                return [
                    'label' => 'Tersedia',
                    'class' => 'bg-emerald-100 text-emerald-700',
                    'dot_class' => 'bg-emerald-500'
                ];
            case 'habis':
                return [
                    'label' => 'Habis',
                    'class' => 'bg-red-100 text-red-700',
                    'dot_class' => 'bg-red-500'
                ];
            case 'nonaktif':
                return [
                    'label' => 'Nonaktif',
                    'class' => 'bg-gray-100 text-gray-600',
                    'dot_class' => 'bg-gray-400'
                ];
            default:
                return [
                    'label' => 'Unknown',
                    'class' => 'bg-gray-100 text-gray-600',
                    'dot_class' => 'bg-gray-400'
                ];
        }
    }

    public function isUsedInOrders(string $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM order_items WHERE id_product = :id");
            $stmt->execute(['id' => $id]);
            return (int) $stmt->fetch()['total'] > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getNextId(): string
    {
        return $this->generateId();
    }

    private function determineProductStatus(int $stok, string $currentStatus): string
    {
        if ($currentStatus === 'nonaktif') {
            return 'nonaktif';
        }

        if ($stok == 0) {
            return 'habis';
        }

        if ($stok > 0 && $currentStatus === 'habis') {
            return 'tersedia';
        }

        return $currentStatus;
    }

    private function generateId(): string
    {
        $stmt = $this->db->query("SELECT id_product FROM products ORDER BY id_product DESC LIMIT 1");
        $last = $stmt->fetch();

        if ($last) {
            $num = (int) substr($last['id_product'], strlen(self::ID_PREFIX)) + 1;
        } else {
            $num = 1;
        }

        return self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);
    }

    private function validateData(array $data): array
    {
        if (empty($data['nama_product']) || trim($data['nama_product']) === '') {
            return [
                'success' => false,
                'message' => 'Nama produk wajib diisi'
            ];
        }

        $name = trim($data['nama_product']);

        if (strlen($name) < 3) {
            return [
                'success' => false,
                'message' => 'Nama produk minimal 3 karakter'
            ];
        }

        if (strlen($name) > 200) {
            return [
                'success' => false,
                'message' => 'Nama produk maksimal 200 karakter'
            ];
        }

        if ($this->containsHtmlOrScript($name)) {
            return [
                'success' => false,
                'message' => 'Nama produk tidak boleh mengandung HTML atau script'
            ];
        }

        $harga = $data['harga'] ?? 0;
        if (!is_numeric($harga) || $harga <= 0) {
            return [
                'success' => false,
                'message' => 'Harga produk harus berupa angka lebih dari 0'
            ];
        }

        $stok = $data['stok'] ?? 0;
        if (!is_numeric($stok) || $stok < 0) {
            return [
                'success' => false,
                'message' => 'Stok produk harus berupa angka 0 atau lebih'
            ];
        }

        $berat = $data['berat_gram'] ?? 0;
        if (!is_numeric($berat) || $berat < 0) {
            return [
                'success' => false,
                'message' => 'Berat produk harus berupa angka 0 atau lebih'
            ];
        }

        $validStatuses = ['tersedia', 'habis', 'nonaktif'];
        $status = $data['status_produk'] ?? 'tersedia';
        if (!in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Status produk tidak valid'
            ];
        }

        return ['success' => true];
    }

    private function sanitizeData(array $data): array
    {
        $harga = $data['harga'] ?? 0;
        if (is_string($harga)) {
            $harga = floatval(str_replace(['.', ','], ['', '.'], $harga));
        }

        return [
            'nama_product' => $this->sanitizeString($data['nama_product']),
            'deskripsi_speksifikasi' => isset($data['deskripsi_speksifikasi']) && !empty($data['deskripsi_speksifikasi'])
                ? $this->sanitizeDescription($data['deskripsi_speksifikasi'])
                : null,
            'harga' => $harga,
            'stok' => intval($data['stok'] ?? 0),
            'id_kategori' => !empty($data['id_kategori']) ? trim($data['id_kategori']) : null,
            'id_brand' => !empty($data['id_brand']) ? trim($data['id_brand']) : null,
            'berat_gram' => intval($data['berat_gram'] ?? 0),
            'status_produk' => $data['status_produk'] ?? 'tersedia'
        ];
    }

    private function sanitizeString(string $input): string
    {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return $sanitized;
    }

    private function sanitizeDescription(string $input): string
    {
        $sanitized = trim($input);
        $allowed_tags = '<br><p><ul><ol><li><strong><em><b><i>';
        $sanitized = strip_tags($sanitized, $allowed_tags);
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
            '/<embed/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    private function categoryExists(string $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM kategori WHERE id_kategori = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetch()['total'] > 0;
    }

    private function brandExists(string $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM brand WHERE id_brand = :id");
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetch()['total'] > 0;
    }

    private function uploadImage(array $file): array
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
                'message' => 'Ukuran file maksimal 5MB'
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

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'success' => false,
                'message' => 'File bukan gambar yang valid'
            ];
        }

        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat direktori upload'
                ];
            }
        }

        $safeFilename = 'product_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
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

    public function deleteImage(string $filename): bool
    {
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../uploads/products/' . $filename;
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    public function getLowStockProducts(int $threshold = 9, int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.id_product, 
                p.nama_product, 
                p.gambar, 
                p.stok,
                k.nama_kategori,
                ROUND((p.stok / GREATEST(p.stok + 1, 1)) * 100) as stock_percentage
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            WHERE p.stok > 0 AND p.stok <= :threshold
            ORDER BY p.stok ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':threshold', $threshold, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
