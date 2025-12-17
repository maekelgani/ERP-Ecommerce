<?php
$pageTitle = "Syarat & Ketentuan";
require_once __DIR__ . '/../../config/config.php';
include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Syarat & Ketentuan', 'url' => null]
];
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
        <section class="bg-[#882426] py-12 md:py-16 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 right-20 w-40 h-40 border-4 border-white rounded-full"></div>
                <div class="absolute bottom-10 left-20 w-32 h-32 border-4 border-white rounded-full"></div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Syarat & Ketentuan</h1>
                <p class="text-white/80 text-lg max-w-2xl mx-auto">
                    Harap membaca syarat dan ketentuan ini dengan saksama sebelum melakukan transaksi di Nano Komputer.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-sm text-white/70">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Terakhir diperbarui: 1 Desember 2025
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Waktu baca: 8 menit
                    </span>
                </div>
            </div>
        </section>

        <div class="mt-10 w-full px-5 md:px-8 lg:px-20">
            <?php include '../../components/users/breadcrumb.php'; ?>
            <div class="lg:hidden mb-8">
                <label for="mobile-nav" class="block text-sm font-semibold text-gray-700 mb-2">Navigasi Cepat</label>
                <select id="mobile-nav" onchange="scrollToSection(this.value)" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-700 focus:border-[#882426] focus:outline-none transition-colors">
                    <option value="pendahuluan">1. Pendahuluan</option>
                    <option value="pemesanan">2. Pemesanan & Pembayaran</option>
                    <option value="pengiriman">3. Pengiriman & Rakit PC</option>
                    <option value="garansi">4. Garansi & Pengembalian</option>
                    <option value="privasi">5. Kebijakan Privasi</option>
                </select>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <aside class="hidden lg:block w-72 flex-shrink-0">
                    <div class="sticky top-24 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Daftar Isi</h3>
                        <nav class="space-y-1" id="terms-nav">
                            <a href="#pendahuluan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all bg-[#882426] text-white">
                                <span class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                                Pendahuluan
                            </a>
                            <a href="#pemesanan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">2</span>
                                Pemesanan & Pembayaran
                            </a>
                            <a href="#pengiriman" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">3</span>
                                Pengiriman & Rakit PC
                            </a>
                            <a href="#garansi" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">4</span>
                                Garansi & Pengembalian
                            </a>
                            <a href="#privasi" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">5</span>
                                Kebijakan Privasi
                            </a>
                        </nav>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Halaman
                            </button>
                        </div>
                    </div>
                </aside>

                <div class="flex-1 space-y-6">
                    <section id="pendahuluan" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-blue-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">1. Pendahuluan</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="prose text-gray-600 leading-relaxed space-y-4">
                                <p>Selamat datang di <strong class="text-[#882426]">Nano Komputer</strong>. Syarat & ketentuan berikut menjelaskan peraturan dan ketentuan penggunaan Website Nano Komputer.</p>
                                <p>Dengan menggunakan layanan kami, Anda dianggap telah <strong>menyetujui seluruh ketentuan</strong> yang berlaku di halaman ini.</p>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-4">
                                    <p class="text-sm text-blue-800 flex items-start gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kami berhak untuk mengubah, memodifikasi, menambah, atau menghapus bagian dari syarat dan ketentuan ini kapan saja tanpa pemberitahuan sebelumnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="pemesanan" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-green-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">2. Pemesanan & Pembayaran</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Ketersediaan Stok</h4>
                                        <p class="text-sm text-gray-600">Stok produk di website bersifat dinamis. Meskipun kami berusaha memperbarui stok secara <em>real-time</em>, konfirmasi ketersediaan barang sangat disarankan sebelum melakukan pembayaran.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Perubahan Harga</h4>
                                        <p class="text-sm text-gray-600">Harga produk dapat berubah sewaktu-waktu mengikuti nilai tukar mata uang asing dan kebijakan distributor resmi tanpa pemberitahuan sebelumnya.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Pembayaran</h4>
                                        <p class="text-sm text-gray-600">Kami menerima pembayaran melalui Transfer Bank, E-Wallet, dan Kartu Kredit. Pesanan akan diproses setelah pembayaran terverifikasi oleh sistem kami (maksimal 1x24 jam).</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
                                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-amber-800 mb-1">Pembatalan Otomatis</h4>
                                        <p class="text-sm text-amber-700">Pesanan yang belum dibayar dalam waktu <strong>24 jam</strong> akan otomatis dibatalkan oleh sistem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="pengiriman" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-amber-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">3. Pengiriman & Jasa Rakit</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-5 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="font-bold text-amber-800 mb-1">Penting: Asuransi Pengiriman</p>
                                        <p class="text-sm text-amber-700">Untuk pembelian produk bernilai tinggi (VGA, Monitor, CPU, Laptop), pembeli <strong>WAJIB</strong> menggunakan asuransi pengiriman. Segala kehilangan atau kerusakan saat pengiriman tanpa asuransi adalah tanggung jawab ekspedisi & pembeli.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4">
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Waktu Proses</h4>
                                        <p class="text-sm text-gray-600">Pesanan komponen lepas (loose parts) akan dikirim <strong>H+1</strong> setelah pembayaran terverifikasi.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Pemesanan Rakit PC</h4>
                                        <p class="text-sm text-gray-600">Untuk pemesanan Full PC Build (Rakit), proses perakitan, instalasi, dan stress test membutuhkan waktu <strong>2-3 hari kerja</strong> untuk memastikan PC berjalan stabil.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Packing Kayu</h4>
                                        <p class="text-sm text-gray-600">Pengiriman PC Rakitan ke luar kota Jakarta <strong>WAJIB</strong> menggunakan Packing Kayu untuk keamanan ekstra. Biaya packing kayu akan ditambahkan pada total ongkos kirim.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="garansi" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">4. Garansi & Pengembalian (RMA)</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-bold text-red-700 mb-2 uppercase tracking-wide">Wajib Video Unboxing</p>
                                        <p class="text-sm text-red-700">Komplain kekurangan barang, cacat fisik, atau barang tidak sesuai <strong>TIDAK AKAN DITERIMA</strong> tanpa menyertakan video unboxing utuh (tanpa cut/edit) yang memperlihatkan label pengiriman hingga barang dibuka & dites fisik.</p>
                                    </div>
                                </div>
                            </div>

                            <h4 class="font-bold text-gray-900 mb-4">Ketentuan Garansi Komponen:</h4>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">Barang yang kami jual bergaransi resmi distributor Indonesia (kecuali tertulis "Garansi Toko").</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">Untuk klaim garansi (RMA), pembeli dapat menyerahkan barang ke toko kami atau langsung ke Service Center distributor terkait.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600"><strong>Biaya ongkir PP ditanggung pembeli.</strong></span>
                                </li>
                            </ul>

                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Garansi Batal (Void) Jika:
                                </h4>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cacat fisik (patah, bengkok, korosi, terbakar)
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Segel garansi rusak/hilang
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Kesalahan penggunaan (Human Error)
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Modifikasi BIOS yang gagal
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="privasi" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-purple-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">5. Kebijakan Privasi</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="prose text-gray-600 leading-relaxed space-y-4">
                                <p><strong class="text-[#882426]">Nano Komputer</strong> menghargai privasi Anda. Informasi pribadi yang Anda berikan (Nama, Alamat, No. Telepon) hanya digunakan untuk keperluan pemrosesan pesanan dan pengiriman.</p>
                                <p>Kami <strong>tidak akan</strong> menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga manapun tanpa persetujuan Anda, kecuali jika diwajibkan oleh hukum atau untuk keperluan logistik (Ekspedisi).</p>

                                <div class="bg-purple-50 border border-purple-100 rounded-xl p-5 mt-6">
                                    <h4 class="font-bold text-purple-800 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Data Anda Aman
                                    </h4>
                                    <ul class="space-y-2 text-sm text-purple-700">
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Enkripsi SSL pada seluruh transaksi
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Data tidak dibagikan ke pihak ketiga
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Penyimpanan data sesuai standar keamanan
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="bg-[#882426] rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="font-bold text-white text-xl mb-2">Masih ada pertanyaan?</h3>
                            <p class="text-white/80">Tim Customer Service kami siap membantu Anda 24/7.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="faq.php" class="px-6 py-3 bg-white/10 text-white font-medium rounded-xl hover:bg-white/20 transition-colors border border-white/20 text-center">
                                Lihat FAQ
                            </a>
                            <a href="aboutContact.php#contact" class="px-6 py-3 bg-white text-[#882426] font-semibold rounded-xl hover:bg-gray-100 transition-colors shadow-lg text-center">
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <script>
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('#terms-nav a');
            const mobileNav = document.getElementById('mobile-nav');

            function updateActiveNav() {
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (scrollY >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    const href = link.getAttribute('href').substring(1);
                    const numSpan = link.querySelector('span');

                    if (href === current) {
                        link.classList.add('bg-[#882426]', 'text-white');
                        link.classList.remove('text-gray-600', 'hover:bg-gray-100');
                        if (numSpan) {
                            numSpan.classList.add('bg-white/20');
                            numSpan.classList.remove('bg-gray-200');
                        }
                    } else {
                        link.classList.remove('bg-[#882426]', 'text-white');
                        link.classList.add('text-gray-600', 'hover:bg-gray-100');
                        if (numSpan) {
                            numSpan.classList.remove('bg-white/20');
                            numSpan.classList.add('bg-gray-200');
                        }
                    }
                });

                if (mobileNav && current) {
                    mobileNav.value = current;
                }
            }

            window.addEventListener('scroll', updateActiveNav);
            updateActiveNav();
        });
    </script>

    <style>
        @media print {

            header,
            footer,
            aside,
            .no-print {
                display: none !important;
            }

            main {
                padding: 0 !important;
            }

            section {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</body>

</html>