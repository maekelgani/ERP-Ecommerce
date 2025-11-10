<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns - Admin</title>
    <link rel="stylesheet" href="/../../src/output.css">
</head>
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

        <!-- main kontent -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Return Management </h1>
                    <p class="text-gray-400">Kelola pengembalian produk dari pelanggan</p>
                </div>
            </div>

            <!-- container TABEL Pengembalian -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="" class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Return Requests</h2>
                        <p class="text-base text-gray-400">Daftar permintaan pengembalian produk</p>
                    </div>
                    <div id="filter-field" class="mb-2 flex flex-wrap items-center gap-4 pb-3 pt-0">
                        <input type="text" placeholder="Cari Pengembalian..." class="border border-gray-300 rounded-lg px-3 w-sm py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            <div class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <select name="" id="">
                                <option value="">Pilih Status</option>
                                <option value="">Diterima</option>
                                <option value="">Menunggu</option>
                                <option value="">Ditolak</option>
                            </select>
                        </div>
                        <button class="bg-gray-800 text-white px-6 py-2 rounded-lg">Cari</button>
                    </div>
                </div>

                <!-- BAGIAN TABEL Pengembalian GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">ID Pengembalian</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">ID Pemesanan</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Pelanggan</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Keluhan</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Tanggan Pengajuan</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600 w-1">Jumlah</th>
                                    <th class="h-12 px-3 text-center align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3 text-xs text-gray-500"># RTN01</td>
                                    <td class="p-3"># ORD24</td>
                                    <td class="p-3">Jojon Satoru</td>
                                    <td class="p-3 text-sm text-gray-400">Ada yang ga ada</td>
                                    <td class="p-3">23-10-2025</td>
                                    <td class="p-3">1</td>
                                    <td class="p-3 text-center">
                                        <div class="text-amber-600 rounded-lg bg-amber-50 border border-amber-600">Pending</div>
                                        <div class="text-green-700 rounded-lg bg-green-100 border border-green-700 hidden">Diterima</div>
                                        <div class="text-red-800 rounded-lg bg-red-200 border border-red-800 hidden">Ditolak</div>
                                    </td>
                                    <!-- Acction terima dan ga terima Permintaan pengajuan -->
                                    <td class="p-3 flex gap-2">
                                        <div class="text-red-800 cursor-pointer">
                                            <span class="material-symbols-outlined">close</span>
                                        </div>
                                        <div class="text-green-700 cursor-pointer">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="text-blue-800 cursor-pointer font-semibold">Lihat Detail</button>
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