<?php
$pageTitle = "Tentang Kami & Hubungi Kami";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Tentang Kami', 'url' => null]
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <section class="bg-[#882426] rounded-2xl py-12 md:py-16 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 overflow-hidden opacity-10">
                <div class="absolute top-6 right-6 w-24 h-24 border-4 border-white rounded-full"></div>
                <div class="absolute bottom-6 left-6 w-20 h-20 border-4 border-white rounded-full"></div>
            </div>
            <div class="relative z-10 text-center px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-5">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h1 class="text-2xl md:text-4xl font-bold text-white mb-3">Tentang Nano Komputer</h1>
                <p class="text-white/80 text-sm md:text-base max-w-xl mx-auto">Mengenal lebih dekat perjalanan dan komitmen kami dalam menyediakan solusi teknologi terbaik</p>
            </div>
        </section>

        <div class="mb-8">
            <?php include '../../components/users/breadcrumb.php'; ?>
        </div>

        <section class="mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 bg-[#882426]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Tentang Kami</h2>
                    </div>
                    <div class="space-y-4 text-gray-600 leading-relaxed text-sm md:text-base">
                        <p>
                            <strong class="text-gray-900">Nano Komputer</strong> adalah toko komputer terpercaya yang telah melayani kebutuhan teknologi masyarakat Indonesia sejak tahun 2010. Berlokasi di pusat perdagangan Jakarta, kami berkomitmen untuk menyediakan produk-produk komputer berkualitas tinggi dengan harga yang kompetitif.
                        </p>
                        <p>
                            Dengan pengalaman lebih dari satu dekade, kami memahami kebutuhan pelanggan dari berbagai kalangan - mulai dari gamer profesional, content creator, hingga pengguna korporat. Tim kami terdiri dari para ahli teknologi yang siap memberikan konsultasi dan solusi terbaik untuk setiap kebutuhan Anda.
                        </p>
                        <p>
                            Kami bermitra langsung dengan brand-brand ternama seperti ASUS ROG, MSI, Gigabyte, Intel, AMD, dan banyak lagi untuk memastikan keaslian dan garansi resmi setiap produk yang kami jual.
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-[#882426] rounded-2xl shadow-lg p-6 md:p-8 text-white">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold">Visi Kami</h3>
                        </div>
                        <p class="text-white/90 leading-relaxed text-sm md:text-base">
                            Menjadi destinasi utama dan terpercaya bagi seluruh kebutuhan teknologi komputer di Indonesia, dengan menghadirkan pengalaman berbelanja yang mudah, aman, dan memuaskan.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 bg-[#882426]/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Misi Kami</h3>
                        </div>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-600 text-sm">Menyediakan produk berkualitas dengan garansi resmi</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-600 text-sm">Memberikan pelayanan pelanggan yang responsif dan profesional</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-600 text-sm">Menghadirkan harga kompetitif tanpa mengorbankan kualitas</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <span class="text-gray-600 text-sm">Terus berinovasi mengikuti perkembangan teknologi terkini</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md hover:border-[#882426]/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-[#882426] transition-colors duration-300">
                        <svg class="w-6 h-6 text-[#882426] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">14+</h4>
                    <p class="text-gray-500 text-xs md:text-sm">Tahun Pengalaman</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md hover:border-[#882426]/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-[#882426] transition-colors duration-300">
                        <svg class="w-6 h-6 text-[#882426] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">50K+</h4>
                    <p class="text-gray-500 text-xs md:text-sm">Pelanggan Puas</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md hover:border-[#882426]/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-[#882426] transition-colors duration-300">
                        <svg class="w-6 h-6 text-[#882426] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">5000+</h4>
                    <p class="text-gray-500 text-xs md:text-sm">Produk Tersedia</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center group hover:shadow-md hover:border-[#882426]/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-[#882426] transition-colors duration-300">
                        <svg class="w-6 h-6 text-[#882426] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">100%</h4>
                    <p class="text-gray-500 text-xs md:text-sm">Garansi Resmi</p>
                </div>
            </div>
        </section>

        <section id="contact" class="mb-8">
            <div class="text-center mb-8">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Hubungi Kami</h2>
                <p class="text-gray-600 text-sm md:text-base">Kami siap membantu Anda dengan pertanyaan atau kebutuhan apapun</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-[#882426]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Kirim Pesan</h3>
                    </div>

                    <form id="contactForm" class="space-y-4" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" required
                                    value="<?= $isLoggedIn && $customer ? htmlspecialchars($customer['nama_lengkap'] ?? '') : '' ?>"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all text-sm"
                                    placeholder="Masukkan nama lengkap Anda">
                                <p class="text-xs text-red-500 mt-1 hidden" id="nama_lengkap_error"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" required
                                    value="<?= $isLoggedIn && $customer ? htmlspecialchars($customer['email'] ?? '') : '' ?>"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all text-sm"
                                    placeholder="nama@email.com">
                                <p class="text-xs text-red-500 mt-1 hidden" id="email_error"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                                <input type="tel" name="no_telepon" id="no_telepon"
                                    value="<?= $isLoggedIn && $customer ? htmlspecialchars($customer['phone'] ?? '') : '' ?>"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all text-sm"
                                    placeholder="08xx-xxxx-xxxx">
                                <p class="text-xs text-red-500 mt-1 hidden" id="no_telepon_error"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                <select name="kategori" id="kategori" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all text-sm">
                                    <option value="">Pilih kategori</option>
                                    <option value="General">General</option>
                                    <option value="Garansi & Retur">Garansi & Retur</option>
                                    <option value="Aktivasi Akun">Aktivasi Akun</option>
                                    <option value="Pengiriman">Pengiriman</option>
                                    <option value="Pertanyaan Produk">Pertanyaan Produk</option>
                                    <option value="Pemesanan & Rakit PC">Pemesanan & Rakit PC</option>
                                    <option value="Status Pesanan">Status Pesanan</option>
                                </select>
                                <p class="text-xs text-red-500 mt-1 hidden" id="kategori_error"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Subjek <span class="text-red-500">*</span></label>
                            <input type="text" name="subjek" id="subjek" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all text-sm"
                                placeholder="Masukkan subjek pesan">
                            <p class="text-xs text-red-500 mt-1 hidden" id="subjek_error"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan <span class="text-red-500">*</span></label>
                            <textarea name="message" id="message" rows="4" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all resize-none text-sm"
                                placeholder="Tulis pesan Anda di sini..."></textarea>
                            <p class="text-xs text-red-500 mt-1 hidden" id="message_error"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Lampiran (Opsional)</label>
                            <div id="dropZone" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-[#882426]/50 hover:bg-[#882426]/5 transition-all">
                                <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                <div id="uploadPlaceholder">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm text-gray-600 mb-1">Klik atau drag & drop file di sini</p>
                                    <p class="text-xs text-gray-400">PDF, JPG, PNG (maks. 5MB)</p>
                                </div>
                                <div id="filePreview" class="hidden">
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="w-8 h-8 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span id="fileName" class="text-sm text-gray-700 font-medium"></span>
                                        <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-red-500 mt-1 hidden" id="attachment_error"></p>
                        </div>

                        <button type="submit" id="submitBtn" class="w-full md:w-auto px-6 py-2.5 bg-[#882426] text-white font-medium rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 flex items-center justify-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span id="submitBtnText">Kirim Pesan</span>
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-base font-bold text-gray-900 mb-4">Informasi Kontak</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">Alamat</h4>
                                    <p class="text-gray-600 text-xs mt-0.5 leading-relaxed">Jl. Mangga Dua Raya No.47A-B Lt. 2, Jakarta Pusat, DKI Jakarta 10730</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">Telepon</h4>
                                    <p class="text-gray-600 text-xs mt-0.5">(021) 623-09578</p>
                                    <p class="text-gray-600 text-xs">0812-9876-5432 (WhatsApp)</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">Email</h4>
                                    <p class="text-gray-600 text-xs mt-0.5">cs@nanokomputer.com</p>
                                    <p class="text-gray-600 text-xs">support@nanokomputer.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">Jam Operasional</h4>
                                    <p class="text-gray-600 text-xs mt-0.5">Senin - Sabtu: 10:00 - 20:00</p>
                                    <p class="text-gray-600 text-xs">Minggu: 10:00 - 18:00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900">Lokasi Kami</h3>
                        </div>
                        <div class="h-48">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.952870960768!2d106.82047507575525!3d-6.137034560167588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698bf8a2cc18d9%3A0x55ec037cb9f65946!2sNano%20Komputer!5e0!3m2!1sid!2sid!4v1764763083486!5m2!1sid!2sid"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                class="w-full h-full">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('attachment');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const removeFileBtn = document.getElementById('removeFile');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');

            const MAX_FILE_SIZE = 5 * 1024 * 1024;
            const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];
            const ALLOWED_EXTENSIONS = ['.pdf', '.jpg', '.jpeg', '.png'];

            dropZone.addEventListener('click', () => fileInput.click());

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-[#882426]', 'bg-[#882426]/5');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-[#882426]', 'bg-[#882426]/5');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-[#882426]', 'bg-[#882426]/5');
                if (e.dataTransfer.files.length > 0) {
                    handleFile(e.dataTransfer.files[0]);
                }
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            removeFileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                clearFile();
            });

            function handleFile(file) {
                hideError('attachment');

                if (!ALLOWED_TYPES.includes(file.type)) {
                    showError('attachment', 'Format file tidak didukung. Gunakan PDF, JPG, atau PNG.');
                    clearFile();
                    return;
                }

                if (file.size > MAX_FILE_SIZE) {
                    showError('attachment', 'Ukuran file maksimal 5MB.');
                    clearFile();
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                fileName.textContent = file.name;
                uploadPlaceholder.classList.add('hidden');
                filePreview.classList.remove('hidden');
            }

            function clearFile() {
                fileInput.value = '';
                uploadPlaceholder.classList.remove('hidden');
                filePreview.classList.add('hidden');
                fileName.textContent = '';
            }

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validatePhone(phone) {
                if (!phone) return true;
                const re = /^[0-9+\-\s()]{8,20}$/;
                return re.test(phone);
            }

            function showError(fieldName, message) {
                const errorEl = document.getElementById(fieldName + '_error');
                const inputEl = document.getElementById(fieldName);
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                }
                if (inputEl) {
                    inputEl.classList.add('border-red-500');
                }
            }

            function hideError(fieldName) {
                const errorEl = document.getElementById(fieldName + '_error');
                const inputEl = document.getElementById(fieldName);
                if (errorEl) {
                    errorEl.classList.add('hidden');
                }
                if (inputEl) {
                    inputEl.classList.remove('border-red-500');
                }
            }

            function hideAllErrors() {
                ['nama_lengkap', 'email', 'no_telepon', 'kategori', 'subjek', 'message', 'attachment'].forEach(hideError);
            }

            function validateForm() {
                hideAllErrors();
                let isValid = true;

                const namaLengkap = document.getElementById('nama_lengkap').value.trim();
                if (!namaLengkap) {
                    showError('nama_lengkap', 'Nama lengkap wajib diisi.');
                    isValid = false;
                } else if (namaLengkap.length < 3) {
                    showError('nama_lengkap', 'Nama lengkap minimal 3 karakter.');
                    isValid = false;
                }

                const email = document.getElementById('email').value.trim();
                if (!email) {
                    showError('email', 'Email wajib diisi.');
                    isValid = false;
                } else if (!validateEmail(email)) {
                    showError('email', 'Format email tidak valid.');
                    isValid = false;
                }

                const phone = document.getElementById('no_telepon').value.trim();
                if (phone && !validatePhone(phone)) {
                    showError('no_telepon', 'Format nomor telepon tidak valid.');
                    isValid = false;
                }

                const kategori = document.getElementById('kategori').value;
                if (!kategori) {
                    showError('kategori', 'Pilih kategori pesan.');
                    isValid = false;
                }

                const subjek = document.getElementById('subjek').value.trim();
                if (!subjek) {
                    showError('subjek', 'Subjek wajib diisi.');
                    isValid = false;
                } else if (subjek.length < 5) {
                    showError('subjek', 'Subjek minimal 5 karakter.');
                    isValid = false;
                }

                const message = document.getElementById('message').value.trim();
                if (!message) {
                    showError('message', 'Pesan wajib diisi.');
                    isValid = false;
                } else if (message.length < 10) {
                    showError('message', 'Pesan minimal 10 karakter.');
                    isValid = false;
                }

                return isValid;
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                submitBtn.disabled = true;
                submitBtnText.innerHTML = `
                    <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengirim...
                `;

                const formData = new FormData(form);

                try {
                    const response = await fetch('../../api/users/support-ticket.php?action=create', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showNotification('Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.', 'success');
                        form.reset();
                        clearFile();
                    } else {
                        showNotification(result.message || 'Gagal mengirim pesan. Silakan coba lagi.', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtnText.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Pesan
                    `;
                }
            });

            function showNotification(message, type) {
                const existingNotif = document.querySelector('.contact-notification');
                if (existingNotif) existingNotif.remove();

                const notification = document.createElement('div');
                notification.className = `contact-notification fixed top-24 left-1/2 transform -translate-x-1/2 px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 max-w-md ${
                    type === 'success' 
                        ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' 
                        : 'bg-gradient-to-r from-red-500 to-red-600 text-white'
                }`;
                notification.innerHTML = `
                    <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${type === 'success' 
                                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">${type === 'success' ? 'Berhasil!' : 'Gagal!'}</p>
                        <p class="text-sm opacity-90">${message}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-white/70 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;

                document.body.appendChild(notification);

                notification.style.animation = 'slideDown 0.3s ease-out';

                setTimeout(() => {
                    notification.style.animation = 'slideUp 0.3s ease-out';
                    setTimeout(() => notification.remove(), 300);
                }, 6000);
            }
        });
    </script>

    <style>
        @keyframes slideDown {
            from {
                transform: translate(-50%, -100%);
                opacity: 0;
            }

            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translate(-50%, 0);
                opacity: 1;
            }

            to {
                transform: translate(-50%, -100%);
                opacity: 0;
            }
        }
    </style>
</body>

</html>