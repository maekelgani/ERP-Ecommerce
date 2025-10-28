<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analitik - Admin</title>
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
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../template/NavbarAdmin.php' ;?>
        </header>

        <!-- Main Contet -->
        <main class="p-6">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold"> Analitik </h1>
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
            <div id="card" class="grid gap-4 grid-cols-4 mb-4">
                <!-- Card 1. -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Pendapatan Bulanan</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">Rp. 129,412,224</p>
                        <p class="text-sm text-green-600"> pendapatan naik dari {100} dari bulan lalu</p> <!--Kalo pendapatan naik-->
                        <p class="text-sm text-red-600 hidden"> pendapatan turun dari {100} dari bulan lalu</p> <!--Kalo pendapatan turun-->
                    </div>
                </div>

                <!-- Card 2. -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Rata-rata Harga penjualan</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">Rp 9,024,152</p>
                        <p class="text-sm text-green-600 hidden"> Order value meningkat {100} dari bulan lalu</p> <!--Kalo User terdaftar naik-->
                        <p class="text-sm text-red-600"> Order value menurun {100} dari bulan lalu</p> <!--Kalo User terdaftar turun-->
                    </div>
                </div>

                
                <!-- Card 3.-->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600"></h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">12029</p>
                        <p class="text-sm text-green-600"> Pembelian naik dari bulan lalu</p> <!--Kalo User terdaftar naik-->
                        <p class="text-sm text-red-600 hidden"> Pembelian dari bulan lalu</p> <!--Kalo User terdaftar turun-->
                    </div>
                </div>

                <!-- Card 4.  -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class="text-base text-gray-600">Rating Rata-rata Product</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">4/5</p>
                        Bintang
                    </div>
                </div>
            </div>


    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/src/js/main.js"></script>
</body>
</html>