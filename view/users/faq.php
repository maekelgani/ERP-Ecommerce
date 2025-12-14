<?php
$pageTitle = "FAQ - Pusat Bantuan";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();

$faqFromDb = [];
$dbError = false;

try {
    require_once __DIR__ . '/../../app/Repository/SupportTicketRepository.php';
    $ticketRepo = new \App\Repository\SupportTicketRepository();
    $faqFromDb = $ticketRepo->getResolvedByCategory();
} catch (Exception $e) {
    $dbError = true;
}

$staticFaqs = [
    'general' => [
        [
            'question' => 'Apakah produk di Nano Komputer 100% baru dan original?',
            'answer' => 'Ya, seluruh produk yang kami jual adalah <strong class="text-[#882426]">100% Baru (BNIB)</strong> dan <strong class="text-[#882426]">Original</strong> bergaransi resmi distributor Indonesia. Kami tidak menjual barang bekas, refurbish, atau black market.'
        ],
        [
            'question' => 'Apakah harga di website sudah termasuk PPN?',
            'answer' => 'Harga yang tertera di website sudah final. Jika Anda memerlukan <strong>Faktur Pajak</strong> untuk pembelian perusahaan, silakan hubungi Admin kami melalui WhatsApp sebelum melakukan pembayaran.'
        ],
        [
            'question' => 'Bagaimana cara mengaktifkan akun saya?',
            'answer' => 'Setelah mendaftar, Anda akan menerima email verifikasi. Klik link yang ada di email tersebut untuk mengaktifkan akun Anda. Jika tidak menerima email, periksa folder spam atau hubungi customer service kami.'
        ]
    ],
    'order' => [
        [
            'question' => 'Berapa lama proses perakitan PC?',
            'answer' => 'Proses perakitan PC membutuhkan waktu estimasi <strong class="text-[#882426]">1-3 hari kerja</strong> tergantung antrian. Waktu ini mencakup pengecekan komponen, perakitan rapi (Cable Management), instalasi OS & Driver (Trial), dan Stress Test (Benchmarking) untuk memastikan kestabilan sistem.'
        ],
        [
            'question' => 'Apakah PC Rakitan sudah termasuk Windows & Office?',
            'answer' => 'Secara standar, kami akan menginstalkan Windows 10/11 versi <strong>Trial (Unactivated)</strong> untuk keperluan pengetesan. Jika Anda ingin Windows Original (Full License) atau Microsoft Office, Anda harus membeli lisensinya secara terpisah di kategori Software.'
        ]
    ],
    'shipping' => [
        [
            'question' => 'Apakah pengiriman PC aman ke luar kota?',
            'answer' => '<strong class="text-[#882426]">Sangat aman.</strong> Untuk pengiriman PC Rakitan ke luar kota (via JNE/Sicepat/Kargo), kami mewajibkan penggunaan <strong>Packing Kayu</strong> dan <strong>Asuransi</strong>. Di bagian dalam PC, kami juga menyisipkan <em>instapak foam</em> atau <em>bubble wrap</em> untuk menahan VGA dan heatsink agar tidak berguncang.'
        ],
        [
            'question' => 'Berapa lama estimasi pengiriman?',
            'answer' => 'Estimasi pengiriman tergantung lokasi tujuan. Untuk area Jabodetabek biasanya 1-2 hari kerja. Luar Jawa 3-7 hari kerja tergantung lokasi dan layanan ekspedisi yang dipilih.'
        ]
    ],
    'warranty' => [
        [
            'question' => 'Bagaimana prosedur klaim garansi?',
            'answer' => 'Pastikan segel garansi utuh dan tidak ada cacat fisik pada barang. Hubungi tim support kami atau bisa langsung membawa barang ke Service Center Distributor terkait (alamat ada di kartu garansi). Jika melalui kami, silakan kirim barang ke toko Nano Komputer. Biaya ongkos kirim Pulang-Pergi ditanggung sepenuhnya oleh pembeli.'
        ],
        [
            'question' => 'Berapa lama masa garansi produk?',
            'answer' => 'Masa garansi berbeda-beda tergantung jenis produk dan kebijakan distributor. Umumnya komponen seperti motherboard dan VGA mendapatkan garansi 2-3 tahun, sedangkan power supply bisa hingga 5-10 tahun tergantung brand.'
        ]
    ]
];

