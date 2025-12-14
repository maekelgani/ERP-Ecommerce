<?php

require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Repository\AnalyticsRepository;

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

AuthMiddleware::requireAdminLoginFromView();

$action = isset($_GET['action']) ? preg_replace('/[^a-z\-]/', '', strtolower($_GET['action'])) : '';
$period = isset($_GET['period']) ? preg_replace('/[^a-z0-9_]/', '', strtolower($_GET['period'])) : 'all';

$validPeriods = ['today', '7', '30', '90', 'all', 'this_month', 'this_year'];
if (!in_array($period, $validPeriods)) {
    $period = 'all';
}

$validActions = ['stats', 'monthly-revenue', 'orders-users', 'category-sales', 'top-products', 'order-status', 'payment-methods', 'daily-revenue', 'revenue-growth', 'dashboard'];
if (!in_array($action, $validActions) && $action !== '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action. Available: ' . implode(', ', $validActions)
    ]);
    exit;
}

$analyticsRepo = new AnalyticsRepository();

try {
    $response = ['success' => true];

    switch ($action) {
        case 'stats':
            $response['data'] = [
                'totalRevenue' => $analyticsRepo->getTotalRevenue($period),
                'avgPurchase' => $analyticsRepo->getAveragePurchasePrice($period),
                'productsSold' => $analyticsRepo->getProductsSold($period),
                'avgRating' => $analyticsRepo->getAverageProductRating(),
                'totalOrders' => $analyticsRepo->getTotalOrders($period),
                'totalCustomers' => $analyticsRepo->getTotalCustomers($period)
            ];
            break;

        case 'monthly-revenue':
            $months = isset($_GET['months']) ? max(1, min(12, (int) $_GET['months'])) : 6;
            $response['data'] = $analyticsRepo->getMonthlyRevenue($months);
            break;

        case 'orders-users':
            $months = isset($_GET['months']) ? max(1, min(12, (int) $_GET['months'])) : 6;
            $response['data'] = $analyticsRepo->getMonthlyOrdersAndUsers($months);
            break;

        case 'category-sales':
            $limit = isset($_GET['limit']) ? max(1, min(20, (int) $_GET['limit'])) : 5;
            $response['data'] = $analyticsRepo->getCategorySales($period, $limit);
            break;

        case 'top-products':
            $limit = isset($_GET['limit']) ? max(1, min(20, (int) $_GET['limit'])) : 5;
            $response['data'] = $analyticsRepo->getTopSellingProducts($period, $limit);
            break;

        case 'order-status':
            $response['data'] = $analyticsRepo->getOrdersByStatus();
            break;

        case 'payment-methods':
            $response['data'] = $analyticsRepo->getPaymentMethodDistribution($period);
            break;

        case 'daily-revenue':
            $days = isset($_GET['days']) ? max(1, min(365, (int) $_GET['days'])) : 30;
            $response['data'] = $analyticsRepo->getDailyRevenue($days);
            break;

        case 'revenue-growth':
            $response['data'] = $analyticsRepo->getRevenueGrowth($period);
            break;

        case 'dashboard':
            $response['data'] = [
                'stats' => [
                    'totalRevenue' => $analyticsRepo->getTotalRevenue($period),
                    'avgPurchase' => $analyticsRepo->getAveragePurchasePrice($period),
                    'productsSold' => $analyticsRepo->getProductsSold($period),
                    'avgRating' => $analyticsRepo->getAverageProductRating()
                ],
                'charts' => [
                    'monthlyRevenue' => $analyticsRepo->getMonthlyRevenue(6),
                    'ordersAndUsers' => $analyticsRepo->getMonthlyOrdersAndUsers(6),
                    'categorySales' => $analyticsRepo->getCategorySales($period, 5)
                ],
                'topProducts' => $analyticsRepo->getTopSellingProducts($period, 5)
            ];
            break;

        default:
            $response = [
                'success' => false,
                'message' => 'Action required. Available: ' . implode(', ', $validActions)
            ];
            http_response_code(400);
    }

    echo json_encode($response);
} catch (\PDOException $e) {
    error_log('Analytics DB Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (\Exception $e) {
    error_log('Analytics Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request'
    ]);
}
