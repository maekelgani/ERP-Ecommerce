<?php
$pageTitle = "Kebijakan Privasi";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Kebijakan Privasi', 'url' => null]
];

$lastUpdated = "1 Desember 2025";
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
            <div class="absolute inset-0 bg-gradient-to-r from-[#882426] via-[#a83234] to-[#882426]">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <defs>
                            <pattern id="shield" width="20" height="20" patternUnits="userSpaceOnUse">
                                <path d="M10 2 L18 6 L18 12 C18 16 14 19 10 20 C6 19 2 16 2 12 L2 6 Z" fill="none" stroke="white" stroke-width="0.3" />
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#shield)" />
                    </svg>
                </div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
            </div>
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-3">Kebijakan Privasi</h1>
                <p class="text-white/80 text-sm md:text-lg max-w-2xl">Komitmen kami dalam melindungi privasi dan data pribadi Anda</p>
            </div>
        </section>

        <div class="w-full px-4 md:px-8 lg:px-20 py-6">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-sm text-gray-500">Terakhir diperbarui: <?= $lastUpdated ?></span>
                            <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak
                            </button>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 prose prose-gray max-w-none">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-8">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <p class="text-sm text-green-700 m-0">Nano Komputer berkomitmen untuk melindungi privasi Anda. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda.</p>
                            </div>
                        </div>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">1</span>
                                Informasi yang Kami Kumpulkan
                            </h2>
                            <p class="text-gray-600 mb-4">Kami mengumpulkan beberapa jenis informasi untuk memberikan layanan terbaik:</p>

                            <div class="space-y-4">
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Informasi yang Anda Berikan</h3>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600 text-sm">
                                        <li>Nama lengkap dan alamat email</li>
                                        <li>Nomor telepon dan alamat pengiriman</li>
                                        <li>Informasi pembayaran (dienkripsi)</li>
                                        <li>Riwayat pesanan dan preferensi belanja</li>
                                    </ul>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Informasi yang Dikumpulkan Otomatis</h3>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600 text-sm">
                                        <li>Alamat IP dan jenis browser</li>
                                        <li>Perangkat yang digunakan</li>
                                        <li>Halaman yang dikunjungi dan waktu kunjungan</li>
                                        <li>Cookies dan teknologi pelacakan serupa</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">2</span>
                                Penggunaan Informasi
                            </h2>
                            <p class="text-gray-600 mb-4">Informasi yang kami kumpulkan digunakan untuk:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Memproses dan mengirimkan pesanan Anda</li>
                                <li>Berkomunikasi tentang pesanan, produk, dan layanan</li>
                                <li>Menyediakan dukungan pelanggan</li>
                                <li>Mengirim promosi dan penawaran khusus (dengan persetujuan)</li>
                                <li>Meningkatkan website dan pengalaman berbelanja</li>
                                <li>Mencegah penipuan dan menjaga keamanan</li>
                                <li>Mematuhi kewajiban hukum</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">3</span>
                                Berbagi Informasi
                            </h2>
                            <p class="text-gray-600 mb-4">Kami tidak menjual informasi pribadi Anda. Kami hanya berbagi informasi dengan:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li><strong>Penyedia layanan:</strong> Kurir pengiriman, payment gateway, penyedia hosting</li>
                                <li><strong>Mitra bisnis:</strong> Brand dan distributor untuk keperluan garansi</li>
                                <li><strong>Otoritas hukum:</strong> Jika diwajibkan oleh hukum yang berlaku</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">4</span>
                                Keamanan Data
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Enkripsi SSL</h4>
                                    <p class="text-sm text-gray-600">Semua data dienkripsi saat transit</p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Server Aman</h4>
                                    <p class="text-sm text-gray-600">Data disimpan di server terproteksi</p>
                                </div>
                            </div>
                            <p class="text-gray-600">Meskipun kami berusaha keras melindungi data Anda, tidak ada metode transmisi internet yang 100% aman. Kami terus meningkatkan langkah-langkah keamanan.</p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">5</span>
                                Cookies
                            </h2>
                            <p class="text-gray-600 mb-4">Kami menggunakan cookies untuk:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Mengingat preferensi dan pengaturan Anda</li>
                                <li>Menjaga sesi login tetap aktif</li>
                                <li>Menganalisis lalu lintas website</li>
                                <li>Menampilkan iklan yang relevan</li>
                            </ul>
                            <p class="text-gray-600 mt-4">Anda dapat mengatur browser untuk menolak cookies, namun beberapa fitur website mungkin tidak berfungsi optimal.</p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">6</span>
                                Hak Anda
                            </h2>
                            <p class="text-gray-600 mb-4">Anda memiliki hak untuk:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-600">Mengakses data pribadi Anda</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-600">Memperbaiki data yang tidak akurat</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-600">Meminta penghapusan data</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-600">Berhenti berlangganan newsletter</span>
                                </div>
                            </div>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">7</span>
                                Penyimpanan Data
                            </h2>
                            <p class="text-gray-600">Kami menyimpan data pribadi Anda selama diperlukan untuk tujuan yang dijelaskan dalam kebijakan ini, atau sesuai yang diwajibkan oleh hukum. Setelah tidak diperlukan, data akan dihapus atau dianonimkan dengan aman.</p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">8</span>
                                Perubahan Kebijakan
                            </h2>
                            <p class="text-gray-600">Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan melalui email atau pemberitahuan di website. Kami menyarankan Anda untuk meninjau kebijakan ini secara berkala.</p>
                        </section>

                        <section>
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">9</span>
                                Hubungi Kami
                            </h2>
                            <p class="text-gray-600 mb-4">Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan hak privasi Anda:</p>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                <p class="text-gray-600"><strong>Email:</strong> privacy@nanokomputer.com</p>
                                <p class="text-gray-600"><strong>Telepon:</strong> (021) 612-8899</p>
                                <p class="text-gray-600"><strong>Alamat:</strong> Mal Mangga Dua Lt.2 No.47A-B, Jakarta Utara</p>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <a href="landingPage.php" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
</body>

</html>