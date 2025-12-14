<?php

namespace App\Helper;

use App\Database\DatabaseConnection;

class CategoryLandingHelper
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Get all unique categories (case-insensitive)
     * Optimized for MySQL 8.x using Window Functions
     * Paling efficient dan modern untuk MySQL 8.4.3
     */
    public function getAllCategories(): array
    {
        $sql = "
            SELECT 
                id_kategori,
                nama_kategori,
                deskripsi_kategori,
                icon_kategori
            FROM (
                SELECT 
                    id_kategori,
                    nama_kategori,
                    deskripsi_kategori,
                    icon_kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(nama_kategori) 
                        ORDER BY id_kategori ASC
                    ) as rn
                FROM kategori
            ) ranked
            WHERE rn = 1
            ORDER BY LOWER(nama_kategori)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all unique categories with product count
     * Menggunakan Window Function untuk performance optimal
     */
    public function getAllCategoriesWithProductCount(): array
    {
        $sql = "
            SELECT 
                k.id_kategori,
                k.nama_kategori,
                k.deskripsi_kategori,
                k.icon_kategori,
                COALESCE(p.product_count, 0) as product_count
            FROM (
                SELECT 
                    id_kategori,
                    nama_kategori,
                    deskripsi_kategori,
                    icon_kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(nama_kategori) 
                        ORDER BY id_kategori ASC
                    ) as rn
                FROM kategori
            ) k
            LEFT JOIN (
                SELECT 
                    id_kategori,
                    COUNT(*) as product_count
                FROM produk
                GROUP BY id_kategori
            ) p ON k.id_kategori = p.id_kategori
            WHERE k.rn = 1
            ORDER BY LOWER(k.nama_kategori)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get categories with complete details including variant count
     * Menggunakan multiple window functions
     */
    public function getAllCategoriesWithDetails(): array
    {
        $sql = "
            SELECT 
                id_kategori,
                nama_kategori,
                deskripsi_kategori,
                icon_kategori,
                variant_count,
                product_count
            FROM (
                SELECT 
                    k.id_kategori,
                    k.nama_kategori,
                    k.deskripsi_kategori,
                    k.icon_kategori,
                    COUNT(*) OVER (PARTITION BY LOWER(k.nama_kategori)) as variant_count,
                    COALESCE(p.product_count, 0) as product_count,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(k.nama_kategori) 
                        ORDER BY k.id_kategori ASC
                    ) as rn
                FROM kategori k
                LEFT JOIN (
                    SELECT id_kategori, COUNT(*) as product_count
                    FROM produk
                    GROUP BY id_kategori
                ) p ON k.id_kategori = p.id_kategori
            ) ranked
            WHERE rn = 1
            ORDER BY LOWER(nama_kategori)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of unique categories (case-insensitive)
     */
    public function getCategoryCount(): int
    {
        $sql = "
            SELECT COUNT(DISTINCT LOWER(nama_kategori)) as total 
            FROM kategori
        ";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Get category statistics
     * Total categories, total variants, categories with products
     */
    public function getCategoryStats(): array
    {
        $sql = "
            WITH category_summary AS (
                SELECT 
                    LOWER(nama_kategori) as lower_name,
                    COUNT(*) as variant_count,
                    MIN(id_kategori) as first_id
                FROM kategori
                GROUP BY LOWER(nama_kategori)
            ),
            category_with_products AS (
                SELECT DISTINCT k.id_kategori
                FROM kategori k
                INNER JOIN produk p ON k.id_kategori = p.id_kategori
            )
            SELECT 
                (SELECT COUNT(*) FROM category_summary) as total_unique_categories,
                (SELECT COUNT(*) FROM kategori) as total_category_variants,
                (SELECT COUNT(*) FROM category_with_products) as categories_with_products,
                (SELECT COALESCE(AVG(variant_count), 0) FROM category_summary) as avg_variants_per_category
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(int $id): ?array
    {
        $sql = "SELECT * FROM kategori WHERE id_kategori = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get category by name (case-insensitive)
     * Returns the first record (lowest id_kategori)
     */
    public function getCategoryByName(string $name): ?array
    {
        $sql = "
            SELECT * FROM kategori 
            WHERE LOWER(nama_kategori) = LOWER(?) 
            ORDER BY id_kategori ASC
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all variants of a category by name
     */
    public function getCategoryVariants(string $name): array
    {
        $sql = "
            SELECT * FROM kategori 
            WHERE LOWER(nama_kategori) = LOWER(?) 
            ORDER BY id_kategori ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get categories with pagination
     * Optimized dengan window function
     */
    public function getCategoriesPaginated(int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT 
                id_kategori,
                nama_kategori,
                deskripsi_kategori,
                icon_kategori
            FROM (
                SELECT 
                    id_kategori,
                    nama_kategori,
                    deskripsi_kategori,
                    icon_kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(nama_kategori) 
                        ORDER BY id_kategori ASC
                    ) as rn
                FROM kategori
            ) ranked
            WHERE rn = 1
            ORDER BY LOWER(nama_kategori)
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Search categories by name
     */
    public function searchCategories(string $keyword): array
    {
        $sql = "
            SELECT 
                id_kategori,
                nama_kategori,
                deskripsi_kategori,
                icon_kategori,
                CASE 
                    WHEN LOWER(nama_kategori) = LOWER(?) THEN 1
                    WHEN LOWER(nama_kategori) LIKE LOWER(CONCAT(?, '%')) THEN 2
                    ELSE 3
                END as relevance
            FROM (
                SELECT 
                    id_kategori,
                    nama_kategori,
                    deskripsi_kategori,
                    icon_kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(nama_kategori) 
                        ORDER BY id_kategori ASC
                    ) as rn
                FROM kategori
                WHERE LOWER(nama_kategori) LIKE LOWER(CONCAT('%', ?, '%'))
                   OR LOWER(deskripsi_kategori) LIKE LOWER(CONCAT('%', ?, '%'))
            ) ranked
            WHERE rn = 1
            ORDER BY relevance, LOWER(nama_kategori)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$keyword, $keyword, $keyword, $keyword]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get popular categories (by product count)
     */
    public function getPopularCategories(int $limit = 8): array
    {
        $sql = "
            SELECT 
                k.id_kategori,
                k.nama_kategori,
                k.deskripsi_kategori,
                k.icon_kategori,
                COALESCE(COUNT(p.id_produk), 0) as product_count
            FROM (
                SELECT 
                    id_kategori,
                    nama_kategori,
                    deskripsi_kategori,
                    icon_kategori,
                    ROW_NUMBER() OVER (
                        PARTITION BY LOWER(nama_kategori) 
                        ORDER BY id_kategori ASC
                    ) as rn
                FROM kategori
            ) k
            LEFT JOIN produk p ON k.id_kategori = p.id_kategori
            WHERE k.rn = 1
            GROUP BY k.id_kategori, k.nama_kategori, k.deskripsi_kategori, k.icon_kategori
            HAVING product_count > 0
            ORDER BY product_count DESC, LOWER(k.nama_kategori)
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check if category name exists (case-insensitive)
     */
    public function categoryExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $sql = "
                SELECT 1 FROM kategori 
                WHERE LOWER(nama_kategori) = LOWER(?) 
                AND id_kategori != ?
                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $excludeId]);
        } else {
            $sql = "
                SELECT 1 FROM kategori 
                WHERE LOWER(nama_kategori) = LOWER(?)
                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name]);
        }
        return $stmt->fetch() !== false;
    }

    /**
     * Get MySQL version info
     */
    public function getDatabaseInfo(): array
    {
        $stmt = $this->db->query("SELECT VERSION() as version");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return [
            'version' => $result['version'] ?? 'Unknown',
            'supports_window_functions' => true, // MySQL 8.0+
            'supports_cte' => true, // MySQL 8.0+
            'supports_json' => true // MySQL 5.7+
        ];
    }
}
