<?php
$pageTitle = "Home";
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
$bestSellProducts = [
    new Product(
        "/../../assets/img/products/productDummy-1.png",
        "ASUS Dual GeForce RTX 5060 8GB GDDR7 OC Edition",
        "Rp6.059.0009",
        "Rp8.609.0009",
        "Model: DUAL-RTX5060-O8G; AI Performance: 623 TOPs; Video Memory: 8GB GDDR7"
    ),
    new Product(
        "https://www.montechpc.com/images/375588/0/1100?stamp=1734687831",
        "Montech XR",
        "Rp 835.001",
        "Rp1.200.000",
        "Three-Sided Magnetic Dust Filter; Support Top-Mount 360mm AIO; Support NVIDIA® GeForce RTX™ 40 .."
    ),
    new Product(
        "https://els.id/wp-content/uploads/2025/05/Samsung-Evo-Plus-990-1TB.png",
        "SSD m.2 NVMe Samsung Evo Plus 990 2280 Gen 4 1TB",
        "Rp1.605.000",
        "Rp2.520.000",
        "FORM FACTOR M.2 (2280); INTERFACE PCIe® Gen 4.0 x4 / 5.0 x2 NVMe™ 2.0"
    ),
    new Product(
        "https://www.asrock.com/Graphics-Card/photo/Radeon%20RX%209060%20XT%20Challenger%208GB%20OC(M1).png",
        "AMD Radeon™ RX 9060 XT Challenger 8GB OC",
        "Rp7.069.000",
        "Rp8.800.000",
        "AMD Radeon™ RX 9060 XT GPU; 8GB GDDR6 on 128-bit Memory Bus; Boost Clock*: Up to 3290 MHz/ 20Gbps"
    ),
    new Product(
        "https://www.asrock.com/mb/photo/B650%20Pro%20RS(M1).png",
        "ASROCK B650M Pro RS",
        "Rp2.759.000",
        "Rp3.500.000",
        "PCIe Gen5 (M.2); 4 x DDR5 DIMM Slots; Supports AMD Socket AM5 Ryzen 8000 and 7000 Series Processors"
    ),
];

// data Dummy gw pindah ke sini
$newProducts = [
    new Product(
        "https://files.coolermaster.com/og-image/hyper-212-rgb-black-1200x630.jpg",
        "Cooler Master Hyper 212 RGB Black Edition",
        "Rp 650.000",
        "Rp 799.000",
        "Fan RGB, 4 Heatpipes Direct Contact, universal mounting, cocok untuk gaming rig pemula."
    ),

    new Product(
        "https://www.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPULiquidCoolers/LT720/Gallery/1440X850/01.jpg",
        "Deepcool LT720 360mm AIO Liquid Cooler",
        "Rp 1.750.000",
        "Rp 2.050.000",
        "Pump high-performance, radiator 360mm, ARGB support, performa stabil untuk CPU high-end."
    ),

    new Product(
        "https://media.kingston.com/kingston/product/FURY_Beast_Black_RGB_DDR5_1-zm-lg.jpg",
        "Kingston Fury Beast DDR5 32GB (2x16GB) 6000MHz",
        "Rp 1.890.000",
        "Rp 2.150.000",
        "RAM DDR5 dengan kecepatan tinggi, cocok untuk gaming dan content creation."
    ),

    new Product(
        "https://flt.com.np/wp-content/uploads/2024/05/Samsung-990-Pro-1.jpg",
        "Samsung 990 PRO 1TB NVMe Gen4",
        "Rp 1.850.000",
        "Rp 2.300.000",
        "Kecepatan read hingga 7450MB/s, cocok untuk editing 4K dan game AAA."
    ),

    new Product(
        "https://dlcdnwebimgs.asus.com/gain/f1bc44c6-40ee-47ba-bb8a-b68ec8ea4684/w800",
        "Asus TUF Gaming 750W Bronze PSU",
        "Rp 1.250.000",
        "Rp 1.480.000",
        "Power supply durable dan stabil, ideal untuk PC mid-high end build."
    ),
];

