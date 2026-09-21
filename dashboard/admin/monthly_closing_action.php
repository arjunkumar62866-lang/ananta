<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'common/connection.php';
require_once 'common/db_method.php';

if (!headers_sent()) {
    header('Content-Type: application/json');
}

if (!isset($_SESSION["auserid"])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'preview') {
    $closing_month = trim($_GET['closing_month'] ?? '');
    $profit_percentage = filter_var($_GET['profit_percentage'] ?? null, FILTER_VALIDATE_FLOAT);

    if (!$closing_month || !preg_match('/^\d{4}-\d{2}$/', $closing_month)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing closing month format (YYYY-MM).']);
        exit;
    }

    if ($profit_percentage === false || $profit_percentage <= 0 || $profit_percentage > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Profit percentage must be a valid number greater than 0 and up to 100.']);
        exit;
    }

    // Check if month is already closed
    $chkStmt = $pdo->prepare("SELECT id, closing_date, profit_percentage, total_profit_paid, status, created_at FROM tbl_monthly_closing WHERE closing_month = :month");
    $chkStmt->execute([':month' => $closing_month]);
    $alreadyClosed = $chkStmt->fetch(PDO::FETCH_ASSOC);

    if ($alreadyClosed) {
        echo json_encode([
            'status' => 'already_closed',
            'message' => "Closing for {$closing_month} has ALREADY been processed on {$alreadyClosed['created_at']} at {$alreadyClosed['profit_percentage']}%.",
            'already_closed_data' => $alreadyClosed
        ]);
        exit;
    }

    // Read-only calculation of eligible investments: User must have active unlock access (active = '1') AND package >= 13050 ($145)
    $stmt = $pdo->prepare("
        SELECT r.id, r.user_id, r.package, r.date, r.count, r.lock_day
        FROM tbl_roi_one r
        JOIN user u ON u.userid = r.user_id
        WHERE r.status = '0' AND r.count < r.lock_day AND u.active = '1' AND r.package >= 13050
    ");
    $stmt->execute();
    $allInvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_investment = 0.0;
    $expected_profit = 0.0;
    $investment_count = count($allInvs);
    $user_set = [];

    // Days in current closing month (e.g. 30 for Sep)
    $closing_ts = strtotime($closing_month . '-01');
    $days_in_month = (int)date('t', $closing_ts);

    foreach ($allInvs as $inv) {
        $pkg = (float)$inv['package'];
        $total_investment += $pkg;
        $user_set[$inv['user_id']] = true;

        $count = (int)$inv['count'];
        $inv_date = $inv['date'];

        if ($count == 0 && !empty($inv_date)) {
            // First Month Pro-rata calculation
            $inv_day = (int)date('d', strtotime($inv_date));
            $eligible_days = max(1, $days_in_month - $inv_day + 1);
            $full_monthly_profit = ($pkg * $profit_percentage) / 100;
            $gen_profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
        } else {
            // Normal full monthly profit
            $gen_profit = round(($pkg * $profit_percentage) / 100, 2);
        }
        $expected_profit += $gen_profit;
    }
    $user_count = count($user_set);

    // Calculate estimated Profit Sharing without database mutations
    $total_profit_sharing = 0.0;
    if ($total_investment > 0 && function_exists('getmysponserid')) {
        foreach ($allInvs as $inv) {
            $pkg = (float)$inv['package'];
            $count = (int)$inv['count'];
            $inv_date = $inv['date'];

            if ($count == 0 && !empty($inv_date)) {
                $inv_day = (int)date('d', strtotime($inv_date));
                $eligible_days = max(1, $days_in_month - $inv_day + 1);
                $full_monthly_profit = ($pkg * $profit_percentage) / 100;
                $gen_profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
            } else {
                $gen_profit = round(($pkg * $profit_percentage) / 100, 2);
            }

            $pinfinal = $inv['user_id'];
            $price = $gen_profit;

            for ($i = 0; $i < 15; $i++) {
                $mysponserid = getmysponserid($pinfinal);
                if (empty($mysponserid)) break;
                $sponserdetails = getuserdatabysponserid($mysponserid);
                if ($pinfinal !== '1290') {
                    $spcode1 = $sponserdetails['userid'];
                    $isidactive = $sponserdetails['idactive'];
                    $directactive = getmydirectactive($spcode1);

                    if ($isidactive == 1) {
                        $rate = 0.0;
                        if ($i == 0 && $directactive >= 0) $rate = 15.0;
                        else if ($i == 1 && $directactive >= 2) $rate = 7.0;
                        else if ($i == 2 && $directactive >= 3) $rate = 5.0;
                        else if ($i == 3 && $directactive >= 4) $rate = 3.0;
                        else if ($i == 4 && $directactive >= 5) $rate = 2.0;
                        else if ($i == 5 && $directactive >= 6) $rate = 1.0;
                        else if ($i == 6 && $directactive >= 7) $rate = 0.75;
                        else if ($i == 7 && $directactive >= 8) $rate = 0.50;
                        else if ($i >= 8 && $i <= 14 && $directactive >= ($i + 1)) $rate = 0.25;

                        if ($rate > 0) {
                            $sh_amt = round(($price * $rate) / 100.0, 2);
                            $total_profit_sharing += $sh_amt;
                        }
                    }
                    $pinfinal = $spcode1;
                } else {
                    $pinfinal = $spcode1;
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'closing_month' => $closing_month,
        'profit_percentage' => $profit_percentage,
        'total_eligible_investment' => $total_investment,
        'expected_total_profit' => round($expected_profit, 2),
        'expected_profit_sharing' => round($total_profit_sharing, 2),
        'eligible_investment_count' => $investment_count,
        'eligible_user_count' => $user_count
    ]);
    exit;
}

if ($action === 'process') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit;
    }

    $closing_month = trim($_POST['closing_month'] ?? '');
    $profit_percentage = filter_var($_POST['profit_percentage'] ?? null, FILTER_VALIDATE_FLOAT);
    $closing_date = trim($_POST['closing_date'] ?? date('Y-m-d'));

    if (!$closing_month || !preg_match('/^\d{4}-\d{2}$/', $closing_month)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid closing month format (YYYY-MM).']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $closing_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid closing date format (YYYY-MM-DD).']);
        exit;
    }

    if ($profit_percentage === false || $profit_percentage <= 0 || $profit_percentage > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Profit percentage must be a valid number between 0 and 100.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Strict duplicate check with DB row locking to block concurrent requests
        $chkStmt = $pdo->prepare("SELECT id FROM tbl_monthly_closing WHERE closing_month = :month FOR UPDATE");
        $chkStmt->execute([':month' => $closing_month]);
        if ($chkStmt->fetch()) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => "Monthly closing for {$closing_month} has ALREADY been processed. Duplicate processing blocked."]);
            exit;
        }

        // Fetch all active eligible investments with dual condition (User active = '1' AND package >= 13050)
        $invStmt = $pdo->prepare("
            SELECT r.id, r.user_id, r.package, r.date, r.count, r.lock_day 
            FROM tbl_roi_one r
            JOIN user u ON u.userid = r.user_id
            WHERE r.status = '0' AND r.count < r.lock_day AND u.active = '1' AND r.package >= 13050 
            FOR UPDATE
        ");
        $invStmt->execute();
        $investments = $invStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($investments)) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'No active eligible investments found for profit calculation.']);
            exit;
        }

        $total_investment = 0.0;
        $total_profit_paid = 0.0;
        $user_set = [];

        // Days in current closing month
        $closing_ts = strtotime($closing_month . '-01');
        $days_in_month = (int)date('t', $closing_ts);

        foreach ($investments as &$inv) {
            $pkg = (float)$inv['package'];
            $count = (int)$inv['count'];
            $inv_date = $inv['date'];

            if ($count == 0 && !empty($inv_date)) {
                $inv_day = (int)date('d', strtotime($inv_date));
                $eligible_days = max(1, $days_in_month - $inv_day + 1);
                $full_monthly_profit = ($pkg * $profit_percentage) / 100;
                $profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
                $inv['_is_prorata'] = true;
                $inv['_eligible_days'] = $eligible_days;
            } else {
                $profit = round(($pkg * $profit_percentage) / 100, 2);
                $inv['_is_prorata'] = false;
            }

            $inv['_calculated_profit'] = $profit;
            $total_investment += $pkg;
            $total_profit_paid += $profit;
            $user_set[$inv['user_id']] = true;
        }
        unset($inv);

        $investment_count = count($investments);
        $user_count = count($user_set);

        // Record monthly closing log with status = 'PROCESSING'
        $closeLogStmt = $pdo->prepare("
            INSERT INTO tbl_monthly_closing 
                (closing_month, closing_date, profit_percentage, total_eligible_investment, total_profit_paid, eligible_investment_count, eligible_user_count, status, processed_by)
            VALUES 
                (:closing_month, :closing_date, :profit_percentage, :total_eligible_investment, :total_profit_paid, :eligible_investment_count, :eligible_user_count, 'PROCESSING', :processed_by)
        ");
        $closeLogStmt->execute([
            ':closing_month' => $closing_month,
            ':closing_date' => $closing_date,
            ':profit_percentage' => $profit_percentage,
            ':total_eligible_investment' => $total_investment,
            ':total_profit_paid' => round($total_profit_paid, 2),
            ':eligible_investment_count' => $investment_count,
            ':eligible_user_count' => $user_count,
            ':processed_by' => $_SESSION['auserid']
        ]);

        $closing_id = $pdo->lastInsertId();

        // Prepared statements for user credit, investment update & transaction logging
        $updUser = $pdo->prepare("UPDATE user SET profit_income_wallet = profit_income_wallet + :profit WHERE userid = :user_id");
        $updInv = $pdo->prepare("UPDATE tbl_roi_one SET count = count + 1, totalincome = totalincome + :profit, amount = :profit, closingdate = :closing_date, status = :status WHERE id = :id");
        $insTxn = $pdo->prepare("
            INSERT INTO tbl_transaction 
                (user_id, franchiseeid, type, subject, time, created_date, status, amount, sales_bonus, sales_incentive, travel_fund, final_amount, weeklypair, flush_pair, beneficiary_id, app_date, api_txn_no, api_status, api_bank_ref_no, api_message) 
            VALUES 
                (:user_id, '', 'Profit Income', :subject, :time, :closing_date, 1, :profit, '', '', '', '', 0, 0, '', :closing_date, '', '', '', '')
        ");

        $time_now = date('h:i a');

        foreach ($investments as $inv) {
            $profit = $inv['_calculated_profit'];
            $new_count = (int)$inv['count'] + 1;
            $new_status = ($new_count >= (int)$inv['lock_day']) ? 1 : 0;

            if ($inv['_is_prorata']) {
                $subject = "Monthly Profit Income ({$closing_month} @ {$profit_percentage}% - Pro-rata {$inv['_eligible_days']} days)";
            } else {
                $subject = "Monthly Profit Income ({$closing_month} @ {$profit_percentage}%)";
            }

            // 1. Credit dedicated Profit Income Wallet
            $updUser->execute([
                ':profit' => $profit,
                ':user_id' => $inv['user_id']
            ]);

            // 2. Update Investment Record
            $updInv->execute([
                ':profit' => $profit,
                ':closing_date' => $closing_date,
                ':status' => $new_status,
                ':id' => $inv['id']
            ]);

            // 3. Record Profit Income Transaction
            $insTxn->execute([
                ':user_id' => $inv['user_id'],
                ':subject' => $subject,
                ':time' => $time_now,
                ':profit' => $profit,
                ':closing_date' => $closing_date
            ]);

            // 4. TRIGGER PROFIT SHARING ENGINE using generated profit ($profit) as base!
            if (function_exists('pay_roi_one_income')) {
                pay_roi_one_income($inv['user_id'], $profit, $profit_percentage, $inv['id'], $inv['id'], $closing_month);
            }
        }

        // Mark monthly closing as COMPLETED after all payouts & profit sharing calculations succeed
        $directBonusResult = processDirectBonusInstallments($closing_month, $closing_date, $pdo);

        $updClosing = $pdo->prepare("UPDATE tbl_monthly_closing SET status = 'COMPLETED' WHERE id = :id");
        $updClosing->execute([':id' => $closing_id]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => "Monthly Closing for {$closing_month} completed successfully! Profit Income, Profit Sharing & Direct Bonus credited.",
            'details' => [
                'closing_id' => $closing_id,
                'closing_month' => $closing_month,
                'closing_date' => $closing_date,
                'profit_percentage' => $profit_percentage,
                'total_eligible_investment' => $total_investment,
                'total_profit_paid' => $total_profit_paid,
                'eligible_investment_count' => $investment_count,
                'eligible_user_count' => $user_count,
                'direct_bonus_paid' => $directBonusResult['total_paid'],
                'direct_bonus_count' => $directBonusResult['processed']
            ]
        ]);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Monthly closing failed: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'details') {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing closing ID']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM tbl_monthly_closing WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $closing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$closing) {
        echo json_encode(['status' => 'error', 'message' => 'Closing record not found']);
        exit;
    }

    // Fetch total profit sharing generated for this closing month from tbl_daily_levelinc
    $psStmt = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_daily_levelinc WHERE closing_month = :month");
    $psStmt->execute([':month' => $closing['closing_month']]);
    $total_sharing = (float)$psStmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'closing' => $closing,
        'total_profit_sharing' => $total_sharing
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
exit;
