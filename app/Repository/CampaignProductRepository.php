<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class CampaignProductRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Get products by campaign with discount details
     * Fixed: Changed column names to match actual database schema
     */
    public function getProductsByCampaign(string $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT kp.*, 
                   p.nama_product, p.harga, p.stok, p.gambar, p.status_produk,
                   k.nama_kategori, 
                   b.nama_brand,
                   d.id_diskon, 
                   d.jenis AS tipe_diskon, 
                   d.nilai AS nilai_diskon, 
                   d.harga_diskon, 
                   d.stok_promo, 
                   d.stok_terpakai
            FROM kampanye_produk kp
            JOIN products p ON kp.id_produk = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN promo_diskon d ON kp.id_diskon = d.id_diskon
            WHERE kp.id_kampanye = :id_kampanye
            ORDER BY kp.prioritas DESC, kp.dibuat_pada ASC
        ");
        $stmt->execute(['id_kampanye' => $campaignId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get campaigns by product
     */
    public function getCampaignsByProduct(string $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT kp.*, 
                   pk.judul, pk.slug, pk.tipe, pk.status, 
                   pk.mulai_pada, pk.selesai_pada, pk.banner
            FROM kampanye_produk kp
            JOIN promo_kampanye pk ON kp.id_kampanye = pk.id_kampanye
            WHERE kp.id_produk = :id_produk
            ORDER BY pk.mulai_pada DESC
        ");
        $stmt->execute(['id_produk' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get active campaigns by product with discount info
     * Fixed: Changed column names to match actual database schema
     */
    public function getActiveCampaignsByProduct(string $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT kp.*, 
                   pk.judul, pk.slug, pk.tipe, pk.banner,
                   d.id_diskon, 
                   d.jenis AS tipe_diskon, 
                   d.nilai AS nilai_diskon, 
                   d.harga_diskon
            FROM kampanye_produk kp
            JOIN promo_kampanye pk ON kp.id_kampanye = pk.id_kampanye
            LEFT JOIN promo_diskon d ON kp.id_diskon = d.id_diskon
            WHERE kp.id_produk = :id_produk
              AND pk.status = 'aktif'
              AND NOW() BETWEEN pk.mulai_pada AND pk.selesai_pada
            ORDER BY d.nilai DESC
        ");
        $stmt->execute(['id_produk' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Attach product to campaign
     */
    public function attachProduct(string $campaignId, string $productId, ?string $discountId = null, int $priority = 0): array
    {
        if ($this->isProductAttached($campaignId, $productId)) {
            return ['success' => false, 'message' => 'Produk sudah ditambahkan ke kampanye ini'];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO kampanye_produk (id_kampanye, id_produk, id_diskon, prioritas)
                VALUES (:id_kampanye, :id_produk, :id_diskon, :prioritas)
            ");

            $success = $stmt->execute([
                'id_kampanye' => $campaignId,
                'id_produk' => $productId,
                'id_diskon' => $discountId,
                'prioritas' => $priority
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Produk berhasil ditambahkan ke kampanye'];
            }

            return ['success' => false, 'message' => 'Gagal menambahkan produk'];
        } catch (\PDOException $e) {
            error_log('CampaignProductRepository::attachProduct - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan produk ke kampanye. Silakan coba lagi.'];
        }
    }

    /**
     * Detach product from campaign
     */
    public function detachProduct(string $campaignId, string $productId): array
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM kampanye_produk 
                WHERE id_kampanye = :id_kampanye AND id_produk = :id_produk
            ");

            $success = $stmt->execute([
                'id_kampanye' => $campaignId,
                'id_produk' => $productId
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Produk berhasil dihapus dari kampanye'];
            }

            return ['success' => false, 'message' => 'Produk tidak ditemukan di kampanye'];
        } catch (\PDOException $e) {
            error_log('CampaignProductRepository::detachProduct - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus produk dari kampanye. Silakan coba lagi.'];
        }
    }

    /**
     * Attach multiple products to campaign
     */
    public function attachMultipleProducts(string $campaignId, array $productIds, ?string $discountId = null): array
    {
        $successCount = 0;
        $errors = [];

        foreach ($productIds as $index => $productId) {
            $result = $this->attachProduct($campaignId, $productId, $discountId, count($productIds) - $index);
            if ($result['success']) {
                $successCount++;
            } else {
                $errors[] = $productId . ': ' . $result['message'];
            }
        }

        if ($successCount === count($productIds)) {
            return ['success' => true, 'message' => "Berhasil menambahkan {$successCount} produk ke kampanye"];
        }

        return [
            'success' => $successCount > 0,
            'message' => "Berhasil: {$successCount}, Gagal: " . count($errors),
            'errors' => $errors
        ];
    }

    /**
     * Detach all products from campaign
     */
    public function detachAllProducts(string $campaignId): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM kampanye_produk WHERE id_kampanye = :id_kampanye");
            $stmt->execute(['id_kampanye' => $campaignId]);

            return ['success' => true, 'message' => 'Semua produk berhasil dihapus dari kampanye'];
        } catch (\PDOException $e) {
            error_log('CampaignProductRepository::detachAllProducts - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghapus produk dari kampanye. Silakan coba lagi.'];
        }
    }

    /**
     * Update product discount in campaign
     */
    public function updateProductDiscount(string $campaignId, string $productId, ?string $discountId): array
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE kampanye_produk 
                SET id_diskon = :id_diskon
                WHERE id_kampanye = :id_kampanye AND id_produk = :id_produk
            ");

            $success = $stmt->execute([
                'id_kampanye' => $campaignId,
                'id_produk' => $productId,
                'id_diskon' => $discountId
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Diskon produk berhasil diperbarui'];
            }

            return ['success' => false, 'message' => 'Gagal memperbarui diskon produk'];
        } catch (\PDOException $e) {
            error_log('CampaignProductRepository::updateProductDiscount - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui diskon produk. Silakan coba lagi.'];
        }
    }

    /**
     * Update product priority in campaign
     */
    public function updatePriority(string $campaignId, string $productId, int $priority): array
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE kampanye_produk 
                SET prioritas = :prioritas
                WHERE id_kampanye = :id_kampanye AND id_produk = :id_produk
            ");

            $success = $stmt->execute([
                'id_kampanye' => $campaignId,
                'id_produk' => $productId,
                'prioritas' => $priority
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'Prioritas berhasil diperbarui'];
            }

            return ['success' => false, 'message' => 'Gagal memperbarui prioritas'];
        } catch (\PDOException $e) {
            error_log('CampaignProductRepository::updatePriority - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui prioritas. Silakan coba lagi.'];
        }
    }

    /**
     * Update multiple priorities in bulk
     */
    public function updateBulkPriorities(string $campaignId, array $productPriorities): array
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE kampanye_produk 
                SET prioritas = :prioritas
                WHERE id_kampanye = :id_kampanye AND id_produk = :id_produk
            ");

            foreach ($productPriorities as $productId => $priority) {
                $stmt->execute([
                    'id_kampanye' => $campaignId,
                    'id_produk' => $productId,
                    'prioritas' => $priority
                ]);
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Prioritas berhasil diperbarui'];
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log('CampaignProductRepository::updateBulkPriorities - ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui prioritas. Silakan coba lagi.'];
        }
    }

    /**
     * Check if product is attached to campaign
     */
    public function isProductAttached(string $campaignId, string $productId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM kampanye_produk 
            WHERE id_kampanye = :id_kampanye AND id_produk = :id_produk
        ");
        $stmt->execute(['id_kampanye' => $campaignId, 'id_produk' => $productId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Count products by campaign
     */
    public function countProductsByCampaign(string $campaignId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM kampanye_produk WHERE id_kampanye = :id");
        $stmt->execute(['id' => $campaignId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Count campaigns by product
     */
    public function countCampaignsByProduct(string $productId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM kampanye_produk WHERE id_produk = :id");
        $stmt->execute(['id' => $productId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Get products not in campaign (for adding products)
     */
    public function getProductsNotInCampaign(string $campaignId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id_product, p.nama_product, p.harga, p.stok, p.gambar, p.status_produk,
                   k.nama_kategori, b.nama_brand
            FROM products p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            WHERE p.id_product NOT IN (
                SELECT id_produk FROM kampanye_produk WHERE id_kampanye = :id_kampanye
            )
            AND p.status_produk != 'nonaktif'
            ORDER BY p.nama_product ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':id_kampanye', $campaignId, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get campaign products with statistics
     */
    public function getCampaignProductsWithStats(string $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT kp.*, 
                   p.nama_product, p.harga, p.stok, p.gambar, p.status_produk,
                   k.nama_kategori, 
                   b.nama_brand,
                   d.id_diskon, d.jenis AS tipe_diskon, d.nilai AS nilai_diskon, d.harga_diskon,
                   COALESCE(
                       (SELECT COUNT(*) FROM order_detail oi 
                        JOIN orders o ON oi.id_order = o.id_order
                        WHERE oi.id_product = p.id_product 
                        AND o.status_order != 'dibatalkan'),
                       0
                   ) as total_penjualan,
                   COALESCE(
                       (SELECT SUM(oi.jumlah) FROM order_detail oi 
                        JOIN orders o ON oi.id_order = o.id_order
                        WHERE oi.id_product = p.id_product 
                        AND o.status_order != 'dibatalkan'),
                       0
                   ) as total_qty_terjual
            FROM kampanye_produk kp
            JOIN products p ON kp.id_produk = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN promo_diskon d ON kp.id_diskon = d.id_diskon
            WHERE kp.id_kampanye = :id_kampanye
            ORDER BY kp.prioritas DESC, kp.dibuat_pada ASC
        ");
        $stmt->execute(['id_kampanye' => $campaignId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Search products for adding to campaign
     */
    public function searchProductsForCampaign(string $campaignId, string $keyword, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id_product, p.nama_product, p.harga, p.stok, p.gambar, p.status_produk,
                   k.nama_kategori, b.nama_brand
            FROM products p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            WHERE p.id_product NOT IN (
                SELECT id_produk FROM kampanye_produk WHERE id_kampanye = :id_kampanye
            )
            AND p.status_produk != 'nonaktif'
            AND (
                p.nama_product LIKE :keyword OR
                k.nama_kategori LIKE :keyword OR
                b.nama_brand LIKE :keyword
            )
            ORDER BY p.nama_product ASC
            LIMIT :limit
        ");

        $searchTerm = '%' . $keyword . '%';
        $stmt->bindValue(':id_kampanye', $campaignId, \PDO::PARAM_STR);
        $stmt->bindValue(':keyword', $searchTerm, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