$regularProducts = [

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

<html lang="en">

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

        <!-- banner promosi slider -->
        <section class="w-full h-[50vh] md:h-[60vh] lg:h-[70vh]">
            <div class="swiper banner-swiper w-full h-full">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <img alt=""
                        class="swiper-slide bg-center object-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        src="/../../assets/img/banner/slider-1.png">

                    <!-- Slide 2 -->
                    <img alt=""
                        class="swiper-slide bg-center object-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        src="/../../assets/img/banner/slider-2.png">

                    <!-- Slide 3 -->
                    <img alt=""
                        class="swiper-slide bg-center object-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        src="/../../assets/img/banner/slider-3.png">
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
                    <!-- Card Content 
                1. PC Ready-->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-pcReady.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">PC Ready</span>
                    </a>
                    <!-- 2. Processor -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-processor.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Processor</span>
                    </a>
                    <!-- 3. Motherboard -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-motherBoard.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Motherboard </span>
                    </a>
                    <!-- 4. Graphic Card -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-graphicCard.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Graphics Card</span>
                    </a>
                    <!-- 5. Storage -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-storage.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Storage</span>
                    </a>
                    <!-- 6. RAM-->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-ram.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">RAM</span>
                    </a>
                    <!-- 7. PSU -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-psu.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">PSU</span>
                    </a>
                    <!-- 8. Casing -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-casing.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Casing</span>
                    </a>
                    <!-- 9. Cooler -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-cooler.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Cooler</span>
                    </a>
                    <!-- 10. Fan -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-fan.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Fan</span>
                    </a>
                    <!-- 11. Motherboard -->
                    <a href="#"
                        class="flex-shrink-0 category-card bg-gray-400/75 w-24 sm:w-48 md:w-52 h-24 sm:h-48 md:h-52 rounded-lg overflow-hidden relative transition-transform duration-300 hover:scale-105">
                        <img alt="PC Bundling" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110"
                            src="/../../assets/img/category/cat-monitor.png">
                        <span class="absolute bottom-2 left-2 text-white font-semibold text-sm bg-black/50 px-2 py-1 rounded">Monitor</span>
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
                        <img src="/../../assets/img/banner/collab-mikuXrog.png" alt="ROG x Miku"
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
                        <img src="/../../assets/img/banner/collab-sflXrog.png" alt="ROG x Miku"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-center items-center text-center">
                            <h3 class="text-white text-xl font-bold mb-2">ROG × Street Fighter League</h3>
                            <p class="text-gray-200 text-sm">Limited Edition Monitor Collaboration</p>
                        </div>
                    </a>

                    <!-- Bawah di kanan -->
                    <a href="#"
                        class="relative group bg-gray-400/75 rounded-lg overflow-hidden h-60 flex items-center justify-center text-3xl font-bold text-gray-700">
                        <img src="/../../assets/img/banner/collab-evaXrog.png" alt="ASUS x Evangelion"
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

                        <?php foreach ($bestSellProducts as $p): ?>
                            <li
                                class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300">
                                <div class="relative">
                                    <img
                                        src="<?= $p->image ?>"
                                        alt="<?= htmlspecialchars($p->name) ?>"
                                        class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg" />
                                </div>

                                <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                    <div class="flex-grow">
                                        <p class="text-gray-700 text-sm sm:text-base">
                                            <?= $p->price ?>
                                            <span class="text-gray-400 line-through text-xs sm:text-sm"><?= $p->oldPrice ?></span>
                                        </p>

                                        <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                            <?= $p->name ?>
                                        </h3>

                                        <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                            <?= $p->description ?>
                                        </p>
                                    </div>

                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <button
                                            class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200">
                                            Add to Cart
                                        </button>

                                        <button
                                            type="button"
                                            class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-[#A14646]/75">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        </section>

        <!-- Banner promosi -->
        <section class="w-full md:px-8 lg:px-20 flex">
            <div class=" p-4 md:p-10 rounded-lg w-full">
                <a href="" class="max-w-full justify-center">
                    <div class="max-w-full h-18 md:h-25 rounded-lg bg-primary text-white justify-center text-center align-middle">Buat Banner promosi</div>
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

                        <?php foreach ($newProducts as $np): ?>
                            <li
                                class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300">
                                <div class="relative">
                                    <img
                                        src="<?= $np->image ?>"
                                        alt="<?= htmlspecialchars($np->name) ?>"
                                        class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg" />
                                </div>

                                <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                    <div class="flex-grow">
                                        <p class="text-gray-700 text-sm sm:text-base">
                                            <?= $np->price ?>
                                            <span class="text-gray-400 line-through text-xs sm:text-sm"><?= $np->oldPrice ?></span>
                                        </p>

                                        <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                            <?= $np->name ?>
                                        </h3>

                                        <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                            <?= $np->description ?>
                                        </p>
                                    </div>

                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <button
                                            class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200">
                                            Add to Cart
                                        </button>

                                        <button
                                            type="button"
                                            class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-[#A14646]/75">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>

                    </ul>

                </div>
            </div>
        </section>

        <!-- Brand Pilihan -->
        <section class="w-full bg-primary py-6 px-4 sm:px-6 md:px-8 lg:px-20">
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
                        class="flex space-x-3 sm:space-x-4 overflow-x-auto scrollbar-hide scroll-smooth justify-start sm:justify-center py-2">
                        <!-- Brand Items -->
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/7d/Intel_logo_%282006-2020%29.svg"
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src="https://logos-world.net/wp-content/uploads/2020/03/AMD-Logo.png"
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTL5WbtWJ0MQzfxZCr0y-zHn44-WzF_293KWA&s"
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSln9EzYFPXEF7aBAxsVhUjkgRvqeTd0rk78Q&s"
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPgtNHcrqqPMRKaj7fRfsf4_u2L6GTc_j6mg&s"
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src=""
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                        <img src=""
                            class="min-w-[100px] h-[70px] sm:min-w-[140px] sm:h-[90px] md:min-w-[180px] md:h-[110px] lg:min-w-[220px] lg:h-[140px] bg-white rounded-md flex-shrink-0 transition-transform duration-300 hover:scale-105"></img>
                    </div>

                    <!-- Button Next -->
                    <button
                        id="scrollNext"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-white rounded-full shadow-sm p-2 hover:bg-gray-100 transition">
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

                        <?php foreach ($regularProducts as $p): ?>
                            <li
                                class="group bg-white border border-gray-200 rounded-lg shadow-sm transition transform hover:scale-105 hover:shadow-lg flex flex-col h-full duration-300">
                                <div class="relative">
                                    <img
                                        src="<?= $p->image ?>"
                                        alt="<?= htmlspecialchars($p->name) ?>"
                                        class="w-full h-auto aspect-[4/3] object-cover rounded-t-lg" />
                                </div>

                                <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
                                    <div class="flex-grow">
                                        <p class="text-gray-700 text-sm sm:text-base">
                                            <?= $p->price ?>
                                            <span class="text-gray-400 line-through text-xs sm:text-sm"><?= $p->oldPrice ?></span>
                                        </p>

                                        <h3 class="mt-1.5 text-base sm:text-lg font-medium text-gray-900">
                                            <?= $p->name ?>
                                        </h3>

                                        <p class="mt-1 text-gray-600 text-sm sm:text-base line-clamp-3">
                                            <?= $p->description ?>
                                        </p>
                                    </div>

                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <button
                                            class="w-full rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 transition hover:scale-105 hover:bg-gray-200">
                                            Add to Cart
                                        </button>

                                        <button
                                            type="button"
                                            class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition hover:scale-105 hover:bg-[#A14646]/75">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="flex justify-center mt-6">
                        <a href="/./view/users/productCollection.php"
                            class="p-2 px-5 mt-5 bg-primary text-sm rounded-lg text-white font-semibold hover:bg-[#A14646]/75 cursor-pointer transition-all duration-200">
                            Lihat Semua
                        </a>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <!-- swiper js masih pake cdn  -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="/assets/js/users/dashboard.js"></script>
</body>

</html>