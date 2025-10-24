<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns - Admin</title>
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
                    <h1 class="text-2xl font-bold"> Return Management </h1>
                    <p class="text-gray-400">Kelola pengembalian produk dari pelanggan</p>
                </div>
            </div>

            <!-- container TABEL Pengembalian -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Return Requests</h2>
                    <p class="text-base text-gray-400">Daftar permintaan pengembalian produk</p>
                </div>

                <!-- BAGIAN TABEL Pengembalian GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Return ID</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Reason</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Amount</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3">Return ID</td>
                                    <td class="p-3">Order ID</td>
                                    <td class="p-3">Customer</td>
                                    <td class="p-3">Reason</td>
                                    <td class="p-3">Amount</td>
                                    <td class="p-3">Status</td>
                                    <td class="p-3">Actions</td>
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