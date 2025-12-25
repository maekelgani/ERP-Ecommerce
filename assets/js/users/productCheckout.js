document.addEventListener('DOMContentLoaded', function() {
    const checkoutDataEl = document.getElementById('checkoutData');
    if (!checkoutDataEl) return;
    
    let checkoutData = JSON.parse(checkoutDataEl.value);
    let currentStep = 1;
    let selectedAddress = null;
    let shippingMethod = 'delivery';
    let selectedCourier = null;
    let selectedStore = null;
    let bubbleWrap = false;
    let packingKayu = false;
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
    
    let shippingRatesLoaded = false;
    let shippingRatesData = [];
    
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
        
        const btnRetryShipping = document.getElementById('btnRetryShipping');
        if (btnRetryShipping) {
            btnRetryShipping.addEventListener('click', () => {
                if (selectedAddress && selectedAddress.kode_pos) {
                    loadShippingRates(selectedAddress.kode_pos);
                }
            });
        }
        
        initCourierCardListeners();
        
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
        
        const bubbleWrapCheckbox = document.getElementById('bubbleWrapCheckbox');
        const packingKayuCheckbox = document.getElementById('packingKayuCheckbox');
        
        if (bubbleWrapCheckbox) {
            bubbleWrapCheckbox.addEventListener('change', function() {
                bubbleWrap = this.checked;
                updatePackingCost();
                updateShippingDisplay();
            });
        }
        
        if (packingKayuCheckbox) {
            packingKayuCheckbox.addEventListener('change', function() {
                packingKayu = this.checked;
                updatePackingCost();
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
        
        initVoucherTabs();
        
        const btnRemoveVoucher = document.getElementById('btnRemoveVoucher');
        if (btnRemoveVoucher) {
            btnRemoveVoucher.addEventListener('click', handleRemoveVoucher);
        }
        
        const btnPlaceOrder = document.getElementById('btnPlaceOrder');
        if (btnPlaceOrder) {
            btnPlaceOrder.addEventListener('click', handlePlaceOrder);
        }
        
        const orderNotesInput = document.getElementById('orderNotes');
        const orderNotesCount = document.getElementById('orderNotesCount');
        if (orderNotesInput && orderNotesCount) {
            orderNotesInput.addEventListener('input', () => {
                orderNotesCount.textContent = orderNotesInput.value.length;
            });
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
            if (!shippingRatesLoaded && selectedAddress.kode_pos) {
                loadShippingRates(selectedAddress.kode_pos);
            }
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
                const courierLabel = selectedCourier.courier_name 
                    ? `${selectedCourier.courier_name} ${selectedCourier.courier_service || ''}`
                    : selectedCourier.value.replace('-', ' ').toUpperCase();
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
        const voucherDiscountRow = document.getElementById('voucherDiscountRow');
        const voucherDiscountDisplay = document.getElementById('voucherDiscountDisplay');
        const storeInfoSection = document.getElementById('storeInfoSection');
        const storeInfoName = document.getElementById('storeInfoName');
        const storeInfoAddress = document.getElementById('storeInfoAddress');
        
        if (shippingCostDisplay) {
            if (shippingMethod === 'pickup') {
                shippingCostDisplay.textContent = 'Gratis (Ambil di Toko)';
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
            const packingCostLabel = document.getElementById('packingCostLabel');
            if (packingCost > 0) {
                packingCostRow.classList.remove('hidden');
                let packingLabels = [];
                if (bubbleWrap) packingLabels.push('Bubble Wrap');
                if (packingKayu) packingLabels.push('Packing Kayu');
                if (packingCostLabel) {
                    packingCostLabel.textContent = packingLabels.join(' + ');
                }
                packingCostDisplay.textContent = `Rp ${formatNumber(packingCost)}`;
            } else {
                packingCostRow.classList.add('hidden');
            }
        }
        
        if (voucherDiscountRow && voucherDiscountDisplay) {
            if (voucherDiscount > 0) {
                voucherDiscountRow.classList.remove('hidden');
                voucherDiscountDisplay.textContent = `- Rp ${formatNumber(voucherDiscount)}`;
            } else {
                voucherDiscountRow.classList.add('hidden');
            }
        }
        
        if (storeInfoSection) {
            if (shippingMethod === 'pickup' && selectedStore) {
                storeInfoSection.classList.remove('hidden');
                const storeRadio = document.querySelector(`.store-card input[type="radio"][value="${selectedStore.value}"]`);
                if (storeRadio && storeInfoName) {
                    storeInfoName.textContent = storeRadio.dataset.name || selectedStore.name;
                }
                if (storeRadio && storeInfoAddress) {
                    storeInfoAddress.textContent = storeRadio.dataset.address || 'Alamat tidak tersedia';
                }
            } else {
                storeInfoSection.classList.add('hidden');
            }
        }
        
        const grandTotal = checkoutData.subtotal + checkoutData.tax_amount + shippingCost + packingCost - voucherDiscount;
        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = `Rp ${formatNumber(grandTotal)}`;
        }
    }
    
    function updatePackingCost() {
        packingCost = 0;
        if (bubbleWrap) packingCost += 5000;
        if (packingKayu) packingCost += 20000;
    }
    
    async function loadShippingRates(postalCode) {
        const courierLoading = document.getElementById('courierLoading');
        const courierError = document.getElementById('courierError');
        const courierList = document.getElementById('courierList');
        const courierFallbackNotice = document.getElementById('courierFallbackNotice');
        const courierWeightInfo = document.getElementById('courierWeightInfo');
        
        courierLoading?.classList.remove('hidden');
        courierError?.classList.add('hidden');
        courierList?.classList.add('hidden');
        courierFallbackNotice?.classList.add('hidden');
        
        selectedCourier = null;
        shippingCost = 0;
        shippingRatesLoaded = false;
        updateBtnToStep3();
        
        const requestData = {
            postal_code: postalCode,
            kecamatan: selectedAddress?.kecamatan || '',
            kota: selectedAddress?.kota || '',
            provinsi: selectedAddress?.provinsi || '',
            area_id: selectedAddress?.biteship_area_id || ''
        };
        
        try {
            const response = await fetch('../../ajax/get-shipping-rates.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
            
            const result = await response.json();
            
            courierLoading?.classList.add('hidden');
            
            if (result.success && result.rates && result.rates.length > 0) {
                shippingRatesData = result.rates;
                shippingRatesLoaded = true;
                renderCourierCards(result.rates);
                courierList?.classList.remove('hidden');
                
                if (result.total_weight && courierWeightInfo) {
                    const weightKg = (result.total_weight / 1000).toFixed(1);
                    const weightText = document.getElementById('courierWeightText');
                    if (weightText) {
                        weightText.textContent = `Total berat: ${weightKg} kg`;
                    }
                    courierWeightInfo.classList.remove('hidden');
                }
                
                if (result.is_fallback) {
                    courierFallbackNotice?.classList.remove('hidden');
                    if (result.message) {
                        const fallbackMsg = courierFallbackNotice.querySelector('span');
                        if (fallbackMsg) fallbackMsg.textContent = result.message;
                    }
                }
            } else {
                courierError?.classList.remove('hidden');
                document.getElementById('courierErrorMessage').textContent = result.message || 'Tidak ada jasa pengiriman tersedia.';
            }
        } catch (error) {
            console.error('Error loading shipping rates:', error);
            courierLoading?.classList.add('hidden');
            courierError?.classList.remove('hidden');
            document.getElementById('courierErrorMessage').textContent = 'Terjadi kesalahan saat mengambil data ongkir.';
        }
    }
    
    function renderCourierCards(rates) {
        const courierList = document.getElementById('courierList');
        if (!courierList) return;
        
        courierList.innerHTML = '';
        
        rates.forEach(rate => {
            const card = document.createElement('label');
            card.className = 'courier-card block relative cursor-pointer';
            
            const logoHtml = rate.logo 
                ? `<img src="${rate.logo}" alt="${rate.courier_name}" class="h-8 w-auto object-contain" onerror="this.parentElement.innerHTML='<span class=\\'text-white text-xs font-bold\\'>${rate.courier_code.toUpperCase()}</span>'">`
                : `<span class="text-white text-xs font-bold">${rate.courier_code.toUpperCase()}</span>`;
            
            card.innerHTML = `
                <input type="radio" name="courier" value="${rate.rate_id}" class="sr-only" 
                    data-cost="${rate.price}" 
                    data-days="${rate.duration_days}"
                    data-courier-code="${rate.courier_code}"
                    data-courier-name="${rate.courier_name}"
                    data-courier-service="${rate.courier_service}">
                <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                                ${logoHtml}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${rate.courier_name} ${rate.courier_service}</p>
                                <p class="text-sm text-gray-500">Estimasi ${rate.duration_days} hari kerja</p>
                                ${rate.description ? `<p class="text-xs text-gray-400">${rate.description}</p>` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-[#882426]">Rp ${formatNumber(rate.price)}</p>
                        </div>
                    </div>
                </div>
            `;
            
            courierList.appendChild(card);
        });
        
        initCourierCardListeners();
    }
    
    function initCourierCardListeners() {
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
                    days: radio.dataset.days,
                    courier_code: radio.dataset.courierCode,
                    courier_name: radio.dataset.courierName,
                    courier_service: radio.dataset.courierService
                };
                shippingCost = selectedCourier.cost;
                
                updateShippingDisplay();
                updateBtnToStep3();
            });
        });
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
            const response = await fetch('../../api/customer/address-book.php?action=add', {
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
                showAppliedVoucher(appliedVoucher.kode, voucherDiscount);
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
    
    function initVoucherTabs() {
        const tabInputCode = document.getElementById('tabInputCode');
        const tabSelectVoucher = document.getElementById('tabSelectVoucher');
        const voucherInputSection = document.getElementById('voucherInputSection');
        const voucherSelectSection = document.getElementById('voucherSelectSection');
        
        if (!tabInputCode || !tabSelectVoucher) return;
        
        tabInputCode.addEventListener('click', () => {
            tabInputCode.classList.add('border-[#882426]', 'text-[#882426]');
            tabInputCode.classList.remove('border-transparent', 'text-gray-500');
            tabSelectVoucher.classList.remove('border-[#882426]', 'text-[#882426]');
            tabSelectVoucher.classList.add('border-transparent', 'text-gray-500');
            voucherInputSection.classList.remove('hidden');
            voucherSelectSection.classList.add('hidden');
        });
        
        tabSelectVoucher.addEventListener('click', () => {
            tabSelectVoucher.classList.add('border-[#882426]', 'text-[#882426]');
            tabSelectVoucher.classList.remove('border-transparent', 'text-gray-500');
            tabInputCode.classList.remove('border-[#882426]', 'text-[#882426]');
            tabInputCode.classList.add('border-transparent', 'text-gray-500');
            voucherSelectSection.classList.remove('hidden');
            voucherInputSection.classList.add('hidden');
            loadAvailableVouchers();
        });
    }
    
    async function loadAvailableVouchers() {
        const loading = document.getElementById('voucherListLoading');
        const empty = document.getElementById('voucherListEmpty');
        const container = document.getElementById('voucherListContainer');
        
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        container.classList.add('hidden');
        
        try {
            const subtotal = checkoutData?.subtotal || 0;
            const response = await fetch(`../../api/checkout/get-vouchers.php?subtotal=${subtotal}`);
            const result = await response.json();
            
            loading.classList.add('hidden');
            
            if (result.success && result.vouchers.length > 0) {
                container.innerHTML = '';
                result.vouchers.forEach(voucher => {
                    container.appendChild(createVoucherCard(voucher));
                });
                container.classList.remove('hidden');
            } else {
                empty.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading vouchers:', error);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }
    
    function createVoucherCard(voucher) {
        const card = document.createElement('div');
        const isDisabled = !voucher.can_use;
        
        card.className = `voucher-card border-2 rounded-xl p-4 cursor-pointer ${isDisabled ? 'disabled border-gray-200' : 'border-gray-200'}`;
        card.dataset.voucherId = voucher.id;
        card.dataset.voucherCode = voucher.kode;
        
        const discountText = getDiscountText(voucher);
        const validityText = getValidityText(voucher);
        const quotaHtml = getQuotaHtml(voucher);
        
        card.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-[#882426] to-[#a63032] rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">${voucher.judul || voucher.kode}</p>
                            <p class="text-[#882426] font-semibold text-base">${discountText}</p>
                        </div>
                        <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-1 rounded-lg flex-shrink-0">${voucher.kode}</span>
                    </div>
                    ${voucher.deskripsi ? `<p class="text-xs text-gray-500 mt-1 line-clamp-2">${voucher.deskripsi}</p>` : ''}
                    <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-gray-500">
                        ${voucher.minimal_belanja > 0 ? `<span class="bg-gray-100 px-2 py-0.5 rounded">Min. Rp ${formatNumber(voucher.minimal_belanja)}</span>` : ''}
                        ${voucher.maksimal_diskon ? `<span class="bg-gray-100 px-2 py-0.5 rounded">Maks. Rp ${formatNumber(voucher.maksimal_diskon)}</span>` : ''}
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs ${isDisabled ? 'text-red-500' : 'text-gray-500'}">
                            ${isDisabled ? voucher.reason : validityText}
                        </span>
                    </div>
                    ${quotaHtml}
                </div>
            </div>
        `;
        
        if (!isDisabled) {
            card.addEventListener('click', () => selectVoucherFromList(voucher));
        }
        
        return card;
    }
    
    function getDiscountText(voucher) {
        switch (voucher.jenis) {
            case 'diskon_persen':
                return `Diskon ${voucher.nilai}%`;
            case 'diskon_nominal':
                return `Diskon Rp ${formatNumber(voucher.nilai)}`;
            case 'gratis_ongkir':
                return 'Gratis Ongkir (Legacy)';
            case 'cashback':
                return `Cashback (Legacy)`;
            default:
                return `Diskon`;
        }
    }
    
    function getValidityText(voucher) {
        if (!voucher.selesai_pada) return 'Tanpa batas waktu';
        
        const endDate = new Date(voucher.selesai_pada);
        const now = new Date();
        const diffDays = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
        
        if (diffDays <= 0) return 'Berakhir hari ini';
        if (diffDays <= 7) return `Berakhir dalam ${diffDays} hari`;
        
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return `Berlaku hingga ${endDate.toLocaleDateString('id-ID', options)}`;
    }
    
    function getQuotaHtml(voucher) {
        if (!voucher.kuota_total) return '';
        
        const remaining = voucher.kuota_total - voucher.kuota_terpakai;
        const percent = voucher.kuota_percent;
        
        return `
            <div class="mt-2">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                    <span>Tersisa ${remaining} kuota</span>
                    <span>${Math.round(percent)}% terpakai</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-[#882426] h-1.5 rounded-full transition-all" style="width: ${percent}%"></div>
                </div>
            </div>
        `;
    }
    
    async function selectVoucherFromList(voucher) {
        try {
            const response = await fetch('../../api/checkout/apply-voucher.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    code: voucher.kode,
                    subtotal: checkoutData.subtotal
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                appliedVoucher = result.voucher;
                voucherDiscount = result.discount;
                showAppliedVoucher(voucher.kode, voucherDiscount);
                updateShippingDisplay();
                updateBtnPlaceOrder();
                
                document.querySelectorAll('.voucher-card').forEach(card => {
                    card.classList.remove('selected');
                    if (card.dataset.voucherCode === voucher.kode) {
                        card.classList.add('selected');
                    }
                });
            } else {
                showToast(result.message || 'Gagal menerapkan voucher', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    }
    
    function showAppliedVoucher(code, discount) {
        const appliedSection = document.getElementById('appliedVoucherSection');
        const appliedCode = document.getElementById('appliedVoucherCode');
        const appliedDiscount = document.getElementById('appliedVoucherDiscount');
        const voucherMessage = document.getElementById('voucherMessage');
        
        if (appliedSection) {
            appliedCode.textContent = `Voucher ${code} diterapkan`;
            appliedDiscount.textContent = `Hemat Rp ${formatNumber(discount)}`;
            appliedSection.classList.remove('hidden');
        }
        
        if (voucherMessage) {
            voucherMessage.textContent = '';
        }
    }
    
    function handleRemoveVoucher() {
        appliedVoucher = null;
        voucherDiscount = 0;
        
        const appliedSection = document.getElementById('appliedVoucherSection');
        const voucherInput = document.getElementById('voucherCode');
        const voucherMessage = document.getElementById('voucherMessage');
        
        if (appliedSection) appliedSection.classList.add('hidden');
        if (voucherInput) voucherInput.value = '';
        if (voucherMessage) voucherMessage.textContent = '';
        
        document.querySelectorAll('.voucher-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        updateShippingDisplay();
        updateBtnPlaceOrder();
        showToast('Voucher dihapus', 'info');
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
        
        const orderNotesEl = document.getElementById('orderNotes');
        const orderNotes = orderNotesEl ? orderNotesEl.value.trim() : '';
        
        const orderData = {
            address: selectedAddress,
            shipping_method: shippingMethod,
            courier: selectedCourier,
            store: selectedStore,
            bubble_wrap: bubbleWrap,
            packing_kayu: packingKayu,
            packing_cost: packingCost,
            shipping_cost: shippingCost,
            payment: selectedPayment,
            voucher: appliedVoucher,
            voucher_discount: voucherDiscount,
            order_notes: orderNotes,
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
        const labelAlamatSelect = document.getElementById('labelAlamatCheckout');
        
        if (!provinsiSelect) return;
        
        if (typeof $ !== 'undefined' && $.fn.select2) {
            if (labelAlamatSelect) {
                $('#labelAlamatCheckout').select2({
                    placeholder: 'Pilih Label Alamat',
                    minimumResultsForSearch: Infinity,
                    width: '100%',
                    dropdownParent: $('#addAddressModal .address-modal-content')
                });
            }
            
            $('#provinsiSelect').select2({
                placeholder: 'Pilih Provinsi',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addAddressModal .address-modal-content')
            });
            
            $('#kotaSelect').select2({
                placeholder: 'Pilih Kota/Kabupaten',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addAddressModal .address-modal-content')
            });
            
            $('#kecamatanSelect').select2({
                placeholder: 'Pilih Kecamatan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addAddressModal .address-modal-content')
            });
            
            $('#kelurahanSelect').select2({
                placeholder: 'Pilih Kelurahan/Desa',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addAddressModal .address-modal-content')
            });
        }
        
        fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(res => res.json())
            .then(data => {
                data.forEach(prov => {
                    provinsiSelect.innerHTML += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                });
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#provinsiSelect').trigger('change.select2');
                }
            })
            .catch(err => console.error('Error loading provinces:', err));
        
        const handleProvinsiChange = function() {
            const selected = provinsiSelect.options[provinsiSelect.selectedIndex];
            document.getElementById('provinsiNama').value = selected?.dataset?.name || '';
            
            kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            kotaSelect.disabled = !provinsiSelect.value;
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kecamatanSelect.disabled = true;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = true;
            
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#kotaSelect').prop('disabled', !provinsiSelect.value).trigger('change.select2');
                $('#kecamatanSelect').prop('disabled', true).trigger('change.select2');
                $('#kelurahanSelect').prop('disabled', true).trigger('change.select2');
            }
            
            if (provinsiSelect.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinsiSelect.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kota => {
                            kotaSelect.innerHTML += `<option value="${kota.id}" data-name="${kota.name}">${kota.name}</option>`;
                        });
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#kotaSelect').trigger('change.select2');
                        }
                    })
                    .catch(err => console.error('Error loading regencies:', err));
            }
        };
        
        const handleKotaChange = function() {
            const selected = kotaSelect.options[kotaSelect.selectedIndex];
            document.getElementById('kotaNama').value = selected?.dataset?.name || '';
            
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            kecamatanSelect.disabled = !kotaSelect.value;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = true;
            
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#kecamatanSelect').prop('disabled', !kotaSelect.value).trigger('change.select2');
                $('#kelurahanSelect').prop('disabled', true).trigger('change.select2');
            }
            
            if (kotaSelect.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kotaSelect.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kec => {
                            kecamatanSelect.innerHTML += `<option value="${kec.id}" data-name="${kec.name}">${kec.name}</option>`;
                        });
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#kecamatanSelect').trigger('change.select2');
                        }
                    })
                    .catch(err => console.error('Error loading districts:', err));
            }
        };
        
        const handleKecamatanChange = function() {
            const selected = kecamatanSelect.options[kecamatanSelect.selectedIndex];
            document.getElementById('kecamatanNama').value = selected?.dataset?.name || '';
            
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            kelurahanSelect.disabled = !kecamatanSelect.value;
            
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#kelurahanSelect').prop('disabled', !kecamatanSelect.value).trigger('change.select2');
            }
            
            if (kecamatanSelect.value) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecamatanSelect.value}.json`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(kel => {
                            kelurahanSelect.innerHTML += `<option value="${kel.id}" data-name="${kel.name}">${kel.name}</option>`;
                        });
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $('#kelurahanSelect').trigger('change.select2');
                        }
                    })
                    .catch(err => console.error('Error loading villages:', err));
            }
        };
        
        const handleKelurahanChange = function() {
            const selected = kelurahanSelect.options[kelurahanSelect.selectedIndex];
            document.getElementById('kelurahanNama').value = selected?.dataset?.name || '';
        };
        
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#provinsiSelect').on('select2:select select2:clear', handleProvinsiChange);
            $('#kotaSelect').on('select2:select select2:clear', handleKotaChange);
            $('#kecamatanSelect').on('select2:select select2:clear', handleKecamatanChange);
            $('#kelurahanSelect').on('select2:select select2:clear', handleKelurahanChange);
        } else {
            provinsiSelect.addEventListener('change', handleProvinsiChange);
            kotaSelect.addEventListener('change', handleKotaChange);
            kecamatanSelect.addEventListener('change', handleKecamatanChange);
            kelurahanSelect.addEventListener('change', handleKelurahanChange);
        }
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(num));
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
