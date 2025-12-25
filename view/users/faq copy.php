<?php
$pageTitle = "FAQ - Pusat Bantuan";
require_once __DIR__ . '/../../config/config.php';
include '../../components/users/head.php';
?>

<body class="w-full bg-gray-50/50 h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden font-sans antialiased text-gray-800">
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="w-full md:px-8 lg:px-20 py-10 mb-20">

        <div class="text-center mb-12 bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100 relative overflow-hidden ">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-primary/50 to-primary"></div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Butuh Bantuan?</h1>
            <p class="text-gray-500 max-w-xl mx-auto mb-8">
                Temukan jawaban atas pertanyaan umum seputar pemesanan, rakit PC, garansi, dan pengiriman di Nano Komputer.
            </p>

            <div class="max-w-2xl mx-auto relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="faq-search" class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-transparent text-gray-900 placeholder-gray-400 focus:bg-white focus:border-primary focus:ring-primary sm:text-sm rounded-xl shadow-inner transition-all duration-300" placeholder="Cari pertanyaan (misal: garansi, rakit pc, pengiriman)...">
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">

            <aside class="hidden lg:block w-1/4 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-4 px-2">Kategori Bantuan</h3>
                <nav class="space-y-1" id="faq-nav">
                    <button onclick="filterCategory('all')" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium transition-colors bg-primary/10 text-primary hover:bg-primary/20 category-btn active" data-category="all">
                        Semua Topik
                    </button>
                    <button onclick="filterCategory('general')" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors category-btn" data-category="general">
                        Umum & Akun
                    </button>
                    <button onclick="filterCategory('order')" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors category-btn" data-category="order">
                        Pemesanan & Rakit PC
                    </button>
                    <button onclick="filterCategory('shipping')" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors category-btn" data-category="shipping">
                        Pengiriman
                    </button>
                    <button onclick="filterCategory('warranty')" class="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors category-btn" data-category="warranty">
                        Garansi & Retur
                    </button>
                </nav>
            </aside>

            <div class="w-full lg:w-3/4 space-y-4" id="faq-container">

                <div class="faq-item" data-category="general">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Apakah produk di Nano Komputer 100% baru dan original?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Ya, seluruh produk yang kami jual adalah 100% Baru (BNIB) dan Original bergaransi resmi distributor Indonesia. Kami tidak menjual barang bekas, refurbish, atau black market.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="general">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Apakah harga di website sudah termasuk PPN?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Harga yang tertera di website sudah final. Jika Anda memerlukan Faktur Pajak untuk pembelian perusahaan, silakan hubungi Admin kami melalui WhatsApp sebelum melakukan pembayaran.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="order">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Berapa lama proses perakitan PC?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Proses perakitan PC membutuhkan waktu estimasi <strong>1-3 hari kerja</strong> tergantung antrian. Waktu ini mencakup:
                            <ul class="list-disc pl-5 mt-2 space-y-1">
                                <li>Pengecekan komponen.</li>
                                <li>Perakitan rapi (Cable Management).</li>
                                <li>Instalasi OS & Driver (Trial).</li>
                                <li>Stress Test (Benchmarking) untuk memastikan kestabilan sistem.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="order">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Apakah PC Rakitan sudah termasuk Windows & Office?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Secara standar, kami akan menginstalkan Windows 10/11 versi <strong>Trial (Unactivated)</strong> untuk keperluan pengetesan. Jika Anda ingin Windows Original (Full License) atau Microsoft Office, Anda harus membeli lisensinya secara terpisah di kategori Software.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="shipping">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Apakah pengiriman PC aman ke luar kota?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Sangat aman. Untuk pengiriman PC Rakitan ke luar kota (via JNE/Sicepat/Kargo), kami mewajibkan penggunaan <strong>Packing Kayu</strong> dan <strong>Asuransi</strong>. Di bagian dalam PC, kami juga menyisipkan <em>instapak foam</em> atau <em>bubble wrap</em> untuk menahan VGA dan heatsink agar tidak berguncang.
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="warranty">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Bagaimana prosedur klaim garansi?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            <ol class="list-decimal pl-5 space-y-1">
                                <li>Pastikan segel garansi utuh dan tidak ada cacat fisik pada barang.</li>
                                <li>Hubungi tim support kami atau bisa langsung membawa barang ke Service Center Distributor terkait (alamat ada di kartu garansi).</li>
                                <li>Jika melalui kami, silakan kirim barang ke toko Nano Komputer. Biaya ongkos kirim Pulang-Pergi ditanggung sepenuhnya oleh pembeli.</li>
                            </ol>
                            <p class="mt-2 text-xs text-red-500 font-semibold">*Wajib menyertakan Video Unboxing untuk klaim kerusakan fisik saat barang baru diterima.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-category="general">
                    <button class="accordion-header w-full flex items-center justify-between p-6 bg-white rounded-xl border border-gray-100 hover:shadow-md transition-all duration-300 group text-left">
                        <span class="font-semibold text-gray-800 group-hover:text-primary transition-colors">Metode pembayaran apa saja yang tersedia?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300 group-hover:text-primary icon-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="accordion-body max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white px-6 rounded-b-xl border-x border-b border-gray-100 border-t-0 opacity-0">
                        <div class="pb-6 pt-2 text-gray-600 text-sm leading-relaxed">
                            Kami menerima pembayaran melalui Transfer Bank (BCA, Mandiri), Virtual Account, dan E-Wallet (GoPay, OVO, ShopeePay) melalui payment gateway kami.
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <section class="mt-12">
            <div class="bg-[#882426] rounded-2xl p-6 md:p-10 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-3">Masih punya pertanyaan?</h3>
                <p class="text-white/80 mb-6 max-w-lg mx-auto">Tim customer service kami siap membantu Anda 24/7. Jangan ragu untuk menghubungi kami!</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="aboutContact.php#contact" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-[#882426] font-medium rounded-xl hover:bg-gray-100 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Hubungi Kami
                    </a>
                    <a href="https://wa.me/6281298765432" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        Chat WhatsApp
                    </a>
                </div>
            </div>
        </section>

    </main>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Accordion Logic
            const accordions = document.querySelectorAll('.accordion-header');

            accordions.forEach(acc => {
                acc.addEventListener('click', function() {
                    // Close other accordions
                    accordions.forEach(otherAcc => {
                        if (otherAcc !== acc && otherAcc.classList.contains('active')) {
                            otherAcc.classList.remove('active');
                            const otherBody = otherAcc.nextElementSibling;
                            const otherIcon = otherAcc.querySelector('.icon-chevron');

                            otherBody.style.maxHeight = '0';
                            otherBody.classList.remove('opacity-100', 'py-4');
                            otherBody.classList.add('opacity-0');
                            otherIcon.style.transform = 'rotate(0deg)';

                            // Visual styling reset
                            otherAcc.classList.remove('bg-gray-50', 'rounded-b-none');
                            otherAcc.classList.add('rounded-xl');
                        }
                    });

                    // Toggle current
                    this.classList.toggle('active');
                    const body = this.nextElementSibling;
                    const icon = this.querySelector('.icon-chevron');

                    if (this.classList.contains('active')) {
                        body.style.maxHeight = body.scrollHeight + "px";
                        body.classList.remove('opacity-0');
                        body.classList.add('opacity-100');
                        icon.style.transform = 'rotate(180deg)';

                        // Visual styling active
                        this.classList.add('bg-gray-50', 'rounded-b-none');
                        this.classList.remove('rounded-xl');
                    } else {
                        body.style.maxHeight = '0';
                        body.classList.remove('opacity-100');
                        body.classList.add('opacity-0');
                        icon.style.transform = 'rotate(0deg)';

                        // Visual styling reset
                        this.classList.remove('bg-gray-50', 'rounded-b-none');
                        this.classList.add('rounded-xl');
                    }
                });
            });

            // 2. Category Filter Logic
            window.filterCategory = function(category) {
                const items = document.querySelectorAll('.faq-item');
                const buttons = document.querySelectorAll('.category-btn');

                // Update active button state
                buttons.forEach(btn => {
                    if (btn.getAttribute('data-category') === category) {
                        btn.classList.add('bg-primary/10', 'text-primary');
                        btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
                    } else {
                        btn.classList.remove('bg-primary/10', 'text-primary');
                        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
                    }
                });

                // Filter items
                items.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'block';
                        // Add fade in animation
                        item.animate([{
                                opacity: 0,
                                transform: 'translateY(10px)'
                            },
                            {
                                opacity: 1,
                                transform: 'translateY(0)'
                            }
                        ], {
                            duration: 300,
                            easing: 'ease-out'
                        });
                    } else {
                        item.style.display = 'none';
                    }
                });
            };

            // 3. Search Logic
            const searchInput = document.getElementById('faq-search');
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const items = document.querySelectorAll('.faq-item');
                let hasResult = false;

                items.forEach(item => {
                    const question = item.querySelector('.accordion-header span').innerText.toLowerCase();
                    const answer = item.querySelector('.accordion-body').innerText.toLowerCase();

                    if (question.includes(term) || answer.includes(term)) {
                        item.style.display = 'block';
                        hasResult = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Reset category buttons visual if searching
                if (term.length > 0) {
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('bg-primary/10', 'text-primary');
                        btn.classList.add('text-gray-600');
                    });
                }
            });
        });
    </script>

    <style>
        /* Smooth height transition helper */
        .accordion-body {
            transition-property: max-height, opacity, padding;
        }
    </style>
</body>