<?php
$pageTitle = "Koleksi Produk";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

use App\Helper\ProductLandingHelper;
use App\Helper\DiscountHelper;

function buildQueryString($params)
{
    return http_build_query(array_filter($params, fn($v) => $v !== '' && $v !== null));
}

$productHelper = new ProductLandingHelper();
$discountHelper = new DiscountHelper();

$categoryFilter = $_GET['category'] ?? '';
$brandFilter = $_GET['brand'] ?? '';
$availabilityFilter = $_GET['availability'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

$filters = [];
if ($categoryFilter) $filters['category'] = $categoryFilter;
if ($brandFilter) $filters['brand'] = $brandFilter;
if ($availabilityFilter) $filters['availability'] = $availabilityFilter;
if ($minPrice) $filters['min_price'] = $minPrice;
if ($maxPrice) $filters['max_price'] = $maxPrice;
if ($searchQuery) $filters['search'] = $searchQuery;

$result = $productHelper->getAllProducts($filters, $sortBy, $currentPage, $perPage);
$products = $discountHelper->applyDiscountsToProducts($result['products']);
$pagination = $result['pagination'];

$categories = $productHelper->getAllCategories();
$brands = $productHelper->getAllBrands();

$categoryName = '';
if ($categoryFilter) {
    $categoryData = $productHelper->getCategoryById($categoryFilter);
    $categoryName = $categoryData['nama_kategori'] ?? '';
}

$brandName = '';
if ($brandFilter) {
    $brandData = $productHelper->getBrandById($brandFilter);
    $brandName = $brandData['nama_brand'] ?? '';
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Produk', 'url' => 'productCollection.php']
];

if ($categoryName) {
    $breadcrumbs[] = ['label' => $categoryName, 'url' => null];
} elseif ($brandName) {
    $breadcrumbs[] = ['label' => $brandName, 'url' => null];
} elseif ($searchQuery) {
    $breadcrumbs[] = ['label' => 'Pencarian: ' . htmlspecialchars($searchQuery), 'url' => null];
}

include '../../components/users/head.php';
include '../../components/users/productCard.php';
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
        <div class="w-full px-5 md:px-8 lg:px-20 py-6">

            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    <?php if ($categoryName): ?>
                        <?= htmlspecialchars($categoryName) ?>
                    <?php elseif ($brandName): ?>
                        Produk <?= htmlspecialchars($brandName) ?>
                    <?php elseif ($searchQuery): ?>
                        Hasil Pencarian "<?= htmlspecialchars($searchQuery) ?>"
                    <?php else: ?>
                        Semua Produk
                    <?php endif; ?>
                </h1>
                <p class="text-gray-600">
                    Menampilkan <?= count($products) ?> dari <?= $pagination['total_items'] ?> produk
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">

                <aside id="filterSidebar" class="hidden lg:block lg:w-72 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-32">
                        <form id="filterForm" method="GET" action="">
                            <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">

                            <?php if ($searchQuery): ?>
                                <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery) ?>">
                            <?php endif; ?>

                            <div class="flex items-center justify-between mb-5">
                                <h3 class="font-bold text-lg text-gray-900">Filter</h3>
                                <a href="productCollection.php" class="text-sm text-primary hover:underline">Reset</a>
                            </div>

                            <div class="space-y-4">
                                <div class="border-b border-gray-100 pb-4">
                                    <button type="button" onclick="toggleFilter('categoryFilter')" class="flex items-center justify-between w-full py-2 font-semibold text-gray-900">
                                        <span>Kategori</span>
                                        <svg class="w-4 h-4 transition-transform" id="categoryFilterIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="categoryFilter" class="mt-3 space-y-2 max-h-48 overflow-y-auto">
                                        <?php foreach ($categories as $cat): ?>
                                            <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition-colors">
                                                <input type="radio" name="category" value="<?= htmlspecialchars($cat['id_kategori']) ?>"
                                                    <?= $categoryFilter === $cat['id_kategori'] ? 'checked' : '' ?>
                                                    class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                                <span class="text-sm text-gray-700 flex-1"><?= htmlspecialchars($cat['nama_kategori']) ?></span>
                                                <span class="text-xs text-gray-400">(<?= $cat['product_count'] ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="border-b border-gray-100 pb-4">
                                    <button type="button" onclick="toggleFilter('brandFilter')" class="flex items-center justify-between w-full py-2 font-semibold text-gray-900">
                                        <span>Brand</span>
                                        <svg class="w-4 h-4 transition-transform" id="brandFilterIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="brandFilter" class="mt-3 space-y-2 max-h-48 overflow-y-auto">
                                        <?php foreach ($brands as $brand): ?>
                                            <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition-colors">
                                                <input type="radio" name="brand" value="<?= htmlspecialchars($brand['id_brand']) ?>"
                                                    <?= $brandFilter === $brand['id_brand'] ? 'checked' : '' ?>
                                                    class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                                <span class="text-sm text-gray-700 flex-1"><?= htmlspecialchars($brand['nama_brand']) ?></span>
                                                <span class="text-xs text-gray-400">(<?= $brand['product_count'] ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="border-b border-gray-100 pb-4">
                                    <button type="button" onclick="toggleFilter('availFilter')" class="flex items-center justify-between w-full py-2 font-semibold text-gray-900">
                                        <span>Ketersediaan</span>
                                        <svg class="w-4 h-4 transition-transform" id="availFilterIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="availFilter" class="mt-3 space-y-2">
                                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition-colors">
                                            <input type="radio" name="availability" value="in_stock"
                                                <?= $availabilityFilter === 'in_stock' ? 'checked' : '' ?>
                                                class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                            <span class="text-sm text-gray-700">Tersedia</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition-colors">
                                            <input type="radio" name="availability" value="out_stock"
                                                <?= $availabilityFilter === 'out_stock' ? 'checked' : '' ?>
                                                class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                            <span class="text-sm text-gray-700">Stok Habis</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="pb-4">
                                    <button type="button" onclick="toggleFilter('priceFilter')" class="flex items-center justify-between w-full py-2 font-semibold text-gray-900">
                                        <span>Rentang Harga</span>
                                        <svg class="w-4 h-4 transition-transform" id="priceFilterIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="priceFilter" class="mt-3 space-y-3">
                                        <div class="flex gap-2">
                                            <div class="flex-1">
                                                <label class="text-xs text-gray-500 mb-1 block">Minimum</label>
                                                <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>"
                                                    placeholder="Rp 0"
                                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            </div>
                                            <div class="flex-1">
                                                <label class="text-xs text-gray-500 mb-1 block">Maximum</label>
                                                <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>"
                                                    placeholder="Rp Max"
                                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition-colors mt-4">
                                Terapkan Filter
                            </button>
                        </form>
                    </div>
                </aside>

                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button id="mobileFilterBtn" class="lg:hidden flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Filter</span>
                            </button>

                            <form method="GET" action="" class="flex-1 sm:flex-initial sm:w-64">
                                <?php if ($categoryFilter): ?>
                                    <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">
                                <?php endif; ?>
                                <?php if ($brandFilter): ?>
                                    <input type="hidden" name="brand" value="<?= htmlspecialchars($brandFilter) ?>">
                                <?php endif; ?>
                                <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                                <div class="relative">
                                    <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>"
                                        placeholder="Cari produk..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </form>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Urutkan:</span>
                            <select id="sortSelect" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none cursor-pointer">
                                <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
                                <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
                                <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Nama: A-Z</option>
                                <option value="name_desc" <?= $sortBy === 'name_desc' ? 'selected' : '' ?>>Nama: Z-A</option>
                            </select>
                        </div>
                    </div>

                    <?php if (!empty($products)): ?>
                        <ul class="grid gap-6 lg:gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <?php foreach ($products as $product): ?>
                                <?= renderProductCard($product, $productHelper) ?>
                            <?php endforeach; ?>
                        </ul>

                        <?php if ($pagination['total_pages'] > 1): ?>
                            <div class="mt-8 flex justify-center">
                                <nav class="flex items-center gap-1 bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                                    <?php
                                    $queryParams = [
                                        'category' => $categoryFilter,
                                        'brand' => $brandFilter,
                                        'availability' => $availabilityFilter,
                                        'min_price' => $minPrice,
                                        'max_price' => $maxPrice,
                                        'search' => $searchQuery,
                                        'sort' => $sortBy
                                    ];
                                    ?>

                                    <?php if ($pagination['has_prev']): ?>
                                        <a href="?<?= buildQueryString(array_merge($queryParams, ['page' => $currentPage - 1])) ?>"
                                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($pagination['total_pages'], $currentPage + 2);

                                    if ($startPage > 1): ?>
                                        <a href="?<?= buildQueryString(array_merge($queryParams, ['page' => 1])) ?>"
                                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                                        <?php if ($startPage > 2): ?>
                                            <span class="px-2 text-gray-400">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <a href="?<?= buildQueryString(array_merge($queryParams, ['page' => $i])) ?>"
                                            class="flex items-center justify-center w-10 h-10 rounded-lg text-sm font-medium transition-colors
                                                    <?= $i === $currentPage ? 'bg-primary text-white' : 'hover:bg-gray-100' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($endPage < $pagination['total_pages']): ?>
                                        <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                                            <span class="px-2 text-gray-400">...</span>
                                        <?php endif; ?>
                                        <a href="?<?= buildQueryString(array_merge($queryParams, ['page' => $pagination['total_pages']])) ?>"
                                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
                                            <?= $pagination['total_pages'] ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($pagination['has_next']): ?>
                                        <a href="?<?= buildQueryString(array_merge($queryParams, ['page' => $currentPage + 1])) ?>"
                                            class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
                            <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                            <a href="productCollection.php" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        </div> -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Produk Ditemukan</h3>
                            <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                            <a href="productCollection.php" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="mobileFilterDrawer" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[85vh] flex flex-col transform translate-y-full transition-transform duration-300 ease-out shadow-2xl" id="mobileFilterContent">
            <div class="flex-shrink-0 bg-white rounded-t-3xl border-b border-gray-100">
                <div class="flex justify-center pt-3 pb-2">
                    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                </div>
                <div class="px-5 pb-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900">Filter Produk</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Temukan produk yang Anda cari</p>
                    </div>
                    <button id="closeMobileFilter" class="p-2.5 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <form method="GET" action="" class="flex flex-col flex-1 overflow-hidden">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sortBy) ?>">
                <?php if ($searchQuery): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery) ?>">
                <?php endif; ?>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3 overscroll-contain">
                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleMobileSection('mobileCategory')" class="w-full flex items-center justify-between p-4 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">Kategori</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" id="mobileCategoryIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="mobileCategory" class="px-4 pb-4 space-y-2 max-h-36 overflow-y-auto hidden">
                            <?php foreach ($categories as $cat): ?>
                                <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-white transition-colors">
                                    <input type="radio" name="category" value="<?= htmlspecialchars($cat['id_kategori']) ?>"
                                        <?= $categoryFilter === $cat['id_kategori'] ? 'checked' : '' ?>
                                        class="w-4 h-4 text-primary accent-primary">
                                    <span class="text-sm text-gray-700 flex-1"><?= htmlspecialchars($cat['nama_kategori']) ?></span>
                                    <span class="text-xs text-gray-400 bg-white px-2 py-0.5 rounded-full"><?= $cat['product_count'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleMobileSection('mobileBrand')" class="w-full flex items-center justify-between p-4 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">Brand</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" id="mobileBrandIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="mobileBrand" class="px-4 pb-4 space-y-2 max-h-36 overflow-y-auto hidden">
                            <?php foreach ($brands as $brand): ?>
                                <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-white transition-colors">
                                    <input type="radio" name="brand" value="<?= htmlspecialchars($brand['id_brand']) ?>"
                                        <?= $brandFilter === $brand['id_brand'] ? 'checked' : '' ?>
                                        class="w-4 h-4 text-primary accent-primary">
                                    <span class="text-sm text-gray-700 flex-1"><?= htmlspecialchars($brand['nama_brand']) ?></span>
                                    <span class="text-xs text-gray-400 bg-white px-2 py-0.5 rounded-full"><?= $brand['product_count'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleMobileSection('mobileAvailability')" class="w-full flex items-center justify-between p-4 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">Ketersediaan</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" id="mobileAvailabilityIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="mobileAvailability" class="px-4 pb-4 space-y-2 hidden">
                            <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-white transition-colors">
                                <input type="radio" name="availability" value="in_stock" <?= $availabilityFilter === 'in_stock' ? 'checked' : '' ?> class="w-4 h-4 text-primary accent-primary">
                                <span class="text-sm text-gray-700">Tersedia</span>
                                <span class="ml-auto w-2 h-2 bg-green-500 rounded-full"></span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-white transition-colors">
                                <input type="radio" name="availability" value="out_stock" <?= $availabilityFilter === 'out_stock' ? 'checked' : '' ?> class="w-4 h-4 text-primary accent-primary">
                                <span class="text-sm text-gray-700">Stok Habis</span>
                                <span class="ml-auto w-2 h-2 bg-red-500 rounded-full"></span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleMobileSection('mobilePrice')" class="w-full flex items-center justify-between p-4 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900">Rentang Harga</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" id="mobilePriceIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="mobilePrice" class="px-4 pb-4 hidden">
                            <div class="flex gap-3 items-center">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-1 block">Minimum</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" placeholder="0" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white">
                                    </div>
                                </div>
                                <span class="text-gray-300 mt-5">—</span>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-1 block">Maksimum</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" placeholder="0" class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 bg-white border-t border-gray-100 p-4 pb-6 safe-area-inset-bottom">
                    <div class="flex gap-3">
                        <a href="productCollection.php" class="flex-1 py-3.5 text-center border-2 border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all">
                            Reset
                        </a>
                        <button type="submit" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/25 active:scale-[0.98]">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../components/users/footer.php'; ?>
    <?php include '../../components/users/loginRequiredModal.php'; ?>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #mobileFilterContent {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        #mobileCategory,
        #mobileBrand,
        #mobileAvailability,
        #mobilePrice {
            transition: max-height 0.2s ease-out;
            overflow: hidden;
        }

        #mobileCategory:not(.hidden),
        #mobileBrand:not(.hidden),
        #mobileAvailability:not(.hidden),
        #mobilePrice:not(.hidden) {
            max-height: 200px;
        }

        .overscroll-contain {
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        #mobileFilterDrawer:not(.hidden) {
            display: flex;
            align-items: flex-end;
        }

        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .safe-area-inset-bottom {
                padding-bottom: calc(1.5rem + env(safe-area-inset-bottom));
            }
        }
    </style>

    <script>
        function toggleFilter(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById(id + 'Icon');
            el.classList.toggle('hidden');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }

        document.getElementById('sortSelect').addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });

        function toggleMobileSection(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById(id + 'Icon');
            const isHidden = el.classList.contains('hidden');

            if (isHidden) {
                el.classList.remove('hidden');
                el.style.maxHeight = el.scrollHeight + 'px';
                if (icon) icon.classList.add('rotate-180');
            } else {
                el.style.maxHeight = '0px';
                if (icon) icon.classList.remove('rotate-180');
                setTimeout(() => el.classList.add('hidden'), 200);
            }
        }

        const mobileFilterBtn = document.getElementById('mobileFilterBtn');
        const mobileFilterDrawer = document.getElementById('mobileFilterDrawer');
        const mobileFilterContent = document.getElementById('mobileFilterContent');
        const closeMobileFilter = document.getElementById('closeMobileFilter');

        if (mobileFilterBtn) {
            mobileFilterBtn.addEventListener('click', function() {
                mobileFilterDrawer.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    mobileFilterDrawer.classList.remove('opacity-0');
                    mobileFilterContent.classList.remove('translate-y-full');
                }, 10);
            });
        }

        if (closeMobileFilter) {
            closeMobileFilter.addEventListener('click', closeMobileDrawer);
        }

        if (mobileFilterDrawer) {
            mobileFilterDrawer.addEventListener('click', function(e) {
                if (e.target === mobileFilterDrawer) {
                    closeMobileDrawer();
                }
            });
        }

        function closeMobileDrawer() {
            mobileFilterContent.classList.add('translate-y-full');
            mobileFilterDrawer.classList.add('opacity-0');
            document.body.style.overflow = '';
            setTimeout(() => {
                mobileFilterDrawer.classList.add('hidden');
            }, 300);
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024 && mobileFilterDrawer && !mobileFilterDrawer.classList.contains('hidden')) {
                closeMobileDrawer();
            }
        });
    </script>
    <script>
        window.APP_CONFIG = {
            baseUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>',
            apiUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/api'
        };
    </script>
    <script src="../../assets/js/users/product-actions.js"></script>
</body>

</html>