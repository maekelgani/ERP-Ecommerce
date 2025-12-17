<?php

namespace App\Repository;

use App\Database\DatabaseConnection;
use PDO;

class DiskonRepository
{
    private PDO $db;
    private const ID_PREFIX = 'DSK';
    private const ID_LENGTH = 10;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    /* =========================
       LIST PROMO DISKON
    ========================== */
    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                d.*,
                p.nama_product,
                p.harga AS harga_produk,
                p.gambar AS gambar_produk,
                pk.judul AS nama_kampanye
            FROM promo_diskon d
            LEFT JOIN products p ON d.id_produk = p.id_product
            LEFT JOIN promo_kampanye pk ON d.id_kampanye = pk.id_kampanye
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['jenis'])) {
            $sql .= " AND d.jenis = :jenis";
            $params['jenis'] = $filters['jenis'];
        }

        if (!empty($filters['id_kampanye'])) {
            $sql .= " AND d.id_kampanye = :id_kampanye";
            $params['id_kampanye'] = $filters['id_kampanye'];
        }

        if (!empty($filters['id_produk'])) {
            $sql .= " AND d.id_produk = :id_produk";
            $params['id_produk'] = $filters['id_produk'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $sql .= " AND (LOWER(d.label) LIKE :search OR LOWER(p.nama_product) LIKE :search2)";
            $params['search'] = $search;
            $params['search2'] = $search;
        }

        $sql .= " ORDER BY d.dibuat_pada DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DETAIL PROMO
    ========================== */
    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                d.*, 
                p.nama_product,
                p.harga AS harga_produk,
                p.gambar AS gambar_produk,
                pk.judul AS nama_kampanye
            FROM promo_diskon d
            LEFT JOIN products p ON d.id_produk = p.id_product
            LEFT JOIN promo_kampanye pk ON d.id_kampanye = pk.id_kampanye
            WHERE d.id_diskon = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* =========================
       DISKON AKTIF PRODUK
    ========================== */
    public function getActiveDiscountsByProduct(string $idProduct): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM promo_diskon
            WHERE id_produk = :id_produk
              AND status = 'aktif'
              AND (mulai_pada IS NULL OR NOW() >= mulai_pada)
              AND (selesai_pada IS NULL OR NOW() <= selesai_pada)
              AND (stok_promo IS NULL OR stok_terpakai < stok_promo)
            ORDER BY nilai DESC
        ");
        $stmt->execute(['id_produk' => $idProduct]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBestDiscountForProduct(string $idProduct): ?array
    {
        $discounts = $this->getActiveDiscountsByProduct($idProduct);
        return $discounts[0] ?? null;
    }

    /* =========================
       CREATE PROMO DISKON
    ========================== */
    public function create(array $data): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) return $validation;

        $id = $this->generateId();

        $hargaAwal = $this->getProductPrice($data['id_produk']);
        $hargaDiskon = $this->calculateFinalPrice(
            $hargaAwal,
            $data['jenis'],
            $data['nilai']
        );

        try {
            $stmt = $this->db->prepare("
                INSERT INTO promo_diskon (
                    id_diskon, id_produk, id_kampanye, label,
                    harga_awal, harga_diskon,
                    jenis, nilai,
                    stok_promo, stok_terpakai,
                    maks_qty_per_pengguna,
                    mulai_pada, selesai_pada,
                    status, dibuat_pada, diperbarui_pada
                ) VALUES (
                    :id, :id_produk, :id_kampanye, :label,
                    :harga_awal, :harga_diskon,
                    :jenis, :nilai,
                    :stok_promo, 0,
                    :maks_qty,
                    :mulai_pada, :selesai_pada,
                    :status, NOW(), NOW()
                )
            ");

            $stmt->execute([
                'id' => $id,
                'id_produk' => $data['id_produk'],
                'id_kampanye' => $data['id_kampanye'] ?? null,
                'label' => $data['label'] ?? null,
                'harga_awal' => $hargaAwal,
                'harga_diskon' => $hargaDiskon,
                'jenis' => $data['jenis'],
                'nilai' => $data['nilai'],
                'stok_promo' => $data['stok_promo'] ?? null,
                'maks_qty' => $data['maks_qty_per_pengguna'] ?? null,
                'mulai_pada' => $this->formatDatetime($data['mulai_pada'] ?? null),
                'selesai_pada' => $this->formatDatetime($data['selesai_pada'] ?? null),
                'status' => $data['status'] ?? 'terjadwal'
            ]);

            return ['success' => true, 'message' => 'Promo diskon berhasil ditambahkan', 'id' => $id];
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return ['success' => false, 'message' => 'Gagal menambahkan promo diskon'];
        }
    }

    /* =========================
       UPDATE PROMO
    ========================== */
    public function update(string $id, array $data): array
    {
        $existing = $this->getById($id);
        if (!$existing) return ['success' => false, 'message' => 'Promo tidak ditemukan'];

        $hargaAwal = $this->getProductPrice($data['id_produk']);
        $hargaDiskon = $this->calculateFinalPrice($hargaAwal, $data['jenis'], $data['nilai']);

        $stmt = $this->db->prepare("
            UPDATE promo_diskon SET
                label = :label,
                harga_awal = :harga_awal,
                harga_diskon = :harga_diskon,
                jenis = :jenis,
                nilai = :nilai,
                stok_promo = :stok_promo,
                maks_qty_per_pengguna = :maks_qty,
                mulai_pada = :mulai_pada,
                selesai_pada = :selesai_pada,
                status = :status,
                diperbarui_pada = NOW()
            WHERE id_diskon = :id
        ");

        $stmt->execute([
            'id' => $id,
            'label' => $data['label'] ?? null,
            'harga_awal' => $hargaAwal,
            'harga_diskon' => $hargaDiskon,
            'jenis' => $data['jenis'],
            'nilai' => $data['nilai'],
            'stok_promo' => $data['stok_promo'] ?? null,
            'maks_qty' => $data['maks_qty_per_pengguna'] ?? null,
            'mulai_pada' => $this->formatDatetime($data['mulai_pada'] ?? null),
            'selesai_pada' => $this->formatDatetime($data['selesai_pada'] ?? null),
            'status' => $data['status']
        ]);

        return ['success' => true, 'message' => 'Promo diskon berhasil diperbarui'];
    }

    public function delete(string $id): array
    {
        $stmt = $this->db->prepare("DELETE FROM promo_diskon WHERE id_diskon = :id");
        $stmt->execute(['id' => $id]);
        return ['success' => true, 'message' => 'Promo diskon berhasil dihapus'];
    }

    /* =========================
       UTILITIES
    ========================== */

    private function generateId(): string
    {
        return self::ID_PREFIX . strtoupper(bin2hex(random_bytes(4)));
    }

    private function getProductPrice(string $productId): float
    {
        $stmt = $this->db->prepare("SELECT harga FROM products WHERE id_product = :id");
        $stmt->execute(['id' => $productId]);
        return (float) $stmt->fetchColumn();
    }

    private function calculateFinalPrice(float $harga, string $jenis, float $nilai): float
    {
        return $jenis === 'persen'
            ? $harga - ($harga * $nilai / 100)
            : max(0, $harga - $nilai);
    }

    private function formatDatetime(?string $datetime): ?string
    {
        if (!$datetime) return null;
        return str_replace('T', ' ', $datetime) . (strlen($datetime) === 16 ? ':00' : '');
    }

    private function validateData(array $data): array
    {
        if (!in_array($data['jenis'], ['persen', 'nominal'])) {
            return ['success' => false, 'message' => 'Jenis diskon tidak valid'];
        }

        if ($data['jenis'] === 'persen' && $data['nilai'] > 100) {
            return ['success' => false, 'message' => 'Diskon persen maksimal 100%'];
        }

        return ['success' => true];
    }

    public function massCreate(array $productIds, array $discountData): array
    {
        $success = 0;
        $failed = 0;

        foreach ($productIds as $productId) {
            $data = $discountData;
            $data['id_produk'] = $productId;

            $result = $this->create($data);

            if ($result['success']) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $success > 0,
            'message' => "Berhasil: {$success}, Gagal: {$failed}"
        ];
    }
}
