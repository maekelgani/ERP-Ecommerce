<?php
require_once __DIR__ . '/../../config/config.php';

use App\Repository\BlogPostRepository;
use App\Repository\BlogCategoryRepository;

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

$blogPostRepo = new BlogPostRepository();
$blogCategoryRepo = new BlogCategoryRepository();

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: blogNews.php');
    exit;
}

$article = $blogPostRepo->getBySlug($slug);

if (!$article || $article['status'] !== 'publish') {
    header('Location: blogNews.php');
    exit;
}

$blogPostRepo->incrementViews($article['id_post']);

$relatedPosts = $blogPostRepo->getRelatedPosts($article['id_post'], $article['id_category'], 4);
$popularPosts = $blogPostRepo->getPopularPosts(5, $article['id_post']);
$prevNextPosts = $blogPostRepo->getPrevNextPosts($article['id_post'], $article['published_at']);

$pageTitle = $article['judul'];
include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Blog & Berita', 'url' => 'blogNews.php'],
    ['label' => $article['judul'], 'url' => null]
];

function formatDateFull($dateString)
{
    if (!$dateString) return '-';
    $date = new DateTime($dateString);
    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $date->format('d') . ' ' . $months[(int)$date->format('m') - 1] . ' ' . $date->format('Y');
}

