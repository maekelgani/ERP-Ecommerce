<?php

namespace App\Helper;

use App\Database\DatabaseConnection;

class BrandLandingHelper
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAllBrands(): array
    {
        $sql = "
            SELECT b.*, 
                   COUNT(p.id_product) as product_count
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand AND p.status_produk != 'nonaktif'
            GROUP BY b.id_brand, b.nama_brand, b.desc_brand, b.logo_brand, b.website
            ORDER BY b.nama_brand ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeaturedBrands(int $limit = 8): array
    {
        $sql = "
            SELECT b.*, 
                   COUNT(p.id_product) as product_count
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand AND p.status_produk != 'nonaktif'
            GROUP BY b.id_brand, b.nama_brand, b.desc_brand, b.logo_brand, b.website
            HAVING COUNT(p.id_product) > 0
            ORDER BY product_count DESC, b.nama_brand ASC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBrandsWithProducts(): array
    {
        $sql = "
            SELECT b.*, 
                   COUNT(p.id_product) as product_count
            FROM brand b
            INNER JOIN products p ON b.id_brand = p.id_brand AND p.status_produk != 'nonaktif'
            GROUP BY b.id_brand, b.nama_brand, b.desc_brand, b.logo_brand, b.website
            ORDER BY product_count DESC, b.nama_brand ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBrandById(string $brandId): ?array
    {
        $sql = "
            SELECT b.*, 
                   COUNT(p.id_product) as product_count
            FROM brand b
            LEFT JOIN products p ON b.id_brand = p.id_brand AND p.status_produk != 'nonaktif'
            WHERE b.id_brand = :brand_id
            GROUP BY b.id_brand, b.nama_brand, b.desc_brand, b.logo_brand, b.website
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':brand_id', $brandId, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getTotalBrandCount(): int
    {
        $sql = "SELECT COUNT(*) as total FROM brand";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetch()['total'];
    }

    public function getBrandLogoUrl(string $logoName): string
    {
        if (empty($logoName)) {
            return '/assets/img/placeholder-brand.png';
        }
        return '/uploads/brands/' . $logoName;
    }

    public function getBrandsForSlider(): array
    {
        $brands = $this->getAllBrands();

        if (count($brands) < 4) {
            $duplicates = [];
            $targetCount = 8;
            while (count($duplicates) < $targetCount) {
                foreach ($brands as $brand) {
                    $duplicates[] = $brand;
                    if (count($duplicates) >= $targetCount) break;
                }
            }
            return $duplicates;
        }

        return $brands;
    }
}
