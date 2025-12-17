<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class BlogPostRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                bp.*,
                bc.nama_kategori,
                bc.slug as category_slug,
                a.nama_lengkap as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            LEFT JOIN administrators a ON bp.id_admin = a.id_admin
        ";
        $params = [];
        $where = [];

        if (!empty($filters['status'])) {
            $where[] = "bp.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $where[] = "bp.id_category = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(bp.judul) LIKE :search OR LOWER(bp.excerpt) LIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        if ($filters['published_only'] ?? false) {
            $where[] = "bp.status = 'publish'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY bp.created_at DESC";

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

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                bp.*,
                bc.nama_kategori,
                bc.slug as category_slug,
                a.nama_lengkap as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            LEFT JOIN administrators a ON bp.id_admin = a.id_admin
            WHERE bp.id_post = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                bp.*,
                bc.nama_kategori,
                bc.slug as category_slug,
                a.nama_lengkap as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            LEFT JOIN administrators a ON bp.id_admin = a.id_admin
            WHERE bp.slug = :slug
        ");
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $slug = $this->generateUniqueSlug($data['judul']);

        try {
            $publishedAt = null;
            if (($data['status'] ?? 'draft') === 'publish') {
                $publishedAt = date('Y-m-d H:i:s');
            }

            $stmt = $this->db->prepare("
                INSERT INTO blog_posts 
                (id_admin, id_category, judul, slug, excerpt, konten, thumbnail, status, published_at)
                VALUES 
                (:id_admin, :id_category, :judul, :slug, :excerpt, :konten, :thumbnail, :status, :published_at)
            ");

            $stmt->execute([
                'id_admin' => $data['id_admin'],
                'id_category' => $data['id_category'],
                'judul' => trim($data['judul']),
                'slug' => $slug,
                'excerpt' => trim($data['excerpt'] ?? ''),
                'konten' => $data['konten'],
                'thumbnail' => $data['thumbnail'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'published_at' => $publishedAt
            ]);

            $result = $this->db->lastInsertId();
            return [
                'success' => true,
                'message' => 'Artikel berhasil ditambahkan',
                'id' => $result,
                'slug' => $slug
            ];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Artikel tidak ditemukan'];
        }

        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $slug = $existing['slug'];
        if ($data['judul'] !== $existing['judul']) {
            $slug = $this->generateUniqueSlug($data['judul'], $id);
        }

        try {
            $publishedAt = $existing['published_at'];
            if (($data['status'] ?? 'draft') === 'publish' && $existing['status'] === 'draft') {
                $publishedAt = date('Y-m-d H:i:s');
            }

            $thumbnail = array_key_exists('thumbnail', $data) ? $data['thumbnail'] : $existing['thumbnail'];

            if ($thumbnail === null && $existing['thumbnail']) {
                $oldThumbnailPath = __DIR__ . '/../../uploads/blog/' . $existing['thumbnail'];
                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath);
                }
            }

            $stmt = $this->db->prepare("
                UPDATE blog_posts 
                SET id_category = :id_category,
                    judul = :judul,
                    slug = :slug,
                    excerpt = :excerpt,
                    konten = :konten,
                    thumbnail = :thumbnail,
                    status = :status,
                    published_at = :published_at,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id_post = :id
            ");

            $stmt->execute([
                'id' => $id,
                'id_category' => $data['id_category'],
                'judul' => trim($data['judul']),
                'slug' => $slug,
                'excerpt' => trim($data['excerpt'] ?? ''),
                'konten' => $data['konten'],
                'thumbnail' => $thumbnail,
                'status' => $data['status'] ?? 'draft',
                'published_at' => $publishedAt
            ]);

            return [
                'success' => true,
                'message' => 'Artikel berhasil diperbarui',
                'slug' => $slug
            ];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Artikel tidak ditemukan'];
        }

        try {
            if ($existing['thumbnail']) {
                $thumbnailPath = __DIR__ . '/../../uploads/blog/' . $existing['thumbnail'];
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id_post = :id");
            $stmt->execute(['id' => $id]);
            return ['success' => true, 'message' => 'Artikel berhasil dihapus'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function incrementViews(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id_post = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM blog_posts bp";
        $params = [];
        $where = [];

        if (!empty($filters['status'])) {
            $where[] = "bp.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $where[] = "bp.id_category = :category";
            $params['category'] = $filters['category'];
        }

        if ($filters['published_only'] ?? false) {
            $where[] = "bp.status = 'publish'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function getPublishedPosts(int $limit = 10, int $offset = 0, ?int $categoryId = null): array
    {
        $sql = "
            SELECT 
                bp.*,
                bc.nama_kategori,
                bc.slug as category_slug,
                a.nama_lengkap as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            LEFT JOIN administrators a ON bp.id_admin = a.id_admin
            WHERE bp.status = 'publish'
        ";
        $params = [];

        if ($categoryId) {
            $sql .= " AND bp.id_category = :category";
            $params['category'] = $categoryId;
        }

        $sql .= " ORDER BY bp.published_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRelatedPosts(int $postId, int $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                bp.*,
                bc.nama_kategori,
                a.nama_lengkap as author_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            LEFT JOIN administrators a ON bp.id_admin = a.id_admin
            WHERE bp.status = 'publish' 
                AND bp.id_category = :category 
                AND bp.id_post != :post_id
            ORDER BY bp.published_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':category', $categoryId, \PDO::PARAM_INT);
        $stmt->bindValue(':post_id', $postId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function validateData(array $data): array
    {
        if (empty($data['judul']) || trim($data['judul']) === '') {
            return ['success' => false, 'message' => 'Judul artikel wajib diisi'];
        }

        if (strlen(trim($data['judul'])) > 200) {
            return ['success' => false, 'message' => 'Judul maksimal 200 karakter'];
        }

        if (empty($data['id_category'])) {
            return ['success' => false, 'message' => 'Kategori wajib dipilih'];
        }

        if (empty($data['konten']) || trim($data['konten']) === '') {
            return ['success' => false, 'message' => 'Konten artikel wajib diisi'];
        }

        return ['success' => true];
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id_post FROM blog_posts WHERE slug = :slug";
            $params = ['slug' => $slug];

            if ($excludeId) {
                $sql .= " AND id_post != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            if (!$stmt->fetch()) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getPopularPosts(int $limit = 5, ?int $excludeId = null): array
    {
        $sql = "
            SELECT 
                bp.id_post, bp.judul, bp.slug, bp.thumbnail, bp.views,
                bc.nama_kategori
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            WHERE bp.status = 'publish'
        ";
        $params = [];

        if ($excludeId) {
            $sql .= " AND bp.id_post != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " ORDER BY bp.views DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPrevNextPosts(int $currentPostId, ?string $publishedAt = null): array
    {
        $result = ['prev' => null, 'next' => null];

        if (!$publishedAt) {
            $current = $this->getById($currentPostId);
            $publishedAt = $current['published_at'] ?? null;
        }

        if (!$publishedAt) {
            return $result;
        }

        $stmtPrev = $this->db->prepare("
            SELECT bp.id_post, bp.judul, bp.slug, bp.thumbnail, bc.nama_kategori
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            WHERE bp.status = 'publish' 
              AND bp.published_at < :published_at
              AND bp.id_post != :current_id
            ORDER BY bp.published_at DESC
            LIMIT 1
        ");
        $stmtPrev->execute(['published_at' => $publishedAt, 'current_id' => $currentPostId]);
        $result['prev'] = $stmtPrev->fetch() ?: null;

        $stmtNext = $this->db->prepare("
            SELECT bp.id_post, bp.judul, bp.slug, bp.thumbnail, bc.nama_kategori
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.id_category = bc.id_category
            WHERE bp.status = 'publish' 
              AND bp.published_at > :published_at
              AND bp.id_post != :current_id
            ORDER BY bp.published_at ASC
            LIMIT 1
        ");
        $stmtNext->execute(['published_at' => $publishedAt, 'current_id' => $currentPostId]);
        $result['next'] = $stmtNext->fetch() ?: null;

        return $result;
    }
}
