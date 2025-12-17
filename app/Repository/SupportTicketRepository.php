<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class SupportTicketRepository
{
    private \PDO $db;
    private const ID_PREFIX = 'TKT';
    private const ID_LENGTH = 5;
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_FILE_SIZE = 5242880;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                st.*,
                c.nama_lengkap as customer_name,
                a.nama_lengkap as assigned_admin_name,
                (SELECT COUNT(*) FROM ticket_replies tr WHERE tr.id_ticket = st.id_ticket) as reply_count
            FROM support_tickets st
            LEFT JOIN customers c ON st.id_customer = c.id_customer
            LEFT JOIN administrators a ON st.assigned_to = a.id_admin
        ";

        $params = [];
        $where = [];

        if (!empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $where[] = "(LOWER(st.nama_pengaju) LIKE :search OR LOWER(st.email) LIKE :search2 OR LOWER(st.subjek) LIKE :search3 OR st.id_ticket LIKE :search4)";
            $params['search'] = $search;
            $params['search2'] = $search;
            $params['search3'] = $search;
            $params['search4'] = $search;
        }

        if (!empty($filters['status'])) {
            $where[] = "st.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['kategori'])) {
            $where[] = "st.kategori = :kategori";
            $params['kategori'] = $filters['kategori'];
        }

        if (!empty($filters['priority'])) {
            $where[] = "st.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "DATE(st.created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "DATE(st.created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY 
            CASE st.priority 
                WHEN 'Urgent' THEN 1 
                WHEN 'High' THEN 2 
                WHEN 'Medium' THEN 3 
                WHEN 'Low' THEN 4 
            END,
            st.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                a.nama_lengkap as assigned_admin_name
            FROM support_tickets st
            LEFT JOIN customers c ON st.id_customer = c.id_customer
            LEFT JOIN administrators a ON st.assigned_to = a.id_admin
            WHERE st.id_ticket = :id
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data, ?array $attachmentFile = null): array
    {
        $validation = $this->validateData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $sanitizedData = $this->sanitizeData($data);
        $id = $this->generateId();

        $attachmentPath = null;
        if ($attachmentFile && $attachmentFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadAttachment($attachmentFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            $attachmentPath = $uploadResult['filename'];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO support_tickets 
                (id_ticket, id_customer, nama_pengaju, email, no_telepon, subjek, kategori, message, attachment, status, priority)
                VALUES 
                (:id, :id_customer, :nama_pengaju, :email, :no_telepon, :subjek, :kategori, :message, :attachment, 'Open', :priority)
            ");

            $success = $stmt->execute([
                'id' => $id,
                'id_customer' => $sanitizedData['id_customer'],
                'nama_pengaju' => $sanitizedData['nama_pengaju'],
                'email' => $sanitizedData['email'],
                'no_telepon' => $sanitizedData['no_telepon'],
                'subjek' => $sanitizedData['subjek'],
                'kategori' => $sanitizedData['kategori'],
                'message' => $sanitizedData['message'],
                'attachment' => $attachmentPath,
                'priority' => $sanitizedData['priority'] ?? 'Medium'
            ]);

            if ($success && $stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Tiket berhasil dibuat',
                    'id' => $id
                ];
            }

            if ($attachmentPath) {
                $this->deleteAttachment($attachmentPath);
            }

            return [
                'success' => false,
                'message' => 'Gagal membuat tiket'
            ];
        } catch (\PDOException $e) {
            if ($attachmentPath) {
                $this->deleteAttachment($attachmentPath);
            }
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateStatus(string $id, string $status, ?int $assignedTo = null): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan'];
        }

        $validStatuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Status tidak valid'];
        }

        try {
            $sql = "UPDATE support_tickets SET status = :status";
            $params = ['id' => $id, 'status' => $status];

            if ($assignedTo !== null) {
                $sql .= ", assigned_to = :assigned_to";
                $params['assigned_to'] = $assignedTo;
            }

            $sql .= " WHERE id_ticket = :id";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Status tiket berhasil diperbarui'
                ];
            }

            return ['success' => false, 'message' => 'Gagal memperbarui status tiket'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function addReply(string $ticketId, array $data, ?array $attachmentFile = null): array
    {
        $ticket = $this->getById($ticketId);
        if (!$ticket) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan'];
        }

        if (empty($data['message']) || trim($data['message']) === '') {
            return ['success' => false, 'message' => 'Pesan balasan wajib diisi'];
        }

        $attachmentPath = null;
        if ($attachmentFile && $attachmentFile['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadAttachment($attachmentFile);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            $attachmentPath = $uploadResult['filename'];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO ticket_replies 
                (id_ticket, id_admin, id_customer, message, attachment, is_internal_note)
                VALUES 
                (:id_ticket, :id_admin, :id_customer, :message, :attachment, :is_internal_note)
            ");

            $success = $stmt->execute([
                'id_ticket' => $ticketId,
                'id_admin' => $data['id_admin'] ?? null,
                'id_customer' => $data['id_customer'] ?? null,
                'message' => htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => $attachmentPath,
                'is_internal_note' => $data['is_internal_note'] ?? 0
            ]);

            if ($success) {
                $replyId = (int) $this->db->lastInsertId();

                if (!empty($data['update_status'])) {
                    $this->updateStatus(
                        $ticketId,
                        $data['update_status'],
                        $data['id_admin'] ?? null
                    );
                }

                return [
                    'success' => true,
                    'message' => 'Balasan berhasil ditambahkan',
                    'id_reply' => $replyId
                ];
            }

            if ($attachmentPath) {
                $this->deleteAttachment($attachmentPath);
            }

            return ['success' => false, 'message' => 'Gagal menambahkan balasan'];
        } catch (\PDOException $e) {
            if ($attachmentPath) {
                $this->deleteAttachment($attachmentPath);
            }
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }


    public function getReplies(string $ticketId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                tr.*,
                a.nama_lengkap as admin_name,
                c.nama_lengkap as customer_name
            FROM ticket_replies tr
            LEFT JOIN administrators a ON tr.id_admin = a.id_admin
            LEFT JOIN customers c ON tr.id_customer = c.id_customer
            WHERE tr.id_ticket = :id_ticket
            ORDER BY tr.created_at ASC
        ");
        $stmt->execute(['id_ticket' => $ticketId]);
        return $stmt->fetchAll();
    }

    public function delete(string $id): array
    {
        $existing = $this->getById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan'];
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM support_tickets WHERE id_ticket = :id");
            $success = $stmt->execute(['id' => $id]);

            if ($success && $stmt->rowCount() > 0) {
                if ($existing['attachment']) {
                    $this->deleteAttachment($existing['attachment']);
                }
                return ['success' => true, 'message' => 'Tiket berhasil dihapus'];
            }

            return ['success' => false, 'message' => 'Gagal menghapus tiket'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM support_tickets";
        $params = [];
        $where = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    public function getStatistics(): array
    {
        $stats = [
            'total' => 0,
            'open' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'closed' => 0
        ];

        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM support_tickets 
            GROUP BY status
        ");

        while ($row = $stmt->fetch()) {
            $key = strtolower(str_replace(' ', '_', $row['status']));
            $stats[$key] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }

        return $stats;
    }

    public function getNextId(): string
    {
        return $this->generateId();
    }

    private function generateId(): string
    {
        $maxRetries = 5;
        for ($i = 0; $i < $maxRetries; $i++) {
            $stmt = $this->db->query("SELECT id_ticket FROM support_tickets ORDER BY id_ticket DESC LIMIT 1 FOR UPDATE");
            $last = $stmt->fetch();

            if ($last) {
                $num = (int) substr($last['id_ticket'], strlen(self::ID_PREFIX)) + 1;
            } else {
                $num = 1;
            }

            $newId = self::ID_PREFIX . str_pad($num, self::ID_LENGTH, '0', STR_PAD_LEFT);

            $checkStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM support_tickets WHERE id_ticket = :id");
            $checkStmt->execute(['id' => $newId]);
            if ((int)$checkStmt->fetch()['cnt'] === 0) {
                return $newId;
            }
        }

        return self::ID_PREFIX . strtoupper(bin2hex(random_bytes(3)));
    }

    private function validateData(array $data): array
    {
        if (empty($data['nama_pengaju']) || trim($data['nama_pengaju']) === '') {
            return ['success' => false, 'message' => 'Nama pengaju wajib diisi'];
        }

        if (empty($data['email']) || trim($data['email']) === '') {
            return ['success' => false, 'message' => 'Email wajib diisi'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }

        if (empty($data['subjek']) || trim($data['subjek']) === '') {
            return ['success' => false, 'message' => 'Subjek wajib diisi'];
        }

        if (empty($data['message']) || trim($data['message']) === '') {
            return ['success' => false, 'message' => 'Pesan wajib diisi'];
        }

        $validCategories = ['General', 'Garansi & Servis', 'Aktivasi Akun', 'Komplain', 'Pertanyaan Produk', 'Status Pesanan'];
        if (!empty($data['kategori']) && !in_array($data['kategori'], $validCategories)) {
            return ['success' => false, 'message' => 'Kategori tidak valid'];
        }

        return ['success' => true];
    }

    private function sanitizeData(array $data): array
    {
        return [
            'id_customer' => !empty($data['id_customer']) ? (int)$data['id_customer'] : null,
            'nama_pengaju' => $this->sanitizeString($data['nama_pengaju']),
            'email' => filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL),
            'no_telepon' => !empty($data['no_telepon']) ? $this->sanitizeString($data['no_telepon']) : null,
            'subjek' => $this->sanitizeString($data['subjek']),
            'kategori' => $data['kategori'] ?? 'General',
            'message' => $this->sanitizeString($data['message']),
            'priority' => $data['priority'] ?? 'Medium'
        ];
    }

    private function sanitizeString(string $input): string
    {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return $sanitized;
    }

    private function uploadAttachment(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Error saat upload file'];
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB'];
        }

        if ($file['size'] === 0) {
            return ['success' => false, 'message' => 'File tidak boleh kosong'];
        }

        $originalName = basename($file['name']);
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_EXTENSIONS)];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return ['success' => false, 'message' => 'Tipe file tidak valid'];
        }

        $extensionMimeMap = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'pdf' => ['application/pdf']
        ];

        if (!isset($extensionMimeMap[$extension]) || !in_array($mimeType, $extensionMimeMap[$extension])) {
            return ['success' => false, 'message' => 'Ekstensi file tidak sesuai dengan tipe file'];
        }

        $uploadDir = __DIR__ . '/../../uploads/tickets/';
        $realUploadDir = realpath(__DIR__ . '/../../uploads');

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Gagal membuat direktori upload'];
            }
        }

        $safeFilename = 'ticket_' . bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $safeFilename;

        $realFilePath = realpath(dirname($filepath));
        if ($realFilePath === false || strpos($realFilePath, $realUploadDir) !== 0) {
            return ['success' => false, 'message' => 'Path file tidak valid'];
        }

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            chmod($filepath, 0644);
            return ['success' => true, 'filename' => $safeFilename];
        }

        return ['success' => false, 'message' => 'Gagal menyimpan file'];
    }

    private function deleteAttachment(string $filename): bool
    {
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../uploads/tickets/' . $filename;
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    public function getResolvedByCategory(): array
    {
        $stmt = $this->db->query("
            SELECT 
                st.id_ticket,
                st.subjek,
                st.kategori,
                st.message AS fallback_answer,
                (
                    SELECT tr.message
                    FROM ticket_replies tr
                    WHERE tr.id_ticket = st.id_ticket
                        AND tr.id_admin IS NOT NULL
                        AND tr.is_internal_note = 0
                    ORDER BY tr.created_at DESC
                    LIMIT 1
                ) AS admin_answer

            FROM support_tickets st
            WHERE st.status IN ('Resolved', 'Closed')
            ORDER BY st.kategori, st.created_at DESC
        ");

        $results = [];

        while ($row = $stmt->fetch()) {

            // PRIORITAS: admin → fallback ke ticket message
            $answer = $row['admin_answer'] ?: $row['fallback_answer'];

            if (!empty($answer)) {
                $results[$row['kategori']][] = [
                    'id'       => $row['id_ticket'],
                    'question' => htmlspecialchars_decode($row['subjek'], ENT_QUOTES),
                    'answer'   => htmlspecialchars_decode($answer, ENT_QUOTES),
                    'category' => $row['kategori']
                ];
            }
        }

        return $results;
    }

    public function getFaqList(): array
    {
        $stmt = $this->db->query("
            SELECT 
                st.kategori,
                st.subjek,
                st.message AS ticket_message,
                (
                    SELECT tr.message
                    FROM ticket_replies tr
                    WHERE tr.id_ticket = st.id_ticket
                      AND tr.id_admin IS NOT NULL
                      AND tr.is_internal_note = 0
                    ORDER BY tr.created_at DESC
                    LIMIT 1
                ) AS admin_reply
            FROM support_tickets st
            WHERE st.is_faq = 1
              AND st.status IN ('Resolved','Closed')
            ORDER BY st.kategori, st.updated_at DESC
        ");

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[$row['kategori']][] = [
                'question' => $row['subjek'], // ← JUDUL
                'answer'   => $row['admin_reply'] ?: $row['ticket_message'], // ← ISI
                'answered_by_admin' => !empty($row['admin_reply'])
            ];
        }

        return $data;
    }


    // public function getFaqList(): array
    // {
    //     $stmt = $this->db->query("
    //     SELECT 
    //         st.id_ticket,
    //         st.subjek,
    //         st.kategori,
    //         st.message AS ticket_message,
    //         (
    //             SELECT tr.message
    //             FROM ticket_replies tr
    //             WHERE tr.id_ticket = st.id_ticket
    //                 AND tr.id_admin IS NOT NULL
    //                 AND tr.is_internal_note = 0
    //             ORDER BY tr.created_at DESC
    //             LIMIT 1
    //         ) AS admin_answer
    //     FROM support_tickets st
    //     WHERE st.is_faq = 1
    //         AND st.status IN ('Resolved','Closed')
    //     ORDER BY st.kategori, st.updated_at DESC
    // ");

    //     $data = [];

    //     while ($row = $stmt->fetch()) {

    //         $answer = $row['admin_answer'] ?: $row['ticket_message'];
    //         $answeredByAdmin = !empty($row['admin_answer']);

    //         $data[$row['kategori']][] = [
    //             'id' => $row['id_ticket'],
    //             'question' => $row['subjek'],
    //             'answer' => $answer,
    //             'answered_by_admin' => $answeredByAdmin
    //         ];
    //     }

    //     return $data;
    // }

    public function updateIsFaq(string $ticketId, int $isFaq): void
    {
        $stmt = $this->db->prepare("
            UPDATE support_tickets
            SET is_faq = :is_faq
            WHERE id_ticket = :id
        ");
        $stmt->execute([
            ':is_faq' => $isFaq,
            ':id'     => $ticketId
        ]);
    }

    public function getReplyById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT id_reply, id_admin FROM ticket_replies WHERE id_reply = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTicketIdByReply(int $replyId): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT id_ticket FROM ticket_replies WHERE id_reply = ?"
        );
        $stmt->execute([$replyId]);
        return $stmt->fetchColumn() ?: null;
    }

    public function hasPublicAdminReply(string $ticketId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM ticket_replies
            WHERE id_ticket = ?
                AND id_admin IS NOT NULL
                AND is_internal_note = 0
        ");
        $stmt->execute([$ticketId]);
        return (int)$stmt->fetchColumn() > 0;
    }



    public function deleteReply($id)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM ticket_replies WHERE id_reply = ?"
        );
        return $stmt->execute([$id]);
    }

    public function deleteAllAdminReplies(string $ticketId): int
    {
        $stmt = $this->db->prepare("
        DELETE FROM ticket_replies
        WHERE id_ticket = :id_ticket
            AND id_admin IS NOT NULL
    ");

        $stmt->execute([
            ':id_ticket' => $ticketId
        ]);

        return $stmt->rowCount(); // jumlah balasan yang terhapus
    }

    public function forceStatusIfInvalid(string $ticketId): void
    {
        $stmt = $this->db->prepare("
        UPDATE support_tickets
        SET status = 'In Progress'
        WHERE id_ticket = ?
            AND status IN ('Resolved', 'Closed')
    ");
        $stmt->execute([$ticketId]);
    }
}
