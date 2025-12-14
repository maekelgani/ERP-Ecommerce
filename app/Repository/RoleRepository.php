<?php

namespace App\Repository;

use App\Database\BaseRepository;

class RoleRepository extends BaseRepository
{
    protected string $table = 'admin_roles';

    public function getById(int $roleId): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE id_role = ?");
        $stmt->execute([$roleId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getByName(string $roleName): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE role_name = ?");
        $stmt->execute([$roleName]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAllRoles(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} ORDER BY id_role ASC");
    }

    public function getAllRolesWithUserCount(): array
    {
        return $this->fetchAll("
            SELECT r.*, 
                   COUNT(a.id_admin) as user_count,
                   (SELECT COUNT(*) FROM role_permissions rp WHERE rp.id_role = r.id_role) as permission_count
            FROM {$this->table} r
            LEFT JOIN administrators a ON r.id_role = a.id_role
            GROUP BY r.id_role, r.role_name, r.role_description
            ORDER BY r.id_role ASC
        ");
    }

    public function createRole(array $data): int
    {
        $stmt = $this->prepare("
            INSERT INTO {$this->table} (role_name, role_description)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $data['role_name'],
            $data['role_description'] ?? null
        ]);
        return (int)$this->lastInsertId();
    }

    public function updateRole(int $roleId, array $data): bool
    {
        $fields = [];
        $values = [];

        if (isset($data['role_name'])) {
            $fields[] = 'role_name = ?';
            $values[] = $data['role_name'];
        }
        if (isset($data['role_description'])) {
            $fields[] = 'role_description = ?';
            $values[] = $data['role_description'];
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $roleId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id_role = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute($values);
    }

    public function deleteRole(int $roleId): bool
    {
        $stmt = $this->prepare("DELETE FROM {$this->table} WHERE id_role = ?");
        return $stmt->execute([$roleId]);
    }

    public function roleNameExists(string $roleName, ?int $excludeRoleId = null): bool
    {
        if ($excludeRoleId) {
            $stmt = $this->prepare("SELECT id_role FROM {$this->table} WHERE role_name = ? AND id_role != ?");
            $stmt->execute([$roleName, $excludeRoleId]);
        } else {
            $stmt = $this->prepare("SELECT id_role FROM {$this->table} WHERE role_name = ?");
            $stmt->execute([$roleName]);
        }
        return $stmt->fetch() !== false;
    }

    public function getAllPermissions(): array
    {
        return $this->fetchAll("SELECT * FROM admin_permissions ORDER BY id_permission ASC");
    }

    public function getPermissionById(int $permissionId): ?array
    {
        $stmt = $this->prepare("SELECT * FROM admin_permissions WHERE id_permission = ?");
        $stmt->execute([$permissionId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getPermissionByName(string $permissionName): ?array
    {
        $stmt = $this->prepare("SELECT * FROM admin_permissions WHERE permission_name = ?");
        $stmt->execute([$permissionName]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function createPermission(array $data): int
    {
        $stmt = $this->prepare("
            INSERT INTO admin_permissions (permission_name, permission_description)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $data['permission_name'],
            $data['permission_description'] ?? null
        ]);
        return (int)$this->lastInsertId();
    }

    public function updatePermission(int $permissionId, array $data): bool
    {
        $fields = [];
        $values = [];

        if (isset($data['permission_name'])) {
            $fields[] = 'permission_name = ?';
            $values[] = $data['permission_name'];
        }
        if (isset($data['permission_description'])) {
            $fields[] = 'permission_description = ?';
            $values[] = $data['permission_description'];
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $permissionId;
        $sql = "UPDATE admin_permissions SET " . implode(', ', $fields) . " WHERE id_permission = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute($values);
    }

    public function deletePermission(int $permissionId): bool
    {
        $stmt = $this->prepare("DELETE FROM admin_permissions WHERE id_permission = ?");
        return $stmt->execute([$permissionId]);
    }

    public function getPermissionsByRoleId(int $roleId): array
    {
        $stmt = $this->prepare("
            SELECT ap.*
            FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            WHERE rp.id_role = ?
            ORDER BY ap.id_permission ASC
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPermissionIdsByRoleId(int $roleId): array
    {
        $stmt = $this->prepare("SELECT id_permission FROM role_permissions WHERE id_role = ?");
        $stmt->execute([$roleId]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($result, 'id_permission');
    }

    public function getPermissionKeysByRoleId(int $roleId): array
    {
        $stmt = $this->prepare("
            SELECT ap.permission_name as permission_key
            FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            WHERE rp.id_role = ?
        ");
        $stmt->execute([$roleId]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($result, 'permission_key');
    }

    public function assignPermissionToRole(int $roleId, int $permissionId): bool
    {
        // MySQL: Gunakan INSERT IGNORE untuk menghindari duplicate entry
        $stmt = $this->prepare("
            INSERT IGNORE INTO role_permissions (id_role, id_permission)
            VALUES (?, ?)
        ");
        return $stmt->execute([$roleId, $permissionId]);
    }

    public function removePermissionFromRole(int $roleId, int $permissionId): bool
    {
        $stmt = $this->prepare("DELETE FROM role_permissions WHERE id_role = ? AND id_permission = ?");
        return $stmt->execute([$roleId, $permissionId]);
    }

    public function clearRolePermissions(int $roleId): bool
    {
        $stmt = $this->prepare("DELETE FROM role_permissions WHERE id_role = ?");
        return $stmt->execute([$roleId]);
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): bool
    {
        // Hapus semua permission yang ada
        $this->clearRolePermissions($roleId);

        // Tambahkan permission baru
        if (!empty($permissionIds)) {
            foreach ($permissionIds as $permissionId) {
                $this->assignPermissionToRole($roleId, (int)$permissionId);
            }
        }

        return true;
    }

    public function getRolePermissionMapping(): array
    {
        return $this->fetchAll("
            SELECT rp.id_role_permission, rp.id_role, rp.id_permission,
                    ar.role_name, ap.permission_name as permission_key, ap.permission_name
            FROM role_permissions rp
            JOIN admin_roles ar ON rp.id_role = ar.id_role
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            ORDER BY ar.id_role, ap.id_permission
        ");
    }

    public function hasRolePermission(int $roleId, string $permissionKey): bool
    {
        $stmt = $this->prepare("
            SELECT 1 FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            WHERE rp.id_role = ? AND ap.permission_name = ?
        ");
        $stmt->execute([$roleId, $permissionKey]);
        return $stmt->fetch() !== false;
    }

    public function getRoleCount(): int
    {
        $result = $this->fetchRow("SELECT COUNT(*) as total FROM {$this->table}");
        return (int)($result['total'] ?? 0);
    }

    public function getPermissionCount(): int
    {
        $result = $this->fetchRow("SELECT COUNT(*) as total FROM admin_permissions");
        return (int)($result['total'] ?? 0);
    }

    public function getPermissionsGrouped(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];

        foreach ($permissions as $perm) {
            $prefix = explode('_', $perm['permission_name'])[0];
            $grouped[$prefix][] = $perm;
        }

        return $grouped;
    }
}
