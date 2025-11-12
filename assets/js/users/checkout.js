document.addEventListener("DOMContentLoaded", () => {
    // =========================
    // Payment Method Switcher
    // =========================
    (function () {
        const paymentSelect = document.getElementById('payment-type');
        const sections = ['credit-card', 'e-wallet', 'bank-transfer'];

        if (!paymentSelect) return;

        // Fungsi untuk sembunyikan semua bagian
        const hideAllSections = () => {
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
        };

        // Jalankan saat pilihan berubah
        paymentSelect.addEventListener('change', function () {
            hideAllSections();
            const selected = this.value;
            const selectedSection = document.getElementById(selected);
            if (selectedSection) selectedSection.classList.remove('hidden');
        });

        // Jalankan sekali saat halaman dimuat (kalau ada pilihan tersimpan)
        const savedValue = localStorage.getItem('selectedPayment');
        if (savedValue && sections.includes(savedValue)) {
            paymentSelect.value = savedValue;
            document.getElementById(savedValue).classList.remove('hidden');
        }

        // Simpan pilihan user ke localStorage
        paymentSelect.addEventListener('change', function () {
            localStorage.setItem('selectedPayment', this.value);
        });
    })();
});
