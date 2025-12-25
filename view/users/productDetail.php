<?php
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

use App\Helper\ProductLandingHelper;
use App\Helper\DiscountHelper;

$productHelper = new ProductLandingHelper();
$discountHelper = new DiscountHelper();

$productId = $_GET['id'] ?? '';
$product = null;
$relatedProducts = [];

if ($productId) {
    $product = $productHelper->getProductById($productId);
    if ($product) {
        $relatedProducts = $productHelper->getRelatedProducts($productId, $product['id_kategori'] ?? '', 4);
    }
}

if (!$product) {
    header('Location: productCollection.php');
    exit;
}

$pageTitle = htmlspecialchars($product['nama_product']);

$imagePath = '';
$gambar = $product['gambar'] ?? '';
if (empty($gambar)) {
    $imagePath = '../../assets/img/placeholder-product.png';
} elseif (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
    $imagePath = $gambar;
} else {
    $imagePath = '../../uploads/products/' . htmlspecialchars($gambar);
}

$stockBadge = $productHelper->getStockBadge((int)($product['stok'] ?? 0), $product['status_produk'] ?? 'tersedia');
$hasDiscount = !empty($product['has_discount']);
$formattedPrice = $productHelper->formatPrice((float)($product['harga_final'] ?? $product['harga']));
$originalPrice = $productHelper->formatPrice((float)$product['harga']);
$discountBadge = $product['discount_badge'] ?? '';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Produk', 'url' => 'productCollection.php']
];

if (!empty($product['nama_kategori'])) {
    $breadcrumbs[] = ['label' => $product['nama_kategori'], 'url' => 'productCollection.php?category=' . urlencode($product['id_kategori'])];
}

$breadcrumbs[] = ['label' => $product['nama_product'], 'url' => null];

// Fetch product reviews
$reviewsQuery = \App\Database\DatabaseConnection::getInstance()->getConnection()->prepare("
    SELECT 
        r.id_review, r.rating, r.komentar, r.foto_review, r.tanggal_review,
        c.nama_lengkap, c.profile_image
    FROM review r
    JOIN customers c ON r.id_customer = c.id_customer
    WHERE r.id_product = :product_id AND r.status_review = 'approved'
    ORDER BY r.tanggal_review DESC
    LIMIT 50
");
$reviewsQuery->execute([':product_id' => $productId]);
$reviews = $reviewsQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating and review count
$avgRating = 0;
$reviewCount = count($reviews);
$ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

if ($reviewCount > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += (int)$review['rating'];
        $ratingDistribution[(int)$review['rating']]++;
    }
    $avgRating = round($totalRating / $reviewCount, 1);
}

