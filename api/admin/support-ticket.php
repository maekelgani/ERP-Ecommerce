<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/SupportTicketRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\SupportTicketRepository;

header('Content-Type: application/json');

AuthMiddleware::requireAdminLoginFromView();

$ticketRepo = new SupportTicketRepository();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        handleList($ticketRepo);
        break;
    case 'get':
        handleGet($ticketRepo);
        break;
    case 'getReplies':
        handleGetReplies($ticketRepo);
        break;
    case 'reply':
        handleReply($ticketRepo);
        break;
    case 'updateStatus':
        handleUpdateStatus($ticketRepo);
        break;
    case 'delete':
        handleDelete($ticketRepo);
        break;
    case 'statistics':
        handleStatistics($ticketRepo);
        break;
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleList(SupportTicketRepository $repo): void
{
    $filters = [];
    
    if (!empty($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    if (!empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    if (!empty($_GET['kategori'])) {
        $filters['kategori'] = $_GET['kategori'];
    }
    if (!empty($_GET['priority'])) {
        $filters['priority'] = $_GET['priority'];
    }
    if (!empty($_GET['date_from'])) {
        $filters['date_from'] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $filters['date_to'] = $_GET['date_to'];
    }

    $tickets = $repo->getAll($filters);
    $stats = $repo->getStatistics();
    
    sendJsonResponse([
        'success' => true,
        'data' => $tickets,
        'statistics' => $stats
    ]);
}

function handleGet(SupportTicketRepository $repo): void
{
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID tiket tidak valid']);
        return;
    }

    $ticket = $repo->getById($id);
    if (!$ticket) {
        sendJsonResponse(['success' => false, 'message' => 'Tiket tidak ditemukan']);
        return;
    }

    $replies = $repo->getReplies($id);

    sendJsonResponse([
        'success' => true,
        'data' => $ticket,
        'replies' => $replies
    ]);
}

function handleGetReplies(SupportTicketRepository $repo): void
{
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID tiket tidak valid']);
        return;
    }

    $replies = $repo->getReplies($id);
    sendJsonResponse(['success' => true, 'data' => $replies]);
}

function handleReply(SupportTicketRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'message' => 'Metode request tidak valid']);
        return;
    }

    $ticketId = $_POST['id_ticket'] ?? '';
    if (empty($ticketId)) {
        sendJsonResponse(['success' => false, 'message' => 'ID tiket tidak valid']);
        return;
    }

    $adminId = $_SESSION['admin_id'] ?? null;
    
    if (empty($adminId)) {
        sendJsonResponse(['success' => false, 'message' => 'Unauthorized: Admin ID tidak ditemukan']);
        return;
    }
    
    $data = [
        'id_admin' => (int)$adminId,
        'message' => $_POST['message'] ?? '',
        'is_internal_note' => isset($_POST['is_internal_note']) ? (int)$_POST['is_internal_note'] : 0,
        'update_status' => $_POST['update_status'] ?? null
    ];

    $validStatuses = ['', 'Open', 'In Progress', 'Resolved', 'Closed'];
    if (!empty($data['update_status']) && !in_array($data['update_status'], $validStatuses)) {
        sendJsonResponse(['success' => false, 'message' => 'Status tidak valid']);
        return;
    }

    $attachmentFile = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $attachmentFile = $_FILES['attachment'];
    }

    $result = $repo->addReply($ticketId, $data, $attachmentFile);
    sendJsonResponse($result);
}

function handleUpdateStatus(SupportTicketRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'message' => 'Metode request tidak valid']);
        return;
    }

    $ticketId = $_POST['id_ticket'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($ticketId) || empty($status)) {
        sendJsonResponse(['success' => false, 'message' => 'Data tidak lengkap']);
        return;
    }

    $validStatuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
    if (!in_array($status, $validStatuses)) {
        sendJsonResponse(['success' => false, 'message' => 'Status tidak valid']);
        return;
    }

    $adminId = $_SESSION['admin_id'] ?? null;
    if (empty($adminId)) {
        sendJsonResponse(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    
    $result = $repo->updateStatus($ticketId, $status, (int)$adminId);
    sendJsonResponse($result);
}

function handleDelete(SupportTicketRepository $repo): void
{
    $id = $_GET['id'] ?? $_POST['id'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID tiket tidak valid']);
        return;
    }

    $result = $repo->delete($id);
    sendJsonResponse($result);
}

function handleStatistics(SupportTicketRepository $repo): void
{
    $stats = $repo->getStatistics();
    sendJsonResponse(['success' => true, 'data' => $stats]);
}

function sendJsonResponse(array $data): void
{
    echo json_encode($data);
    exit;
}
