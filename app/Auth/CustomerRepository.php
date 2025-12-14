<?php

namespace App\Auth;

use App\Database\BaseRepository;

class CustomerRepository extends BaseRepository
{
    protected string $table = 'customers';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE no_telp = ?");
        $stmt->execute([$phone]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE google_id = ?");
        $stmt->execute([$googleId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function phoneExists(string $phone): bool
    {
        return $this->findByPhone($phone) !== null;
    }

    public function createRegularCustomer(array $data): int
    {
        $stmt = $this->prepare("
            INSERT INTO {$this->table} 
            (nama_lengkap, email, no_telp, password_hash, login_type) 
            VALUES (?, ?, ?, ?, 'regular')
        ");

        $stmt->execute([
            $data['nama_lengkap'],
            $data['email'],
            $data['no_telp'],
            $data['password_hash']
        ]);

        // Ambil ID yang baru saja di-insert menggunakan query SELECT LAST_INSERT_ID()
        $idStmt = $this->prepare("SELECT LAST_INSERT_ID() as id");
        $idStmt->execute();
        $result = $idStmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    /**
     * Upsert Google Customer - Create baru atau update existing
     * Mendukung:
     * - Profile image dari Google
     * - Nomor telepon dari Google (jika tersedia)
     * - Account linking (jika email sudah terdaftar sebagai regular user)
     * 
     * @param array $googleData Data dari Google OAuth
     * @return array ['id' => int, 'is_new' => bool, 'needs_phone' => bool]
     */
    public function upsertGoogleCustomer(array $googleData): array
    {
        $googleId = $googleData['id'];
        $googleEmail = $googleData['email'] ?? '';
        $googleName = $googleData['name'] ?? $googleEmail;
        $profileImage = $googleData['profile_image'] ?? null;
        $phone = $googleData['phone'] ?? null;

        $existingByGoogleId = $this->findByGoogleId($googleId);

        if ($existingByGoogleId) {
            $this->updateGoogleCustomer($existingByGoogleId['id_customer'], $googleData);

            $needsPhone = empty($existingByGoogleId['no_telp']) && empty($phone);

            return [
                'id' => $existingByGoogleId['id_customer'],
                'is_new' => false,
                'needs_phone' => $needsPhone
            ];
        }

        $existingByEmail = $this->findByEmail($googleEmail);

        if ($existingByEmail) {
            $this->linkGoogleToExistingAccount($existingByEmail['id_customer'], $googleData);

            $needsPhone = empty($existingByEmail['no_telp']) && empty($phone);

            return [
                'id' => $existingByEmail['id_customer'],
                'is_new' => false,
                'needs_phone' => $needsPhone,
                'linked' => true
            ];
        }

        $stmt = $this->prepare("
            INSERT INTO {$this->table} 
            (nama_lengkap, email, no_telp, google_id, google_email, google_name, profile_image, login_type, email_verified, email_verified_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'google', 1, NOW())
        ");

        $stmt->execute([
            $googleName,
            $googleEmail,
            $phone,
            $googleId,
            $googleEmail,
            $googleName,
            $profileImage
        ]);

        // Ambil ID yang baru saja di-insert menggunakan query SELECT LAST_INSERT_ID()
        $idStmt = $this->prepare("SELECT LAST_INSERT_ID() as id");
        $idStmt->execute();
        $result = $idStmt->fetch(\PDO::FETCH_ASSOC);
        $customerId = (int) $result['id'];

        return [
            'id' => $customerId,
            'is_new' => true,
            'needs_phone' => empty($phone)
        ];
    }

    /**
     * Link Google account ke existing regular customer
     */
    private function linkGoogleToExistingAccount(int $customerId, array $googleData): void
    {
        $googleId = $googleData['id'];
        $googleEmail = $googleData['email'] ?? '';
        $googleName = $googleData['name'] ?? '';
        $profileImage = $googleData['profile_image'] ?? null;
        $phone = $googleData['phone'] ?? null;

        $updateFields = [
            'google_id' => $googleId,
            'google_email' => $googleEmail,
            'google_name' => $googleName,
            'email_verified' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ];

        if ($profileImage) {
            $updateFields['profile_image'] = $profileImage;
        }

        if ($phone) {
            $customer = $this->getById($customerId);
            if (empty($customer['no_telp'])) {
                $updateFields['no_telp'] = $phone;
            }
        }

        $setParts = [];
        $values = [];
        foreach ($updateFields as $field => $value) {
            if ($value === true) {
                $setParts[] = "$field = 1";
            } elseif ($value === false) {
                $setParts[] = "$field = 0";
            } else {
                $setParts[] = "$field = ?";
                $values[] = $value;
            }
        }
        $setParts[] = "updated_at = NOW()";
        $values[] = $customerId;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id_customer = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * Update existing Google customer data
     */
    private function updateGoogleCustomer(int $customerId, array $googleData): void
    {
        $googleName = $googleData['name'] ?? '';
        $googleEmail = $googleData['email'] ?? '';
        $profileImage = $googleData['profile_image'] ?? null;
        $phone = $googleData['phone'] ?? null;

        $customer = $this->getById($customerId);

        $updateParts = ['google_name = ?', 'google_email = ?'];
        $values = [$googleName, $googleEmail];

        if ($profileImage && empty($customer['profile_image'])) {
            $updateParts[] = 'profile_image = ?';
            $values[] = $profileImage;
        }

        if ($phone && empty($customer['no_telp'])) {
            $updateParts[] = 'no_telp = ?';
            $values[] = $phone;
        }

        $updateParts[] = 'updated_at = NOW()';
        $values[] = $customerId;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateParts) . " WHERE id_customer = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute($values);
    }

    /**
     * Update phone number for customer
     */
    public function updatePhone(int $customerId, string $phone): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET no_telp = ?, updated_at = NOW() 
            WHERE id_customer = ?
        ");
        return $stmt->execute([$phone, $customerId]);
    }

    /**
     * Update profile image for customer
     */
    public function updateProfileImage(int $customerId, string $imagePath): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET profile_image = ?, updated_at = NOW() 
            WHERE id_customer = ?
        ");
        return $stmt->execute([$imagePath, $customerId]);
    }

    /**
     * Check if customer needs to complete profile (missing phone)
     */
    public function needsProfileCompletion(int $customerId): bool
    {
        $customer = $this->getById($customerId);
        return $customer && empty($customer['no_telp']);
    }

    /**
     * Set password for Google user (optional local password)
     */
    public function setPasswordForGoogleUser(int $customerId, string $passwordHash): bool
    {
        $customer = $this->getById($customerId);

        if (!$customer || $customer['login_type'] !== 'google') {
            return false;
        }

        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET password_hash = ?, updated_at = NOW() 
            WHERE id_customer = ?
        ");
        return $stmt->execute([$passwordHash, $customerId]);
    }

    /**
     * Check if customer has local password set
     */
    public function hasLocalPassword(int $customerId): bool
    {
        $customer = $this->getById($customerId);
        return $customer && !empty($customer['password_hash']);
    }

    public function updatePassword(int $customerId, string $passwordHash): void
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET password_hash = ?, updated_at = NOW() 
            WHERE id_customer = ?
        ");

        $stmt->execute([$passwordHash, $customerId]);
    }

    public function getById(int $customerId): ?array
    {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE id_customer = ?");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function findByRememberToken(string $token): ?array
    {
        $stmt = $this->prepare("
            SELECT * FROM {$this->table} 
            WHERE remember_token = ? 
            AND remember_expires > NOW() 
            AND is_active = 1
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function updateRememberToken(int $customerId, string $token, string $expiresAt): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET remember_token = ?, remember_expires = ? 
            WHERE id_customer = ?
        ");
        return $stmt->execute([$token, $expiresAt, $customerId]);
    }

    public function clearRememberToken(int $customerId): bool
    {
        $stmt = $this->prepare("
            UPDATE {$this->table} 
            SET remember_token = NULL, remember_expires = NULL 
            WHERE id_customer = ?
        ");
        return $stmt->execute([$customerId]);
    }
}
