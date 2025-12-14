<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class PromoCampaignRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'CMP';
    private const ID_LENGTH = 10;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT pk.*, 
                       (SELECT COUNT(*) FROM kampanye_produk kp WHERE kp.id_kampanye = pk.id_kampanye) as jumlah_produk
                FROM promo_kampanye pk WHERE 1=1";
        $params = [];

        if (!empty($filters['tipe'])) {
            $sql .= " AND pk.tipe = :tipe";
            $params['tipe'] = $filters['tipe'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND pk.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(pk.judul) LIKE :search OR LOWER(pk.slug) LIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        $orderBy = $filters['sort'] ?? 'newest';
        switch ($orderBy) {
            case 'oldest':
                $sql .= " ORDER BY pk.dibuat_pada ASC";
                break;
            case 'title':
                $sql .= " ORDER BY pk.judul ASC";
                break;
            case 'start':
                $sql .= " ORDER BY pk.mulai_pada ASC";
                break;
            default:
                $sql .= " ORDER BY pk.dibuat_pada DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT pk.*, 
                   (SELECT COUNT(*) FROM kampanye_produk kp WHERE kp.id_kampanye = pk.id_kampanye) as jumlah_produk
            FROM promo_kampanye pk 
            WHERE pk.id_kampanye = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT pk.*, 
                   (SELECT COUNT(*) FROM kampanye_produk kp WHERE kp.id_kampanye = pk.id_kampanye) as jumlah_produk
            FROM promo_kampanye pk 
            WHERE pk.slug = :slug
        ");
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getActiveCampaigns(): array
    {
        $stmt = $this->db->prepare("
            SELECT pk.*, 
                   (SELECT COUNT(*) FROM kampanye_produk kp WHERE kp.id_kampanye = pk.id_kampanye) as jumlah_produk
            FROM promo_kampanye pk 
            WHERE pk.status = 'aktif' 
              AND NOW() BETWEEN pk.mulai_pada AND pk.selesai_pada
              AND (pk.kuota_total IS NULL OR pk.kuota_terpakai < pk.kuota_total)
            ORDER BY pk.mulai_pada ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data, ?array $bannerFile = null): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $bannerPath = null;
        if ($bannerFile && $bannerFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadBanner($bannerFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            $bannerPath = $uploadResult['filename'];
        }

        $id = $this->generateId();
        $slug = $this->generateSlug($data['judul']);

        $mulaiPada = $this->formatDatetime($data['mulai_pada']);
        $selesaiPada = $this->formatDatetime($data['selesai_pada']);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO promo_kampanye (
                    id_kampanye, judul, slug, deskripsi, banner, tipe, 
                    mulai_pada, selesai_pada, kuota_total, status
                ) VALUES (
                    :id, :judul, :slug, :deskripsi, :banner, :tipe,
                    :mulai_pada, :selesai_pada, :kuota_total, :status
                )
            ");

            $success = $stmt->execute([
                'id' => $id,
                'judul' => trim($data['judul']),
                'slug' => $slug,
                'deskripsi' => $data['deskripsi'] ?? null,
                'banner' => $bannerPath,
                'tipe' => $data['tipe'],
                'mulai_pada' => $mulaiPada,
                'selesai_pada' => $selesaiPada,
                'kuota_total' => !empty($data['kuota_total']) ? (int)$data['kuota_total'] : null,
                'status' => $data['status'] ?? 'draf'
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Kampanye berhasil ditambahkan',
                    'id' => $id
                ];
            }

            if ($bannerPath) {
                $this->deleteBanner($bannerPath);
            }

            return [
                'success' => false,
                'message' => 'Gagal menambahkan kampanye'
            ];
        } catch (\PDOException $e) {
            if ($bannerPath) {
                $this->deleteBanner($bannerPath);
            }
            error_log('PromoCampaignRepository::create - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan kampanye. Silakan coba lagi.'
            ];
        }
    }

    public function update(string $id, array $data, ?array $bannerFile = null): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Kampanye tidak ditemukan'
            ];
        }

        $validation = $this->validateData($data, $id);
        if (!$validation['success']) {
            return $validation;
        }

        $bannerPath = $existing['banner'];
        if ($bannerFile && $bannerFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadBanner($bannerFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            if ($existing['banner']) {
                $this->deleteBanner($existing['banner']);
            }
            $bannerPath = $uploadResult['filename'];
        }

        $slug = $existing['slug'];
        if ($data['judul'] !== $existing['judul']) {
            $slug = $this->generateSlug($data['judul'], $id);
        }

        $mulaiPada = $this->formatDatetime($data['mulai_pada']);
        $selesaiPada = $this->formatDatetime($data['selesai_pada']);

        try {
            $stmt = $this->db->prepare("
                UPDATE promo_kampanye SET 
                    judul = :judul,
                    slug = :slug,
                    deskripsi = :deskripsi,
                    banner = :banner,
                    tipe = :tipe,
                    mulai_pada = :mulai_pada,
                    selesai_pada = :selesai_pada,
                    kuota_total = :kuota_total,
                    status = :status
                WHERE id_kampanye = :id
            ");

            $success = $stmt->execute([
                'id' => $id,
                'judul' => trim($data['judul']),
                'slug' => $slug,
                'deskripsi' => $data['deskripsi'] ?? null,
                'banner' => $bannerPath,
                'tipe' => $data['tipe'],
                'mulai_pada' => $mulaiPada,
                'selesai_pada' => $selesaiPada,
                'kuota_total' => !empty($data['kuota_total']) ? (int)$data['kuota_total'] : null,
                'status' => $data['status'] ?? 'draf'
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Kampanye berhasil diperbarui'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui kampanye'
            ];
        } catch (\PDOException $e) {
            error_log('PromoCampaignRepository::update - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kampanye. Silakan coba lagi.'
            ];
        }
    }

    public function delete(string $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Kampanye tidak ditemukan'
            ];
        }

        if ($existing['kuota_terpakai'] > 0) {
            return [
                'success' => false,
                'message' => 'Kampanye tidak dapat dihapus karena sudah pernah digunakan'
            ];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM promo_kampanye WHERE id_kampanye = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                if ($existing['banner']) {
                    $this->deleteBanner($existing['banner']);
                }
                return [
                    'success' => true,
                    'message' => 'Kampanye berhasil dihapus'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghapus kampanye'
            ];
        } catch (\PDOException $e) {
            error_log('PromoCampaignRepository::delete - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kampanye. Silakan coba lagi.'
            ];
        }
    }

    public function updateStatus(string $id, string $status): array
    {
        $validStatuses = ['draf', 'aktif', 'berakhir', 'nonaktif'];
        if (!in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Status tidak valid'
            ];
        }

        try {
            $stmt = $this->db->prepare("UPDATE promo_kampanye SET status = :status WHERE id_kampanye = :id");
            $success = $stmt->execute(['id' => $id, 'status' => $status]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Status kampanye berhasil diperbarui'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memperbarui status'
            ];
        } catch (\PDOException $e) {
            error_log('PromoCampaignRepository::updateStatus - ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status. Silakan coba lagi.'
            ];
        }
    }

    public function decrementQuota(string $id): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT kuota_total, kuota_terpakai 
                FROM promo_kampanye 
                WHERE id_kampanye = :id 
                FOR UPDATE
            ");
            $stmt->execute(['id' => $id]);
            $campaign = $stmt->fetch();

            if (!$campaign) {
                $this->db->rollBack();
                return false;
            }

            if ($campaign['kuota_total'] !== null && $campaign['kuota_terpakai'] >= $campaign['kuota_total']) {
                $this->db->rollBack();
                return false;
            }

            $updateStmt = $this->db->prepare("
                UPDATE promo_kampanye 
                SET kuota_terpakai = kuota_terpakai + 1 
                WHERE id_kampanye = :id
            ");
            $updateStmt->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM promo_kampanye");
        return (int) $stmt->fetch()['total'];
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM promo_kampanye WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetch()['total'];
    }

    public function getStatusInfo(string $status): array
    {
        $statusMap = [
            'draf' => [
                'label' => 'Draf',
                'class' => 'bg-gray-100 text-gray-700',
                'dot_class' => 'bg-gray-500'
            ],
            'aktif' => [
                'label' => 'Aktif',
                'class' => 'bg-emerald-100 text-emerald-700',
                'dot_class' => 'bg-emerald-500'
            ],
            'berakhir' => [
                'label' => 'Berakhir',
                'class' => 'bg-red-100 text-red-700',
                'dot_class' => 'bg-red-500'
            ],
            'nonaktif' => [
                'label' => 'Nonaktif',
                'class' => 'bg-amber-100 text-amber-700',
                'dot_class' => 'bg-amber-500'
            ]
        ];

        return $statusMap[$status] ?? $statusMap['draf'];
    }

    public function getTipeInfo(string $tipe): array
    {
        $tipeMap = [
            'diskon_produk' => [
                'label' => 'Diskon Produk',
                'icon' => 'percent',
                'class' => 'bg-blue-100 text-blue-700'
            ],
            'voucher' => [
                'label' => 'Voucher',
                'icon' => 'confirmation_number',
                'class' => 'bg-purple-100 text-purple-700'
            ],
            'flash_sale' => [
                'label' => 'Flash Sale',
                'icon' => 'bolt',
                'class' => 'bg-orange-100 text-orange-700'
            ],
            'bundle' => [
                'label' => 'Bundle',
                'icon' => 'inventory_2',
                'class' => 'bg-green-100 text-green-700'
            ],
            'gratis_ongkir' => [
                'label' => 'Gratis Ongkir',
                'icon' => 'local_shipping',
                'class' => 'bg-teal-100 text-teal-700'
            ]
        ];

        return $tipeMap[$tipe] ?? $tipeMap['diskon_produk'];
    }

    private function validateData(array $data, ?string $excludeId = null): array
    {
        if (empty($data['judul']) || trim($data['judul']) === '') {
            return ['success' => false, 'message' => 'Judul kampanye wajib diisi'];
        }

        if (strlen(trim($data['judul'])) < 3) {
            return ['success' => false, 'message' => 'Judul kampanye minimal 3 karakter'];
        }

        $validTipes = ['diskon_produk', 'voucher', 'flash_sale', 'bundle', 'gratis_ongkir'];
        if (empty($data['tipe']) || !in_array($data['tipe'], $validTipes)) {
            return ['success' => false, 'message' => 'Tipe kampanye tidak valid'];
        }

        if (empty($data['mulai_pada'])) {
            return ['success' => false, 'message' => 'Tanggal mulai wajib diisi'];
        }

        if (empty($data['selesai_pada'])) {
            return ['success' => false, 'message' => 'Tanggal selesai wajib diisi'];
        }

        if (strtotime($data['selesai_pada']) <= strtotime($data['mulai_pada'])) {
            return ['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai'];
        }

        return ['success' => true];
    }

    private function generateId(): string
    {
        $stmt = $this->db->query("SELECT id_kampanye FROM promo_kampanye ORDER BY id_kampanye DESC LIMIT 1");
        $last = $stmt->fetch();

        if ($last) {
            $num = (int) substr($last['id_kampanye'], strlen(self::ID_PREFIX)) + 1;
        } else {
            $num = 1;
        }

        return self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);
    }

    private function generateSlug(string $title, ?string $excludeId = null): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?string $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM promo_kampanye WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($excludeId) {
            $sql .= " AND id_kampanye != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'] > 0;
    }

    private function uploadBanner(array $file): array
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB'];
        }

        $uploadDir = __DIR__ . '/../../uploads/promo/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'campaign_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }

    private function deleteBanner(string $filename): void
    {
        $path = __DIR__ . '/../../uploads/promo/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function formatDatetime(?string $datetime): ?string
    {
        if (empty($datetime)) {
            return null;
        }
        
        $datetime = str_replace('T', ' ', $datetime);
        
        if (strlen($datetime) === 16) {
            $datetime .= ':00';
        }
        
        return $datetime;
    }
}
