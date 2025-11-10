<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik - Admin</title>
    <link rel="stylesheet" href="/../../src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<!-- NOTE!!! perlu diingat bahwa semuanya belum ada javascriptnya jadi belum interaktif dan responsive
-->

<body class="bg-no-repeat h-screen flex">

    
    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../../components/admin/sidebarAdmin.php' ;?>
    </aside>

    <!-- Rightside:-->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php' ;?>
        </header>

        <!-- Main Contet -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold"> Analitik </h1>
                    <p class="text-gray-400">Laporan komprehensif tentang performa website dan penjualan</p>
                </div>
                <div class="border p-2 px-5 border-gray-300 rounded-lg">
                    <select name="" id="">
                        <option value="">Hari ini</option>
                        <option value="">7 Hari terakhir</option>
                        <option value="">30 Hari Terakhir</option>
                        <option value="">90 Hari Terakhir</option>
                    </select>
                </div>
            </div>
            
            <!-- Stsats Card -->
            <div class="grid grid-row-1 grid-cols-4 gap-4 mb-4">
                <!-- Card-1 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Total Pendapatan</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">Rp. 124,250,000</p>
                    </div>
                </div>
                
                <!-- Card-2 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Rata-rata Harga Pembelian</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">Rp. 9,500,000</p>
                    </div>
                </div>

                <!-- Card-3 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Produk terjual</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">23</p>
                    </div>
                </div>

                <!-- Card-4 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Rata-rata Rating Produk</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">4.4 / 5</p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap mb-4">
            <nav class="bg-gray-100 rounded-lg p-1 font-semibold text-sm gap-4 flex">
                    <button class="tab-btn-analitik p-1 px-2 rounded-lg text-gray-400 cursor-pointer">Pendapatan</button>
                    <button class="tab-btn-analitik p-1 px-2 rounded-lg text-gray-400 cursor-pointer">Analisi Produk</button>
                </nav>
            </div>

<!-- Content analitik 1 -->
            <div class="tab-content-analitik grid grid-row-1 grid-cols-2 gap-4 mb-4">
                <!-- Card 1 -->
                <div class="cols-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                    <div class="p-2">
                        <div>
                            <div class="tab-content-header mb-4">
                                <h2 class="text-2xl font-semibold">Pendapatan Toko</h2>
                                <p class="text-gray-400">Grafik pendapatan bulanan selama 6 bulan terakhir</p>
                            </div>
                            <div class="tab-content-main">
                                <canvas id="chartPendapatan" class="w-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 1 -->
                <div class="cols-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                    <div class="p-2">
                        <div>
                            <div class="tab-content-header mb-4">
                                <h2 class="text-2xl font-semibold">Order dan Users</h2>
                                <p class="text-gray-400">Grafik perbandingan total order dan total users</p>
                            </div>
                            <div class="tab-content-main">
                                <canvas id="chartUserVsOrder" class="w-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
<!-- Content analitik 2 -->
            <div class="tab-content-analitik grid grid-row-1 grid-cols-2 gap-4 mb-4">
                <!-- Card 1 -->
                <div class="cols-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                    <div class="p-2">
                        <div>
                            <div class="tab-content-header mb-4">
                                <h2 class="text-2xl font-semibold">Kategori Terpopuler</h2>
                                <p class="text-gray-400">Grafik kategori yang paling populer</p>
                            </div>
                            <div class="tab-content-main">
                                <canvas id="chartCategorySales" class="w-full "></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 1 -->
                <div class="cols-span-1 w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                    <div class="p-2">
                        <div class="warp-tab-content">
                            <div class="tab-content-header mb-4">
                                <h2 class="text-2xl font-semibold">Produk Terpopuler</h2>
                                <p class="text-gray-400">Produk paling populer bulan ini</p>
                            </div>
                            <div class="tab-content-main">
                                <table class="gap-4  w-full">
                                    <thead class="border-b">
                                        <tr>
                                            <th class="px-3 text-left">Nama Produk</th>
                                            <th class="px-3 text-right">Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="px-3 bg-gray-50">
                                            <td>
                                                <div>
                                                    <h2 class="text-base">BenQ ZOWIE XL2540K 24.5 FHD TN 240Hz Gaming Monitor</h2>
                                                    <p class="text-sm text-gray-400">Total sales: 22</p>
                                                </div>
                                            </td>
                                            <td class="px-3 text-right font-semibold">Rp. 132,000,000</td>
                                        </tr>
                                        <tr class="px-3 bg-gray-50">
                                            <td>
                                                <div>
                                                    <h2 class="text-base">BenQ ZOWIE XL2540K 24.5 FHD TN 240Hz Gaming Monitor</h2>
                                                    <p class="text-sm text-gray-400">Total sales: 22</p>
                                                </div>
                                            </td>
                                            <td class="px-3 text-right font-semibold">Rp. 132,000,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>


    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/chart.js" defer></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>