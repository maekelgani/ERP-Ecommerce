<?php

namespace App\Repository;

use App\Database\DatabaseConnection;
use PDO;

class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getIncomingOrders(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $limit = max(1, min(100, (int)$limit));
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;
        $where = ["o.status_order IN ('pending', 'dikonfirmasi', 'diproses')"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(o.id_order LIKE :search OR c.nama_lengkap LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $where[] = "o.status_order = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $where[] = "p.status_pembayaran = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "DATE(o.tanggal_order) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "DATE(o.tanggal_order) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "
            SELECT COUNT(DISTINCT o.id_order) as total
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            {$whereClause}
        ";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $sql = "
            SELECT 
                o.id_order,
                o.id_customer,
                o.tanggal_order,
                o.total_harga,
                o.total_diskon,
                o.total_ongkir,
                o.total_bayar,
                o.status_order,
                o.catatan_order,
                o.bubble_wrap,
                o.packing_kayu,
                o.biaya_packing,
                o.shipping_method,
                c.nama_lengkap,
                c.email as customer_email,
                c.no_telp as customer_phone,
                p.id_payment,
                p.metode_pembayaran,
                p.nama_bank,
                p.status_pembayaran,
                p.va_number,
                s.id_shipment,
                s.jasa_pengiriman,
                s.no_resi,
                s.nama_penerima,
                s.nomor_hp_penerima,
                s.alamat_pengiriman,
                s.kota as alamat_kota,
                s.kode_pos as alamat_kode_pos,
                s.ongkir,
                s.estimasi_hari,
                s.status_pengiriman,
                (SELECT COUNT(*) FROM order_detail WHERE id_order = o.id_order) as total_items,
                (SELECT COALESCE(SUM(od.harga_satuan * od.jumlah), 0) FROM order_detail od WHERE od.id_order = o.id_order) as original_subtotal,
                (SELECT COALESCE(SUM(od.diskon_satuan * od.jumlah), 0) FROM order_detail od WHERE od.id_order = o.id_order) as product_discount
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            LEFT JOIN shipment s ON o.id_order = s.id_order
            {$whereClause}
            ORDER BY o.tanggal_order DESC
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['voucher_discount'] = max(0, floatval($order['total_diskon'] ?? 0) - floatval($order['product_discount'] ?? 0));
        }

        return [
            'data' => $orders,
            'total' => (int)$totalRows,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalRows / $limit)
        ];
    }

    public function getAllOrders(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $limit = max(1, min(100, (int)$limit));
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(o.id_order LIKE :search OR c.nama_lengkap LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $where[] = "o.status_order = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $where[] = "p.status_pembayaran = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "DATE(o.tanggal_order) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "DATE(o.tanggal_order) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "
            SELECT COUNT(DISTINCT o.id_order) as total
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            {$whereClause}
        ";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $sql = "
            SELECT 
                o.id_order,
                o.id_customer,
                o.tanggal_order,
                o.total_harga,
                o.total_diskon,
                o.total_ongkir,
                o.total_bayar,
                o.status_order,
                o.catatan_order,
                o.bubble_wrap,
                o.packing_kayu,
                o.biaya_packing,
                o.shipping_method,
                c.nama_lengkap,
                c.email as customer_email,
                c.no_telp as customer_phone,
                p.id_payment,
                p.metode_pembayaran,
                p.nama_bank,
                p.status_pembayaran,
                p.tanggal_pembayaran,
                p.va_number,
                s.id_shipment,
                s.jasa_pengiriman,
                s.no_resi,
                s.nama_penerima,
                s.nomor_hp_penerima,
                s.alamat_pengiriman,
                s.kota as alamat_kota,
                s.kode_pos as alamat_kode_pos,
                s.ongkir,
                s.estimasi_hari,
                s.status_pengiriman,
                s.tanggal_dikirim,
                s.tanggal_diterima,
                (SELECT COUNT(*) FROM order_detail WHERE id_order = o.id_order) as total_items,
                (SELECT COALESCE(SUM(od.harga_satuan * od.jumlah), 0) FROM order_detail od WHERE od.id_order = o.id_order) as original_subtotal,
                (SELECT COALESCE(SUM(od.diskon_satuan * od.jumlah), 0) FROM order_detail od WHERE od.id_order = o.id_order) as product_discount
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            LEFT JOIN shipment s ON o.id_order = s.id_order
            {$whereClause}
            ORDER BY o.tanggal_order DESC
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['voucher_discount'] = max(0, floatval($order['total_diskon'] ?? 0) - floatval($order['product_discount'] ?? 0));
        }

        return [
            'data' => $orders,
            'total' => (int)$totalRows,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalRows / $limit)
        ];
    }

    public function getOrderById(string $orderId): ?array
    {
        $sql = "
            SELECT 
                o.*,
                c.nama_lengkap,
                c.email as customer_email,
                c.no_telp as customer_phone,
                p.id_payment,
                p.metode_pembayaran,
                p.nama_bank,
                p.status_pembayaran,
                p.tanggal_pembayaran,
                p.va_number,
                p.total_bayar as payment_total,
                s.id_shipment,
                s.jasa_pengiriman,
                s.no_resi,
                s.nama_penerima,
                s.nomor_hp_penerima,
                s.alamat_pengiriman,
                s.kota as alamat_kota,
                s.kode_pos as alamat_kode_pos,
                s.ongkir,
                s.estimasi_hari,
                s.status_pengiriman,
                s.tanggal_dikirim,
                s.tanggal_diterima
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            LEFT JOIN shipment s ON o.id_order = s.id_order
            WHERE o.id_order = :order_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getOrderItems($orderId);

        return $order;
    }

    public function getOrderItems(string $orderId): array
    {
        $sql = "
            SELECT 
                od.*,
                p.gambar
            FROM order_detail od
            LEFT JOIN products p ON od.id_product = p.id_product
            WHERE od.id_order = :order_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus(string $orderId, string $status): bool
    {
        $validStatuses = ['pending', 'dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE orders SET status_order = :status WHERE id_order = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':order_id' => $orderId]);
    }

    public function updateShipmentStatus(string $orderId, string $status, ?string $noResi = null): bool
    {
        $validStatuses = ['pending', 'dikemas', 'dikirim', 'dalam_perjalanan', 'tiba', 'diterima'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $params = [':status' => $status, ':order_id' => $orderId];
        $setClause = "status_pengiriman = :status";

        if ($noResi !== null) {
            $setClause .= ", no_resi = :no_resi";
            $params[':no_resi'] = $noResi;
        }

        if ($status === 'dikirim') {
            $setClause .= ", tanggal_dikirim = NOW()";
        }

        if ($status === 'diterima') {
            $setClause .= ", tanggal_diterima = NOW()";
            $this->updateOrderStatus($orderId, 'selesai');
        }

        $sql = "UPDATE shipment SET {$setClause} WHERE id_order = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updatePaymentStatus(string $orderId, string $status): bool
    {
        $validStatuses = ['pending', 'verifikasi', 'berhasil', 'gagal'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $setClause = "status_pembayaran = :status";
        if ($status === 'berhasil') {
            $setClause .= ", tanggal_pembayaran = NOW()";
        }

        $sql = "UPDATE payment SET {$setClause} WHERE id_order = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':order_id' => $orderId]);
    }

    public function getOrderStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status_order = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status_order IN ('dikonfirmasi', 'diproses') THEN 1 ELSE 0 END) as processing_orders,
                SUM(CASE WHEN status_order = 'dikirim' THEN 1 ELSE 0 END) as shipping_orders,
                SUM(CASE WHEN status_order = 'selesai' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status_order = 'dibatalkan' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(total_bayar) as total_revenue
            FROM orders
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