function formatDateShort($dateString)
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
    return $minutes . ' menit baca';
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
    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[166px]">
        <section class="relative h-[250px] md:h-[350px] overflow-hidden">
            <?php if ($article['thumbnail']): ?>
                <img src="../../uploads/blog/<?= htmlspecialchars($article['thumbnail']) ?>"
                    alt="<?= htmlspecialchars($article['judul']) ?>"
                    class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
            <?php else: ?>
                <div class="absolute inset-0 bg-gradient-to-br <?= getCategoryColor($article['nama_kategori'] ?? '') ?>">
                    <div class="absolute inset-0 opacity-20">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="article-pattern" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <rect x="2" y="2" width="8" height="6" fill="none" stroke="white" stroke-width="0.5" />
                                    <line x1="3" y1="5" x2="9" y2="5" stroke="white" stroke-width="0.3" />
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#article-pattern)" />
                        </svg>
                    </div>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <?php endif; ?>

            <div class="relative z-10 h-full flex flex-col justify-end px-4 md:px-8 lg:px-20 pb-8">
                <div class="max-w-4xl">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <a href="blogNews.php?category=<?= urlencode($article['category_slug'] ?? '') ?>"
                            class="px-3 py-1 bg-[#882426] text-white text-xs font-semibold rounded-full hover:bg-[#6a1c1e] transition-colors">
                            <?= htmlspecialchars($article['nama_kategori'] ?? 'Uncategorized') ?>
                        </a>
                        <span class="text-white/80 text-sm"><?= estimateReadTime($article['konten']) ?></span>
                    </div>
                    <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight"><?= htmlspecialchars($article['judul']) ?></h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/80 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white font-semibold backdrop-blur-sm">
                                <?= strtoupper(substr($article['author_name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <span><?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></span>
                        </div>
                        <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?= formatDateFull($article['published_at']) ?>
                        </span>
                        <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <?= number_format($article['views'] + 1) ?> views
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <div class="w-full px-4 md:px-8 lg:px-20 py-8">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                <article class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
                        <?php if ($article['excerpt']): ?>
                            <div class="mb-8 p-4 bg-gray-50 rounded-xl border-l-4 border-[#882426]">
                                <p class="text-gray-700 text-lg italic"><?= htmlspecialchars($article['excerpt']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="article-content">
                            <?= $article['konten'] ?>
                        </div>

                        <style>
                            .article-content {
                                color: #374151;
                                line-height: 1.75;
                            }

                            .article-content p {
                                margin-bottom: 1rem;
                                color: #374151;
                            }

                            .article-content a {
                                color: #882426;
                                text-decoration: none;
                            }

                            .article-content a:hover {
                                text-decoration: underline;
                            }

                            .article-content img {
                                border-radius: 12px;
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                                margin: 1.5rem 0;
                                max-width: 100%;
                            }

                            .article-content strong {
                                color: #111827;
                                font-weight: 600;
                            }

                            .article-content ul,
                            .article-content ol {
                                margin: 1rem 0;
                                padding-left: 1.5rem;
                            }

                            .article-content li {
                                margin-bottom: 0.5rem;
                            }

                            .article-intro {
                                background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
                                color: white;
                                padding: 1.5rem;
                                border-radius: 12px;
                                margin-bottom: 2rem;
                                font-size: 1.1rem;
                                line-height: 1.8;
                            }

                            .article-intro p {
                                color: white;
                                margin: 0;
                            }

                            .section-header {
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                margin-top: 2.5rem;
                                margin-bottom: 1rem;
                            }

                            .section-number {
                                width: 36px;
                                height: 36px;
                                background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);
                                color: white;
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-weight: 700;
                                font-size: 1.1rem;
                                flex-shrink: 0;
                            }

                            .section-title {
                                font-size: 1.5rem;
                                font-weight: 700;
                                color: #1f2937;
                                margin: 0;
                            }

                            .component-grid {
                                display: grid;
                                grid-template-columns: repeat(2, 1fr);
                                gap: 1rem;
                                margin: 1.5rem 0;
                            }

                            @media (max-width: 640px) {
                                .component-grid {
                                    grid-template-columns: 1fr;
                                }
                            }

                            .component-card {
                                background: #f9fafb;
                                border: 1px solid #e5e7eb;
                                border-radius: 12px;
                                padding: 1rem;
                                display: flex;
                                align-items: flex-start;
                                gap: 0.75rem;
                            }

                            .component-icon {
                                width: 40px;
                                height: 40px;
                                background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                            }

                            .component-icon .material-symbols-outlined {
                                color: #882426;
                                font-size: 20px;
                            }

                            .component-info h4 {
                                font-weight: 600;
                                color: #1f2937;
                                margin: 0 0 0.25rem 0;
                                font-size: 0.95rem;
                            }

                            .component-info p {
                                color: #6b7280;
                                font-size: 0.85rem;
                                margin: 0;
                                line-height: 1.4;
                            }

                            .tip-box {
                                background: linear-gradient(135deg, #fef2f2 0%, #fce7f3 100%);
                                border: 1px solid #fecaca;
                                border-radius: 12px;
                                padding: 1.25rem;
                                margin: 1.5rem 0;
                            }

                            .tip-header {
                                display: flex;
                                align-items: center;
                                gap: 0.5rem;
                                margin-bottom: 0.75rem;
                            }

                            .tip-header .material-symbols-outlined {
                                color: #882426;
                                font-size: 20px;
                            }

                            .tip-header span:last-child {
                                font-weight: 600;
                                color: #882426;
                                font-size: 0.95rem;
                            }

                            .tip-box p {
                                color: #374151;
                                margin: 0;
                                font-size: 0.95rem;
                                line-height: 1.6;
                            }

                            .warning-box {
                                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                                border: 1px solid #fcd34d;
                                border-radius: 12px;
                                padding: 1.25rem;
                                margin: 1.5rem 0;
                            }

                            .warning-box .tip-header .material-symbols-outlined,
                            .warning-box .tip-header span:last-child {
                                color: #b45309;
                            }

                            .info-box {
                                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                                border: 1px solid #93c5fd;
                                border-radius: 12px;
                                padding: 1.25rem;
                                margin: 1.5rem 0;
                            }

                            .info-box .tip-header .material-symbols-outlined,
                            .info-box .tip-header span:last-child {
                                color: #1d4ed8;
                            }

                            .article-content blockquote {
                                border-left: 4px solid #882426;
                                padding-left: 1rem;
                                margin: 1.5rem 0;
                                font-style: italic;
                                color: #6b7280;
                            }

                            .article-content code {
                                background: #f3f4f6;
                                padding: 0.25rem 0.5rem;
                                border-radius: 4px;
                                font-size: 0.9rem;
                            }

                            .article-content pre {
                                background: #1f2937;
                                color: #f3f4f6;
                                padding: 1rem;
                                border-radius: 12px;
                                overflow-x: auto;
                                margin: 1.5rem 0;
                            }

                            .article-content pre code {
                                background: transparent;
                                padding: 0;
                            }

                            .feature-list {
                                list-style: none;
                                padding: 0;
                                margin: 1rem 0;
                            }

                            .feature-list li {
                                display: flex;
                                align-items: flex-start;
                                gap: 0.75rem;
                                padding: 0.75rem 0;
                                border-bottom: 1px solid #e5e7eb;
                            }

                            .feature-list li:last-child {
                                border-bottom: none;
                            }

                            .feature-list .check-icon {
                                width: 24px;
                                height: 24px;
                                background: #dcfce7;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                            }

                            .feature-list .check-icon .material-symbols-outlined {
                                color: #16a34a;
                                font-size: 16px;
                            }

                            .step-divider {
                                height: 1px;
                                background: linear-gradient(to right, transparent, #e5e7eb, transparent);
                                margin: 2rem 0;
                            }
                        </style>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-[#882426]/10 rounded-full flex items-center justify-center text-[#882426] font-bold text-lg">
                                        <?= strtoupper(substr($article['author_name'] ?? 'A', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></p>
                                        <p class="text-sm text-gray-500">Penulis</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500 mr-2">Bagikan:</span>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                                        target="_blank"
                                        class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z" />
                                        </svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($article['judul']) ?>"
                                        target="_blank"
                                        class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                        </svg>
                                    </a>
                                    <a href="https://wa.me/?text=<?= urlencode($article['judul'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                                        target="_blank"
                                        class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                        </svg>
                                    </a>
                                    <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link berhasil disalin!');"
                                        class="w-10 h-10 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($prevNextPosts['prev'] || $prevNextPosts['next']): ?>
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if ($prevNextPosts['prev']): ?>
                                <a href="articleTemplate.php?slug=<?= urlencode($prevNextPosts['prev']['slug']) ?>"
                                    class="group flex flex-col p-4 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl hover:border-[#882426]/30 hover:shadow-md transition-all duration-300">
                                    <div class="flex items-center gap-1 text-gray-500 text-xs font-medium uppercase tracking-wide mb-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Artikel Sebelumnya
                                    </div>
                                    <h4 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-[#882426] transition-colors">
                                        <?= htmlspecialchars($prevNextPosts['prev']['judul']) ?>
                                    </h4>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if ($prevNextPosts['next']): ?>
                                <a href="articleTemplate.php?slug=<?= urlencode($prevNextPosts['next']['slug']) ?>"
                                    class="group flex flex-col p-4 bg-gradient-to-l from-gray-50 to-white border border-gray-200 rounded-xl hover:border-[#882426]/30 hover:shadow-md transition-all duration-300 text-right">
                                    <div class="flex items-center justify-end gap-1 text-gray-500 text-xs font-medium uppercase tracking-wide mb-2">
                                        Artikel Selanjutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-[#882426] transition-colors">
                                        <?= htmlspecialchars($prevNextPosts['next']['judul']) ?>
                                    </h4>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6 flex items-center justify-center">
                        <a href="blogNews.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Kembali ke Blog
                        </a>
                    </div>
                </article>

                <aside class="lg:col-span-1">
                    <div class="sticky top-32 space-y-6">
                        <?php if (!empty($popularPosts)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    Artikel Populer
                                </h3>
                                <div class="space-y-4">
                                    <?php foreach ($popularPosts as $index => $popular): ?>
                                        <a href="articleTemplate.php?slug=<?= urlencode($popular['slug']) ?>" class="group block">
                                            <div class="flex gap-3">
                                                <div class="relative flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden">
                                                    <?php if ($popular['thumbnail']): ?>
                                                        <img src="../../uploads/blog/<?= htmlspecialchars($popular['thumbnail']) ?>"
                                                            alt="<?= htmlspecialchars($popular['judul']) ?>"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    <?php else: ?>
                                                        <div class="w-full h-full bg-gradient-to-br <?= getCategoryColor($popular['nama_kategori'] ?? '') ?> flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                            </svg>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="absolute top-0 left-0 bg-gray-800/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-br-lg">#<?= $index + 1 ?></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-gray-900 text-sm line-clamp-2 group-hover:text-[#882426] transition-colors"><?= htmlspecialchars($popular['judul']) ?></h4>
                                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <?php
                                                        $views = $popular['views'] ?? 0;
                                                        if ($views >= 1000) {
                                                            echo number_format($views / 1000, 1) . 'k';
                                                        } else {
                                                            echo number_format($views);
                                                        }
                                                        ?> views
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($relatedPosts)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                    Artikel Terkait
                                </h3>
                                <div class="space-y-4">
                                    <?php foreach ($relatedPosts as $related): ?>
                                        <a href="articleTemplate.php?slug=<?= urlencode($related['slug']) ?>" class="group block">
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden">
                                                    <?php if ($related['thumbnail']): ?>
                                                        <img src="../../uploads/blog/<?= htmlspecialchars($related['thumbnail']) ?>"
                                                            alt="<?= htmlspecialchars($related['judul']) ?>"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    <?php else: ?>
                                                        <div class="w-full h-full bg-gradient-to-br <?= getCategoryColor($related['nama_kategori'] ?? '') ?> flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                            </svg>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-medium text-gray-900 text-sm line-clamp-2 group-hover:text-[#882426] transition-colors"><?= htmlspecialchars($related['judul']) ?></h4>
                                                    <p class="text-xs text-gray-500 mt-1"><?= formatDateShort($related['published_at']) ?></p>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Kategori
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $allCategories = $blogCategoryRepo->getAll(true);
                                foreach ($allCategories as $cat):
                                ?>
                                    <a href="blogNews.php?category=<?= urlencode($cat['slug']) ?>"
                                        class="px-3 py-1.5 text-sm font-medium rounded-full transition-colors <?= ($cat['id_category'] ?? null) === $article['id_category'] ? 'bg-[#882426] text-white' : 'bg-gray-100 text-gray-700 hover:bg-[#882426]/10 hover:text-[#882426]' ?>">
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="bg-[#882426] rounded-2xl p-6 text-white">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Newsletter</h3>
                            <p class="text-white/80 text-sm mb-4">Dapatkan artikel terbaru langsung ke inbox Anda.</p>
                            <form class="space-y-3">
                                <input type="email" placeholder="Email Anda" class="w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 text-sm focus:outline-none focus:border-white/40 focus:ring-2 focus:ring-white/20 transition-all">
                                <button type="submit" class="w-full px-4 py-2.5 bg-white text-[#882426] font-medium rounded-xl text-sm hover:bg-gray-100 transition-all duration-300">
                                    Berlangganan
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
</body>

</html>