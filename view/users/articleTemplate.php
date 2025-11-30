<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cara Merakit PC Gaming untuk Pemula 2025 - Nanocomp</title>
    <link rel="icon" href="../../assets/img/Nanocomp.png">
    <link rel="stylesheet" href="../../src/output.css">
    <!-- Tailwind for preview if local output.css missing -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan variabel warna yang sama dengan Dashboard */
        .bg-primary { background-color: #fb923c; }
        .text-primary { color: #ea580c; }
        .hover\:text-primary:hover { color: #ea580c; }
        .border-primary { border-color: #fb923c; }
        
        /* Typography khusus untuk konten artikel agar enak dibaca */
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #111827; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.75; color: #374151; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #374151; }
        .prose li { margin-bottom: 0.5rem; }
    </style>
</head>
<body class="w-full bg-white h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <?php include '../../components/users/navbarUsers.php' ;?>
        <!-- Navbar Placeholder for preview purpose (jika include php tidak jalan di preview ini) -->
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-20 h-16 flex items-center justify-between md:hidden lg:hidden">
            <span class="font-bold text-xl text-primary">Nanocomp</span>
        </div>
    </header>

    <main class="max-w-full mx-auto px-4 sm:px-6 lg:px-20 py-10 mb-20">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="dashboard.php" class="inline-flex items-center hover:text-primary transition">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="#" class="ml-1 md:ml-2 hover:text-primary transition">Tips & Trick</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-gray-400 truncate max-w-xs">Cara Merakit PC Gaming</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left Column: Main Article Content -->
            <article class="lg:col-span-2">
                <!-- Article Header -->
                <header class="mb-8">
                    <span class="bg-orange-100 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 inline-block">Tips & Trick</span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                        Cara Merakit PC Gaming untuk Pemula 2025: Panduan Lengkap
                    </h1>
                    
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center text-gray-500 text-sm border-b border-gray-100 pb-6 gap-4 sm:gap-6">
                        <div class="flex items-center">
                            <img src="https://ui-avatars.com/api/?name=Admin+Nano&background=fb923c&color=fff" alt="Admin" class="w-8 h-8 rounded-full mr-2">
                            <span class="font-medium text-gray-900">Admin Nanocomp</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            28 Nov 2025
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            5 min read
                        </div>
                    </div>
                </header>

                <!-- Featured Image -->
                <figure class="mb-10 group overflow-hidden rounded-xl">
                    <img src="https://images.unsplash.com/photo-1751374156944-aa91dee48408" alt="Merakit PC" class="w-full h-auto rounded-xl shadow-lg object-cover max-h-[500px] transform transition duration-500 group-hover:scale-105">
                    <figcaption class="text-center text-gray-400 text-sm mt-3 italic">Ilustrasi komponen PC Gaming modern.</figcaption>
                </figure>

                <!-- Article Body (Content from Admin) -->
                <div class="prose max-w-none text-gray-700">
                    <p class="lead text-lg md:text-xl text-gray-600 mb-8 font-medium">
                        Merakit PC sendiri bisa menjadi pengalaman yang menakutkan bagi pemula, tetapi juga sangat memuaskan. Dengan panduan yang tepat, Anda bisa membangun mesin gaming impian Anda dengan harga yang lebih hemat.
                    </p>

                    <h2>1. Persiapan Komponen</h2>
                    <p>Sebelum memulai, pastikan Anda memiliki semua komponen yang diperlukan. Komponen utama meliputi:</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Processor (CPU):</strong> Otak dari komputer Anda (Intel Core atau AMD Ryzen).</li>
                        <li><strong>Motherboard:</strong> Papan sirkuit utama yang menghubungkan semua komponen.</li>
                        <li><strong>RAM:</strong> Memori jangka pendek untuk multitasking.</li>
                        <li><strong>Storage (SSD/HDD):</strong> Tempat menyimpan data dan sistem operasi.</li>
                        <li><strong>Power Supply (PSU):</strong> Sumber daya listrik yang stabil.</li>
                        <li><strong>Casing:</strong> Rumah untuk semua komponen agar terlindungi.</li>
                    </ul>

                    <h2>2. Memasang CPU ke Motherboard</h2>
                    <p>Ini adalah langkah yang paling krusial. Buka soket CPU pada motherboard dengan mengangkat tuas pengunci. Perhatikan tanda segitiga emas pada sudut CPU dan cocokan dengan tanda di soket. Letakkan CPU secara perlahan tanpa ditekan. Kunci kembali tuasnya.</p>
                    
                    <!-- Quote / Highlight Box -->
                    <div class="bg-orange-50 border-l-4 border-primary p-4 my-6 rounded-r-lg">
                        <p class="font-bold text-orange-800 m-0 mb-1">Tips Penting:</p>
                        <p class="text-orange-700 text-sm m-0">Jangan lupa mengoleskan thermal paste seukuran biji jagung di atas CPU sebelum memasang cooler, kecuali cooler bawaan sudah memiliki pasta pre-applied.</p>
                    </div>

                    <h2>3. Manajemen Kabel</h2>
                    <p>Setelah semua komponen terpasang di dalam casing, saatnya merapikan kabel. Gunakan cable ties yang biasanya disertakan dalam paket casing. Manajemen kabel yang baik tidak hanya membuat PC terlihat rapi, tetapi juga membantu sirkulasi udara (airflow) agar komponen tidak cepat panas.</p>

                    <h2>Kesimpulan</h2>
                    <p>Merakit PC membutuhkan kesabaran. Jangan terburu-buru dan selalu referensikan buku manual motherboard Anda jika bingung di mana letak kabel front panel. Selamat mencoba!</p>
                </div>

                <!-- Tags & Share Actions -->
                <div class="mt-12 pt-8 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 cursor-pointer transition">#PCGaming</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 cursor-pointer transition">#Tutorial</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 cursor-pointer transition">#Hardware</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-500 text-sm font-medium">Share:</span>
                            <button class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-600 hover:text-white transition duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </button>
                            <button class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-blue-800 hover:text-white transition duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </button>
                            <button class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-green-600 hover:text-white transition duration-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.894-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Next/Prev Article -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-10">
                    <a href="#" class="group block p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition bg-gray-50/50">
                        <span class="text-xs text-gray-400 uppercase font-semibold flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Artikel Sebelumnya
                        </span>
                        <h4 class="font-bold text-gray-800 group-hover:text-primary truncate mt-1">Review Montech XR: Casing Budget</h4>
                    </a>
                    <a href="#" class="group block p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition bg-gray-50/50 text-right">
                        <span class="text-xs text-gray-400 uppercase font-semibold flex items-center justify-end gap-1">
                            Artikel Selanjutnya
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                        <h4 class="font-bold text-gray-800 group-hover:text-primary truncate mt-1">NVIDIA GeForce RTX 50 Series</h4>
                    </a>
                </div>
            </article>

            <!-- Right Column: Sidebar (Sticky) -->
            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Cari Artikel</h3>
                        <div class="relative">
                            <input type="text" placeholder="Cari topik atau review..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm transition">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Popular Articles Widget -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Artikel Populer</h3>
                        <div class="space-y-5">
                            <!-- Item 1 -->
                            <a href="#" class="flex gap-4 group">
                                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 relative">
                                    <span class="absolute top-0 left-0 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-br-md">#1</span>
                                    <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=2070" alt="News" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 group-hover:text-primary line-clamp-2 leading-snug">NVIDIA GeForce RTX 50 Series Resmi Diumumkan</h4>
                                    <span class="text-xs text-gray-400 mt-1 block flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        2.5k views
                                    </span>
                                </div>
                            </a>
                            <!-- Item 2 -->
                            <a href="#" class="flex gap-4 group">
                                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 relative">
                                    <span class="absolute top-0 left-0 bg-gray-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-br-md">#2</span>
                                    <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?q=80&w=2042" alt="Review" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 group-hover:text-primary line-clamp-2 leading-snug">Review Montech XR: Casing Budget Rasa Premium</h4>
                                    <span class="text-xs text-gray-400 mt-1 block flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        1.8k views
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Categories Widget -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Kategori</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="flex justify-between items-center text-gray-600 hover:text-primary text-sm group p-2 hover:bg-orange-50 rounded-lg transition">
                                    <span>News & Update</span>
                                    <span class="bg-gray-100 text-gray-500 group-hover:bg-primary group-hover:text-white px-2 py-0.5 rounded-full text-xs transition">12</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex justify-between items-center text-gray-600 hover:text-primary text-sm group p-2 hover:bg-orange-50 rounded-lg transition">
                                    <span>Tips & Trick</span>
                                    <span class="bg-gray-100 text-gray-500 group-hover:bg-primary group-hover:text-white px-2 py-0.5 rounded-full text-xs transition">8</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex justify-between items-center text-gray-600 hover:text-primary text-sm group p-2 hover:bg-orange-50 rounded-lg transition">
                                    <span>Product Review</span>
                                    <span class="bg-gray-100 text-gray-500 group-hover:bg-primary group-hover:text-white px-2 py-0.5 rounded-full text-xs transition">24</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </aside>
        </div>

        <!-- Related Products Section (Commerce Integration) -->
        <section class="mt-20 border-t border-gray-200 pt-10">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-gray-900">Produk Terkait Artikel Ini</h3>
                <a href="#" class="text-primary font-semibold text-sm hover:underline">Lihat Katalog &rarr;</a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Product 1 -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="https://images.unsplash.com/photo-1591488320449-011701bb6704" alt="Processor" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-100">
                    <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-1">Intel Core i5-14400F</h4>
                    <p class="text-primary font-bold">Rp 3.500.000</p>
                    <button class="mt-3 w-full py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-primary hover:text-white transition border border-gray-200 hover:border-primary">Lihat Detail</button>
                </div>
                <!-- Product 2 -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="https://dlcdnwebimgs.asus.com/gain/f1bc44c6-40ee-47ba-bb8a-b68ec8ea4684/w800" alt="PSU" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-100">
                    <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-1">ASUS TUF 750W Bronze</h4>
                    <p class="text-primary font-bold">Rp 1.250.000</p>
                    <button class="mt-3 w-full py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-primary hover:text-white transition border border-gray-200 hover:border-primary">Lihat Detail</button>
                </div>
                <!-- Product 3 -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="https://www.montechpc.com/images/375588/0/1100?stamp=1734687831" alt="Casing" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-100">
                    <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-1">Montech XR Black</h4>
                    <p class="text-primary font-bold">Rp 835.000</p>
                    <button class="mt-3 w-full py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-primary hover:text-white transition border border-gray-200 hover:border-primary">Lihat Detail</button>
                </div>
                <!-- Product 4 -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                    <img src="https://cdn.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPUAirCoolers/GAMMAXX_400_Blue/Gallery/800X800/01.jpg" alt="Cooler" class="w-full h-40 object-cover rounded-lg mb-4 bg-gray-100">
                    <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-1">DeepCool Gammaxx 400</h4>
                    <p class="text-primary font-bold">Rp 329.000</p>
                    <button class="mt-3 w-full py-2 bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg hover:bg-primary hover:text-white transition border border-gray-200 hover:border-primary">Lihat Detail</button>
                </div>
            </div>
        </section>

    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>

</body>
</html>