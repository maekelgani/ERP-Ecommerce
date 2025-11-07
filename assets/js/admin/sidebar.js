document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // Sidebar
    // ===================
    // 
    (function () {
        const dropdownSections = document.querySelectorAll('.dropdown-section');
        const sidebar = document.getElementById('sidebar');
        if (!sidebar || dropdownSections.length === 0) return;

        // === 1. mulihin status setiap dropdown ===
        dropdownSections.forEach(dropdownSections => {
            const sectionName = dropdownSections.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();
            const savedState = localStorage.getItem(`dropdown-${sectionName}`) === 'open';
            const menu = dropdownSections.querySelector('.dropdown-menu');
            const arrow = dropdownSections.querySelector('.arrow-icon');
            if (savedState) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
        // === 2. Event buka/tutup ===
        dropdownSections.forEach(dropdownSections => {
            const btn = dropdownSections.querySelector('.toggle-btn');
            const menu = dropdownSections.querySelector('.dropdown-menu');
            const arrow = dropdownSections.querySelector('.arrow-icon');
            const sectionName = dropdownSections.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();
            btn.addEventListener('click', () => {
                const isOpen = !menu.classList.contains('hidden');
                // Toggle dropdown yang diklik
                menu.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
                // Simpan statusnya
                localStorage.setItem(`dropdown-${sectionName}`, menu.classList.contains('hidden') ? 'closed' : 'open');
            });
        });
        // === 3. Tandai menu aktif berdasarkan URL saat ini ===
        const menuItems = document.querySelectorAll('.menu-item');
        const currentUrl = window.location.pathname;
        let foundActive = false;
        menuItems.forEach(item => {
            const itemUrl = item.getAttribute('href');
            if (itemUrl === currentUrl) {
                item.classList.add('bg-gray-100', 'text-gray-800');
                localStorage.setItem('activeMenu', itemUrl);
                foundActive = true;
            } else {
                item.classList.remove('bg-gray-100', 'text-gray-800');
            }
        });
        // Jika halaman sekarang bukan salah satu menu sidebar, hapus activeMenu
        if (!foundActive) {
            localStorage.removeItem('activeMenu');
        }
        requestAnimationFrame(() => sidebar.classList.remove('invisible'));
    })();
})