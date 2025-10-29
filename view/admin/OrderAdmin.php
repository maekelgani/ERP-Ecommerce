<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
    <link rel="stylesheet" href="/src/output.css">
</head>
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
        <!-- main kontent -->
        <main class="p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Order List</h1>
                    <p class="text-gray-400">Detail Semua order yang telah terjadi</p>
                </div>
            </div>

            <!-- container TABEL orders -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Order List</h2>
                    <p class="text-base text-gray-400">Daftar order yang sedang dalam proses pengemasan</p>
                </div>

                <!-- Filter -->
                <div id="filter-field" class="mb-4 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                    <!-- Filter cari produk -->
                    <div class="flex items-center gap-2">
                        <input type="text" placeholder="Cari Product" class="border border-gray-300 rounded-lg px-3 w-md py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <!-- filter by category -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Status</option>
                            <option value="">Dikemas</option>
                            <option value="">Pengiriman</option>
                            <option value="">Selesai</option>
                        </select>
                    </div>
                    <!-- filter by Tanggal -->
                    <div>
                        <input type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <button class=" px-10 bg-gray-800 text-white py-2 rounded-md font-semibold text-sm hover:bg-gray-500 cursor-pointer transition"> 
                        Terapkan Filter 
                    </button>
                </div>

                <!-- BAGIAN TABEL orders GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Amount</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Items</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Payment Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Payment Method</th>
                                    <th class="h-12 px-3 text-center align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th> 
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3">ORD69</td>
                                    <td class="p-3">Juan Kamil</td>
                                    <td class="p-3">2</td>
                                    <td class="p-3">Monitor ZOWIE xl2546x, CPU i9 14th 14900k</td>
                                    <td class="p-3">
                                        <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200 ">Sudah Membayar</div>
                                        <div class="px-2 text-gray-500 border border-gray-400 rounded-lg bg-gray-100 hidden">Belum Membayar</div>
                                    </td>
                                    <td class="p-3">Bank Transfer</td>
                                    <td class="p-3">
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-full rounded-lg hidden">Selesai</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-amber-300 w-full rounded-lg ">Pengiriman</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-full rounded-lg hidden">Batal/dikembalikan</div> <!--Buat Status nya-->
                                    </td>
                                    <td class="p-3">
                                        <button class="px-2 font-semibold cursor-pointer text-blue-900 hover:brightness-100"> Lihat Detail</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>