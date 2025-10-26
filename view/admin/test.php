    <!-- =======================
    Contoh Tabel dengan Gambar
    Taruh di dalam <main> atau area kontenmu
    ======================= -->
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin</title>
    <link rel="stylesheet" href="/src/output.css">
</head>
<body>
    
    <!-- Tabel dengan gambar -->
    <div class="overflow-x-auto">
    <table class="min-w-full table-auto border-collapse">
        <thead>
        <tr class="text-left border-b">
            <th class="py-2 px-3">No</th>
            <th class="py-2 px-3">Nama</th>
            <th class="py-2 px-3">Foto</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b">
            <td class="py-3 px-3">1</td>
            <td class="py-3 px-3">Produk A</td>
            <td class="py-3 px-3">
            <img src="/path/to/imgA.jpg"
                alt="Produk A"
                class="w-20 h-20 object-cover rounded cursor-pointer transition-transform hover:scale-105 lightbox-trigger">
            </td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">2</td>
            <td class="py-3 px-3">Produk B</td>
            <td class="py-3 px-3">
            <img src="/path/to/imgB.jpg"
                alt="Produk B"
                class="w-20 h-20 object-cover rounded cursor-pointer transition-transform hover:scale-105 lightbox-trigger">
            </td>
        </tr>
        </tbody>
    </table>
    </div>


    <!-- Modal Lightbox -->
    <div id="lightbox" class="fixed inset-0 hidden z-50 items-center justify-center p-6">
    <div id="lb-backdrop" class="absolute inset-0 bg-black bg-opacity-60"></div>

    <div class="relative max-w-[95vw] max-h-[95vh] flex items-center justify-center">
        <button id="lb-close" class="absolute top-2 right-2 bg-white/90 rounded-full p-1 shadow hover:bg-white z-20">
        ✕
        </button>

        <img id="lb-image"
            src=""
            alt=""
            class="max-w-full max-h-[88vh] rounded-md shadow-lg opacity-0 transition-opacity duration-200" />
    </div>
    </div>


    <!-- =======================
    Script: pasang di bawah, setelah markup
    ======================= -->
        <script>
    (function () {
        const triggers = document.querySelectorAll('.lightbox-trigger');
        const lightbox = document.getElementById('lightbox');
        const lbImage = document.getElementById('lb-image');
        const lbClose = document.getElementById('lb-close');
        const lbBackdrop = document.getElementById('lb-backdrop');

        function openLightbox(imgEl) {
        lbImage.src = imgEl.src;
        lbImage.alt = imgEl.alt || '';
        lightbox.classList.remove('hidden');

        requestAnimationFrame(() => {
            lbImage.classList.remove('opacity-0');
        });

        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', onKeyDown);
        }

        function closeLightbox() {
        lbImage.classList.add('opacity-0');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lbImage.src = '';
        }, 200);

        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKeyDown);
        }

        function onKeyDown(e) {
        if (e.key === 'Escape') closeLightbox();
        }

        triggers.forEach(img => {
        img.addEventListener('click', () => openLightbox(img));
        });

        lbBackdrop.addEventListener('click', closeLightbox);
        lbClose.addEventListener('click', closeLightbox);
    })();
    </script>


</body>