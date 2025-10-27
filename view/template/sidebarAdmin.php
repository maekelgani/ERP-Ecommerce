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
                    <img class="h-6 w-6 text-sidebar-primary bg-amber-50 rounded-4xl" src="/public/assets/img/favicon.png" alt="">
                    <span class="font-bold text-sm text-white">NANO KOMPUTER</span>    
                </div>
            </div>
            
            <!-- NAVIGATION MENU -->
            <!-- MENU DASHBOARD -->
            <nav id="sidebarMenu" class="flex-1 overflow-y-auto p-4 space-y-1">
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/DashboardAdmin.php">
                    <!-- GAMBAR LOGO DASHBOARD -->
                    <span class="material-symbols-outlined">home</span> <!-- icon by google brok-->
                    <span>Dashboard</span>
                </a>

                <!-- PRODUCTS SECTION -->
                <div class="dropdown-section">
                    <button class="toggle-btn flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all 
                    text-sidebar-foreground hover:bg-gray-100 hover:text-gray-800">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">inventory_2</span>
                            <span>Products</span>
                        </div>
                        <span class="arrow-icon material-symbols-outlined transition-transform duration-200">expand_more</span>
                    </button>

                    <div class="dropdown-menu ml-8 mt-1 space-y-1 hidden">
                        <a href="/view/admin/ProductAdmin.php" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800">
                            <span class="material-symbols-outlined">box_add</span> Product List
                        </a>
                        <a href="/view/admin/CategoryAdmin.php" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800">
                            <span class="material-symbols-outlined">category</span> Categories
                        </a>
                        <a href="/view/admin/DiscountAdmin.php" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800">
                            <span class="material-symbols-outlined">loyalty</span> Discount
                        </a>
                    </div>
                </div>

                <!-- ORDERS SECTION -->
                <div class="dropdown-section">
                    <button class="toggle-btn flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all 
                    text-sidebar-foreground hover:bg-gray-100 hover:text-gray-800">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined">orders</span>
                            <span>Orders</span>
                        </div>
                        <span class="arrow-icon material-symbols-outlined transition-transform duration-200">expand_more</span>
                    </button>

                    <div class="dropdown-menu ml-8 mt-1 space-y-1 hidden">
                        <a href="/view/admin/IncomingOrdersAdmin.php" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800">
                            <span class="material-symbols-outlined">draft_orders</span> Incoming Orders
                        </a>
                        <a href="/view/admin/OrderAdmin.php" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 hover:text-gray-800">
                            <span class="material-symbols-outlined">order_approve</span> All Orders
                        </a>
                    </div>
                </div>

                <!-- PENGEMBALIAN SECTION -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/ReturnAdmin.php">
                    <!-- GAMBAR LOGO PENGEMBALIAN NANTI -->
                    <span class="material-symbols-outlined">forms_apps_script</span> <!-- icon by google brok-->
                    <span>Retrun Product</span>
                </a>

                <!-- HALAMAN HAK AKSES -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/HakAksesAdmin.php">
                    <!-- GAMBAR LOGO NANTI -->
                    <span class="material-symbols-outlined">add_moderator</span> <!-- icon by google brok-->
                    <span>Kelola Akses</span>
                </a>

                <!-- HALAMAN WEB MANAGEMENT -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/WebManagement.php">
                    <!-- GAMBAR LOGO NANTI -->
                    <span class="material-symbols-outlined">construction</span> <!-- icon by google brok-->
                    <span>Web Management</span>
                </a>

                <!-- REPORT SECTION -->
                <a class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sidebar-foreground 
                hover:bg-gray-100 hover:text-gray-800" href="/view/admin/ReportAdmin.php">
                    <!-- GAMBAR LOGO REPORT NANTI -->
                    <span class="material-symbols-outlined">picture_as_pdf</span> <!-- icon by google brok-->
                    <span>Report</span>
                </a>
            </nav>
        </div>
    </aside>
</body>
<script src="/public/assets/js/main.js"></script>
</html>