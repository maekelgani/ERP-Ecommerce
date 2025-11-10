<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="stylesheet" href="/../../src/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarGuest.php' ;?>
    </header>
    <main class="max-w-full">
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
                    <a href="#" class="block transition-colors hover:text-gray-900"> ... </a>
                    </li>
                </ol>
            </nav>
            </div>
        </section>
        
        <!-- img -->
        <section class="w-full md:px-8 lg:px-20 flex">
            <div class=" p-4 md:px-10 rounded-lg w-full">
                <div>
                    <span class="absolute inset-0 text-white text-xl font-bold mb-2">Collection</span>
                    <div class="max-w-full h-18 md:h-25 rounded-lg bg-[#563232] text-white justify-center text-center align-middle"></div>
                </div>    
            </div>
        </section>
        
        <!-- Product Collection List -->
        <section class="px-3 w-full md:px-8 lg:px-20 flex justify-center">
            <div class="px-2 md:px-10 pt-4 rounded-lg w-full">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Product Collection</h2>

                    <div class="flex items-center gap-3">
                    <!-- Sort Dropdown iconnya -->
                    <div class="relative">
                        <button id="sortBtn" class="flex items-center gap-1 text-gray-600 hover:text-gray-800">
                        <span class="material-symbols-outlined text-sm">expand_more</span> <!--Icon Di sini-->
                        </button>

                        <!-- 
                        INI SORTING HARGA
                        -->
                        <div id="sortMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-md shadow-lg">
                        <ul class="text-sm text-gray-700">
                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Newest</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Lowest Price</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">Highest Price</a></li>
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
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- 
                    Filter Sidebar 
                    -->
                    <aside id="filterSidebar" class="hidden lg:block lg:w-1/4 space-y-4 ">
                        <!-- Type -->
                        <div class="space-y-1 text-gray-700">
                            <p class="font-semibold">Type</p>
                            <ul class="space-y-1">
                            <li><a href="#" class="block hover:text-gray-900">Totes</a></li>
                            <li><a href="#" class="block hover:text-gray-900">Backpacks</a></li>
                            <li><a href="#" class="block hover:text-gray-900">Travel Bags</a></li>
                            <li><a href="#" class="block hover:text-gray-900">Hip Bags</a></li>
                            <li><a href="#" class="block hover:text-gray-900">Laptop Sleeves</a></li>
                            </ul>
                        </div>

                        <!-- Filters -->
                        <div class="mt-6">
                            <p class="font-semibold text-gray-900 mb-3">Filters</p>

                            <!-- Filter Card 
                            Filter Kategori -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Category
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <div class="px-4 pb-4 text-sm text-gray-700 space-y-3">
                                    <div class="flex flex-wrap gap-2">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="rounded text-red-800">
                                        <span class="w-4 h-4 bg-black rounded-full border border-gray-300"></span> Black
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="rounded text-red-800">
                                        <span class="w-4 h-4 bg-blue-500 rounded-full border border-gray-300"></span> Blue
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="rounded text-red-800">
                                        <span class="w-4 h-4 bg-amber-600 rounded-full border border-gray-300"></span> Brown
                                    </label>
                                    </div>
                                </div>
                            </details>

                            <!-- Filter S -->
                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Availability
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <div class="px-4 pb-4 text-sm text-gray-700 space-y-2">
                                    <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-red-800">
                                    In Stock
                                    </label>
                                    <label class="flex items-center gap-2">
                                    <input type="checkbox" class="rounded text-red-800">
                                    Out of Stock
                                    </label>
                                </div>
                            </details>

                            <details class="group border border-gray-200 rounded-lg overflow-hidden">
                                <summary class="flex items-center justify-between px-4 py-3 cursor-pointer font-semibold text-gray-900 hover:bg-gray-50">
                                    Price
                                    <span class="material-symbols-outlined text-sm transition-transform duration-200 group-open:rotate-180">expand_more</span> <!--icon-->
                                </summary>
                                <div class="px-4 pb-4 text-sm text-gray-700 space-y-3">
                                    <label class="flex items-center gap-2">
                                    <input type="radio" name="price" class="text-red-800"> Under $50
                                    </label>
                                    <label class="flex items-center gap-2">
                                    <input type="radio" name="price" class="text-red-800"> $50–$100
                                    </label>
                                    <label class="flex items-center gap-2">
                                    <input type="radio" name="price" class="text-red-800"> Above $100
                                    </label>
                                </div>
                            </details>
                        </div>
                    </aside>


                    <!-- Product Grid -->
                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 border border-dashed border-gray-300 rounded-lg p-6 min-h-[300px]">
                    <!-- Dummy product cards -->
                    <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                    <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                    <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                    <div class="aspect-square bg-gray-100 rounded-lg animate-pulse"></div>
                    </div>
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

    <Script src="/assets/js/users/productCollection.js" defer></Script>
</body>
</html>