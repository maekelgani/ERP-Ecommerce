<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/SupportTicketRepository.php';

use App\Repository\SupportTicketRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

$rateLimitKey = 'contact_' . md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
if (isset($_SESSION[$rateLimitKey])) {
    $lastSubmit = $_SESSION[$rateLimitKey];
    if (time() - $lastSubmit < 60) {
        echo json_encode(['success' => false, 'message' => 'Harap tunggu 1 menit sebelum mengirim pesan lagi']);
        exit;
    }
}
$_SESSION[$rateLimitKey] = time();

$ticketRepo = new SupportTicketRepository();

$customerId = null;
if (isset($_SESSION['customer_id'])) {
    $customerId = (int)$_SESSION['customer_id'];
}

$validCategories = [
    'product' => 'Pertanyaan Produk',
    'order' => 'Status Pesanan',
    'warranty' => 'Garansi & Servis',
    'partnership' => 'General',
    'complaint' => 'Komplain',
    'account' => 'Aktivasi Akun',
    'other' => 'General'
];

$subjectValue = $_POST['subject'] ?? 'other';
if (!isset($validCategories[$subjectValue])) {
    $subjectValue = 'other';
}
$kategori = $validCategories[$subjectValue];

$validSubjects = [
    'product' => 'Pertanyaan Produk',
    'order' => 'Status Pesanan',
    'warranty' => 'Garansi & Servis',
    'partnership' => 'Kerjasama Bisnis',
    'complaint' => 'Komplain',
    'account' => 'Aktivasi Akun',
    'other' => 'Pertanyaan Umum'
];
$subjek = $validSubjects[$subjectValue] ?? 'Pertanyaan Umum';

$data = [
    'id_customer' => $customerId,
    'nama_pengaju' => substr(trim($_POST['name'] ?? ''), 0, 100),
    'email' => $email,
    'no_telepon' => substr(preg_replace('/[^0-9\-\+\s]/', '', $_POST['phone'] ?? ''), 0, 20),
    'subjek' => $subjek,
    'kategori' => $kategori,
    'message' => substr(trim($_POST['message'] ?? ''), 0, 5000),
    'priority' => 'Medium'
];

$attachmentFile = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    $attachmentFile = $_FILES['attachment'];
}

$result = $ticketRepo->create($data, $attachmentFile);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Pesan Anda telah terkirim! Kami akan segera menghubungi Anda. Nomor tiket: ' . $result['id'],
        'ticket_id' => $result['id']
    ]);
} else {
    echo json_encode($result);
}
