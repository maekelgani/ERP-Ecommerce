<?php

namespace App\Auth;

use App\Database\BaseRepository;

class AdminRepository extends BaseRepository
{
    protected string $table = 'administrators';
    private const ADMIN_PHOTO_DIR = '/uploads/admin/';
    private const DEFAULT_PHOTO = '/assets/img/profil/default-profil.png';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAdminByEmail(string $email): ?array
    {
        $stmt = $this->prepare("
            SELECT a.*, ar.role_name, ar.role_description
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.email = ?
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getById(int $adminId): ?array
    {
        $stmt = $this->prepare("
            SELECT a.* 
            FROM {$this->table} a 
            WHERE a.id_admin = ?
        ");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAdminWithRole(int $adminId): ?array
    {
        $stmt = $this->prepare("
            SELECT a.*, ar.role_name, ar.role_description
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.id_admin = ?
        ");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAdminWithPhoto(int $adminId): ?array
    {
        $stmt = $this->prepare("
            SELECT a.*, ar.role_name
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.id_admin = ?
        ");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($admin) {
            $admin['photo_url'] = $this->getPhotoUrl($admin['photo'] ?? null);
            $admin['role'] = $admin['role_name'] ?? 'admin';
        }

        return $admin ?: null;
    }

    public function getPhotoUrl(?string $photoPath): string
    {
        if (!empty($photoPath)) {
            $cleanPath = ltrim($photoPath, '/');
            $fullPath = __DIR__ . '/../../' . $cleanPath;
            if (file_exists($fullPath)) {
                return '/' . $cleanPath;
            }
        }
        return self::DEFAULT_PHOTO;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function createAdmin(array $data): int
    {
        $stmt = $this->prepare("
            INSERT INTO {$this->table} 
            (nama_lengkap, email, password_hash, id_role, phone, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
            RETURNING id_admin
        ");

        $stmt->execute([
            $data['nama_lengkap'],
            $data['email'],
            $data['password_hash'],
            $data['id_role'] ?? 2,
            $data['phone'] ?? null,
            $data['is_active'] ?? 1
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['id_admin'];
    }

    public function updateAdmin(int $adminId, array $data): bool
    {
        $fields = [];
        $values = [];

        if (isset($data['nama_lengkap'])) {
            $fields[] = 'nama_lengkap = ?';
            $values[] = $data['nama_lengkap'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = ?';
            $values[] = $data['email'];
        }
        if (isset($data['id_role'])) {
            $fields[] = 'id_role = ?';
            $values[] = $data['id_role'];
        }
        if (isset($data['phone'])) {
            $fields[] = 'phone = ?';
            $values[] = $data['phone'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $values[] = $data['is_active'];
        }
        if (isset($data['password_hash'])) {
            $fields[] = 'password_hash = ?';
            $values[] = $data['password_hash'];
            $fields[] = 'password_updated_at = NOW()';
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $adminId;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id_admin = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute($values);
    }

    public function deleteAdmin(int $adminId): bool
    {
        $stmt = $this->prepare("DELETE FROM {$this->table} WHERE id_admin = ?");
        return $stmt->execute([$adminId]);
    }

    public function updatePhoto(int $adminId, string $photoPath): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET photo = ? 
            WHERE id_admin = ?
        ");

        return $stmt->execute([$photoPath, $adminId]);
    }

    public function updateLastLogin(int $adminId): void
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET last_login = NOW() 
            WHERE id_admin = ?
        ");

        $stmt->execute([$adminId]);
    }

    public function getRoleById(int $roleId): ?array
    {
        $stmt = $this->prepare("
            SELECT * FROM admin_roles WHERE id_role = ?
        ");
        $stmt->execute([$roleId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getRoleByName(string $roleName): ?array
    {
        $stmt = $this->prepare("
            SELECT * FROM admin_roles WHERE role_name = ?
        ");
        $stmt->execute([$roleName]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getPermissionsByRoleId(int $roleId): array
    {
        $stmt = $this->prepare("
            SELECT ap.permission_key, ap.permission_name, ap.permission_description
            FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            WHERE rp.id_role = ?
            ORDER BY ap.id_permission ASC
        ");
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPermissionKeysByRoleId(int $roleId): array
    {
        $stmt = $this->prepare("
            SELECT ap.permission_key
            FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            WHERE rp.id_role = ?
        ");
        $stmt->execute([$roleId]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $permissions = [];
        foreach ($result as $row) {
            $permissions[] = $row['permission_key'];
        }
        return $permissions;
    }

    public function getPermissions(string $roleName): array
    {
        $stmt = $this->prepare("
            SELECT ap.permission_key 
            FROM role_permissions rp
            JOIN admin_permissions ap ON rp.id_permission = ap.id_permission
            JOIN admin_roles ar ON rp.id_role = ar.id_role
            WHERE ar.role_name = ?
        ");
        $stmt->execute([$roleName]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $permissions = [];
        foreach ($result as $row) {
            $permissions[] = $row['permission_key'];
        }
        return $permissions;
    }

    public function hasPermission(string $roleName, string $permissionKey): bool
    {
        $permissions = $this->getPermissions($roleName);
        return in_array($permissionKey, $permissions);
    }

    public function hasPermissionByRoleId(int $roleId, string $permissionKey): bool
    {
        $permissions = $this->getPermissionKeysByRoleId($roleId);
        return in_array($permissionKey, $permissions);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function getAllAdmins(): array
    {
        return $this->fetchAll("
            SELECT a.id_admin, a.nama_lengkap, a.email, a.phone, a.is_active, a.last_login,
                   a.created_at, ar.role_name, ar.id_role
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            ORDER BY a.created_at DESC
        ");
    }

    public function getAllAdminsWithRoles(): array
    {
        return $this->fetchAll("
            SELECT a.*, ar.role_name, ar.role_description
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            ORDER BY a.created_at DESC
        ");
    }

    public function getAdminCountByRole(): array
    {
        return $this->fetchAll("
            SELECT ar.id_role, ar.role_name, ar.role_description, COUNT(a.id_admin) as user_count
            FROM admin_roles ar
            LEFT JOIN {$this->table} a ON ar.id_role = a.id_role AND a.is_active = true
            GROUP BY ar.id_role, ar.role_name, ar.role_description
            ORDER BY ar.id_role ASC
        ");
    }

    public function getAllRoles(): array
    {
        return $this->fetchAll("SELECT * FROM admin_roles ORDER BY id_role ASC");
    }

    public function getAllPermissions(): array
    {
        return $this->fetchAll("SELECT * FROM admin_permissions ORDER BY id_permission ASC");
    }

    public function findByRememberToken(string $token): ?array
    {
        $stmt = $this->prepare("
            SELECT a.*, ar.role_name
            FROM {$this->table} a
            LEFT JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.remember_token = ? 
            AND a.remember_expires > NOW() 
            AND a.is_active = true
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function updateRememberToken(int $adminId, string $token, string $expiresAt): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET remember_token = ?, remember_expires = ? 
            WHERE id_admin = ?
        ");
        return $stmt->execute([$token, $expiresAt, $adminId]);
    }

    public function clearRememberToken(int $adminId): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET remember_token = NULL, remember_expires = NULL 
            WHERE id_admin = ?
        ");
        return $stmt->execute([$adminId]);
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function usernameExists(string $username, ?int $excludeAdminId = null): bool
    {
        if ($excludeAdminId) {
            $stmt = $this->prepare("SELECT id_admin FROM {$this->table} WHERE username = ? AND id_admin != ?");
            $stmt->execute([$username, $excludeAdminId]);
        } else {
            $stmt = $this->prepare("SELECT id_admin FROM {$this->table} WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetch() !== false;
    }

    public function emailExistsExcept(string $email, int $excludeAdminId): bool
    {
        $stmt = $this->prepare("SELECT id_admin FROM {$this->table} WHERE email = ? AND id_admin != ?");
        $stmt->execute([$email, $excludeAdminId]);
        return $stmt->fetch() !== false;
    }

    public function updateProfile(int $adminId, array $data): bool
    {
        $fields = [];
        $values = [];

        if (array_key_exists('nama_lengkap', $data)) {
            $fields[] = 'nama_lengkap = ?';
            $values[] = $data['nama_lengkap'];
        }
        if (array_key_exists('email', $data)) {
            $fields[] = 'email = ?';
            $values[] = $data['email'];
        }
        if (array_key_exists('username', $data)) {
            $fields[] = 'username = ?';
            $values[] = $data['username'];
        }
        if (array_key_exists('phone', $data)) {
            $fields[] = 'phone = ?';
            $values[] = $data['phone'];
        }
        if (array_key_exists('photo', $data)) {
            $fields[] = 'photo = ?';
            $values[] = $data['photo'];
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $adminId;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id_admin = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute($values);
    }

    public function updatePassword(int $adminId, string $newPasswordHash): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET password_hash = ?, password_updated_at = NOW(), updated_at = NOW()
            WHERE id_admin = ?
        ");
        return $stmt->execute([$newPasswordHash, $adminId]);
    }

    public function getPasswordHash(int $adminId): ?string
    {
        $stmt = $this->prepare("SELECT password_hash FROM {$this->table} WHERE id_admin = ?");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['password_hash'] : null;
    }

    public function isSuperAdmin(int $adminId): bool
    {
        $stmt = $this->prepare("
            SELECT ar.role_name 
            FROM {$this->table} a
            JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.id_admin = ?
        ");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result && $result['role_name'] === 'super_admin';
    }

    public function getAdminRoleId(int $adminId): ?int
    {
        $stmt = $this->prepare("SELECT id_role FROM {$this->table} WHERE id_admin = ?");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int)$result['id_role'] : null;
    }

    public function getAdminRoleName(int $adminId): ?string
    {
        $stmt = $this->prepare("
            SELECT ar.role_name 
            FROM {$this->table} a
            JOIN admin_roles ar ON a.id_role = ar.id_role
            WHERE a.id_admin = ?
        ");
        $stmt->execute([$adminId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['role_name'] : null;
    }

    public function getTotalAdminCount(): int
    {
        $result = $this->fetchRow("SELECT COUNT(*) as total FROM {$this->table}");
        return (int)($result['total'] ?? 0);
    }

    public function getActiveAdminCount(): int
    {
        $result = $this->fetchRow("SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = true");
        return (int)($result['total'] ?? 0);
    }
}
