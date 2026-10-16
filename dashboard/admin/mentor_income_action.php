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
    case 'get_rate_config':
        $config = getMentorIncomeConfig($pdo);
        $stmtHistory = $pdo->query("
            SELECT h.*, a.auserid as admin_name
            FROM tbl_mentor_income_rate_history h
            LEFT JOIN admin a ON (CONVERT(h.admin_id USING utf8mb4) = CONVERT(a.auserid USING utf8mb4) OR CONVERT(h.admin_id USING utf8mb4) = CONVERT(a.id USING utf8mb4))
            ORDER BY h.id DESC LIMIT 20
        ");
        $history = $stmtHistory ? $stmtHistory->fetchAll(PDO::FETCH_ASSOC) : [];

        echo json_encode([
            'status' => 'success',
            'config' => $config,
            'history' => $history
        ]);
        break;

    case 'update_rate':
        $admin_id = $_SESSION['auserid'];
        $new_rate = filter_var($_POST['rate_percent'] ?? null, FILTER_VALIDATE_FLOAT);
        $reason = trim($_POST['reason'] ?? '');
        $effective_from_month = trim($_POST['effective_from_month'] ?? date('Y-m'));
        $is_auto_closing = isset($_POST['is_auto_closing']) ? (int)$_POST['is_auto_closing'] : null;

        if ($new_rate === false || $new_rate < 0 || $new_rate > 100) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Valid rate percentage between 0% and 100% is required.'
            ]);
            exit;
        }

        $res = updateMentorIncomeRate($new_rate, $admin_id, $reason, $effective_from_month, $is_auto_closing, $pdo);
        echo json_encode($res);
        break;

    case 'preview_closing':
        $closing_month = trim($_POST['closing_month'] ?? $_GET['closing_month'] ?? date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $closing_month)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid closing month format. Expected YYYY-MM.'
            ]);
            exit;
        }

        $preview = previewMentorIncomeMonthlyClosing($closing_month, $pdo);
        echo json_encode($preview);
        break;

    case 'process_closing':
        $closing_month = trim($_POST['closing_month'] ?? '');
        $closing_date  = trim($_POST['closing_date'] ?? date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}$/', $closing_month)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid closing month format. Expected YYYY-MM.'
            ]);
            exit;
        }

        try {
            $pdo->beginTransaction();
            $res = processMentorIncome($closing_month, $closing_date, $pdo, 'MANUAL_ADMIN_' . $closing_month . '_' . time());
            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'message' => "Mentor Income for {$closing_month} processed successfully. Credited: ₹" . number_format($res['total_paid'] ?? 0, 2) . " across {$res['processed']} direct leg(s).",
                'data' => $res
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode([
                'status' => 'error',
                'message' => 'Closing failed: ' . $e->getMessage()
            ]);
        }
        break;

    case 'get_mentor_leg_breakdown':
        $mentor_id = trim($_GET['mentor_id'] ?? $_POST['mentor_id'] ?? '');
        $closing_month = trim($_GET['closing_month'] ?? $_POST['closing_month'] ?? date('Y-m'));

        if (empty($mentor_id)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mentor ID is required.'
            ]);
            exit;
        }

        $data = calculateMentorMonthlyLegBusiness($mentor_id, $closing_month, $pdo);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        break;

    case 'get_schedules':
        $mentor_id = trim($_GET['mentor_id'] ?? $_POST['mentor_id'] ?? '');
        $closing_month = trim($_GET['closing_month'] ?? $_POST['closing_month'] ?? '');
        $status_filter = trim($_GET['status_filter'] ?? $_POST['status_filter'] ?? '');
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
                s.leg_monthly_business,
                s.total_eligible_legs_business,
                s.total_mentor_income,
                s.contribution_percentage,
                s.payout_amount,
                s.status,
                s.closing_id,
                s.calculation_version,
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
        if (!empty($status_filter)) {
            $query .= " AND s.status = :status_filter";
            $params[':status_filter'] = $status_filter;
        }

        $query .= " ORDER BY s.id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
        if ($rate <= 0) $rate = 90.0;

        foreach ($schedules as &$s) {
            $mInc = (float)($s['mentor_monthly_income'] ?? 0);
            $s['mentor_monthly_income_inr'] = round($mInc, 2);
            $s['mentor_monthly_income_usd'] = round($mInc / $rate, 2);
            $s['mentor_monthly_income']     = round($mInc / $rate, 2);

            $legB = (float)($s['leg_monthly_business'] ?? 0);
            $s['leg_monthly_business_inr']  = round($legB, 2);
            $s['leg_monthly_business_usd']  = round($legB / $rate, 2);
            $s['leg_monthly_business']      = round($legB / $rate, 2);

            $totLegB = (float)($s['total_eligible_legs_business'] ?? 0);
            $s['total_eligible_legs_business_inr'] = round($totLegB, 2);
            $s['total_eligible_legs_business_usd'] = round($totLegB / $rate, 2);
            $s['total_eligible_legs_business']     = round($totLegB / $rate, 2);

            $totM = (float)($s['total_mentor_income'] ?? 0);
            $s['total_mentor_income_inr'] = round($totM, 2);
            $s['total_mentor_income_usd'] = round($totM / $rate, 2);
            $s['total_mentor_income']     = round($totM / $rate, 2);

            $payM = (float)($s['payout_amount'] ?? 0);
            $s['payout_amount_inr'] = round($payM, 2);
            $s['payout_amount_usd'] = round($payM / $rate, 2);
            $s['payout_amount']     = round($payM / $rate, 2);

            $wM = (float)($s['direct_user_wallet'] ?? 0);
            $s['direct_user_wallet_inr'] = round($wM, 2);
            $s['direct_user_wallet_usd'] = round($wM / $rate, 2);
            $s['direct_user_wallet']     = round($wM / $rate, 2);
        }
        unset($s);

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

        $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
        if ($rate <= 0) $rate = 90.0;

        foreach ($history as &$h) {
            $amtInr = (float)($h['amount'] ?? 0);
            $h['amount_inr'] = round($amtInr, 2);
            $h['amount_usd'] = round($amtInr / $rate, 2);
            $h['amount']     = round($amtInr / $rate, 2);

            $prevInr = (float)($h['previous_balance'] ?? 0);
            $h['previous_balance_inr'] = round($prevInr, 2);
            $h['previous_balance_usd'] = round($prevInr / $rate, 2);
            $h['previous_balance']     = round($prevInr / $rate, 2);

            $newInr = (float)($h['new_balance'] ?? 0);
            $h['new_balance_inr'] = round($newInr, 2);
            $h['new_balance_usd'] = round($newInr / $rate, 2);
            $h['new_balance']     = round($newInr / $rate, 2);
        }
        unset($h);

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
