<?php
$pageTitle = "Syarat dan Ketentuan";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Syarat dan Ketentuan', 'url' => null]
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
                            <pattern id="lines" width="20" height="20" patternUnits="userSpaceOnUse">
                                <path d="M 0 10 L 20 10" fill="none" stroke="white" stroke-width="0.5"/>
                                <path d="M 10 0 L 10 20" fill="none" stroke="white" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#lines)" />
                    </svg>
                </div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            </div>
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-3">Syarat dan Ketentuan</h1>
                <p class="text-white/80 text-sm md:text-lg max-w-2xl">Ketentuan penggunaan layanan dan website Nano Komputer</p>
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
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-700 m-0">Dengan menggunakan website dan layanan Nano Komputer, Anda menyetujui untuk terikat dengan syarat dan ketentuan berikut. Harap baca dengan seksama sebelum melakukan transaksi.</p>
                            </div>
                        </div>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">1</span>
                                Definisi
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li><strong>"Nano Komputer"</strong> mengacu pada PT Nano Komputer Indonesia sebagai penyedia layanan dan pemilik website.</li>
                                <li><strong>"Pengguna"</strong> adalah setiap orang yang mengakses atau menggunakan website dan layanan Nano Komputer.</li>
                                <li><strong>"Produk"</strong> adalah barang yang ditawarkan untuk dijual melalui website.</li>
                                <li><strong>"Layanan"</strong> mencakup semua fitur dan fungsi yang tersedia di website.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">2</span>
                                Ketentuan Umum
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Pengguna harus berusia minimal 18 tahun atau memiliki izin dari orang tua/wali.</li>
                                <li>Pengguna bertanggung jawab untuk menjaga kerahasiaan akun dan password.</li>
                                <li>Dilarang menggunakan website untuk tujuan ilegal atau yang melanggar hukum.</li>
                                <li>Nano Komputer berhak mengubah syarat dan ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">3</span>
                                Pemesanan dan Pembayaran
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Harga produk dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
                                <li>Pesanan dianggap sah setelah pembayaran diterima dan dikonfirmasi.</li>
                                <li>Pembayaran harus dilakukan dalam batas waktu yang ditentukan (24 jam).</li>
                                <li>Nano Komputer berhak membatalkan pesanan jika terjadi kesalahan harga atau stok.</li>
                                <li>Bukti pembayaran wajib disimpan hingga produk diterima dengan baik.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">4</span>
                                Pengiriman
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Estimasi waktu pengiriman tidak mengikat dan dapat berubah sesuai kondisi.</li>
                                <li>Risiko kehilangan atau kerusakan selama pengiriman ditanggung oleh jasa kurir.</li>
                                <li>Pengguna wajib memeriksa kondisi paket saat menerima dari kurir.</li>
                                <li>Klaim kerusakan saat pengiriman harus dilaporkan dalam waktu 1x24 jam dengan bukti.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">5</span>
                                Garansi dan Pengembalian
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Semua produk bergaransi resmi sesuai ketentuan masing-masing brand/distributor.</li>
                                <li>Garansi tidak berlaku untuk kerusakan akibat kelalaian pengguna.</li>
                                <li>Pengembalian dapat dilakukan dalam waktu 7 hari dengan syarat tertentu.</li>
                                <li>Produk yang dikembalikan harus dalam kondisi asli dan kemasan lengkap.</li>
                                <li>Beberapa kategori produk tidak dapat dikembalikan (software, aksesoris terbuka).</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">6</span>
                                Hak Kekayaan Intelektual
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Seluruh konten website adalah milik Nano Komputer dan dilindungi hukum.</li>
                                <li>Dilarang menyalin, mendistribusikan, atau memodifikasi konten tanpa izin tertulis.</li>
                                <li>Logo dan merek dagang adalah milik masing-masing pemegang hak.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">7</span>
                                Batasan Tanggung Jawab
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Nano Komputer tidak bertanggung jawab atas kerugian tidak langsung atau konsekuensial.</li>
                                <li>Website disediakan "sebagaimana adanya" tanpa jaminan apapun.</li>
                                <li>Nano Komputer tidak menjamin ketersediaan layanan tanpa gangguan.</li>
                            </ul>
                        </section>
                        
                        <section class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">8</span>
                                Penyelesaian Sengketa
                            </h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Segala sengketa akan diselesaikan secara musyawarah terlebih dahulu.</li>
                                <li>Jika tidak tercapai kesepakatan, sengketa akan diselesaikan melalui pengadilan.</li>
                                <li>Hukum yang berlaku adalah hukum Republik Indonesia.</li>
                            </ul>
                        </section>
                        
                        <section>
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="w-8 h-8 bg-[#882426]/10 rounded-lg flex items-center justify-center text-[#882426] text-sm font-bold">9</span>
                                Kontak
                            </h2>
                            <p class="text-gray-600 mb-4">Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini, silakan hubungi kami:</p>
                            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                <p class="text-gray-600"><strong>Email:</strong> legal@nanokomputer.com</p>
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
