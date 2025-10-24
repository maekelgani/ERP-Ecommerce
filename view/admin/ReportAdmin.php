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
            <h1 class="text-2xl font-bold"> Reports </h1>
            <p class="text-gray-400">Buat dan Unduh laporan hari ini.</p>
        </div>
        
        <!-- Stsats Card -->
        <div id="card-Generate-Reports" class="grid gap-4 grid-cols-2 mb-4">
            <!-- Card 1. pendapatan bulanan -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                <div id="card-header" class="mb-4">
                    <h2 class="font-semibold text-lg text-gray-800">Generate Reports</h2>
                    <p class="text-sm text-gray-400">Pilih periode waktu dan format laporan</p>
                </div>
                <div id="card-content" class="flex gap-4">
                    <!-- Periode waktu -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="hari-ini">Hari Ini</option>
                            <option value="minggu-ini">Minggu Ini</option>
                            <option value="bulan-ini">Bulan ini</option>
                            <option value="bulan-lalu">Bulan lalu</option>
                            <option value="nam-bulan">6 Bulan terakhir</option>
                            <option value="tahun-ini">Tahun ini</option>
                        </select>
                    </div>
                    <!-- Format File -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Brang</option>
                            <option value="Msi">Msi</option>
                            <option value="NVIDIA">NVIDIA</option>
                            <option value="Intel">Intel</option>
                            <option value="AMD">AMD</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- DOWNLOAD LAPORAN -->
            <div  >

            </div>

            <!--  Tabel Laporan terbaru -->
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
        </div>
        </main>
    </div>

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/src/js/main.js"></script>
</body>
</html>