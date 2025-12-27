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

$allProductIds = array_merge(
    array_column($bestSellers, 'id_product'),
    array_column($newProducts, 'id_product'),
    array_column($catalogProducts, 'id_product')
);
$allProductIds = array_unique($allProductIds);
$soldRatings = $productHelper->getProductsSoldAndRatings($allProductIds);

foreach ($bestSellers as &$product) {
    $pid = $product['id_product'];
    $product["total_terjual"] = $soldRatings[$pid]['sold_count'] ?? 0;
    $product['avg_rating'] = $soldRatings[$pid]['avg_rating'] ?? 0;
    $product["total_reviews"] = $soldRatings[$pid]['review_count'] ?? 0;
}
unset($product);

foreach ($newProducts as &$product) {
    $pid = $product['id_product'];
    $product["total_terjual"] = $soldRatings[$pid]['sold_count'] ?? 0;
    $product['avg_rating'] = $soldRatings[$pid]['avg_rating'] ?? 0;
    $product["total_reviews"] = $soldRatings[$pid]['review_count'] ?? 0;
}
unset($product);

foreach ($catalogProducts as &$product) {
    $pid = $product['id_product'];
    $product["total_terjual"] = $soldRatings[$pid]['sold_count'] ?? 0;
    $product['avg_rating'] = $soldRatings[$pid]['avg_rating'] ?? 0;
    $product["total_reviews"] = $soldRatings[$pid]['review_count'] ?? 0;
}
unset($product);

?>

