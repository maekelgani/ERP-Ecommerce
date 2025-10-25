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
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../template/NavbarAdmin.php' ;?>
        </header>

        <!-- Main Contet -->
        <main class="p-6">
        <div class="mb-4">
            <h1 class="text-2xl font-bold"> Dashboard </h1>
            <p class="text-gray-400">Selamat Datang kembali, bagaimana penjualan hari ini?</p>
        </div>
        
        <!-- Stsats Card -->
        <div id="card" class="grid gap-4 grid-cols-4 mb-4">
            <!-- Card 1. pendapatan bulanan -->
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

            <!-- Card 2. Jumlah Users -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                <div id="card-header" class="mb-3"">
                    <h2 class="text-base text-gray-600">Jumlah Users Terdaftar</h2>
                </div>
                <div id="card-content">
                    <p class="font-bold text-2xl">1092</p>
                    <p class="text-sm text-green-600 hidden"> User terdaftar naik dari {100} dari bulan lalu</p> <!--Kalo User terdaftar naik-->
                    <p class="text-sm text-red-600"> User terdaftar dari {100} dari bulan lalu</p> <!--Kalo User terdaftar turun-->
                </div>
            </div>

            
            <!-- Card 3. Jumlah Orders -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                <div id="card-header" class="mb-3"">
                    <h2 class="text-base text-gray-600">Total Orders</h2>
                </div>
                <div id="card-content">
                    <p class="font-bold text-2xl">12029</p>
                    <p class="text-sm text-green-600"> Pembelian naik dari bulan lalu</p> <!--Kalo User terdaftar naik-->
                    <p class="text-sm text-red-600 hidden"> Pembelian dari bulan lalu</p> <!--Kalo User terdaftar turun-->
                </div>
            </div>

            <!-- Card 4. ??? -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                <div id="card-header" class="mb-3"">
                    <h2 class="text-base text-gray-600"> Total ??</h2>
                </div>
                <div id="card-content">
                    <p class="font-bold text-2xl">99999</p>
                    <p class="text-sm text-green-600 hidden"> ??? naik dari bulan lalu</p> <!--Kalo User terdaftar naik-->
                    <p class="text-sm text-red-600"> ??? dari bulan lalu</p> <!--Kalo User terdaftar turun-->
                </div>
            </div>
        </div>

        <!-- MASIH DUMMY AJA GW GA TAU CARANYA -->
        <div class="grid grid-cols-4 gap-4 mb-4">
            <!-- Chart Pendaapatan -->
            <div id="chart-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 col-span-3 justify-center">
                <div id="container-header" class="">
                    <h2 class="text-2xl font-bold">Grafik pendapatan Bulanan</h2>
                    <p class="text-gray-400">Pantau pendapatan setiap bulannya</p>
                </div>
                <canvas id="graphCanvas" class="max-w-full max-h-150 justify-center p-4"></canvas>
            </div>

            <!-- Bar Stok tersisa-->
            <div id="chart-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-4">
                    <h2 class="text-lg font-bold">Stok Menipis</h2>
                    <p class="text-gray-400 text-sm">Jumlah Stok sudah semakin menipis, buruan isi</p>
                </div>
                <div id="container-content">
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-red-600 h-2 w-[10%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>10%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-red-500 h-2 w-[20%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>20%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-orange-500 h-2 w-[30%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>30%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-orange-400 h-2 w-[40%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>40%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-yellow-500 h-2 w-[50%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>50%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-yellow-400 h-2 w-[60%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>60%</strong> </p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-2xl">
                        <img src="https://blossomzones.com/wp-content/uploads/2021/08/5600G.jpg" class="w-10 rounded-full " alt="">
                        <div class="w-25 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-green-500 h-2 w-[70%]"></div>
                        </div>
                        <p class="text-[12px] mt-1">Stok tersisa: <strong>70%</strong> </p>
                    </div>
                    <a href="/view/admin/ProductAdmin.php">
                        <button class="bg-gray-800 text-white font-semibold text-base w-full py-1 rounded-lg hover:bg-gray-600 cursor-pointer"> Lihat Stok
                        </button>
                    </a>
                </div>
            </div>
        </div>
        
        <!--  Tabel pembelian terbaru -->
        <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
            <div id="container-header" class="mb-6">
                <h2 class="text-2xl font-bold">Pembelian Terbaru</h2>
                <p class="text-gray-400">Menampilkan 5 pembelian yang baru saja terjadi</p>
            </div>
            <div id="container-content">
                <div class="p-6 pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 ">
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">lorem</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">lorem</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">lorem</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">lorem</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">lorem</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3">Apa aja</td>
                                    <td class="p-3">Apa aja</td>
                                    <td class="p-3">Apa aja</td>
                                    <td class="p-3">Apa aja</td>
                                    <td class="p-3">Apa aja</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/src/js/main.js"></script>
</body>
</html>