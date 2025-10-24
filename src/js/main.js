    async function includeHTML() {
    const elements = document.querySelectorAll("[data-include]");

    for (const el of elements) {
        const file = el.getAttribute("data-include");
        if (!file) continue;

        const res = await fetch(file);
        const html = await res.text();
        el.innerHTML = html;

        // Jalankan ulang semua script yang ada di file itu
        const scripts = el.querySelectorAll("script");
        scripts.forEach((oldScript) => {
        const newScript = document.createElement("script");
        if (oldScript.src) {
            // Jika script punya src eksternal
            newScript.src = oldScript.src;
        } else {
            // Jika inline script
            newScript.textContent = oldScript.textContent;
        }
        document.body.appendChild(newScript);
        oldScript.remove();
        });
    }
    }
    document.addEventListener("DOMContentLoaded", includeHTML);

    // CHART nya GATAU GW WAK
    const ctx = document.getElementById('graphCanvas');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            borderWidth: 1
        }]
        },
        options: {
        scales: {
            y: {
            beginAtZero: true
            }
        }
        }
    });

    // 

    // document.addEventListener('DOMContentLoaded', () => {
    // const menuItems = document.querySelectorAll('.menu-item');

    // // Ambil data "halaman aktif" dari localStorage
    // const activePage = localStorage.getItem('activeMenu');

    // // Jika ada menu aktif tersimpan, beri warna
    // if (activePage) {
    //     menuItems.forEach(item => {
    //     if (item.getAttribute('href') === activePage) {
    //         item.classList.add('bg-blue-600');
    //     } else {
    //         item.classList.remove('bg-blue-600');
    //     }
    //     });
    // }

    // // Event: ketika menu diklik
    // menuItems.forEach(item => {
    //     item.addEventListener('click', e => {
    //     // Simpan menu yang diklik ke localStorage
    //     localStorage.setItem('activeMenu', item.getAttribute('href'));

    //     // Ubah tampilan aktif secara langsung
    //     menuItems.forEach(link => link.classList.remove('bg-blue-600'));
    //     item.classList.add('bg-blue-600');
    //     });
    // });
    // });
    