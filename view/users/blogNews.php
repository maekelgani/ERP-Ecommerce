<?php
$pageTitle = "Blog & Berita";
require_once __DIR__ . '/../../config/config.php';

use App\Repository\BlogPostRepository;
use App\Repository\BlogCategoryRepository;

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Blog & Berita', 'url' => null]
];

$blogPostRepo = new BlogPostRepository();
$blogCategoryRepo = new BlogCategoryRepository();

$dbCategories = $blogCategoryRepo->getAll(true);
$categories = [['id' => null, 'nama_kategori' => 'Semua', 'slug' => 'semua']];
foreach ($dbCategories as $cat) {
    $categories[] = $cat;
}

$selectedCategorySlug = $_GET['category'] ?? 'semua';
$selectedCategoryId = null;
$selectedCategoryName = 'Semua';

foreach ($categories as $cat) {
    if (strtolower($cat['slug'] ?? 'semua') === strtolower($selectedCategorySlug)) {
        $selectedCategoryId = $cat['id'] ?? $cat['id_category'] ?? null;
        $selectedCategoryName = $cat['nama_kategori'];
        break;
    }
}

$perPage = 9;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $perPage;

$totalPosts = $blogPostRepo->count([
    'published_only' => true,
    'category' => $selectedCategoryId
]);

$totalPages = ceil($totalPosts / $perPage);
$currentPage = min($currentPage, max(1, $totalPages));
$offset = ($currentPage - 1) * $perPage;

$blogPosts = $blogPostRepo->getPublishedPosts($perPage, $offset, $selectedCategoryId);

function formatDate($dateString)
{
    if (!$dateString) return '-';
    $date = new DateTime($dateString);
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return $date->format('d') . ' ' . $months[(int)$date->format('m') - 1] . ' ' . $date->format('Y');
}

function estimateReadTime($content)
{
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200);
    return $minutes . ' menit';
}

function getCategoryColor($categoryName)
{
    $colors = [
        'Hardware' => 'from-red-500 to-orange-500',
        'Tips & Tutorial' => 'from-green-500 to-teal-500',
        'Berita' => 'from-blue-500 to-cyan-500',
        'Review' => 'from-purple-500 to-pink-500',
        'Promo' => 'from-rose-500 to-red-600'
    ];
    return $colors[$categoryName] ?? 'from-gray-500 to-gray-600';
}
?>

