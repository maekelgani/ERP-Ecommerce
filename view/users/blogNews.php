<?php
$pageTitle = "Blog & Berita";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Blog & Berita', 'url' => null]
];

$blogPosts = [
    [
        'id' => 1,
        'title' => 'AMD Ryzen 9000 Series: Revolusi Performa Gaming 2025',
        'excerpt' => 'AMD resmi mengumumkan prosesor Ryzen 9000 series dengan arsitektur Zen 5 terbaru. Peningkatan performa hingga 40% dibanding generasi sebelumnya.',
        'category' => 'Hardware',
        'author' => 'Admin Nano',
        'date' => '28 Nov 2025',
        'read_time' => '5 menit',
        'color' => 'from-red-500 to-orange-500'
    ],
    [
        'id' => 2,
        'title' => 'Tips Memilih VGA Card yang Tepat untuk Kebutuhan Anda',
        'excerpt' => 'Panduan lengkap memilih graphics card sesuai budget dan kebutuhan, dari gaming casual hingga professional workstation.',
        'category' => 'Tips & Tutorial',
        'author' => 'Tech Team',
        'date' => '25 Nov 2025',
        'read_time' => '8 menit',
        'color' => 'from-green-500 to-teal-500'
    ],
    [
        'id' => 3,
        'title' => 'NVIDIA RTX 50 Series Bocoran Spesifikasi Terbaru',
        'excerpt' => 'Informasi terbaru tentang RTX 5090 dan RTX 5080 yang akan hadir dengan performa ray tracing yang revolusioner.',
        'category' => 'Berita',
        'author' => 'Admin Nano',
        'date' => '22 Nov 2025',
        'read_time' => '4 menit',
        'color' => 'from-green-600 to-lime-500'
    ],
    [
        'id' => 4,
        'title' => 'Cara Merakit PC Gaming Budget 10 Juta di 2025',
        'excerpt' => 'Panduan step-by-step merakit PC gaming dengan budget terjangkau namun tetap powerful untuk gaming 1080p.',
        'category' => 'Tips & Tutorial',
        'author' => 'Build Master',
        'date' => '20 Nov 2025',
        'read_time' => '12 menit',
        'color' => 'from-blue-500 to-cyan-500'
    ],
    [
        'id' => 5,
        'title' => 'Intel Arrow Lake: Saingan Berat AMD di 2025',
        'excerpt' => 'Intel kembali dengan arsitektur hybrid baru yang menjanjikan efisiensi daya lebih baik dengan performa kompetitif.',
        'category' => 'Hardware',
        'author' => 'Tech Team',
        'date' => '18 Nov 2025',
        'read_time' => '6 menit',
        'color' => 'from-blue-600 to-indigo-600'
    ],
    [
        'id' => 6,
        'title' => 'Review Monitor Gaming 4K 144Hz Terbaik 2025',
        'excerpt' => 'Komparasi 5 monitor gaming 4K dengan refresh rate tinggi dari berbagai brand ternama untuk pengalaman gaming ultimate.',
        'category' => 'Review',
        'author' => 'Admin Nano',
        'date' => '15 Nov 2025',
        'read_time' => '10 menit',
        'color' => 'from-purple-500 to-pink-500'
    ],
    [
        'id' => 7,
        'title' => 'DDR5 vs DDR4: Apakah Sudah Waktunya Upgrade?',
        'excerpt' => 'Analisis mendalam tentang perbedaan performa DDR5 dan DDR4, serta kapan waktu yang tepat untuk melakukan upgrade.',
        'category' => 'Tips & Tutorial',
        'author' => 'Tech Team',
        'date' => '12 Nov 2025',
        'read_time' => '7 menit',
        'color' => 'from-amber-500 to-yellow-500'
    ],
    [
        'id' => 8,
        'title' => 'Promo Akhir Tahun: Diskon Hingga 50% di Nano Komputer',
        'excerpt' => 'Jangan lewatkan promo besar-besaran akhir tahun dengan diskon fantastis untuk berbagai produk komputer dan aksesoris.',
        'category' => 'Promo',
        'author' => 'Admin Nano',
        'date' => '10 Nov 2025',
        'read_time' => '3 menit',
        'color' => 'from-rose-500 to-red-600'
    ]
];

