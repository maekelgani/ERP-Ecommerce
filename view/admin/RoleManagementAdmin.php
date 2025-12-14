<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/RoleRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\RoleRepository;

AuthMiddleware::requireAdminLoginFromView();
AuthMiddleware::requireAnyPermissionFromView(['manage_roles', 'assign_role_permissions']);

$roleRepo = new RoleRepository();
$roles = $roleRepo->getAllRolesWithUserCount();
$permissions = $roleRepo->getAllPermissions();

$canManageRoles = PermissionHelper::canManageRoles();
$canAssignPermissions = PermissionHelper::canAssignRolePermissions();

$pageTitle = "Role & Permission";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?php include '../../components/admin/breadcrumb.php'; ?>

            <div class="mb-8">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Role & Permission</h1>
                        <p class="text-gray-500 mt-1">Kelola role dan hak akses administrator sistem</p>
                    </div>
                    <?php if ($canManageRoles): ?>
                        <button onclick="openAddRoleModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-white font-medium text-sm rounded-lg shadow transition-all duration-300 hover:shadow-lg active:scale-95 whitespace-nowrap" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <span class="material-symbols-outlined text-base">add_circle</span>
                            <span>Tambah Role</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Role List Section -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-100" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="material-symbols-outlined">security</span>
                                Daftar Role
                            </h2>
                            <p class="text-sm text-white/80 mt-1">Total <?= count($roles) ?> role</p>
                        </div>
                        <div class="space-y-2 p-4 max-h-[600px] overflow-y-auto" id="rolesList">
                            <?php foreach ($roles as $role):
                                $isSystemRole = in_array($role['role_name'], ['super_admin', 'admin']);
                            ?>
                                <div class="role-card p-4 rounded-lg border-2 border-gray-200 hover:border-[#882426]/50 cursor-pointer transition-all duration-300 group relative"
                                    data-role-id="<?= $role['id_role'] ?>"
                                    onclick="selectRole(<?= $role['id_role'] ?>, '<?= htmlspecialchars($role['role_name']) ?>')">
                                    <div class="flex justify-between items-start gap-2">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-bold text-gray-900 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $role['role_name'])) ?></h3>
                                                <?php if ($isSystemRole): ?>
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">System</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($role['role_description'] ?? 'Tidak ada deskripsi') ?></p>
                                            <div class="flex gap-3 text-xs">
                                                <span class="flex items-center gap-1 text-gray-500">
                                                    <span class="material-symbols-outlined text-sm">person</span>
                                                    <strong><?= $role['user_count'] ?></strong>
                                                </span>
                                                <span class="flex items-center gap-1 text-gray-500">
                                                    <span class="material-symbols-outlined text-sm">shield</span>
                                                    <strong><?= $role['permission_count'] ?></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <?php if ($canManageRoles && !$isSystemRole): ?>
                                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button onclick="event.stopPropagation(); editRole(<?= $role['id_role'] ?>)" class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors" title="Edit">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                                <button onclick="event.stopPropagation(); deleteRole(<?= $role['id_role'] ?>, '<?= htmlspecialchars($role['role_name']) ?>')" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Permission Section -->
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                        <span class="material-symbols-outlined">vpn_key</span>
                                        Permission untuk Role: <span id="selectedRoleName" class="text-white font-black">-</span>
                                    </h2>
                                    <p class="text-sm text-white/80 mt-1" id="permissionSubtitle">Pilih role untuk melihat dan mengelola permission</p>
                                </div>
                                <?php if ($canAssignPermissions): ?>
                                    <button onclick="savePermissions()" id="savePermBtn" class="inline-flex items-center gap-2 px-4 py-2 text-white font-semibold text-sm rounded-lg shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 active:scale-95 hidden" style="background: #10b981;">
                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                        <span>Simpan Perubahan</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- No Role Selected State -->
                            <div id="noRoleSelected" class="text-center py-16">
                                <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                                    <span class="material-symbols-outlined text-amber-600 text-3xl">touch_app</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Role Dipilih</h3>
                                <p class="text-gray-500">Klik pada role di sebelah kiri untuk melihat dan mengelola permission</p>
                            </div>

                            <!-- Permissions Container -->
                            <div id="permissionsContainer" class="hidden">
                                <input type="hidden" id="selectedRoleId">
                                <div class="space-y-4 max-h-[500px] overflow-y-auto" id="permissionsList">
                                    <?php
                                    $permissionGroups = [];
                                    foreach ($permissions as $perm) {
                                        $prefix = explode('_', $perm['permission_key'])[0];
                                        $permissionGroups[$prefix][] = $perm;
                                    }

                                    $categoryIcons = [
                                        'view' => 'visibility',
                                        'manage' => 'settings',
                                        'assign' => 'admin_panel_settings',
                                        'delete' => 'delete_forever'
                                    ];

                                    foreach ($permissionGroups as $group => $perms):
                                        $groupLabel = str_replace('_', ' ', strtoupper($group));
                                        $icon = $categoryIcons[$group] ?? 'shield';
                                    ?>
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                                <h4 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-base"><?= $icon ?></span>
                                                    <?= $groupLabel ?>
                                                </h4>
                                            </div>
                                            <div class="space-y-1 p-3">
                                                <?php foreach ($perms as $perm): ?>
                                                    <label class="flex items-start gap-3 cursor-pointer hover:bg-gray-50 p-2.5 rounded transition-colors">
                                                        <input type="checkbox" name="permissions[]" value="<?= $perm['id_permission'] ?>"
                                                            class="permission-checkbox w-4 h-4 mt-1 rounded border-gray-300 text-[#882426] focus:ring-[#882426]"
                                                            <?= !$canAssignPermissions ? 'disabled' : '' ?>>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($perm['permission_name']) ?></p>
                                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($perm['permission_key']) ?></p>
                                                        </div>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Role Modal -->
    <div id="roleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeRoleModal()"></div>
        <div class="relative z-10 max-w-lg w-full rounded-2xl bg-white shadow-xl overflow-hidden">
            <div class="p-6 flex items-center justify-between" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                <h2 class="text-xl font-bold text-white" id="roleModalTitle">Tambah Role</h2>
                <button onclick="closeRoleModal()" class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="roleForm" onsubmit="submitRoleForm(event)" class="p-6 space-y-5">
                <input type="hidden" id="roleId" name="id_role">

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">Nama Role <span class="text-red-500">*</span></label>
                    <input type="text" id="roleName" name="role_name" required
                        class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all"
                        placeholder="Contoh: manager, staff, viewer">
                </div>

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">Deskripsi</label>
                    <textarea id="roleDesc" name="role_description" rows="3"
                        class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all"
                        placeholder="Deskripsi singkat tentang role ini"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2.5 border border-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2.5 text-white rounded-lg font-medium transition-all duration-300 hover:shadow-lg active:scale-95" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Role Modal -->
    <div id="deleteRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeDeleteRoleModal()"></div>
        <div class="relative z-10 max-w-sm w-full rounded-2xl border border-gray-100 bg-white shadow-xl p-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-500 text-3xl">error</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Role?</h2>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus role <strong id="deleteRoleName" class="text-gray-900"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <input type="hidden" id="deleteRoleId">
                <div class="flex justify-center gap-3">
                    <button onclick="closeDeleteRoleModal()" class="px-4 py-2.5 border border-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                    <button onclick="confirmDeleteRole()" class="px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal for Permission Update -->
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 max-w-md w-full rounded-2xl bg-white shadow-2xl overflow-hidden">
            <div class="p-6" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Berhasil!</h2>
                    <button onclick="closeSuccessModal()" class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <!-- Success Icon -->
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-4xl animate-pulse">check_circle</span>
                    </div>
                </div>

                <!-- Message -->
                <div class="text-center mb-6">
                    <p class="text-gray-600 text-sm mb-2">Permission telah berhasil diperbarui untuk:</p>
                    <p class="text-xl font-bold text-gray-900 capitalize" id="successRoleName">-</p>
                </div>

                <!-- Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-green-600">shield</span>
                            Total Permission
                        </span>
                        <span class="font-bold text-gray-900" id="successPermissionCount">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-blue-600">schedule</span>
                            Waktu Update
                        </span>
                        <span class="font-semibold text-gray-700 text-sm" id="successUpdateTime">-</span>
                    </div>
                </div>

                <!-- CTA Button -->
                <button onclick="closeSuccessModal()" class="w-full px-4 py-3 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">done_all</span>
                        Selesai
                    </span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../../app/controllers/accessController.php';
        let isEditMode = false;
        let currentRoleId = null;
        let currentRoleName = '';
        let initialPermissionStates = {};
        const canAssignPermissions = <?= $canAssignPermissions ? 'true' : 'false' ?>;

        function checkForChanges() {
            const allCheckboxes = document.querySelectorAll('.permission-checkbox');

            let hasChanges = false;
            allCheckboxes.forEach(cb => {
                const cbId = cb.value;
                const initialState = initialPermissionStates[cbId];
                if (!cb.disabled && cb.checked !== initialState) {
                    hasChanges = true;
                }
            });

            console.log('Changes detected:', hasChanges);
        }

        function selectRole(roleId, roleName) {
            // Toggle behavior - click again to close
            if (currentRoleId === roleId) {
                closePermissions();
                return;
            }

            currentRoleId = roleId;
            currentRoleName = roleName;
            document.getElementById('selectedRoleId').value = roleId;

            // Update selected role name display
            const selectedRoleNameElement = document.getElementById('selectedRoleName');
            const displayName = roleName.replace('_', ' ');
            selectedRoleNameElement.textContent = displayName;

            // Update role card styling
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('border-[#882426]', 'border-4', 'shadow-lg', 'bg-[#882426]/5');
                card.classList.add('border-gray-200', 'border-2');
            });
            const selectedCard = document.querySelector(`[data-role-id="${roleId}"]`);
            selectedCard.classList.remove('border-gray-200', 'border-2');
            selectedCard.classList.add('border-[#882426]', 'border-4', 'shadow-lg', 'bg-[#882426]/5');

            // Update subtitle
            const subtitle = roleName === 'super_admin' ? 'Role system dengan akses penuh ke semua permission' :
                roleName === 'admin' ? 'Role operasional dengan akses ke fitur dasar' :
                'Role kustom dengan permission terbatas';
            document.getElementById('permissionSubtitle').textContent = subtitle;

            // ===== LOGIKA BUTTON: MUNCUL LANGSUNG SAAT ROLE DIPILIH =====
            const saveBtn = document.getElementById('savePermBtn');
            // Button muncul jika:
            // 1. Role sudah dipilih (displayName bukan '-')
            // 2. Bukan super_admin (read-only)
            // 3. User punya permission
            if (displayName !== '-' && roleName !== 'super_admin' && canAssignPermissions) {
                saveBtn.classList.remove('hidden');
            } else {
                saveBtn.classList.add('hidden');
            }

            fetch(`${API_URL}?action=get_role_permissions&id=${roleId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        initialPermissionStates = {};

                        const checkboxes = document.querySelectorAll('.permission-checkbox');
                        checkboxes.forEach(cb => {
                            const isChecked = data.data.permission_ids.includes(parseInt(cb.value));
                            cb.checked = isChecked;
                            initialPermissionStates[cb.value] = isChecked;
                            cb.disabled = !canAssignPermissions || roleName === 'super_admin';
                            cb.onchange = checkForChanges;
                        });

                        document.getElementById('permissionsContainer').classList.remove('hidden');
                        document.getElementById('noRoleSelected').classList.add('hidden');
                    }
                });
        }

        function closePermissions() {
            currentRoleId = null;
            currentRoleName = '';

            // Remove ALL active styling
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('border-[#882426]', 'border-4', 'shadow-lg', 'bg-[#882426]/5', 'ring-2', 'ring-[#882426]', 'bg-gradient-to-r', 'from-[#882426]/10', 'to-[#6d1a1c]/10');
                card.classList.add('border-gray-200', 'border-2');
            });

            // Reset to initial state
            document.getElementById('permissionsContainer').classList.add('hidden');
            document.getElementById('noRoleSelected').classList.remove('hidden');
            document.getElementById('selectedRoleName').textContent = '-';
            document.getElementById('savePermBtn').classList.add('hidden'); // HIDE button
            document.getElementById('permissionSubtitle').textContent = 'Pilih role untuk melihat dan mengelola permission';
        }

        function savePermissions() {
            if (!currentRoleId) return;

            const formData = new FormData();
            formData.append('action', 'update_role_permissions');
            formData.append('id_role', currentRoleId);

            const checkedPermissions = [];
            document.querySelectorAll('.permission-checkbox:checked').forEach(cb => {
                formData.append('permissions[]', cb.value);
                checkedPermissions.push(cb.value);
            });

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const now = new Date();
                        const timeString = now.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        document.getElementById('successRoleName').textContent = currentRoleName.replace('_', ' ');
                        document.getElementById('successPermissionCount').textContent = checkedPermissions.length + ' Permission';
                        document.getElementById('successUpdateTime').textContent = timeString;

                        const modal = document.getElementById('successModal');
                        modal.classList.remove('hidden');

                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            location.reload();
        }

        function openAddRoleModal() {
            isEditMode = false;
            document.getElementById('roleModalTitle').textContent = 'Tambah Role';
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = '';
            document.getElementById('roleModal').classList.remove('hidden');
        }

        function editRole(id) {
            isEditMode = true;
            document.getElementById('roleModalTitle').textContent = 'Edit Role';

            fetch(`${API_URL}?action=get_role&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const role = data.data;
                        document.getElementById('roleId').value = role.id_role;
                        document.getElementById('roleName').value = role.role_name;
                        document.getElementById('roleDesc').value = role.role_description || '';
                        document.getElementById('roleModal').classList.remove('hidden');
                    } else {
                        alert(data.message);
                    }
                });
        }

        function closeRoleModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }

        function submitRoleForm(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('roleForm'));
            formData.append('action', isEditMode ? 'update_role' : 'create_role');

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        function deleteRole(id, name) {
            document.getElementById('deleteRoleId').value = id;
            document.getElementById('deleteRoleName').textContent = name;
            document.getElementById('deleteRoleModal').classList.remove('hidden');
        }

        function closeDeleteRoleModal() {
            document.getElementById('deleteRoleModal').classList.add('hidden');
        }

        function confirmDeleteRole() {
            const id = document.getElementById('deleteRoleId').value;
            const formData = new FormData();
            formData.append('action', 'delete_role');
            formData.append('id_role', id);

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }
    </script>
</body>

</html>