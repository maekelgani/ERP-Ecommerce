document.addEventListener("DOMContentLoaded", () => {
    (function () {
    // ==============================
    // MOBILE FILTER TOGGLE (Filter & Sorting)
    // ==============================
    const filterToggleBtn = document.querySelector(
        '.flex.cursor-pointer.items-center.gap-2.border-b'
    ); // tombol "Filters & Sorting"
    const filterSidebar = document.querySelector('aside'); // elemen <aside> berisi filter
    const body = document.body;

    if (filterToggleBtn && filterSidebar) {
        // Buat overlay untuk background gelap
        let overlay = document.createElement('div');
        overlay.className =
        'fixed inset-0 bg-black/40 hidden z-40 transition-opacity duration-300';
        document.body.appendChild(overlay);

        // Pastikan sidebar diatur posisi offcanvas
        filterSidebar.classList.add(
        'fixed',
        'top-0',
        '-left-full',
        'h-full',
        'w-3/4',
        'max-w-sm',
        'bg-white',
        'z-50',
        'overflow-y-auto',
        'transition-all',
        'duration-300',
        'lg:static',
        'lg:w-auto',
        'lg:h-auto',
        'lg:bg-transparent',
        'lg:overflow-visible'
        );

        // Fungsi buka sidebar
        const openFilter = () => {
        filterSidebar.classList.remove('-left-full');
        filterSidebar.classList.add('left-0');
        overlay.classList.remove('hidden');
        overlay.classList.add('opacity-100');
        body.style.overflow = 'hidden';
        };

        // Fungsi tutup sidebar
        const closeFilter = () => {
        filterSidebar.classList.remove('left-0');
        filterSidebar.classList.add('-left-full');
        overlay.classList.add('hidden');
        overlay.classList.remove('opacity-100');
        body.style.overflow = '';
        };

        // Event klik pada tombol filter
        filterToggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openFilter();
        });

        // Tutup saat klik overlay
        overlay.addEventListener('click', closeFilter);

        // Tutup saat menekan tombol Escape
        document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeFilter();
        });
    }

    // ==============================
    // DETAIL FILTER EXPAND/COLLAPSE (untuk Category, Availability, Price)
    // ==============================
    const detailsEls = document.querySelectorAll('aside details');

    if (detailsEls.length) {
        detailsEls.forEach((detail) => {
        const summary = detail.querySelector('summary');
        const content = summary.nextElementSibling;
        if (!summary || !content) return;

        content.style.overflow = 'hidden';
        content.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
        content.style.maxHeight = detail.open ? content.scrollHeight + 'px' : '0';
        content.style.opacity = detail.open ? '1' : '0';

        summary.addEventListener('click', (e) => {
            e.preventDefault();

            const isOpen = detail.hasAttribute('open');

            if (isOpen) {
            content.style.maxHeight = content.scrollHeight + 'px';
            requestAnimationFrame(() => {
                content.style.maxHeight = '0';
                content.style.opacity = '0';
            });
            setTimeout(() => detail.removeAttribute('open'), 300);
            } else {
            detail.setAttribute('open', '');
            content.style.maxHeight = '0';
            requestAnimationFrame(() => {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
            });
            }
        });
        });
    }
    })();

})