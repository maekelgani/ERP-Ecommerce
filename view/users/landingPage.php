<?php
$pageTitle = "Home";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $_SESSION['toast_success'] = 'Anda telah berhasil logout. Sampai jumpa kembali!';
}

include '../../components/users/head.php';
include '../../components/users/productCard.php';

use App\Helper\CategoryLandingHelper;
use App\Helper\ProductLandingHelper;
use App\Helper\BrandLandingHelper;


$categoryHelper = new CategoryLandingHelper();
$productHelper = new ProductLandingHelper();
$brandHelper = new BrandLandingHelper();

$categories = $categoryHelper->getAllCategories();
$bestSellers = $productHelper->getBestSellers(5);
$newProducts = $productHelper->getNewProducts(5);
$catalogProducts = $productHelper->getProductsForCatalog(10);
$brands = $brandHelper->getBrandsForSlider();

$articles = [
    [
        "image" => "https://images.unsplash.com/photo-1751374156944-aa91dee48408?q=80&w=2070&auto=format&fit=crop",
        "category" => "Tips & Trick",
        "date" => "28 Nov 2025",
        "title" => "Cara Merakit PC Gaming untuk Pemula 2025",
        "excerpt" => "Panduan langkah demi langkah merakit PC impianmu, mulai dari pemasangan CPU hingga manajemen kabel yang rapi."
    ],
    [
        "image" => "https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070&auto=format&fit=crop",
        "category" => "News",
        "date" => "25 Nov 2025",
        "title" => "NVIDIA GeForce RTX 50 Series Resmi Diumumkan",
        "excerpt" => "Peningkatan performa hingga 40% dan efisiensi daya yang lebih baik. Simak spesifikasi lengkap dan harganya di sini."
    ],
    [
        "image" => "https://images.unsplash.com/photo-1593640408182-31c70c8268f5?q=80&w=2042&auto=format&fit=crop",
        "category" => "Review",
        "date" => "20 Nov 2025",
        "title" => "Review Montech XR: Casing Budget Rasa Premium",
        "excerpt" => "Apakah casing dengan harga di bawah 1 juta ini layak untuk build high-end? Kita uji airflow dan build quality-nya."
    ]
];

?>

