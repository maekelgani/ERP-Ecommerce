<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Customer</title>
    <link rel="stylesheet" href="/../../src/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarGuest.php' ;?>
    </header>
    <main class="max-w-full mb-10">
        <div class="border-b border-gray-200 bg-gray-100 px-4 py-2 text-gray-900">
            <p class="text-center font-medium">
                Lorem, ipsum dolor
                <a href="#" class="inline-block underline"> sit amet consectetur </a>
            </p>
        </div>
        
        <!-- Navigasi breadcrumb / PATH -->
        <section class="mt-5 px-6 md:px-8 lg:px-20 w-full flex justify-center"> 
            <div class="w-full md:px-10">
            <nav aria-label="Breadcrumb">   
                <ol class="flex items-center gap-1 text-sm text-gray-700">
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> Home </a>
                    </li>

                    <!-- Path 1 -->
                    <li class="rtl:rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.247 6-16.5"></path>
                    </svg>
                    </li>

                    <!-- Path 2 -->
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> Collection </a>
                    </li>

                    <li class="rtl:rotate-180">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.247 6-16.5"></path>
                    </svg>
                    </li>

                    <!-- Path 3 (jika ada) -->
                    <li>
                    <a href="#" class="block transition-colors hover:text-gray-900"> ... </a>
                    </li>
                </ol>
            </nav>
            </div>
        </section>

        <!-- Image gallery -->
        <section class="max-w-6xl mx-auto px-4 py-8 border border-dashed border-gray-300 rounded-lg">
            <div class="grid gap-4">
                <!-- Gambar besar -->
                <div class="rounded-lg overflow-hidden">
                <img 
                    src="https://images.unsplash.com/photo-1624701928517-44c8ac49d93c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170" 
                    alt="Gambar Utama" 
                    class="w-full h-80 md:h-96 object-cover"
                >
                </div>

                <!-- 4 gambar kecil di bawah -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-lg overflow-hidden">
                    <img 
                    src="https://images.unsplash.com/photo-1624701928517-44c8ac49d93c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170" 
                    alt="Gambar 2" 
                    class="w-full h-40 object-cover"
                    >
                </div>
                <div class="rounded-lg overflow-hidden">
                    <img 
                    src="https://images.unsplash.com/photo-1624701928517-44c8ac49d93c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170" 
                    alt="Gambar 3" 
                    class="w-full h-40 object-cover"
                    >
                </div>
                <div class="rounded-lg overflow-hidden">
                    <img 
                    src="https://images.unsplash.com/photo-1624701928517-44c8ac49d93c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170" 
                    alt="Gambar 4" 
                    class="w-full h-40 object-cover"
                    >
                </div>
                <div class="rounded-lg overflow-hidden">
                    <img 
                    src="https://images.unsplash.com/photo-1624701928517-44c8ac49d93c?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170" 
                    alt="Gambar 5" 
                    class="w-full h-40 object-cover"
                    >
                </div>
                </div>
            </div>
        </section>

        
        <!-- Product info -->
        <section class="w-full mt-5 px-6 md:px-8 lg:px-20 sm:px-6 lg:grid lg:grid-cols-3 lg:grid-rows-[auto_auto_1fr] lg:gap-x-8 lg:pt-16 lg:pb-24">
            <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                <h1 id="" 
                class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">ASUS ROG Strix GeForce RTX 3070 Ti OC Edition 8GB GDDR6X</h1> <!--NAMA PRODUCT DI SINI CUY-->
            </div>

                <!-- Options -->
                <div class="mt-4 lg:row-span-3 lg:mt-0">
                    <h2 class="sr-only">Product information</h2>
                    <p class="text-3xl tracking-tight text-gray-900">$192</p>

                    <!-- Reviews  BINTANG -->
                    <div class="mt-6">
                    <h3 class="sr-only">Reviews</h3>
                    <div class="flex items-center">
                        <div class="flex items-center">
                        <!-- Active: "text-gray-900", Default: "text-gray-200" -->
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0 text-yellow-500">
                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0 text-yellow-500">
                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0 text-yellow-500">
                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0 text-yellow-500">
                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 shrink-0 text-gray-200">
                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        </div>
                        <p class="sr-only">4 out of 5 stars</p>
                        <a href="#" class="ml-3 text-sm font-medium text-red-nano hover:text-red-nano/50">117 reviews</a>
                    </div>
                    </div>

                    <form class="mt-10">
                    <!-- Colors -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Color</h3>

                        <fieldset aria-label="Choose a color" class="mt-4">
                        <div class="flex items-center gap-x-3">
                            <div class="flex rounded-full outline -outline-offset-1 outline-black/10">
                            <input type="radio" name="color" value="white" checked aria-label="White" class="size-8 appearance-none rounded-full bg-white forced-color-adjust-none checked:outline-2 checked:outline-offset-2 checked:outline-gray-400 focus-visible:outline-3 focus-visible:outline-offset-3" />
                            </div>
                            <div class="flex rounded-full outline -outline-offset-1 outline-black/10">
                            <input type="radio" name="color" value="gray" aria-label="Gray" class="size-8 appearance-none rounded-full bg-gray-200 forced-color-adjust-none checked:outline-2 checked:outline-offset-2 checked:outline-gray-400 focus-visible:outline-3 focus-visible:outline-offset-3" />
                            </div>
                            <div class="flex rounded-full outline -outline-offset-1 outline-black/10">
                            <input type="radio" name="color" value="black" aria-label="Black" class="size-8 appearance-none rounded-full bg-gray-900 forced-color-adjust-none checked:outline-2 checked:outline-offset-2 checked:outline-gray-900 focus-visible:outline-3 focus-visible:outline-offset-3" />
                            </div>
                        </div>
                        </fieldset>
                    </div>

                    <!-- TYPE -->
                    <div class="mt-10">
                        <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-900">TYPE</h3>
                        </div>

                        <fieldset aria-label="Choose a size" class="mt-4">
                        <div class="grid grid-cols-4 gap-3">
                            <label aria-label="XXS" class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-red-nano has-checked:bg-red-nano has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-red-nano has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                            <input type="radio" name="size" disabled class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                            <span class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">XXS</span>
                            </label>
                            <label aria-label="XS" class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-red-nano has-checked:bg-red-nano has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-red-nano has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                            <input type="radio" name="size" class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                            <span class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">XS</span>
                            </label>
                            <label aria-label="S" class="group relative flex items-center justify-center rounded-md border border-gray-300 bg-white p-3 has-checked:border-red-nano has-checked:bg-red-nano has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-red-nano has-disabled:border-gray-400 has-disabled:bg-gray-200 has-disabled:opacity-25">
                            <input type="radio" name="size" checked class="absolute inset-0 appearance-none focus:outline-none disabled:cursor-not-allowed" />
                            <span class="text-sm font-medium text-gray-900 uppercase group-has-checked:text-white">S</span>
                            </label>

                        </div>
                        </fieldset>
                    </div>

                    <button type="submit" class="mt-10 flex w-full items-center justify-center rounded-md border border-transparent bg-red-nano px-8 py-3 text-base font-medium text-white hover:bg-red-nano/75 focus:ring-2 focus:ring-red-nano focus:ring-offset-2 focus:outline-hidden transition duration-200">Add to bag</button>
                    </form>
                </div>

                <div class="py-10 lg:col-span-2 lg:col-start-1 lg:border-r lg:border-gray-200 lg:pt-6 lg:pr-8 lg:pb-16">
                    <!-- Description and details -->
                    <div>
                    <h3 class="text-sm font-medium text-gray-900">Description</h3>

                    <div class="space-y-6">
                        <p class="text-base text-gray-900">The Basic Tee 6-Pack allows you to fully express your vibrant personality with three grayscale options. Feeling adventurous? Put on a heather gray tee. Want to be a trendsetter? Try our exclusive colorway: &quot;Black&quot;. Need to add an extra pop of color to your outfit? Our white tee has you covered.</p>
                    </div>
                    </div>

                    <div class="mt-10">
                    <h3 class="text-sm font-medium text-gray-900">Spesifikasi</h3>

                    <div class="mt-4">
                        <ul role="list" class="list-disc space-y-2 pl-4 text-sm">
                        <li class="text-gray-400"><span class="text-gray-600">Hand cut and sewn locally</span></li>
                        <li class="text-gray-400"><span class="text-gray-600">Dyed with our proprietary colors</span></li>
                        <li class="text-gray-400"><span class="text-gray-600">Pre-washed &amp; pre-shrunk</span></li>
                        <li class="text-gray-400"><span class="text-gray-600">Ultra-soft 100% cotton</span></li>
                        </ul>
                    </div>
                    </div>

                    <div class="mt-10">
                    <h2 class="text-sm font-medium text-gray-900">Details</h2>

                    <div class="mt-4 space-y-6">
                        <p class="text-sm text-gray-600">The 6-Pack includes two black, two white, and two heather gray Basic Tees. Sign up for our subscription service and be the first to get new, exciting colors, like our upcoming &quot;Charcoal Gray&quot; limited release.</p>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>

</body>
</html>