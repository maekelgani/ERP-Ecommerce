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
            <div class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold"> Kelola Akses </h1>
                    <p class="text-gray-400">Kelola peran dan hak akses ERP</p>
                </div>
                <button  class="openUsers px-5 py-2 bg-gray-800 text-white rounded-lg cursor-pointer transition duration-200 hover:bg-gray-600">
                    Tambah produk
                </button>
            </div>
        
        <!-- Stsats Card -->
            <div id="card" class="grid gap-4 grid-cols-4 mb-4">
                <!-- Card 1.Admin -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3 flex gap-3 items-center">
                        <span class="material-symbols-outlined">shield</span> <!--Icon wak-->
                        <div>
                            <h2 class="text-base font-semibold">Admin</h2>
                            <p class="text-sm text-gray-400 ">Jumlah Users: 1</p>
                        </div>
                    </div>
                    <p class="text-sm pl-9 text-blue-800"> Full Access</p>
                </div>

                <!-- Card 2. Manager -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3 flex gap-3 items-center">
                        <span class="material-symbols-outlined">shield</span> <!--Icon wak-->
                        <div>
                            <h2 class="text-base font-semibold">Manager</h2>
                            <p class="text-sm text-gray-400 ">Jumlah Users: 2</p>
                        </div>
                    </div>
                    <p class="text-sm pl-9 text-green-600">Limited Access</p>
                </div>

                
                <!-- Card 3. Staff -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3 flex gap-3 items-center">
                        <span class="material-symbols-outlined">shield</span> <!--Icon wak-->
                        <div>
                            <h2 class="text-base font-semibold">Staff</h2>
                            <p class="text-sm text-gray-400 ">Jumlah Users: 2</p>
                        </div>
                    </div>
                    <p class="text-sm pl-9 text-amber-400">Limited Access</p>
                </div>

                <!-- Card 4. ??? -->
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3 flex gap-3 items-center">
                        <span class="material-symbols-outlined">shield</span> <!--Icon wak-->
                        <div>
                            <h2 class="text-base font-semibold">Viewer</h2>
                            <p class="text-sm text-gray-400 ">Jumlah Users: 1</p>
                        </div>
                    </div>
                    <p class="text-sm pl-9 text-red-600">Read Only</p>
                </div>
            </div>
            
            <!--  Tabel Users  -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6 flex justify-between">
                    <h2 class="text-2xl font-bold">List Users</h2>
                    <!-- Filter cari USERS -->
                    <div id="filter-field" class="mb-2 flex flex-wrap items-center gap-4 pb-3 pt-0">
                        <input type="text" placeholder="Cari Users..." class="border border-gray-300 rounded-lg px-3 w-xl py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                        <button class="bg-gray-800 text-white px-6 py-2 rounded-lg">Cari</button>
                    </div>
                </div>
                <div id="container-content">
                    <div class="">
                        <div class="relative w-full overflow-auto">
                            <!-- TABEL NYA DI SINI -->
                            <table class="w-full caption-bottom text-sm">
                                <thead class="border-b uppercase">
                                    <tr class="border-b bg-gray-50">
                                        <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Id</th>
                                        <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama</th>
                                        <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Email</th>
                                        <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">No. Telepon</th>
                                        <th class="h-12 px-4 text-center align-center font-bold text-muted-foreground text-gray-600">Role</th>
                                        <th class="h-12 px-4 text-center align-center font-bold text-muted-foreground text-gray-600">Status</th>
                                        <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-t">
                                        <td class="p-3"># USR102</td>
                                        <td class="p-3">Jojo Joestar</td>
                                        <td class="p-3">joe.star@gmail.com</td>
                                        <td class="p-3">081234567890</td>
                                        <td class="p-3 w-1.5 text-center">
                                            <div class="">
                                                <div class="px-2 text-blue-800 border border-blue-500 rounded-lg bg-blue-200 ">Admin</div>
                                                <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200 hidden">Manager</div>
                                                <div class="px-2 text-yellow-800 border border-yellow-500 rounded-lg bg-amber-200 hidden">Staff</div>
                                                <div class="px-2 text-red-950 border border-red-500 rounded-lg bg-red-200 hidden">viewer</div>
                                            </div>
                                        </td>
                                        <td class="p-3 text-center w-2">
                                            <div class="">
                                                <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200">Active</div>
                                                <div class="px-2 text-gray-500 border border-gray-400 rounded-lg bg-gray-100 hidden ">Inactive</div>
                                            </div>
                                        </td>
                                        <td class="p-3 gap-2">
                                            <button class="editUsers cursor-pointer text-blue-500 font-semibold">Edit</button> |
                                            <button class="cursor-pointer text-red-500 font-semibold">Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal form tambah Hak Akses / Users -->
    <div id="form-users" class="fixed inset-0 z-50 flex items-center justify-center p-6 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>
            <div id="modal-card" class="relative z-10 max-w-lg w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center
                transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4"> 

            <div class="mb-4 flex justify-between"> <!--ini header-->
                <div>
                    <h2 class="text-lg font-semibold">Tambah Users</h2>
                    <p class="text-sm text-gray-400">Masukkan Detail data diri users</p>
                </div>
                <button class="cancel cursor-pointer">
                    <span class="material-symbols-outlined">close</span> <!--Close/Cancel Button-->
                </button>
            </div>

            <form action="">
                <!-- Imput ID Produk/ dibuat Otomatis paling -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">ID Users</label>
                    <input type="text" disabled
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-200 text-base focus:ring-blue-500 focus:border-blue-500 ">
                </div>

                <!-- Input nama Users -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">Nama</label>
                    <input type="text" required 
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Input Email Users -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">Email</label>
                    <input type="text" required 
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- No Telepon -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">No Telepon</label>
                    <input type="text" required 
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Jabatan / Posisi -->
                <div class="flex gap-2 mb-4">
                    <div class="w-[50%]">
                        <label for="" class="font-semibold text-sm">Role</label>
                        <select name="" id="" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                            <option value="" class="value:text-gray">Pilih Role</option>
                            <option value="">Admin</option>
                            <option value="">Manajer</option>
                            <option value="">Staff</option>
                            <option value="">Viewer</option>
                        </select>
                    </div>

                    <!-- Status Users -->
                    <div class="w-[50%]">
                        <label for="" class="font-semibold text-sm">Status</label>
                        <select name="" id="" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                            <option value="" class="value:text-gray">Pilih Status</option>
                            <option value="">Active</option>
                            <option value="">Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Tambah</button>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="/src/js/main.js"></script>
</body>
</html>