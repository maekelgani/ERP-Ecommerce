(function () {
    document.addEventListener("DOMContentLoaded", () => {
        initAddressBook();
        initPaymentMethodSwitcher();
        initCheckoutStepper();
    });

    // =========================
    // Address Book Management
    // =========================
    function initAddressBook() {
        const addAddressModal = document.getElementById('addAddressModal');
        const addAddressForm = document.getElementById('addAddressForm');
        const btnAddNewAddress = document.getElementById('btnAddNewAddress');
        const btnAddFirstAddress = document.getElementById('btnAddFirstAddress');
        const closeButtons = document.querySelectorAll('.close-modal');

        if (!addAddressForm) return;

        // Open modal
        const openAddressModal = () => {
            addAddressForm.reset();
            addAddressModal.classList.remove('hidden');
            addAddressModal.classList.add('show');
            document.body.style.overflow = 'hidden';
            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast('Silakan isi data alamat dengan lengkap', 'info', 'Form Alamat');
            }
        };

        // Close modal
        const closeAddressModal = () => {
            addAddressModal.classList.add('hidden');
            addAddressModal.classList.remove('show');
            document.body.style.overflow = '';
            addAddressForm.reset();
        };

        if (btnAddNewAddress) {
            btnAddNewAddress.addEventListener('click', openAddressModal);
        }

        if (btnAddFirstAddress) {
            btnAddFirstAddress.addEventListener('click', openAddressModal);
        }

        closeButtons.forEach(btn => {
            if (btn.classList.contains('address-modal-close') || btn.classList.contains('address-modal-btn-cancel')) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    closeAddressModal();
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast('Penambahan alamat dibatalkan', 'warning', 'Dibatalkan');
                    }
                });
            }
        });

        // Close modal when clicking outside
        addAddressModal.addEventListener('click', (e) => {
            if (e.target === addAddressModal) {
                closeAddressModal();
            }
        });

        // Handle form submission
        addAddressForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate inputs
            const namaParenerima = document.getElementById('namaPenerimaCheckout').value.trim();
            const nomorHp = document.getElementById('nomorHpCheckout').value.trim();
            const alamatLengkap = document.getElementById('alamatLengkapCheckout').value.trim();
            const provinsi = document.getElementById('provinsiSelect').value;
            const kota = document.getElementById('kotaSelect').value;
            const kecamatan = document.getElementById('kecamatanSelect').value;
            const kelurahan = document.getElementById('kelurahanSelect').value;
            const kodePos = document.getElementById('kodePosCheckout').value.trim();

            if (!namaParenerima || !nomorHp || !alamatLengkap || !provinsi || !kota || !kecamatan || !kelurahan || !kodePos) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Harap isi semua kolom yang diperlukan', 'warning', 'Data Tidak Lengkap');
                }
                return;
            }

            // Validate phone number
            if (!/^(\+62|0)[0-9]{9,12}$/.test(nomorHp)) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Format nomor HP tidak valid. Gunakan format 08xxx atau +62xxx', 'warning', 'Format Tidak Valid');
                }
                return;
            }

            // Validate postal code
            if (!/^[0-9]{5}$/.test(kodePos)) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Kode pos harus 5 digit angka', 'warning', 'Format Tidak Valid');
                }
                return;
            }

            const submitBtn = addAddressForm.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            
            const spinnerIcon = document.getElementById('addAddressIcon');
            const btnText = document.getElementById('addAddressBtnText');
            const spinner = document.getElementById('addAddressSpinner');
            
            if (spinnerIcon) spinnerIcon.classList.add('hidden');
            if (btnText) btnText.textContent = 'Menyimpan...';
            if (spinner) spinner.classList.remove('hidden');

            try {
                const formData = new FormData(addAddressForm);

                const response = await fetch('../../api/customer/add-address.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast('Alamat berhasil ditambahkan', 'success', 'Alamat Tersimpan');
                    }

                    closeAddressModal();

                    // Reload address list
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menyimpan alamat', 'error');
                    }
                }
            } catch (error) {
                console.error('Add address error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan saat menyimpan alamat. Silakan coba lagi.', 'error');
                }
            } finally {
                submitBtn.disabled = false;
                if (spinnerIcon) spinnerIcon.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Simpan Alamat';
                if (spinner) spinner.classList.add('hidden');
            }
        });

        // Handle address selection
        const addressRadios = document.querySelectorAll('.address-radio');
        addressRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                const addressCard = radio.closest('.address-card');
                const addressLabel = addressCard.querySelector('.font-bold')?.textContent || 'Alamat';
                
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast(`Alamat ${addressLabel} dipilih`, 'success', 'Alamat Terpilih');
                }

                // Update all address cards styling
                document.querySelectorAll('.address-card').forEach(card => {
                    card.classList.remove('selected');
                    const check = card.querySelector('.address-check');
                    const checkSvg = check.querySelector('svg');
                    check.classList.remove('border-[#882426]', 'bg-[#882426]');
                    check.classList.add('border-gray-300');
                    if (checkSvg) checkSvg.classList.add('hidden');
                    card.querySelector('.address-card-border')?.classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    card.querySelector('.address-card-border')?.classList.add('border-gray-200');
                });

                // Update selected card styling
                addressCard.classList.add('selected');
                const selectedCheck = addressCard.querySelector('.address-check');
                const selectedCheckSvg = selectedCheck.querySelector('svg');
                selectedCheck.classList.add('border-[#882426]', 'bg-[#882426]');
                selectedCheck.classList.remove('border-gray-300');
                if (selectedCheckSvg) selectedCheckSvg.classList.remove('hidden');

                const borderEl = addressCard.querySelector('.border-2');
                if (borderEl) {
                    borderEl.classList.remove('border-gray-200');
                    borderEl.classList.add('border-[#882426]', 'bg-[#882426]/5');
                }
            });
        });

        // Initialize Select2 for location dropdowns
        initLocationDropdowns();
    }

    // Initialize location dropdowns
    function initLocationDropdowns() {
        const provinsiSelect = document.getElementById('provinsiSelect');
        const kotaSelect = document.getElementById('kotaSelect');
        const kecamatanSelect = document.getElementById('kecamatanSelect');
        const kelurahanSelect = document.getElementById('kelurahanSelect');

        if (!provinsiSelect) return;

        // Initialize Select2
        const initSelect2 = (selector, placeholder) => {
            const element = document.querySelector(selector);
            if (element && typeof $ !== 'undefined' && $.fn.select2) {
                $(element).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%'
                });
            }
        };

        initSelect2('#provinsiSelect', 'Pilih Provinsi');
        initSelect2('#kotaSelect', 'Pilih Kota/Kabupaten');
        initSelect2('#kecamatanSelect', 'Pilih Kecamatan');
        initSelect2('#kelurahanSelect', 'Pilih Kelurahan/Desa');

        // Load provinces
        loadProvinces();

        // Handle province change
        if (provinsiSelect) {
            provinsiSelect.addEventListener('change', () => {
                kotaSelect.value = '';
                kecamatanSelect.value = '';
                kelurahanSelect.value = '';
                kecamatanSelect.disabled = true;
                kelurahanSelect.disabled = true;

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(kotaSelect).trigger('change');
                    $(kecamatanSelect).trigger('change');
                    $(kelurahanSelect).trigger('change');
                }

                if (provinsiSelect.value) {
                    kotaSelect.disabled = false;
                    loadCities(provinsiSelect.value);
                } else {
                    kotaSelect.disabled = true;
                }
            });
        }

        if (kotaSelect) {
            kotaSelect.addEventListener('change', () => {
                kecamatanSelect.value = '';
                kelurahanSelect.value = '';
                kelurahanSelect.disabled = true;

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(kecamatanSelect).trigger('change');
                    $(kelurahanSelect).trigger('change');
                }

                if (kotaSelect.value) {
                    kecamatanSelect.disabled = false;
                    loadDistricts(kotaSelect.value);
                } else {
                    kecamatanSelect.disabled = true;
                }
            });
        }

        if (kecamatanSelect) {
            kecamatanSelect.addEventListener('change', () => {
                kelurahanSelect.value = '';

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(kelurahanSelect).trigger('change');
                }

                if (kecamatanSelect.value) {
                    kelurahanSelect.disabled = false;
                    loadVillages(kecamatanSelect.value);
                } else {
                    kelurahanSelect.disabled = true;
                }
            });
        }
    }

    // Load provinces from API
    async function loadProvinces() {
        try {
            const response = await fetch('../../api/location/provinces.php');
            const data = await response.json();

            const provinsiSelect = document.getElementById('provinsiSelect');
            if (!provinsiSelect) return;

            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.nama;
                provinsiSelect.appendChild(option);
            });

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(provinsiSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    // Load cities
    async function loadCities(provinceId) {
        try {
            const response = await fetch(`../../api/location/cities.php?province_id=${provinceId}`);
            const data = await response.json();

            const kotaSelect = document.getElementById('kotaSelect');
            kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';

            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.nama;
                kotaSelect.appendChild(option);
            });

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(kotaSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error loading cities:', error);
        }
    }

    // Load districts
    async function loadDistricts(cityId) {
        try {
            const response = await fetch(`../../api/location/districts.php?city_id=${cityId}`);
            const data = await response.json();

            const kecamatanSelect = document.getElementById('kecamatanSelect');
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

            data.forEach(district => {
                const option = document.createElement('option');
                option.value = district.id;
                option.textContent = district.nama;
                kecamatanSelect.appendChild(option);
            });

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(kecamatanSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }

    // Load villages
    async function loadVillages(districtId) {
        try {
            const response = await fetch(`../../api/location/villages.php?district_id=${districtId}`);
            const data = await response.json();

            const kelurahanSelect = document.getElementById('kelurahanSelect');
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

            data.forEach(village => {
                const option = document.createElement('option');
                option.value = village.id;
                option.textContent = village.nama;
                kelurahanSelect.appendChild(option);
            });

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(kelurahanSelect).trigger('change');
            }
        } catch (error) {
            console.error('Error loading villages:', error);
        }
    }

    // =========================
    // Payment Method Switcher
    // =========================
    function initPaymentMethodSwitcher() {
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

            // Show toast notification
            const methodNames = {
                'credit-card': 'Kartu Kredit',
                'e-wallet': 'E-Wallet',
                'bank-transfer': 'Transfer Bank'
            };
            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast(`Metode pembayaran ${methodNames[selected]} dipilih`, 'info', 'Metode Pembayaran');
            }
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
    }

    // =========================
    // Checkout Stepper
    // =========================
    function initCheckoutStepper() {
        const btnToStep2 = document.getElementById('btnToStep2');
        const stepItems = document.querySelectorAll('.step-item');
        const stepContents = document.querySelectorAll('.step-content');

        if (!btnToStep2 || !stepItems.length) return;

        btnToStep2.addEventListener('click', () => {
            const selectedAddress = document.querySelector('.address-radio:checked');
            if (!selectedAddress) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Silakan pilih alamat pengiriman terlebih dahulu', 'warning', 'Alamat Belum Dipilih');
                }
                return;
            }

            // Move to step 2
            moveToStep(2);

            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast('Lanjutkan dengan memilih metode pengiriman', 'info', 'Langkah 2: Pengiriman');
            }
        });
    }

    function moveToStep(stepNumber) {
        const stepItems = document.querySelectorAll('.step-item');
        const stepContents = document.querySelectorAll('.step-content');

        stepItems.forEach(item => {
            item.classList.remove('active');
            if (parseInt(item.dataset.step) <= stepNumber) {
                item.classList.add('active');
            }
        });

        stepContents.forEach(content => {
            content.classList.add('hidden');
        });

        const targetContent = document.getElementById(`step${stepNumber}Content`);
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }
    }
})();
