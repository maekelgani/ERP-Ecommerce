<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
    <aside id="sidebar" class="left-0 top-0 h-screen sticky border-r border-sidebar-border transition-all duration-300 z-40 w-full bg-gray-800 text-white">
        <div class="flex flex-col h-full">
            <!-- PASANG LOGO -->
            <div class="flex items-center justify-between p-4.5 border-b">
                <div class="flex items-center gap-2">
                    <img class="h-6 w-6 text-sidebar-primary bg-amber-50 rounded-4xl" src="/src/img/favicon.png" alt="">
                    <span class="font-bold text-sm text-white">NANO KOMPUTER</span>    
                </div>
            </div>
            
            <!-- NAVIGATION MENU -->
            <!-- MENU DASHBOARD -->
            <nav id="sidebarMenu" class="flex-1 overflow-y-auto p-4 space-y-1">
                <a class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg transition-all text-sidebar-foreground" 
                href="/view/admin/DashboardAdmin.php">
                    <!-- GAMBAR LOGO DASHBOARD -->
                    <span class="material-symbols-outlined">home</span> <!-- icon by google brok-->
                    <span>Dashboard</span>
                </a>

                <!-- PRODUCT SECTION -->
                <div>
                    <button class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all 
                    text-sidebar-foreground hover:bg-gray-100 hover:text-gray-800">
                        <div class="flex items-center gap-3">
                            <!-- GAMAR LOGO PRODOCT -->
                            <span class="material-symbols-outlined">inventory_2</span> <!-- icon by google brok-->
                            <span>Products</span>
                        </div>
                    </button>
                    <!-- MENU DROPDOWN PRODUCTS -->
                    <div class="ml-8 mt-1 space-y-1">
                        <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800" 
                        href="/view/admin/ProductAdmin.php">
                            <span class="material-symbols-outlined">box_add</span> <!-- icon by google brok-->
                            Product List</a>
                        <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800" 
                        href="/view/admin/CategoryAdmin.php">
                            <span class="material-symbols-outlined">category</span> <!-- icon by google brok-->
                            Categories</a>
                        <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800" 
                        href="/view/admin/DiscountAdmin.php">
                            <span class="material-symbols-outlined">loyalty</span> <!-- icon by google brok-->
                            Discount</a>
                    </div>
                </div>

                <!-- ORDERS SECTION -->
                <div>
                    <button class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all 
                    text-sidebar-foreground hover:bg-gray-100 hover:text-gray-800">
                        <div class="flex items-center gap-3">
                            <!-- GAMAR LOGO ORDERS -->
                            <span class="material-symbols-outlined">orders</span> <!-- icon by google brok-->
                            <span>Orders</span>
                        </div>
                    </button>
                    <!-- MENU DROPDOWN ORDES -->
                    <div class="ml-8 mt-1 space-y-1">
                        <a class="menu-item flex items-center gap-3 px-3 py-2.5 hidden rounded-lg hover:bg-gray-100 hover:text-gray-800" 
                        href="/">
                            <span class="material-symbols-outlined">inactive_order</span> <!-- icon by google brok-->
                            Incoming Orders</a>
                        <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800" 
                        href="/view/admin/OrderAdmin.php">
                            <span class="material-symbols-outlined">order_approve</span> <!-- icon by google brok-->
                            All Orders</a>
                    </div>
                </div>

                <!-- PENGEMBALIAN SECTION -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/ReturnAdmin.php">
                    <!-- GAMBAR LOGO PENGEMBALIAN NANTI -->
                    <span class="material-symbols-outlined">forms_apps_script</span> <!-- icon by google brok-->
                    <span>Retrun Product</span>
                </a>

                <!-- REPORT SECTION -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="">
                    <!-- GAMBAR LOGO REPORT NANTI -->
                    <span class="material-symbols-outlined">picture_as_pdf</span> <!-- icon by google brok-->
                    <span>Report</span>
                </a>
            </nav>
        </div>
        
        <!-- Script untuk selected menu option -->
        <script>
        document.addEventListener('DOMContentLoaded', () => {
        const menuItems = document.querySelectorAll('.menu-item');

        // Ambil data "halaman aktif" dari localStorage
        const activePage = localStorage.getItem('activeMenu');

        // Jika ada menu aktif tersimpan, beri warna
        if (activePage) {
            menuItems.forEach(item => {
            if (item.getAttribute('href') === activePage) {
                item.classList.add('bg-gray-100');
                item.classList.add('text-gray-800')
            } else {
                item.classList.remove('bg-gray-100');
            }
            });
        }

        // Event: ketika menu diklik
        menuItems.forEach(item => {
            item.addEventListener('click', e => {
            // Simpan menu yang diklik ke localStorage
            localStorage.setItem('activeMenu', item.getAttribute('href'));

            // Ubah tampilan aktif secara langsung
            menuItems.forEach(link => link.classList.remove('bg-gray-800'));
            item.classList.add('bg-gray-800');
            });
        });
        });
        </script>

    </aside>
</body>
</html>