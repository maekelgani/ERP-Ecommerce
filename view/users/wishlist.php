<?php
$pageTitle = "Wishlist Saya";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

if (!$isLoggedIn) {
    header('Location: ../../view/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

include '../../components/users/head.php';
?>

<style>
    #customToastContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
    }

    .custom-toast {
        position: relative;
        min-width: 320px;
        max-width: 330px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(120%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    }

    .custom-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .custom-toast.hiding {
        transform: translateX(120%);
        opacity: 0;
        margin-top: -70px;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), margin-top 0.3s ease 0.2s;
    }

    .custom-toast.success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .custom-toast.error {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }

    .custom-toast.warning {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        color: white;
    }

    .custom-toast.info {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        color: white;
    }

    .custom-toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .custom-toast-content {
        flex: 1;
    }

    .custom-toast-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .custom-toast-message {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .custom-toast-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .custom-toast-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .custom-toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 0 0 12px 12px;
        animation: toast-progress 4s linear forwards;
    }

    @keyframes toast-progress {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    @media (max-width: 480px) {
        #customToastContainer {
            left: 10px;
            right: 10px;
            top: 10px;
        }

        .custom-toast {
            min-width: unset;
            max-width: unset;
            width: 100%;
        }
    }
</style>

<?php

use App\Helper\ProductLandingHelper;
use App\Helper\DiscountHelper;

$productHelper = new ProductLandingHelper();
$discountHelper = new DiscountHelper();

$wishlistItems = [];
$categories = [];
$brands = [];

if (isset($customer['id_customer'])) {
    try {
        $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("
            SELECT 
                p.*, 
                k.nama_kategori, 
                b.nama_brand, 
                w.tanggal_ditambahkan as added_at,
                pd.id_diskon,
                pd.jenis as tipe_diskon,
                pd.nilai as nilai_diskon,
                pd.mulai_pada as diskon_mulai,
                pd.selesai_pada as diskon_berakhir,
                pd.status as diskon_status,
                pd.stok_promo,
                pd.label as diskon_label,
                (SELECT SUM(od.jumlah) FROM order_detail od JOIN orders o ON od.id_order = o.id_order WHERE od.id_product = p.id_product AND o.status_order = 'selesai') as total_terjual,
                (SELECT AVG(r.rating) FROM review r WHERE r.id_product = p.id_product AND r.status_review = 'approved') as avg_rating,
                (SELECT COUNT(r.id_review) FROM review r WHERE r.id_product = p.id_product AND r.status_review = 'approved') as total_reviews
            FROM wishlist w
            JOIN products p ON w.id_product = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN promo_diskon pd ON p.id_product = pd.id_produk 
                AND pd.status = 'aktif' 
                AND (pd.mulai_pada IS NULL OR pd.mulai_pada <= :now1)
                AND (pd.selesai_pada IS NULL OR pd.selesai_pada >= :now2)
                AND (pd.stok_promo IS NULL OR pd.stok_promo > 0)
            WHERE w.id_customer = :customer_id
            ORDER BY w.tanggal_ditambahkan DESC
        ");
        $stmt->execute([
            ':customer_id' => $customer['id_customer'],
            ':now1' => $now,
            ':now2' => $now
        ]);
        $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($wishlistItems as $item) {
            if (!empty($item['nama_kategori']) && !in_array($item['nama_kategori'], $categories)) {
                $categories[] = $item['nama_kategori'];
            }
            if (!empty($item['nama_brand']) && !in_array($item['nama_brand'], $brands)) {
                $brands[] = $item['nama_brand'];
            }
        }
        sort($categories);
        sort($brands);
    } catch (Exception $e) {
        error_log('Wishlist error: ' . $e->getMessage());
    }
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Wishlist', 'url' => null]
];
?>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]">
        <div class="w-full px-4 md:px-8 lg:px-20 py-6">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <?php if (!empty($wishlistItems)): ?>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <p class="text-gray-600"><?= count($wishlistItems) ?> produk dalam wishlist</p>
                    <button onclick="clearAllWishlist()" class="text-sm text-red-500 hover:text-red-600 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Semua
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" id="searchWishlist" placeholder="Cari produk di wishlist..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <?php if (!empty($categories)): ?>
                                <select id="filterCategory" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all min-w-[140px]">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                            <?php if (!empty($brands)): ?>
                                <select id="filterBrand" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all min-w-[140px]">
                                    <option value="">Semua Brand</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?= htmlspecialchars($brand) ?>"><?= htmlspecialchars($brand) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>

                            <select id="sortWishlist" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all min-w-[160px]">
                                <option value="newest">Terbaru Ditambahkan</option>
                                <option value="oldest">Terlama Ditambahkan</option>
                                <option value="price-low">Harga Terendah</option>
                                <option value="price-high">Harga Tertinggi</option>
                                <option value="name-asc">Nama A-Z</option>
                                <option value="name-desc">Nama Z-A</option>
                            </select>

                            <button id="resetFilters" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                    <div id="filterInfo" class="hidden mt-3 text-sm text-gray-500"></div>
                </div>

                <div id="wishlistGrid" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    <?php foreach ($wishlistItems as $product): ?>
                        <?php
                        $svgPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23f3f4f6'/%3E%3Cpath d='M200 120c-44.18 0-80 35.82-80 80s35.82 80 80 80 80-35.82 80-80-35.82-80-80-80zm0 140c-33.14 0-60-26.86-60-60s26.86-60 60-60 60 26.86 60 60-26.86 60-60 60z' fill='%23d1d5db'/%3E%3Cpath d='M200 160c-22.09 0-40 17.91-40 40s17.91 40 40 40 40-17.91 40-40-17.91-40-40-40z' fill='%23d1d5db'/%3E%3C/svg%3E";
                        $imagePath = !empty($product['gambar']) ? '../../uploads/products/' . htmlspecialchars($product['gambar']) : $svgPlaceholder;
                        $productName = htmlspecialchars($product['nama_product']);

                        $hasDiscount = !empty($product['id_diskon']) && $product['diskon_status'] === 'aktif';
                        $originalPrice = (float)$product['harga'];
                        $discountPrice = null;
                        $discountPercent = 0;
                        $savings = 0;

                        if ($hasDiscount) {
                            $nilaiDiskon = (float)$product['nilai_diskon'];
                            $tipeDiskon = $product['tipe_diskon'];

                            if ($tipeDiskon === 'persen') {
                                $discountPercent = (int)$nilaiDiskon;
                                $discountPrice = $originalPrice - ($originalPrice * $nilaiDiskon / 100);
                            } else {
                                $discountPrice = $originalPrice - $nilaiDiskon;
                                if ($originalPrice > 0) {
                                    $discountPercent = round(($nilaiDiskon / $originalPrice) * 100);
                                }
                            }
                            $discountPrice = max(0, $discountPrice);
                            $savings = $originalPrice - $discountPrice;
                        }

                        $displayPrice = $hasDiscount && $discountPrice !== null ? $discountPrice : $originalPrice;
                        $price = $productHelper->formatPrice($displayPrice);
                        $originalPriceFormatted = $productHelper->formatPrice($originalPrice);
                        $savingsFormatted = $productHelper->formatPrice($savings);

                        $description = htmlspecialchars(substr($product['deskripsi_speksifikasi'] ?? '', 0, 100));
                        $stockBadge = $productHelper->getStockBadge($product['stok'], $product['status_produk']);
                        $productId = $product['id_product'] ?? '';
                        $addedDate = !empty($product['added_at']) ? date('d M Y', strtotime($product['added_at'])) : '-';
                        $categoryName = htmlspecialchars($product['nama_kategori'] ?? '');
                        $brandName = htmlspecialchars($product['nama_brand'] ?? '');
                        $diskonLabel = htmlspecialchars($product['diskon_label'] ?? '');

                        $avgRating = (float)($product['avg_rating'] ?? 0);
                        $totalReviews = (int)($product['total_reviews'] ?? 0);
                        $totalTerjual = (int)($product['total_terjual'] ?? 0);
                        ?>
                        <div class="wishlist-item group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full overflow-hidden"
                            data-product-id="<?= $productId ?>"
                            data-name="<?= strtolower($productName) ?>"
                            data-category="<?= $categoryName ?>"
                            data-brand="<?= $brandName ?>"
                            data-price="<?= $displayPrice ?>"
                            data-added="<?= strtotime($product['added_at'] ?? 'now') ?>">
                            <a href="productDetail.php?id=<?= urlencode($productId) ?>" class="block">
                                <div class="relative aspect-square overflow-hidden">
                                    <img src="<?= $imagePath ?>" alt="<?= $productName ?>" class="w-full h-full object-cover" loading="lazy"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 400 400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Cpath d=%27M200 120c-44.18 0-80 35.82-80 80s35.82 80 80 80 80-35.82 80-80-35.82-80-80-80zm0 140c-33.14 0-60-26.86-60-60s26.86-60 60-60 60 26.86 60 60-26.86 60-60 60z%27 fill=%27%23d1d5db%27/%3E%3Cpath d=%27M200 160c-22.09 0-40 17.91-40 40s17.91 40 40 40 40-17.91 40-40-17.91-40-40-40z%27 fill=%27%23d1d5db%27/%3E%3C/svg%3E'" />

                                    <?php if ($hasDiscount): ?>
                                        <div class="absolute top-2 left-2 flex flex-col gap-1">

                                            <?php if ($tipeDiskon === 'persen' && $discountPercent > 0): ?>
                                                <!-- DISKON PERSEN -->
                                                <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-bold z-10">
                                                    -<?= $discountPercent ?>%
                                                </div>

                                            <?php elseif ($tipeDiskon === 'nominal' && $savings > 0): ?>
                                                <!-- DISKON NOMINAL -->
                                                <div class="bg-gradient-to-r from-red-500 to-red-600 
                        text-white px-2 py-1 rounded-lg text-xs font-bold shadow-sm">
                                                    Hemat <?= $savingsFormatted ?>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    <?php endif; ?>


                                    <?php if (!$stockBadge['available']): ?>
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Stok Habis</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">Lihat Detail</span>
                                        </div>
                                    <?php endif; ?>

                                    <button onclick="event.preventDefault(); event.stopPropagation(); removeFromWishlist('<?= $productId ?>')"
                                        class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 shadow-sm"
                                        title="Hapus dari wishlist">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                        </svg>
                                    </button>
                                </div>
                            </a>
                            <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                <div class="flex-grow">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-baseline gap-2 flex-wrap">
                                            <p class="text-primary font-bold text-lg"><?= $price ?></p>
                                            <?php if ($hasDiscount && $discountPrice !== null && $discountPrice < $originalPrice): ?>
                                                <p class="text-gray-400 text-sm line-through"><?= $originalPriceFormatted ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($hasDiscount && $savings > 0): ?>
                                            <p class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Hemat <?= $savingsFormatted ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="productDetail.php?id=<?= urlencode($productId) ?>">
                                        <h3 class="mt-1.5 text-sm sm:text-base font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors"><?= $productName ?></h3>
                                    </a>

                                    <div class="mt-1.5 flex items-center gap-2">
                                        <div class="flex items-center">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg class="w-3.5 h-3.5 <?= $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-200' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-[10px] text-gray-500">(<?= $totalReviews ?>)</span>
                                        <?php if ($totalTerjual > 0): ?>
                                            <span class="text-gray-300">|</span>
                                            <span class="text-[10px] text-gray-500">Terjual <?= number_format($totalTerjual) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($description)): ?>
                                        <p class="mt-2 text-gray-500 text-xs sm:text-sm line-clamp-2"><?= $description ?>...</p>
                                    <?php endif; ?>
                                    <p class="mt-2 text-xs text-gray-400">Ditambahkan: <?= $addedDate ?></p>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button class="w-10 flex-shrink-0 rounded-lg bg-gray-100 px-2.5 py-2.5 text-sm font-medium text-red-500 transition-all hover:bg-red-50 hover:shadow-sm"
                                        onclick="event.preventDefault(); event.stopPropagation(); removeFromWishlist('<?= $productId ?>')"
                                        title="Hapus dari wishlist">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="w-10 flex-shrink-0 rounded-lg border border-gray-200 px-2.5 py-2.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 hover:border-[#882426] hover:text-[#882426] <?= !$stockBadge['available'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        <?= !$stockBadge['available'] ? 'disabled' : '' ?>
                                        onclick="event.preventDefault(); event.stopPropagation(); addToCartFromWishlist('<?= $productId ?>')"
                                        title="Tambah ke Keranjang">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="flex-1 rounded-lg bg-primary px-3 py-2.5 text-sm font-medium text-white transition-all hover:bg-[#6a1c1e] hover:shadow-md <?= !$stockBadge['available'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        <?= !$stockBadge['available'] ? 'disabled' : '' ?>
                                        onclick="event.preventDefault(); event.stopPropagation(); buyNowFromWishlist('<?= $productId ?>')">
                                        Beli Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="noResults" class="hidden text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                    <p class="text-gray-500 text-sm">Tidak ada produk yang cocok dengan filter Anda.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Wishlist Anda Kosong</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Belum ada produk yang ditambahkan ke wishlist. Mulai jelajahi produk kami dan simpan favorit Anda!</p>
                    <a href="productCollection.php" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Jelajahi Produk
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
    <script>
        function ensureToastContainer() {
            let container = document.getElementById('customToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'customToastContainer';
                document.body.appendChild(container);
            }
            return container;
        }

        function showCustomToast(message, type = 'success', title = '') {
            const container = ensureToastContainer();
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;

            const icons = {
                success: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };

            toast.innerHTML = `
                <div class="custom-toast-icon">
                    ${icons[type] || icons.info}
                </div>
                <div class="custom-toast-content">
                    ${title ? `<div class="custom-toast-title">${title}</div>` : ''}
                    <div class="custom-toast-message">${message}</div>
                </div>
                <button class="custom-toast-close" onclick="this.closest('.custom-toast').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="custom-toast-progress"></div>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            const autoClose = setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            }, 4000);

            toast.querySelector('.custom-toast-close').addEventListener('click', () => {
                clearTimeout(autoClose);
                toast.classList.remove('show');
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 400);
            });
        }

        window.showCustomToast = showCustomToast;

        function updateWishlistCounter() {
            const count = document.querySelectorAll('.wishlist-item:not(.hidden)').length;
            const counterEl = document.querySelector('.wishlist-count-badge');
            if (counterEl) {
                if (count > 0) {
                    counterEl.textContent = count > 99 ? '99+' : count;
                    counterEl.classList.remove('hidden');
                } else {
                    counterEl.classList.add('hidden');
                }
            }
        }

        function removeFromWishlist(productId) {
            if (!confirm('Hapus produk ini dari wishlist?')) return;

            fetch('../../api/wishlist/remove.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`[data-product-id="${productId}"]`);
                        if (card) {
                            card.style.transition = 'opacity 0.3s, transform 0.3s';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                updateWishlistCounter();
                                applyFilters();
                                if (document.querySelectorAll('.wishlist-item').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        showCustomToast(data.message || 'Produk dihapus dari wishlist', 'success', 'Dihapus');
                    } else {
                        showCustomToast(data.message || 'Gagal menghapus dari wishlist', 'error', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCustomToast('Terjadi kesalahan', 'error', 'Error');
                });
        }

        function clearAllWishlist() {
            if (!confirm('Hapus semua produk dari wishlist?')) return;

            fetch('../../api/wishlist/clear.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCustomToast(data.message || 'Semua produk dihapus dari wishlist', 'success', 'Berhasil');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showCustomToast(data.message || 'Gagal menghapus wishlist', 'error', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCustomToast('Terjadi kesalahan', 'error', 'Error');
                });
        }

        function addToCartFromWishlist(productId) {
            fetch('../../api/cart/add.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCustomToast(data.message || 'Produk berhasil ditambahkan ke keranjang!', 'success', 'Ditambahkan');
                        if (data.cart_count !== undefined) {
                            const cartBadges = document.querySelectorAll('.cart-count-badge');
                            cartBadges.forEach(badge => {
                                if (data.cart_count > 0) {
                                    badge.textContent = data.cart_count > 99 ? '99+' : data.cart_count;
                                    badge.classList.remove('hidden');
                                } else {
                                    badge.classList.add('hidden');
                                }
                            });
                        }
                    } else {
                        showCustomToast(data.message || 'Gagal menambahkan ke keranjang', 'error', 'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCustomToast('Terjadi kesalahan', 'error', 'Error');
                });
        }

        function buyNowFromWishlist(productId) {
            const isLoggedIn = document.body.dataset.customerLoggedIn === 'true';
            if (!isLoggedIn) {
                if (typeof showLoginRequiredModal === 'function') {
                    showLoginRequiredModal();
                } else {
                    window.location.href = 'customerLogin.php';
                }
                return;
            }
            window.location.href = 'productCheckout.php?from=buynow&product=' + encodeURIComponent(productId) + '&qty=1';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchWishlist');
            const categoryFilter = document.getElementById('filterCategory');
            const brandFilter = document.getElementById('filterBrand');
            const sortFilter = document.getElementById('sortWishlist');
            const resetBtn = document.getElementById('resetFilters');
            const filterInfo = document.getElementById('filterInfo');
            const noResults = document.getElementById('noResults');
            const grid = document.getElementById('wishlistGrid');

            function applyFilters() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedCategory = categoryFilter ? categoryFilter.value : '';
                const selectedBrand = brandFilter ? brandFilter.value : '';
                const sortBy = sortFilter ? sortFilter.value : 'newest';

                const items = document.querySelectorAll('.wishlist-item');
                let visibleItems = [];

                items.forEach(item => {
                    const name = item.dataset.name || '';
                    const category = item.dataset.category || '';
                    const brand = item.dataset.brand || '';

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;
                    const matchesBrand = !selectedBrand || brand === selectedBrand;

                    if (matchesSearch && matchesCategory && matchesBrand) {
                        item.classList.remove('hidden');
                        visibleItems.push(item);
                    } else {
                        item.classList.add('hidden');
                    }
                });

                visibleItems.sort((a, b) => {
                    switch (sortBy) {
                        case 'newest':
                            return parseInt(b.dataset.added) - parseInt(a.dataset.added);
                        case 'oldest':
                            return parseInt(a.dataset.added) - parseInt(b.dataset.added);
                        case 'price-low':
                            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                        case 'price-high':
                            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                        case 'name-asc':
                            return a.dataset.name.localeCompare(b.dataset.name);
                        case 'name-desc':
                            return b.dataset.name.localeCompare(a.dataset.name);
                        default:
                            return 0;
                    }
                });

                visibleItems.forEach(item => grid.appendChild(item));

                if (visibleItems.length === 0) {
                    noResults.classList.remove('hidden');
                    grid.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    grid.classList.remove('hidden');
                }

                const activeFilters = [];
                if (searchTerm) activeFilters.push(`Pencarian: "${searchTerm}"`);
                if (selectedCategory) activeFilters.push(`Kategori: ${selectedCategory}`);
                if (selectedBrand) activeFilters.push(`Brand: ${selectedBrand}`);

                if (activeFilters.length > 0 && filterInfo) {
                    filterInfo.textContent = `Filter aktif: ${activeFilters.join(', ')} - Menampilkan ${visibleItems.length} produk`;
                    filterInfo.classList.remove('hidden');
                } else if (filterInfo) {
                    filterInfo.classList.add('hidden');
                }
            }

            window.applyFilters = applyFilters;

            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(applyFilters, 300);
                });
            }

            if (categoryFilter) categoryFilter.addEventListener('change', applyFilters);
            if (brandFilter) brandFilter.addEventListener('change', applyFilters);
            if (sortFilter) sortFilter.addEventListener('change', applyFilters);

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (categoryFilter) categoryFilter.value = '';
                    if (brandFilter) brandFilter.value = '';
                    if (sortFilter) sortFilter.value = 'newest';
                    applyFilters();
                });
            }
        });
    </script>
</body>

</html>