<body class="w-full min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[165px]">
        <section class="relative h-[200px] md:h-[280px] overflow-hidden mb-8">
            <div class="absolute inset-0 bg-[#882426]">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="news" width="20" height="20" patternUnits="userSpaceOnUse">
                                <rect x="2" y="2" width="8" height="6" fill="none" stroke="white" stroke-width="0.5" />
                                <line x1="3" y1="5" x2="9" y2="5" stroke="white" stroke-width="0.3" />
                                <line x1="3" y1="6.5" x2="7" y2="6.5" stroke="white" stroke-width="0.3" />
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#news)" />
                    </svg>
                </div>
                <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-3">Blog & Berita</h1>
                <p class="text-white/80 text-sm md:text-lg max-w-2xl">Wawasan, tips, dan berita terkini seputar teknologi komputer</p>
            </div>
        </section>

        <div class="w-full px-4 md:px-8 lg:px-20 py-6">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <?php foreach ($categories as $category): ?>
                            <?php $catSlug = $category['slug'] ?? 'semua'; ?>
                            <a href="?category=<?= urlencode($catSlug) ?>"
                                class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 <?= strtolower($selectedCategorySlug) === strtolower($catSlug) ? 'bg-[#882426] text-white' : 'text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5' ?>">
                                <?= htmlspecialchars($category['nama_kategori']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($blogPosts)): ?>
                <?php $allPosts = $blogPosts;
                $firstPost = array_shift($allPosts); ?>
                <section class="mb-10">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="relative aspect-video lg:aspect-auto overflow-hidden">
                                <?php if ($firstPost['thumbnail']): ?>
                                    <img src="../../uploads/blog/<?= htmlspecialchars($firstPost['thumbnail']) ?>"
                                        alt="<?= htmlspecialchars($firstPost['judul']) ?>"
                                        class="absolute inset-0 w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="absolute inset-0 bg-gradient-to-br <?= getCategoryColor($firstPost['nama_kategori'] ?? '') ?>">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                            </svg>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-[#882426] text-xs font-semibold rounded-full"><?= htmlspecialchars($firstPost['nama_kategori'] ?? 'Uncategorized') ?></span>
                                </div>
                            </div>
                            <div class="p-6 lg:p-8 flex flex-col justify-center">
                                <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?= formatDate($firstPost['published_at']) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= estimateReadTime($firstPost['konten']) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <?= number_format($firstPost['views'] ?? 0) ?>
                                    </span>
                                </div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 group-hover:text-[#882426] transition-colors"><?= htmlspecialchars($firstPost['judul']) ?></h2>
                                <p class="text-gray-600 mb-6"><?= htmlspecialchars($firstPost['excerpt'] ?: substr(strip_tags($firstPost['konten']), 0, 200) . '...') ?></p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center text-[#882426] font-semibold">
                                            <?= strtoupper(substr($firstPost['author_name'] ?? 'A', 0, 1)) ?>
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium"><?= htmlspecialchars($firstPost['author_name'] ?? 'Admin') ?></span>
                                    </div>
                                    <a href="articleTemplate.php?slug=<?= urlencode($firstPost['slug']) ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300">
                                        Baca Selengkapnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if (!empty($allPosts)): ?>
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Artikel Lainnya</h2>
                            <span class="text-sm text-gray-500"><?= $totalPosts - 1 ?> artikel lainnya</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php foreach ($allPosts as $post): ?>
                                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all duration-300">
                                    <a href="articleTemplate.php?slug=<?= urlencode($post['slug']) ?>" class="block">
                                        <div class="relative aspect-video overflow-hidden">
                                            <?php if ($post['thumbnail']): ?>
                                                <img src="../../uploads/blog/<?= htmlspecialchars($post['thumbnail']) ?>"
                                                    alt="<?= htmlspecialchars($post['judul']) ?>"
                                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <?php else: ?>
                                                <div class="absolute inset-0 bg-gradient-to-br <?= getCategoryColor($post['nama_kategori'] ?? '') ?> flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                                            <div class="absolute top-3 left-3">
                                                <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-[#882426] text-xs font-semibold rounded-full"><?= htmlspecialchars($post['nama_kategori'] ?? 'Uncategorized') ?></span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-5">
                                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                                            <span><?= formatDate($post['published_at']) ?></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span><?= estimateReadTime($post['konten']) ?></span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span><?= number_format($post['views'] ?? 0) ?> views</span>
                                        </div>
                                        <a href="articleTemplate.php?slug=<?= urlencode($post['slug']) ?>">
                                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#882426] transition-colors"><?= htmlspecialchars($post['judul']) ?></h3>
                                        </a>
                                        <p class="text-gray-600 text-sm line-clamp-2 mb-4"><?= htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['konten']), 0, 100) . '...') ?></p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 bg-[#882426]/10 rounded-full flex items-center justify-center text-[#882426] text-xs font-semibold">
                                                    <?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?>
                                                </div>
                                                <span class="text-xs text-gray-600"><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></span>
                                            </div>
                                            <a href="articleTemplate.php?slug=<?= urlencode($post['slug']) ?>" class="text-[#882426] text-sm font-medium hover:underline">Baca</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <div class="mt-10 flex justify-center">
                        <nav class="inline-flex items-center gap-1 bg-white rounded-xl shadow-sm border border-gray-100 p-1">
                            <?php if ($currentPage > 1): ?>
                                <a href="?category=<?= urlencode($selectedCategorySlug) ?>&page=<?= $currentPage - 1 ?>" class="p-2 text-gray-600 hover:text-[#882426] rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            <?php else: ?>
                                <button class="p-2 text-gray-400 rounded-lg cursor-not-allowed" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);

                            if ($startPage > 1): ?>
                                <a href="?category=<?= urlencode($selectedCategorySlug) ?>&page=1" class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors flex items-center justify-center">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <span class="w-10 h-10 text-sm font-medium text-white bg-[#882426] rounded-lg flex items-center justify-center"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="?category=<?= urlencode($selectedCategorySlug) ?>&page=<?= $i ?>" class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors flex items-center justify-center"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="?category=<?= urlencode($selectedCategorySlug) ?>&page=<?= $totalPages ?>" class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors flex items-center justify-center"><?= $totalPages ?></a>
                            <?php endif; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?category=<?= urlencode($selectedCategorySlug) ?>&page=<?= $currentPage + 1 ?>" class="p-2 text-gray-600 hover:text-[#882426] rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            <?php else: ?>
                                <button class="p-2 text-gray-400 rounded-lg cursor-not-allowed" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                    <p class="text-gray-500 mb-6">Tidak ada artikel dalam kategori ini. Coba pilih kategori lain.</p>
                    <a href="?category=semua" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300">
                        Lihat Semua Artikel
                    </a>
                </div>
            <?php endif; ?>

            <section class="mt-16">
                <div class="bg-[#882426] rounded-2xl p-6 md:p-10 text-center text-white">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-3">Berlangganan Newsletter</h3>
                    <p class="text-white/80 mb-6 max-w-lg mx-auto">Dapatkan update terbaru tentang promo, artikel, dan tips teknologi langsung ke inbox Anda.</p>
                    <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                        <input type="email" placeholder="Masukkan email Anda" class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all">
                        <button type="submit" class="px-6 py-3 bg-white text-[#882426] font-medium rounded-xl hover:bg-gray-100 transition-all duration-300">
                            Langganan
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
</body>

</html>