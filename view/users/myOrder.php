<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order - Customer</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../../assets/img/Nanocomp.png">
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarUsers.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <div class="mb-8">
                <p class="text-4xl font-bold text-gray-900 mb-2">Pesanan Saya</p>
                <p class="text-gray-600">Kelola dan lacak semua pesanan Anda</p>
            </div>
            <!-- btn Section -->
            <div class="lg:flex-row mb-8 flex flex-col gap-6">
                <button type="submit" class="hover:bg-primary transition-all duration-300 hover:shadow-lg px-6
                    py-3 bg-primary text-white font-semibold rounded-lg shadow-md">Semua Pesanan</button>
                <button type="submit" class="hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Diproses</button>
                <button type="submit" class="hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Dikirim</button>
                <button type="submit" class="hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Selesai</button>
                <button type="submit" class="hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Dibatalkan</button>
            </div>

            
        </div>
    </main>
</body>