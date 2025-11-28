(function () {
    document.addEventListener("DOMContentLoaded", () => {

        const navLinks = document.querySelectorAll("aside nav a");
        const cards = document.querySelectorAll(".lg\\:col-span-9 > div");

        // guard supaya tidak error kalau elemen tidak ditemukan
        if (!navLinks.length || !cards.length) return;

        navLinks.forEach((link, index) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();

                // hapus active state dari semua menu aside
                navLinks.forEach(item => {
                    item.classList.remove("bg-primary", "text-white");
                    item.classList.add("text-neutral-600");
                });

                // kasih active state ke menu yang diklik
                link.classList.add("bg-primary", "text-white");
                link.classList.remove("text-neutral-600");

                // hide semua card
                cards.forEach(card => card.classList.add("hidden"));

                // tampilkan card sesuai index menu
                if (cards[index]) cards[index].classList.remove("hidden");
            });
        });

        // tampilkan card pertama saat load awal
        cards[0].classList.remove("hidden");

    });
})();
