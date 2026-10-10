<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once 'common/connection.php';
require_once 'common/db_method.php';

// Strictly authenticate using $_SESSION['userid']
if (!isset($_SESSION['userid'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. User session required.'
    ]);
    exit;
}

$user_id = $_SESSION['userid'];

$action = $_POST['action'] ?? $_GET['action'] ?? 'get_wallet_summary';

switch ($action) {
    case 'get_wallet_summary':
        // Fetch User record
        $stmtUser = $pdo->prepare("SELECT * FROM user WHERE userid = :uid");
        $stmtUser->execute([':uid' => $user_id]);
        $uRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$uRow) {
            echo json_encode(['status' => 'error', 'message' => 'User profile record not found.']);
            exit;
        }

        // 1. Wallets Balances (DB Source)
        $mainWalletBalance     = round((float)($uRow['amount'] ?? 0.00), 2);
        $profitIncomeWallet    = round((float)($uRow['profit_income_wallet'] ?? 0.00), 2);
        $profitSharingWallet   = round((float)($uRow['profit_sharing_wallet'] ?? 0.00), 2);
        $directBonusWallet     = round((float)($uRow['direct_bonus_wallet'] ?? 0.00), 2);
        $mentorIncomeWallet    = round((float)($uRow['mentor_income_wallet'] ?? 0.00), 2);
        $vipClubWallet         = round((float)($uRow['vip_club_wallet'] ?? 0.00), 2);
        $depositWallet         = round((float)($uRow['deposite_wallet'] ?? 0.00), 2);

        // 2. Cumulative Incomes Received (DB Aggregations)
        $totProfitIncome = (float)$pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_roiinc WHERE user_id = :uid")->execute([':uid' => $user_id]) ? (float)$pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_roiinc WHERE user_id = :uid")->fetchColumn() : 0.00;

        $stmtPs = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_daily_levelinc WHERE user_id = :uid");
        $stmtPs->execute([':uid' => $user_id]);
        $totProfitSharing = (float)$stmtPs->fetchColumn();

        $stmtDb = $pdo->prepare("SELECT COALESCE(SUM(installment_amount), 0) FROM tbl_direct_bonus_schedule WHERE beneficiary_id = :uid AND status = 'CREDITED'");
        $stmtDb->execute([':uid' => $user_id]);
        $totDirectBonus = (float)$stmtDb->fetchColumn();
        $totDirectBonus = max($totDirectBonus, $directBonusWallet);

        $stmtMi = $pdo->prepare("SELECT COALESCE(SUM(payout_amount), 0) FROM tbl_mentor_income_schedule WHERE direct_user_id = :uid AND status = 'CREDITED'");
        $stmtMi->execute([':uid' => $user_id]);
        $totMentorIncome = (float)$stmtMi->fetchColumn();
        $totMentorIncome = max($totMentorIncome, $mentorIncomeWallet);

        $stmtVipRewards = $pdo->prepare("SELECT COALESCE(SUM(reward_amount), 0) FROM tbl_vip_user_qualification WHERE user_id = :uid AND reward_status = 'CREDITED'");
        $stmtVipRewards->execute([':uid' => $user_id]);
        $totVipRewards = (float)$stmtVipRewards->fetchColumn();

        $stmtVipMonthly = $pdo->prepare("SELECT COALESCE(SUM(total_payout), 0), COALESCE(SUM(turnover_payout), 0) FROM tbl_vip_monthly_schedule WHERE user_id = :uid AND status = 'CREDITED'");
        $stmtVipMonthly->execute([':uid' => $user_id]);
        $vipMonthlyRow = $stmtVipMonthly->fetch(PDO::FETCH_NUM);
        $totVipMonthly = (float)($vipMonthlyRow[0] ?? 0.00);
        $totTurnover   = (float)($vipMonthlyRow[1] ?? 0.00);

        $totRankReward = (float)$pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_rewardinc WHERE user_id = :uid")->execute([':uid' => $user_id]) ? (float)$pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_rewardinc WHERE user_id = :uid")->fetchColumn() : 0.00;

        $totVipClubTotal = round($totVipRewards + $totVipMonthly, 2);
        $totVipClubTotal = max($totVipClubTotal, $vipClubWallet);

        // Combined User Growth Total (All 7 Incomes: Profit Income, Profit Sharing, Direct Bonus, Mentor Income, VIP Club, Rank Reward, Company Turnover)
        $userGrowthTotal = round(
            $totProfitIncome +
            $totProfitSharing +
            $totDirectBonus +
            $totMentorIncome +
            $totVipClubTotal +
            $totRankReward +
            $totTurnover,
            2
        );

        if (function_exists('getUserIncomeWalletSummary')) {
            $growthSummary = getUserIncomeWalletSummary($user_id, $pdo);
            if (!empty($growthSummary['total_income_balance'])) {
                $userGrowthTotal = max($userGrowthTotal, round((float)$growthSummary['total_income_balance'], 2));
            }
        }
        // Available User Growth Wallet Balance stored in database (Reconciled with live generated income)
        $userGrowthWallet = (float)($uRow['user_growth_wallet'] ?? 0.00);
        if ($userGrowthTotal > $userGrowthWallet) {
            $userGrowthWallet = $userGrowthTotal;
            $pdo->prepare("UPDATE user SET user_growth_wallet = :ugw WHERE userid = :uid")->execute([':ugw' => $userGrowthTotal, ':uid' => $user_id]);
        }

        // 3. Withdrawals Breakdown
        $stmtWdPaid = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE user_id = :uid AND subject LIKE '%Withdraw%' AND type = 'Credit' AND status = 1");
        $stmtWdPaid->execute([':uid' => $user_id]);
        $totWdPaid = (float)$stmtWdPaid->fetchColumn();

        $stmtWdPend = $pdo->prepare("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_transaction WHERE user_id = :uid AND subject LIKE '%Withdraw%' AND status = 0");
        $stmtWdPend->execute([':uid' => $user_id]);
        $totWdPend = (float)$stmtWdPend->fetchColumn();

        // 4. Active Investment Details (Total Active Investment across all active packages)
        $stmtInv = $pdo->prepare("
            SELECT id, package, real_fund_usd, bonus_amount_usd, date, count, lock_day, status
            FROM tbl_roi_one
            WHERE user_id = :uid 
              AND status = '0'
              AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
            ORDER BY id DESC
        ");
        $stmtInv->execute([':uid' => $user_id]);
        $activeInvs = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

        $invDetails = null;
        if (!empty($activeInvs)) {
            $totalPkgInr = 0.00;
            $totalReturnBaseUsd = 0.00;
            $totalRealFundUsd = 0.00;
            $totalBonusFundUsd = 0.00;
            foreach ($activeInvs as $invRow) {
                $pInr = (float)$invRow['package'];
                $rUsd = (float)($invRow['real_fund_usd'] ?? 0);
                $bUsd = (float)($invRow['bonus_amount_usd'] ?? 0);
                if ($rUsd <= 0 && $pInr > 0) {
                    $rUsd = round($pInr / 90.0, 2);
                }
                $retBaseUsd = round($rUsd + $bUsd, 2);
                if ($pInr <= 0 && $retBaseUsd > 0) {
                    $pInr = round($retBaseUsd * 90.0, 2);
                }
                $totalPkgInr += $pInr;
                $totalReturnBaseUsd += $retBaseUsd;
                $totalRealFundUsd += $rUsd;
                $totalBonusFundUsd += $bUsd;
            }
            $latestInv = $activeInvs[0];
            $invDetails = [
                'investment_id'   => $latestInv['id'],
                'package_inr'     => $totalPkgInr,
                'package_usd'     => $totalReturnBaseUsd,
                'real_fund_usd'   => $totalRealFundUsd,
                'bonus_fund_usd'  => $totalBonusFundUsd,
                'return_base_usd' => $totalReturnBaseUsd,
                'activation_date' => $latestInv['date'],
                'lock_day'        => (int)$latestInv['lock_day'],
                'months_paid'     => (int)$latestInv['count'],
                'status'          => ($uRow['active'] == '1') ? 'ACTIVE' : 'INACTIVE',
                'active_count'    => count($activeInvs)
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'user_id' => $user_id,
                'user_name' => $uRow['name'],
                'wallets' => [
                    'main_wallet'                   => $mainWalletBalance,
                    'user_growth_total'             => $userGrowthTotal,
                    'user_growth_wallet'            => $userGrowthWallet,
                    'lifetime_generated_income'     => $userGrowthTotal,
                    'available_user_growth_balance' => $userGrowthWallet,
                    'profit_income_wallet'  => $profitIncomeWallet,
                    'profit_sharing_wallet' => $profitSharingWallet,
                    'direct_bonus_wallet'   => $directBonusWallet,
                    'mentor_income_wallet'  => $mentorIncomeWallet,
                    'vip_club_wallet'       => $vipClubWallet,
                    'deposit_wallet'        => $depositWallet,
                    'bonus_30_wallet'       => round((float)($uRow['bonus_30_wallet'] ?? 0.00), 2)
                ],
                'cumulative_income' => [
                    'profit_income_received'      => round($totProfitIncome, 2),
                    'profit_sharing_received'     => round($totProfitSharing, 2),
                    'direct_bonus_received'       => round($totDirectBonus, 2),
                    'mentor_income_received'      => round($totMentorIncome, 2),
                    'vip_club_rewards_received'   => round($totVipRewards, 2),
                    'vip_club_monthly_received'   => round($totVipMonthly, 2),
                    'vip_club_total_received'     => $totVipClubTotal,
                    'company_turnover_received'   => round($totTurnover, 2),
                    'rank_reward_received'        => round($totRankReward, 2)
                ],
                'withdrawals' => [
                    'total_paid'    => round($totWdPaid, 2),
                    'total_pending' => round($totWdPend, 2)
                ],
                'active_investment' => $invDetails
            ]
        ]);
        break;

    case 'get_income_history':
        $category = trim($_GET['category'] ?? $_POST['category'] ?? 'ALL');
        $limit    = (int)($_GET['limit'] ?? 200);

        $query = "SELECT id, user_id, subject, type, amount, created_date, status FROM tbl_transaction WHERE user_id = :uid";
        $params = [':uid' => $user_id];

        if ($category === 'PROFIT_INCOME') {
            $query .= " AND subject LIKE '%Profit Income%'";
        } elseif ($category === 'PROFIT_SHARING') {
            $query .= " AND (subject LIKE '%Profit Sharing%' OR subject LIKE '%Level Income%')";
        } elseif ($category === 'DIRECT_BONUS') {
            $query .= " AND subject LIKE '%Direct Bonus%'";
        } elseif ($category === 'MENTOR_INCOME') {
            $query .= " AND subject LIKE '%Mentor Income%'";
        } elseif ($category === 'VIP_CLUB') {
            $query .= " AND subject LIKE '%VIP Club%'";
        } elseif ($category === 'WITHDRAWAL') {
            $query .= " AND subject LIKE '%Withdraw%'";
        }

        $query .= " ORDER BY id DESC LIMIT " . $limit;

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $txns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $txns]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
}
