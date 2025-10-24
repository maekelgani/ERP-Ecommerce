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
                    <h1 class="text-2xl font-bold"> Incoming Orders </h1>
                    <p class="text-gray-400">Kelola order yang masuk dan ubah status pengiriman</p>
                </div>
            </div>

            <!-- container TABEL orders -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Order List</h2>
                    <p class="text-base text-gray-400">Daftar order yang sedang dalam proses pengemasan</p>
                </div>

                <!-- buat filter -->
                <div id="filter-field" class="mb-4 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                    <!-- Filter cari produk -->
                    <input type="text" placeholder="cari order id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Amount</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Items</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Payment</th>
                                    <th class="h-12 px-4 text-center align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3">ORD69</td>
                                    <td class="p-3">Juan Kamil</td>
                                    <td class="p-3">2</td>
                                    <td class="p-3">Monitor ZOWIE xl2546x, CPU i9 14th 14900k</td>
                                    <td class="p-3">Payment</td>
                                    <td class="p-3">
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-full rounded-lg hidden">Selesai</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-orange-300 w-full rounded-lg ">Pengiriman</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-full rounded-lg hidden">Batal/dikembalikan</div> <!--Buat Status nya-->
                                    </td>
                                    <td class="p-3">Edit | Hapus</td>
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