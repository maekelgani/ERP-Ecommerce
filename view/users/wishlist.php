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

use App\Helper\ProductLandingHelper;

$productHelper = new ProductLandingHelper();

$wishlistItems = [];
$categories = [];
$brands = [];

if (isset($customer['id_customer'])) {
    try {
        $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                p.*, 
                k.nama_kategori, 
                b.nama_brand, 
                w.tanggal_ditambahkan as added_at,
                d.id_diskon,
                d.tipe_diskon,
                d.nilai_diskon,
                d.harga_setelah_diskon as harga_diskon,
                d.tanggal_mulai as diskon_mulai,
                d.tanggal_berakhir as diskon_berakhir,
                d.status as diskon_status
            FROM wishlist w
            JOIN products p ON w.id_product = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN diskon d ON p.id_product = d.id_product 
                AND d.status = 'aktif' 
                AND (d.tanggal_mulai IS NULL OR d.tanggal_mulai <= NOW())
                AND (d.tanggal_berakhir IS NULL OR d.tanggal_berakhir >= NOW())
            WHERE w.id_customer = :customer_id
            ORDER BY w.tanggal_ditambahkan DESC
        ");
        $stmt->execute([':customer_id' => $customer['id_customer']]);
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

    <div id="navbarSpacer" class="transition-all duration-300 h-32 md:h-44"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const promoBanner = document.getElementById('promoBanner');
            const navbarSpacer = document.getElementById('navbarSpacer');

            function updateSpacerHeight() {
                if (window.innerWidth >= 768 && promoBanner) {
                    navbarSpacer.style.height = window.scrollY > 50 ? '112px' : '156px';
                } else {
                    navbarSpacer.style.height = '112px';
                }
            }

            updateSpacerHeight();
            window.addEventListener('scroll', updateSpacerHeight);
            window.addEventListener('resize', updateSpacerHeight);
        });
    </script>

    <main class="max-w-full mb-10">
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
                        $discountPrice = $hasDiscount ? (float)$product['harga_diskon'] : null;
                        $discountPercent = 0;

                        if ($hasDiscount && $product['tipe_diskon'] === 'persen') {
                            $discountPercent = (int)$product['nilai_diskon'];
                        } elseif ($hasDiscount && $discountPrice && $originalPrice > 0) {
                            $discountPercent = round((($originalPrice - $discountPrice) / $originalPrice) * 100);
                        }

                        $displayPrice = $hasDiscount && $discountPrice ? $discountPrice : $originalPrice;
                        $price = $productHelper->formatPrice($displayPrice);
                        $originalPriceFormatted = $productHelper->formatPrice($originalPrice);

                        $description = htmlspecialchars(substr($product['deskripsi_speksifikasi'] ?? '', 0, 100));
                        $stockBadge = $productHelper->getStockBadge($product['stok'], $product['status_produk']);
                        $productId = $product['id_product'] ?? '';
                        $addedDate = !empty($product['added_at']) ? date('d M Y', strtotime($product['added_at'])) : '-';
                        $categoryName = htmlspecialchars($product['nama_kategori'] ?? '');
                        $brandName = htmlspecialchars($product['nama_brand'] ?? '');
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

                                    <?php if ($hasDiscount && $discountPercent > 0): ?>
                                        <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                            -<?= $discountPercent ?>%
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
                                    <div class="flex items-baseline gap-2 flex-wrap">
                                        <p class="text-primary font-bold text-lg"><?= $price ?></p>
                                        <?php if ($hasDiscount && $discountPrice && $discountPrice < $originalPrice): ?>
                                            <p class="text-gray-400 text-sm line-through"><?= $originalPriceFormatted ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="productDetail.php?id=<?= urlencode($productId) ?>">
                                        <h3 class="mt-1.5 text-sm sm:text-base font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors"><?= $productName ?></h3>
                                    </a>
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
        function showNotification(message, type = 'success') {
            const existing = document.querySelector('.notification-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = `notification-toast fixed top-24 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 flex items-center gap-3 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;

            const icon = type === 'success' ?
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

            toast.innerHTML = icon + '<span>' + message + '</span>';
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

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
                        showNotification(data.message || 'Produk dihapus dari wishlist', 'success');
                    } else {
                        showNotification(data.message || 'Gagal menghapus dari wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
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
                        showNotification(data.message || 'Semua produk dihapus dari wishlist', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Gagal menghapus wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
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
                        showNotification(data.message || 'Produk berhasil ditambahkan ke keranjang!', 'success');
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
                        showNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
                });
        }

        function buyNowFromWishlist(productId) {
            window.location.href = 'productCheckout.php?from=buynow&product_id=' + productId + '&quantity=1';
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