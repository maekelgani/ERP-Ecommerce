<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/src/output.css">
    <!-- ICON jangan lupa cuy -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="h-screen flex">
    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include './template/sidebarAdmin.php' ;?>
    </aside>

    <!-- Rightside -->
    <div class="flex-1 flex flex-col overflow-y-auto"> 
        <!-- navbar -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include './template/NavbarAdmin.php';?>
        </header>

        <!-- Main -->
        <main class="flex flex-1 p-2">
            <?php include './admin/DashboardAdmin.php';?>
        </main>
    </div>


    <script src="/src/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/src/js/chart.js"></script>
</body>
</html>