<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/SupportTicketRepository.php';

header('Content-Type: application/json');

use App\Auth\CustomerAuthMiddleware;
use App\Repository\SupportTicketRepository;

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $repository = new SupportTicketRepository();

    if ($method === 'POST' && $action === 'create') {
        $customerId = null;
        if (CustomerAuthMiddleware::isLoggedIn()) {
            $customerId = CustomerAuthMiddleware::getCustomerId();
        }

        $data = [
            'id_customer' => $customerId,
            'nama_pengaju' => trim($_POST['nama_lengkap'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'no_telepon' => trim($_POST['no_telepon'] ?? ''),
            'subjek' => trim($_POST['subjek'] ?? ''),
            'kategori' => $_POST['kategori'] ?? 'General',
            'message' => trim($_POST['message'] ?? ''),
            'priority' => 'Medium'
        ];

        $attachmentFile = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $attachmentFile = $_FILES['attachment'];
        }

        $result = $repository->create($data, $attachmentFile);

        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.',
                'ticket_id' => $result['id'] ?? null
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal mengirim pesan'
            ]);
        }
    } elseif ($method === 'GET' && $action === 'faq') {
        $faqs = $repository->getResolvedByCategory();
        echo json_encode([
            'success' => true,
            'data' => $faqs
        ]);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method tidak didukung']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server']);
}
