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
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Category </h1>
                    <p class="text-gray-400">Kelola berbagai kategori yang ada pada tokomu</p>
                </div>
                <button class="openCategory px-5 py-2 bg-gray-800 text-white rounded-lg cursor-pointer transition duration-200 hover:bg-gray-600">
                    Tambah Kategori
                </button>
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
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">ID kategori</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Kategori</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Deskripsi</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Jumlah Produk</th>
                                    <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3 text-xs text-gray-500"># CAT01</td> <!-- ID kategori -->
                                    <td class="p-3">PC Bundling</td> <!-- NAMA kategori -->
                                    <td class="p-3 text-gray-500">Kategori untuk pc jadi</td> <!-- Deskripsi kategori -->
                                    <td class="p-3 pl-10">
                                        <div class="text-center border w-15 rounded-md bg-gray-50 border-gray-200 text-shadow-gray-950">14</div>
                                    </td> <!-- jumlah produk di kategori -->
                                    <td class="p-3 gap-3">
                                        <div>
                                            <button class="editCategory cursor-pointer text-blue-500 font-semibold">Edit</button> |
                                            <button class="cursor-pointer text-red-500 font-semibold">Hapus</button>
                                        </div>
                                    </td>    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal FORM Tambah Category -->
    <div id="form-category" class="fixed inset-0 z-50 flex items-center justify-center p-6 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>
            <div id="modal-card" class="relative z-10 max-w-lg w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center
                opacity-0 scale-95 translate-y-4
                transition-all duration-300 ease-out">
            <div class="mb-4 flex justify-between"> <!--ini header-->
                <div>
                    <h2 class="text-lg font-semibold">Add Category</h2>
                    <p class="text-sm text-gray-400">Masukkan detail mengenai kategori ini</p>
                </div>
                <button class="cancel cursor-pointer">
                    <span class="material-symbols-outlined">close</span> <!--Close/Cancel Button-->
                </button>
            </div>
            <form action="">
                <!-- Imput ID Produk/ dibuat Otomatis paling -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">ID Kategori</label>
                    <input type="text" disabled
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-200 text-base focus:ring-blue-500 focus:border-blue-500 ">
                </div>

                <!-- Input nama produk -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">Nama Kategori</label>
                    <input type="text" required 
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">Deskripsi</label>
                    <textarea name="" id="" placeholder="Jelaskan mengenai kategori ini..." class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <button type="submit" class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Tambah</button>
            </form>
        </div>
    </div>
    <script src="/assets/js/admin/category.js" defer></script>
</body>
</html>