<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

// Ensure user is logged in as admin using project-standard session variable
if (!isset($_SESSION['auserid'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Admin session required.'
    ]);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_vip_configs':
        $stmt = $pdo->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC");
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            'status' => 'success',
            'data' => $configs
        ]);
        break;

    case 'evaluate_qualifications':
        $user_id = trim($_POST['user_id'] ?? $_GET['user_id'] ?? '');
        if (empty($user_id)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'User ID is required.'
            ]);
            exit;
        }

        $res = evaluateUserVIPQualifications($user_id, $pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $res
        ]);
        break;

    case 'get_qualifications':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $limit = (int)($_GET['limit'] ?? 500);

        $query = "
            SELECT 
                q.*,
                u.name as username,
                u.vip_club_wallet
            FROM tbl_vip_user_qualification q
            LEFT JOIN user u ON u.userid = q.user_id
            WHERE 1=1
        ";

        $params = [];
        if (!empty($user_id)) {
            $query .= " AND q.user_id = :user_id";
            $params[':user_id'] = $user_id;
        }

        $query .= " ORDER BY q.id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $quals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $quals
        ]);
        break;

    case 'get_schedules':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $closing_month = trim($_GET['closing_month'] ?? $_POST['closing_month'] ?? '');
        $limit = (int)($_GET['limit'] ?? 500);

        $query = "
            SELECT 
                s.*,
                u.name as username,
                u.vip_club_wallet
            FROM tbl_vip_monthly_schedule s
            LEFT JOIN user u ON u.userid = s.user_id
            WHERE 1=1
        ";

        $params = [];
        if (!empty($user_id)) {
            $query .= " AND s.user_id = :user_id";
            $params[':user_id'] = $user_id;
        }
        if (!empty($closing_month)) {
            $query .= " AND s.closing_month = :closing_month";
            $params[':closing_month'] = $closing_month;
        }

        $query .= " ORDER BY s.id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $schedules
        ]);
        break;

    case 'adjust_balance':
        $admin_id = $_SESSION['auserid'];
        $user_id = trim($_POST['user_id'] ?? '');
        $adjustment_type = strtoupper(trim($_POST['type'] ?? ''));
        $amount = (float)($_POST['amount'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $reference = trim($_POST['reference'] ?? '');

        if (empty($user_id) || !in_array($adjustment_type, ['CREDIT', 'DEBIT']) || $amount <= 0 || empty($reason)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid parameters. User ID, valid type (CREDIT/DEBIT), positive amount, and reason are required.'
            ]);
            exit;
        }

        $res = processAdminVIPAdjustment($admin_id, $user_id, $adjustment_type, $amount, $reason, $reference, $pdo);
        echo json_encode($res);
        break;

    case 'get_audit_history':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $limit = (int)($_GET['limit'] ?? 100);
        if ($limit <= 0 || $limit > 1000) {
            $limit = 100;
        }

        $query = "SELECT a.*, u.auserid as admin_name, target.name as target_username 
                  FROM tbl_vip_admin_audit a
                  LEFT JOIN admin u ON (CONVERT(a.admin_id USING utf8mb4) = CONVERT(u.auserid USING utf8mb4) OR CONVERT(a.admin_id USING utf8mb4) = CONVERT(u.id USING utf8mb4))
                  LEFT JOIN user target ON CONVERT(a.user_id USING utf8mb4) = CONVERT(target.userid USING utf8mb4)";

        $params = [];
        if (!empty($user_id)) {
            $query .= " WHERE a.user_id = :user_id";
            $params[':user_id'] = $user_id;
        }

        $query .= " ORDER BY a.id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $history
        ]);
        break;

    case 'trigger_11th_closing':
        $closing_month = trim($_POST['closing_month'] ?? $_GET['closing_month'] ?? date('Y-m'));
        $closing_date  = trim($_POST['closing_date'] ?? $_GET['closing_date'] ?? date('Y-m-d'));
        $bypass_check  = isset($_POST['bypass_date_check']) && $_POST['bypass_date_check'] == '1';

        $res = processVIPMonthlyIncome($closing_month, $closing_date, $pdo, $bypass_check);
        echo json_encode($res);
        break;

    case 'get_report_stats':
        $stats = getVIPClubReportStats($pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action requested.'
        ]);
        break;
}
