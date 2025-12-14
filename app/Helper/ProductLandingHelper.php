<?php

namespace App\Helper;

use App\Database\DatabaseConnection;

class ProductLandingHelper
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getNewProducts(int $limit = 8): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.status_produk != 'nonaktif'
            ORDER BY p.tanggal_ditambahkan DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBestSellers(int $limit = 4): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.status_produk = 'tersedia' AND p.stok > 0
            ORDER BY p.harga DESC, p.tanggal_ditambahkan DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductsForCatalog(int $limit = 12): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.status_produk != 'nonaktif'
            ORDER BY RAND()
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeaturedProducts(int $limit = 6): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.status_produk = 'tersedia' AND p.stok > 0
            ORDER BY p.harga DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductsByCategory(string $categoryId, int $limit = 8): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_kategori = :category_id AND p.status_produk != 'nonaktif'
            ORDER BY p.tanggal_ditambahkan DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductsByBrand(string $brandId, int $limit = 8): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_brand = :brand_id AND p.status_produk != 'nonaktif'
            ORDER BY p.tanggal_ditambahkan DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':brand_id', $brandId, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalProductCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE status_produk != 'nonaktif'";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetch()['total'];
    }

    public function getAvailableProductCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE status_produk = 'tersedia' AND stok > 0";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetch()['total'];
    }

    public function formatPrice(float $price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public function getProductImageUrl(string $imageName): string
    {
        if (empty($imageName)) {
            return '/assets/images/placeholder-product.png';
        }
        return '/uploads/products/' . $imageName;
    }

    public function getStockBadge(int $stock, string $status): array
    {
        if ($status === 'habis' || $stock <= 0) {
            return [
                'text' => 'Stok Habis',
                'class' => 'bg-red-100 text-red-700',
                'available' => false
            ];
        }

        if ($stock <= 5) {
            return [
                'text' => 'Sisa ' . $stock,
                'class' => 'bg-amber-100 text-amber-700',
                'available' => true
            ];
        }

        return [
            'text' => 'Tersedia',
            'class' => 'bg-emerald-100 text-emerald-700',
            'available' => true
        ];
    }

    public function getProductById(string $productId): ?array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, k.id_kategori as kategori_id, 
                   b.nama_brand, b.logo_brand, b.id_brand as brand_id
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_product = :product_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function getAllProducts(array $filters = [], string $sort = 'newest', int $page = 1, int $perPage = 12): array
    {
        $where = ["p.status_produk != 'nonaktif'"];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = "p.id_kategori = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['brand'])) {
            $where[] = "p.id_brand = :brand";
            $params[':brand'] = $filters['brand'];
        }

        if (!empty($filters['availability'])) {
            if ($filters['availability'] === 'in_stock') {
                $where[] = "p.stok > 0 AND p.status_produk = 'tersedia'";
            } elseif ($filters['availability'] === 'out_stock') {
                $where[] = "(p.stok <= 0 OR p.status_produk = 'habis')";
            }
        }

        if (!empty($filters['min_price'])) {
            $where[] = "p.harga >= :min_price";
            $params[':min_price'] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "p.harga <= :max_price";
            $params[':max_price'] = (float) $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.nama_product LIKE :search1 OR p.deskripsi_speksifikasi LIKE :search2 OR EXISTS (SELECT 1 FROM brand b2 WHERE b2.id_brand = p.id_brand AND b2.nama_brand LIKE :search3) OR EXISTS (SELECT 1 FROM kategori k2 WHERE k2.id_kategori = p.id_kategori AND k2.nama_kategori LIKE :search4))";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
            $params[':search4'] = $searchTerm;
        }

        $whereClause = implode(' AND ', $where);

        $orderBy = match ($sort) {
            'price_low' => 'p.harga ASC',
            'price_high' => 'p.harga DESC',
            'name_asc' => 'p.nama_product ASC',
            'name_desc' => 'p.nama_product DESC',
            default => 'p.tanggal_ditambahkan DESC'
        };

        $countSql = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalItems = (int) $countStmt->fetch()['total'];

        $totalPages = ceil($totalItems / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE {$whereClause}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'products' => $stmt->fetchAll(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ];
    }

    public function getAllCategories(): array
    {
        $sql = "
            SELECT k.id_kategori, k.nama_kategori, k.icon_kategori,
                   COUNT(p.id_product) as product_count
            FROM kategori k
            LEFT JOIN products p ON k.id_kategori = p.id_kategori AND p.status_produk != 'nonaktif'
            GROUP BY k.id_kategori, k.nama_kategori, k.icon_kategori
            ORDER BY k.nama_kategori ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllBrands(): array
    {
        $sql = "
            SELECT b.id_brand, b.nama_brand, b.logo_brand,
                   COUNT(p.id_product) as product_count
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand AND p.status_produk != 'nonaktif'
            GROUP BY b.id_brand, b.nama_brand, b.logo_brand
            ORDER BY b.nama_brand ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryById(string $categoryId): ?array
    {
        $sql = "SELECT * FROM kategori WHERE id_kategori = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $categoryId, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBrandById(string $brandId): ?array
    {
        $sql = "SELECT * FROM brand WHERE id_brand = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $brandId, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getRelatedProducts(string $productId, string $categoryId, int $limit = 4): array
    {
        $sql = "
            SELECT p.*, k.nama_kategori, b.nama_brand, b.logo_brand
            FROM products p 
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
            LEFT JOIN brand b ON p.id_brand = b.id_brand 
            WHERE p.id_kategori = :category_id 
              AND p.id_product != :product_id 
              AND p.status_produk != 'nonaktif'
            ORDER BY RAND()
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, \PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $productId, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function calculateDiscount(float $originalPrice, float $discountPercent = 0): array
    {
        $discountAmount = $originalPrice * ($discountPercent / 100);
        $finalPrice = $originalPrice - $discountAmount;

        return [
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'formatted_original' => $this->formatPrice($originalPrice),
            'formatted_final' => $this->formatPrice($finalPrice)
        ];
    }
}
