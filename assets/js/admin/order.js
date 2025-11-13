document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // Pembuka LIHAT DETAIL DI INORDER DAN ORDER
    // ===================
    // untuk modal lihat detail dari Orderan
    // diterapkan di halaman OrderAdmin.php dan IncomingOrdersAdmin.php

    (function () {
        const detailBtns = document.querySelectorAll(".detailOrder");
        const modal = document.getElementById("order-details");
        if (!modal) return;

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal.querySelector(".cancel");

        const elOrderId = modal.querySelector("#d-order-id");
        const elName = modal.querySelector("#d-name");
        const elAddress = modal.querySelector("#d-address");
        const elItemsList = modal.querySelector("#d-items-list"); // Diubah ke #d-items-list untuk div dinamis
        const elItemCount = modal.querySelector("#d-item-count"); // Tambahan untuk jumlah items
        const elDate = modal.querySelector("#d-date");
        const elRecieveDate = modal.querySelector("#d-recieveDate");
        const elPayment = modal.querySelector("#d-payment");
        const elMethod = modal.querySelector("#d-method");
        const elStatus = modal.querySelector("#d-status");
        const elTotal = modal.querySelector("#d-total");
        // Tambahan elemen pembayaran
        const elPaymentId = modal.querySelector("#d-payment-id");
        const elPaymentDate = modal.querySelector("#d-payment-date");
        const elPaymentStatus = modal.querySelector("#d-payment-status");

        // Elemen Progress Bar
        const stepPacked = modal.querySelector("#step-packed");
        const stepShipped = modal.querySelector("#step-shipped");
        const stepDone = modal.querySelector("#step-done");

        function resetProgress() {
            [stepPacked, stepShipped, stepDone].forEach(step => {
                step.classList.remove("bg-green-600");
                step.classList.add("bg-gray-300");
            });
        }

        function updateShippingProgress(status) {
            resetProgress();
            if (status === "dikemas") {
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
            } else if (status === "dikirim") { // Sesuai kode Anda (pengiriman -> dikirim)
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
                stepShipped.classList.replace("bg-gray-300", "bg-green-600");
            } else if (status === "selesai") {
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
                stepShipped.classList.replace("bg-gray-300", "bg-green-600");
                stepDone.classList.replace("bg-gray-300", "bg-green-600");
            }
        }

        function openModal() {
            modal.classList.remove("hidden");
            requestAnimationFrame(() => {
                backdrop.classList.remove("opacity-0");
                modalCard.classList.remove("opacity-0", "scale-95", "translate-y-4");
            });
        }

        function closeModal() {
            backdrop.classList.add("opacity-0");
            modalCard.classList.add("opacity-0", "scale-95", "translate-y-4");
            setTimeout(() => modal.classList.add("hidden"), 300);
        }

        detailBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();

                // Isi daftar items secara dinamis (diubah dari <li> ke div untuk konsistensi HTML)
                const itemsRaw = btn.getAttribute("data-items") || "";
                const itemsArr = itemsRaw.split(";").filter(item => item.trim());
                elItemsList.innerHTML = ""; // Kosongkan sebelumnya
                elItemCount.textContent = itemsArr.length; // Update jumlah items
                itemsArr.forEach((item) => {
                    const [name, qty, price] = item.split("|");
                    const itemDiv = document.createElement("div");
                    itemDiv.className = "flex items-center justify-between p-4 rounded-lg"; // Styling sederhana
                    itemDiv.innerHTML = `
                        <div class="flex-1">
                            <p class="font-semibold">${name || 'N/A'}</p>
                            <p class="text-sm text-gray-400">Qty: ${qty || 0}</p>
                        </div>
                        <div class="text-right">Rp ${(parseInt(price) || 0).toLocaleString('id-ID')}</div>
                    `;
                    elItemsList.appendChild(itemDiv);
                });

                const status = btn.dataset.status;

                // Isi elemen utama
                elOrderId.textContent = btn.dataset.orderId || '';
                elName.textContent = btn.dataset.name || '';
                elAddress.textContent = btn.dataset.address || '';
                elDate.textContent = btn.dataset.date || '';
                elRecieveDate.textContent = btn.dataset.recievedate || ''; // Sesuai kode Anda
                elPayment.textContent = btn.dataset.payment || '';
                elMethod.textContent = btn.dataset.method || '';
                elStatus.textContent = status || '';
                elTotal.textContent = (parseInt(btn.dataset.total) || 0).toLocaleString('id-ID');

                // Isi elemen pembayaran (tambahan)
                elPaymentId.textContent = btn.dataset.paymentId || '';
                elPaymentDate.textContent = btn.dataset.paymentDate || '';
                elPaymentStatus.textContent = btn.dataset.payment || ''; // Asumsi sama dengan payment

                // Update progress bar
                updateShippingProgress(status);
            });
        });

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();
});