<body class="w-full bg-no-repeat min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <?php include '../../components/users/toastNotifications.php'; ?>
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <!-- Main content with responsive padding to offset fixed navbar -->
    <!-- Mobile: navbar 64px, Desktop: promo banner 44px + navbar 72px = 116px -->
    <main class="max-w-full mb-10 pt-16 md:pt-[116px]">
        <!-- banner promosi slider -->
        <section class="w-full relative overflow-hidden group">
            <div class="swiper banner-swiper w-full aspect-[4/3] sm:aspect-[16/9] lg:aspect-[21/9] xl:aspect-[21/8]">
                <div class="swiper-wrapper">

                    <!-- Slide 1: Build Your Dream PC -->
                    <div class="swiper-slide slide-1 relative overflow-hidden">
                        <!-- Gradient overlay - responsive -->
                        <div class="absolute inset-0 banner-gradient-left z-10"></div>

                        <!-- Image with responsive object-position -->
                        <img
                            alt="Premium Gaming Hardware"
                            class="banner-slide-img w-full h-full transform scale-100 group-hover:scale-105 transition-transform duration-[10000ms] ease-linear"
                            src="../../assets/img/banner/slider-1.png"
                            loading="eager">

                        <!-- Content - Mobile: bottom aligned, Desktop: center left -->
                        <div class="absolute inset-0 z-20 flex items-end sm:items-center px-4 sm:px-8 md:px-16 lg:px-24 pb-16 sm:pb-0">
                            <div class="slide-content max-w-xl space-y-3 sm:space-y-4 md:space-y-5">
                                <!-- Badge -->
                                <span class="banner-badge inline-block px-3 sm:px-4 py-1.5 bg-primary text-white text-[11px] sm:text-xs font-bold tracking-wider uppercase rounded">
                                    New Arrival
                                </span>

                                <!-- Title - Match reference exactly -->
                                <h2 class="banner-title text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] drop-shadow-lg">
                                    Build Your<br>
                                    <span class="text-primary italic">Dream PC</span> Today
                                </h2>

                                <!-- Description -->
                                <p class="banner-subtitle text-gray-200 text-sm sm:text-base md:text-lg max-w-sm sm:max-w-md drop-shadow-md leading-relaxed">
                                    Dapatkan komponen hardware terbaik dengan performa maksimal untuk kebutuhan gaming dan workstation Anda.
                                </p>

                                <!-- Buttons - Side by side -->
                                <div class="pt-2 sm:pt-4 flex flex-row flex-wrap gap-3 sm:gap-4">
                                    <a href="productCollection.php" class="banner-btn px-5 sm:px-6 md:px-8 py-2.5 sm:py-3 bg-primary hover:bg-[#A14646] text-white text-sm sm:text-base font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                        Belanja Sekarang
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                    <a href="#category-section" class="banner-btn px-5 sm:px-6 md:px-8 py-2.5 sm:py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/30 text-sm sm:text-base font-bold rounded-lg transition-all flex items-center justify-center">
                                        Lihat Kategori
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Hatsune Miku x ROG -->
                    <div class="swiper-slide slide-2 relative overflow-hidden">
                        <!-- Gradient overlay - responsive -->
                        <div class="absolute inset-0 banner-gradient-right z-10"></div>

                        <!-- Image -->
                        <img
                            alt="Limited Collaboration - Hatsune Miku x ROG"
                            class="banner-slide-img w-full h-full"
                            src="../../assets/img/banner/collab-mikuXrog.png"
                            loading="lazy">

                        <!-- Content - Mobile: bottom center, Desktop: center right -->
                        <div class="absolute inset-0 z-20 flex items-end sm:items-center justify-center sm:justify-end px-4 sm:px-8 md:px-16 lg:px-24 pb-16 sm:pb-0 text-center sm:text-right">
                            <div class="max-w-xl space-y-2 sm:space-y-3 md:space-y-4">
                                <!-- Badge -->
                                <span class="banner-badge inline-block px-2 sm:px-3 py-1 bg-blue-600 text-white text-[10px] sm:text-xs font-bold tracking-wider uppercase rounded-full">
                                    Special Edition
                                </span>

                                <!-- Title -->
                                <h2 class="banner-title text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight drop-shadow-lg">
                                    Hatsune Miku <br>
                                    x <span class="text-blue-400">ROG</span> Bundle
                                </h2>

                                <!-- Description -->
                                <p class="banner-subtitle text-gray-200 text-xs sm:text-sm md:text-base lg:text-lg max-w-xs sm:max-w-sm md:max-w-md mx-auto sm:ml-auto sm:mr-0 drop-shadow-md">
                                    Koleksi terbatas bertema Hatsune Miku. Estetika premium bertemu dengan performa legendaris ROG.
                                </p>

                                <!-- Button -->
                                <div class="pt-2 sm:pt-4 flex flex-wrap gap-2 sm:gap-4 justify-center sm:justify-end">
                                    <a href="productCollection.php?search=Miku" class="banner-btn px-4 sm:px-6 md:px-8 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm md:text-base font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                        Cek Koleksi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Year End Mega Sale -->
                    <div class="swiper-slide slide-3 relative overflow-hidden">
                        <!-- Gradient overlay -->
                        <div class="absolute inset-0 banner-gradient-center z-10"></div>

                        <!-- Image -->
                        <img
                            alt="Best Seller Hardware"
                            class="banner-slide-img w-full h-full"
                            src="../../assets/img/banner/slider-3.png"
                            loading="lazy">

                        <!-- Content - Always center -->
                        <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6">
                            <div class="max-w-2xl space-y-2 sm:space-y-3 md:space-y-4">
                                <!-- Title -->
                                <h2 class="banner-title text-2xl sm:text-4xl md:text-6xl lg:text-7xl font-black text-white leading-tight tracking-tighter drop-shadow-2xl uppercase">
                                    Year End <span class="text-primary italic">Mega Sale</span>
                                </h2>

                                <!-- Discount info -->
                                <p class="text-white text-sm sm:text-lg md:text-2xl font-medium drop-shadow-lg">
                                    Diskon Hingga <span class="text-xl sm:text-2xl md:text-3xl font-bold text-yellow-400">50%</span> Untuk Produk Terpilih
                                </p>

                                <!-- Button -->
                                <div class="pt-3 sm:pt-6">
                                    <a href="productCollection.php?sort=best" class="banner-btn-large inline-block px-6 sm:px-8 md:px-10 py-2 sm:py-3 md:py-4 bg-white text-primary hover:bg-primary hover:text-white text-sm sm:text-base md:text-lg font-black rounded-full transition-all transform hover:scale-110 shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                                        AMBIL PROMO
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Custom Navigation Controls - Pagination -->
                <div class="absolute bottom-4 sm:bottom-6 md:bottom-8 left-1/2 -translate-x-1/2 z-30 flex items-center gap-4 sm:gap-6">
                    <div class="banner-pagination !relative !bottom-0 !w-auto !flex gap-1.5 sm:gap-2"></div>
                </div>

                <!-- Navigation Arrows - Always visible on touch, hover on desktop -->
                <button class="banner-prev banner-nav-btn absolute left-2 sm:left-4 md:left-8 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 flex items-center justify-center bg-white/10 hover:bg-primary backdrop-blur-md text-white border border-white/20 rounded-full transition-all lg:opacity-0 lg:group-hover:opacity-100 transform lg:-translate-x-4 lg:group-hover:translate-x-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button class="banner-next banner-nav-btn absolute right-2 sm:right-4 md:right-8 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 flex items-center justify-center bg-white/10 hover:bg-primary backdrop-blur-md text-white border border-white/20 rounded-full transition-all lg:opacity-0 lg:group-hover:opacity-100 transform lg:translate-x-4 lg:group-hover:translate-x-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Features Highlight Bar -->
            <div class="hidden md:flex bg-white shadow-lg mx-6 md:mx-16 lg:mx-24 rounded-xl -mt-8 relative z-40 border border-gray-100 divide-x divide-gray-100">
                <div class="flex-1 p-5 flex items-center justify-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Produk Original</h4>
                        <p class="text-xs text-gray-500">Garansi Resmi 100%</p>
                    </div>
                </div>
                <div class="flex-1 p-5 flex items-center justify-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Pengiriman Cepat</h4>
                        <p class="text-xs text-gray-500">Tiba Dalam 24 Jam</p>
                    </div>
                </div>
                <div class="flex-1 p-5 flex items-center justify-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Cicilan 0%</h4>
                        <p class="text-xs text-gray-500">Hingga 12 Bulan</p>
                    </div>
                </div>
                <div class="flex-1 p-5 flex items-center justify-center gap-4 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Support 24/7</h4>
                        <p class="text-xs text-gray-500">Bantuan Ahli IT</p>
                    </div>
                </div>
            </div>
        </section>

        <style>
            /* Keyframe animation untuk slide content */
            @keyframes slideInUp {
                0% {
                    opacity: 0;
                    transform: translateY(30px);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Custom responsive banner styles */
            .banner-slide-img {
                object-fit: cover;
                object-position: center center;
            }

            /* Slide content animation - ensure visibility */
            .slide-content {
                opacity: 1 !important;
                transform: translateY(0) !important;
                animation: slideInUp 0.8s ease-out forwards;
            }

            .swiper-slide-active .slide-content {
                animation: slideInUp 0.8s ease-out 0.3s forwards;
            }

            /* Slide 1: Focus on left side where PC build is */
            .slide-1 .banner-slide-img {
                object-position: left center;
            }

            /* Slide 2: Focus on center-right where Miku character is */
            .slide-2 .banner-slide-img {
                object-position: 70% center;
            }

            @media (max-width: 768px) {
                .slide-2 .banner-slide-img {
                    object-position: 60% center;
                }
            }

            /* Slide 3: Center focus for mega sale */
            .slide-3 .banner-slide-img {
                object-position: center center;
            }

            /* Mobile-first text improvements */
            @media (max-width: 640px) {
                .banner-title {
                    font-size: 1.5rem !important;
                    line-height: 1.2 !important;
                }

                .banner-subtitle {
                    font-size: 0.75rem !important;
                    line-height: 1.4 !important;
                }

                .banner-badge {
                    font-size: 0.625rem !important;
                    padding: 0.25rem 0.5rem !important;
                }

                .banner-btn {
                    padding: 0.5rem 1rem !important;
                    font-size: 0.75rem !important;
                }

                .banner-btn-large {
                    padding: 0.75rem 1.5rem !important;
                    font-size: 0.875rem !important;
                }
            }

            @media (min-width: 641px) and (max-width: 1024px) {
                .banner-title {
                    font-size: 2.25rem !important;
                }
            }

            /* Navigation always visible on mobile */
            @media (max-width: 1024px) {
                .banner-nav-btn {
                    opacity: 1 !important;
                    transform: translateX(0) translateY(-50%) !important;
                }
            }

            /* Gradient overlay improvements for better text readability */
            .banner-gradient-left {
                background: linear-gradient(to right,
                        rgba(0, 0, 0, 0.75) 0%,
                        rgba(0, 0, 0, 0.5) 30%,
                        rgba(0, 0, 0, 0.2) 60%,
                        transparent 100%);
            }

            .banner-gradient-right {
                background: linear-gradient(to left,
                        rgba(0, 0, 0, 0.75) 0%,
                        rgba(0, 0, 0, 0.5) 30%,
                        rgba(0, 0, 0, 0.2) 60%,
                        transparent 100%);
            }

            .banner-gradient-center {
                background: rgba(0, 0, 0, 0.4);
            }

            @media (max-width: 768px) {

                .banner-gradient-left,
                .banner-gradient-right {
                    background: linear-gradient(to top,
                            rgba(0, 0, 0, 0.85) 0%,
                            rgba(0, 0, 0, 0.5) 40%,
                            rgba(0, 0, 0, 0.2) 70%,
                            transparent 100%);
                }
            }
        </style>


        <!-- CATEGORY - Dynamic from Database -->
        <section class="mt-4 w-full px-4 sm:px-5 md:px-8 lg:px-20" id="category-section">
            <div class="py-5 sm:py-6 md:py-8 px-4 sm:px-5 md:px-6 lg:px-8 rounded-xl shadow-sm w-full bg-white">
                <div class="flex items-center justify-between mb-6 sm:mb-8">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 rounded-full">Kategori</span>
                            </div>
                            <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-gray-900 tracking-tight leading-tight">
                                Shop by <span class="text-primary">Category</span>
                            </h2>
                            <p class="text-[11px] sm:text-xs md:text-sm text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">Temukan produk berdasarkan kategori favorit Anda</p>
                        </div>
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
        <section class="mt-8 sm:mt-12 w-full px-4 sm:px-8 md:px-16 lg:px-20">
            <div class="py-4 w-full">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 sm:mb-8 gap-2">
                    <div>
                        <h2 class="font-extrabold text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-gray-900 tracking-tight">
                            Limited <span class="text-primary italic">Collaboration</span>
                        </h2>
                        <p class="text-gray-500 text-sm sm:text-base mt-2">Koleksi eksklusif hasil kolaborasi brand ternama</p>
                    </div>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    <!-- Main Featured Collab (Full Width) -->
                    <a href="productCollection.php?search=Hatsune+Miku" class="relative group block rounded-2xl overflow-hidden h-[300px] sm:h-[400px] lg:h-[500px] shadow-xl">
                        <img src="../../assets/img/banner/collab-mikuXrog.png" alt="ROG x Miku" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-6 sm:p-10">
                            <span class="inline-block w-fit px-3 py-1 bg-blue-500 text-white text-[10px] sm:text-xs font-bold uppercase tracking-widest rounded-full mb-3">ROG EXCLUSIVE</span>
                            <h3 class="text-white text-2xl sm:text-4xl md:text-5xl font-black mb-2 sm:mb-4 tracking-tight uppercase">ROG x Hatsune Miku</h3>
                            <p class="text-gray-200 text-sm sm:text-lg max-w-2xl mb-4 sm:mb-6 line-clamp-2">Edisi terbatas komponen PC bertema Hatsune Miku. Gabungan estetika futuristik dan performa gaming kelas atas.</p>
                            <span class="w-fit px-6 py-2 sm:px-8 sm:py-3 bg-white text-black font-bold rounded-xl transition-all group-hover:bg-primary group-hover:text-white transform group-hover:scale-105 text-sm sm:text-base">
                                Jelajahi Koleksi
                            </span>
                        </div>
                    </a>
                    <!-- Two Side Collabs Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <a href="productCollection.php?search=Monster+Hunter" class="relative group rounded-2xl overflow-hidden h-[250px] sm:h-[300px] shadow-lg">
                            <img src="../../assets/img/banner/collab-msi-mh.jpg" alt="MSI x Monster Hunter" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-6 sm:p-8">
                                <span class="inline-block w-fit px-3 py-1 bg-red-600 text-white text-[10px] sm:text-xs font-bold uppercase tracking-widest rounded-full mb-2">MSI SPECIAL</span>
                                <h3 class="text-white text-xl sm:text-2xl font-black mb-2 tracking-tight uppercase">MSI x Monster Hunter</h3>
                                <p class="text-gray-200 text-xs sm:text-sm mb-4 line-clamp-2">Rayakan 20 tahun Monster Hunter dengan hardware spesial dari MSI.</p>
                                <span class="w-fit px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs sm:text-sm font-bold rounded-lg group-hover:bg-white group-hover:text-black transition-all">
                                    Lihat Detail
                                </span>
                            </div>
                        </a>
                        <a href="productCollection.php?search=Evangelion" class="relative group rounded-2xl overflow-hidden h-[250px] sm:h-[300px] shadow-lg">
                            <img src="../../assets/img/banner/collab-evaXrog.png" alt="ASUS x Evangelion" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-6 sm:p-8">
                                <span class="inline-block w-fit px-3 py-1 bg-purple-600 text-white text-[10px] sm:text-xs font-bold uppercase tracking-widest rounded-full mb-2">EVA EDITION</span>
                                <h3 class="text-white text-xl sm:text-2xl font-black mb-2 tracking-tight uppercase">ASUS x Evangelion</h3>
                                <p class="text-gray-200 text-xs sm:text-sm mb-4 line-clamp-2">Collector's Edition Rig terinspirasi dari anime legendaris Evangelion.</p>
                                <span class="w-fit px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs sm:text-sm font-bold rounded-lg group-hover:bg-white group-hover:text-black transition-all">
                                    Lihat Detail
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- BEST SELLER - Dynamic from Database -->
        <section class="mt-10 w-full px-5 md:px-8 lg:px-20">
            <div class="py-4 w-full">
                <div class="mx-auto">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                    <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-100 rounded-full">Populer</span>
                                    <span class="hidden lg:inline-flex items-center gap-1 text-[10px] text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        Paling Diminati
                                    </span>
                                </div>
                                <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-gray-900 tracking-tight leading-tight">
                                    Best <span class="text-primary">Seller</span>
                                </h2>
                                <p class="text-[11px] sm:text-xs md:text-sm text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">Produk terlaris yang dipercaya pelanggan</p>
                            </div>
                        </div>
                        <a href="productCollection.php?sort=best" class="group inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-primary/5 hover:bg-primary text-primary hover:text-white font-semibold text-xs sm:text-sm rounded-lg sm:rounded-xl transition-all duration-300 hidden sm:inline-flex">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
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
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                    <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-100 rounded-full animate-pulse">Baru!</span>
                                    <span class="hidden lg:inline-flex items-center gap-1 text-[10px] text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                                        </svg>
                                        Baru Ditambahkan
                                    </span>
                                </div>
                                <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-gray-900 tracking-tight leading-tight">
                                    Produk <span class="text-primary">Baru</span>
                                </h2>
                                <p class="text-[11px] sm:text-xs md:text-sm text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">Koleksi terbaru langsung dari distributor resmi</p>
                            </div>
                        </div>
                        <a href="productCollection.php?sort=newest" class="group inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-primary/5 hover:bg-primary text-primary hover:text-white font-semibold text-xs sm:text-sm rounded-lg sm:rounded-xl transition-all duration-300 hidden sm:inline-flex">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
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
                <div class="flex items-center justify-between mb-6 sm:mb-8 md:mb-10">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-white bg-white/20 rounded-full">Partner Resmi</span>
                            </div>
                            <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white tracking-tight leading-tight">
                                Brand <span class="text-white/80 italic">Pilihan</span>
                            </h2>
                            <p class="text-white/60 text-[11px] sm:text-xs md:text-sm mt-0.5 sm:mt-1">Partner terpercaya untuk kebutuhan PC Anda</p>
                        </div>
                    </div>
                    <a href="brandCollection.php" class="group hidden sm:inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-white/10 hover:bg-white text-white hover:text-primary font-semibold text-xs sm:text-sm rounded-lg sm:rounded-xl transition-all duration-300 border border-white/20 hover:border-white backdrop-blur-sm">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
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
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                    <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 rounded-full">Explore</span>
                                    <span class="hidden lg:inline-flex items-center gap-1 text-[10px] text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.5c1.93 0 3.5 1.57 3.5 3.5s-1.57 3.5-3.5 3.5S8.5 11.93 8.5 10 10.07 6.5 12 6.5zM19 18H5v-1c0-2 4-3.1 7-3.1s7 1.1 7 3.1v1z" />
                                        </svg>
                                        Semua Koleksi
                                    </span>
                                </div>
                                <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-gray-900 tracking-tight leading-tight">
                                    Jelajah <span class="text-primary">Katalog</span>
                                </h2>
                                <p class="text-[11px] sm:text-xs md:text-sm text-gray-500 mt-0.5 sm:mt-1 hidden sm:block">Temukan berbagai produk pilihan untuk kebutuhan PC Anda</p>
                            </div>
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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-8 md:mb-10 gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                <span class="hidden md:inline-block px-2 sm:px-2.5 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-violet-600 bg-violet-100 rounded-full">Blog</span>
                                <span class="hidden lg:inline-flex items-center gap-1 text-[10px] text-gray-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                                    </svg>
                                    Insight & Tips
                                </span>
                            </div>
                            <h2 class="font-extrabold text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-gray-900 tracking-tight leading-tight">
                                Blog & <span class="text-primary">Artikel</span> Terbaru
                            </h2>
                            <p class="text-[11px] sm:text-xs md:text-sm text-gray-500 mt-0.5 sm:mt-1">Wawasan, tips, dan berita terkini seputar teknologi</p>
                        </div>
                    </div>
                    <a href="blogNews.php" class="group inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-primary/5 hover:bg-primary text-primary hover:text-white font-semibold text-xs sm:text-sm rounded-lg sm:rounded-xl transition-all duration-300 w-fit">
                        Lihat Semua Artikel
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
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
            overflow: hidden;
        }

        .banner-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .banner-pagination .swiper-pagination-bullet-active {
            width: 30px;
            border-radius: 5px;
            background: #8B1E1E;
            /* Primary color */
        }

        /* Hide default Swiper navigation since we use custom ones */
        .banner-swiper .swiper-button-prev,
        .banner-swiper .swiper-button-next {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Banner Swiper Initialization
            const bannerSwiper = new Swiper('.banner-swiper', {
                loop: true,
                speed: 1000,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.banner-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.banner-next',
                    prevEl: '.banner-prev',
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                on: {
                    init: function() {
                        // Trigger animations for the first slide
                        const activeSlide = this.slides[this.activeIndex];
                        if (activeSlide) {
                            const animatedElements = activeSlide.querySelectorAll('[animation]');
                            animatedElements.forEach(el => {
                                el.style.animation = 'none';
                                el.offsetHeight; // Trigger reflow
                                el.style.animation = null;
                            });
                        }
                    },
                    slideChangeTransitionStart: function() {
                        const activeSlide = this.slides[this.activeIndex];
                        if (activeSlide) {
                            const animatedElements = activeSlide.querySelectorAll('[animation]');
                            animatedElements.forEach(el => {
                                el.style.animation = 'none';
                                el.offsetHeight; // Trigger reflow
                                el.style.animation = null;
                            });
                        }
                    }
                }
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