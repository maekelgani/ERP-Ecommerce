<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="stylesheet" href="/../../src/output.css">
    <link rel="icon" href="../assets/img/Nanocomp.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarGuest.php' ;?>
    </header>
    <main class="max-w-full mb-10">
        <div class="border-b border-gray-200 bg-gray-100 px-4 py-2 text-gray-900">
            <p class="text-center font-medium">
                Lorem, ipsum dolor
                <a href="#" class="inline-block underline"> sit amet consectetur </a>
            </p>
        </div>

        <!-- Navigasi breadcrumb / PATH -->
        <section class="mt-5 px-6 md:px-8 lg:px-20 w-full flex justify-center"> 
            <div class="w-full md:px-10">
            <nav aria-label="Breadcrumb">   
                <ol class="flex items-center gap-1 text-sm text-gray-700">
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> Home </a>
                    </li>

                    <!-- Path 1 -->
                    <li class="rtl:rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.247 6-16.5"></path>
                    </svg>
                    </li>

                    <!-- Path 2 -->
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> Collection </a>
                    </li>

                    <li class="rtl:rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.247 6-16.5"></path>
                    </svg>
                    </li>

                    <!-- Path 3 (jika ada) -->
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> Product Collection </a>
                    </li>
                </ol>
            </nav>
            </div>
        </section>
        
        <!-- banner promosi slider -->
        <section class="w-full h-40 md:h-56 lg:h-72 ">
            <div class="relative mx-6 md:mx-18 lg:mx-30 h-full bg-cover bg-center rounded-2xl
            [background-image:url('https://images.unsplash.com/photo-1504237111663-37d6094bec09?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170')]">
                
                <!-- overlay hitam transparan -->
                <div class="absolute inset-0 bg-black/55 rounded-2xl"></div>

                <!-- teks di atas overlay -->
                <span class="absolute inset-0 flex items-center justify-center text-white text-2xl lg:text-3xl xl:text-4xl font-bold">
                Collection
                </span>

            </div>
        </section>
        
        <!-- Product Collection List -->
        <section class="px-3 w-full md:px-8 lg:px-20 flex justify-center">
            <div class="px-2 md:px-10 pt-4 rounded-lg w-full">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl lg:text-3xl xl:text-4xl font-bold text-gray-900">Product Collection</h2>

                    <div class="flex items-center gap-3">
                        <!-- Sort Dropdown icon (UNTUK MOBILE) -->
                        <div class="relative">
                            <button id="sortBtn" class="flex items-center gap-1 text-gray-600 hover:text-gray-800">
                                <span class="material-symbols-outlined text-sm">expand_more</span> <!--Icon Di sini-->
                            </button>

                            <!-- 
                            INI SORTING HARGA (sebelah kanan)
                            -->
                            <div id="sortMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg">
                                <ul class="text-sm text-gray-700">
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Terbaru</a></li>
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Paling Murah</a></li>
                                    <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Paling Mahal</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Grid & Filter Icons -->
                        <button id="filterToggle" class="lg:hidden p-2 rounded-md hover:bg-gray-100 text-gray-600">
                            <span class="material-symbols-outlined">filter_alt</span>
                        </button>
                        <button class="p-2 rounded-md hover:bg-gray-100 text-gray-600 hidden lg:block">
                            <span class="material-symbols-outlined">grid_view</span>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex flex-col lg:flex-row gap-6 items-start">
                    <!-- 
                    Filter Sidebar 
                    -->
                    <aside id="filterSidebar" class="hidden lg:block lg:w-1/4 space-y-4 ">
                        <!-- Brand Terbaik -->
                        <div class="space-y-1 text-gray-700">
                            <p class="font-semibold">Brand Terbaik</p>
                            <ul class="space-y-1">
                            <li><a href="#" class="block hover:text-gray-900">Intel</a></li>
                            <li><a href="#" class="block hover:text-gray-900">MSI</a></li>
                            <li><a href="#" class="block hover:text-gray-900">Asus</a></li>
                            <li><a href="#" class="block hover:text-gray-900">AMD</a></li>
                            <li><a href="#" class="block hover:text-gray-900">GIGABYTE</a></li>
                            </ul>
                        </div>

                        <!-- Filters -->
                        <div class="mt-6 space-y-2">
                            <p class="font-semibold text-gray-900 mb-3">Filters</p>

                            <!-- Filter Card 
                            Filter KETERSEDIAAN STOK () -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Availability
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <!-- item-item (kategorinya) -->
                                <ul class="px-4 pb-4 text-sm text-gray-700 space-y-2">
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            In Stock
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            Out Stock
                                        </label>
                                    </li>
                                </ul>
                            </details>

                            <!-- Filter Card 
                            Filter Kategori -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Kategori
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <!-- item-item (kategorinya) -->
                                <ul class="px-4 pb-4 text-sm text-gray-700 space-y-2">
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            Motherboard
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            Processor
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            Graphic Card
                                        </label>
                                    </li>
                                </ul>
                            </details>

                            <!-- Filter Card 
                            Filter Kategori -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Brand/Vendor
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <!-- item-item (kategorinya) -->
                                <ul class="px-4 pb-4 text-sm text-gray-700 space-y-2">
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            AMD
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            Intel
                                        </label>
                                    </li>
                                    
                                    <li>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="size-5 rounded-sm border-gray-300 shadow-sm">
                                            COLORFUL
                                        </label>
                                    </li>
                                </ul>
                            </details>

                            <!-- Filter Card 
                            Filter set Harga -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Rentang Harga
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <div class="border-t border-gray-200 bg-white">
                                    <div class="border-t border-gray-200 p-4">
                                        <div class="flex justify-between gap-4">
                                            <!-- SET MINIMUM -->
                                            <label for="" class="flex items-center gap-2">
                                                <span></span>
                                                <input type="number" placeholder="From" min="0" class="w-full rounded-md border border-gray-200 shadow-xs sm:text-sm p-1">
                                            </label>
                                            <!-- SET MAKSIMUM -->
                                            <label for="" class="flex items-center gap-2">
                                                <span></span>
                                                <input type="number" placeholder="To" min="999999999" class="w-full rounded-md border border-gray-200 shadow-xs sm:text-sm p-1">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <button class="p-2 px-5 mt-4 bg-[#563232] text-sm rounded-lg text-white font-semibold hover:bg-[#563232]/75 cursor-pointer">Terapkan Filter</button>
                        </div>
                    </aside>

                    <!-- Product Grid -->
                    <ul class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 border border-dashed border-gray-300 rounded-lg p-6 min-h-[300px] h-auto">
                        
                        <!-- Dummy product cards 1 -->
                        <li 
                            class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col duration-300"
                            >
                            <a href="/view/users/productDetail.php"> 
                            <div class="relative">
                                <!-- Product Image -->
                                <img
                                src="https://images.unsplash.com/photo-1628202926206-c63a34b1618f?auto=format&fit=crop&q=80&w=1160"
                                alt="Wireless Headphones"
                                class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                <!-- Konten teks -->
                                <div class="flex-grow">
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        $49.99
                                        <span class="text-gray-400 line-through text-xs sm:text-sm">$80</span>
                                    </p>

                                    <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                    Wireless Headphones
                                    </h3>

                                    <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore nobis iure obcaecati pariatur.
                                    </p>
                                </div>

                                <!-- Tombol -->
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200"
                                    >
                                        Add to Cart
                                    </button>

                                    <button
                                        type="button"
                                        class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-gray-800"
                                    >
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                            </a>
                        </li>
                        
                        <!-- Dummy product cards 2 -->
                        <li 
                            class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                            >
                            <a href="/view/users/productDetail.php"> 
                            <div class="relative">
                                <!-- Product Image -->
                                <img
                                src="https://images.unsplash.com/photo-1628202926206-c63a34b1618f?auto=format&fit=crop&q=80&w=1160"
                                alt="Wireless Headphones"
                                class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                <!-- Konten teks -->
                                <div class="flex-grow">
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        $49.99
                                        <span class="text-gray-400 line-through text-xs sm:text-sm">$80</span>
                                    </p>

                                    <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                    Wireless Headphones
                                    </h3>

                                    <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore nobis iure obcaecati pariatur.
                                    </p>
                                </div>

                                <!-- Tombol -->
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200"
                                    >
                                        Add to Cart
                                    </button>

                                    <button
                                        type="button"
                                        class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-gray-800"
                                    >
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                            </a>
                        </li>
                        
                        <!-- Dummy product cards 3 -->
                        <li 
                            class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                            >
                            <a href="/view/users/productDetail.php"> 
                            <div class="relative">
                                <!-- Product Image -->
                                <img
                                src="https://images.unsplash.com/photo-1628202926206-c63a34b1618f?auto=format&fit=crop&q=80&w=1160"
                                alt="Wireless Headphones"
                                class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                <!-- Konten teks -->
                                <div class="flex-grow">
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        $49.99
                                        <span class="text-gray-400 line-through text-xs sm:text-sm">$80</span>
                                    </p>

                                    <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                    Wireless Headphones
                                    </h3>

                                    <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore nobis iure obcaecati pariatur.
                                    </p>
                                </div>

                                <!-- Tombol -->
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200"
                                    >
                                        Add to Cart
                                    </button>

                                    <button
                                        type="button"
                                        class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-gray-800"
                                    >
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                            </a>
                        </li>
                        
                        <!-- Dummy product cards 4 -->
                        <li 
                            class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                            >
                            <a href="/view/users/productDetail.php"> 
                            <div class="relative">
                                <!-- Product Image -->
                                <img
                                src="https://images.unsplash.com/photo-1628202926206-c63a34b1618f?auto=format&fit=crop&q=80&w=1160"
                                alt="Wireless Headphones"
                                class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                <!-- Konten teks -->
                                <div class="flex-grow">
                                    <p class="text-gray-700 text-sm sm:text-base">
                                        $49.99
                                        <span class="text-gray-400 line-through text-xs sm:text-sm">$80</span>
                                    </p>

                                    <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                    Wireless Headphones
                                    </h3>

                                    <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore nobis iure obcaecati pariatur.
                                    </p>
                                </div>

                                <!-- Tombol -->
                                <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200"
                                    >
                                        Add to Cart
                                    </button>

                                    <button
                                        type="button"
                                        class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-gray-800"
                                    >
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                            </a>
                        </li>
                        
                    </ul>
                </div>

                <!-- Mobile Filter Drawer -->
                <div id="filterDrawer" class="fixed inset-0 bg-black/40 hidden z-40">
                    <div class="absolute bottom-0 w-full bg-white rounded-t-2xl p-6 shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-lg">Filters</h3>
                        <button id="closeFilter" class="text-gray-500 hover:text-gray-700">
                        <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="space-y-3 text-sm text-gray-700">
                        <p>Filter options (same as sidebar)</p>
                    </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="">
        <?php include '../../components/users/footer.php' ;?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script> 
    <Script src="/assets/js/users/productCollection.js" defer></Script>
</body>
</html>