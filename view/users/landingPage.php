<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="stylesheet" href="/../../src/output.css">
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

        <!-- banner promosi slider -->
        <section class="w-full h-[50vh] md:h-[60vh] lg:h-[70vh]">
            <div class="swiper banner-swiper w-full h-full">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#ADADAD">
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#f0f0f0">
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#d4d4d4">
                    </div>
                </div>

                <!-- Navigasi dan Pagination -->
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>

        <!-- CATEGORY -->
        <section class="mt-4 p-5 w-full md:px-8 lg:px-20 flex justify-center">
            <div class=" p-4 md:p-10  pt-4 rounded-lg shadow-sm w-full">
                <h2 class="font-bold text-xl md:text-2xl lg:text-4xl mb-5 "> Shop by Category</h2>
                <!-- Slider Container -->
                <div class="flex gap-4 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 p-2">
                <!-- Cards -->
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">PC Bundling</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="Processor" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Processor</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="Motherboard" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Motherboard</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://images.unsplash.com/photo-1591488320449-011701bb6704?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8Z3B1fGVufDB8fDB8fHww&auto=format&fit=crop&q=60&w=500" alt="VGA" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">VGA</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="SSD" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">SSD</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="RAM" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">RAM</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="PSU" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">PSU</span>
                </a>
                <a href="#"
                    class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                    <img src="https://via.placeholder.com/300x300" alt="Casing" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                    <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Casing</span>
                </a>
                </div>
            </div>
        </section>

        <!-- EVENT Collaboration -->
        <section class="mt-10 p-5 w-full md:px-8 lg:px-20 flex justify-center">
        <div class="p-4 md:p-10 pt-4 rounded-lg w-full">
            <h2 class="font-bold text-xl md:text-2xl lg:text-4xl mb-6">
            Limited Collaboration
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Yang atas ini -->
            <a href="#"
                class="relative group lg:col-span-2 bg-gray-400/75 rounded-lg overflow-hidden h-100 flex items-center justify-center text-3xl font-bold text-gray-700">
                <img src="https://rog.asus.com/media/175456150960.jpg" alt="ROG x Miku"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                <div
                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                <h3 class="text-white text-2xl font-bold mb-2">ROG × Hatsune Miku</h3>
                <p class="text-gray-200 text-sm">Special PC Bundle Collaboration</p>
                </div>
            </a>

            <!-- Bawah di kiri -->
            <a href="#"
                class="relative group bg-gray-400/75 rounded-lg overflow-hidden h-60 flex items-center justify-center text-3xl font-bold text-gray-700">
                <img src="" alt="ROG x Miku"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                <div
                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                <h3 class="text-white text-xl font-bold mb-2">MSI × Battlefield 6</h3>
                <p class="text-gray-200 text-sm">Limited Edition Motherboard Collaboration</p>
                </div>
            </a>

            <!-- Bawah di kanan -->
            <a href="#"
                class="relative group bg-gray-400/75 rounded-lg overflow-hidden h-60 flex items-center justify-center text-3xl font-bold text-gray-700">
                <img src="" alt="ASUS x Evangelion"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                <div
                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                <h3 class="text-white text-xl font-bold mb-2">ASUS × Evangelion</h3>
                <p class="text-gray-200 text-sm">Collector’s Edition Rig</p>
                </div>
            </a>
            </div>
        </div>
        </section>

        <!-- Card Collection Product (BEST SELL PRODUCTS) -->
        <section class="mt-10 p-5 py-1 w-full md:px-8 lg:px-20 flex justify-center">
            <div class="p-4 md:p-10 pt-4 rounded-lg w-full">
                <div class="mx-auto">
                    <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Best Seller</h2>

                    <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        
                    <!-- Produk 1 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 2 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 3 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 4 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 5 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    </ul>
                </div>
            </div>
        </section>

        <!-- Banner promosi -->
        <section class="w-full md:px-8 lg:px-20 flex">
            <div class=" p-4 md:p-10 rounded-lg w-full">
                <a href="" class="max-w-full justify-center">
                    <div class="max-w-full h-18 md:h-25 rounded-lg bg-[#563232] text-white justify-center text-center align-middle">Buat Banner promosi</div>
                    <!-- <img src="" alt="promosibanner" class=""> -->
                </a>
            </div>
        </section>

        <!-- Card Collection Product (NEW PRODUCTS) -->
        <section class="p-5 w-full md:px-8 lg:px-20 flex justify-center">
            <div class="p-4 md:p-10 pt-4 rounded-lg w-full">
                <div class="mx-auto">
                    <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Produk baru</h2>

                    <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        
                    <!-- Produk 1 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 2 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 3 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 4 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    <!-- Produk 5 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    </ul>
                </div>
            </div>
        </section>

        <!-- Brand Pilihan -->
        <section class="w-full bg-[#563232] py-6 px-4 sm:px-6 md:px-8 lg:px-20">
            <div class="w-full relative rounded-lg">
                <h2
                class="font-bold text-sm sm:text-base lg:text-xl mb-4 sm:mb-6 text-white tracking-wide text-center sm:text-left">
                BRAND PILIHAN
                </h2>

                <!-- Wrapper -->
                <div class="relative">
                <!-- Scroll Container -->
                <div
                    id="brandScroll"
                    class="flex space-x-3 sm:space-x-4 overflow-x-auto scrollbar-hide scroll-smooth justify-start sm:justify-center py-2"
                >
                    <!-- Brand Items -->
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                    <div
                    class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"
                    ></div>
                </div>

                <!-- Button Next -->
                <button
                    id="scrollNext"
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-white rounded-full shadow-sm p-2 hover:bg-gray-100 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                </div>
            </div>
        </section>

        <!-- Card Collection Product LIST -->
        <section class="p-5 w-full md:px-8 lg:px-20 flex justify-center">
            <div class="p-4 md:p-10 pt-4 rounded-lg w-full">
                <div class="mx-auto">
                    
                    <div class="flex justify-between">
                        <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Jelajah Katalog</h2>
                    </div>

                    <ul class="mt-8 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        
                    <!-- Produk 1 mulai dari sini -->
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
                        
                    <!-- Produk 2 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>
                        
                    <!-- Produk 3 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>
                        
                    <!-- Produk 4 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>
                        
                    <!-- Produk 5 mulai dari sini -->
                    <li
                        class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300"
                        >
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
                    </li>

                    </ul>
                    <div class="flex justify-center mt-6">
                        <a href="/./view/users/productCollection.php"
                            class="p-2 px-5 bg-[#563232] text-sm rounded-lg text-white font-semibold hover:bg-[#563232]/75 cursor-pointer">
                            Lihat Semua
                        </a>
                    </div>
                    
                </div>
            </div>
        </section>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>

<!-- swiper js masih pake cdn  -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/assets/js/users/dashboard.js"></script>
</body>
</html>