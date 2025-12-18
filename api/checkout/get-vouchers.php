<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();
$subtotal = isset($_GET['subtotal']) ? floatval($_GET['subtotal']) : 0;

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        SELECT 
            v.id_voucher,
            v.kode,
            v.judul,
            v.deskripsi,
            v.jenis,
            v.nilai,
            v.minimal_belanja,
            v.maksimal_diskon,
            v.mulai_pada,
            v.selesai_pada,
            v.kuota_total,
            v.kuota_terpakai,
            v.kuota_per_pengguna,
            COALESCE(
                (SELECT COUNT(*) FROM penggunaan_voucher 
                 WHERE id_voucher = v.id_voucher AND id_pengguna = :customer_id), 0
            ) as usage_count
        FROM voucher v
        WHERE v.status = 'aktif'
        AND (v.mulai_pada IS NULL OR v.mulai_pada <= NOW())
        AND (v.selesai_pada IS NULL OR v.selesai_pada >= NOW())
        AND (v.kuota_total IS NULL OR v.kuota_terpakai < v.kuota_total)
        ORDER BY v.selesai_pada ASC
    ");
    
    $stmt->execute([':customer_id' => $customerId]);
    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($vouchers as $voucher) {
        $canUse = true;
        $reason = '';
        
        if ($voucher['kuota_per_pengguna'] && $voucher['usage_count'] >= $voucher['kuota_per_pengguna']) {
            $canUse = false;
            $reason = 'Kuota penggunaan Anda sudah habis';
        }
        
        if ($subtotal > 0 && $voucher['minimal_belanja'] && $subtotal < floatval($voucher['minimal_belanja'])) {
            $canUse = false;
            $reason = 'Min. belanja Rp ' . number_format($voucher['minimal_belanja'], 0, ',', '.');
        }
        
        $quotaPercent = 0;
        if ($voucher['kuota_total']) {
            $quotaPercent = min(100, ($voucher['kuota_terpakai'] / $voucher['kuota_total']) * 100);
        }
        
        $result[] = [
            'id' => $voucher['id_voucher'],
            'kode' => $voucher['kode'],
            'judul' => $voucher['judul'],
            'deskripsi' => $voucher['deskripsi'],
            'jenis' => $voucher['jenis'],
            'nilai' => floatval($voucher['nilai']),
            'minimal_belanja' => floatval($voucher['minimal_belanja']),
            'maksimal_diskon' => $voucher['maksimal_diskon'] ? floatval($voucher['maksimal_diskon']) : null,
            'mulai_pada' => $voucher['mulai_pada'],
            'selesai_pada' => $voucher['selesai_pada'],
            'kuota_total' => $voucher['kuota_total'] ? intval($voucher['kuota_total']) : null,
            'kuota_terpakai' => intval($voucher['kuota_terpakai']),
            'kuota_percent' => round($quotaPercent, 1),
            'can_use' => $canUse,
            'reason' => $reason
        ];
    }
    
    echo json_encode([
        'success' => true,
        'vouchers' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal memuat voucher']);
}
