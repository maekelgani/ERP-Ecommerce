<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#882426] rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Pilih Alamat Pengiriman</h2>
                <p class="text-sm text-gray-500">Pilih atau tambah alamat tujuan pengiriman</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <?php if (!empty($addresses)): ?>
            <div class="space-y-4" id="addressList">
                <?php foreach ($addresses as $index => $address):
                    $isDefault = $address['default_alamat'] == 1;
                ?>
                    <label class="address-card block relative cursor-pointer <?= $isDefault ? 'selected' : '' ?>">
                        <input type="radio" name="selected_address" value="<?= htmlspecialchars($address['id_alamat']) ?>"
                            class="sr-only address-radio" <?= $isDefault ? 'checked' : '' ?>
                            data-address='<?= json_encode([
                                                'id_alamat' => $address['id_alamat'],
                                                'label' => $address['label_alamat'] ?? 'Alamat',
                                                'nama_penerima' => $address['nama_penerima'],
                                                'nomor_hp' => $address['nomor_hp'],
                                                'alamat_lengkap' => $address['alamat_lengkap'],
                                                'provinsi' => $address['provinsi'],
                                                'kota' => $address['kota'],
                                                'kecamatan' => $address['kecamatan'],
                                                'kelurahan' => $address['kelurahan'],
                                                'kode_pos' => $address['kode_pos']
                                            ]) ?>'>
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 hover:border-[#882426]/50 <?= $isDefault ? 'border-[#882426] bg-[#882426]/5' : 'border-gray-200' ?>">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="address-check w-6 h-6 rounded-full border-2 flex items-center justify-center mt-0.5 transition-all duration-200 <?= $isDefault ? 'border-[#882426] bg-[#882426]' : 'border-gray-300' ?>">
                                        <svg class="w-4 h-4 text-white <?= $isDefault ? '' : 'hidden' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-bold text-gray-900"><?= htmlspecialchars($address['label_alamat'] ?? 'Alamat') ?></span>
                                            <?php if ($isDefault): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-[#882426] text-white">
                                                    UTAMA
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($address['nama_penerima']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($address['nomor_hp']) ?></p>
                                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                            <?= htmlspecialchars($address['alamat_lengkap']) ?>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?= htmlspecialchars($address['kelurahan'] ?? '') ?>,
                                            <?= htmlspecialchars($address['kecamatan'] ?? '') ?>,
                                            <?= htmlspecialchars($address['kota'] ?? '') ?>,
                                            <?= htmlspecialchars($address['provinsi'] ?? '') ?>
                                            <?= htmlspecialchars($address['kode_pos'] ?? '') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="button" id="btnAddNewAddress" class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:border-[#882426] hover:text-[#882426] transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Alamat Baru
            </button>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Alamat</h3>
                <p class="text-gray-500 mb-6">Tambahkan alamat pengiriman untuk melanjutkan checkout.</p>
                <button type="button" id="btnAddFirstAddress" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Alamat
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="flex justify-end mt-6">
    <button type="button" id="btnToStep2" class="inline-flex items-center gap-2 px-8 py-4 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none" <?= empty($addresses) ? 'disabled' : '' ?>>
        Pilih Pengiriman
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
    </button>
</div>

<div id="addAddressModal" class="address-modal-overlay hidden">
    <div class="address-modal-content">
        <div class="address-modal-header">
            <div class="address-modal-header-icon">
                <span class="material-symbols-outlined">location_on</span>
            </div>
            <div>
                <h3 class="address-modal-title text-gray-600">Tambah Alamat Baru</h3>
                <p class="address-modal-subtitle text-gray-600">Lengkapi informasi alamat pengiriman Anda</p>
            </div>
            <button type="button" class="close-modal address-modal-close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="addAddressForm" class="address-modal-form">
            <div class="address-modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Label Alamat</label>
                        <select name="label_alamat" id="labelAlamatCheckout" class="select2-wilayah w-full">
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                            <option value="Apartemen">Apartemen</option>
                            <option value="Kos">Kos</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Penerima <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_penerima" id="namaPenerimaCheckout" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                placeholder="Nama lengkap penerima">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                            <input type="tel" name="nomor_hp" id="nomorHpCheckout" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="alamat_lengkap" id="alamatLengkapCheckout" required rows="3"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all resize-none"
                            placeholder="Nama jalan, nomor rumah, RT/RW"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                            <select name="provinsi" id="provinsiSelect" required class="select2-wilayah w-full">
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="provinsi_nama" id="provinsiNama">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kota/Kabupaten <span class="text-red-500">*</span></label>
                            <select name="kota" id="kotaSelect" required disabled class="select2-wilayah w-full">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                            <input type="hidden" name="kota_nama" id="kotaNama">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                            <select name="kecamatan" id="kecamatanSelect" required disabled class="select2-wilayah w-full">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" name="kecamatan_nama" id="kecamatanNama">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kelurahan/Desa <span class="text-red-500">*</span></label>
                            <select name="kelurahan" id="kelurahanSelect" required disabled class="select2-wilayah w-full">
                                <option value="">Pilih Kelurahan/Desa</option>
                            </select>
                            <input type="hidden" name="kelurahan_nama" id="kelurahanNama">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_pos" id="kodePosCheckout" required placeholder="12345" maxlength="5"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all">
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-[#882426]/5 to-[#882426]/10 rounded-xl border border-[#882426]/30">
                        <input type="checkbox" name="default_alamat" id="defaultAlamatCheckout" value="1"
                            class="w-5 h-5 text-[#882426] border-gray-300 rounded focus:ring-[#882426]">
                        <label for="defaultAlamatCheckout" class="text-sm font-medium text-gray-700">Jadikan alamat utama</label>
                    </div>
                </div>
            </div>
            <div class="address-modal-footer">
                <button type="button" class="close-modal address-modal-btn address-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button type="submit" class="address-modal-btn address-modal-btn-save">
                    <span class="material-symbols-outlined" id="addAddressIcon">save</span>
                    <span id="addAddressBtnText">Simpan Alamat</span>
                    <svg class="w-5 h-5 animate-spin hidden" id="addAddressSpinner" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>