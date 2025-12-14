<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/RoleRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\AdminRepository;
use App\Auth\SessionManager;
use App\Repository\RoleRepository;

header('Content-Type: application/json');

AuthMiddleware::checkAdminLoginJson();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$adminRepo = new AdminRepository();
$roleRepo = new RoleRepository();

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_all_users':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $users = $adminRepo->getAllAdminsWithRoles();
            $response = ['success' => true, 'data' => $users];
            break;

        case 'get_user':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $userId = (int)($_GET['id'] ?? 0);
            if ($userId) {
                $user = $adminRepo->getAdminWithRole($userId);
                if ($user) {
                    unset($user['password_hash'], $user['remember_token'], $user['remember_expires']);
                    $response = ['success' => true, 'data' => $user];
                } else {
                    $response = ['success' => false, 'message' => 'User tidak ditemukan'];
                }
            }
            break;

        case 'create_user':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $nama = trim($_POST['nama_lengkap'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = (int)($_POST['id_role'] ?? 2);
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

            if (empty($nama) || empty($email) || empty($password)) {
                $response = ['success' => false, 'message' => 'Nama, email, dan password wajib diisi'];
                break;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Format email tidak valid'];
                break;
            }

            if ($adminRepo->emailExists($email)) {
                $response = ['success' => false, 'message' => 'Email sudah terdaftar'];
                break;
            }

            if (strlen($password) < 6) {
                $response = ['success' => false, 'message' => 'Password minimal 6 karakter'];
                break;
            }

            $userId = $adminRepo->createAdmin([
                'nama_lengkap' => $nama,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'id_role' => $roleId,
                'is_active' => $isActive
            ]);

            $response = [
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => ['id_admin' => $userId]
            ];
            break;

        case 'update_user':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $userId = (int)($_POST['id_admin'] ?? 0);
            $nama = trim($_POST['nama_lengkap'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $roleId = (int)($_POST['id_role'] ?? 0);
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
            $password = $_POST['password'] ?? '';

            if (!$userId) {
                $response = ['success' => false, 'message' => 'ID user tidak valid'];
                break;
            }

            if (empty($nama) || empty($email)) {
                $response = ['success' => false, 'message' => 'Nama dan email wajib diisi'];
                break;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Format email tidak valid'];
                break;
            }

            if ($adminRepo->emailExistsExcept($email, $userId)) {
                $response = ['success' => false, 'message' => 'Email sudah digunakan user lain'];
                break;
            }

            $updateData = [
                'nama_lengkap' => $nama,
                'email' => $email,
                'phone' => $phone,
                'id_role' => $roleId,
                'is_active' => $isActive
            ];

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $response = ['success' => false, 'message' => 'Password minimal 6 karakter'];
                    break;
                }
                $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $adminRepo->updateAdmin($userId, $updateData);
            $response = ['success' => true, 'message' => 'User berhasil diupdate'];
            break;

        case 'delete_user':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $userId = (int)($_POST['id_admin'] ?? 0);
            $currentAdmin = SessionManager::getCurrentAdmin();

            if (!$userId) {
                $response = ['success' => false, 'message' => 'ID user tidak valid'];
                break;
            }

            if ($currentAdmin && $currentAdmin['id_admin'] == $userId) {
                $response = ['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'];
                break;
            }

            $user = $adminRepo->getById($userId);
            if (!$user) {
                $response = ['success' => false, 'message' => 'User tidak ditemukan'];
                break;
            }

            $roleName = $adminRepo->getAdminRoleName($userId);
            if ($roleName === 'super_admin' && !AuthMiddleware::isSuperAdmin()) {
                $response = ['success' => false, 'message' => 'Tidak dapat menghapus Super Admin'];
                break;
            }

            $adminRepo->deleteAdmin($userId);
            $response = ['success' => true, 'message' => 'User berhasil dihapus'];
            break;

        case 'toggle_user_status':
            AuthMiddleware::checkPermissionJson('manage_admin_users');
            $userId = (int)($_POST['id_admin'] ?? 0);
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;

            if (!$userId) {
                $response = ['success' => false, 'message' => 'ID user tidak valid'];
                break;
            }

            $adminRepo->updateAdmin($userId, ['is_active' => $isActive]);
            $response = ['success' => true, 'message' => 'Status user berhasil diubah'];
            break;

        case 'get_all_roles':
            AuthMiddleware::checkPermissionJson('manage_roles');
            $roles = $roleRepo->getAllRolesWithUserCount();
            $response = ['success' => true, 'data' => $roles];
            break;

        case 'get_role':
            AuthMiddleware::checkPermissionJson('manage_roles');
            $roleId = (int)($_GET['id'] ?? 0);
            if ($roleId) {
                $role = $roleRepo->getById($roleId);
                if ($role) {
                    $role['permissions'] = $roleRepo->getPermissionIdsByRoleId($roleId);
                    $response = ['success' => true, 'data' => $role];
                } else {
                    $response = ['success' => false, 'message' => 'Role tidak ditemukan'];
                }
            }
            break;

        case 'create_role':
            AuthMiddleware::checkPermissionJson('manage_roles');
            $roleName = trim($_POST['role_name'] ?? '');
            $roleDesc = trim($_POST['role_description'] ?? '');

            if (empty($roleName)) {
                $response = ['success' => false, 'message' => 'Nama role wajib diisi'];
                break;
            }

            $roleNameSlug = strtolower(str_replace(' ', '_', $roleName));
            if ($roleRepo->roleNameExists($roleNameSlug)) {
                $response = ['success' => false, 'message' => 'Nama role sudah ada'];
                break;
            }

            $roleId = $roleRepo->createRole([
                'role_name' => $roleNameSlug,
                'role_description' => $roleDesc
            ]);

            $response = [
                'success' => true,
                'message' => 'Role berhasil ditambahkan',
                'data' => ['id_role' => $roleId]
            ];
            break;

        case 'update_role':
            AuthMiddleware::checkPermissionJson('manage_roles');
            $roleId = (int)($_POST['id_role'] ?? 0);
            $roleName = trim($_POST['role_name'] ?? '');
            $roleDesc = trim($_POST['role_description'] ?? '');

            if (!$roleId) {
                $response = ['success' => false, 'message' => 'ID role tidak valid'];
                break;
            }

            if (empty($roleName)) {
                $response = ['success' => false, 'message' => 'Nama role wajib diisi'];
                break;
            }

            $role = $roleRepo->getById($roleId);
            if (!$role) {
                $response = ['success' => false, 'message' => 'Role tidak ditemukan'];
                break;
            }

            if ($role['role_name'] === 'super_admin') {
                $response = ['success' => false, 'message' => 'Role Super Admin tidak dapat diubah'];
                break;
            }

            $roleNameSlug = strtolower(str_replace(' ', '_', $roleName));
            if ($roleRepo->roleNameExists($roleNameSlug, $roleId)) {
                $response = ['success' => false, 'message' => 'Nama role sudah digunakan'];
                break;
            }

            $roleRepo->updateRole($roleId, [
                'role_name' => $roleNameSlug,
                'role_description' => $roleDesc
            ]);

            $response = ['success' => true, 'message' => 'Role berhasil diupdate'];
            break;

        case 'delete_role':
            AuthMiddleware::checkPermissionJson('manage_roles');
            $roleId = (int)($_POST['id_role'] ?? 0);

            if (!$roleId) {
                $response = ['success' => false, 'message' => 'ID role tidak valid'];
                break;
            }

            $role = $roleRepo->getById($roleId);
            if (!$role) {
                $response = ['success' => false, 'message' => 'Role tidak ditemukan'];
                break;
            }

            if (in_array($role['role_name'], ['super_admin', 'admin'])) {
                $response = ['success' => false, 'message' => 'Role sistem tidak dapat dihapus'];
                break;
            }

            $roleRepo->deleteRole($roleId);
            $response = ['success' => true, 'message' => 'Role berhasil dihapus'];
            break;

        case 'get_all_permissions':
            AuthMiddleware::checkPermissionJson('manage_permissions');
            $permissions = $roleRepo->getAllPermissions();
            $response = ['success' => true, 'data' => $permissions];
            break;

        case 'get_role_permissions':
            AuthMiddleware::checkPermissionJson('assign_role_permissions');
            $roleId = (int)($_GET['id'] ?? 0);
            if ($roleId) {
                $permissions = $roleRepo->getPermissionsByRoleId($roleId);
                $permissionIds = $roleRepo->getPermissionIdsByRoleId($roleId);
                $response = [
                    'success' => true,
                    'data' => [
                        'permissions' => $permissions,
                        'permission_ids' => $permissionIds
                    ]
                ];
            }
            break;

        case 'update_role_permissions':
            AuthMiddleware::checkPermissionJson('assign_role_permissions');
            $roleId = (int)($_POST['id_role'] ?? 0);
            $permissionIds = $_POST['permissions'] ?? [];

            if (!$roleId) {
                $response = ['success' => false, 'message' => 'ID role tidak valid'];
                break;
            }

            $role = $roleRepo->getById($roleId);
            if (!$role) {
                $response = ['success' => false, 'message' => 'Role tidak ditemukan'];
                break;
            }

            if ($role['role_name'] === 'super_admin') {
                $response = ['success' => false, 'message' => 'Permission Super Admin tidak dapat diubah'];
                break;
            }

            if (!is_array($permissionIds)) {
                $permissionIds = [];
            }

            $roleRepo->syncRolePermissions($roleId, $permissionIds);
            $response = ['success' => true, 'message' => 'Permission berhasil diupdate'];
            break;

        case 'get_user_stats':
            $stats = $adminRepo->getAdminCountByRole();
            $totalUsers = $adminRepo->getTotalAdminCount();
            $activeUsers = $adminRepo->getActiveAdminCount();
            $response = [
                'success' => true,
                'data' => [
                    'by_role' => $stats,
                    'total' => $totalUsers,
                    'active' => $activeUsers
                ]
            ];
            break;

        case 'get_roles_for_select':
            $roles = $roleRepo->getAllRoles();
            $response = ['success' => true, 'data' => $roles];
            break;

        case 'get_dashboard_data':
            $stats = $adminRepo->getAdminCountByRole();
            $users = $adminRepo->getAllAdminsWithRoles();
            $roles = $roleRepo->getAllRolesWithUserCount();
            $response = [
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'users' => $users,
                    'roles' => $roles
                ]
            ];
            break;

        default:
            $response = ['success' => false, 'message' => 'Action tidak dikenali'];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ];
}

echo json_encode($response);
