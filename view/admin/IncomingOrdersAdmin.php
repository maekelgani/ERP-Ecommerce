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
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Incoming Orders </h1>
                    <p class="text-gray-400">Kelola order yang masuk dan ubah status pengiriman</p>
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
                            <option value="laptop">Laptop</option>
                            <option value="aksesoris">Aksesoris</option>
                            <option value="komponen">Komponen</option>
                            <option value="peripheral">Peripheral</option>
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
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Items</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Total</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Tanggal-Pesan</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Status Pembayaran</th>
                                    <th class="h-12 px-4 text-center align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Actions</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3">#ORD69</td>
                                    <td class="p-3">Juan Kamil</td>
                                    <td class="p-3">2</td>
                                    <td class="p-3">Rp. 24,425,000</td>
                                    <td class="p-3">23-10-2025</td>
                                    <td class="p-3">
                                        <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200 hidden">Sudah Membayar</div>
                                        <div class="px-2 text-gray-500 border border-gray-400 rounded-lg bg-gray-100">Belum Membayar</div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <select name="" id="" class="bg-gray-50 rounded-lg px-5 border border-gray-300">
                                            <option value="dikemas" class="">Dikemas</option>
                                            <option value="dikirim">Dikirim</option>
                                        </select>
                                    </td>
                                    <td class="p-3">Tugaskan Kurir</td>
                                    <td class="p-3">
                                        <button class="px-2 font-semibold cursor-pointer text-blue-900 hover:brightness-100">Lihat Detail</button>
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