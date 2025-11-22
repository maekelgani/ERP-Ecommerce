<?php
$pageTitle = "Product Collection";
$metaDescription = "Koleksi lengkap produk gaming: PC Gaming, Monitor Gaming, Keyboard Mechanical, Mouse Gaming dengan harga terbaik.";
// atau bisa tidak perlu set, biarkan default
include '../../components/users/head.php';

class Product
{
    public function __construct(
        public string $image,
        public string $name,
        public string $price,
        public string $oldPrice,
        public string $description
    ) {}
}
// data dummy gw pindah di sini
$collectionProducts = [

    new Product(
        "https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/99/MTA-159325542/logitech_logitech-g304-lightspeed-mouse-gaming-wireless-sensor-12k-dpi_full01.jpg",
        "Logitech G304 Lightspeed Wireless Gaming Mouse",
        "Rp 499.000",
        "Rp 649.000",
        "Sensor HERO 12K, wireless Lightspeed, cocok untuk gaming dan produktivitas."
    ),

    new Product(
        "https://rexus.id/cdn/shop/files/Davia_D68_1_d5b0e8bc-f7ee-4aae-8308-6bd9b880e0f6.png?v=1704377114",
        "Rexus DA Public Beta Mechanical Keyboard",
        "Rp 399.000",
        "Rp 520.000",
        "Keyboard mechanical hot-swap dengan switch tactile, RGB dynamic lighting."
    ),

    new Product(
        "https://media.dinomarket.com/docs/imgTD/2024-12/DM_F16984C104D86F322B3AC7BE96A6AEE8_231224091224_ll.jpg",
        "Vention USB-C Hub 6-in-1",
        "Rp 289.000",
        "Rp 340.000",
        "USB-C hub dengan HDMI 4K, USB 3.0, dan card reader, cocok untuk laptop modern."
    ),

    new Product(
        "https://els.id/wp-content/uploads/2023/09/57a897cc-8a0f-44e2-bedc-9d4e27ec7e1c.jpg",
        "Seagate Barracuda 2TB 3.5 SATA",
        "Rp 799.000",
        "Rp 950.000",
        "HDD kapasitas besar untuk penyimpanan data, cocok untuk PC dan CCTV."
    ),

    new Product(
        "https://orbit.co.id/assets/uploads/2023/05/328f55ed-b089-4449-b8c2-5d37cfeee6af.jpg",
        "Acer Nitro VG240Y 24-inch 75Hz IPS Monitor",
        "Rp 1.599.000",
        "Rp 1.900.000",
        "Monitor IPS Full HD 24 inci dengan warna akurat dan refresh rate 75Hz."
    ),

    new Product(
        "https://www.excelbd.com/wp-content/uploads/2022/09/Archer-C64-AC1200-Dual-Band-Gigabit-Wi-Fi-Router-box.jpg",
        "TP-Link Archer C6 Gigabit Dual Band Router",
        "Rp 429.000",
        "Rp 550.000",
        "Router dual band dengan MU-MIMO dan kecepatan stabil hingga 1200 Mbps."
    ),

    new Product(
        "https://www.asus.com/media/global/products/klnhxkefsyvowbzy/P_setting_xxx_0_90_end_500.png",
        "Asus Prime A520M-K Motherboard",
        "Rp 899.000",
        "Rp 1.050.000",
        "Motherboard AM4 dengan VRM solid, cocok untuk Ryzen seri 3000–5000."
    ),

    new Product(
        "https://fantech.id/cdn/shop/files/ProductImage_MAXFIT67Black.png?v=1746865163",
        "Fantech MAXFIT67 MK875 Mechanical Keyboard",
        "Rp 899.000",
        "Rp 1.150.000",
        "Keyboard gasket mount, RGB, hot-swappable, cocok untuk content creator."
    ),

    new Product(
        "https://cdn.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPUAirCoolers/GAMMAXX_400_Blue/Gallery/800X800/01.jpg?fm=webp&q=60",
        "DeepCool GAMMAXX 400 V2",
        "Rp 329.000",
        "Rp 410.000",
        "CPU Air Cooler 120mm dengan performa pendinginan kuat dan kipas silent."
    ),

    new Product(
        "https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//100/MTA-48938220/wd_ssd_wd_blue_sn570_500gb_-_ssd_m-2_nvme_pcie_full03_tmeudjdn.jpg",
        "Western Digital Blue SN570 NVMe SSD 500GB",
        "Rp 529.000",
        "Rp 650.000",
        "SSD NVMe cepat untuk meningkatkan performa PC dan laptop harian."
    ),

];


?>

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
        <?php include '../../components/users/navbarUsers.php'; ?>
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

                            <button class="p-2 px-5 mt-4 bg-primary text-sm rounded-lg text-white font-semibold hover:bg-primary/75 cursor-pointer">Terapkan Filter</button>
                        </div>
                    </aside>

                    <!-- Product Grid -->
                    <ul class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 
                            border border-dashed border-gray-300 rounded-lg p-6 min-h-[300px] h-auto">

                        <?php foreach ($collectionProducts as $p): ?>
                            <li
                                class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300">
                                <div class="relative">
                                    <a href="/view/users/productDetail.php">
                                        <img
                                            src="<?= $p->image ?>"
                                            alt="<?= htmlspecialchars($p->name) ?>"
                                            class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg" />
                                    </a>
                                </div>

                                <div class="p-4 sm:p-5 flex flex-col flex-grow">

                                    <div class="flex-grow">
                                        <p class="text-gray-700 text-sm sm:text-base">
                                            <?= $p->price ?>
                                            <span class="text-gray-400 line-through text-xs sm:text-sm"><?= $p->oldPrice ?></span>
                                        </p>

                                        <a href="/view/users/productDetail.php">
                                            <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                                <?= $p->name ?>
                                            </h3>
                                        </a>

                                        <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                            <?= $p->description ?>
                                        </p>
                                    </div>

                                    <!-- Tombol SELALU di bawah -->
                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <button
                                            class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900
                                            transition hover:scale-105 hover:bg-gray-200">
                                            Add to Cart
                                        </button>

                                        <button
                                            type="button"
                                            class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white
                                            transition hover:scale-105 hover:bg-[#A14646]/75">
                                            Buy Now
                                        </button>
                                    </div>

                                </div>
                            </li>
                        <?php endforeach; ?>

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
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <Script src="/assets/js/users/productCollection.js" defer></Script>
</body>

</html>