<body class="w-full bg-no-repeat min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <?php include '../../components/users/toastNotifications.php'; ?>
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <!-- Spacer untuk fixed navbar -->
    <div id="navbarSpacer" class="transition-all duration-300" style="height: 112px;"></div>
    <main class="max-w-full mb-10">

        <!-- banner promosi slider -->
        <section class="w-full">
            <div class="swiper banner-swiper w-full">
                <div class="swiper-wrapper">
                    <div class="swiper-slide bg-gray-100">
                        <img alt="Banner Promosi 1" class="w-full h-auto object-contain" src="../../assets/img/banner/slider-1.png">
                    </div>
                    <div class="swiper-slide bg-gray-100">
                        <img alt="Banner Promosi 2" class="w-full h-auto object-contain" src="../../assets/img/banner/slider-2.png">
                    </div>
                    <div class="swiper-slide bg-gray-100">
                        <img alt="Banner Promosi 3" class="w-full h-auto object-contain" src="../../assets/img/banner/slider-3.png">
                    </div>
                </div>
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Navigation -->
                <div class="swiper-button-prev !text-white !bg-primary/60 hover:!bg-primary transition-all rounded-full p-3 !w-12 !h-12 !left-4 md:!left-8 lg:!left-20"></div>
                <div class="swiper-button-next !text-white !bg-primary/60 hover:!bg-primary transition-all rounded-full p-3 !w-12 !h-12 !right-4 md:!right-8 lg:!right-20"></div>
            </div>
        </section>

        <!-- CATEGORY - Dynamic from Database -->
        <section class="mt-4 w-full px-4 sm:px-5 md:px-8 lg:px-20" id="category-section">
            <div class="py-5 sm:py-6 md:py-8 px-4 sm:px-5 md:px-6 lg:px-8 rounded-xl shadow-sm w-full bg-white">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div>
                        <h2 class="font-bold text-lg sm:text-xl md:text-2xl lg:text-3xl text-gray-900">
                            Shop by Category
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 hidden sm:block">Temukan produk berdasarkan kategori</p>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="categoryCollection.php" class="hidden md:inline-flex text-sm text-primary hover:text-[#6a1c1e] font-medium transition-colors">
                            Lihat Semua
                        </a>
                        <div class="flex gap-1.5 sm:gap-2">
                            <button id="category-prev" class="p-1.5 sm:p-2 rounded-full bg-gray-100 hover:bg-primary hover:text-white transition-all duration-300 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button id="category-next" class="p-1.5 sm:p-2 rounded-full bg-gray-100 hover:bg-primary hover:text-white transition-all duration-300 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden">
                    <div id="category-slider" class="flex gap-3 sm:gap-4 transition-transform duration-500 ease-out pb-2" style="scroll-behavior: smooth;">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $index => $category): ?>
                                <?php
                                $iconValue = $category['icon_kategori'] ?? '';
                                if (empty(trim($iconValue))) {
                                    $iconPath = '../../uploads/category/';
                                } elseif (str_starts_with($iconValue, 'http://') || str_starts_with($iconValue, 'https://') || str_starts_with($iconValue, '/uploads/') || str_starts_with($iconValue, 'uploads/')) {
                                    $iconPath = htmlspecialchars($iconValue);
                                    if (str_starts_with($iconPath, '/uploads/')) {
                                        $iconPath = '../..' . $iconPath;
                                    } elseif (str_starts_with($iconPath, 'uploads/')) {
                                        $iconPath = '../../' . $iconPath;
                                    }
                                } else {
                                    $iconPath = '../../uploads/category/' . htmlspecialchars($iconValue);
                                }
                                $categoryName = htmlspecialchars($category['nama_kategori']);
                                $categoryId = htmlspecialchars($category['id_kategori']);
                                ?>
                                <a href="productCollection.php?category=<?= urlencode($categoryId) ?>"
                                    class="flex-shrink-0 category-card group relative rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-500
                                            w-[calc(50%-6px)] sm:w-[calc(33.333%-11px)] md:w-[calc(25%-12px)] lg:w-[calc(20%-13px)] xl:w-[calc(16.666%-14px)]
                                            aspect-square min-w-[140px] max-w-[200px]"
                                    style="animation: fadeInUp 0.5s ease-out <?= $index * 0.08 ?>s both;">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent z-10 opacity-70 group-hover:opacity-90 transition-opacity duration-300"></div>
                                    <img alt="<?= $categoryName ?>"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                        src="<?= $iconPath ?>"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 200 200%27%3E%3Crect width=%27200%27 height=%27200%27 fill=%27%23e5e7eb%27/%3E%3Crect x=%2740%27 y=%2740%27 width=%27120%27 height=%27120%27 rx=%2710%27 fill=%27%23d1d5db%27/%3E%3Crect x=%2755%27 y=%2760%27 width=%2735%27 height=%2735%27 rx=%275%27 fill=%27%239ca3af%27/%3E%3Crect x=%27110%27 y=%2760%27 width=%2735%27 height=%2735%27 rx=%275%27 fill=%27%239ca3af%27/%3E%3Crect x=%2755%27 y=%27105%27 width=%2735%27 height=%2735%27 rx=%275%27 fill=%27%239ca3af%27/%3E%3Crect x=%27110%27 y=%27105%27 width=%2735%27 height=%2735%27 rx=%275%27 fill=%27%239ca3af%27/%3E%3C/svg%3E'">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 z-20"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-2.5 sm:p-3 md:p-4 z-30">
                                        <span class="inline-block text-white font-semibold text-[11px] sm:text-xs md:text-sm bg-primary/90 backdrop-blur-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg shadow-lg group-hover:bg-primary transition-all duration-300 line-clamp-1">
                                            <?= $categoryName ?>
                                        </span>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-primary/50 rounded-xl transition-all duration-300 z-20"></div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="w-full text-center py-10 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="text-sm font-medium">Belum ada kategori tersedia</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center justify-center mt-4 gap-2" id="category-dots">
                    <?php
                    $totalCategories = count($categories);
                    $dotsCount = min(ceil($totalCategories / 3), 6);
                    for ($i = 0; $i < $dotsCount; $i++):
                    ?>
                        <button class="category-dot w-2 h-2 rounded-full bg-gray-300 hover:bg-primary transition-all duration-300 <?= $i === 0 ? 'bg-primary w-5' : '' ?>" data-index="<?= $i ?>"></button>
                    <?php endfor; ?>
                </div>

                <div class="mt-4 text-center md:hidden">
                    <a href="categoryCollection.php" class="inline-flex items-center gap-1 text-sm text-primary hover:text-[#6a1c1e] font-medium transition-colors">
                        Lihat Semua Kategori
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- EVENT Collaboration -->
        <section class="mt-10 w-full px-5 md:px-8 lg:px-20">
            <div class="py-4 w-full">
                <h2 class="font-bold text-xl md:text-2xl lg:text-4xl mb-6">
                    Limited Collaboration
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <a href="productCollection.php?search=Hatsune+Miku" class="relative group lg:col-span-2 bg-gray-400/75 rounded-lg overflow-hidden h-100 flex items-center justify-center text-3xl font-bold text-gray-700">
                        <img src="../../assets/img/banner/collab-mikuXrog.png" alt="ROG x Miku" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                            <h3 class="text-white text-2xl font-bold mb-2">ROG x Hatsune Miku</h3>
                            <p class="text-gray-200 text-sm">Special PC Bundle Collaboration</p>
                        </div>
                    </a>
                    <a href="productCollection.php?search=Monster+Hunter" class="relative group bg-gray-400/75 rounded-lg overflow-hidden h-60 flex items-center justify-center text-3xl font-bold text-gray-700">
                        <img src="../../assets/img/banner/collab-msi-mh.jpg" alt="MSI x Monster Hunter" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                            <h3 class="text-white text-xl font-bold mb-2">MSI x Monster Hunter</h3>
                            <p class="text-gray-200 text-sm">Limited Special PC Bundle Collaboration </p>
                        </div>
                    </a>
                    <a href="productCollection.php?search=Evangelion" class="relative group bg-gray-400/75 rounded-lg overflow-hidden h-60 flex items-center justify-center text-3xl font-bold text-gray-700">
                        <img src="../../assets/img/banner/collab-evaXrog.png" alt="ASUS x Evangelion" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                            <h3 class="text-white text-xl font-bold mb-2">ASUS x Evangelion</h3>
                            <p class="text-gray-200 text-sm">Collector's Edition Rig</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- BEST SELLER - Dynamic from Database -->
        <section class="mt-10 w-full px-5 md:px-8 lg:px-20">
            <div class="py-4 w-full">
                <div class="mx-auto">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Best Seller</h2>
                        <a href="productCollection.php?sort=best" class="text-primary hover:text-[#A14646] font-medium text-sm transition-colors hidden sm:block">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <?php if (!empty($bestSellers)): ?>
                        <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                            <?php foreach ($bestSellers as $product): ?>
                                <?= renderProductCard($product, $productHelper) ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-16 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-lg font-medium">Belum ada produk best seller</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Banner promosi -->
        <section class="w-full md:px-8 lg:px-20 flex">
            <div class="p-4 md:p-10 rounded-lg w-full">
                <a href="" class="max-w-full justify-center block">
                    <div class="max-w-full h-20 md:h-28 rounded-xl bg-primary text-white flex items-center justify-center text-center shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-center gap-4">
                            <svg class="w-8 h-8 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            <span class="text-lg md:text-2xl font-bold">Promo Spesial Akhir Tahun - Diskon Hingga 50%!</span>
                            <svg class="w-8 h-8 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- NEW PRODUCTS - Dynamic from Database -->
        <section class="w-full px-5 md:px-8 lg:px-20">
            <div class="py-4 w-full">
                <div class="mx-auto">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Produk Baru</h2>
                        <a href="productCollection.php?sort=newest" class="text-primary hover:text-[#A14646] font-medium text-sm transition-colors hidden sm:block">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <?php if (!empty($newProducts)): ?>
                        <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                            <?php foreach ($newProducts as $product): ?>
                                <?= renderProductCard($product, $productHelper) ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-16 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-lg font-medium">Belum ada produk baru</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- BRAND PILIHAN - Infinite Loop Slider from Database -->
        <section class="mt-4 py-10 w-full bg-primary overflow-hidden">
            <div class="px-5 md:px-8 lg:px-20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-bold text-xl sm:text-2xl lg:text-3xl text-white">Brand Pilihan</h2>
                        <p class="text-white/70 text-sm mt-1">Partner terpercaya untuk kebutuhan PC Anda</p>
                    </div>
                    <a href="brandCollection.php" class="hidden sm:flex items-center gap-2 text-white/90 hover:text-white font-medium text-sm transition-colors">
                        Lihat Semua Brand
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <?php if (!empty($brands)): ?>
                <div class="brand-slider-container relative">
                    <div class="brand-slider flex gap-6 animate-scroll">
                        <?php foreach ($brands as $brand): ?>
                            <?php
                            $logoPath = !empty($brand['logo_brand']) ? '../../uploads/brands/' . htmlspecialchars($brand['logo_brand']) : '../../assets/img/placeholder-brand.png';
                            $brandName = htmlspecialchars($brand['nama_brand']);
                            $productCount = $brand['product_count'] ?? 0;
                            ?>
                            <a href="productCollection.php?brand=<?= urlencode($brand['id_brand']) ?>" class="brand-item group flex-shrink-0 w-[120px] sm:w-[140px] md:w-[160px] lg:w-[180px]">
                                <div class="bg-white rounded-lg p-3 sm:p-4 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
                                    <div class="h-16 flex items-center justify-center overflow-hidden rounded-lg bg-gray-50 p-2">
                                        <img src="<?= $logoPath ?>" alt="<?= $brandName ?>" class="max-w-full max-h-full w-140px h-auto object-contain transition-transform duration-300 group-hover:scale-110" loading="lazy">
                                    </div>
                                    <!-- <div class="mt-2 text-center">
                                        <h3 class="font-semibold text-gray-800 text-xs truncate"><?= $brandName ?></h3>
                                        <p class="text-[10px] text-gray-500 mt-0.5"><?= $productCount ?> Produk</p>
                                    </div> -->
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php foreach ($brands as $brand): ?>
                            <?php
                            $logoPath = !empty($brand['logo_brand']) ? '../../uploads/brands/' . htmlspecialchars($brand['logo_brand']) : '../../assets/img/placeholder-brand.png';
                            $brandName = htmlspecialchars($brand['nama_brand']);
                            $productCount = $brand['product_count'] ?? 0;
                            ?>
                            <a href="productCollection.php?brand=<?= urlencode($brand['id_brand']) ?>" class="brand-item group flex-shrink-0 w-[140px] sm:w-[140px] md:w-[160px] lg:w-[180px]">
                                <div class="bg-white rounded-lg p-3 sm:p-4 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
                                    <div class="h-16 flex items-center justify-center overflow-hidden rounded-lg bg-gray-50 p-2">
                                        <img src="<?= $logoPath ?>" alt="<?= $brandName ?>" class="max-w-full max-h-full w-140px h-auto object-contain transition-transform duration-300 group-hover:scale-110" loading="lazy">
                                    </div>
                                    <!-- <div class="mt-2 text-center">
                                        <h3 class="font-semibold text-gray-800 text-xs truncate"><?= $brandName ?></h3>
                                        <p class="text-[10px] text-gray-500 mt-0.5"><?= $productCount ?> Produk</p>
                                    </div> -->
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-white/70">
                    <p>Belum ada brand tersedia</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- JELAJAH KATALOG - Dynamic from Database -->
        <section class="w-full px-5 md:px-8 lg:px-20 mt-8">
            <div class="py-4 w-full">
                <div class="mx-auto">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Jelajah Katalog</h2>
                            <p class="text-gray-500 text-sm mt-1">Temukan berbagai produk pilihan untuk kebutuhan PC Anda</p>
                        </div>
                    </div>

                    <?php if (!empty($catalogProducts)): ?>
                        <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                            <?php foreach ($catalogProducts as $product): ?>
                                <?= renderProductCard($product, $productHelper) ?>
                            <?php endforeach; ?>
                        </ul>

                        <div class="flex justify-center mt-8">
                            <a href="productCollection.php" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-[#A14646] transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                <span>Lihat Semua Produk</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-16 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-lg font-medium">Belum ada produk tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php

        use App\Database\DatabaseConnection;

        $pdo = DatabaseConnection::getInstance()->getConnection();

        $sqlArticle = "
            SELECT 
                bp.id_post,
                bp.judul,
                bp.slug,
                bp.excerpt,
                bp.thumbnail,
                bp.views,
                bp.published_at,
                bc.nama_kategori,
                a.nama_lengkap AS author
            FROM blog_posts bp
            JOIN blog_categories bc ON bp.id_category = bc.id_category
            JOIN administrators a ON bp.id_admin = a.id_admin
            WHERE bp.status = 'publish'
            ORDER BY bp.published_at DESC
            LIMIT 3
        ";

        $stmtArticle = $pdo->prepare($sqlArticle);
        $stmtArticle->execute();
        $articles = $stmtArticle->fetchAll();

        $hasArticle = count($articles) > 0;
        ?>



        <!-- ARTICLES SECTION -->
        <section class="w-full px-5 md:px-8 lg:px-20 mt-8">
            <div class="py-4 w-full">

                <!-- Header -->
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">
                            Blog & Artikel Terbaru
                        </h2>
                        <p class="text-gray-500">
                            Wawasan, tips, dan berita terkini seputar teknologi.
                        </p>
                    </div>

                    <a href="blogNews.php"
                        class="text-sm font-medium text-[#882426] hover:underline">
                        Lihat Blog & Artikel Lainnya →
                    </a>
                </div>

                <?php if ($hasArticle): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <?php foreach ($articles as $row): ?>
                            <?php
                            // estimasi waktu baca (200 kata / menit)
                            $wordCount = str_word_count(strip_tags($row['excerpt']));
                            $readTime = max(1, ceil($wordCount / 200));
                            ?>
                            <article
                                class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:shadow-xl hover:-translate-y-1 duration-300">

                                <!-- Thumbnail (NO CROP) -->
                                <a href="articleTemplate.php?slug=<?= htmlspecialchars($row['slug']); ?>"
                                    class="block bg-gray-50 h-48 w-full overflow-hidden flex items-center justify-center shrink-0">
                                    <img
                                        src="../../uploads/blog/<?= htmlspecialchars($row['thumbnail']); ?>"
                                        alt="<?= htmlspecialchars($row['judul']); ?>"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-110">
                                </a>

                                <!-- Content -->
                                <div class="p-6 flex flex-col flex-1">

                                    <!-- Meta -->
                                    <div class="flex items-center gap-3 text-xs mb-3">
                                        <span class="px-3 py-1 rounded-full bg-[#882426]/10 text-[#882426] font-medium">
                                            <?= htmlspecialchars($row['nama_kategori']); ?>
                                        </span>
                                        <span class="text-gray-400">
                                            <?= date('d M Y', strtotime($row['published_at'])); ?>
                                        </span>
                                        <span class="text-gray-400">
                                            • <?= $readTime; ?> menit
                                        </span>
                                        <span class="text-gray-400">
                                            • <?= (int)$row['views']; ?> views
                                        </span>
                                    </div>

                                    <!-- Judul (tinggi konsisten) -->
                                    <h3 class="text-lg font-bold text-gray-900 leading-snug mb-3 line-clamp-2 min-h-[3.5rem]">
                                        <a href="articleTemplate.php?slug=<?= htmlspecialchars($row['slug']); ?>"
                                            class="hover:text-[#882426] transition-colors">
                                            <?= htmlspecialchars($row['judul']); ?>
                                        </a>
                                    </h3>

                                    <!-- Excerpt (tinggi konsisten) -->
                                    <p class="text-sm text-gray-600 leading-relaxed mb-6 line-clamp-3 min-h-[4.5rem]">
                                        <?= htmlspecialchars($row['excerpt']); ?>
                                    </p>

                                    <!-- Footer (SELALU DI BAWAH) -->
                                    <a href="articleTemplate.php?slug=<?= htmlspecialchars($row['slug']); ?>"
                                        class="inline-flex items-center gap-1 text-sm font-medium text-[#882426] hover:gap-2 transition-all mt-auto">
                                        Baca Selengkapnya
                                        <span>→</span>
                                    </a>

                                </div>
                            </article>


                        <?php endforeach; ?>

                    </div>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-gray-50 rounded-xl">
                        <p class="text-lg font-semibold text-gray-700">
                            Belum ada artikel
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            Artikel terbaru akan segera kami hadirkan.
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="/assets/js/users/dashboard.js"></script>

    <!-- Category Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('category-slider');
            const prevBtn = document.getElementById('category-prev');
            const nextBtn = document.getElementById('category-next');
            const dots = document.querySelectorAll('.category-dot');

            if (!slider) return;

            let currentIndex = 0;
            const cards = slider.querySelectorAll('.category-card');

            function getVisibleCards() {
                const screenWidth = window.innerWidth;
                if (screenWidth >= 1280) return 6;
                if (screenWidth >= 1024) return 5;
                if (screenWidth >= 768) return 4;
                if (screenWidth >= 640) return 3;
                return 2;
            }

            function getCardDimensions() {
                const card = cards[0];
                if (!card) return {
                    width: 150,
                    gap: 12
                };
                const style = window.getComputedStyle(slider);
                const gap = parseInt(style.gap) || 12;
                return {
                    width: card.offsetWidth,
                    gap
                };
            }

            function getMaxIndex() {
                const visibleCards = getVisibleCards();
                return Math.max(0, Math.ceil(cards.length / visibleCards) - 1);
            }

            function updateButtonStates() {
                const maxIndex = getMaxIndex();
                if (prevBtn) {
                    prevBtn.disabled = currentIndex === 0;
                }
                if (nextBtn) {
                    nextBtn.disabled = currentIndex >= maxIndex;
                }
            }

            function updateSlider() {
                const {
                    width,
                    gap
                } = getCardDimensions();
                const visibleCards = getVisibleCards();
                const offset = currentIndex * visibleCards * (width + gap);
                const maxOffset = slider.scrollWidth - slider.parentElement.offsetWidth;
                const clampedOffset = Math.min(offset, maxOffset);

                slider.style.transform = `translateX(-${Math.max(0, clampedOffset)}px)`;

                updateButtonStates();

                const maxIndex = getMaxIndex();
                dots.forEach((dot, i) => {
                    if (i === currentIndex) {
                        dot.classList.add('bg-primary', 'w-5');
                        dot.classList.remove('bg-gray-300', 'w-2');
                    } else {
                        dot.classList.remove('bg-primary', 'w-5');
                        dot.classList.add('bg-gray-300', 'w-2');
                    }
                    dot.style.display = i <= maxIndex ? 'block' : 'none';
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentIndex = Math.max(0, currentIndex - 1);
                    updateSlider();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const maxIndex = getMaxIndex();
                    currentIndex = Math.min(maxIndex, currentIndex + 1);
                    updateSlider();
                });
            }

            dots.forEach((dot, i) => {
                dot.addEventListener('click', () => {
                    const maxIndex = getMaxIndex();
                    currentIndex = Math.min(i, maxIndex);
                    updateSlider();
                });
            });

            let touchStartX = 0;
            let touchEndX = 0;

            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, {
                passive: true
            });

            slider.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                const maxIndex = getMaxIndex();

                if (Math.abs(diff) > 50) {
                    if (diff > 0 && currentIndex < maxIndex) {
                        currentIndex++;
                    } else if (diff < 0 && currentIndex > 0) {
                        currentIndex--;
                    }
                    updateSlider();
                }
            }, {
                passive: true
            });

            let autoSlide = setInterval(() => {
                const maxIndex = getMaxIndex();
                currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
                updateSlider();
            }, 5000);

            slider.parentElement.addEventListener('mouseenter', () => clearInterval(autoSlide));
            slider.parentElement.addEventListener('mouseleave', () => {
                autoSlide = setInterval(() => {
                    const maxIndex = getMaxIndex();
                    currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
                    updateSlider();
                }, 5000);
            });

            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const maxIndex = getMaxIndex();
                    currentIndex = Math.min(currentIndex, maxIndex);
                    updateSlider();
                }, 150);
            });

            setTimeout(updateSlider, 100);
        });
    </script>

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

        .category-card {
            backface-visibility: hidden;
            -webkit-font-smoothing: subpixel-antialiased;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: skewX(-25deg);
            transition: 0.75s;
            z-index: 25;
        }

        .category-card:hover::before {
            left: 125%;
        }

        #category-slider {
            will-change: transform;
        }

        .category-dot {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 639px) {
            .category-card {
                min-width: 130px !important;
                max-width: 160px !important;
            }
        }

        @media (min-width: 640px) and (max-width: 767px) {
            .category-card {
                min-width: 140px !important;
                max-width: 180px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .category-card {
                min-width: 150px !important;
                max-width: 190px !important;
            }
        }

        @media (min-width: 1024px) {
            .category-card {
                min-width: 160px !important;
                max-width: 200px !important;
            }
        }

        /* Infinite scroll animation for brand slider */
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .brand-slider {
            animation: scroll 30s linear infinite;
        }

        .brand-slider:hover {
            animation-play-state: paused;
        }

        .brand-slider-container {
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }

        /* Banner Swiper Styling */
        .banner-swiper {
            border-radius: 0;
        }

        .swiper-pagination-bullet {
            background-color: rgba(255, 255, 255, 0.7);
            opacity: 1;
            width: 10px;
            height: 10px;
            transition: all 0.3s ease;
        }

        .swiper-pagination-bullet-active {
            background-color: #fff;
            opacity: 1;
            width: 28px;
            border-radius: 5px;
        }

        .banner-swiper .swiper-button-prev,
        .banner-swiper .swiper-button-next {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .banner-swiper:hover .swiper-button-prev,
        .banner-swiper:hover .swiper-button-next {
            opacity: 1;
        }

        .banner-swiper .swiper-button-prev::after,
        .banner-swiper .swiper-button-next::after {
            content: '';
        }

        .banner-swiper .swiper-button-prev::before {
            content: '‹';
            font-size: 28px;
            color: white;
        }

        .banner-swiper .swiper-button-next::before {
            content: '›';
            font-size: 28px;
            color: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bannerSwiper = new Swiper('.banner-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 1000,
                spaceBetween: 0,
                allowTouchMove: true
            });
        });
    </script>

    <?php include '../../components/users/loginRequiredModal.php'; ?>
    <script>
        window.APP_CONFIG = {
            baseUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>',
            apiUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/api'
        };
    </script>
    <script src="../../assets/js/users/product-actions.js"></script>
</body>

</html>