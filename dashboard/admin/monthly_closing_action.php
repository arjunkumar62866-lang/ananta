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
        SELECT r.id, r.user_id, r.package, r.package_code, r.real_fund_usd, r.bonus_amount_usd, r.date, r.count, r.lock_day
        FROM tbl_roi_one r
        JOIN user u ON u.userid = r.user_id
        WHERE r.status = '0' AND r.count < r.lock_day AND u.active = '1' AND (r.package >= 13050 OR r.real_fund_usd >= 145.00)
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
        $realUsd = (float)($inv['real_fund_usd'] ?? 0);
        $bonusUsd = (float)($inv['bonus_amount_usd'] ?? 0);
        if ($realUsd <= 0 && (float)$inv['package'] > 0) {
            $realUsd = round(((float)$inv['package']) / 90.0, 2);
        }
        // Return calculation base (Real Fund + Bonus Fund, e.g. $145 + $43.50 = $188.50)
        $returnBaseUsd = round($realUsd + $bonusUsd, 2);
        $returnBaseInr = round($returnBaseUsd * 90.0, 2);

        $total_investment += $returnBaseInr;
        $user_set[$inv['user_id']] = true;

        $count = (int)$inv['count'];
        $inv_date = $inv['date'];

        if ($count == 0 && !empty($inv_date)) {
            // First Month Pro-rata calculation on combined return base
            $inv_day = (int)date('d', strtotime($inv_date));
            $eligible_days = max(1, $days_in_month - $inv_day + 1);
            $full_monthly_profit = ($returnBaseInr * $profit_percentage) / 100;
            $gen_profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
        } else {
            // Normal full monthly profit on combined return base
            $gen_profit = round(($returnBaseInr * $profit_percentage) / 100, 2);
        }
        $expected_profit += $gen_profit;
    }
    $user_count = count($user_set);

    // Calculate estimated Profit Sharing without database mutations
    $total_profit_sharing = 0.0;
    if ($total_investment > 0 && function_exists('getmysponserid')) {
        foreach ($allInvs as $inv) {
            $realUsd = (float)($inv['real_fund_usd'] ?? 0);
            $bonusUsd = (float)($inv['bonus_amount_usd'] ?? 0);
            if ($realUsd <= 0 && (float)$inv['package'] > 0) {
                $realUsd = round(((float)$inv['package']) / 90.0, 2);
            }
            $returnBaseUsd = round($realUsd + $bonusUsd, 2);
            $returnBaseInr = round($returnBaseUsd * 90.0, 2);

            $count = (int)$inv['count'];
            $inv_date = $inv['date'];

            if ($count == 0 && !empty($inv_date)) {
                $inv_day = (int)date('d', strtotime($inv_date));
                $eligible_days = max(1, $days_in_month - $inv_day + 1);
                $full_monthly_profit = ($returnBaseInr * $profit_percentage) / 100;
                $gen_profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
            } else {
                $gen_profit = round(($returnBaseInr * $profit_percentage) / 100, 2);
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

    $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
    if ($rate <= 0) $rate = 90.0;

    // Calculate Mentor Income Preview Metrics
    // Base: 2% of Mentor's Expected Profit Income
    $mentor_expected_payout = 0.0;
    $mentor_count_total = 0;
    $mentor_valid_count = 0;
    $mentor_blocked_count = 0;
    $mentor_blocked_details = [];
    $mentor_recipient_count = 0;

    foreach ($user_set as $uId => $dummy) {
        $uProfitInr = 0.0;
        foreach ($allInvs as $inv) {
            if ($inv['user_id'] === $uId) {
                $count = (int)$inv['count'];
                $inv_date = $inv['date'];
                $rUsd = (float)($inv['real_fund_usd'] ?? 0);
                $bUsd = (float)($inv['bonus_amount_usd'] ?? 0);
                if ($rUsd <= 0 && (float)$inv['package'] > 0) {
                    $rUsd = round(((float)$inv['package']) / 90.0, 2);
                }
                $retBaseInr = round(($rUsd + $bUsd) * 90.0, 2);
                if ($count == 0 && !empty($inv_date)) {
                    $inv_day = (int)date('d', strtotime($inv_date));
                    $el_days = max(1, $days_in_month - $inv_day + 1);
                    $full_profit = ($retBaseInr * $profit_percentage) / 100;
                    $uProfitInr += round(($full_profit * $el_days) / $days_in_month, 2);
                } else {
                    $uProfitInr += round(($retBaseInr * $profit_percentage) / 100, 2);
                }
            }
        }

        if ($uProfitInr <= 0) continue;

        $directs = function_exists('getMentorDirectContributions') ? getMentorDirectContributions($uId, $pdo) : [];
        if (empty($directs)) continue; // Not a mentor with direct referrals

        $mentor_count_total++;

        // Income eligibility of mentor
        if (function_exists('isUserIncomeEligible') && !isUserIncomeEligible($uId, $pdo)) {
            $mentor_blocked_count++;
            $mentor_blocked_details[] = "Mentor {$uId}: Does not meet income eligibility (active ID with minimum $145 investment).";
            continue;
        }

        // Option A: 100% Contribution Validation
        $val = function_exists('validateMentorContributions') ? validateMentorContributions($uId, $pdo) : ['valid' => false, 'reason' => 'Validator missing'];
        if (!$val['valid']) {
            $mentor_blocked_count++;
            $mentor_blocked_details[] = "Mentor {$uId}: " . ($val['reason'] ?? 'Contribution does not equal 100.00%');
            continue;
        }

        $mentor_valid_count++;
        $totMentorIncInr = round($uProfitInr * 0.02, 2);

        foreach ($directs as $d) {
            $dId = $d['direct_user_id'];
            $pct = (float)$d['contribution_percentage'];
            if ($pct <= 0) continue;

            $dPayout = round(($totMentorIncInr * $pct) / 100.0, 2);
            if ($dPayout <= 0) continue;

            if (function_exists('isUserIncomeEligible') && !isUserIncomeEligible($dId, $pdo)) {
                continue;
            }

            $mentor_expected_payout += $dPayout;
            $mentor_recipient_count++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'closing_month' => $closing_month,
        'profit_percentage' => $profit_percentage,
        'total_eligible_investment_inr' => round($total_investment, 2),
        'total_eligible_investment_usd' => round($total_investment / $rate, 2),
        'total_eligible_investment' => round($total_investment / $rate, 2),
        'expected_total_profit_inr' => round($expected_profit, 2),
        'expected_total_profit_usd' => round($expected_profit / $rate, 2),
        'expected_total_profit' => round($expected_profit / $rate, 2),
        'expected_profit_sharing_inr' => round($total_profit_sharing, 2),
        'expected_profit_sharing_usd' => round($total_profit_sharing / $rate, 2),
        'expected_profit_sharing' => round($total_profit_sharing / $rate, 2),
        'eligible_investment_count' => $investment_count,
        'eligible_user_count' => $user_count,
        // Mentor Income Preview (Option A Strict Validation)
        'mentor_income_rate' => 2.0,
        'mentor_eligible_count' => $mentor_count_total,
        'mentor_valid_config_count' => $mentor_valid_count,
        'mentor_blocked_count' => $mentor_blocked_count,
        'mentor_recipient_count' => $mentor_recipient_count,
        'expected_mentor_income_inr' => round($mentor_expected_payout, 2),
        'expected_mentor_income_usd' => round($mentor_expected_payout / $rate, 2),
        'expected_mentor_income' => round($mentor_expected_payout / $rate, 2),
        'mentor_blocked_reasons' => $mentor_blocked_details
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

        // Fetch all active eligible investments with dual condition (User active = '1' AND (package >= 13050 OR real_fund_usd >= 145))
        $invStmt = $pdo->prepare("
            SELECT r.id, r.user_id, r.package, r.package_code, r.real_fund_usd, r.bonus_amount_usd, r.date, r.count, r.lock_day 
            FROM tbl_roi_one r
            JOIN user u ON u.userid = r.user_id
            WHERE r.status = '0' AND r.count < r.lock_day AND u.active = '1' AND (r.package >= 13050 OR r.real_fund_usd >= 145.00) 
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
            $realUsd = (float)($inv['real_fund_usd'] ?? 0);
            $bonusUsd = (float)($inv['bonus_amount_usd'] ?? 0);
            if ($realUsd <= 0 && (float)$inv['package'] > 0) {
                $realUsd = round(((float)$inv['package']) / 90.0, 2);
            }
            // Return calculation base (Real Fund + Bonus Fund, e.g. $145 + $43.50 = $188.50)
            $returnBaseUsd = round($realUsd + $bonusUsd, 2);
            $returnBaseInr = round($returnBaseUsd * 90.0, 2);

            $count = (int)$inv['count'];
            $inv_date = $inv['date'];

            if ($count == 0 && !empty($inv_date)) {
                $inv_day = (int)date('d', strtotime($inv_date));
                $eligible_days = max(1, $days_in_month - $inv_day + 1);
                $full_monthly_profit = ($returnBaseInr * $profit_percentage) / 100;
                $profit = round(($full_monthly_profit * $eligible_days) / $days_in_month, 2);
                $inv['_is_prorata'] = true;
                $inv['_eligible_days'] = $eligible_days;
            } else {
                $profit = round(($returnBaseInr * $profit_percentage) / 100, 2);
                $inv['_is_prorata'] = false;
            }

            $inv['_return_base_usd'] = $returnBaseUsd;
            $inv['_return_base_inr'] = $returnBaseInr;
            $inv['_calculated_profit'] = $profit;
            $total_investment += $returnBaseInr;
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
        $updUser = $pdo->prepare("UPDATE user SET amount = amount + :profit, profit_income_wallet = profit_income_wallet + :profit, user_growth_wallet = user_growth_wallet + :profit WHERE userid = :user_id");
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

            $baseUsdStr = "$" . number_format($inv['_return_base_usd'], 2);
            if ($inv['_is_prorata']) {
                $subject = "Monthly Profit Income ({$closing_month} @ {$profit_percentage}% - Base: {$baseUsdStr} - Pro-rata {$inv['_eligible_days']} days)";
            } else {
                $subject = "Monthly Profit Income ({$closing_month} @ {$profit_percentage}% - Base: {$baseUsdStr})";
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

        // Payout schedules execution with actual closing_id passed
        $directBonusResult  = processDirectBonusInstallments($closing_month, $closing_date, $pdo);
        $mentorIncomeResult = processMentorIncome($closing_month, $closing_date, $pdo, $closing_id);
        $vipClubResult      = processVIPMonthlyIncome($closing_month, $closing_date, $pdo);

        $updClosing = $pdo->prepare("UPDATE tbl_monthly_closing SET status = 'COMPLETED' WHERE id = :id");
        $updClosing->execute([':id' => $closing_id]);

        $pdo->commit();

        // Build truthful, comprehensive message regarding all 5 income streams
        $incomeSummaries = [];
        $incomeSummaries[] = "Profit Income credited (₹" . number_format($total_profit_paid, 2) . ")";
        $incomeSummaries[] = "Profit Sharing distributed";

        if (($directBonusResult['processed'] ?? 0) > 0) {
            $incomeSummaries[] = "Direct Bonus credited (₹" . number_format($directBonusResult['total_paid'] ?? 0, 2) . ")";
        }

        if (($mentorIncomeResult['processed'] ?? 0) > 0) {
            $incomeSummaries[] = "Mentor Income credited (₹" . number_format($mentorIncomeResult['total_paid'] ?? 0, 2) . ")";
        } elseif (($mentorIncomeResult['blocked_mentors'] ?? 0) > 0) {
            $incomeSummaries[] = "Mentor Income: {$mentorIncomeResult['blocked_mentors']} mentor(s) blocked (0.00 credited under Option A strict 100% rule)";
        } else {
            $incomeSummaries[] = "Mentor Income: ₹0.00 (No eligible mentors)";
        }

        if (($vipClubResult['processed'] ?? 0) > 0) {
            $incomeSummaries[] = "VIP Club Income credited (₹" . number_format($vipClubResult['total_paid'] ?? 0, 2) . ")";
        }

        $summaryMsg = implode(', ', $incomeSummaries) . '.';
        $respMsg = "Monthly Closing for {$closing_month} completed! " . $summaryMsg;

        echo json_encode([
            'status' => 'success',
            'message' => $respMsg,
            'details' => [
                'closing_id' => $closing_id,
                'closing_month' => $closing_month,
                'closing_date' => $closing_date,
                'profit_percentage' => $profit_percentage,
                'total_eligible_investment' => $total_investment,
                'total_profit_paid' => $total_profit_paid,
                'eligible_investment_count' => $investment_count,
                'eligible_user_count' => $user_count,
                'direct_bonus_paid' => $directBonusResult['total_paid'] ?? 0.0,
                'direct_bonus_count' => $directBonusResult['processed'] ?? 0,
                'mentor_income_paid' => $mentorIncomeResult['total_paid'] ?? 0.0,
                'mentor_income_count' => $mentorIncomeResult['processed'] ?? 0,
                'mentor_mentors_processed' => $mentorIncomeResult['mentors_processed'] ?? 0,
                'mentor_blocked_count' => $mentorIncomeResult['blocked_mentors'] ?? 0,
                'mentor_blocked_reasons' => $mentorIncomeResult['blocked_reasons'] ?? [],
                'vip_club_paid' => $vipClubResult['total_paid'] ?? 0.0,
                'vip_club_count' => $vipClubResult['processed'] ?? 0
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

    // Fetch actual Mentor Income credited and count from schedule
    $miStmt = $pdo->prepare("SELECT COALESCE(SUM(payout_amount), 0) FROM tbl_mentor_income_schedule WHERE closing_month = :month AND status = 'CREDITED'");
    $miStmt->execute([':month' => $closing['closing_month']]);
    $mentor_income_paid = (float)$miStmt->fetchColumn();

    $miCntStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_mentor_income_schedule WHERE closing_month = :month AND status = 'CREDITED'");
    $miCntStmt->execute([':month' => $closing['closing_month']]);
    $mentor_payout_count = (int)$miCntStmt->fetchColumn();

    // Fetch Direct Bonus paid for this month
    $dbStmt = $pdo->prepare("SELECT COALESCE(SUM(installment_amount), 0) FROM tbl_direct_bonus_schedule WHERE closing_month = :month AND status = 'CREDITED'");
    $dbStmt->execute([':month' => $closing['closing_month']]);
    $direct_bonus_paid = (float)$dbStmt->fetchColumn();

    // Fetch VIP Club paid for this month
    $vipStmt = $pdo->prepare("SELECT COALESCE(SUM(total_payout), 0) FROM tbl_vip_monthly_schedule WHERE closing_month = :month AND status = 'CREDITED'");
    $vipStmt->execute([':month' => $closing['closing_month']]);
    $vip_club_paid = (float)$vipStmt->fetchColumn();

    $rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
    if ($rate <= 0) $rate = 90.0;

    $closing['total_eligible_investment_inr'] = (float)$closing['total_eligible_investment'];
    $closing['total_eligible_investment_usd'] = round((float)$closing['total_eligible_investment'] / $rate, 2);
    $closing['total_eligible_investment']     = round((float)$closing['total_eligible_investment'] / $rate, 2);

    $closing['total_profit_paid_inr']         = (float)$closing['total_profit_paid'];
    $closing['total_profit_paid_usd']         = round((float)$closing['total_profit_paid'] / $rate, 2);
    $closing['total_profit_paid']             = round((float)$closing['total_profit_paid'] / $rate, 2);

    $total_sharing_usd                        = round($total_sharing / $rate, 2);

    echo json_encode([
        'status' => 'success',
        'closing' => $closing,
        'total_profit_sharing_inr' => round($total_sharing, 2),
        'total_profit_sharing_usd' => $total_sharing_usd,
        'total_profit_sharing'     => $total_sharing_usd,
        'direct_bonus_paid_inr'    => round($direct_bonus_paid, 2),
        'direct_bonus_paid_usd'    => round($direct_bonus_paid / $rate, 2),
        'mentor_income_paid_inr'   => round($mentor_income_paid, 2),
        'mentor_income_paid_usd'   => round($mentor_income_paid / $rate, 2),
        'mentor_payout_count'      => $mentor_payout_count,
        'vip_club_paid_inr'        => round($vip_club_paid, 2),
        'vip_club_paid_usd'        => round($vip_club_paid / $rate, 2)
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
exit;