$categories = [
    'general' => ['name' => 'Umum & Akun', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
    'order' => ['name' => 'Pemesanan & Rakit PC', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>'],
    'shipping' => ['name' => 'Pengiriman', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'],
    'warranty' => ['name' => 'Garansi & Retur', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>']
];

$allFaqs = $staticFaqs;
foreach ($faqFromDb as $catKey => $items) {
    if (!isset($allFaqs[$catKey])) {
        $allFaqs[$catKey] = [];
    }
    foreach ($items as $item) {
        $allFaqs[$catKey][] = [
            'question' => $item['question'],
            'answer' => $item['answer'],
            'from_db' => true
        ];
    }
}

$totalFaqs = 0;
foreach ($allFaqs as $items) {
    $totalFaqs += count($items);
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'FAQ', 'url' => null]
];

include '../../components/users/head.php';
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <section class="bg-[#882426] rounded-2xl py-12 md:py-16 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 border-4 border-white rounded-full"></div>
                <div class="absolute bottom-10 right-10 w-48 h-48 border-4 border-white rounded-full"></div>
                <div class="absolute top-1/2 left-1/3 w-20 h-20 border-2 border-white rounded-full"></div>
            </div>

            <div class="relative z-10 text-center px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-5 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl md:text-4xl font-bold text-white mb-3">Pusat Bantuan</h1>
                <p class="text-white/80 text-sm md:text-base max-w-xl mx-auto mb-6">
                    Temukan jawaban atas pertanyaan umum seputar pemesanan, rakit PC, garansi, dan pengiriman.
                </p>

                <div class="max-w-xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="faq-search"
                        class="w-full pl-12 pr-5 py-3.5 bg-white text-gray-900 placeholder-gray-400 rounded-xl shadow-lg focus:outline-none focus:ring-4 focus:ring-white/30 transition-all text-sm"
                        placeholder="Cari pertanyaan (misal: garansi, rakit pc, pengiriman)...">
                </div>
            </div>
        </section>

        <div class="mb-8">
            <?php include '../../components/users/breadcrumb.php'; ?>
        </div>

        <div class="lg:hidden mb-6">
            <label for="mobile-category" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kategori</label>
            <select id="mobile-category" onchange="filterCategory(this.value)"
                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-700 focus:border-[#882426] focus:outline-none transition-colors">
                <option value="all">Semua Topik</option>
                <?php foreach ($categories as $key => $cat): ?>
                    <option value="<?= $key ?>"><?= $cat['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="hidden lg:block w-72 flex-shrink-0">
                <div class="sticky top-32 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 text-lg">Kategori Bantuan</h3>
                    <nav class="space-y-2" id="faq-nav">
                        <button onclick="filterCategory('all')"
                            class="category-btn active w-full text-left px-4 py-3 rounded-xl text-sm font-medium transition-all bg-[#882426] text-white"
                            data-category="all">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Semua Topik
                            </span>
                        </button>
                        <?php foreach ($categories as $key => $cat): ?>
                            <button onclick="filterCategory('<?= $key ?>')"
                                class="category-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all"
                                data-category="<?= $key ?>">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?= $cat['icon'] ?>
                                    </svg>
                                    <?= $cat['name'] ?>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </nav>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-2">Total Pertanyaan</p>
                            <p class="text-3xl font-bold text-[#882426]" id="faq-count"><?= $totalFaqs ?></p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="aboutContact.php#contact" class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-[#882426] text-white rounded-xl text-sm font-medium hover:bg-[#6a1c1e] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </aside>

            <div class="flex-1 space-y-4" id="faq-container">
                <?php if (empty($allFaqs) || $totalFaqs === 0): ?>
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada FAQ</h3>
                        <p class="text-gray-500 text-sm">Pertanyaan yang sering ditanyakan akan muncul di sini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($allFaqs as $catKey => $items): ?>
                        <?php foreach ($items as $index => $faq): ?>
                            <div class="faq-item" data-category="<?= $catKey ?>" data-question="<?= strtolower(htmlspecialchars($faq['question'])) ?>">
                                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                        <div class="flex items-start gap-4 flex-1">
                                            <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                            <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug"><?= htmlspecialchars($faq['question']) ?></span>
                                        </div>
                                        <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-100 group-hover:bg-[#882426] flex items-center justify-center transition-all ml-4">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-white transform transition-transform duration-300 icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div class="accordion-body">
                                        <div class="px-5 md:px-6 pb-6 pt-0">
                                            <div class="pl-14 text-gray-600 leading-relaxed">
                                                <p><?= $faq['answer'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div id="no-results" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak ditemukan</h3>
                    <p class="text-gray-500 text-sm">Pertanyaan yang Anda cari tidak ditemukan. Coba kata kunci lain atau <a href="aboutContact.php#contact" class="text-[#882426] hover:underline">hubungi kami</a>.</p>
                </div>
            </div>
        </div>

        <section class="mt-12 bg-[#882426] rounded-2xl p-8 md:p-12 text-center">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-xl md:text-2xl font-bold text-white mb-3">Masih ada pertanyaan?</h2>
                <p class="text-white/80 mb-6 text-sm md:text-base">Tim customer service kami siap membantu Anda 7 hari seminggu.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="aboutContact.php#contact" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#882426] font-medium rounded-xl hover:bg-gray-100 transition-colors text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Pesan
                    </a>
                    <a href="https://wa.me/6281298765432" target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-colors text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const body = this.nextElementSibling;
                    const icon = this.querySelector('.icon-chevron');
                    const isOpen = body.classList.contains('open');

                    document.querySelectorAll('.accordion-body.open').forEach(openBody => {
                        openBody.classList.remove('open');
                        openBody.style.maxHeight = null;
                        openBody.previousElementSibling.querySelector('.icon-chevron').classList.remove('rotate-180');
                    });

                    if (!isOpen) {
                        body.classList.add('open');
                        body.style.maxHeight = body.scrollHeight + 'px';
                        icon.classList.add('rotate-180');
                    }
                });
            });
        });

        let currentCategory = 'all';
        let searchQuery = '';

        function filterCategory(category) {
            currentCategory = category;

            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-[#882426]', 'text-white');
                btn.classList.add('text-gray-600', 'hover:bg-gray-100');
            });

            const activeBtn = document.querySelector(`.category-btn[data-category="${category}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-[#882426]', 'text-white');
                activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            }

            const mobileSelect = document.getElementById('mobile-category');
            if (mobileSelect) {
                mobileSelect.value = category;
            }

            filterFaqs();
        }

        function filterFaqs() {
            const items = document.querySelectorAll('.faq-item');
            const noResults = document.getElementById('no-results');
            let visibleCount = 0;

            items.forEach(item => {
                const itemCategory = item.dataset.category;
                const itemQuestion = item.dataset.question || '';

                const categoryMatch = currentCategory === 'all' || itemCategory === currentCategory;
                const searchMatch = searchQuery === '' || itemQuestion.includes(searchQuery.toLowerCase());

                if (categoryMatch && searchMatch) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            document.getElementById('faq-count').textContent = visibleCount;

            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        const searchInput = document.getElementById('faq-search');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchQuery = this.value.trim();
                filterFaqs();
            }, 300);
        });
    </script>

    <style>
        .accordion-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .accordion-body.open {
            max-height: 1000px;
        }
    </style>
</body>

</html>