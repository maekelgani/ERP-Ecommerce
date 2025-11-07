<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="stylesheet" href="/src/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarGuest.php' ;?>
    </header>
    <main class="max-w-full">
        <div class="border-b border-gray-200 bg-gray-100 px-4 py-2 text-gray-900">
            <p class="text-center font-medium">
                Lorem, ipsum dolor
                <a href="#" class="inline-block underline"> sit amet consectetur </a>
            </p>
        </div>

        <!-- banner promosi slider -->
        <section class="w-full h-[50vh] md:h-[60vh] lg:h-[70vh]">
            <div class="swiper banner-swiper w-full h-full">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#ADADAD">
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#f0f0f0">
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide bg-center bg-cover flex items-center justify-center text-red-900 text-3xl font-semibold"
                        style="background-color:#d4d4d4">
                    </div>
                </div>

                <!-- Navigasi dan Pagination -->
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
            </section>

        <section class="mt-4 p-10">
            <div class="bg-gray-300 p-10 pt-4 rounded-sm shadow-sm">
                <h2 class="font-bold text-center text-2xl">Category</h2>
                <div class="grid grid-cols-6 grid-row-2">
                    <!-- Cat 1 -->
                    
                </div>
            </div>
        </section>
    </main>

<!-- swiper js masih pake cdn  -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/assets/js/users/dashboard.js"></script>
</body>
</html>