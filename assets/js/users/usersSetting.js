(function () {
    document.addEventListener("DOMContentLoaded", () => {
        initTabNavigation();
        initProfileForm();
        initProfilePhotoUpload();
        initPasswordForm();
        initSetPasswordForm();
        initLogout();
        initPasswordToggle();
        initPasswordStrength();
        initAddressBook();
        initDeactivateAccount();
    });

    function updateAllProfilePhotos(photoUrl) {
        const sidebarImg = document.querySelector('aside .bg-\\[\\#882426\\] img');
        const sidebarInitial = document.querySelector('aside .bg-\\[\\#882426\\] .w-16.h-16:not(img)');
        
        if (sidebarImg) {
            sidebarImg.src = photoUrl;
        } else if (sidebarInitial) {
            const img = document.createElement('img');
            img.src = photoUrl;
            img.alt = 'Profile';
            img.className = 'w-16 h-16 rounded-full object-cover ring-4 ring-white/20';
            sidebarInitial.replaceWith(img);
        }

        const navbarPhoto = document.getElementById('navbarProfilePhoto');
        const navbarInitial = document.getElementById('navbarProfileInitial');
        if (navbarPhoto) {
            navbarPhoto.src = photoUrl;
        } else if (navbarInitial) {
            const img = document.createElement('img');
            img.id = 'navbarProfilePhoto';
            img.src = photoUrl;
            img.alt = 'Profile';
            img.className = 'w-8 h-8 rounded-full object-cover shadow-sm';
            img.setAttribute('data-profile-photo', 'navbar');
            navbarInitial.replaceWith(img);
        }

        const dropdownPhoto = document.getElementById('dropdownProfilePhoto');
        const dropdownInitial = document.getElementById('dropdownProfileInitial');
        if (dropdownPhoto) {
            dropdownPhoto.src = photoUrl;
        } else if (dropdownInitial) {
            const img = document.createElement('img');
            img.id = 'dropdownProfilePhoto';
            img.src = photoUrl;
            img.alt = 'Profile';
            img.className = 'w-12 h-12 rounded-full object-cover ring-2 ring-white/30';
            img.setAttribute('data-profile-photo', 'dropdown');
            dropdownInitial.replaceWith(img);
        }

        const mobileMenuPhoto = document.getElementById('mobileMenuProfilePhoto');
        const mobileMenuInitial = document.getElementById('mobileMenuProfileInitial');
        if (mobileMenuPhoto) {
            mobileMenuPhoto.src = photoUrl;
        } else if (mobileMenuInitial) {
            const img = document.createElement('img');
            img.id = 'mobileMenuProfilePhoto';
            img.src = photoUrl;
            img.alt = 'Profile';
            img.className = 'w-12 h-12 rounded-full object-cover ring-2 ring-white/30';
            img.setAttribute('data-profile-photo', 'mobile');
            mobileMenuInitial.replaceWith(img);
        }
    }

    function resetAllProfilePhotosToInitial() {
        const userName = document.querySelector('aside .bg-\\[\\#882426\\] .text-white.font-bold')?.textContent || 'U';
        const fallbackInitial = userName.charAt(0).toUpperCase();

        const sidebarImg = document.querySelector('aside .bg-\\[\\#882426\\] img');
        if (sidebarImg) {
            const div = document.createElement('div');
            div.className = 'w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-white/20';
            div.textContent = fallbackInitial;
            sidebarImg.replaceWith(div);
        }

        const navbarContainer = document.getElementById('navbarProfileContainer');
        const navbarInitial = navbarContainer?.getAttribute('data-initial') || fallbackInitial;
        const navbarPhoto = document.getElementById('navbarProfilePhoto');
        if (navbarPhoto) {
            const div = document.createElement('div');
            div.id = 'navbarProfileInitial';
            div.className = 'w-8 h-8 rounded-full bg-[#882426] flex items-center justify-center text-white text-sm font-semibold shadow-sm';
            div.setAttribute('data-profile-initial', 'navbar');
            div.textContent = navbarInitial;
            navbarPhoto.replaceWith(div);
        }

        const dropdownContainer = document.getElementById('dropdownProfileContainer');
        const dropdownInitial = dropdownContainer?.getAttribute('data-initial') || fallbackInitial;
        const dropdownPhoto = document.getElementById('dropdownProfilePhoto');
        if (dropdownPhoto) {
            const div = document.createElement('div');
            div.id = 'dropdownProfileInitial';
            div.className = 'w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white text-lg font-bold ring-2 ring-white/30';
            div.setAttribute('data-profile-initial', 'dropdown');
            div.textContent = dropdownInitial;
            dropdownPhoto.replaceWith(div);
        }

        const mobileContainer = document.getElementById('mobileMenuProfileContainer');
        const mobileInitial = mobileContainer?.getAttribute('data-initial') || fallbackInitial;
        const mobileMenuPhoto = document.getElementById('mobileMenuProfilePhoto');
        if (mobileMenuPhoto) {
            const div = document.createElement('div');
            div.id = 'mobileMenuProfileInitial';
            div.className = 'w-12 h-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-lg font-bold ring-2 ring-white/30';
            div.setAttribute('data-profile-initial', 'mobile');
            div.textContent = mobileInitial;
            mobileMenuPhoto.replaceWith(div);
        }
    }

    function initTabNavigation() {
        const navItems = document.querySelectorAll('#settingsNav .nav-item[data-tab]');
        const tabContents = document.querySelectorAll('.tab-content');

        if (!navItems.length || !tabContents.length) return;

        navItems.forEach(item => {
            item.addEventListener('click', () => {
                const targetTab = item.dataset.tab;

                navItems.forEach(navItem => {
                    navItem.classList.remove('active');
                });

                item.classList.add('active');

                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                const targetContent = document.getElementById(`tab-${targetTab}`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    }

    let pendingProfilePhoto = null;
    let originalProfilePhotoSrc = null;
    let hadProfilePhoto = false;
    let pendingPhotoRemoval = false;

    function initProfilePhotoUpload() {
        const fileInput = document.getElementById('profilePhotoInput');
        const preview = document.getElementById('profilePreview');
        const initialDiv = document.getElementById('profileInitial');
        const removeBtn = document.getElementById('removePhotoBtn');

        if (!fileInput) return;

        originalProfilePhotoSrc = preview && !preview.classList.contains('hidden') ? preview.src : null;
        hadProfilePhoto = originalProfilePhotoSrc !== null;
        pendingPhotoRemoval = false;

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!file.type.match(/^image\/(jpeg|png|gif)$/)) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Format file tidak valid. Gunakan JPG, PNG, atau GIF.', 'error');
                }
                fileInput.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Ukuran file terlalu besar. Maksimal 2MB.', 'error');
                }
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                if (preview) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                }
                if (initialDiv) {
                    initialDiv.classList.add('hidden');
                }
                if (removeBtn) {
                    removeBtn.classList.remove('hidden');
                }
            };
            reader.readAsDataURL(file);

            pendingProfilePhoto = file;
            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast('Foto dipilih. Klik "Simpan Perubahan" untuk menyimpan.', 'info', 'Preview Foto');
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (!hadProfilePhoto && !pendingProfilePhoto && !pendingPhotoRemoval) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast('Tidak ada foto profil untuk dihapus', 'warning', 'Info');
                    }
                    return;
                }
                
                if (pendingProfilePhoto && !hadProfilePhoto) {
                    pendingProfilePhoto = null;
                    fileInput.value = '';
                    if (preview) {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }
                    if (initialDiv) {
                        initialDiv.classList.remove('hidden');
                    }
                    removeBtn.classList.add('hidden');
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast('Foto preview dibatalkan', 'success', 'Dibatalkan');
                    }
                    return;
                }
                
                pendingPhotoRemoval = true;
                pendingProfilePhoto = null;
                fileInput.value = '';
                
                if (preview) {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
                if (initialDiv) {
                    initialDiv.classList.remove('hidden');
                }
                removeBtn.classList.add('hidden');
                
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Foto akan dihapus saat Anda klik "Simpan Perubahan"', 'info', 'Foto Dihapus');
                }
            });
        }

        async function removePhoto() {
            const btn = document.getElementById('confirmDeletePhotoBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
            }

            try {
                const response = await fetch('../../api/customer/remove-photo.php', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const text = await response.text();
                let result;
                
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError, 'Response:', text);
                    throw new Error('Server response tidak valid');
                }

                if (result.success) {
                    if (preview) {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }
                    if (initialDiv) {
                        initialDiv.classList.remove('hidden');
                    }
                    if (removeBtn) {
                        removeBtn.classList.add('hidden');
                    }
                    fileInput.value = '';
                    pendingProfilePhoto = null;
                    originalProfilePhotoSrc = null;
                    hadProfilePhoto = false;
                    closeDeletePhotoModal();
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast('Foto profil berhasil dihapus', 'success', 'Foto Dihapus');
                    }
                    resetAllProfilePhotosToInitial();
                } else {
                    closeDeletePhotoModal();
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menghapus foto', 'error');
                    }
                }
            } catch (error) {
                closeDeletePhotoModal();
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan saat menghapus foto', 'error');
                }
                console.error('Remove photo error:', error);
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined">delete</span> Hapus Foto';
                }
            }
        }

        window.openDeletePhotoModal = function() {
            const modal = document.getElementById('deletePhotoModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeDeletePhotoModal = function() {
            const modal = document.getElementById('deletePhotoModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        };

        window.confirmDeletePhoto = function() {
            removePhoto();
        };

        const deletePhotoModal = document.getElementById('deletePhotoModal');
        if (deletePhotoModal) {
            deletePhotoModal.addEventListener('click', function(e) {
                if (e.target === deletePhotoModal) {
                    closeDeletePhotoModal();
                }
            });
        }
    }

    function initProfileForm() {
        const form = document.getElementById('profileForm');
        const resetBtn = document.getElementById('resetProfileBtn');

        if (!form) return;

        const originalValues = {
            nama: document.getElementById('inputNama')?.value || '',
            email: document.getElementById('inputEmail')?.value || '',
            no_telepon: document.getElementById('inputTelepon')?.value || ''
        };

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                document.getElementById('inputNama').value = originalValues.nama;
                document.getElementById('inputEmail').value = originalValues.email;
                document.getElementById('inputTelepon').value = originalValues.no_telepon;
                
                const preview = document.getElementById('profilePreview');
                const initialDiv = document.getElementById('profileInitial');
                const removeBtn = document.getElementById('removePhotoBtn');
                const fileInput = document.getElementById('profilePhotoInput');
                
                pendingProfilePhoto = null;
                pendingPhotoRemoval = false;
                if (fileInput) fileInput.value = '';
                
                if (hadProfilePhoto && originalProfilePhotoSrc) {
                    if (preview) {
                        preview.src = originalProfilePhotoSrc;
                        preview.classList.remove('hidden');
                    }
                    if (initialDiv) initialDiv.classList.add('hidden');
                    if (removeBtn) removeBtn.classList.remove('hidden');
                } else {
                    if (preview) {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }
                    if (initialDiv) initialDiv.classList.remove('hidden');
                    if (removeBtn) removeBtn.classList.add('hidden');
                }
                
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Perubahan dibatalkan', 'warning', 'Dibatalkan');
                }
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const saveBtn = document.getElementById('saveProfileBtn');
            const originalBtnContent = saveBtn.innerHTML;

            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;

            const formData = new FormData();
            formData.append('nama', document.getElementById('inputNama').value.trim());
            formData.append('email', document.getElementById('inputEmail').value.trim());
            formData.append('no_telepon', document.getElementById('inputTelepon').value.trim());

            if (pendingPhotoRemoval) {
                formData.append('delete_photo', 'true');
            } else if (pendingProfilePhoto) {
                formData.append('profile_photo', pendingProfilePhoto);
            }

            try {
                const response = await fetch('../../api/customer/update-profile.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Profil Diperbarui');
                    }

                    const nama = document.getElementById('inputNama').value.trim();
                    const email = document.getElementById('inputEmail').value.trim();
                    const no_telepon = document.getElementById('inputTelepon').value.trim();

                    originalValues.nama = nama;
                    originalValues.email = email;
                    originalValues.no_telepon = no_telepon;

                    const sidebarName = document.querySelector('aside h2');
                    const sidebarEmail = document.querySelector('aside .text-white\\/80');
                    if (sidebarName) sidebarName.textContent = nama;
                    if (sidebarEmail) sidebarEmail.textContent = email;

                    const initial = nama.charAt(0).toUpperCase();
                    const initialDisplay = document.querySelector('aside .w-20.h-20');
                    if (initialDisplay) initialDisplay.textContent = initial;

                    if (result.photo_deleted) {
                        resetAllProfilePhotosToInitial();
                        originalProfilePhotoSrc = null;
                        hadProfilePhoto = false;
                        pendingPhotoRemoval = false;
                        
                        const profilePreview = document.getElementById('profilePreview');
                        const profileInitial = document.getElementById('profileInitial');
                        const removeBtn = document.getElementById('removePhotoBtn');
                        
                        if (profilePreview) {
                            profilePreview.src = '';
                            profilePreview.classList.add('hidden');
                        }
                        if (profileInitial) {
                            profileInitial.classList.remove('hidden');
                        }
                        if (removeBtn) {
                            removeBtn.classList.add('hidden');
                        }
                    } else if (result.photo_url) {
                        updateAllProfilePhotos(result.photo_url);
                        originalProfilePhotoSrc = result.photo_url;
                        hadProfilePhoto = true;
                        pendingPhotoRemoval = false;
                        
                        const profilePreview = document.getElementById('profilePreview');
                        if (profilePreview) {
                            profilePreview.src = result.photo_url;
                        }
                    }

                    pendingProfilePhoto = null;
                    pendingPhotoRemoval = false;
                    document.getElementById('profilePhotoInput').value = '';
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menyimpan perubahan', 'error');
                    }

                    if (result.require_login) {
                        setTimeout(() => {
                            window.location.href = '../../../view/login.php?redirect=' + encodeURIComponent(window.location.href);
                        }, 1500);
                    }
                }
            } catch (error) {
                console.error('Profile update error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnContent;
            }
        });
    }

    function initPasswordForm() {
        const form = document.getElementById('passwordForm');
        const resetBtn = document.getElementById('resetPasswordBtn');

        if (!form) return;

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                form.reset();
                document.getElementById('passwordStrength')?.classList.add('hidden');
                document.getElementById('passwordMatch')?.classList.add('hidden');
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Form direset', 'warning', 'Direset');
                }
            });
        }

        const confirmInput = document.getElementById('confirmPassword');
        const newPasswordInput = document.getElementById('newPassword');

        if (confirmInput && newPasswordInput) {
            confirmInput.addEventListener('input', () => {
                const matchEl = document.getElementById('passwordMatch');
                if (!matchEl) return;

                if (confirmInput.value === '') {
                    matchEl.classList.add('hidden');
                    return;
                }

                matchEl.classList.remove('hidden');
                if (confirmInput.value === newPasswordInput.value) {
                    matchEl.textContent = 'Password cocok';
                    matchEl.className = 'text-sm mt-1 text-green-600';
                } else {
                    matchEl.textContent = 'Password tidak cocok';
                    matchEl.className = 'text-sm mt-1 text-red-600';
                }
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Konfirmasi password tidak cocok', 'error');
                }
                return;
            }

            if (newPassword.length < 8) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Password baru minimal 8 karakter', 'error');
                }
                return;
            }

            const saveBtn = document.getElementById('savePasswordBtn');
            const originalBtnContent = saveBtn.innerHTML;

            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mengubah...</span>
            `;

            try {
                const response = await fetch('../../api/customer/change-password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Password Diubah');
                    }
                    form.reset();
                    document.getElementById('passwordStrength')?.classList.add('hidden');
                    document.getElementById('passwordMatch')?.classList.add('hidden');
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal mengubah password', 'error');
                    }

                    if (result.require_login) {
                        setTimeout(() => {
                            window.location.href = '/view/login-customer.php?redirect=' + encodeURIComponent(window.location.href);
                        }, 1500);
                    }
                }
            } catch (error) {
                console.error('Password change error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnContent;
            }
        });
    }

    function initSetPasswordForm() {
        const form = document.getElementById('setPasswordForm');

        if (!form) return;

        const confirmInput = document.getElementById('confirmPassword');
        const newPasswordInput = document.getElementById('newPassword');

        if (confirmInput && newPasswordInput) {
            confirmInput.addEventListener('input', () => {
                const matchEl = document.getElementById('passwordMatch');
                if (!matchEl) return;

                if (confirmInput.value === '') {
                    matchEl.classList.add('hidden');
                    return;
                }

                matchEl.classList.remove('hidden');
                if (confirmInput.value === newPasswordInput.value) {
                    matchEl.textContent = 'Password cocok';
                    matchEl.className = 'text-sm mt-1 text-green-600';
                } else {
                    matchEl.textContent = 'Password tidak cocok';
                    matchEl.className = 'text-sm mt-1 text-red-600';
                }
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Konfirmasi password tidak cocok', 'error');
                }
                return;
            }

            if (newPassword.length < 8) {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Password minimal 8 karakter', 'error');
                }
                return;
            }

            const saveBtn = document.getElementById('setPasswordBtn');
            const originalBtnContent = saveBtn.innerHTML;

            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;

            try {
                const response = await fetch('../../api/customer/set-password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Password Ditambahkan');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menambahkan password', 'error');
                    }

                    if (result.require_login) {
                        setTimeout(() => {
                            window.location.href = '../../../view/login.php?redirect=' + encodeURIComponent(window.location.href);
                        }, 1500);
                    }
                }
            } catch (error) {
                console.error('Set password error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnContent;
            }
        });
    }

    function initPasswordToggle() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.previousElementSibling;
                const icon = btn.querySelector('.material-symbols-outlined');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = 'visibility';
                } else {
                    input.type = 'password';
                    icon.textContent = 'visibility_off';
                }
            });
        });
    }

    function initPasswordStrength() {
        const passwordInput = document.getElementById('newPassword');
        const strengthContainer = document.getElementById('passwordStrength');
        const strengthBars = document.querySelectorAll('.strength-bar');
        const strengthText = document.getElementById('strengthText');

        if (!passwordInput || !strengthContainer) return;

        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;

            if (password.length === 0) {
                strengthContainer.classList.add('hidden');
                return;
            }

            strengthContainer.classList.remove('hidden');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
            const texts = ['Sangat lemah', 'Lemah', 'Cukup kuat', 'Kuat'];

            strengthBars.forEach((bar, index) => {
                bar.classList.remove('bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500');
                if (index < strength) {
                    bar.classList.add(colors[strength - 1]);
                } else {
                    bar.classList.add('bg-gray-200');
                }
            });

            strengthText.textContent = texts[strength - 1] || 'Sangat lemah';
            strengthText.className = 'text-xs ' + (strength <= 1 ? 'text-red-600' : strength === 2 ? 'text-orange-600' : strength === 3 ? 'text-yellow-600' : 'text-green-600');
        });
    }

    function initLogout() {
        const logoutBtn = document.getElementById('logoutBtn');

        if (!logoutBtn) return;

        logoutBtn.addEventListener('click', () => {
            document.getElementById('logoutModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        const logoutModal = document.getElementById('logoutModal');
        if (logoutModal) {
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    closeLogoutModal();
                }
            });
        }
    }

    window.openLogoutModal = function() {
        document.getElementById('logoutModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeLogoutModal = function() {
        document.getElementById('logoutModal').classList.remove('show');
        document.body.style.overflow = '';
    };

    window.confirmLogout = async function() {
        const confirmBtn = document.getElementById('confirmLogoutBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        try {
            const response = await fetch('../../api/auth/logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const text = await response.text();
            let result;
            
            try {
                result = JSON.parse(text);
            } catch (parseError) {
                console.error('JSON parse error:', parseError, 'Response:', text);
                result = { success: true, redirect: '/view/users/landingPage.php' };
            }

            if (result.success) {
                closeLogoutModal();
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Logout berhasil. Mengarahkan...', 'success', 'Logout');
                }
                setTimeout(() => {
                    window.location.href = result.redirect || '/view/users/landingPage.php';
                }, 1000);
            } else {
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast(result.message || 'Gagal logout', 'error');
                }
                closeLogoutModal();
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span>Keluar</span>';
            }
        } catch (error) {
            console.error('Logout error:', error);
            closeLogoutModal();
            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast('Logout berhasil. Mengarahkan...', 'success', 'Logout');
            }
            setTimeout(() => {
                window.location.href = '/view/users/landingPage.php';
            }, 1000);
        }
    };

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastContent = document.getElementById('toastContent');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        if (!toast || !toastContent || !toastIcon || !toastMessage) return;

        toastContent.className = 'flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border';

        switch (type) {
            case 'success':
                toastContent.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
                toastIcon.textContent = 'check_circle';
                toastIcon.className = 'material-symbols-outlined text-xl text-green-600';
                break;
            case 'error':
                toastContent.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
                toastIcon.textContent = 'error';
                toastIcon.className = 'material-symbols-outlined text-xl text-red-600';
                break;
            case 'info':
                toastContent.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-800');
                toastIcon.textContent = 'info';
                toastIcon.className = 'material-symbols-outlined text-xl text-blue-600';
                break;
            case 'warning':
                toastContent.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-800');
                toastIcon.textContent = 'warning';
                toastIcon.className = 'material-symbols-outlined text-xl text-amber-600';
                break;
        }

        toastMessage.textContent = message;

        toast.classList.remove('translate-y-full', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 3000);
    }

    function initAddressBook() {
        const addBtn = document.getElementById('addAddressBtn');
        const addressList = document.getElementById('addressList');
        const addressForm = document.getElementById('addressForm');

        if (!addressList) return;

        loadAddresses();

        if (addBtn) {
            addBtn.addEventListener('click', () => openAddressModal());
        }

        if (addressForm) {
            addressForm.addEventListener('submit', handleAddressSubmit);
        }

        async function loadAddresses() {
            try {
                const response = await fetch('../../api/customer/address-book.php');
                const result = await response.json();

                if (result.success) {
                    renderAddresses(result.data);
                } else {
                    addressList.innerHTML = `
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">Gagal memuat alamat</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Load addresses error:', error);
                addressList.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Terjadi kesalahan saat memuat alamat</p>
                    </div>
                `;
            }
        }

        function renderAddresses(addresses) {
            if (!addresses || addresses.length === 0) {
                addressList.innerHTML = `
                    <div onclick="openAddressModal()" class="col-span-full p-8 rounded-xl border-2 border-dashed border-gray-200 hover:border-[#882426]/50 hover:bg-[#882426]/5 cursor-pointer transition-all flex flex-col items-center justify-center min-h-[200px] group">
                        <div class="w-16 h-16 bg-gray-100 group-hover:bg-[#882426]/10 rounded-xl flex items-center justify-center mb-4 transition-colors">
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-[#882426] text-3xl transition-colors">add_location</span>
                        </div>
                        <p class="font-semibold text-gray-900">Tambah Alamat Pertama</p>
                        <p class="text-sm text-gray-500 mt-1 text-center">Anda belum memiliki alamat tersimpan</p>
                    </div>
                `;
                return;
            }

            let html = '';
            addresses.forEach(addr => {
                const isDefault = addr.default_alamat == 1;
                const iconMap = {
                    'Rumah': 'home',
                    'Kantor': 'business',
                    'Apartemen': 'apartment',
                    'Kos': 'cottage',
                    'Lainnya': 'location_on'
                };
                const icon = iconMap[addr.label_alamat] || 'location_on';

                html += `
                    <div class="relative p-5 rounded-xl border-2 ${isDefault ? 'border-[#882426] bg-[#882426]/5' : 'border-gray-200 bg-white hover:border-gray-300'} transition-all">
                        ${isDefault ? `
                            <div class="absolute top-4 right-4">
                                <span class="px-2.5 py-1 bg-[#882426] text-white text-xs font-bold rounded-lg">UTAMA</span>
                            </div>
                        ` : ''}
                        <div class="flex gap-4">
                            <div class="w-12 h-12 ${isDefault ? 'bg-[#882426]/10' : 'bg-gray-100'} rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined ${isDefault ? 'text-[#882426]' : 'text-gray-500'}">${icon}</span>
                            </div>
                            <div class="flex-1 min-w-0 pt-1 ${isDefault ? 'pr-20' : ''}">
                                <p class="font-bold text-gray-900">${escapeHtml(addr.label_alamat || 'Alamat')}</p>
                                <p class="text-sm text-gray-800 font-medium mt-1">${escapeHtml(addr.nama_penerima)}</p>
                                <p class="text-sm text-gray-600 mt-1 leading-relaxed">${escapeHtml(addr.alamat_lengkap)}</p>
                                <p class="text-sm text-gray-600">${formatWilayah(addr)}</p>
                                <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">phone</span>
                                    ${escapeHtml(addr.nomor_hp)}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-5">
                            <button onclick="openAddressDetailModal('${addr.id_alamat}')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                Detail
                            </button>
                            <button onclick="openAddressModal('${addr.id_alamat}')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">edit</span>
                                Edit
                            </button>
                            <button onclick="openDeleteAddressModal('${addr.id_alamat}')" class="px-4 py-2.5 border border-gray-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-all flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                        ${!isDefault ? `
                        <div class="mt-2">
                            <button onclick="setDefaultAddress('${addr.id_alamat}')" class="w-full px-4 py-2 border border-[#882426] text-[#882426] rounded-lg text-sm font-medium hover:bg-[#882426]/5 transition-all flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                                Jadikan Alamat Utama
                            </button>
                        </div>
                        ` : ''}
                    </div>
                `;
            });

            html += `
                <div onclick="openAddressModal()" class="p-5 rounded-xl border-2 border-dashed border-gray-200 hover:border-[#882426]/50 hover:bg-[#882426]/5 cursor-pointer transition-all flex flex-col items-center justify-center min-h-[200px] group">
                    <div class="w-14 h-14 bg-gray-100 group-hover:bg-[#882426]/10 rounded-xl flex items-center justify-center mb-3 transition-colors">
                        <span class="material-symbols-outlined text-gray-400 group-hover:text-[#882426] text-2xl transition-colors">add_location</span>
                    </div>
                    <p class="font-medium text-gray-900">Tambah Alamat Baru</p>
                    <p class="text-sm text-gray-500 mt-1 text-center">Tambahkan alamat pengiriman lainnya</p>
                </div>
            `;

            addressList.innerHTML = html;
        }

        async function handleAddressSubmit(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.default_alamat = document.getElementById('defaultAlamat').checked ? 1 : 0;
            
            if (typeof window.getWilayahFormData === 'function') {
                const wilayah = window.getWilayahFormData();
                data.provinsi = wilayah.provinsi || data.provinsi_name || '';
                data.kota = wilayah.kota || data.kota_name || '';
                data.kecamatan = wilayah.kecamatan || data.kecamatan_name || '';
                data.kelurahan = wilayah.kelurahan || data.kelurahan_name || '';
            }
            
            delete data.provinsi_name;
            delete data.kota_name;
            delete data.kecamatan_name;
            delete data.kelurahan_name;

            const addressId = document.getElementById('addressId').value;
            const action = addressId ? 'update' : 'add';

            const saveBtn = document.getElementById('saveAddressBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="animate-spin material-symbols-outlined text-xl">refresh</span> Menyimpan...';

            try {
                const response = await fetch(`../../api/customer/address-book.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Alamat Tersimpan');
                    }
                    closeAddressModal();
                    loadAddresses();
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menyimpan alamat', 'error');
                    }
                }
            } catch (error) {
                console.error('Save address error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<span class="material-symbols-outlined text-xl">save</span><span id="saveAddressBtnText">Simpan</span>';
            }
        }

        window.addressesData = [];

        window.openAddressModal = async function(addressId = null) {
            const modal = document.getElementById('addressModal');
            const form = document.getElementById('addressForm');
            const title = document.getElementById('addressModalTitle');
            const idField = document.getElementById('addressId');

            form.reset();
            idField.value = '';
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            if (addressId) {
                title.textContent = 'Edit Alamat';
                idField.value = addressId;

                try {
                    const response = await fetch('../../api/customer/address-book.php');
                    const result = await response.json();
                    if (result.success) {
                        const address = result.data.find(a => String(a.id_alamat) === String(addressId));
                        if (address) {
                            console.log('Loading address for edit:', address);
                            document.getElementById('labelAlamat').value = address.label_alamat || 'Rumah';
                            document.getElementById('namaPenerima').value = address.nama_penerima || '';
                            document.getElementById('nomorHp').value = address.nomor_hp || '';
                            document.getElementById('alamatLengkap').value = address.alamat_lengkap || '';
                            document.getElementById('kodePos').value = address.kode_pos || '';
                            document.getElementById('defaultAlamat').checked = address.default_alamat == 1;
                            
                            if (typeof window.setAddressFormWilayahForEdit === 'function') {
                                window.setAddressFormWilayahForEdit({
                                    provinsi: address.provinsi || '',
                                    kota: address.kota || '',
                                    kecamatan: address.kecamatan || '',
                                    kelurahan: address.kelurahan || ''
                                });
                            }
                        }
                    }
                } catch (error) {
                    console.error('Load address error:', error);
                }
            } else {
                title.textContent = 'Tambah Alamat Baru';
                if (typeof window.resetAddressFormWilayah === 'function') {
                    window.resetAddressFormWilayah();
                }
                if (typeof window.initAddressWilayah === 'function') {
                    window.initAddressWilayah();
                }
            }
        };

        window.closeAddressModal = function() {
            const modal = document.getElementById('addressModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        };

        window.openAddressDetailModal = async function(addressId) {
            const modal = document.getElementById('addressDetailModal');
            if (!modal) return;
            
            try {
                const response = await fetch('../../api/customer/address-book.php');
                const result = await response.json();
                if (result.success) {
                    const address = result.data.find(a => String(a.id_alamat) === String(addressId));
                    if (address) {
                        const iconMap = {
                            'Rumah': 'home',
                            'Kantor': 'business',
                            'Apartemen': 'apartment',
                            'Kos': 'cottage',
                            'Lainnya': 'location_on'
                        };
                        const icon = iconMap[address.label_alamat] || 'location_on';
                        const isDefault = address.default_alamat == 1;
                        
                        document.getElementById('detailLabelIcon').innerHTML = `<span class="material-symbols-outlined text-[#882426] text-2xl">${icon}</span>`;
                        document.getElementById('detailLabelText').textContent = address.label_alamat || 'Alamat';
                        document.getElementById('detailDefaultBadge').classList.toggle('hidden', !isDefault);
                        document.getElementById('detailNamaPenerima').textContent = address.nama_penerima || '-';
                        document.getElementById('detailNomorHp').textContent = address.nomor_hp || '-';
                        document.getElementById('detailAlamatLengkap').textContent = address.alamat_lengkap || '-';
                        document.getElementById('detailKelurahan').textContent = address.kelurahan || '-';
                        document.getElementById('detailKecamatan').textContent = address.kecamatan || '-';
                        document.getElementById('detailKota').textContent = address.kota || '-';
                        document.getElementById('detailProvinsi').textContent = address.provinsi || '-';
                        document.getElementById('detailKodePos').textContent = address.kode_pos || '-';
                        
                        document.getElementById('detailEditBtn').onclick = function() {
                            closeAddressDetailModal();
                            openAddressModal(addressId);
                        };
                    }
                }
            } catch (error) {
                console.error('Load address detail error:', error);
            }
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        };

        window.closeAddressDetailModal = function() {
            const modal = document.getElementById('addressDetailModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        };

        const addressModalOverlay = document.getElementById('addressModal');
        if (addressModalOverlay) {
            addressModalOverlay.addEventListener('click', function(e) {
                if (e.target === addressModalOverlay) {
                    closeAddressModal();
                }
            });
        }

        const addressDetailModalOverlay = document.getElementById('addressDetailModal');
        if (addressDetailModalOverlay) {
            addressDetailModalOverlay.addEventListener('click', function(e) {
                if (e.target === addressDetailModalOverlay) {
                    closeAddressDetailModal();
                }
            });
        }

        window.openDeleteAddressModal = function(addressId) {
            const modal = document.getElementById('deleteAddressModal');
            document.getElementById('deleteAddressId').value = addressId;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        };

        window.closeDeleteAddressModal = function() {
            const modal = document.getElementById('deleteAddressModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        };

        window.confirmDeleteAddress = async function() {
            const addressId = document.getElementById('deleteAddressId').value;
            const btn = document.getElementById('confirmDeleteAddressBtn');

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;

            try {
                const response = await fetch('../../api/customer/address-book.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_alamat: addressId })
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Alamat Dihapus');
                    }
                    closeDeleteAddressModal();
                    loadAddresses();
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menghapus alamat', 'error');
                    }
                }
            } catch (error) {
                console.error('Delete address error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined">delete</span> Hapus Alamat';
            }
        };

        const deleteAddressModal = document.getElementById('deleteAddressModal');
        if (deleteAddressModal) {
            deleteAddressModal.addEventListener('click', function(e) {
                if (e.target === deleteAddressModal) {
                    closeDeleteAddressModal();
                }
            });
        }

        window.setDefaultAddress = async function(addressId) {
            try {
                const response = await fetch('../../api/customer/address-book.php?action=set-default', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_alamat: addressId })
                });

                const result = await response.json();

                if (result.success) {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message, 'success', 'Alamat Utama');
                    }
                    loadAddresses();
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal mengubah alamat utama', 'error');
                    }
                }
            } catch (error) {
                console.error('Set default address error:', error);
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            }
        };

        function formatWilayah(addr) {
            const isValidName = (val) => {
                if (!val || val.trim() === '') return false;
                if (/^\d+$/.test(val.trim())) return false;
                if (val.includes('Pilih') || val.includes('Memuat')) return false;
                return true;
            };
            
            const parts = [];
            if (isValidName(addr.kelurahan)) parts.push(escapeHtml(addr.kelurahan));
            if (isValidName(addr.kecamatan)) parts.push(escapeHtml(addr.kecamatan));
            if (isValidName(addr.kota)) parts.push(escapeHtml(addr.kota));
            if (isValidName(addr.provinsi)) parts.push(escapeHtml(addr.provinsi));
            if (addr.kode_pos && addr.kode_pos.trim() !== '') parts.push(escapeHtml(addr.kode_pos));
            
            if (parts.length === 0) {
                if (addr.kode_pos && addr.kode_pos.trim() !== '') {
                    return 'Kode Pos: ' + escapeHtml(addr.kode_pos);
                }
                return '<span class="text-gray-400 italic">Wilayah belum lengkap</span>';
            }
            
            return parts.join(', ');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    function initDeactivateAccount() {
        const deactivateBtn = document.getElementById('deactivateAccountBtn');
        const confirmInput = document.getElementById('deactivateConfirmInput');
        const confirmBtn = document.getElementById('confirmDeactivateBtn');

        if (!deactivateBtn) return;

        deactivateBtn.addEventListener('click', () => {
            openDeactivateModal();
        });

        if (confirmInput) {
            confirmInput.addEventListener('input', () => {
                confirmBtn.disabled = confirmInput.value !== 'NONAKTIFKAN';
            });
        }

        window.openDeactivateModal = function() {
            const modal = document.getElementById('deactivateModal');
            if (confirmInput) confirmInput.value = '';
            if (confirmBtn) confirmBtn.disabled = true;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        };

        window.closeDeactivateModal = function() {
            const modal = document.getElementById('deactivateModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        };
        
        const deactivateModal = document.getElementById('deactivateModal');
        if (deactivateModal) {
            deactivateModal.addEventListener('click', function(e) {
                if (e.target === deactivateModal) {
                    closeDeactivateModal();
                }
            });
        }

        window.confirmDeactivate = async function() {
            const confirmText = confirmInput.value;

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="animate-spin material-symbols-outlined">refresh</span> Memproses...';

            try {
                const response = await fetch('../../api/customer/deactivate-account.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ confirm_text: confirmText })
                });

                const text = await response.text();
                let result;
                
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError, 'Response:', text);
                    result = { success: true, redirect: '/view/users/landingPage.php' };
                }

                if (result.success) {
                    closeDeactivateModal();
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Akun berhasil dinonaktifkan', 'success', 'Akun Dinonaktifkan');
                    }
                    setTimeout(() => {
                        window.location.href = 'landingPage.php';
                    }, 1500);
                } else {
                    if (typeof window.showCustomToast === 'function') {
                        window.showCustomToast(result.message || 'Gagal menonaktifkan akun', 'error');
                    }
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined text-xl">block</span> Nonaktifkan';
                }
            } catch (error) {
                console.error('Deactivate error:', error);
                closeDeactivateModal();
                if (typeof window.showCustomToast === 'function') {
                    window.showCustomToast('Terjadi kesalahan', 'error');
                }
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span class="material-symbols-outlined text-xl">block</span> Nonaktifkan';
            }
        };
    }
})();
