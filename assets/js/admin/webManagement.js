document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '../../api/admin/';
    const WILAYAH_API = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    let currentDeleteId = null;
    let currentDeleteType = null;
    let storeEditMode = false;
    let debounceTimer = null;
    
    // Pagination variables
    let ticketPerPage = 10;
    let ticketCurrentPage = 1;
    let ticketAllData = [];
    let articlePerPage = 10;
    let articleCurrentPage = 1;
    let articleAllData = [];

    initSelect2();
    loadStores();
    loadTickets();
    loadArticles();
    loadArticleCategories();
    initDragDropThumbnail();


    document.getElementById('btnAddStore').addEventListener('click', () => openStoreModal());
    document.getElementById('closeStoreModal').addEventListener('click', closeStoreModal);
    document.getElementById('cancelStoreBtn').addEventListener('click', closeStoreModal);
    document.getElementById('storeForm').addEventListener('submit', handleStoreSubmit);

    document.getElementById('cancelDeleteBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

    document.getElementById('closeTicketModal').addEventListener('click', closeTicketModal);

    document.getElementById('ticketSearch').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(loadTickets, 300);
    });
    document.getElementById('filterStatus').addEventListener('change', loadTickets);
    document.getElementById('filterKategori').addEventListener('change', loadTickets);
    document.getElementById('filterDateFrom').addEventListener('change', loadTickets);
    document.getElementById('filterDateTo').addEventListener('change', loadTickets);

    document.getElementById('storeModal').addEventListener('click', function(e) {
        if (e.target === this) closeStoreModal();
    });
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
    document.getElementById('ticketModal').addEventListener('click', function(e) {
        if (e.target === this) closeTicketModal();
    });

    function initSelect2() {
        $('.select2-provinsi').select2({
            placeholder: 'Pilih Provinsi',
            allowClear: true,
            dropdownParent: $('#storeModal'),
            width: '100%'
        });

        $('.select2-kota').select2({
            placeholder: 'Pilih Kota/Kabupaten',
            allowClear: true,
            dropdownParent: $('#storeModal'),
            width: '100%'
        });

        $('.select2-kecamatan').select2({
            placeholder: 'Pilih Kecamatan',
            allowClear: true,
            dropdownParent: $('#storeModal'),
            width: '100%'
        });

        $('.select2-kelurahan').select2({
            placeholder: 'Pilih Kelurahan',
            allowClear: true,
            dropdownParent: $('#storeModal'),
            width: '100%'
        });

        loadProvinces();

        $('#provinsiToko').on('change', function() {
            const provinceId = $(this).val();
            loadCities(provinceId);
            $('#kotaToko, #kecamatanToko, #kelurahanToko').val('').trigger('change');
        });

        $('#kotaToko').on('change', function() {
            const cityId = $(this).val();
            loadDistricts(cityId);
            $('#kecamatanToko, #kelurahanToko').val('').trigger('change');
        });

        $('#kecamatanToko').on('change', function() {
            const districtId = $(this).val();
            loadVillages(districtId);
            $('#kelurahanToko').val('').trigger('change');
        });
    }

    async function loadProvinces() {
        try {
            const response = await fetch(`${WILAYAH_API}/provinces.json`);
            const provinces = await response.json();

            const select = document.getElementById('provinsiToko');
            select.innerHTML = '<option value="">Pilih Provinsi</option>';

            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.name;
                option.dataset.name = province.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    async function loadCities(provinceId) {
        if (!provinceId) return;

        try {
            const response = await fetch(`${WILAYAH_API}/regencies/${provinceId}.json`);
            const cities = await response.json();

            const select = document.getElementById('kotaToko');
            select.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';

            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                option.dataset.name = city.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading cities:', error);
        }
    }

    async function loadDistricts(cityId) {
        if (!cityId) return;

        try {
            const response = await fetch(`${WILAYAH_API}/districts/${cityId}.json`);
            const districts = await response.json();

            const select = document.getElementById('kecamatanToko');
            select.innerHTML = '<option value="">Pilih Kecamatan</option>';

            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.id;
                option.textContent = district.name;
                option.dataset.name = district.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }

    async function loadVillages(districtId) {
        if (!districtId) return;

        try {
            const response = await fetch(`${WILAYAH_API}/villages/${districtId}.json`);
            const villages = await response.json();

            const select = document.getElementById('kelurahanToko');
            select.innerHTML = '<option value="">Pilih Kelurahan</option>';

            villages.forEach(village => {
                const option = document.createElement('option');
                option.value = village.id;
                option.textContent = village.name;
                option.dataset.name = village.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading villages:', error);
        }
    }

    async function loadStores() {
        const container = document.getElementById('storeList');

        try {
            const response = await fetch(`${API_BASE}store-location.php?action=list`);
            const result = await response.json();

            if (!result.success || !result.data || result.data.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-12 h-12 bg-[#882426] rounded-xl flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-white text-2xl">store</span>
                        </div>
                        <p class="text-gray-600 font-medium">
                            Belum ada lokasi toko
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            Klik tombol <b>"Tambah Toko"</b> untuk menambahkan
                        </p>
                    </div>
                `;
                return;
            }

            container.innerHTML = result.data.map(store => `
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#882426] backdrop-blur rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-white">store</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">${escapeHtml(store.nama_toko)}</h4>
                                <span class="text-xs px-2 py-0.5 rounded-full ${store.is_active == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                    ${store.is_active == 1 ? 'Aktif' : 'Nonaktif'}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="editStore('${store.id_toko}')" class="p-1.5 hover:bg-gray-200 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-gray-600 text-lg">edit</span>
                            </button>
                            <button onclick="deleteStore('${store.id_toko}')" class="p-1.5 hover:bg-red-100 rounded-lg transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-red-600 text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-gray-400 text-lg flex-shrink-0">location_on</span>
                            <span>${escapeHtml(store.alamat)}, ${escapeHtml(store.kota_kabupaten)}, ${escapeHtml(store.provinsi)}</span>
                        </div>
                        ${store.no_telepon ? `
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400 text-lg">phone</span>
                            <span>${escapeHtml(store.no_telepon)}</span>
                        </div>
                        ` : ''}
                        ${store.jam_buka && store.jam_tutup ? `
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400 text-lg">schedule</span>
                            <span>${store.jam_buka.substring(0,5)} - ${store.jam_tutup.substring(0,5)}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading stores:', error);
            container.innerHTML = `<div class="col-span-full text-center py-12 text-red-500">Error memuat data toko</div>`;
        }
    }

    async function openStoreModal(storeData = null) {
        storeEditMode = !!storeData;
        document.getElementById('storeModalTitle').textContent = storeEditMode ? 'Edit Lokasi Toko' : 'Tambah Lokasi Toko';
        document.getElementById('storeForm').reset();

        document.getElementById('storeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (!storeData) return;

        // 🔹 isi field biasa (cepat)
        document.getElementById('storeId').value = storeData.id_toko;
        document.getElementById('namaToko').value = storeData.nama_toko || '';
        document.getElementById('noTelepon').value = storeData.no_telepon || '';
        document.getElementById('alamatToko').value = storeData.alamat || '';
        document.getElementById('kodePos').value = storeData.kode_pos || '';
        document.getElementById('jamBuka').value = storeData.jam_buka || '';
        document.getElementById('jamTutup').value = storeData.jam_tutup || '';
        document.getElementById('isActive').checked = storeData.is_active == 1;

        // 🔄 LOAD WILAYAH ASYNC (TIDAK BLOK UI)
        loadLocationForEdit(storeData);

    }

    async function loadLocationForEdit(storeData) {
        try {
            const provinces = await fetch(`${WILAYAH_API}/provinces.json`).then(r => r.json());
            const matchedProvince = provinces.find(p => p.name === storeData.provinsi);
            if (!matchedProvince) return;

            $('#provinsiToko').val(matchedProvince.id).trigger('change');

            const cities = await fetch(`${WILAYAH_API}/regencies/${matchedProvince.id}.json`).then(r => r.json());
            const matchedCity = cities.find(c => c.name === storeData.kota_kabupaten);
            if (!matchedCity) return;

            $('#kotaToko').val(matchedCity.id).trigger('change');

            if (!storeData.kecamatan) return;

            const districts = await fetch(`${WILAYAH_API}/districts/${matchedCity.id}.json`).then(r => r.json());
            const matchedDistrict = districts.find(d => d.name === storeData.kecamatan);
            if (!matchedDistrict) return;

            $('#kecamatanToko').val(matchedDistrict.id).trigger('change');

            if (!storeData.kelurahan) return;

            const villages = await fetch(`${WILAYAH_API}/villages/${matchedDistrict.id}.json`).then(r => r.json());
            const matchedVillage = villages.find(v => v.name === storeData.kelurahan);
            if (matchedVillage) {
                $('#kelurahanToko').val(matchedVillage.id).trigger('change');
            }
        } catch (err) {
            console.error('Load wilayah gagal:', err);
        }
    }


    function closeStoreModal() {
        document.getElementById('storeModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('storeForm').reset();
    }

    async function handleStoreSubmit(e) {
        e.preventDefault();

        const formData = new FormData();
        const storeId = document.getElementById('storeId').value;

        if (storeId) {
            formData.append('id_toko', storeId);
            formData.append('action', 'update');
        } else {
            formData.append('action', 'add');
        }

        formData.append('nama_toko', document.getElementById('namaToko').value);
        formData.append('no_telepon', document.getElementById('noTelepon').value);
        formData.append('alamat', document.getElementById('alamatToko').value);
        formData.append('kode_pos', document.getElementById('kodePos').value);
        formData.append('jam_buka', document.getElementById('jamBuka').value);
        formData.append('jam_tutup', document.getElementById('jamTutup').value);
        formData.append('is_active', document.getElementById('isActive').checked ? 1 : 0);

        const provinsiSelect = document.getElementById('provinsiToko');
        const kotaSelect = document.getElementById('kotaToko');
        const kecamatanSelect = document.getElementById('kecamatanToko');
        const kelurahanSelect = document.getElementById('kelurahanToko');

        const provinsiOption = provinsiSelect.options[provinsiSelect.selectedIndex];
        const kotaOption = kotaSelect.options[kotaSelect.selectedIndex];
        const kecamatanOption = kecamatanSelect.options[kecamatanSelect.selectedIndex];
        const kelurahanOption = kelurahanSelect.options[kelurahanSelect.selectedIndex];

        formData.append('provinsi', provinsiOption?.dataset?.name || provinsiOption?.text || '');
        formData.append('kota_kabupaten', kotaOption?.dataset?.name || kotaOption?.text || '');
        formData.append('kecamatan', kecamatanOption?.dataset?.name || kecamatanOption?.text || '');
        formData.append('kelurahan', kelurahanOption?.dataset?.name || kelurahanOption?.text || '');

        try {
            const action = storeId ? 'update' : 'add';
            const response = await fetch(`${API_BASE}store-location.php?action=${action}`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                closeStoreModal();
                loadStores();
                showToast(result.message, 'success');
            } else {
                showToast(result.message || 'Gagal menyimpan data', 'error');
            }
        } catch (error) {
            console.error('Error saving store:', error);
            showToast('Terjadi kesalahan saat menyimpan data', 'error');
        }
    }

    window.editStore = async function(id) {
        try {
            const response = await fetch(`${API_BASE}store-location.php?action=get&id=${id}`);
            const result = await response.json();

            if (result.success && result.data) {
                openStoreModal(result.data);
            } else {
                showToast('Data toko tidak ditemukan', 'error');
            }
        } catch (error) {
            console.error('Error fetching store:', error);
            showToast('Gagal memuat data toko', 'error');
        }
    };

    window.deleteStore = function(id) {
        currentDeleteId = id;
        currentDeleteType = 'store';
        document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menghapus lokasi toko ini? Tindakan ini tidak dapat dibatalkan.';
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentDeleteId = null;
        currentDeleteType = null;
    }

    async function confirmDelete() {
        if (!currentDeleteId || !currentDeleteType) return;

        try {
            let url = '';
            if (currentDeleteType === 'store') {
                url = `${API_BASE}store-location.php?action=delete&id=${currentDeleteId}`;
            } else if (currentDeleteType === 'ticket') {
                url = `${API_BASE}support-ticket.php?action=delete&id=${currentDeleteId}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                closeDeleteModal();
                if (currentDeleteType === 'store') {
                    loadStores();
                } else {
                    loadTickets();
                }
                showToast(result.message, 'success');
            } else {
                showToast(result.message || 'Gagal menghapus data', 'error');
            }
        } catch (error) {
            console.error('Error deleting:', error);
            showToast('Terjadi kesalahan saat menghapus data', 'error');
        }
    }

    async function loadTickets() {
        const tbody = document.getElementById('ticketTableBody');

        const params = new URLSearchParams({
            action: 'list',
            search: document.getElementById('ticketSearch').value,
            status: document.getElementById('filterStatus').value,
            kategori: document.getElementById('filterKategori').value,
            date_from: document.getElementById('filterDateFrom').value,
            date_to: document.getElementById('filterDateTo').value
        });

        try {
            const response = await fetch(`${API_BASE}support-ticket.php?${params}`);
            const result = await response.json();

            if (result.statistics) {
                document.getElementById('statTotal').textContent = result.statistics.total || 0;
                document.getElementById('statOpen').textContent = result.statistics.open || 0;
                document.getElementById('statInProgress').textContent = result.statistics.in_progress || 0;
                document.getElementById('statResolved').textContent = result.statistics.resolved || 0;
            }

            if (!result.success || !result.data || result.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            Tidak ada tiket ditemukan
                        </td>
                    </tr>
                `;
                updateTicketPagination(0);
                return;
            }

            // Store all data and reset to page 1
            ticketAllData = result.data;
            ticketCurrentPage = 1;
            renderTicketPage();
        } catch (error) {
            console.error('Error loading tickets:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-12 text-center text-red-500">Error memuat data tiket</td></tr>`;
        }
    }

    function renderTicketPage() {
        const tbody = document.getElementById('ticketTableBody');
        const offset = (ticketCurrentPage - 1) * ticketPerPage;
        const paginatedData = ticketAllData.slice(offset, offset + ticketPerPage);

        tbody.innerHTML = paginatedData.map(ticket => `
                <tr data-ticket-id="${ticket.id_ticket}" class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-sm font-medium text-gray-900">${ticket.id_ticket}</td>
                    <td class="px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-900">${escapeHtml(ticket.nama_pengaju)}</p>
                            <p class="text-xs text-gray-500">${escapeHtml(ticket.email)}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 max-w-xs truncate" title="${escapeHtml(ticket.subjek)}">${escapeHtml(ticket.subjek)}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full ${getKategoriBadgeClass(ticket.kategori)}">
                            ${escapeHtml(ticket.kategori)}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full font-medium ${getStatusBadgeClass(ticket.status)}">
                            ${ticket.status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">${formatDate(ticket.created_at)}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="viewTicket('${ticket.id_ticket}')" class="p-1.5 hover:bg-gray-200 rounded-lg transition-colors" title="Lihat Detail">
                                <span class="material-symbols-outlined text-gray-600 text-lg">visibility</span>
                            </button>
                            <button onclick="deleteTicket('${ticket.id_ticket}')" class="p-1.5 hover:bg-red-100 rounded-lg transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-red-600 text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        updateTicketPagination();
    }

    function updateTicketPagination() {
        const totalPages = Math.ceil(ticketAllData.length / ticketPerPage);
        const offset = (ticketCurrentPage - 1) * ticketPerPage;
        const endEntry = Math.min(offset + ticketPerPage, ticketAllData.length);
        const startEntry = ticketAllData.length > 0 ? offset + 1 : 0;

        document.getElementById('ticketPaginationInfo').innerHTML = `
            Showing <span class="font-semibold text-gray-800">${startEntry}</span> to <span class="font-semibold text-gray-800">${endEntry}</span> of <span class="font-semibold text-gray-800">${ticketAllData.length}</span> entries
        `;

        const paginationNav = document.getElementById('ticketPaginationNav');
        if (!paginationNav) return;

        let paginationHTML = '';

        if (ticketCurrentPage > 1) {
            paginationHTML += `<button onclick="goToTicketPage(${ticketCurrentPage - 1})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
            </button>`;
        } else {
            paginationHTML += `<button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
            </button>`;
        }

        const startPage = Math.max(1, ticketCurrentPage - 2);
        const endPage = Math.min(totalPages, ticketCurrentPage + 2);

        if (startPage > 1) {
            paginationHTML += `<a onclick="goToTicketPage(1)" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">1</a>`;
            if (startPage > 2) paginationHTML += `<span class="px-2 py-2 text-gray-400">...</span>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === ticketCurrentPage) {
                paginationHTML += `<button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;">${i}</button>`;
            } else {
                paginationHTML += `<a onclick="goToTicketPage(${i})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">${i}</a>`;
            }
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHTML += `<span class="px-2 py-2 text-gray-400">...</span>`;
            paginationHTML += `<a onclick="goToTicketPage(${totalPages})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">${totalPages}</a>`;
        }

        if (ticketCurrentPage < totalPages) {
            paginationHTML += `<button onclick="goToTicketPage(${ticketCurrentPage + 1})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
            </button>`;
        } else {
            paginationHTML += `<button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
            </button>`;
        }

        paginationNav.innerHTML = paginationHTML;
    }

    window.goToTicketPage = function(page) {
        ticketCurrentPage = page;
        renderTicketPage();
    };

    window.changeTicketPerPage = function(value) {
        ticketPerPage = parseInt(value);
        ticketCurrentPage = 1;
        renderTicketPage();
    };

    function updateTicketTableStatus(ticketId, status) {
        const row = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        if (!row) return;

        const badge = row.querySelector('td:nth-child(5) span');
        if (!badge) return;

        badge.textContent = status;
        badge.className = `px-2 py-1 text-xs rounded-full font-medium ${getStatusBadgeClass(status)}`;
    }

    window.viewTicket = async function(id) {
        try {
            const response = await fetch(`${API_BASE}support-ticket.php?action=get&id=${id}`);
            const result = await response.json();

            if (result.success && result.data) {
                const ticket = result.data;
                const replies = result.replies || [];

                document.getElementById('ticketIdDisplay').textContent = ticket.id_ticket;

                const content = document.getElementById('ticketDetailContent');
                content.innerHTML = `
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Ticket Question Card -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-gray-900 leading-tight">
                                            ${escapeHtml(ticket.subjek)}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">person</span>
                                            <b>${escapeHtml(ticket.nama_pengaju)}</b> · ${formatDate(ticket.created_at)}
                                        </p>
                                    </div>
                                    <span data-ticket-status class="px-3 py-1.5 text-xs font-semibold rounded-lg ${getStatusBadgeClass(ticket.status)} shrink-0">
                                        ${ticket.status}
                                    </span>
                                </div>
                                <div class="p-5">
                                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                                        <p>${escapeHtml(ticket.message).replace(/\n/g, '<br>')}</p>
                                    </div>
                                    ${ticket.attachment ? `
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <a href="../../uploads/tickets/${ticket.attachment}" target="_blank" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            <span class="material-symbols-outlined text-lg">attachment</span>
                                            Lihat Lampiran
                                        </a>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>

                            <!-- Riwayat Balasan Section -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#882426]">forum</span>
                                        <h4 class="font-semibold text-gray-900">Riwayat Balasan</h4>
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">${replies.length}</span>
                                    </div>
                                    ${replies.length > 0 ? `
                                    <button onclick="deleteAllReplies('${ticket.id_ticket}')"
                                        class="flex items-center gap-1 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium">
                                        <span class="material-symbols-outlined text-sm">delete_sweep</span>
                                        Hapus Semua
                                    </button>
                                    ` : ''}
                                </div>
                                <div class="p-5 space-y-4 max-h-[400px] overflow-y-auto" id="repliesList">
                                    ${replies.length === 0 ? `
                                        <div class="text-center py-8">
                                            <div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                                                <span class="material-symbols-outlined text-3xl text-gray-400">chat_bubble_outline</span>
                                            </div>
                                            <p class="text-gray-500 text-sm">Belum ada balasan</p>
                                            <p class="text-gray-400 text-xs mt-1">Kirim balasan pertama untuk tiket ini</p>
                                        </div>
                                    ` : replies.map(reply => `
                                        <div data-reply-id="${reply.id_reply}" 
                                             class="rounded-xl p-4 transition-all hover:shadow-sm
                                                    ${reply.id_admin 
                                                        ? 'bg-[#882426]/5 border-l-4 border-l-[#882426] ml-4' 
                                                        : 'bg-blue-50 border-l-4 border-l-blue-400'}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold
                                                                ${reply.id_admin ? 'bg-[#882426]' : 'bg-blue-500'}">
                                                        ${reply.id_admin ? 'A' : 'C'}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-900 text-sm">
                                                            ${reply.id_admin ? (reply.admin_name || 'Admin') : (reply.customer_name || 'Customer')}
                                                        </span>
                                                        ${reply.is_internal_note == 1 ? `
                                                            <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full font-medium">
                                                                Internal Note
                                                            </span>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-500">${formatDate(reply.created_at)}</span>
                                                    ${reply.id_admin ? `
                                                        <button onclick="deleteReply('${reply.id_reply}', '${ticket.id_ticket}')"
                                                            class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"
                                                            title="Hapus balasan">
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                        </button>
                                                    ` : ''}
                                                </div>
                                            </div>
                                            <p class="text-gray-700 text-sm leading-relaxed pl-10">
                                                ${escapeHtml(reply.message).replace(/\n/g, '<br>')}
                                            </p>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>

                            <!-- Kirim Balasan Section -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#882426]">reply</span>
                                    <h4 class="font-semibold text-gray-900">Kirim Balasan</h4>
                                </div>
                                <form id="replyForm" onsubmit="submitReply(event, '${ticket.id_ticket}')" class="p-5">
                                    <textarea
                                        id="replyMessage"
                                        rows="5"
                                        required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all resize-none text-sm"
                                        placeholder="Tulis balasan yang jelas dan membantu pelanggan..."></textarea>
                                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">info</span>
                                        Gunakan bahasa sopan dan ringkas. Balasan dapat ditampilkan ke publik jika dijadikan FAQ.
                                    </p>
                                    <div class="flex flex-wrap items-center justify-between gap-4 mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex flex-wrap items-center gap-4">
                                            <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-[#882426] transition-colors">
                                                <input type="checkbox" id="internalNote" class="w-4 h-4 accent-[#882426] rounded">
                                                <span>Internal Note</span>
                                            </label>
                                            <select id="updateStatusOnReply" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white">
                                                <option value="">Tidak ubah status</option>
                                                <option value="In Progress">Set: In Progress</option>
                                                <option value="Resolved">Set: Resolved</option>
                                                <option value="Closed">Set: Closed</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="px-5 py-2.5 bg-[#882426] text-white rounded-xl hover:bg-[#6d1a1c] transition-all font-medium flex items-center gap-2 shadow-sm hover:shadow-md">
                                            <span class="material-symbols-outlined text-lg">send</span>
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-4">
                            <!-- Informasi Tiket Card -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#882426]">info</span>
                                    <h4 class="font-semibold text-gray-900">Informasi Tiket</h4>
                                </div>
                                <div class="p-5 space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">ID Tiket</span>
                                        <span class="font-mono font-semibold text-[#882426] bg-[#882426]/10 px-2 py-1 rounded">${ticket.id_ticket}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Kategori</span>
                                        <span class="px-2.5 py-1 text-xs rounded-lg font-medium ${getKategoriBadgeClass(ticket.kategori)}">${ticket.kategori}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Prioritas</span>
                                        <span class="px-2.5 py-1 text-xs rounded-lg font-medium ${getPriorityBadgeClass(ticket.priority)}">${ticket.priority}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Dibuat</span>
                                        <span class="text-gray-700">${formatDate(ticket.created_at)}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Update Terakhir</span>
                                        <span class="text-gray-700">${formatDate(ticket.updated_at)}</span>
                                    </div>
                                    <div class="pt-4 mt-2 border-t border-gray-100">
                                        <label class="flex items-start gap-3 text-sm cursor-pointer p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" id="isFaqCheckbox" class="mt-0.5 w-5 h-5 accent-[#882426] rounded">
                                            <div>
                                                <span class="font-medium text-gray-800">Tampilkan sebagai FAQ</span>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    FAQ hanya muncul jika status <b>Resolved</b> atau <b>Closed</b>
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Update Status Card -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#882426]">sync</span>
                                    <h4 class="font-semibold text-gray-900">Update Status</h4>
                                </div>
                                <div class="p-5">
                                    <select id="ticketStatusUpdate" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] text-sm bg-white">
                                        <option value="Open" ${ticket.status === 'Open' ? 'selected' : ''}>Open</option>
                                        <option value="In Progress" ${ticket.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                        <option value="Resolved" ${ticket.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                                        <option value="Closed" ${ticket.status === 'Closed' ? 'selected' : ''}>Closed</option>
                                    </select>
                                    <button onclick="updateTicketStatus('${ticket.id_ticket}')" 
                                            class="w-full mt-3 px-4 py-3 bg-[#882426] text-white rounded-xl hover:bg-[#6d1a1c] transition-all font-medium flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        Update Status
                                    </button>
                                </div>
                            </div>

                            <!-- Kontak Card -->
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#882426]">contact_mail</span>
                                    <h4 class="font-semibold text-gray-900">Kontak</h4>
                                </div>
                                <div class="p-5 space-y-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[#882426]">person</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 text-xs block">Nama</span>
                                            <span class="font-medium text-gray-900">${escapeHtml(ticket.nama_pengaju)}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-blue-600">email</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 text-xs block">Email</span>
                                            <a href="mailto:${ticket.email}" class="text-blue-600 hover:underline font-medium">${escapeHtml(ticket.email)}</a>
                                        </div>
                                    </div>
                                    ${ticket.no_telepon ? `
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-green-600">phone</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 text-xs block">Telepon</span>
                                            <a href="tel:${ticket.no_telepon}" class="text-green-600 hover:underline font-medium">${escapeHtml(ticket.no_telepon)}</a>
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                // ===============================
                // FAQ checkbox & status sync
                // ===============================
                const statusSelect = document.getElementById('ticketStatusUpdate');
                const faqCheckbox = document.getElementById('isFaqCheckbox');

                if (statusSelect && faqCheckbox) {

                    // 🔥 1. SET STATE AWAL DARI DATABASE
                    faqCheckbox.checked = ticket.is_faq == 1;

                    const syncFaqCheckboxState = () => {
                        const allowed = ['Resolved', 'Closed'].includes(statusSelect.value);

                        if (!allowed) {
                            faqCheckbox.checked = false;
                            faqCheckbox.disabled = true;
                        } else {
                            faqCheckbox.disabled = false;

                            // 🔥 JANGAN override nilai dari database
                            // checkbox tetap sesuai ticket.is_faq
                        }
                    };

                    // 🔥 2. JALANKAN SEKALI SAAT MODAL DIBUKA
                    syncFaqCheckboxState();

                    // 🔄 3. SYNC SAAT STATUS BERUBAH
                    statusSelect.addEventListener('change', syncFaqCheckboxState);
                }

                document.getElementById('ticketModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                showToast('Tiket tidak ditemukan', 'error');
            }
        } catch (error) {
            console.error('Error viewing ticket:', error);
            showToast('Gagal memuat detail tiket', 'error');
        }
    };

    window.deleteReply = async function (replyId, ticketId) {
        if (!confirm('Yakin ingin menghapus balasan ini?')) return;

        const response = await fetch(`${API_BASE}support-ticket.php?action=deleteReply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_reply: replyId })
        });

        const result = await response.json();

        if (!result.success) {
            showToast(result.message || 'Terjadi kesalahan', 'error');
            return;
        }

        document.querySelector(`[data-reply-id="${replyId}"]`)?.remove();

        // 🔥 SINKRON STATUS & FAQ DARI SERVER
        const ticket = await refreshTicketMeta(ticketId);

        if (ticket) {
            updateTicketTableStatus(ticketId, ticket.status);
        }

        showToast('Balasan dihapus', 'success');
    };

    window.deleteAllReplies = async function (ticketId) {
        if (!confirm('Yakin ingin menghapus SEMUA balasan tiket ini?')) return;

        const response = await fetch(`${API_BASE}support-ticket.php?action=deleteAllReplies`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ticket: ticketId })
        });

        const result = await response.json();

        if (!result.success) {
            showToast(result.message || 'Gagal menghapus balasan', 'error');
            return;
        }

        document.getElementById('repliesList').innerHTML =
            '<p class="text-gray-500 text-sm">Belum ada balasan</p>';

            updateStatusBadge('In Progress');
            syncStatusSelect('In Progress');
            updateFaqCheckboxState('In Progress', 0);

            // 🔥 UPDATE TABEL
            updateTicketTableStatus(ticketId, 'In Progress');


        showToast('Semua balasan dihapus', 'success');
    };

    async function refreshTicketMeta(ticketId) {
        const response = await fetch(`${API_BASE}support-ticket.php?action=get&id=${ticketId}`);
        const result = await response.json();

        if (!result.success) return null;

        const ticket = result.data;

        updateStatusBadge(ticket.status);
        syncStatusSelect(ticket.status);
        updateFaqCheckboxState(ticket.status, ticket.is_faq);

        return ticket; // 🔥 PENTING
    }    

    async function submitReply(event, ticketId) {
        event.preventDefault();

        const formData = new FormData();
        const isFaq = document.getElementById('isFaqCheckbox')?.checked ? 1 : 0;
        formData.append('is_faq', isFaq);
        formData.append('id_ticket', ticketId);
        formData.append('action', 'reply');
        formData.append('message', document.getElementById('replyMessage').value);
        formData.append(
            'is_internal_note',
            document.getElementById('internalNote')?.checked ? 1 : 0
        );
        formData.append(
            'update_status',
            document.getElementById('updateStatusOnReply')?.value || ''
        );

        // 🔥 INI KUNCI MASALAH KAMU
        formData.append(
            'is_faq',
            document.getElementById('isFaqCheckbox')?.checked ? 1 : 0
        );

        const response = await fetch('../../api/admin/support-ticket.php?action=reply', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        // handle response...
    }    

    window.submitReply = async function (e, ticketId) {
        e.preventDefault();

        const messageEl = document.getElementById('replyMessage');
        const internalNoteEl = document.getElementById('internalNote');
        const updateStatusEl = document.getElementById('updateStatusOnReply');
        const faqCheckbox = document.getElementById('isFaqCheckbox');

        const formData = new FormData();
        formData.append('id_ticket', ticketId);
        formData.append('message', messageEl.value);
        formData.append('is_internal_note', internalNoteEl.checked ? 1 : 0);
        formData.append('update_status', updateStatusEl.value);
        formData.append('is_faq', faqCheckbox?.checked ? 1 : 0);

        try {
            const response = await fetch(`${API_BASE}support-ticket.php?action=reply`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                showToast(result.message || 'Gagal mengirim balasan', 'error');
                return;
            }

            // 🔥 APPEND BALASAN TANPA REFRESH - include id_reply from server
            appendNewReply({
                id_reply: result.id_reply,
                admin_name: 'Admin',
                message: messageEl.value,
                is_internal_note: internalNoteEl.checked ? 1 : 0,
                created_at: new Date().toISOString()
            }, ticketId);

            // 🔄 STATUS AUTO JIKA DIPILIH
            if (updateStatusEl.value) {
                updateStatusBadge(updateStatusEl.value);
                syncStatusSelect(updateStatusEl.value);

                const autoFaq = ['Resolved', 'Closed'].includes(updateStatusEl.value) ? 1 : 0;
                updateFaqCheckboxState(updateStatusEl.value, autoFaq);

                // 🔥 UPDATE TABEL
                updateTicketTableStatus(ticketId, updateStatusEl.value);
            }
            // RESET FORM
            messageEl.value = '';
            internalNoteEl.checked = false;
            updateStatusEl.value = '';

            showToast('Balasan berhasil dikirim', 'success');

        } catch (error) {
            console.error(error);
            showToast('Terjadi kesalahan', 'error');
        }
    };

function appendNewReply(reply, ticketId) {
    const container = document.getElementById('repliesList');
    if (!container) return;

    const emptyState = container.querySelector('.text-center');
    if (emptyState) {
        container.innerHTML = '';
    }

    const div = document.createElement('div');
    div.dataset.replyId = reply.id_reply;
    div.className = 'rounded-xl p-4 transition-all hover:shadow-sm bg-[#882426]/5 border-l-4 border-l-[#882426] ml-4';

    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold bg-[#882426]">
                    A
                </div>
                <div>
                    <span class="font-medium text-gray-900 text-sm">${reply.admin_name || 'Admin'}</span>
                    ${reply.is_internal_note ? `
                        <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full font-medium">
                            Internal Note
                        </span>
                    ` : ''}
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">${formatDate(reply.created_at)}</span>
                <button onclick="deleteReply('${reply.id_reply}', '${ticketId}')"
                    class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors"
                    title="Hapus balasan">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </button>
            </div>
        </div>
        <p class="text-gray-700 text-sm leading-relaxed pl-10">
            ${escapeHtml(reply.message).replace(/\n/g, '<br>')}
        </p>
    `;

    container.appendChild(div);

    const replyCountBadge = document.querySelector('.bg-gray-100.text-gray-600.rounded-full');
    if (replyCountBadge) {
        const currentCount = parseInt(replyCountBadge.textContent) || 0;
        replyCountBadge.textContent = currentCount + 1;
    }
}


    window.updateTicketStatus = async function (ticketId) {
        const statusSelect = document.getElementById('ticketStatusUpdate');
        const faqCheckbox = document.getElementById('isFaqCheckbox');

        const status = statusSelect.value;
        let isFaq = faqCheckbox.checked ? 1 : 0;

        // 🔒 RULE FRONTEND
        if (!['Resolved', 'Closed'].includes(status)) {
            isFaq = 0;
        }

        const response = await fetch(`${API_BASE}support-ticket.php?action=updateStatus`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_ticket: ticketId,
                status,
                is_faq: isFaq
            })
        });

        const result = await response.json();

        if (!result.success) {
            showToast(result.message || 'Gagal update status', 'error');
            return;
        }

        // 🔥 REALTIME SYNC
        updateStatusBadge(status);
        syncStatusSelect(status);
        updateFaqCheckboxState(status, isFaq);
        updateTicketTableStatus(ticketId, status);        

        showToast('Status tiket diperbarui', 'success');
    };

    window.deleteTicket = function(id) {
        currentDeleteId = id;
        currentDeleteType = 'ticket';
        document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menghapus tiket ini? Semua balasan juga akan dihapus.';
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    function updateTicketRowStatus(ticketId, status) {
        const row = document.querySelector(`tr td:first-child:textContent("${ticketId}")`);
        if (!row) return;

        const badge = row.closest('tr')?.querySelector('td:nth-child(5) span');
        if (!badge) return;

        badge.textContent = status;
        badge.className = `px-2 py-1 text-xs rounded-full font-medium ${getStatusBadgeClass(status)}`;
    }    

    function updateStatusBadge(status) {
        const badge = document.querySelector('[data-ticket-status]');
        if (!badge) return;

        badge.textContent = status;
        badge.className = `px-3 py-1 text-xs font-semibold rounded-full ${getStatusBadgeClass(status)}`;
    }    

    function syncStatusSelect(status) {
        const select = document.getElementById('ticketStatusUpdate');
        if (!select) return;
        select.value = status;
    }


    function updateFaqCheckboxState(status, isFaqFromServer = null) {
        const faqCheckbox = document.getElementById('isFaqCheckbox');
        if (!faqCheckbox) return;

        const allowed = ['Resolved', 'Closed'].includes(status);

        if (!allowed) {
            faqCheckbox.checked = false;
            faqCheckbox.disabled = true;
            faqCheckbox.dataset.autoUnchecked = '1';
            return;
        }

        faqCheckbox.disabled = false;

        // 🔥 AUTO CHECK
        if (isFaqFromServer !== null) {
            faqCheckbox.checked = !!isFaqFromServer;
        } else if (faqCheckbox.dataset.autoUnchecked === '1') {
            faqCheckbox.checked = true;
            delete faqCheckbox.dataset.autoUnchecked;
        }
    }    

    function closeTicketModal() {
        document.getElementById('ticketModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'Open': 'bg-yellow-100 text-yellow-700',
            'In Progress': 'bg-blue-100 text-blue-700',
            'Resolved': 'bg-green-100 text-green-700',
            'Closed': 'bg-gray-100 text-gray-700'
        };
        return classes[status] || 'bg-gray-100 text-gray-700';
    }

    function getKategoriBadgeClass(kategori) {
        const classes = {
            'General': 'bg-gray-100 text-gray-700',
            'Garansi & Servis': 'bg-purple-100 text-purple-700',
            'Aktivasi Akun': 'bg-blue-100 text-blue-700',
            'Komplain': 'bg-red-100 text-red-700',
            'Pertanyaan Produk': 'bg-green-100 text-green-700',
            'Status Pesanan': 'bg-orange-100 text-orange-700'
        };
        return classes[kategori] || 'bg-gray-100 text-gray-700';
    }

    function getPriorityBadgeClass(priority) {
        const classes = {
            'Low': 'bg-gray-100 text-gray-700',
            'Medium': 'bg-blue-100 text-blue-700',
            'High': 'bg-orange-100 text-orange-700',
            'Urgent': 'bg-red-100 text-red-700'
        };
        return classes[priority] || 'bg-gray-100 text-gray-700';
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white z-[100] transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-gray-800'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    const BLOG_API = '../../api/blog/';
    let articleEditMode = false;
    let currentDeleteArticleId = null;
    let articleCategories = [];
    let articleDebounceTimer = null;

    loadArticles();
    loadArticleCategories();
    loadArticleCategories();
    initDragDropThumbnail(); // ← Tambahkan baris ini

    document.getElementById('btnAddArticle')?.addEventListener('click', () => openArticleModal());
    document.getElementById('closeArticleModal')?.addEventListener('click', closeArticleModal);
    document.getElementById('cancelArticleBtn')?.addEventListener('click', closeArticleModal);
    document.getElementById('articleForm')?.addEventListener('submit', handleArticleSubmit);
    document.getElementById('btnUploadThumbnail')?.addEventListener('click', () => document.getElementById('thumbnailInput').click());
    // document.getElementById('thumbnailDropZone')?.addEventListener('click', () => document.getElementById('thumbnailInput').click());
    // document.getElementById('thumbnailInput')?.addEventListener('change', handleThumbnailUpload);
    document.getElementById('btnRemoveThumbnail')?.addEventListener('click', removeThumbnail);

    document.getElementById('cancelDeleteArticleBtn')?.addEventListener('click', closeDeleteArticleModal);
    document.getElementById('confirmDeleteArticleBtn')?.addEventListener('click', confirmDeleteArticle);

    document.getElementById('articleSearch')?.addEventListener('input', function() {
        clearTimeout(articleDebounceTimer);
        articleDebounceTimer = setTimeout(loadArticles, 300);
    });
    document.getElementById('filterArticleCategory')?.addEventListener('change', loadArticles);
    document.getElementById('filterArticleStatus')?.addEventListener('change', loadArticles);

    document.getElementById('articleModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeArticleModal();
    });
    document.getElementById('deleteArticleModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteArticleModal();
    });

    async function loadArticleCategories() {
        try {
            const response = await fetch(`${BLOG_API}categories.php?active=1`);
            const result = await response.json();

            if (result.success && result.data) {
                articleCategories = result.data;

                const filterSelect = document.getElementById('filterArticleCategory');
                const formSelect = document.getElementById('articleCategory');

                if (filterSelect) {
                    filterSelect.innerHTML = '<option value="">Semua Kategori</option>';
                    result.data.forEach(cat => {
                        filterSelect.innerHTML += `<option value="${cat.id_category}">${escapeHtml(cat.nama_kategori)}</option>`;
                    });
                }

                if (formSelect) {
                    formSelect.innerHTML = '<option value="">Pilih Kategori</option>';
                    result.data.forEach(cat => {
                        formSelect.innerHTML += `<option value="${cat.id_category}">${escapeHtml(cat.nama_kategori)}</option>`;
                    });
                }
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    async function loadArticles() {
        const tbody = document.getElementById('articleTableBody');
        if (!tbody) return;

        const params = new URLSearchParams({
            search: document.getElementById('articleSearch')?.value || '',
            category: document.getElementById('filterArticleCategory')?.value || '',
            status: document.getElementById('filterArticleStatus')?.value || ''
        });

        try {
            const response = await fetch(`${BLOG_API}list.php?${params}`);
            const result = await response.json();

            let totalViews = 0;
            let publishCount = 0;
            let draftCount = 0;

            if (result.data) {
                result.data.forEach(a => {
                    totalViews += parseInt(a.views) || 0;
                    if (a.status === 'publish') publishCount++;
                    else draftCount++;
                });
            }

            document.getElementById('statTotalArticles').textContent = result.total || 0;
            document.getElementById('statPublished').textContent = publishCount;
            document.getElementById('statDraft').textContent = draftCount;
            document.getElementById('statTotalViews').textContent = totalViews;

            if (!result.success || !result.data || result.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            Tidak ada artikel ditemukan
                        </td>
                    </tr>
                `;
                updateArticlePagination();
                return;
            }

            // Store all data and reset to page 1
            articleAllData = result.data;
            articleCurrentPage = 1;
            renderArticlePage();
        } catch (error) {
            console.error('Error loading articles:', error);
            tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center text-red-500">Error memuat data artikel</td></tr>`;
        }
    }

    function renderArticlePage() {
        const tbody = document.getElementById('articleTableBody');
        const offset = (articleCurrentPage - 1) * articlePerPage;
        const paginatedData = articleAllData.slice(offset, offset + articlePerPage);

        tbody.innerHTML = paginatedData.map(article => `
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-4">
                        <div class="relative group cursor-pointer" ${article.thumbnail ? `onclick="openLightbox('../../uploads/blog/${article.thumbnail}')"` : ''}>
                            <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden shadow-sm border border-gray-100">
                                ${article.thumbnail 
                                    ? `<img src="../../uploads/blog/${article.thumbnail}" class="w-full h-full object-cover transition-transform group-hover:scale-105" alt="" onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full flex items-center justify-center\\'><span class=\\'material-symbols-outlined text-gray-300 text-2xl\\'>image</span></div>'">` 
                                    : `<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-gray-300 text-2xl">image</span></div>`
                                }
                            </div>
                            ${article.thumbnail ? `<div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center"><span class="material-symbols-outlined text-white">zoom_in</span></div>` : ''}
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate max-w-[200px]">${escapeHtml(article.judul)}</p>
                            <p class="text-xs text-gray-400 font-mono">${escapeHtml(article.slug)}</p>
                        </div>
                    </td>
                    <td class="px-4 py-4 hidden lg:table-cell">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">${escapeHtml(article.nama_kategori || '-')}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${article.status === 'publish' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'}">
                            <span class="w-1.5 h-1.5 rounded-full ${article.status === 'publish' ? 'bg-emerald-500' : 'bg-gray-400'} mr-1.5"></span>
                            ${article.status === 'publish' ? 'Published' : 'Draft'}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-gray-600 hidden md:table-cell">${escapeHtml(article.author_name || '-')}</td>
                    <td class="px-4 py-4 text-gray-500 text-sm hidden sm:table-cell">${formatDate(article.created_at)}</td>
                    <td class="px-4 py-4">
                        <span class="font-semibold text-gray-800">${article.views || 0}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editArticle(${article.id_post})" class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            <button onclick="deleteArticle(${article.id_post}, '${escapeHtml(article.judul).replace(/'/g, "\\'")}')" class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        updateArticlePagination();
    }

    function updateArticlePagination() {
        const totalPages = Math.ceil(articleAllData.length / articlePerPage);
        const offset = (articleCurrentPage - 1) * articlePerPage;
        const endEntry = Math.min(offset + articlePerPage, articleAllData.length);
        const startEntry = articleAllData.length > 0 ? offset + 1 : 0;

        document.getElementById('articlePaginationInfo').innerHTML = `
            Showing <span class="font-semibold text-gray-800">${startEntry}</span> to <span class="font-semibold text-gray-800">${endEntry}</span> of <span class="font-semibold text-gray-800">${articleAllData.length}</span> entries
        `;

        const paginationNav = document.getElementById('articlePaginationNav');
        if (!paginationNav) return;

        let paginationHTML = '';

        if (articleCurrentPage > 1) {
            paginationHTML += `<button onclick="goToArticlePage(${articleCurrentPage - 1})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
            </button>`;
        } else {
            paginationHTML += `<button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
            </button>`;
        }

        const startPage = Math.max(1, articleCurrentPage - 2);
        const endPage = Math.min(totalPages, articleCurrentPage + 2);

        if (startPage > 1) {
            paginationHTML += `<a onclick="goToArticlePage(1)" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">1</a>`;
            if (startPage > 2) paginationHTML += `<span class="px-2 py-2 text-gray-400">...</span>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === articleCurrentPage) {
                paginationHTML += `<button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;">${i}</button>`;
            } else {
                paginationHTML += `<a onclick="goToArticlePage(${i})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">${i}</a>`;
            }
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) paginationHTML += `<span class="px-2 py-2 text-gray-400">...</span>`;
            paginationHTML += `<a onclick="goToArticlePage(${totalPages})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium cursor-pointer">${totalPages}</a>`;
        }

        if (articleCurrentPage < totalPages) {
            paginationHTML += `<button onclick="goToArticlePage(${articleCurrentPage + 1})" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
            </button>`;
        } else {
            paginationHTML += `<button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
            </button>`;
        }

        paginationNav.innerHTML = paginationHTML;
    }

    window.goToArticlePage = function(page) {
        articleCurrentPage = page;
        renderArticlePage();
    };

    window.changeArticlePerPage = function(value) {
        articlePerPage = parseInt(value);
        articleCurrentPage = 1;
        renderArticlePage();
    };

    function openArticleModal(articleData = null) {
        articleEditMode = !!articleData;
        thumbnailRemoved = false;
        originalThumbnail = null;

        document.getElementById('articleModalTitle').textContent = articleEditMode ? 'Edit Artikel' : 'Tambah Artikel';
        document.getElementById('articleForm').reset();

        document.getElementById('articleId').value = '';
        document.getElementById('articleTitle').value = '';
        document.getElementById('articleCategory').value = '';
        document.getElementById('articleStatus').value = 'draft';
        document.getElementById('articleExcerpt').value = '';
        document.getElementById('articleContent').value = '';
        document.getElementById('thumbnailFilename').value = '';

        resetThumbnailPreview();

        if (articleData) {
            document.getElementById('articleId').value = articleData.id_post;
            document.getElementById('articleTitle').value = articleData.judul || '';
            document.getElementById('articleCategory').value = articleData.id_category || '';
            document.getElementById('articleStatus').value = articleData.status || 'draft';
            document.getElementById('articleExcerpt').value = articleData.excerpt || '';
            document.getElementById('articleContent').value = articleData.konten || '';

            if (articleData.thumbnail) {
                originalThumbnail = articleData.thumbnail;
                showThumbnailPreview(`../../uploads/blog/${articleData.thumbnail}`, articleData.thumbnail);
            }
        }

        document.getElementById('articleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeArticleModal() {
        document.getElementById('articleModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('articleForm').reset();

        document.getElementById('articleId').value = '';
        document.getElementById('thumbnailFilename').value = '';
        thumbnailRemoved = false;
        originalThumbnail = null;
        resetThumbnailPreview();
    }

    let thumbnailRemoved = false;
    let originalThumbnail = null;

    async function handleThumbnailUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('thumbnail', file);

        try {
            const response = await fetch(`${BLOG_API}upload-thumbnail.php`, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            const result = await response.json();

            if (result.success) {
                showThumbnailPreview(result.url, result.filename);
                thumbnailRemoved = false;
                showToast('Thumbnail berhasil diupload', 'success');
            } else {
                showToast(result.message || 'Gagal upload thumbnail', 'error');
            }
        } catch (error) {
            console.error('Error uploading thumbnail:', error);
            showToast('Terjadi kesalahan saat upload', 'error');
        }

        e.target.value = '';
    }

    function showThumbnailPreview(url, filename) {
        const thumbnailImage = document.getElementById('thumbnailImage');
        const uploadBtn = document.getElementById('uploadThumbnailBtn');
        const previewContainer = document.getElementById('thumbnailPreviewContainer');
        const fileNameEl = document.getElementById('thumbnailFileName');

        document.getElementById('thumbnailFilename').value = filename;

        let imgUrl = url;
        if (filename && !url.startsWith('http') && !url.startsWith('data:') && !url.startsWith('../../')) {
            imgUrl = `../../uploads/blog/${filename}`;
        }

        thumbnailImage.src = imgUrl;
        thumbnailImage.onerror = function() {
            this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150" viewBox="0 0 200 150"%3E%3Crect fill="%23f3f4f6" width="200" height="150"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" fill="%239ca3af" font-family="sans-serif" font-size="14"%3EPreview%3C/text%3E%3C/svg%3E';
        };
        uploadBtn.classList.add('hidden');
        previewContainer.classList.remove('hidden');
        fileNameEl.textContent = filename;
    }

    function resetThumbnailPreview() {
        const thumbnailImage = document.getElementById('thumbnailImage');
        const uploadBtn = document.getElementById('uploadThumbnailBtn');
        const previewContainer = document.getElementById('thumbnailPreviewContainer');
        const thumbnailInput = document.getElementById('thumbnailInput');

        document.getElementById('thumbnailFilename').value = '';
        if (thumbnailInput) thumbnailInput.value = '';
        thumbnailImage.src = '';
        uploadBtn.classList.remove('hidden');
        previewContainer.classList.add('hidden');
    }

    function removeThumbnail() {
        thumbnailRemoved = true;
        resetThumbnailPreview();
        showToast('Thumbnail dihapus. Simpan artikel untuk menyimpan perubahan.', 'info');
    }

    async function handleArticleSubmit(e) {
        e.preventDefault();

        const articleId = document.getElementById('articleId').value;
        const currentThumbnail = document.getElementById('thumbnailFilename').value;

        const data = {
            id_post: articleId || undefined,
            id_category: document.getElementById('articleCategory').value,
            judul: document.getElementById('articleTitle').value,
            excerpt: document.getElementById('articleExcerpt').value,
            konten: document.getElementById('articleContent').value,
            thumbnail: thumbnailRemoved ? null : (currentThumbnail || null),
            remove_thumbnail: thumbnailRemoved && originalThumbnail ? true : false,
            status: document.getElementById('articleStatus').value
        };

        try {
            const url = articleId ? `${BLOG_API}update.php` : `${BLOG_API}create.php`;
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                credentials: 'include'
            });

            const result = await response.json();

            if (result.success) {
                closeArticleModal();
                loadArticles();
                showToast(result.message, 'success');
            } else {
                showToast(result.message || 'Gagal menyimpan artikel', 'error');
            }
        } catch (error) {
            console.error('Error saving article:', error);
            showToast('Terjadi kesalahan saat menyimpan', 'error');
        }
    }

    window.editArticle = async function(id) {
        try {
            const response = await fetch(`${BLOG_API}get.php?id=${id}`);
            const result = await response.json();

            if (result.success && result.data) {
                openArticleModal(result.data);
            } else {
                showToast('Artikel tidak ditemukan', 'error');
            }
        } catch (error) {
            console.error('Error fetching article:', error);
            showToast('Gagal memuat data artikel', 'error');
        }
    };

    window.deleteArticle = function(id, title) {
        currentDeleteArticleId = id;
        document.getElementById('deleteArticleMessage').textContent = `Apakah Anda yakin ingin menghapus artikel "${title}"? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('deleteArticleModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    function closeDeleteArticleModal() {
        document.getElementById('deleteArticleModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentDeleteArticleId = null;
    }

    async function confirmDeleteArticle() {
        if (!currentDeleteArticleId) return;

        try {
            const response = await fetch(`${BLOG_API}delete.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_post: currentDeleteArticleId }),
                credentials: 'include'
            });

            const result = await response.json();

            if (result.success) {
                closeDeleteArticleModal();
                loadArticles();
                showToast(result.message, 'success');
            } else {
                showToast(result.message || 'Gagal menghapus artikel', 'error');
            }
        } catch (error) {
            console.error('Error deleting article:', error);
            showToast('Terjadi kesalahan saat menghapus', 'error');
        }
    }

    function initDragDropThumbnail() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const fileInput = document.getElementById('thumbnailInput');

        if (!dropZone || !fileInput) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-[#882426]', 'bg-[#882426]/5', 'scale-[1.02]');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-[#882426]', 'bg-[#882426]/5', 'scale-[1.02]');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) handleThumbnailFiles(files);
        });

        // dropZone.addEventListener('click', (e) => {
        //     if (!e.target.closest('#thumbnailPreviewContainer')) {
        //         fileInput.click();
        //     }
        // });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleThumbnailFiles(e.target.files);
            }
        });
    }

    async function handleThumbnailFiles(files) {
        const file = files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            showToast('Format tidak valid. Gunakan JPG, PNG, atau WebP', 'error');
            return;
        }

        if (file.size > maxSize) {
            showToast('Ukuran file terlalu besar. Maksimal 5MB', 'error');
            return;
        }

        const uploadBtn = document.getElementById('btnUploadThumbnail');
        const originalHTML = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">refresh</span> Uploading...';
        uploadBtn.disabled = true;

        const formData = new FormData();
        formData.append('thumbnail', file);

        try {
            const response = await fetch(`${BLOG_API}upload-thumbnail.php`, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            const result = await response.json();

            if (result.success) {
                showThumbnailPreview(result.url, result.filename);
                thumbnailRemoved = false;
                showToast('Thumbnail berhasil diupload', 'success');
            } else {
                showToast(result.message || 'Gagal upload thumbnail', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat upload', 'error');
        } finally {
            uploadBtn.innerHTML = originalHTML;
            uploadBtn.disabled = false;
        }

        document.getElementById('thumbnailInput').value = '';
    }
});
