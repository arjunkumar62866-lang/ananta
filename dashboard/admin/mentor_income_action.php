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
    case 'get_mentor_directs':
        $mentor_id = trim($_GET['mentor_id'] ?? $_POST['mentor_id'] ?? '');

        if (empty($mentor_id)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mentor ID is required.'
            ]);
            exit;
        }

        $directs = getMentorDirectContributions($mentor_id, $pdo);
        $validation = validateMentorContributions($mentor_id, $pdo);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'mentor_id' => $mentor_id,
                'directs' => $directs,
                'validation' => $validation
            ]
        ]);
        break;

    case 'save_contribution':
        $mentor_id = trim($_POST['mentor_id'] ?? '');
        $direct_user_id = trim($_POST['direct_user_id'] ?? '');
        $contribution_percentage = filter_var($_POST['contribution_percentage'] ?? null, FILTER_VALIDATE_FLOAT);

        if (empty($mentor_id) || empty($direct_user_id) || $contribution_percentage === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mentor ID, Direct User ID, and a valid contribution percentage are required.'
            ]);
            exit;
        }

        $res = saveMentorDirectContribution($mentor_id, $direct_user_id, $contribution_percentage, $pdo);
        $val = validateMentorContributions($mentor_id, $pdo);
        $res['validation'] = $val;
        echo json_encode($res);
        break;

    case 'save_bulk_contributions':
        $mentor_id = trim($_POST['mentor_id'] ?? '');
        $contributions = $_POST['contributions'] ?? []; // Array of direct_user_id => percentage

        if (empty($mentor_id) || !is_array($contributions)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mentor ID and a valid list of contributions are required.'
            ]);
            exit;
        }

        try {
            $pdo->beginTransaction();
            foreach ($contributions as $direct_user_id => $pct) {
                $pctVal = (float)$pct;
                saveMentorDirectContribution($mentor_id, $direct_user_id, $pctVal, $pdo);
            }
            $pdo->commit();

            $val = validateMentorContributions($mentor_id, $pdo);
            echo json_encode([
                'status' => 'success',
                'message' => 'Contributions saved successfully.',
                'validation' => $val
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Save failed: ' . $e->getMessage()]);
        }
        break;

    case 'get_schedules':
        $mentor_id = trim($_GET['mentor_id'] ?? $_POST['mentor_id'] ?? '');
        $closing_month = trim($_GET['closing_month'] ?? $_POST['closing_month'] ?? '');
        $limit = (int)($_GET['limit'] ?? 500);

        $query = "
            SELECT 
                s.id,
                s.mentor_id,
                u1.name as mentor_name,
                s.direct_user_id,
                u2.name as direct_user_name,
                u2.mentor_income_wallet as direct_user_wallet,
                s.closing_month,
                s.mentor_monthly_income,
                s.mentor_income_rate,
                s.total_mentor_income,
                s.contribution_percentage,
                s.payout_amount,
                s.status,
                s.credited_at
            FROM tbl_mentor_income_schedule s
            LEFT JOIN user u1 ON u1.userid = s.mentor_id
            LEFT JOIN user u2 ON u2.userid = s.direct_user_id
            WHERE 1=1
        ";

        $params = [];
        if (!empty($mentor_id)) {
            $query .= " AND s.mentor_id = :mentor_id";
            $params[':mentor_id'] = $mentor_id;
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

        $res = processAdminMentorIncomeAdjustment($admin_id, $user_id, $adjustment_type, $amount, $reason, $reference, $pdo);
        echo json_encode($res);
        break;

    case 'get_audit_history':
        $user_id = trim($_GET['user_id'] ?? $_POST['user_id'] ?? '');
        $limit = (int)($_GET['limit'] ?? 100);
        if ($limit <= 0 || $limit > 1000) {
            $limit = 100;
        }

        $query = "SELECT a.*, u.auserid as admin_name, target.name as target_username 
                  FROM tbl_mentor_income_admin_audit a
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

    case 'get_report_stats':
        $stats = getMentorIncomeReportStats($pdo);
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

