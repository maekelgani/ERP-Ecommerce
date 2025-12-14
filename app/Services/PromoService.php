<?php

namespace App\Services;

use App\Database\DatabaseConnection;
use App\Repository\PromoCampaignRepository;
use App\Repository\DiskonRepository;
use App\Repository\VoucherRepository;
use App\Repository\VoucherUsageRepository;
use App\Repository\CampaignProductRepository;

class PromoService
{
    private \PDO $db;
    private PromoCampaignRepository $campaignRepo;
    private DiskonRepository $diskonRepo;
    private VoucherRepository $voucherRepo;
    private VoucherUsageRepository $usageRepo;
    private CampaignProductRepository $cpRepo;

    private bool $allowStacking = false;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
        $this->campaignRepo = new PromoCampaignRepository();
        $this->diskonRepo = new DiskonRepository();
        $this->voucherRepo = new VoucherRepository();
        $this->usageRepo = new VoucherUsageRepository();
        $this->cpRepo = new CampaignProductRepository();
    }

    public function calculateBestDiscount(array $cart, ?int $userId = null, ?string $voucherCode = null): array
    {
        $result = [
            'original_total' => 0,
            'product_discounts' => [],
            'voucher_discount' => 0,
            'shipping_discount' => 0,
            'total_discount' => 0,
            'final_total' => 0,
            'applied_voucher' => null,
            'breakdown' => []
        ];

        $productIds = [];
        $categoryIds = [];

        foreach ($cart as $item) {
            $result['original_total'] += $item['price'] * $item['quantity'];
            $productIds[] = $item['product_id'];
            if (!empty($item['category_id'])) {
                $categoryIds[] = $item['category_id'];
            }
        }

        foreach ($cart as $index => $item) {
            $productDiscount = $this->getProductDiscount($item['product_id'], $item['price'], $item['quantity'], $userId);

            if ($productDiscount['discount'] > 0) {
                $result['product_discounts'][$item['product_id']] = $productDiscount;
                $result['total_discount'] += $productDiscount['discount'];
                $result['breakdown'][] = [
                    'type' => 'product_discount',
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'] ?? 'Produk',
                    'discount' => $productDiscount['discount'],
                    'label' => $productDiscount['label'] ?? 'Diskon'
                ];
            }
        }

        if ($voucherCode) {
            $cartTotal = $result['original_total'] - $result['total_discount'];
            $voucherResult = $this->voucherRepo->validateVoucher(
                $voucherCode,
                $userId,
                $cartTotal,
                $productIds,
                $categoryIds
            );

            if ($voucherResult['valid']) {
                $voucher = $voucherResult['voucher'];

                if ($voucher['jenis'] === 'gratis_ongkir') {
                    $result['shipping_discount'] = $cart['shipping_cost'] ?? 0;
                    $result['breakdown'][] = [
                        'type' => 'shipping_discount',
                        'discount' => $result['shipping_discount'],
                        'label' => 'Gratis Ongkir'
                    ];
                } else {
                    $voucherDiscount = $voucherResult['discount_amount'];

                    if (!$this->allowStacking && $result['total_discount'] > 0) {
                        if ($voucherDiscount > $result['total_discount']) {
                            $result['product_discounts'] = [];
                            $result['breakdown'] = array_filter($result['breakdown'], fn($b) => $b['type'] !== 'product_discount');
                            $result['total_discount'] = $voucherDiscount;
                            $result['voucher_discount'] = $voucherDiscount;
                        }
                    } else {
                        $result['voucher_discount'] = $voucherDiscount;
                        $result['total_discount'] += $voucherDiscount;
                    }

                    if ($result['voucher_discount'] > 0) {
                        $result['breakdown'][] = [
                            'type' => 'voucher_discount',
                            'discount' => $result['voucher_discount'],
                            'label' => $voucher['judul'] ?? $voucher['kode']
                        ];
                    }
                }

                $result['applied_voucher'] = $voucher;
            } else {
                $result['voucher_error'] = $voucherResult['message'];
            }
        }

        $result['total_discount'] += $result['shipping_discount'];
        $result['final_total'] = max(0, $result['original_total'] - $result['total_discount']);

        return $result;
    }

    private function getProductDiscount(string $productId, float $price, int $quantity, ?int $userId): array
    {
        $discounts = $this->diskonRepo->getActiveDiscountsByProduct($productId);

        if (empty($discounts)) {
            $campaigns = $this->cpRepo->getActiveCampaignsByProduct($productId);
            foreach ($campaigns as $cp) {
                if ($cp['id_diskon']) {
                    $campaignDiscount = $this->diskonRepo->getById($cp['id_diskon']);
                    if ($campaignDiscount && $campaignDiscount['status'] === 'aktif') {
                        $discounts[] = $campaignDiscount;
                    }
                }
            }
        }

        if (empty($discounts)) {
            return ['discount' => 0, 'label' => null];
        }

        $bestDiscount = null;
        $bestValue = 0;

        foreach ($discounts as $discount) {
            if ($userId && $discount['maks_qty_per_pengguna']) {
                continue;
            }

            if ($discount['stok_promo'] !== null && $discount['stok_terpakai'] >= $discount['stok_promo']) {
                continue;
            }

            $discountValue = $this->calculateDiscountValue($discount, $price);
            if ($discountValue > $bestValue) {
                $bestValue = $discountValue;
                $bestDiscount = $discount;
            }
        }

        if (!$bestDiscount) {
            return ['discount' => 0, 'label' => null];
        }

        $totalDiscount = $bestValue * $quantity;

        return [
            'discount' => $totalDiscount,
            'discount_per_item' => $bestValue,
            'discount_id' => $bestDiscount['id_diskon'],
            'label' => $bestDiscount['label'],
            'jenis' => $bestDiscount['jenis'],
            'nilai' => $bestDiscount['nilai'],
            'original_price' => $price,
            'discounted_price' => $price - $bestValue
        ];
    }

    private function calculateDiscountValue(array $discount, float $price): float
    {
        if ($discount['jenis'] === 'persen') {
            return $price * ($discount['nilai'] / 100);
        }
        return min($discount['nilai'], $price);
    }

    public function applyPromoToOrder(string $orderId, array $cart, ?int $userId, ?string $voucherCode = null): array
    {
        try {
            $this->db->beginTransaction();

            $promoResult = $this->calculateBestDiscount($cart, $userId, $voucherCode);

            foreach ($promoResult['product_discounts'] as $productId => $discount) {
                if (!empty($discount['discount_id'])) {
                    $reserved = $this->diskonRepo->decrementStock($discount['discount_id']);
                    if (!$reserved) {
                        throw new \Exception("Stok promo untuk produk {$productId} tidak tersedia");
                    }
                }
            }

            if ($promoResult['applied_voucher']) {
                $voucher = $promoResult['applied_voucher'];

                $reserved = $this->voucherRepo->decrementQuota($voucher['id_voucher']);
                if (!$reserved) {
                    throw new \Exception("Kuota voucher sudah habis");
                }

                $this->usageRepo->create([
                    'id_voucher' => $voucher['id_voucher'],
                    'id_pengguna' => $userId,
                    'id_pesanan' => $orderId,
                    'jumlah_diskon' => $promoResult['voucher_discount']
                ]);
            }

            $this->logPromoUsage($orderId, $promoResult, $userId);

            $this->db->commit();

            return [
                'success' => true,
                'promo_result' => $promoResult
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getProductWithPromo(string $productId): array
    {
        $product = $this->getProductDetails($productId);
        if (!$product) {
            return [];
        }

        $bestDiscount = $this->diskonRepo->getBestDiscountForProduct($productId);
        $activeCampaigns = $this->cpRepo->getActiveCampaignsByProduct($productId);

        $promoInfo = [
            'has_discount' => false,
            'discount' => null,
            'discounted_price' => $product['harga'],
            'campaigns' => [],
            'badge' => null,
            'countdown' => null
        ];

        if ($bestDiscount) {
            $promoInfo['has_discount'] = true;
            $promoInfo['discount'] = $bestDiscount;
            $promoInfo['discounted_price'] = $bestDiscount['harga_diskon'] ??
                ($product['harga'] - $this->calculateDiscountValue($bestDiscount, $product['harga']));
            $promoInfo['badge'] = $bestDiscount['label'] ??
                ($bestDiscount['jenis'] === 'persen' ? $bestDiscount['nilai'] . '% OFF' : 'Hemat Rp ' . number_format($bestDiscount['nilai'], 0, ',', '.'));

            if ($bestDiscount['selesai_pada']) {
                $promoInfo['countdown'] = strtotime($bestDiscount['selesai_pada']);
            }
        }

        foreach ($activeCampaigns as $campaign) {
            $promoInfo['campaigns'][] = [
                'id' => $campaign['id_kampanye'],
                'judul' => $campaign['judul'],
                'slug' => $campaign['slug'],
                'tipe' => $campaign['tipe']
            ];
        }

        return array_merge($product, ['promo' => $promoInfo]);
    }

    public function getPromoLandingData(): array
    {
        $activeCampaigns = $this->campaignRepo->getActiveCampaigns();
        $result = [];

        foreach ($activeCampaigns as $campaign) {
            $products = $this->cpRepo->getProductsByCampaign($campaign['id_kampanye']);
            $countdown = strtotime($campaign['selesai_pada']) - time();

            $result[] = [
                'campaign' => $campaign,
                'products' => array_slice($products, 0, 8),
                'total_products' => count($products),
                'countdown_seconds' => max(0, $countdown)
            ];
        }

        return $result;
    }

    public function getCampaignDetailBySlug(string $slug): ?array
    {
        $campaign = $this->campaignRepo->getBySlug($slug);
        if (!$campaign || $campaign['status'] !== 'aktif') {
            return null;
        }

        $products = $this->cpRepo->getProductsByCampaign($campaign['id_kampanye']);

        foreach ($products as &$product) {
            if ($product['id_diskon']) {
                $product['has_discount'] = true;
                $product['badge'] = $product['diskon_jenis'] === 'persen'
                    ? $product['diskon_nilai'] . '% OFF'
                    : 'Hemat Rp ' . number_format($product['diskon_nilai'], 0, ',', '.');
            }
        }

        return [
            'campaign' => $campaign,
            'products' => $products,
            'countdown_seconds' => max(0, strtotime($campaign['selesai_pada']) - time())
        ];
    }

    public function getAvailableVouchersForUser(?int $userId, float $cartTotal = 0): array
    {
        $vouchers = $this->voucherRepo->getActiveVouchers();
        $available = [];

        foreach ($vouchers as $voucher) {
            $usable = true;
            $reason = '';

            if ($voucher['minimal_belanja'] > 0 && $cartTotal < $voucher['minimal_belanja']) {
                $usable = false;
                $reason = 'Minimal belanja Rp ' . number_format($voucher['minimal_belanja'], 0, ',', '.');
            }

            if ($usable && $userId && $voucher['kuota_per_pengguna']) {
                $userUsage = $this->voucherRepo->getUserUsageCount($voucher['id_voucher'], $userId);
                if ($userUsage >= $voucher['kuota_per_pengguna']) {
                    $usable = false;
                    $reason = 'Batas penggunaan tercapai';
                }
            }

            $available[] = array_merge($voucher, [
                'usable' => $usable,
                'reason' => $reason
            ]);
        }

        return $available;
    }

    private function getProductDetails(string $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id_product = :id");
        $stmt->execute(['id' => $productId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    private function logPromoUsage(string $orderId, array $promoResult, ?int $userId): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO log_promo (level, aksi, entitas, id_entitas, id_admin, pesan, meta)
            VALUES ('info', 'promo_applied', 'order', :order_id, :user_id, :pesan, :meta)
        ");

        $stmt->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
            'pesan' => 'Promo diterapkan ke pesanan',
            'meta' => json_encode([
                'total_discount' => $promoResult['total_discount'],
                'voucher_code' => $promoResult['applied_voucher']['kode'] ?? null,
                'product_discounts_count' => count($promoResult['product_discounts'])
            ])
        ]);
    }

    public function updateAllPromoStatus(): void
    {
        $this->db->query("SELECT update_promo_status()");
    }

    public function setAllowStacking(bool $allow): void
    {
        $this->allowStacking = $allow;
    }
}