$categories = ['Semua', 'Hardware', 'Tips & Tutorial', 'Berita', 'Review', 'Promo'];
$selectedCategory = $_GET['category'] ?? 'Semua';

if ($selectedCategory !== 'Semua') {
    $blogPosts = array_filter($blogPosts, function ($post) use ($selectedCategory) {
        return $post['category'] === $selectedCategory;
    });
}
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
                            <a href="?category=<?= urlencode($category) ?>"
                                class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 <?= $selectedCategory === $category ? 'bg-[#882426] text-white' : 'text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5' ?>">
                                <?= $category ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($blogPosts)): ?>
                <?php $firstPost = array_shift($blogPosts); ?>
                <section class="mb-10">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="relative aspect-video lg:aspect-auto overflow-hidden">
                                <div class="absolute inset-0 bg-[#882426]">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-[#882426] text-xs font-semibold rounded-full"><?= $firstPost['category'] ?></span>
                                </div>
                            </div>
                            <div class="p-6 lg:p-8 flex flex-col justify-center">
                                <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?= $firstPost['date'] ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= $firstPost['read_time'] ?>
                                    </span>
                                </div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 group-hover:text-[#882426] transition-colors"><?= $firstPost['title'] ?></h2>
                                <p class="text-gray-600 mb-6"><?= $firstPost['excerpt'] ?></p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center text-[#882426] font-semibold">
                                            <?= strtoupper(substr($firstPost['author'], 0, 1)) ?>
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium"><?= $firstPost['author'] ?></span>
                                    </div>
                                    <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300">
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

                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Artikel Lainnya</h2>
                        <span class="text-sm text-gray-500"><?= count($blogPosts) ?> artikel</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($blogPosts as $post): ?>
                            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all duration-300">
                                <a href="#" class="block">
                                    <div class="relative aspect-video overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br <?= $post['color'] ?? 'from-gray-400 to-gray-500' ?> flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                            </svg>
                                        </div>
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                                        <div class="absolute top-3 left-3">
                                            <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-[#882426] text-xs font-semibold rounded-full"><?= $post['category'] ?></span>
                                        </div>
                                    </div>
                                </a>
                                <div class="p-5">
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                                        <span><?= $post['date'] ?></span>
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span><?= $post['read_time'] ?></span>
                                    </div>
                                    <a href="#">
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-[#882426] transition-colors"><?= $post['title'] ?></h3>
                                    </a>
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-4"><?= $post['excerpt'] ?></p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-[#882426]/10 rounded-full flex items-center justify-center text-[#882426] text-xs font-semibold">
                                                <?= strtoupper(substr($post['author'], 0, 1)) ?>
                                            </div>
                                            <span class="text-xs text-gray-600"><?= $post['author'] ?></span>
                                        </div>
                                        <a href="#" class="text-[#882426] text-sm font-medium hover:underline">Baca</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="mt-10 flex justify-center">
                    <nav class="inline-flex items-center gap-1 bg-white rounded-xl shadow-sm border border-gray-100 p-1">
                        <button class="p-2 text-gray-400 hover:text-[#882426] rounded-lg transition-colors" disabled>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="w-10 h-10 text-sm font-medium text-white bg-[#882426] rounded-lg">1</button>
                        <button class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors">2</button>
                        <button class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors">3</button>
                        <span class="px-2 text-gray-400">...</span>
                        <button class="w-10 h-10 text-sm font-medium text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors">10</button>
                        <button class="p-2 text-gray-600 hover:text-[#882426] rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </nav>
                </div>
            <?php else: ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                    <p class="text-gray-500 mb-6">Tidak ada artikel dalam kategori ini. Coba pilih kategori lain.</p>
                    <a href="?category=Semua" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300">
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