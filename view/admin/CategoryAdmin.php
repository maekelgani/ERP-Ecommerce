<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category - Admin</title>
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

        <!-- main content -->
        <main class="p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold"> Category </h1>
                    <p class="text-gray-400">Kelola berbagai kategori yang ada pada tokomu</p>
                </div>
                <div>
                    <a class="px-5 py-2 bg-gray-800 text-white rounded-lg" href=""> Add Category</a>
                </div>
            </div>

            <!-- container TABEL CATEGORY -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-2 flex justify-between">
                    <h2 class="text-2xl font-bold">Category List</h2>
                    <!-- buat filter -->
                    <div id="filter-field" class="mb-2 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                        <!-- Filter cari produk -->
                        <input type="text" placeholder="Cari Kategori..." class="border border-gray-300 rounded-lg px-3 w-xl py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                        <button class="bg-gray-800 text-white px-6 py-2 rounded-lg">Cari</button>
                    </div>
                </div>

                <!-- BAGIAN TABEL CATEGORY GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">ID kategori</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Kategori</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Deskripsi</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Jumlah Produk</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3"># CAT01</td> <!-- ID kategori -->
                                    <td class="p-3">PC Bundling</td> <!-- ID kategori -->
                                    <td class="p-3 text-gray-500">Kategori untuk pc jadi</td> <!-- ID kategori -->
                                    <td class="p-3 pl-10">
                                        <div class="text-center border w-15 rounded-md bg-gray-50 border-gray-200 text-shadow-gray-950">14</div>
                                    </td> <!-- ID kategori -->
                                    <td class="p-3">Edit | Hapus</td> <!-- ID kategori -->
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