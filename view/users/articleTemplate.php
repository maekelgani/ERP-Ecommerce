<?php
$pageTitle = "Cara Merakit PC Gaming untuk Pemula 2025";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';
?>

<body class="bg-gray-50 min-h-screen font-sans antialiased">
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="w-full">
        <section class="bg-[#882426] py-8 md:py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2">
                        <li class="inline-flex items-center">
                            <a href="landingPage.php" class="inline-flex items-center text-white/70 hover:text-white transition">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <a href="blogNews.php" class="text-white/70 hover:text-white transition">Blog & News</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-white/50 truncate max-w-[150px] md:max-w-xs">Artikel</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Tips & Trick</span>
                    <span class="text-white/60 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        5 menit baca
                    </span>
                </div>

                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                    Cara Merakit PC Gaming untuk Pemula 2025: Panduan Lengkap
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-white/80 text-sm">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Admin+Nano&background=ffffff&color=882426" alt="Admin" class="w-10 h-10 rounded-full border-2 border-white/30">
                        <div>
                            <p class="font-semibold text-white">Admin Nanocomp</p>
                            <p class="text-xs text-white/60">Penulis</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>28 Nov 2025</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>2.5k views</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <article class="lg:col-span-2">
                    <figure class="mb-10 -mt-20 relative z-10">
                        <div class="overflow-hidden rounded-2xl shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1751374156944-aa91dee48408" alt="Merakit PC" class="w-full h-auto object-cover max-h-[450px] hover:scale-105 transition-transform duration-500">
                        </div>
                        <figcaption class="text-center text-gray-500 text-sm mt-4 italic">Ilustrasi komponen PC Gaming modern.</figcaption>
                    </figure>

                    <div class="bg-white rounded-2xl p-6 md:p-10 shadow-sm border border-gray-100">
                        <div class="prose prose-lg max-w-none">
                            <p class="text-xl text-gray-600 mb-8 leading-relaxed border-l-4 border-[#882426] pl-6 bg-gray-50 py-4 rounded-r-xl">
                                Merakit PC sendiri bisa menjadi pengalaman yang menakutkan bagi pemula, tetapi juga sangat memuaskan. Dengan panduan yang tepat, Anda bisa membangun mesin gaming impian Anda dengan harga yang lebih hemat.
                            </p>

                            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-6 flex items-center gap-3">
                                <span class="flex-shrink-0 w-10 h-10 bg-[#882426] text-white rounded-xl flex items-center justify-center text-lg font-bold">1</span>
                                Persiapan Komponen
                            </h2>
                            <p class="text-gray-600 leading-relaxed mb-6">Sebelum memulai, pastikan Anda memiliki semua komponen yang diperlukan. Komponen utama meliputi:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Processor (CPU)</h4>
                                            <p class="text-sm text-gray-500">Otak dari komputer (Intel Core atau AMD Ryzen)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Motherboard</h4>
                                            <p class="text-sm text-gray-500">Papan sirkuit utama penghubung komponen</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">RAM</h4>
                                            <p class="text-sm text-gray-500">Memori jangka pendek untuk multitasking</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Storage (SSD/HDD)</h4>
                                            <p class="text-sm text-gray-500">Tempat menyimpan data dan sistem operasi</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Power Supply (PSU)</h4>
                                            <p class="text-sm text-gray-500">Sumber daya listrik yang stabil</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Casing</h4>
                                            <p class="text-sm text-gray-500">Rumah untuk semua komponen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-6 flex items-center gap-3">
                                <span class="flex-shrink-0 w-10 h-10 bg-[#882426] text-white rounded-xl flex items-center justify-center text-lg font-bold">2</span>
                                Memasang CPU ke Motherboard
                            </h2>
                            <p class="text-gray-600 leading-relaxed mb-6">Ini adalah langkah yang paling krusial. Buka soket CPU pada motherboard dengan mengangkat tuas pengunci. Perhatikan tanda segitiga emas pada sudut CPU dan cocokan dengan tanda di soket. Letakkan CPU secara perlahan tanpa ditekan. Kunci kembali tuasnya.</p>

                            <div class="bg-[#882426]/5 border-l-4 border-[#882426] p-5 my-8 rounded-r-xl">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <div>
                                        <p class="font-bold text-[#882426] mb-1">Tips Penting:</p>
                                        <p class="text-gray-700">Jangan lupa mengoleskan thermal paste seukuran biji jagung di atas CPU sebelum memasang cooler, kecuali cooler bawaan sudah memiliki pasta pre-applied.</p>
                                    </div>
                                </div>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-6 flex items-center gap-3">
                                <span class="flex-shrink-0 w-10 h-10 bg-[#882426] text-white rounded-xl flex items-center justify-center text-lg font-bold">3</span>
                                Manajemen Kabel
                            </h2>
                            <p class="text-gray-600 leading-relaxed mb-6">Setelah semua komponen terpasang di dalam casing, saatnya merapikan kabel. Gunakan cable ties yang biasanya disertakan dalam paket casing. Manajemen kabel yang baik tidak hanya membuat PC terlihat rapi, tetapi juga membantu sirkulasi udara (airflow) agar komponen tidak cepat panas.</p>

                            <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-6 flex items-center gap-3">
                                <span class="flex-shrink-0 w-10 h-10 bg-[#882426] text-white rounded-xl flex items-center justify-center text-lg font-bold">4</span>
                                Kesimpulan
                            </h2>
                            <p class="text-gray-600 leading-relaxed">Merakit PC membutuhkan kesabaran. Jangan terburu-buru dan selalu referensikan buku manual motherboard Anda jika bingung di mana letak kabel front panel. Selamat mencoba!</p>
                        </div>

                        <div class="mt-12 pt-8 border-t border-gray-100">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 mb-3">Tags</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-[#882426] hover:text-white cursor-pointer transition-colors">#PCGaming</span>
                                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-[#882426] hover:text-white cursor-pointer transition-colors">#Tutorial</span>
                                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-[#882426] hover:text-white cursor-pointer transition-colors">#Hardware</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 mb-3">Bagikan</p>
                                    <div class="flex items-center gap-2">
                                        <button class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-500 hover:text-white transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                            </svg>
                                        </button>
                                        <button class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-600 hover:text-white transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                            </svg>
                                        </button>
                                        <button class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-green-500 hover:text-white transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.894-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                            </svg>
                                        </button>
                                        <button class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-800 hover:text-white transition-colors" onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'; setTimeout(() => this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3\'/></svg>', 2000)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
                        <a href="#" class="group block p-5 bg-white border border-gray-100 rounded-2xl hover:border-[#882426] hover:shadow-lg transition-all">
                            <span class="text-xs text-gray-400 uppercase font-semibold flex items-center gap-1 mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Artikel Sebelumnya
                            </span>
                            <h4 class="font-bold text-gray-800 group-hover:text-[#882426] transition-colors line-clamp-2">Review Montech XR: Casing Budget Rasa Premium</h4>
                        </a>
                        <a href="#" class="group block p-5 bg-white border border-gray-100 rounded-2xl hover:border-[#882426] hover:shadow-lg transition-all text-right">
                            <span class="text-xs text-gray-400 uppercase font-semibold flex items-center justify-end gap-1 mb-2">
                                Artikel Selanjutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                            <h4 class="font-bold text-gray-800 group-hover:text-[#882426] transition-colors line-clamp-2">NVIDIA GeForce RTX 50 Series Resmi Diumumkan</h4>
                        </a>
                    </div>
                </article>

                <aside class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h3 class="font-bold text-gray-900 mb-4 text-lg">Cari Artikel</h3>
                            <div class="relative">
                                <input type="text" placeholder="Cari topik atau review..." class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:outline-none focus:border-[#882426] text-sm transition-colors">
                                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                            <h3 class="font-bold text-gray-900 mb-6 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Artikel Populer
                            </h3>
                            <div class="space-y-4">
                                <a href="#" class="flex gap-4 group p-3 -mx-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 relative">
                                        <span class="absolute top-0 left-0 bg-[#882426] text-white text-[10px] font-bold px-2 py-1 rounded-br-lg">#1</span>
                                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070" alt="News" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#882426] line-clamp-2 leading-snug transition-colors">NVIDIA GeForce RTX 50 Series Resmi Diumumkan</h4>
                                        <span class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            2.5k views
                                        </span>
                                    </div>
                                </a>
                                <a href="#" class="flex gap-4 group p-3 -mx-3 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 relative">
                                        <span class="absolute top-0 left-0 bg-gray-500 text-white text-[10px] font-bold px-2 py-1 rounded-br-lg">#2</span>
                                        <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?q=80&w=2042" alt="Review" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#882426] line-clamp-2 leading-snug transition-colors">Review Montech XR: Casing Budget Rasa Premium</h4>
                                        <span class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            1.8k views
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                            <h3 class="font-bold text-gray-900 mb-4 text-lg">Kategori</h3>
                            <ul class="space-y-1">
                                <li>
                                    <a href="#" class="flex justify-between items-center text-gray-600 hover:text-[#882426] text-sm group p-3 hover:bg-[#882426]/5 rounded-xl transition-all">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                            </svg>
                                            News & Update
                                        </span>
                                        <span class="bg-gray-100 text-gray-500 group-hover:bg-[#882426] group-hover:text-white px-2.5 py-1 rounded-full text-xs font-medium transition-colors">12</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex justify-between items-center text-gray-600 hover:text-[#882426] text-sm group p-3 hover:bg-[#882426]/5 rounded-xl transition-all">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            Tips & Trick
                                        </span>
                                        <span class="bg-gray-100 text-gray-500 group-hover:bg-[#882426] group-hover:text-white px-2.5 py-1 rounded-full text-xs font-medium transition-colors">8</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex justify-between items-center text-gray-600 hover:text-[#882426] text-sm group p-3 hover:bg-[#882426]/5 rounded-xl transition-all">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Product Review
                                        </span>
                                        <span class="bg-gray-100 text-gray-500 group-hover:bg-[#882426] group-hover:text-white px-2.5 py-1 rounded-full text-xs font-medium transition-colors">24</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-[#882426] rounded-2xl p-6 text-center">
                            <h3 class="font-bold text-white mb-2">Berlangganan Newsletter</h3>
                            <p class="text-white/70 text-sm mb-4">Dapatkan update artikel terbaru langsung di email Anda</p>
                            <input type="email" placeholder="Email Anda" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:bg-white/20 transition-colors mb-3">
                            <button class="w-full py-3 bg-white text-[#882426] font-semibold rounded-xl hover:bg-gray-100 transition-colors">Berlangganan</button>
                        </div>
                    </div>
                </aside>
            </div>

            <section class="mt-16 pt-12 border-t border-gray-200">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Produk Terkait Artikel Ini</h3>
                    <a href="productCollection.php" class="text-[#882426] font-semibold text-sm hover:underline flex items-center gap-1">
                        Lihat Katalog
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="aspect-square bg-gray-50 rounded-xl mb-4 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1591488320449-011701bb6704" alt="Processor" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">Intel Core i5-14400F</h4>
                        <p class="text-[#882426] font-bold mb-3">Rp 3.500.000</p>
                        <button class="w-full py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-[#882426] hover:text-white transition-colors">Lihat Detail</button>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="aspect-square bg-gray-50 rounded-xl mb-4 overflow-hidden">
                            <img src="https://dlcdnwebimgs.asus.com/gain/f1bc44c6-40ee-47ba-bb8a-b68ec8ea4684/w800" alt="PSU" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">ASUS TUF 750W Bronze</h4>
                        <p class="text-[#882426] font-bold mb-3">Rp 1.250.000</p>
                        <button class="w-full py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-[#882426] hover:text-white transition-colors">Lihat Detail</button>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="aspect-square bg-gray-50 rounded-xl mb-4 overflow-hidden">
                            <img src="https://www.montechpc.com/images/375588/0/1100?stamp=1734687831" alt="Casing" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">Montech XR Black</h4>
                        <p class="text-[#882426] font-bold mb-3">Rp 835.000</p>
                        <button class="w-full py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-[#882426] hover:text-white transition-colors">Lihat Detail</button>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="aspect-square bg-gray-50 rounded-xl mb-4 overflow-hidden">
                            <img src="https://cdn.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPUAirCoolers/GAMMAXX_400_Blue/Gallery/800X800/01.jpg" alt="Cooler" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">DeepCool Gammaxx 400</h4>
                        <p class="text-[#882426] font-bold mb-3">Rp 329.000</p>
                        <button class="w-full py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-[#882426] hover:text-white transition-colors">Lihat Detail</button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
    <?php include '../../components/users/loginRequiredModal.php'; ?>
</body>

</html>