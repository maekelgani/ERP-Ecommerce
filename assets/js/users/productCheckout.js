document.addEventListener('DOMContentLoaded', function() {
    const checkoutDataEl = document.getElementById('checkoutData');
    if (!checkoutDataEl) return;
    
    let checkoutData = JSON.parse(checkoutDataEl.value);
    let currentStep = 1;
    let selectedAddress = null;
    let shippingMethod = 'delivery';
    let selectedCourier = null;
    let selectedStore = null;
    let extraPacking = false;
    let packingCost = 0;
    let shippingCost = 0;
    let selectedPayment = null;
    let voucherDiscount = 0;
    let appliedVoucher = null;
    
    initStep1();
    initStep2();
    initStep3();
    initWilayahCascade();
    
    function initStep1() {
        const addressCards = document.querySelectorAll('.address-card');
        addressCards.forEach(card => {
            card.addEventListener('click', function() {
                addressCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    c.querySelector('div').classList.add('border-gray-200');
                    const check = c.querySelector('.address-check');
                    check.classList.remove('border-[#882426]', 'bg-[#882426]');
                    check.classList.add('border-gray-300');
                    check.querySelector('svg').classList.add('hidden');
                });
                
                this.classList.add('selected');
                this.querySelector('div').classList.add('border-[#882426]', 'bg-[#882426]/5');
                this.querySelector('div').classList.remove('border-gray-200');
                const check = this.querySelector('.address-check');
                check.classList.add('border-[#882426]', 'bg-[#882426]');
                check.classList.remove('border-gray-300');
                check.querySelector('svg').classList.remove('hidden');
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                selectedAddress = JSON.parse(radio.dataset.address);
                
                const btnToStep2 = document.getElementById('btnToStep2');
                if (btnToStep2) btnToStep2.disabled = false;
            });
        });
        
        const defaultSelected = document.querySelector('.address-card.selected input[type="radio"]');
        if (defaultSelected && defaultSelected.dataset.address) {
            selectedAddress = JSON.parse(defaultSelected.dataset.address);
        }
        
        const btnToStep2 = document.getElementById('btnToStep2');
        if (btnToStep2) {
            btnToStep2.addEventListener('click', function() {
                if (!selectedAddress) {
                    showToast('Pilih alamat pengiriman terlebih dahulu', 'error');
                    return;
                }
                goToStep(2);
            });
        }
        
        const addAddressModal = document.getElementById('addAddressModal');
        const btnAddNewAddress = document.getElementById('btnAddNewAddress');
        const btnAddFirstAddress = document.getElementById('btnAddFirstAddress');
        
        [btnAddNewAddress, btnAddFirstAddress].forEach(btn => {
            if (btn) {
                btn.addEventListener('click', function() {
                    addAddressModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }
        });
        
        addAddressModal?.querySelectorAll('.close-modal, .modal-overlay').forEach(el => {
            el.addEventListener('click', function() {
                addAddressModal.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });
        
        const addAddressForm = document.getElementById('addAddressForm');
        if (addAddressForm) {
            addAddressForm.addEventListener('submit', handleAddAddress);
        }
    }
    
    function initStep2() {
        const shippingMethodCards = document.querySelectorAll('.shipping-method-card');
        shippingMethodCards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                shippingMethodCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    c.querySelector('div').classList.add('border-gray-200');
                });
                
                this.classList.add('selected');
                this.querySelector('div').classList.add('border-[#882426]', 'bg-[#882426]/5');
                this.querySelector('div').classList.remove('border-gray-200');
                
                shippingMethod = radio.value;
                
                const deliveryOptions = document.getElementById('deliveryOptions');
                const pickupOptions = document.getElementById('pickupOptions');
                
                if (shippingMethod === 'delivery') {
                    deliveryOptions.classList.remove('hidden');
                    pickupOptions.classList.add('hidden');
                    selectedStore = null;
                    document.querySelectorAll('.store-card').forEach(c => {
                        c.classList.remove('selected');
                        c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    });
                } else {
                    deliveryOptions.classList.add('hidden');
                    pickupOptions.classList.remove('hidden');
                    selectedCourier = null;
                    shippingCost = 0;
                    document.querySelectorAll('.courier-card').forEach(c => {
                        c.classList.remove('selected');
                        c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    });
                }
                
                updateShippingDisplay();
                updateBtnToStep3();
            });
        });
        
        const courierCards = document.querySelectorAll('.courier-card');
        courierCards.forEach(card => {
            card.addEventListener('click', function() {
                courierCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    c.querySelector('div').classList.add('border-gray-200');
                });
                
                this.classList.add('selected');
                this.querySelector('div').classList.add('border-[#882426]', 'bg-[#882426]/5');
                this.querySelector('div').classList.remove('border-gray-200');
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                selectedCourier = {
                    value: radio.value,
                    cost: parseInt(radio.dataset.cost),
                    days: radio.dataset.days
                };
                shippingCost = selectedCourier.cost;
                
                updateShippingDisplay();
                updateBtnToStep3();
            });
        });
        
        const storeCards = document.querySelectorAll('.store-card');
        storeCards.forEach(card => {
            card.addEventListener('click', function() {
                storeCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    c.querySelector('div').classList.add('border-gray-200');
                });
                
                this.classList.add('selected');
                this.querySelector('div').classList.add('border-[#882426]', 'bg-[#882426]/5');
                this.querySelector('div').classList.remove('border-gray-200');
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                selectedStore = {
                    value: radio.value,
                    name: radio.dataset.name
                };
                shippingCost = 0;
                
                updateShippingDisplay();
                updateBtnToStep3();
            });
        });
        
        const extraPackingCheckbox = document.getElementById('extraPackingCheckbox');
        if (extraPackingCheckbox) {
            extraPackingCheckbox.addEventListener('change', function() {
                extraPacking = this.checked;
                packingCost = extraPacking ? 15000 : 0;
                updateShippingDisplay();
            });
        }
        
        const btnBackToStep1 = document.getElementById('btnBackToStep1');
        if (btnBackToStep1) {
            btnBackToStep1.addEventListener('click', () => goToStep(1));
        }
        
        const btnToStep3 = document.getElementById('btnToStep3');
        if (btnToStep3) {
            btnToStep3.addEventListener('click', function() {
                if (shippingMethod === 'delivery' && !selectedCourier) {
                    showToast('Pilih jasa pengiriman terlebih dahulu', 'error');
                    return;
                }
                if (shippingMethod === 'pickup' && !selectedStore) {
                    showToast('Pilih lokasi toko terlebih dahulu', 'error');
                    return;
                }
                goToStep(3);
            });
        }
        
        const btnChangeAddress = document.getElementById('btnChangeAddress');
        if (btnChangeAddress) {
            btnChangeAddress.addEventListener('click', () => goToStep(1));
        }
    }
    
    function initStep3() {
        const categoryToggles = document.querySelectorAll('.payment-category-toggle');
        categoryToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const category = this.dataset.category;
                const options = document.querySelector(`.payment-options[data-category="${category}"]`);
                
                if (options) {
                    options.classList.toggle('hidden');
                    this.classList.toggle('expanded');
                }
            });
        });
        
        const paymentCards = document.querySelectorAll('.payment-card');
        paymentCards.forEach(card => {
            card.addEventListener('click', function() {
                paymentCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('div').classList.remove('border-[#882426]', 'bg-[#882426]/5');
                    c.querySelector('div').classList.add('border-gray-200');
                });
                
                this.classList.add('selected');
                this.querySelector('div').classList.add('border-[#882426]', 'bg-[#882426]/5');
                this.querySelector('div').classList.remove('border-gray-200');
                
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                selectedPayment = {
                    value: radio.value,
                    type: radio.dataset.type,
                    name: radio.dataset.name
                };
                
                updateBtnPlaceOrder();
            });
        });
        
        const agreeTerms = document.getElementById('agreeTerms');
        if (agreeTerms) {
            agreeTerms.addEventListener('change', updateBtnPlaceOrder);
        }
        
        const btnBackToStep2 = document.getElementById('btnBackToStep2');
        if (btnBackToStep2) {
            btnBackToStep2.addEventListener('click', () => goToStep(2));
        }
        
        const btnChangeShipping = document.getElementById('btnChangeShipping');
        if (btnChangeShipping) {
            btnChangeShipping.addEventListener('click', () => goToStep(2));
        }
        
        const btnApplyVoucher = document.getElementById('btnApplyVoucher');
        if (btnApplyVoucher) {
            btnApplyVoucher.addEventListener('click', handleApplyVoucher);
        }
        
        const btnPlaceOrder = document.getElementById('btnPlaceOrder');
        if (btnPlaceOrder) {
            btnPlaceOrder.addEventListener('click', handlePlaceOrder);
        }
    }
    
    function goToStep(step) {
        currentStep = step;
        
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('active');
        });
        
        const targetContent = document.getElementById(`step${step}Content`);
        if (targetContent) {
            targetContent.classList.remove('hidden');
            targetContent.classList.add('active');
        }
        
        document.querySelectorAll('.step-item').forEach(item => {
            const itemStep = parseInt(item.dataset.step);
            item.classList.remove('active', 'completed');
            
            if (itemStep < step) {
                item.classList.add('completed');
            } else if (itemStep === step) {
                item.classList.add('active');
            }
        });
        
        document.querySelectorAll('.step-line').forEach(line => {
            const afterStep = parseInt(line.dataset.after);
            if (afterStep < step) {
                line.classList.add('completed');
            } else {
                line.classList.remove('completed');
            }
        });
        
        if (step === 2 && selectedAddress) {
            updateAddressPreview();
        }
        
        if (step === 3) {
            updatePaymentPreview();
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function updateAddressPreview() {
        const previewNama = document.getElementById('previewNamaPenerima');
        const previewAlamat = document.getElementById('previewAlamatLengkap');
        const previewWilayah = document.getElementById('previewWilayah');
        
        if (previewNama && selectedAddress) {
            previewNama.textContent = `${selectedAddress.nama_penerima} (${selectedAddress.nomor_hp})`;
            previewAlamat.textContent = selectedAddress.alamat_lengkap;
            previewWilayah.textContent = `${selectedAddress.kelurahan}, ${selectedAddress.kecamatan}, ${selectedAddress.kota}, ${selectedAddress.provinsi} ${selectedAddress.kode_pos}`;
        }
    }
    
    function updatePaymentPreview() {
        const previewNama = document.getElementById('paymentPreviewNama');
        const previewAlamat = document.getElementById('paymentPreviewAlamat');
        const previewCourier = document.getElementById('paymentPreviewCourier');
        const previewEstimasi = document.getElementById('paymentPreviewEstimasi');
        
        if (previewNama && selectedAddress) {
            previewNama.textContent = selectedAddress.nama_penerima;
            previewAlamat.textContent = `${selectedAddress.alamat_lengkap}, ${selectedAddress.kota}`;
        }
        
        if (previewCourier) {
            if (shippingMethod === 'delivery' && selectedCourier) {
                const courierLabel = selectedCourier.value.replace('-', ' ').toUpperCase();
                previewCourier.textContent = courierLabel;
                previewEstimasi.textContent = `Estimasi ${selectedCourier.days} hari kerja - Rp ${formatNumber(selectedCourier.cost)}`;
            } else if (shippingMethod === 'pickup' && selectedStore) {
                previewCourier.textContent = 'Ambil di Toko';
                previewEstimasi.textContent = selectedStore.name;
            }
        }
    }
    
    function updateShippingDisplay() {
        const shippingCostDisplay = document.getElementById('shippingCostDisplay');
        const packingCostRow = document.getElementById('packingCostRow');
        const packingCostDisplay = document.getElementById('packingCostDisplay');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        
        if (shippingCostDisplay) {
            if (shippingMethod === 'pickup') {
                shippingCostDisplay.textContent = 'Gratis';
                shippingCostDisplay.classList.remove('text-gray-400');
                shippingCostDisplay.classList.add('text-green-600');
            } else if (selectedCourier) {
                shippingCostDisplay.textContent = `Rp ${formatNumber(shippingCost)}`;
                shippingCostDisplay.classList.remove('text-gray-400', 'text-green-600');
            } else {
                shippingCostDisplay.textContent = 'Belum dipilih';
                shippingCostDisplay.classList.add('text-gray-400');
                shippingCostDisplay.classList.remove('text-green-600');
            }
        }
        
        if (packingCostRow && packingCostDisplay) {
            if (extraPacking) {
                packingCostRow.classList.remove('hidden');
                packingCostDisplay.textContent = `Rp ${formatNumber(packingCost)}`;
            } else {
                packingCostRow.classList.add('hidden');
            }
        }
        
        const grandTotal = checkoutData.subtotal + checkoutData.tax_amount + shippingCost + packingCost - voucherDiscount;
        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = `Rp ${formatNumber(grandTotal)}`;
        }
    }
    
    function updateBtnToStep3() {
        const btnToStep3 = document.getElementById('btnToStep3');
        if (btnToStep3) {
            if (shippingMethod === 'delivery') {
                btnToStep3.disabled = !selectedCourier;
            } else {
                btnToStep3.disabled = !selectedStore;
            }
        }
    }
    
    function updateBtnPlaceOrder() {
        const btnPlaceOrder = document.getElementById('btnPlaceOrder');
        const agreeTerms = document.getElementById('agreeTerms');
        const btnPlaceOrderTotal = document.getElementById('btnPlaceOrderTotal');
        
        if (btnPlaceOrder) {
            btnPlaceOrder.disabled = !selectedPayment || !agreeTerms?.checked;
        }
        
        if (btnPlaceOrderTotal) {
            const grandTotal = checkoutData.subtotal + checkoutData.tax_amount + shippingCost + packingCost - voucherDiscount;
            btnPlaceOrderTotal.textContent = `(Rp ${formatNumber(grandTotal)})`;
        }
    }
    
    async function handleAddAddress(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const spinner = document.getElementById('addAddressSpinner');
        
        spinner?.classList.remove('hidden');
        
        const data = {
            label_alamat: formData.get('label_alamat') || 'Alamat',
            nama_penerima: formData.get('nama_penerima'),
            nomor_hp: formData.get('nomor_hp'),
            alamat_lengkap: formData.get('alamat_lengkap'),
            provinsi: document.getElementById('provinsiNama')?.value || '',
            kota: document.getElementById('kotaNama')?.value || '',
            kecamatan: document.getElementById('kecamatanNama')?.value || '',
            kelurahan: document.getElementById('kelurahanNama')?.value || '',
            kode_pos: formData.get('kode_pos'),
            default_alamat: formData.get('default_alamat') ? 1 : 0
        };
        
        try {
            const response = await fetch('../../api/customer/address-book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Alamat berhasil ditambahkan', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message || 'Gagal menambahkan alamat', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        } finally {
            spinner?.classList.add('hidden');
        }
    }
    
    async function handleApplyVoucher() {
        const voucherInput = document.getElementById('voucherCode');
        const voucherMessage = document.getElementById('voucherMessage');
        const code = voucherInput?.value.trim().toUpperCase();
        
        if (!code) {
            voucherMessage.textContent = 'Masukkan kode voucher';
            voucherMessage.classList.add('text-red-600');
            return;
        }
        
        try {
            const response = await fetch('../../api/checkout/apply-voucher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    subtotal: checkoutData.subtotal
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                appliedVoucher = result.voucher;
                voucherDiscount = result.discount;
                voucherMessage.textContent = `Voucher berhasil! Diskon Rp ${formatNumber(voucherDiscount)}`;
                voucherMessage.classList.remove('text-red-600');
                voucherMessage.classList.add('text-green-600');
                updateShippingDisplay();
                updateBtnPlaceOrder();
            } else {
                voucherMessage.textContent = result.message || 'Voucher tidak valid';
                voucherMessage.classList.add('text-red-600');
                voucherMessage.classList.remove('text-green-600');
            }
        } catch (error) {
            console.error('Error:', error);
            voucherMessage.textContent = 'Terjadi kesalahan saat memvalidasi voucher';
            voucherMessage.classList.add('text-red-600');
        }
    }
    
    async function handlePlaceOrder() {
        const btnPlaceOrder = document.getElementById('btnPlaceOrder');
        const originalText = btnPlaceOrder.innerHTML;
        
        btnPlaceOrder.disabled = true;
        btnPlaceOrder.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Memproses...
        `;
        
        const orderData = {
            address: selectedAddress,
            shipping_method: shippingMethod,
            courier: selectedCourier,
            store: selectedStore,
            extra_packing: extraPacking,
            packing_cost: packingCost,
            shipping_cost: shippingCost,
            payment: selectedPayment,
            voucher: appliedVoucher,
            voucher_discount: voucherDiscount,
            subtotal: checkoutData.subtotal,
            tax_amount: checkoutData.tax_amount,
            grand_total: checkoutData.subtotal + checkoutData.tax_amount + shippingCost + packingCost - voucherDiscount
        };
        
        try {
            const response = await fetch('../../api/checkout/place-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessModal(result.order_id, orderData.grand_total);
            } else {
                showToast(result.message || 'Gagal membuat pesanan', 'error');
                btnPlaceOrder.disabled = false;
                btnPlaceOrder.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat membuat pesanan', 'error');
            btnPlaceOrder.disabled = false;
            btnPlaceOrder.innerHTML = originalText;
        }
    }
    
    function showSuccessModal(orderId, total) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center animate-bounce-in">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h2>
                <p class="text-gray-600 mb-4">Terima kasih telah berbelanja di Nano Komputer</p>
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm text-gray-500">ID Pesanan</p>
                    <p class="text-lg font-bold text-[#882426]">${orderId}</p>
                    <p class="text-sm text-gray-500 mt-2">Total Pembayaran</p>
                    <p class="text-xl font-bold text-gray-900">Rp ${formatNumber(total)}</p>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="processPayment.php?order=${orderId}" class="w-full px-6 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-colors">
                        Lanjutkan Pembayaran
                    </a>
                    <a href="landingPage.php" class="w-full px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        launchConfetti();
    }
    
    function initWilayahCascade() {
        const provinsiSelect = document.getElementById('provinsiSelect');
        const kotaSelect = document.getElementById('kotaSelect');
        const kecamatanSelect = document.getElementById('kecamatanSelect');
        const kelurahanSelect = document.getElementById('kelurahanSelect');
        
        if (!provinsiSelect) return;
        
        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(res => res.json())
            .then(data => {
                data.forEach(prov => {
                    provinsiSelect.innerHTML += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                });
            })
            .catch(err => console.error('Error loading provinces:', err));
        
        provinsiSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('provinsiNama').value = selected.dataset.name || '';
            
            kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            kotaSelect.disabled = !this.value;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kecamatanSelect.disabled = true;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = true;
            
            if (this.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kota => {
                            kotaSelect.innerHTML += `<option value="${kota.id}" data-name="${kota.name}">${kota.name}</option>`;
                        });
                    })
                    .catch(err => console.error('Error loading regencies:', err));
            }
        });
        
        kotaSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('kotaNama').value = selected.dataset.name || '';
            
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kecamatanSelect.disabled = !this.value;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = true;
            
            if (this.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kec => {
                            kecamatanSelect.innerHTML += `<option value="${kec.id}" data-name="${kec.name}">${kec.name}</option>`;
                        });
                    })
                    .catch(err => console.error('Error loading districts:', err));
            }
        });
        
        kecamatanSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('kecamatanNama').value = selected.dataset.name || '';
            
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = !this.value;
            
            if (this.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kel => {
                            kelurahanSelect.innerHTML += `<option value="${kel.id}" data-name="${kel.name}">${kel.name}</option>`;
                        });
                    })
                    .catch(err => console.error('Error loading villages:', err));
            }
        });
        
        kelurahanSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('kelurahanNama').value = selected.dataset.name || '';
        });
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
    
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-24 right-4 z-50 px-6 py-4 rounded-xl shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    function launchConfetti() {
        const colors = ['#882426', '#10b981', '#f59e0b', '#3b82f6', '#ec4899'];
        
        for (let i = 0; i < 100; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'fixed w-2 h-2 z-50 rounded-full';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animation = `confetti-fall ${2 + Math.random() * 2}s linear forwards`;
            
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 4000);
        }
    }
});

const style = document.createElement('style');
style.textContent = `
    @keyframes confetti-fall {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
    
    @keyframes bounce-in {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .animate-bounce-in {
        animation: bounce-in 0.5s ease-out;
    }
`;
document.head.appendChild(style);
