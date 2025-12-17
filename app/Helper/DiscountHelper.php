<?php

namespace App\Helper;

use App\Database\DatabaseConnection;

class DiscountHelper
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getActiveDiscountForProduct(string $productId): ?array
    {
        try {
            $currentTime = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                SELECT pd.*, pk.judul as nama_kampanye
                FROM promo_diskon pd
                LEFT JOIN promo_kampanye pk ON pd.id_kampanye = pk.id_kampanye
                WHERE pd.id_produk = :product_id
                  AND pd.status = 'aktif'
                  AND (pd.mulai_pada IS NULL OR :current_time1 >= pd.mulai_pada)
                  AND (pd.selesai_pada IS NULL OR :current_time2 <= pd.selesai_pada)
                  AND (pd.stok_promo IS NULL OR pd.stok_promo > COALESCE(pd.stok_terpakai, 0))
                ORDER BY pd.nilai DESC
                LIMIT 1
            ");
            $stmt->execute([
                'product_id' => $productId,
                'current_time1' => $currentTime,
                'current_time2' => $currentTime
            ]);
            $discount = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $discount ?: null;
        } catch (\PDOException $e) {
            error_log('DiscountHelper::getActiveDiscountForProduct - ' . $e->getMessage());
            return null;
        }
    }

    public function getActiveDiscountsForProducts(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            $currentTime = date('Y-m-d H:i:s');
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            $stmt = $this->db->prepare("
                SELECT pd.*, pk.judul as nama_kampanye
                FROM promo_diskon pd
                LEFT JOIN promo_kampanye pk ON pd.id_kampanye = pk.id_kampanye
                INNER JOIN (
                    SELECT id_produk, MAX(nilai) as max_nilai
                    FROM promo_diskon
                    WHERE id_produk IN ($placeholders)
                      AND status = 'aktif'
                      AND (mulai_pada IS NULL OR ? >= mulai_pada)
                      AND (selesai_pada IS NULL OR ? <= selesai_pada)
                      AND (stok_promo IS NULL OR stok_promo > COALESCE(stok_terpakai, 0))
                    GROUP BY id_produk
                ) best ON pd.id_produk = best.id_produk AND pd.nilai = best.max_nilai
                WHERE pd.status = 'aktif'
                  AND (pd.mulai_pada IS NULL OR ? >= pd.mulai_pada)
                  AND (pd.selesai_pada IS NULL OR ? <= pd.selesai_pada)
                  AND (pd.stok_promo IS NULL OR pd.stok_promo > COALESCE(pd.stok_terpakai, 0))
            ");

            $params = array_merge(
                $productIds,
                [$currentTime, $currentTime],
                [$currentTime, $currentTime]
            );
            $stmt->execute($params);
            $discounts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $result = [];
            foreach ($discounts as $discount) {
                if (!isset($result[$discount['id_produk']])) {
                    $result[$discount['id_produk']] = $discount;
                }
            }

            return $result;
        } catch (\PDOException $e) {
            error_log('DiscountHelper::getActiveDiscountsForProducts - ' . $e->getMessage());
            return [];
        }
    }

    public function calculateDiscountedPrice(float $originalPrice, array $discount): float
    {
        if ($discount['jenis'] === 'persen') {
            return $originalPrice - ($originalPrice * $discount['nilai'] / 100);
        }
        return max(0, $originalPrice - $discount['nilai']);
    }

    public function getDiscountLabel(array $discount): string
    {
        if (!empty($discount['label'])) {
            return $discount['label'];
        }

        if ($discount['jenis'] === 'persen') {
            return 'Diskon ' . (int)$discount['nilai'] . '%';
        }

        return 'Hemat Rp ' . number_format($discount['nilai'], 0, ',', '.');
    }

    public function getDiscountBadge(array $discount): string
    {
        if ($discount['jenis'] === 'persen') {
            return '-' . (int)$discount['nilai'] . '%';
        }
        return '-Rp ' . number_format($discount['nilai'], 0, ',', '.');
    }

    public function applyDiscountToProduct(array $product, ?array $discount = null): array
    {
        if ($discount === null) {
            $discount = $this->getActiveDiscountForProduct($product['id_product']);
        }

        $product['has_discount'] = false;
        $product['discount_info'] = null;
        $product['harga_asli'] = $product['harga'];
        $product['harga_final'] = $product['harga'];
        $product['discount_label'] = null;
        $product['discount_badge'] = null;
        $product['discount_percentage'] = 0;
        $product['savings_per_item'] = 0;

        if ($discount) {
            $discountedPrice = $this->calculateDiscountedPrice($product['harga'], $discount);
            $product['has_discount'] = true;
            $product['discount_info'] = $discount;
            $product['harga_final'] = $discountedPrice;
            $product['discount_label'] = $this->getDiscountLabel($discount);
            $product['discount_badge'] = $this->getDiscountBadge($discount);
            $product['savings_per_item'] = $product['harga'] - $discountedPrice;

            if ($discount['jenis'] === 'persen') {
                $product['discount_percentage'] = (int)$discount['nilai'];
            } else {
                $product['discount_percentage'] = $product['harga'] > 0
                    ? round(($discount['nilai'] / $product['harga']) * 100)
                    : 0;
            }
        }

        return $product;
    }

    public function applyDiscountsToProducts(array $products): array
    {
        if (empty($products)) {
            return $products;
        }

        $productIds = array_column($products, 'id_product');
        $discounts = $this->getActiveDiscountsForProducts($productIds);

        foreach ($products as &$product) {
            $discount = $discounts[$product['id_product']] ?? null;
            $product = $this->applyDiscountToProduct($product, $discount);
        }
        unset($product);

        return $products;
    }

    public function getProductsWithActiveDiscounts(int $limit = 20): array
    {
        try {
            $currentTime = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("
                SELECT p.*, k.nama_kategori, b.nama_brand,
                       pd.id_diskon, pd.label as discount_label, pd.jenis as discount_type,
                       pd.nilai as discount_value, pd.stok_promo, pd.stok_terpakai,
                       pk.judul as campaign_name
                FROM promo_diskon pd
                JOIN products p ON pd.id_produk = p.id_product
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                LEFT JOIN brand b ON p.id_brand = b.id_brand
                LEFT JOIN promo_kampanye pk ON pd.id_kampanye = pk.id_kampanye
                WHERE pd.status = 'aktif'
                  AND (pd.mulai_pada IS NULL OR :current_time1 >= pd.mulai_pada)
                  AND (pd.selesai_pada IS NULL OR :current_time2 <= pd.selesai_pada)
                  AND (pd.stok_promo IS NULL OR pd.stok_promo > COALESCE(pd.stok_terpakai, 0))
                  AND p.status_produk = 'tersedia'
                  AND p.stok > 0
                ORDER BY pd.dibuat_pada DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':current_time1', $currentTime);
            $stmt->bindValue(':current_time2', $currentTime);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($products as &$product) {
                $discount = [
                    'id_diskon' => $product['id_diskon'],
                    'jenis' => $product['discount_type'],
                    'nilai' => $product['discount_value'],
                    'label' => $product['discount_label'],
                    'stok_promo' => $product['stok_promo'],
                    'stok_terpakai' => $product['stok_terpakai']
                ];
                $product = $this->applyDiscountToProduct($product, $discount);
            }
            unset($product);

            return $products;
        } catch (\PDOException $e) {
            error_log('DiscountHelper::getProductsWithActiveDiscounts - ' . $e->getMessage());
            return [];
        }
    }

    public function validateAndApplyDiscount(string $productId, int $quantity, ?int $userId = null): array
    {
        $discount = $this->getActiveDiscountForProduct($productId);

        if (!$discount) {
            return [
                'has_discount' => false,
                'discount' => null,
                'quantity_allowed' => $quantity,
                'message' => null
            ];
        }

        $quantityAllowed = $quantity;
        $message = null;

        if ($discount['stok_promo'] !== null) {
            $remainingStock = $discount['stok_promo'] - ($discount['stok_terpakai'] ?? 0);
            if ($remainingStock <= 0) {
                return [
                    'has_discount' => false,
                    'discount' => null,
                    'quantity_allowed' => $quantity,
                    'message' => 'Stok promo sudah habis'
                ];
            }
            if ($quantity > $remainingStock) {
                $quantityAllowed = $remainingStock;
                $message = "Hanya {$remainingStock} item yang mendapat diskon";
            }
        }

        if ($discount['maks_qty_per_pengguna'] !== null && $quantity > $discount['maks_qty_per_pengguna']) {
            $quantityAllowed = min($quantityAllowed, $discount['maks_qty_per_pengguna']);
            $message = "Maksimal {$discount['maks_qty_per_pengguna']} item per pengguna";
        }

        return [
            'has_discount' => true,
            'discount' => $discount,
            'quantity_allowed' => $quantityAllowed,
            'message' => $message
        ];
    }
}
