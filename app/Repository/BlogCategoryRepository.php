<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class BlogCategoryRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM blog_categories";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY nama_kategori ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM blog_categories WHERE id_category = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM blog_categories WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        if (empty($data['nama_kategori'])) {
            return ['success' => false, 'message' => 'Nama kategori wajib diisi'];
        }

        $slug = $this->generateSlug($data['nama_kategori']);
        
        $existing = $this->getBySlug($slug);
        if ($existing) {
            return ['success' => false, 'message' => 'Kategori dengan nama serupa sudah ada'];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO blog_categories (nama_kategori, slug, is_active)
                VALUES (:nama_kategori, :slug, :is_active)
                RETURNING id_category
            ");
            
            $stmt->execute([
                'nama_kategori' => trim($data['nama_kategori']),
                'slug' => $slug,
                'is_active' => $data['is_active'] ?? 1
            ]);

            $result = $stmt->fetch();
            return [
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'id' => $result['id_category']
            ];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Kategori tidak ditemukan'];
        }

        if (empty($data['nama_kategori'])) {
            return ['success' => false, 'message' => 'Nama kategori wajib diisi'];
        }

        $slug = $this->generateSlug($data['nama_kategori']);
        
        $slugCheck = $this->getBySlug($slug);
        if ($slugCheck && $slugCheck['id_category'] != $id) {
            return ['success' => false, 'message' => 'Kategori dengan nama serupa sudah ada'];
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE blog_categories 
                SET nama_kategori = :nama_kategori, slug = :slug, is_active = :is_active, updated_at = CURRENT_TIMESTAMP
                WHERE id_category = :id
            ");
            
            $stmt->execute([
                'id' => $id,
                'nama_kategori' => trim($data['nama_kategori']),
                'slug' => $slug,
                'is_active' => $data['is_active'] ?? 1
            ]);

            return ['success' => true, 'message' => 'Kategori berhasil diperbarui'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Kategori tidak ditemukan'];
        }

        $postCount = $this->getPostCount($id);
        if ($postCount > 0) {
            return ['success' => false, 'message' => "Tidak dapat menghapus kategori karena masih memiliki $postCount artikel"];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM blog_categories WHERE id_category = :id");
            $stmt->execute(['id' => $id]);
            return ['success' => true, 'message' => 'Kategori berhasil dihapus'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getPostCount(int $categoryId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM blog_posts WHERE id_category = :id");
        $stmt->execute(['id' => $categoryId]);
        return (int) $stmt->fetch()['total'];
    }

    private function generateSlug(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}
