<?php
$pageTitle = "FAQ - Pusat Bantuan";
require_once __DIR__ . '/../../config/config.php';
include '../../components/users/head.php';
?>

<body class="bg-gray-50 min-h-screen font-sans antialiased">
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="w-full">
        <section class="bg-[#882426] py-16 md:py-24 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 border-4 border-white rounded-full"></div>
                <div class="absolute bottom-10 right-10 w-48 h-48 border-4 border-white rounded-full"></div>
                <div class="absolute top-1/2 left-1/3 w-20 h-20 border-2 border-white rounded-full"></div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Pusat Bantuan</h1>
                <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto mb-8">
                    Temukan jawaban atas pertanyaan umum seputar pemesanan, rakit PC, garansi, dan pengiriman.
                </p>

                <div class="max-w-2xl mx-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="faq-search"
                        class="w-full pl-12 pr-5 py-4 bg-white text-gray-900 placeholder-gray-400 rounded-xl shadow-lg focus:outline-none focus:ring-4 focus:ring-white/30 transition-all text-base"
                        placeholder="Cari pertanyaan (misal: garansi, rakit pc, pengiriman)...">
                </div>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:hidden mb-8">
                <label for="mobile-category" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kategori</label>
                <select id="mobile-category" onchange="filterCategory(this.value)"
                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-700 focus:border-[#882426] focus:outline-none transition-colors">
                    <option value="all">Semua Topik</option>
                    <option value="general">Umum & Akun</option>
                    <option value="order">Pemesanan & Rakit PC</option>
                    <option value="shipping">Pengiriman</option>
                    <option value="warranty">Garansi & Retur</option>
                </select>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <aside class="hidden lg:block w-72 flex-shrink-0">
                    <div class="sticky top-24 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
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
                            <button onclick="filterCategory('general')"
                                class="category-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all"
                                data-category="general">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Umum & Akun
                                </span>
                            </button>
                            <button onclick="filterCategory('order')"
                                class="category-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all"
                                data-category="order">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                    Pemesanan & Rakit PC
                                </span>
                            </button>
                            <button onclick="filterCategory('shipping')"
                                class="category-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all"
                                data-category="shipping">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    Pengiriman
                                </span>
                            </button>
                            <button onclick="filterCategory('warranty')"
                                class="category-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all"
                                data-category="warranty">
                                <span class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Garansi & Retur
                                </span>
                            </button>
                        </nav>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-2">Total Pertanyaan</p>
                                <p class="text-3xl font-bold text-[#882426]" id="faq-count">7</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="flex-1 space-y-4" id="faq-container">
                    <div class="faq-item" data-category="general">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Apakah produk di Nano Komputer 100% baru dan original?</span>
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
                                        <p>Ya, seluruh produk yang kami jual adalah <strong class="text-[#882426]">100% Baru (BNIB)</strong> dan <strong class="text-[#882426]">Original</strong> bergaransi resmi distributor Indonesia. Kami tidak menjual barang bekas, refurbish, atau black market.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="general">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Apakah harga di website sudah termasuk PPN?</span>
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
                                        <p>Harga yang tertera di website sudah final. Jika Anda memerlukan <strong>Faktur Pajak</strong> untuk pembelian perusahaan, silakan hubungi Admin kami melalui WhatsApp sebelum melakukan pembayaran.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="order">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Berapa lama proses perakitan PC?</span>
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
                                        <p>Proses perakitan PC membutuhkan waktu estimasi <strong class="text-[#882426]">1-3 hari kerja</strong> tergantung antrian. Waktu ini mencakup:</p>
                                        <ul class="mt-3 space-y-2">
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Pengecekan komponen
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Perakitan rapi (Cable Management)
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Instalasi OS & Driver (Trial)
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Stress Test (Benchmarking) untuk memastikan kestabilan sistem
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="order">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Apakah PC Rakitan sudah termasuk Windows & Office?</span>
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
                                        <p>Secara standar, kami akan menginstalkan Windows 10/11 versi <strong>Trial (Unactivated)</strong> untuk keperluan pengetesan. Jika Anda ingin Windows Original (Full License) atau Microsoft Office, Anda harus membeli lisensinya secara terpisah di kategori Software.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="shipping">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Apakah pengiriman PC aman ke luar kota?</span>
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
                                        <p><strong class="text-[#882426]">Sangat aman.</strong> Untuk pengiriman PC Rakitan ke luar kota (via JNE/Sicepat/Kargo), kami mewajibkan penggunaan <strong>Packing Kayu</strong> dan <strong>Asuransi</strong>. Di bagian dalam PC, kami juga menyisipkan <em>instapak foam</em> atau <em>bubble wrap</em> untuk menahan VGA dan heatsink agar tidak berguncang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="warranty">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Bagaimana prosedur klaim garansi?</span>
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
                                        <ol class="space-y-3">
                                            <li class="flex items-start gap-3">
                                                <span class="flex-shrink-0 w-6 h-6 bg-[#882426] text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                                <span>Pastikan segel garansi utuh dan tidak ada cacat fisik pada barang.</span>
                                            </li>
                                            <li class="flex items-start gap-3">
                                                <span class="flex-shrink-0 w-6 h-6 bg-[#882426] text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                                <span>Hubungi tim support kami atau bisa langsung membawa barang ke Service Center Distributor terkait (alamat ada di kartu garansi).</span>
                                            </li>
                                            <li class="flex items-start gap-3">
                                                <span class="flex-shrink-0 w-6 h-6 bg-[#882426] text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                                <span>Jika melalui kami, silakan kirim barang ke toko Nano Komputer. Biaya ongkos kirim Pulang-Pergi ditanggung sepenuhnya oleh pembeli.</span>
                                            </li>
                                        </ol>
                                        <div class="mt-4 p-4 bg-red-50 border border-red-100 rounded-xl">
                                            <p class="text-sm text-red-700 font-medium flex items-start gap-2">
                                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Wajib menyertakan Video Unboxing untuk klaim kerusakan fisik saat barang baru diterima.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item" data-category="general">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <button class="accordion-header w-full flex items-center justify-between p-5 md:p-6 text-left group">
                                <div class="flex items-start gap-4 flex-1">
                                    <span class="flex-shrink-0 w-10 h-10 bg-[#882426]/10 text-[#882426] rounded-xl flex items-center justify-center font-bold text-sm">Q</span>
                                    <span class="font-semibold text-gray-800 group-hover:text-[#882426] transition-colors text-base md:text-lg leading-snug">Metode pembayaran apa saja yang tersedia?</span>
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
                                        <p>Kami menerima berbagai metode pembayaran:</p>
                                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-100">
                                                <span class="text-sm font-semibold text-blue-600">BCA</span>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-100">
                                                <span class="text-sm font-semibold text-yellow-600">Mandiri</span>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-100">
                                                <span class="text-sm font-semibold text-green-600">GoPay</span>
                                            </div>
                                            <div class="p-3 bg-gray-50 rounded-xl text-center border border-gray-100">
                                                <span class="text-sm font-semibold text-purple-600">OVO</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="no-results" class="hidden text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada hasil ditemukan</h3>
                        <p class="text-gray-500">Coba gunakan kata kunci lain atau pilih kategori berbeda</p>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-16 bg-[#882426]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">Masih punya pertanyaan?</h3>
                <p class="text-white/80 mb-8 max-w-lg mx-auto">Tim customer service kami siap membantu Anda 24/7. Jangan ragu untuk menghubungi kami!</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="aboutContact.php#contact" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-[#882426] font-semibold rounded-xl hover:bg-gray-100 transition-all shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Hubungi Kami
                    </a>
                    <a href="https://wa.me/6281298765432" target="_blank" class="inline-flex items-center gap-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-all shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        Chat WhatsApp
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <style>
        .accordion-body {
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.25s ease-out;
        }

        .accordion-header .icon-chevron {
            transition: transform 0.3s ease-out;
        }

        .accordion-header.is-open .icon-chevron {
            transform: rotate(180deg);
        }

        .accordion-header.is-open {
            background-color: #f9fafb;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .faq-item.fade-in {
            animation: fadeInUp 0.3s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                const faqContainer = document.getElementById('faq-container');
                const faqItems = document.querySelectorAll('.faq-item');
                const searchInput = document.getElementById('faq-search');
                const noResults = document.getElementById('no-results');
                const faqCount = document.getElementById('faq-count');
                const mobileSelect = document.getElementById('mobile-category');
                const desktopButtons = document.querySelectorAll('.category-btn');

                function initAccordions() {
                    document.querySelectorAll('.accordion-body').forEach(body => {
                        body.style.maxHeight = '0px';
                        body.style.opacity = '0';
                    });
                }

                function closeAccordion(header) {
                    if (!header) return;
                    const body = header.nextElementSibling;
                    header.classList.remove('is-open');
                    if (body && body.classList.contains('accordion-body')) {
                        body.style.maxHeight = '0px';
                        body.style.opacity = '0';
                    }
                }

                function openAccordion(header) {
                    if (!header) return;
                    const body = header.nextElementSibling;
                    if (body && body.classList.contains('accordion-body')) {
                        header.classList.add('is-open');
                        const scrollH = body.scrollHeight;
                        body.style.maxHeight = scrollH + 'px';
                        body.style.opacity = '1';
                    }
                }

                function closeAllAccordions() {
                    document.querySelectorAll('.accordion-header').forEach(h => closeAccordion(h));
                }

                function syncCategoryUI(category) {
                    desktopButtons.forEach(btn => {
                        const cat = btn.getAttribute('data-category');
                        if (cat === category) {
                            btn.classList.add('bg-[#882426]', 'text-white');
                            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
                        } else {
                            btn.classList.remove('bg-[#882426]', 'text-white');
                            btn.classList.add('text-gray-600', 'hover:bg-gray-100');
                        }
                    });
                    if (mobileSelect) mobileSelect.value = category;
                }

                function updateCount(count) {
                    if (faqCount) faqCount.textContent = count;
                    if (noResults) noResults.classList.toggle('hidden', count > 0);
                }

                initAccordions();

                document.querySelectorAll('.accordion-header').forEach(acc => {
                    acc.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const wasOpen = this.classList.contains('is-open');
                        closeAllAccordions();
                        if (!wasOpen) {
                            openAccordion(this);
                        }
                    });
                });

                window.filterCategory = function(category) {
                    let count = 0;
                    closeAllAccordions();
                    syncCategoryUI(category);

                    faqItems.forEach(item => {
                        const matches = category === 'all' || item.getAttribute('data-category') === category;
                        if (matches) {
                            item.style.display = 'block';
                            item.classList.remove('fade-in');
                            void item.offsetWidth;
                            item.classList.add('fade-in');
                            count++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    updateCount(count);
                };

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const term = this.value.toLowerCase().trim();
                        let count = 0;
                        closeAllAccordions();

                        faqItems.forEach(item => {
                            const qText = (item.querySelector('.accordion-header')?.innerText || '').toLowerCase();
                            const aText = (item.querySelector('.accordion-body')?.innerText || '').toLowerCase();
                            const matches = qText.includes(term) || aText.includes(term);

                            if (matches) {
                                item.style.display = 'block';
                                count++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        if (term.length > 0) {
                            desktopButtons.forEach(btn => {
                                btn.classList.remove('bg-[#882426]', 'text-white');
                                btn.classList.add('text-gray-600', 'hover:bg-gray-100');
                            });
                        } else {
                            syncCategoryUI('all');
                        }

                        updateCount(count);
                    });
                }

                syncCategoryUI('all');
            });
        })();
    </script>
</body>

</html>