// Fetch total sold count
$soldQuery = \App\Database\DatabaseConnection::getInstance()->getConnection()->prepare("
    SELECT SUM(od.jumlah) as total_terjual
    FROM order_detail od
    JOIN orders o ON od.id_order = o.id_order
    WHERE od.id_product = :product_id AND o.status_order = 'selesai'
");
$soldQuery->execute([':product_id' => $productId]);
$soldResult = $soldQuery->fetch(PDO::FETCH_ASSOC);
$totalSold = (int)($soldResult['total_terjual'] ?? 0);

include '../../components/users/head.php';
?>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]">
        <div class="w-full px-5 md:px-8 lg:px-20 py-6">

            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

                <div class="space-y-4">
                    <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                        <div class="aspect-square">
                            <img id="mainImage"
                                src="<?= $imagePath ?>"
                                alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                class="w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='../../assets/img/placeholder-product.png'">
                        </div>

                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <?php if ($hasDiscount): ?>
                                <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-red-500 text-white">
                                    <?= htmlspecialchars($discountBadge) ?>
                                </span>
                            <?php endif; ?>
                            <span class="px-3 py-1.5 text-xs font-semibold rounded-full <?= $stockBadge['class'] ?>">
                                <?= $stockBadge['text'] ?>
                            </span>
                        </div>

                        <button class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full shadow-md hover:bg-white transition-colors"
                            onclick="addToWishlist('<?= $product['id_product'] ?>')"
                            data-wishlist-product="<?= $product['id_product'] ?>">
                            <svg class="w-5 h-5 text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 pointer-events-none"></div>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <button class="aspect-square bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden p-2 hover:border-primary transition-colors"
                            onclick="changeMainImage('<?= $imagePath ?>')">
                            <img src="<?= $imagePath ?>" alt="Thumbnail 1" class="w-full h-full object-contain" onerror="this.src='../../assets/img/placeholder-product.png'">
                        </button>
                        <div class="aspect-square bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-2 flex items-center justify-center">
                            <img src="<?= $imagePath ?>" alt="Thumbnail 2" class="w-full h-full object-contain opacity-50" onerror="this.src='../../assets/img/placeholder-product.png'">
                        </div>
                        <div class="aspect-square bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-2 flex items-center justify-center">
                            <img src="<?= $imagePath ?>" alt="Thumbnail 3" class="w-full h-full object-contain opacity-50" onerror="this.src='../../assets/img/placeholder-product.png'">
                        </div>
                        <div class="aspect-square bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-2 flex items-center justify-center">
                            <img src="<?= $imagePath ?>" alt="Thumbnail 4" class="w-full h-full object-contain opacity-50" onerror="this.src='../../assets/img/placeholder-product.png'">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <?php if (!empty($product['nama_brand'])): ?>
                            <a href="productCollection.php?brand=<?= urlencode($product['id_brand']) ?>"
                                class="inline-block text-sm text-primary font-medium hover:underline mb-2">
                                <?= htmlspecialchars($product['nama_brand']) ?>
                            </a>
                        <?php endif; ?>

                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                            <?= htmlspecialchars($product['nama_product']) ?>
                        </h1>

                        <?php if (!empty($product['nama_kategori'])): ?>
                            <a href="productCollection.php?category=<?= urlencode($product['id_kategori']) ?>"
                                class="inline-flex items-center gap-1.5 mt-3 text-sm text-gray-500 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <?= htmlspecialchars($product['nama_kategori']) ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-5 h-5 <?= $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-200' ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="text-sm text-gray-500"><?= number_format($avgRating, 1) ?> (<?= $reviewCount ?> ulasan)</span>
                        <?php if ($totalSold > 0): ?>
                            <span class="text-gray-300">|</span>
                            <span class="text-sm text-gray-500">Terjual <?= number_format($totalSold) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span class="text-3xl md:text-4xl font-bold text-primary"><?= $formattedPrice ?></span>
                            <?php if ($hasDiscount): ?>
                                <span class="text-lg md:text-xl text-gray-400 line-through"><?= $originalPrice ?></span>
                                <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-600"><?= htmlspecialchars($discountBadge) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-4 mt-3 text-sm">
                            <div class="flex items-center gap-1.5 text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>Stok: <?= (int)$product['stok'] ?> unit</span>
                            </div>
                            <?php if (!empty($product['berat_gram']) && $product['berat_gram'] > 0): ?>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                    <span><?= number_format($product['berat_gram']) ?> gram</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Jumlah</label>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center border border-gray-200 rounded-lg">
                                    <button type="button" onclick="decreaseQty()" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors rounded-l-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1" max="<?= (int)$product['stok'] ?>"
                                        class="w-16 h-10 text-center border-x border-gray-200 text-sm font-medium focus:outline-none">
                                    <button type="button" onclick="increaseQty()" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors rounded-r-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                <span class="text-sm text-gray-500">Maksimal <?= (int)$product['stok'] ?> unit</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button onclick="addToCart('<?= $product['id_product'] ?>')"
                                class="flex-1 py-3.5 px-6 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary/5 transition-colors flex items-center justify-center gap-2 <?= !$stockBadge['available'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                <?= !$stockBadge['available'] ? 'disabled' : '' ?>>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Masukkan Keranjang
                            </button>
                            <button onclick="buyNow('<?= $product['id_product'] ?>')"
                                class="flex-1 py-3.5 px-6 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-colors flex items-center justify-center gap-2 <?= !$stockBadge['available'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                <?= !$stockBadge['available'] ? 'disabled' : '' ?>>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Beli Sekarang
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                        <div class="text-center p-3">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 rounded-full mb-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-700">Garansi Resmi</p>
                        </div>
                        <div class="text-center p-3">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-700">30 Hari Retur</p>
                        </div>
                        <div class="text-center p-3">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-amber-100 rounded-full mb-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-700">Produk Original</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100">
                        <nav class="flex">
                            <button onclick="switchTab('description')" id="tab-description"
                                class="tab-btn flex-1 py-4 px-6 text-center font-semibold text-primary border-b-2 border-primary transition-colors">
                                Deskripsi & Spesifikasi
                            </button>
                            <button onclick="switchTab('reviews')" id="tab-reviews"
                                class="tab-btn flex-1 py-4 px-6 text-center font-semibold text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition-colors">
                                Ulasan (<?= $reviewCount ?>)
                            </button>
                        </nav>
                    </div>

                    <div id="content-description" class="tab-content p-6 md:p-8">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Produk</h3>

                            <?php if (!empty($product['deskripsi_speksifikasi'])): ?>
                                <?php
                                $desc = $product['deskripsi_speksifikasi'];
                                $specs = [];

                                if (preg_match('/Specifications?:\s*(.+)/i', $desc, $matches)) {
                                    $specString = $matches[1];
                                    $specParts = preg_split('/,\s*/', $specString);
                                    foreach ($specParts as $part) {
                                        $part = trim($part);
                                        if (!empty($part)) {
                                            if (strpos($part, ':') !== false) {
                                                list($key, $value) = explode(':', $part, 2);
                                                $specs[trim($key)] = trim($value);
                                            } else {
                                                $specs[] = $part;
                                            }
                                        }
                                    }
                                }
                                ?>

                                <p class="text-gray-700 leading-relaxed mb-6">
                                    <?= nl2br(htmlspecialchars($product['deskripsi_speksifikasi'])) ?>
                                </p>

                                <?php if (!empty($specs)): ?>
                                    <h3 class="text-lg font-bold text-gray-900 mb-4 mt-8">Spesifikasi</h3>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <tbody class="divide-y divide-gray-100">
                                                <?php foreach ($specs as $key => $value): ?>
                                                    <tr>
                                                        <td class="py-3 pr-4 font-medium text-gray-900 w-1/3 align-top">
                                                            <?= is_string($key) ? htmlspecialchars($key) : '' ?>
                                                        </td>
                                                        <td class="py-3 text-gray-600">
                                                            <?= htmlspecialchars(is_string($key) ? $value : $value) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-gray-500 italic">Deskripsi produk belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="content-reviews" class="tab-content p-6 md:p-8 hidden">
                        <?php if ($reviewCount > 0): ?>
                            <div class="flex flex-col md:flex-row gap-8">
                                <div class="md:w-1/3">
                                    <div class="text-center p-6 bg-gray-50 rounded-xl sticky top-28">
                                        <div class="text-5xl font-bold text-gray-900"><?= number_format($avgRating, 1) ?></div>
                                        <div class="flex items-center justify-center mt-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg class="w-5 h-5 <?= $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-200' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Dari <?= $reviewCount ?> ulasan</p>

                                        <div class="mt-6 space-y-2">
                                            <?php for ($star = 5; $star >= 1; $star--): ?>
                                                <?php $count = $ratingDistribution[$star]; ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-500 w-3"><?= $star ?></span>
                                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <div class="h-1 flex-1 bg-gray-200 rounded" style="width: 40px;">
                                                        <div class="h-full bg-yellow-400 rounded" style="width: <?= $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0 ?>%;"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 w-6 text-right"><?= $count ?></span>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:w-2/3 space-y-6">
                                    <?php foreach (array_slice($reviews, 0, 5) as $review):
                                        $timeAgo = date('j M Y', strtotime($review['tanggal_review']));
                                        $customerPhoto = !empty($review['profile_image']) ? '../../uploads/customers/' . htmlspecialchars($review['profile_image']) : 'https://i.pravatar.cc/48?u=' . urlencode($review['nama_lengkap']);
                                    ?>
                                        <div class="border-b border-gray-100 pb-6">
                                            <div class="flex items-start gap-4">
                                                <img src="<?= $customerPhoto ?>" alt="<?= htmlspecialchars($review['nama_lengkap']) ?>" class="w-10 h-10 rounded-full object-cover">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-semibold text-gray-900"><?= htmlspecialchars($review['nama_lengkap']) ?></span>
                                                        <span class="text-xs text-gray-400"><?= $timeAgo ?></span>
                                                    </div>
                                                    <div class="flex items-center mb-2">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <svg class="w-4 h-4 <?= $i <= (int)$review['rating'] ? 'text-yellow-400' : 'text-gray-200' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <p class="text-gray-700 text-sm"><?= htmlspecialchars($review['komentar']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if ($reviewCount > 5): ?>
                                        <button class="w-full py-3 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            Lihat Semua Ulasan (<?= $reviewCount ?>)
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium mb-2">Belum ada ulasan</p>
                                <p class="text-sm text-gray-400">Jadilah yang pertama memberikan ulasan untuk produk ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($relatedProducts)): ?>
                <div class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Produk Terkait</h2>
                        <a href="productCollection.php?category=<?= urlencode($product['id_kategori']) ?>"
                            class="text-sm text-primary font-medium hover:underline flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($relatedProducts as $relProduct):
                            $relImagePath = '';
                            $relGambar = $relProduct['gambar'] ?? '';
                            if (empty($relGambar)) {
                                $relImagePath = '../../assets/img/placeholder-product.png';
                            } elseif (str_starts_with($relGambar, 'http://') || str_starts_with($relGambar, 'https://')) {
                                $relImagePath = $relGambar;
                            } else {
                                $relImagePath = '../../uploads/products/' . htmlspecialchars($relGambar);
                            }

                            $relStockBadge = $productHelper->getStockBadge((int)($relProduct['stok'] ?? 0), $relProduct['status_produk'] ?? 'tersedia');
                            $relFormattedPrice = $productHelper->formatPrice((float)$relProduct['harga']);
                        ?>
                            <a href="productDetail.php?id=<?= urlencode($relProduct['id_product']) ?>"
                                class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <div class="relative aspect-square overflow-hidden bg-gray-100">
                                    <img src="<?= $relImagePath ?>"
                                        alt="<?= htmlspecialchars($relProduct['nama_product']) ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                        loading="lazy"
                                        onerror="this.src='../../assets/img/placeholder-product.png'">
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $relStockBadge['class'] ?>">
                                            <?= $relStockBadge['text'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-gray-900 text-sm line-clamp-2 group-hover:text-primary transition-colors">
                                        <?= htmlspecialchars($relProduct['nama_product']) ?>
                                    </h3>
                                    <p class="text-primary font-bold mt-2"><?= $relFormattedPrice ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
    <div id="customToastContainer"></div>
    <?php include '../../components/users/loginRequiredModal.php'; ?>
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
            width: 320px;
            /* FIXED width */
            max-width: 320px;
            /* jangan pakai 400px */
            padding: 14px 16px;
            /* lebih compact */
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
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
    baseUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>',
    apiUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/api'
    };
    </script>
    <script src="../../assets/js/users/product-actions.js"></script>

    <script>
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
        }

        function switchTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-primary', 'border-primary');
                btn.classList.add('text-gray-500', 'border-transparent');
            });
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            document.getElementById('tab-' + tabName).classList.add('text-primary', 'border-primary');
            document.getElementById('tab-' + tabName).classList.remove('text-gray-500', 'border-transparent');
            document.getElementById('content-' + tabName).classList.remove('hidden');
        }

        function increaseQty() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max);
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
            }
        }

        function showCustomToast(message, type = 'success', title = null) {
            const container = document.getElementById('customToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;

            const titleMap = {
                'success': 'Berhasil!',
                'error': 'Gagal!',
                'warning': 'Perhatian!',
                'info': 'Info'
            };

            const iconMap = {
                'success': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
                'error': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
                'warning': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
                'info': '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            };

            const toastTitle = title || titleMap[type] || 'Info';
            const toastIcon = iconMap[type] || iconMap['info'];

            toast.innerHTML = `
                <div class="custom-toast-icon">
                    ${toastIcon}
                </div>
                <div class="custom-toast-content">
                    <div class="custom-toast-title">${toastTitle}</div>
                    <div class="custom-toast-message">${message}</div>
                </div>
                <button class="custom-toast-close" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="custom-toast-progress"></div>
            `;

            container.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);

            const timer = setTimeout(() => {
                hideToast(toast);
            }, 4000);

            function hideToast(el) {
                el.classList.add('hiding');
                el.classList.remove('show');
                setTimeout(() => el.remove(), 400);
            }
        }
    </script>
</body>

</html>