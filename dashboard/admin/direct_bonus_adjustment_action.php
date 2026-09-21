<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once 'common/db_method.php';

// Ensure user is logged in as admin using project-standard session variable
if (!isset($_SESSION['auserid'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Admin session required.'
    ]);
    exit;
}

$admin_id = $_SESSION['auserid'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'adjust_balance':
        $user_id = trim($_POST['user_id'] ?? '');
        $adjustment_type = strtoupper(trim($_POST['type'] ?? ''));
        $amount = (float)($_POST['amount'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if (empty($user_id) || !in_array($adjustment_type, ['CREDIT', 'DEBIT']) || $amount <= 0 || empty($reason)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid parameters. User ID, valid type (CREDIT/DEBIT), positive amount, and reason are required.'
            ]);
            exit;
        }

        $res = processAdminDirectBonusAdjustment($admin_id, $user_id, $adjustment_type, $amount, $reason, '', $pdo);
        echo json_encode($res);
        break;

    case 'get_qualified_details':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');

        if (empty($user_id)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'User ID is required.'
            ]);
            exit;
        }

        $details = getQualifiedDirectDetails($user_id, $pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $details
        ]);
        break;

    case 'get_audit_history':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $limit = (int)($_GET['limit'] ?? 100);
        if ($limit <= 0 || $limit > 1000) {
            $limit = 100;
        }

        $query = "SELECT a.*, u.auserid as admin_name, target.name as target_username 
                  FROM tbl_direct_bonus_admin_audit a
                  LEFT JOIN admin u ON (a.admin_id = u.auserid OR a.admin_id = CAST(u.id AS CHAR))
                  LEFT JOIN user target ON a.user_id = target.userid";

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

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action requested.'
        ]);
        break;
}
