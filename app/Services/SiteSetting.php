<?php 
namespace App\Services;

use App\Database\DatabaseConnection;

use PDO;
use Exception;

class SiteSetting
{
    private \PDO $db;
    private $table = 'site_settings';

    private $allowedColumns = [
        'site_title', 
        'site_description', 
        'site_logo', 
        'site_favicon', 
        'contact_email', 
        'contact_phone', 
        'facebook_url', 
        'instagram_url', 
        'tiktok_url', 
        'x_url',
        'youtube_url'
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // public function getSetting(string $key): ?string
    // {
    //     $stmt = $this->db->prepare("SELECT value FROM {$this->table} WHERE `id` = :id LIMIT 1");
    //     $stmt->execute(['id' => $key]);
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result ? $result['value'] : null;
    // }

    // public function setSetting(string $key, string $value): bool
    // {
    //     $stmt = $this->db->prepare("REPLACE INTO {$this->table} (`id`, `value`) VALUES (:id, :value)");
    //     return $stmt->execute(['id' => $key, 'value' => $value]);
    // }

    public function getAllSettings(): array
    {
        // Query ambil data baris pertama (id = 1)
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = 1 LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: [];
    }

    public function updateBatchSettings(array $data): bool
    {
        try {
            // 1. Filter data agar hanya kolom yang valid yang diupdate
            $fieldsToUpdate = [];
            $params = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $this->allowedColumns)) {
                    // Susun query set: "site_title = :site_title"
                    $fieldsToUpdate[] = "{$key} = :{$key}";
                    // Masukkan value ke binding param
                    $params[$key] = $value;
                }
            }

            // Jika tidak ada data valid yang dikirim, return false/true sesuai kebutuhan
            if (empty($fieldsToUpdate)) {
                return false; 
            }

            // 2. Susun Query UPDATE
            // UPDATE site_settings SET site_title = :site_title, ... WHERE id = 1
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fieldsToUpdate) . " WHERE id = 1";
            
            $stmt = $this->db->prepare($sql);
            
            // 3. Eksekusi dengan array params
            return $stmt->execute($params);

        } catch (Exception $e) {
            // Log error jika perlu
            return false;
        }
    }
}

?>