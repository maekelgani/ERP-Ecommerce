<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
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
    <div class="flex-1 flex flex-col overflow-y-auto min-w-0">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php' ;?>
        </header>

        <!-- Main Contet -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="mb-4">
                <h1 class="text-3xl font-bold"> Reports </h1>
                <p class="text-gray-400">Buat dan Unduh laporan hari ini.</p>
            </div>
            
            <!-- Card pilih waktu dan format file -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 mb-4 justify-center">
                <div id="container-header" class="mb-4">
                    <h2 class="text-2xl font-semibold">Generate Report</h2>
                    <p class="text-gray-400">Select time period and download your reports</p>
                </div>
                <div class="flex gap-4">
                    <div class="w-[50%]">
                        <select class="border bg-gray-100 w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Hari Ini</option>
                            <option value="">Minggu ini</option>
                            <option value="">Bulan ini</option>
                            <option value="">Bulan Lalu</option>
                            <option value="">6 Bulan terakhir</option>
                            <option value="">Tahun ini</option>
                        </select>
                    </div>    
                    <div class="w-[50%]">
                        <select class="border bg-gray-100 w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">PDF</option>
                            <option value="">Excel</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- DOWNLOAD LAPORAN -->
            <!-- Laporan Penjualan -->
            <div class="flex gap-4 mb-4">
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4 transform ">
                    <div class="flex gap-2 items-center mb-4 ">
                        <div class="p-2 rounded-lg bg-blue-200 ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2854C5"><path d="M320-414v-306h120v306l-60-56-60 56Zm200 60v-526h120v406L520-354ZM120-216v-344h120v224L120-216Zm0 98 258-258 142 122 224-224h-64v-80h200v200h-80v-64L524-146 382-268 232-118H120Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Penjualan</h2>
                            <p class="text-gray-400 text-sm">Detail mengenai data penjualan termasuk pendapatan, pembelian dan kategori yang sedang tren </p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>

                <!-- Laporan Pembelian -->
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-purple-100  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#8C1AF6"><path d="M221-120q-27 0-48-16.5T144-179L42-549q-5-19 6.5-35T80-600h190l176-262q5-8 14-13t19-5q10 0 19 5t14 13l176 262h192q20 0 31.5 16t6.5 35L816-179q-8 26-29 42.5T739-120H221Zm-1-80h520l88-320H132l88 320Zm260-80q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM367-600h225L479-768 367-600Zm113 240Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Pembelian</h2>
                            <p class="text-gray-400 text-sm">Detail lengkap penjualan dengan status dan detail lain</p>
                        </div>
                    </div>
                    <button class=" text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>
            </div>

            <!-- Laporan Pelanggan -->
            <div class="flex gap-4">
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-amber-50  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F19E39"><path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113ZM120-240h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0 320Zm0-400Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Pelanggan</h2>
                            <p class="text-gray-400 text-sm">Detail mengenai data customer termasuk pendaftaran dan riwayat pembelian</p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>

                <!-- Laporan Persediaan -->
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-green-100  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#48752C"><path d="M440-183v-274L200-596v274l240 139Zm80 0 240-139v-274L520-457v274Zm-80 92L160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v318q0 22-10.5 40T800-252L520-91q-19 11-40 11t-40-11Zm200-528 77-44-237-137-78 45 238 136Zm-160 93 78-45-237-137-78 45 237 137Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Persediaan</h2>
                            <p class="text-gray-400 text-sm">Detail Stok produk, status ketersediaan dan pergerakan stok</p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>
            </div>

        </main>
    </div>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>