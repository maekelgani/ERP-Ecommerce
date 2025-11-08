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
        const elItems = modal.querySelector("#d-items");
        const elDate = modal.querySelector("#d-date");
        const elPayment = modal.querySelector("#d-payment");
        const elMethod = modal.querySelector("#d-method");
        const elStatus = modal.querySelector("#d-status");
        const elTotal = modal.querySelector("#d-total");

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
            } else if (status === "dikirim") {
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
            document.documentElement.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        }

        function closeModal() {
            backdrop.classList.add("opacity-0");
            modalCard.classList.add("opacity-0", "scale-95", "translate-y-4");
            setTimeout(() => modal.classList.add("hidden"), 300);
            document.documentElement.style.overflow = "";
            document.body.style.overflow = "";
        }

        detailBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();

                const itemsRaw = btn.getAttribute("data-items") || "";
                const itemsArr = itemsRaw.split(";").filter(item => item.trim());
                elItems.innerHTML = "";
                itemsArr.forEach((item) => {
                    const [name, qty, price] = item.split("|");
                    const li = document.createElement("li");
                    li.textContent = `${name} — ${qty} pcs — Rp ${price}`;
                    elItems.appendChild(li);
                });

                const status = btn.dataset.status;

                elOrderId.textContent = btn.dataset.orderId;
                elName.textContent = btn.dataset.name;
                elAddress.textContent = btn.dataset.address;
                elDate.textContent = btn.dataset.date;
                elPayment.textContent = btn.dataset.payment;
                elMethod.textContent = btn.dataset.method;
                elStatus.textContent = status;
                elTotal.textContent = btn.dataset.total;

                // Biar keupdate
                updateShippingProgress(status);
            });
        });

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();
})
