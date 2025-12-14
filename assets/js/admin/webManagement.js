document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '../../api/admin/';
    const WILAYAH_API = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    
    let currentDeleteId = null;
    let currentDeleteType = null;
    let storeEditMode = false;
    let debounceTimer = null;
    
    initSelect2();
    loadStores();
    loadTickets();
    
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
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-gray-400 text-3xl">store</span>
                        </div>
                        <p class="text-gray-500">Belum ada lokasi toko</p>
                        <p class="text-sm text-gray-400">Klik tombol "Tambah Toko" untuk menambahkan</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = result.data.map(store => `
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center">
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

    function openStoreModal(storeData = null) {
        storeEditMode = !!storeData;
        document.getElementById('storeModalTitle').textContent = storeEditMode ? 'Edit Lokasi Toko' : 'Tambah Lokasi Toko';
        document.getElementById('storeForm').reset();
        
        if (storeData) {
            document.getElementById('storeId').value = storeData.id_toko;
            document.getElementById('namaToko').value = storeData.nama_toko || '';
            document.getElementById('noTelepon').value = storeData.no_telepon || '';
            document.getElementById('alamatToko').value = storeData.alamat || '';
            document.getElementById('kodePos').value = storeData.kode_pos || '';
            document.getElementById('jamBuka').value = storeData.jam_buka || '';
            document.getElementById('jamTutup').value = storeData.jam_tutup || '';
            document.getElementById('isActive').checked = storeData.is_active == 1;
        }
        
        document.getElementById('storeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
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
                return;
            }
            
            tbody.innerHTML = result.data.map(ticket => `
                <tr class="hover:bg-gray-50">
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
        } catch (error) {
            console.error('Error loading tickets:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-12 text-center text-red-500">Error memuat data tiket</td></tr>`;
        }
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
                            <div class="bg-gray-50 rounded-xl p-5">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">${escapeHtml(ticket.subjek)}</h4>
                                        <p class="text-sm text-gray-500">Dari: ${escapeHtml(ticket.nama_pengaju)} (${escapeHtml(ticket.email)})</p>
                                    </div>
                                    <span class="px-3 py-1 text-sm rounded-full font-medium ${getStatusBadgeClass(ticket.status)}">${ticket.status}</span>
                                </div>
                                <div class="prose prose-sm max-w-none text-gray-700">
                                    <p>${escapeHtml(ticket.message).replace(/\n/g, '<br>')}</p>
                                </div>
                                ${ticket.attachment ? `
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <a href="../../uploads/tickets/${ticket.attachment}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline">
                                        <span class="material-symbols-outlined text-lg">attachment</span>
                                        Lihat Lampiran
                                    </a>
                                </div>
                                ` : ''}
                            </div>
                            
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-4">Riwayat Balasan</h4>
                                <div class="space-y-4" id="repliesList">
                                    ${replies.length === 0 ? '<p class="text-gray-500 text-sm">Belum ada balasan</p>' : 
                                    replies.map(reply => `
                                        <div class="bg-white border border-gray-200 rounded-xl p-4 ${reply.id_admin ? 'ml-6 border-l-4 border-l-gray-800' : ''}">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-gray-900">${reply.id_admin ? (reply.admin_name || 'Admin') : (reply.customer_name || 'Customer')}</span>
                                                    ${reply.is_internal_note == 1 ? '<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">Internal Note</span>' : ''}
                                                </div>
                                                <span class="text-xs text-gray-500">${formatDate(reply.created_at)}</span>
                                            </div>
                                            <p class="text-gray-700 text-sm">${escapeHtml(reply.message).replace(/\n/g, '<br>')}</p>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-xl p-5">
                                <h4 class="font-semibold text-gray-900 mb-4">Kirim Balasan</h4>
                                <form id="replyForm" onsubmit="submitReply(event, '${ticket.id_ticket}')">
                                    <textarea id="replyMessage" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 resize-none" placeholder="Tulis balasan..."></textarea>
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 text-sm">
                                                <input type="checkbox" id="internalNote" class="w-4 h-4 text-gray-800 border-gray-300 rounded">
                                                <span>Internal Note</span>
                                            </label>
                                            <select id="updateStatusOnReply" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                                                <option value="">Tidak ubah status</option>
                                                <option value="In Progress">Set: In Progress</option>
                                                <option value="Resolved">Set: Resolved</option>
                                                <option value="Closed">Set: Closed</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-white border border-gray-200 rounded-xl p-5">
                                <h4 class="font-semibold text-gray-900 mb-4">Informasi Tiket</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">ID Tiket</span>
                                        <span class="font-mono font-medium">${ticket.id_ticket}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Kategori</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full ${getKategoriBadgeClass(ticket.kategori)}">${ticket.kategori}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Prioritas</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full ${getPriorityBadgeClass(ticket.priority)}">${ticket.priority}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Dibuat</span>
                                        <span>${formatDate(ticket.created_at)}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Update Terakhir</span>
                                        <span>${formatDate(ticket.updated_at)}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-xl p-5">
                                <h4 class="font-semibold text-gray-900 mb-4">Update Status</h4>
                                <select id="ticketStatusUpdate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="Open" ${ticket.status === 'Open' ? 'selected' : ''}>Open</option>
                                    <option value="In Progress" ${ticket.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                    <option value="Resolved" ${ticket.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                                    <option value="Closed" ${ticket.status === 'Closed' ? 'selected' : ''}>Closed</option>
                                </select>
                                <button onclick="updateTicketStatus('${ticket.id_ticket}')" class="w-full mt-3 px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                                    Update Status
                                </button>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-xl p-5">
                                <h4 class="font-semibold text-gray-900 mb-4">Kontak</h4>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 block">Nama</span>
                                        <span class="font-medium">${escapeHtml(ticket.nama_pengaju)}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Email</span>
                                        <a href="mailto:${ticket.email}" class="text-blue-600 hover:underline">${escapeHtml(ticket.email)}</a>
                                    </div>
                                    ${ticket.no_telepon ? `
                                    <div>
                                        <span class="text-gray-500 block">Telepon</span>
                                        <a href="tel:${ticket.no_telepon}" class="text-blue-600 hover:underline">${escapeHtml(ticket.no_telepon)}</a>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
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

    window.submitReply = async function(e, ticketId) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('id_ticket', ticketId);
        formData.append('message', document.getElementById('replyMessage').value);
        formData.append('is_internal_note', document.getElementById('internalNote').checked ? 1 : 0);
        formData.append('update_status', document.getElementById('updateStatusOnReply').value);
        
        try {
            const response = await fetch(`${API_BASE}support-ticket.php?action=reply`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                viewTicket(ticketId);
                loadTickets();
            } else {
                showToast(result.message || 'Gagal mengirim balasan', 'error');
            }
        } catch (error) {
            console.error('Error submitting reply:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    };

    window.updateTicketStatus = async function(ticketId) {
        const status = document.getElementById('ticketStatusUpdate').value;
        
        const formData = new FormData();
        formData.append('id_ticket', ticketId);
        formData.append('status', status);
        
        try {
            const response = await fetch(`${API_BASE}support-ticket.php?action=updateStatus`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                viewTicket(ticketId);
                loadTickets();
            } else {
                showToast(result.message || 'Gagal update status', 'error');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    };

    window.deleteTicket = function(id) {
        currentDeleteId = id;
        currentDeleteType = 'ticket';
        document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menghapus tiket ini? Semua balasan juga akan dihapus.';
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

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
});
