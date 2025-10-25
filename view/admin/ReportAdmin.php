<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="/src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<!-- NOTE!!! perlu diingat bahwa semuanya belum ada javascriptnya jadi belum interaktif dan responsive
-->

<body class="bg-no-repeat h-screen flex">

    
    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../template/sidebarAdmin.php' ;?>
    </aside>

    <!-- Rightside:-->
    <div class="flex-1 flex flex-col overflow-y-auto min-w-0">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../template/NavbarAdmin.php' ;?>
        </header>

        <!-- Main Contet -->
        <main class="p-6">
            <div class="mb-4">
                <h1 class="text-2xl font-bold"> Reports </h1>
                <p class="text-gray-400">Buat dan Unduh laporan hari ini.</p>
            </div>
            
            <!-- Card pilih waktu dan format file -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 mb-4 justify-center">
                <div id="container-header" class="mb-4">
                    <h2 class="text-2xl font-semibold">Grafik pendapatan Bulanan</h2>
                    <p class="text-gray-400">Pantau pendapatan setiap bulannya</p>
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
            <div class="flex gap-4 mb-4">
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4 transform ">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-200  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2854C5"><path d="M320-414v-306h120v306l-60-56-60 56Zm200 60v-526h120v406L520-354ZM120-216v-344h120v224L120-216Zm0 98 258-258 142 122 224-224h-64v-80h200v200h-80v-64L524-146 382-268 232-118H120Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Penjualan</h2>
                            <p class="text-gray-400 text-sm">Detail mengenai data penjualan termasuk pendapatan, pembelian dan kategori yang sedang tren </p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>

                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-rose-100  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#8C1AF6"><path d="M221-120q-27 0-48-16.5T144-179L42-549q-5-19 6.5-35T80-600h190l176-262q5-8 14-13t19-5q10 0 19 5t14 13l176 262h192q20 0 31.5 16t6.5 35L816-179q-8 26-29 42.5T739-120H221Zm-1-80h520l88-320H132l88 320Zm260-80q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM367-600h225L479-768 367-600Zm113 240Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Pembelian Barang </h2>
                            <p class="text-gray-400 text-sm">Detail lengkap penjualan dengan status dan detail lain</p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-200  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2854C5"><path d="M320-414v-306h120v306l-60-56-60 56Zm200 60v-526h120v406L520-354ZM120-216v-344h120v224L120-216Zm0 98 258-258 142 122 224-224h-64v-80h200v200h-80v-64L524-146 382-268 232-118H120Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Penjualan</h2>
                            <p class="text-gray-400 text-sm">Detailed sales data including revenue, orders, and trends</p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>

                <div class="rounded-lg border border-gray-200 w-full bg-white shadow-md p-4">
                    <div class="flex gap-2 items-center mb-4">
                        <div class="p-2 rounded-lg bg-blue-200  ">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#2854C5"><path d="M320-414v-306h120v306l-60-56-60 56Zm200 60v-526h120v406L520-354ZM120-216v-344h120v224L120-216Zm0 98 258-258 142 122 224-224h-64v-80h200v200h-80v-64L524-146 382-268 232-118H120Z"/></svg> <!--Buat iconnya-->
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Laporan Penjualan</h2>
                            <p class="text-gray-400 text-sm">Detailed sales data including revenue, orders, and trends</p>
                        </div>
                    </div>
                    <button class="text-base hover:bg-gray-600 cursor-pointer bg-gray-800 text-white p-2 w-full rounded-lg"> Download </button>
                </div>
            </div>

        </main>
    </div>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/src/js/main.js"></script>
</body>
</html>