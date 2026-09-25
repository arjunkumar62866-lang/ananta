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

$admin_id = $_SESSION['auserid'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_dashboard_stats':
        $stats = getAdminComprehensiveDashboardStats($pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
        break;

    case 'get_system_controls':
        $controls = getSystemControls($pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $controls
        ]);
        break;

    case 'toggle_system_control':
        $key   = trim($_POST['key'] ?? '');
        $value = trim($_POST['value'] ?? '0');

        if (empty($key)) {
            echo json_encode(['status' => 'error', 'message' => 'Setting key is required.']);
            exit;
        }

        $res = setSystemControl($key, $value, $admin_id, $pdo);
        if ($res) {
            echo json_encode(['status' => 'success', 'message' => "Successfully updated {$key} setting to {$value}."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Failed to update {$key} setting."]);
        }
        break;

    case 'universal_wallet_adjustment':
        $target_user_id = trim($_POST['user_id'] ?? '');
        $wallet_type    = trim($_POST['wallet_type'] ?? '');
        $adj_type       = strtoupper(trim($_POST['type'] ?? ''));
        $amount         = (float)($_POST['amount'] ?? 0);
        $reason         = trim($_POST['reason'] ?? '');
        $reference      = trim($_POST['reference'] ?? '');

        $res = processUniversalAdminWalletAdjustment($admin_id, $target_user_id, $wallet_type, $adj_type, $amount, $reason, $reference, $pdo);
        echo json_encode($res);
        break;

    case 'update_user_status':
        $target_user_id = trim($_POST['user_id'] ?? '');
        $new_status     = trim($_POST['status'] ?? ''); // '1' = Active, '0' = Pending/Inactive, 'BLOCKED' = Blocked
        $reason         = trim($_POST['reason'] ?? 'Admin status update');

        if (empty($target_user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
            exit;
        }

        if ($new_status === 'BLOCKED') {
            $stmt = $pdo->prepare("UPDATE user SET active = '0', status = '0' WHERE userid = :uid");
            $stmt->execute([':uid' => $target_user_id]);
            logAdminAuditAction($admin_id, 'USER_STATUS_CHANGE', $target_user_id, 0.00, 'active', 1, 0, "User account BLOCKED/SUSPENDED: {$reason}", null, $pdo);
            echo json_encode(['status' => 'success', 'message' => "User {$target_user_id} account blocked/suspended successfully."]);
        } elseif ($new_status === '1') {
            $stmt = $pdo->prepare("UPDATE user SET active = '1', status = '1' WHERE userid = :uid");
            $stmt->execute([':uid' => $target_user_id]);
            logAdminAuditAction($admin_id, 'USER_STATUS_CHANGE', $target_user_id, 0.00, 'active', 0, 1, "User account ACTIVATED: {$reason}", null, $pdo);
            echo json_encode(['status' => 'success', 'message' => "User {$target_user_id} account activated successfully."]);
        } else {
            $stmt = $pdo->prepare("UPDATE user SET active = '0' WHERE userid = :uid");
            $stmt->execute([':uid' => $target_user_id]);
            logAdminAuditAction($admin_id, 'USER_STATUS_CHANGE', $target_user_id, 0.00, 'active', 1, 0, "User account DEACTIVATED: {$reason}", null, $pdo);
            echo json_encode(['status' => 'success', 'message' => "User {$target_user_id} account deactivated."]);
        }
        break;

    case 'toggle_user_withdrawal':
        $target_user_id = trim($_POST['user_id'] ?? '');
        $status         = (int)($_POST['status'] ?? 1); // 1 = ON, 0 = OFF

        if (empty($target_user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE user SET withdrawal_status = :st WHERE userid = :uid");
        $stmt->execute([':st' => $status, ':uid' => $target_user_id]);

        logAdminAuditAction($admin_id, 'USER_WITHDRAWAL_TOGGLE', $target_user_id, 0.00, 'withdrawal_status', 0, $status, "Set withdrawal status to {$status} for user {$target_user_id}", null, $pdo);

        echo json_encode(['status' => 'success', 'message' => "Withdrawal status updated to " . ($status ? 'ENABLED' : 'DISABLED') . " for user {$target_user_id}."]);
        break;

    case 'get_admin_audit_logs':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $limit   = (int)($_GET['limit'] ?? 100);

        $query = "SELECT l.*, u.name as target_username FROM tbl_admin_audit_log l LEFT JOIN user u ON l.target_user_id = u.userid WHERE 1=1";
        $params = [];
        if (!empty($user_id)) {
            $query .= " AND l.target_user_id = :uid";
            $params[':uid'] = $user_id;
        }
        $query .= " ORDER BY l.id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $logs]);
        break;

    case 'delete_user_account_permanent':
        $target_user_id = trim($_POST['user_id'] ?? '');

        if (empty($target_user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
            exit;
        }

        $res = processPermanentUserAccountDeletion($admin_id, $target_user_id, $pdo);
        echo json_encode($res);
        break;

    case 'get_user_tree_details':
        $user_id = trim($_POST['user_id'] ?? $_GET['user_id'] ?? '');
        $data = getTreeUserDetails($user_id, $pdo);
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "User {$user_id} not found."]);
        }
        break;

    case 'get_new_parent_availability':
        $target_user_id = trim($_POST['target_user_id'] ?? $_GET['target_user_id'] ?? '');
        $new_parent_id  = trim($_POST['new_parent_id'] ?? $_GET['new_parent_id'] ?? '');

        $res = getNewParentAvailability($target_user_id, $new_parent_id, $pdo);
        echo json_encode($res);
        break;

    case 'move_team_in_tree':
        $target_user_id  = trim($_POST['target_user_id'] ?? '');
        $new_parent_id   = trim($_POST['new_parent_id'] ?? '');
        $target_position = trim($_POST['target_position'] ?? '');
        $reason          = trim($_POST['reason'] ?? '');

        $res = processAdminMoveTeamInTree($admin_id, $target_user_id, $new_parent_id, $target_position, $reason, $pdo);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action requested.']);
        break;
}

