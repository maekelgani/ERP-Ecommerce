<?php
$pageTitle = "Semua Brand";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

use App\Helper\BrandLandingHelper;

$brandHelper = new BrandLandingHelper();
$brands = $brandHelper->getAllBrands();

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Brand', 'url' => null]
];
?>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]">
        <div class="w-full px-4 md:px-8 lg:px-20 py-6">

            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Semua Brand</h1>
                <p class="text-gray-600">Jelajahi berbagai brand terpercaya untuk kebutuhan komputer dan aksesoris Anda</p>
            </div>

            <?php if (!empty($brands)): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                    <?php foreach ($brands as $index => $brand): ?>
                        <?php
                        $logoValue = $brand['logo_brand'] ?? '';
                        if (empty(trim($logoValue))) {
                            $logoPath = '../../assets/img/placeholder-brand.png';
                        } elseif (str_starts_with($logoValue, 'http://') || str_starts_with($logoValue, 'https://') || str_starts_with($logoValue, '/uploads/') || str_starts_with($logoValue, 'uploads/')) {
                            $logoPath = htmlspecialchars($logoValue);
                            if (str_starts_with($logoPath, '/uploads/')) {
                                $logoPath = '../..' . $logoPath;
                            } elseif (str_starts_with($logoPath, 'uploads/')) {
                                $logoPath = '../../' . $logoPath;
                            }
                        } else {
                            $logoPath = '../../uploads/brands/' . htmlspecialchars($logoValue);
                        }
                        $brandName = htmlspecialchars($brand['nama_brand']);
                        $brandId = htmlspecialchars($brand['id_brand']);
                        $brandDesc = htmlspecialchars($brand['desc_brand'] ?? '');
                        $productCount = $brand['product_count'] ?? 0;
                        $website = $brand['website'] ?? '';
                        ?>

                        <a href="productCollection.php?brand=<?= urlencode($brandId) ?>"
                            class="group relative rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-white"
                            style="animation: fadeInUp 0.4s ease-out <?= $index * 0.05 ?>s both;">

                            <div class="relative aspect-square w-full overflow-hidden bg-white p-4 flex items-center justify-center">
                                <img alt="<?= $brandName ?>"
                                    class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-110"
                                    src="<?= $logoPath ?>"
                                    loading="lazy"
                                    onerror="this.src='../../assets/img/placeholder-brand.png'">

                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            </div>

                            <div class="p-3 bg-gray-50 border-t border-gray-100">
                                <h3 class="font-semibold text-gray-900 text-sm md:text-base truncate group-hover:text-primary transition-colors duration-200 text-center">
                                    <?= $brandName ?>
                                </h3>
                                <?php if ($productCount > 0): ?>
                                    <p class="text-xs text-gray-500 mt-0.5 text-center"><?= $productCount ?> produk</p>
                                <?php else: ?>
                                    <p class="text-xs text-gray-400 mt-0.5 text-center">Belum ada produk</p>
                                <?php endif; ?>
                            </div>

                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-primary/30 rounded-xl transition-all duration-300 pointer-events-none"></div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 text-center text-gray-500 text-sm">
                    Menampilkan <?= count($brands) ?> brand
                </div>
            <?php else: ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Brand</h3>
                    <p class="text-gray-500 mb-6">Brand produk belum tersedia saat ini</p>
                    <a href="landingPage.php" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Home
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

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
    </style>
</body>

</html>