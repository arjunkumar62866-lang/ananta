<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include __DIR__ . '/common/header.php';

$type        = strtolower(trim($_GET['type'] ?? 'daily'));
$user_search = trim($_GET['user_search'] ?? '');

$cToday      = date('Y-m-d');
$cMonthStart = date('Y-m-01');
$cYearStart  = date('Y-01-01');

// Default date range: today for daily, start of month for monthly/wallet/investment/withdrawal/income/business/company/user, start of year for yearly
if (isset($_GET['from_date']) && !empty($_GET['from_date'])) {
    $from_date = trim($_GET['from_date']);
} else {
    if ($type === 'daily') {
        $from_date = $cToday;
    } elseif ($type === 'yearly') {
        $from_date = $cYearStart;
    } else {
        $from_date = $cMonthStart;
    }
}

if (isset($_GET['to_date']) && !empty($_GET['to_date'])) {
    $to_date = trim($_GET['to_date']);
} else {
    $to_date = $cToday;
}

$dateError = '';
if ($from_date > $to_date) {
    $dateError = 'From Date cannot be later than To Date. Showing records for selected default period.';
    $from_date = ($type === 'daily') ? $cToday : (($type === 'yearly') ? $cYearStart : $cMonthStart);
    $to_date   = $cToday;
}

// Search parameter string for user query filtering
$uSearchParam = '%' . $user_search . '%';

// Report title & meta mapping
$reportTitles = [
    'daily'      => 'Daily Financial & Business Summary',
    'monthly'    => 'Monthly Financial & Payout Summary',
    'wallet'     => 'Monthly Wallet Transaction Summary',
    'yearly'     => 'Yearly Business & Tax Statement',
    'user'       => 'User-Wise Financial Statement Report',
    'investment' => 'Package Investment & Activation Report',
    'withdrawal' => 'Withdrawal Payout & Settlement Report',
    'income'     => 'Comprehensive Income Distribution Report',
    'business'   => 'Team & System Business Volume Report',
    'company'    => 'Company Revenue & $11 Unlock Access Report'
];

$title = $reportTitles[$type] ?? 'Financial System Report';

$reportData = [];
$totalSum   = 0.0;
$totalCount = 0;

$rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
if ($rate <= 0) $rate = 90.0;

switch ($type) {
    case 'daily':
        // Daily Financial Summary: Authoritative Package Investments, Income distributions, and Settled Withdrawals
        $uInvFilter = "";
        $uIncFilter = "";
        $uWdFilter  = "";
        $params = [
            ':from_date1' => $from_date, ':to_date1' => $to_date,
            ':from_date2' => $from_date, ':to_date2' => $to_date,
            ':from_date3' => $from_date, ':to_date3' => $to_date,
            ':from_date4' => $from_date, ':to_date4' => $to_date,
            ':from_date5' => $from_date, ':to_date5' => $to_date
        ];
        if (!empty($user_search)) {
            $uInvFilter = " AND (r.user_id LIKE :usearch_inv OR u.name LIKE :usearch_inv)";
            $uIncFilter = " AND (t.user_id LIKE :usearch_inc OR u.name LIKE :usearch_inc)";
            $uWdFilter  = " AND (t.user_id LIKE :usearch_wd OR u.name LIKE :usearch_wd)";
            $params[':usearch_inv'] = $uSearchParam;
            $params[':usearch_inc'] = $uSearchParam;
            $params[':usearch_wd']  = $uSearchParam;
        }

        $sql = "SELECT dt.report_date,
                       COALESCE(inv.total_users, 0) as total_users,
                       COALESCE(inv.total_investment, 0) as total_investment,
                       COALESCE(inc.total_income, 0) as total_income,
                       COALESCE(wd.total_withdrawals, 0) as total_withdrawals,
                       COALESCE(inv.total_txns, 0) as total_txns
                FROM (
                    SELECT DATE(r.date) as report_date FROM tbl_roi_one r LEFT JOIN user u ON r.user_id = u.userid WHERE DATE(r.date) BETWEEN :from_date1 AND :to_date1 {$uInvFilter}
                    UNION
                    SELECT DATE(t.created_date) as report_date FROM tbl_transaction t LEFT JOIN user u ON t.user_id = u.userid WHERE DATE(t.created_date) BETWEEN :from_date2 AND :to_date2 {$uWdFilter}
                ) dt
                LEFT JOIN (
                    SELECT DATE(r.date) as report_date,
                           COUNT(DISTINCT r.user_id) as total_users,
                           SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) as total_investment,
                           COUNT(*) as total_txns
                    FROM tbl_roi_one r
                    LEFT JOIN user u ON r.user_id = u.userid
                    WHERE DATE(r.date) BETWEEN :from_date3 AND :to_date3 {$uInvFilter}
                    GROUP BY DATE(r.date)
                ) inv ON dt.report_date = inv.report_date
                LEFT JOIN (
                    SELECT DATE(t.created_date) as report_date,
                           SUM(t.amount / {$rate}) as total_income
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%')
                      AND t.subject NOT LIKE 'Admin Adjustment%' AND t.status = 1
                      AND DATE(t.created_date) BETWEEN :from_date4 AND :to_date4 {$uIncFilter}
                    GROUP BY DATE(t.created_date)
                ) inc ON dt.report_date = inc.report_date
                LEFT JOIN (
                    SELECT DATE(t.created_date) as report_date,
                           SUM(t.amount) as total_withdrawals
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%')
                      AND t.subject NOT LIKE 'Admin Adjustment%'
                      AND (t.status = 1 OR t.a_status = '1') AND t.status != 2
                      AND DATE(t.created_date) BETWEEN :from_date5 AND :to_date5 {$uWdFilter}
                    GROUP BY DATE(t.created_date)
                ) wd ON dt.report_date = wd.report_date
                HAVING (total_investment > 0 OR total_income > 0 OR total_withdrawals > 0 OR total_txns > 0)
                ORDER BY dt.report_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'monthly':
        // Monthly Financial Summary
        $uInvFilter = "";
        $uIncFilter = "";
        $uWdFilter  = "";
        $params = [
            ':from_date1' => $from_date, ':to_date1' => $to_date,
            ':from_date2' => $from_date, ':to_date2' => $to_date,
            ':from_date3' => $from_date, ':to_date3' => $to_date,
            ':from_date4' => $from_date, ':to_date4' => $to_date,
            ':from_date5' => $from_date, ':to_date5' => $to_date
        ];
        if (!empty($user_search)) {
            $uInvFilter = " AND (r.user_id LIKE :usearch_inv OR u.name LIKE :usearch_inv)";
            $uIncFilter = " AND (t.user_id LIKE :usearch_inc OR u.name LIKE :usearch_inc)";
            $uWdFilter  = " AND (t.user_id LIKE :usearch_wd OR u.name LIKE :usearch_wd)";
            $params[':usearch_inv'] = $uSearchParam;
            $params[':usearch_inc'] = $uSearchParam;
            $params[':usearch_wd']  = $uSearchParam;
        }

        $sql = "SELECT dt.report_month,
                       COALESCE(inv.total_users, 0) as total_users,
                       COALESCE(inv.total_investment, 0) as total_investment,
                       COALESCE(inc.total_income, 0) as total_income,
                       COALESCE(wd.total_withdrawals, 0) as total_withdrawals,
                       COALESCE(inv.total_txns, 0) as total_txns
                FROM (
                    SELECT DATE_FORMAT(r.date, '%Y-%m') as report_month FROM tbl_roi_one r LEFT JOIN user u ON r.user_id = u.userid WHERE DATE(r.date) BETWEEN :from_date1 AND :to_date1 {$uInvFilter}
                    UNION
                    SELECT DATE_FORMAT(t.created_date, '%Y-%m') as report_month FROM tbl_transaction t LEFT JOIN user u ON t.user_id = u.userid WHERE DATE(t.created_date) BETWEEN :from_date2 AND :to_date2 {$uWdFilter}
                ) dt
                LEFT JOIN (
                    SELECT DATE_FORMAT(r.date, '%Y-%m') as report_month,
                           COUNT(DISTINCT r.user_id) as total_users,
                           SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) as total_investment,
                           COUNT(*) as total_txns
                    FROM tbl_roi_one r
                    LEFT JOIN user u ON r.user_id = u.userid
                    WHERE DATE(r.date) BETWEEN :from_date3 AND :to_date3 {$uInvFilter}
                    GROUP BY DATE_FORMAT(r.date, '%Y-%m')
                ) inv ON dt.report_month = inv.report_month
                LEFT JOIN (
                    SELECT DATE_FORMAT(t.created_date, '%Y-%m') as report_month,
                           SUM(t.amount / {$rate}) as total_income
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%')
                      AND t.subject NOT LIKE 'Admin Adjustment%' AND t.status = 1
                      AND DATE(t.created_date) BETWEEN :from_date4 AND :to_date4 {$uIncFilter}
                    GROUP BY DATE_FORMAT(t.created_date, '%Y-%m')
                ) inc ON dt.report_month = inc.report_month
                LEFT JOIN (
                    SELECT DATE_FORMAT(t.created_date, '%Y-%m') as report_month,
                           SUM(t.amount) as total_withdrawals
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%')
                      AND t.subject NOT LIKE 'Admin Adjustment%'
                      AND (t.status = 1 OR t.a_status = '1') AND t.status != 2
                      AND DATE(t.created_date) BETWEEN :from_date5 AND :to_date5 {$uWdFilter}
                    GROUP BY DATE_FORMAT(t.created_date, '%Y-%m')
                ) wd ON dt.report_month = wd.report_month
                HAVING (total_investment > 0 OR total_income > 0 OR total_withdrawals > 0 OR total_txns > 0)
                ORDER BY dt.report_month DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'wallet':
        // Comprehensive Wallet Transaction Audit Ledger
        $whereClause = "WHERE DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.a_status, t.created_date as created_at 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 10000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rawRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $walletInflowUSD   = 0.0;
        $walletOutflowUSD  = 0.0;
        $walletAdminAdjUSD = 0.0;
        $walletRejectedUSD = 0.0;

        foreach ($rawRows as $r) { 
            $rawAmt = (float)$r['amount'];
            $subj   = $r['subject'] ?? '';
            $st     = (int)($r['status'] ?? 1);
            $ast    = (string)($r['a_status'] ?? '');

            $isINR = (
                (strpos($subj, 'Ananta Package Investment') !== false) ||
                (strpos($subj, 'Unlock Access Fee') !== false)
            );
            $usdAmount = $isINR ? ($rawAmt / $rate) : $rawAmt;
            $inrAmount = $isINR ? $rawAmt : ($usdAmount * $rate);

            $r['usd_amount'] = $usdAmount;
            $r['inr_amount'] = $inrAmount;
            $r['currency']   = $isINR ? 'INR' : 'USD';

            if ($st === 2 || $ast === '2') {
                $r['category']  = 'REJECTED';
                $r['cat_badge'] = '<span class="badge badge-danger">REJECTED (EXCLUDED)</span>';
                $walletRejectedUSD += $usdAmount;
            } elseif (strpos($subj, 'Admin Adjustment') !== false) {
                $r['category']  = 'ADMIN_ADJUSTMENT';
                $r['cat_badge'] = '<span class="badge badge-warning">ADMIN ADJUSTMENT</span>';
                $walletAdminAdjUSD += $usdAmount;
            } elseif (strpos($subj, 'Withdrawal') !== false || strpos($subj, 'Withdraw') !== false) {
                if ($st === 1 || $ast === '1') {
                    $r['category']  = 'OUTFLOW';
                    $r['cat_badge'] = '<span class="badge badge-info">APPROVED PAYOUT</span>';
                    $walletOutflowUSD += $usdAmount;
                } else {
                    $r['category']  = 'PENDING_PAYOUT';
                    $r['cat_badge'] = '<span class="badge badge-warning">PENDING PAYOUT</span>';
                }
            } else {
                $r['category']  = 'INFLOW';
                $r['cat_badge'] = '<span class="badge badge-success">BUSINESS INFLOW</span>';
                $walletInflowUSD += $usdAmount;
            }

            $reportData[] = $r;
        }
        $totalSum   = $walletInflowUSD;
        $totalCount = count($reportData);
        break;

    case 'yearly':
        // Group by Year for Yearly Summary
        $uInvFilter = "";
        $uIncFilter = "";
        $uWdFilter  = "";
        $params = [
            ':from_date1' => $from_date, ':to_date1' => $to_date,
            ':from_date2' => $from_date, ':to_date2' => $to_date,
            ':from_date3' => $from_date, ':to_date3' => $to_date,
            ':from_date4' => $from_date, ':to_date4' => $to_date,
            ':from_date5' => $from_date, ':to_date5' => $to_date
        ];
        if (!empty($user_search)) {
            $uInvFilter = " AND (r.user_id LIKE :usearch_inv OR u.name LIKE :usearch_inv)";
            $uIncFilter = " AND (t.user_id LIKE :usearch_inc OR u.name LIKE :usearch_inc)";
            $uWdFilter  = " AND (t.user_id LIKE :usearch_wd OR u.name LIKE :usearch_wd)";
            $params[':usearch_inv'] = $uSearchParam;
            $params[':usearch_inc'] = $uSearchParam;
            $params[':usearch_wd']  = $uSearchParam;
        }

        $sql = "SELECT dt.report_year,
                       COALESCE(inv.total_users, 0) as total_users,
                       COALESCE(inv.total_investment, 0) as total_investment,
                       COALESCE(inc.total_income, 0) as total_income,
                       COALESCE(wd.total_withdrawals, 0) as total_withdrawals,
                       COALESCE(inv.total_txns, 0) as total_txns
                FROM (
                    SELECT YEAR(r.date) as report_year FROM tbl_roi_one r LEFT JOIN user u ON r.user_id = u.userid WHERE DATE(r.date) BETWEEN :from_date1 AND :to_date1 {$uInvFilter}
                    UNION
                    SELECT YEAR(t.created_date) as report_year FROM tbl_transaction t LEFT JOIN user u ON t.user_id = u.userid WHERE DATE(t.created_date) BETWEEN :from_date2 AND :to_date2 {$uWdFilter}
                ) dt
                LEFT JOIN (
                    SELECT YEAR(r.date) as report_year,
                           COUNT(DISTINCT r.user_id) as total_users,
                           SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) as total_investment,
                           COUNT(*) as total_txns
                    FROM tbl_roi_one r
                    LEFT JOIN user u ON r.user_id = u.userid
                    WHERE DATE(r.date) BETWEEN :from_date3 AND :to_date3 {$uInvFilter}
                    GROUP BY YEAR(r.date)
                ) inv ON dt.report_year = inv.report_year
                LEFT JOIN (
                    SELECT YEAR(t.created_date) as report_year,
                           SUM(t.amount / {$rate}) as total_income
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%')
                      AND t.subject NOT LIKE 'Admin Adjustment%' AND t.status = 1
                      AND DATE(t.created_date) BETWEEN :from_date4 AND :to_date4 {$uIncFilter}
                    GROUP BY YEAR(t.created_date)
                ) inc ON dt.report_year = inc.report_year
                LEFT JOIN (
                    SELECT YEAR(t.created_date) as report_year,
                           SUM(t.amount) as total_withdrawals
                    FROM tbl_transaction t
                    LEFT JOIN user u ON t.user_id = u.userid
                    WHERE (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%')
                      AND t.subject NOT LIKE 'Admin Adjustment%'
                      AND (t.status = 1 OR t.a_status = '1') AND t.status != 2
                      AND DATE(t.created_date) BETWEEN :from_date5 AND :to_date5 {$uWdFilter}
                    GROUP BY YEAR(t.created_date)
                ) wd ON dt.report_year = wd.report_year
                HAVING (total_investment > 0 OR total_income > 0 OR total_withdrawals > 0 OR total_txns > 0)
                ORDER BY dt.report_year DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'user':
        // User-wise Statement Report
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch OR u.mobile LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT 
                    u.id, u.userid, u.name, u.mobile, 
                    COALESCE((SELECT SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) FROM tbl_roi_one r WHERE r.user_id = u.userid AND r.status = '0'), 0) as investment, 
                    u.active, u.joining_date,
                    COALESCE((SELECT SUM(t.amount / {$rate}) FROM tbl_transaction t WHERE t.user_id = u.userid AND (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%') AND t.subject NOT LIKE 'Admin Adjustment%' AND t.status = 1), 0) as total_income,
                    COALESCE((SELECT SUM(t.amount) FROM tbl_transaction t WHERE t.user_id = u.userid AND (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%') AND t.subject NOT LIKE 'Admin Adjustment%' AND (t.status = 1 OR t.a_status = '1') AND t.status != 2), 0) as total_withdrawal
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'investment':
        // Package Investment & Activations
        $whereClause = "WHERE DATE(r.date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (r.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT r.id, r.user_id, u.name, 
                       CASE 
                           WHEN r.real_fund_usd > 0 THEN r.real_fund_usd 
                           ELSE (r.package / {$rate}) 
                       END as amount, 
                       r.package as inr_amount,
                       r.capital_withdrawal_status,
                       r.status,
                       r.percentage, r.date as created_at 
                FROM tbl_roi_one r 
                LEFT JOIN user u ON r.user_id = u.userid 
                {$whereClause} 
                ORDER BY r.id DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['amount']; 
        }
        $totalCount = count($reportData);
        break;

    case 'withdrawal':
        // Withdrawal Payout & Settlements
        $whereClause = "WHERE (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%') 
                        AND t.subject NOT LIKE 'Admin Adjustment%'
                        AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.status, t.a_status, t.admin_remarks, t.created_date as created_at 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rawWd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $wdPaidUSD     = 0.0;
        $wdPendingUSD  = 0.0;
        $wdRejectedUSD = 0.0;

        foreach ($rawWd as $r) {
            $amt = (float)$r['amount'];
            $st  = (int)($r['status'] ?? 0);
            $ast = (string)($r['a_status'] ?? '');

            if ($st === 2 || $ast === '2') {
                $r['status_label'] = 'REJECTED';
                $r['status_badge'] = '<span class="badge badge-danger">REJECTED</span>';
                $wdRejectedUSD += $amt;
            } elseif ($st === 1 || $ast === '1') {
                $r['status_label'] = 'APPROVED / PAID';
                $r['status_badge'] = '<span class="badge badge-success">APPROVED / PAID</span>';
                $wdPaidUSD += $amt;
            } else {
                $r['status_label'] = 'PENDING';
                $r['status_badge'] = '<span class="badge badge-warning">PENDING</span>';
                $wdPendingUSD += $amt;
            }
            $r['usd_amount'] = $amt;
            $reportData[] = $r;
        }
        $totalSum   = $wdPaidUSD;
        $totalCount = count($reportData);
        break;

    case 'income':
        // Comprehensive Income Distributions
        $whereClause = "WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Reward%' OR t.subject LIKE '%Profit%') 
                        AND t.subject NOT LIKE 'Admin Adjustment%'
                        AND t.status != 2
                        AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT t.id, t.user_id, u.name, (t.amount / {$rate}) as amount, t.subject, t.status, t.created_date as created_at 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['amount']; 
        }
        $totalCount = count($reportData);
        break;

    case 'business':
        // Business Volume Report
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT u.id, u.userid as user_id, u.name, 
                       COALESCE((SELECT SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) FROM tbl_roi_one r WHERE r.user_id = u.userid AND r.status = '0'), 0) as self_investment, 
                       COALESCE(t.left_id, 'None') as left_volume, 
                       COALESCE(t.right_id, 'None') as right_volume,
                       COALESCE((SELECT SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) FROM tbl_roi_one r JOIN user down ON down.sponserid = u.userid WHERE down.userid = r.user_id AND r.status = '0'), 0) as total_team_volume,
                       u.joining_date as created_at
                FROM user u 
                LEFT JOIN tree t ON u.userid = t.userid 
                {$whereClause} 
                ORDER BY total_team_volume DESC, self_investment DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_team_volume']; 
        }
        $totalCount = count($reportData);
        break;

    case 'company':
        // Company Revenue & $11 Unlock Access Report
        $whereClause = "WHERE u.active = '1' AND DATE(COALESCE(u.activation_start_date, u.upgrade_date, u.joining_date)) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT u.id, u.userid as user_id, u.name, 
                       COALESCE(DATE(u.activation_start_date), u.upgrade_date, u.joining_date) as created_at 
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 1000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $u) {
            $totalSum += 11.00; // $11 base USD unlock access revenue
            $reportData[] = [
                'id' => $u['id'],
                'user_id' => $u['user_id'],
                'name' => $u['name'],
                'amount' => 11.00,
                'subject' => 'Account Unlock Access Fee ($11)',
                'created_at' => $u['created_at']
            ];
        }
        $totalCount = count($reportData);
        break;
}
?>

<style>
/* Responsive High contrast form control styling */
.form-control, select.form-control, input[type="text"].form-control, input[type="date"].form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    opacity: 1 !important;
    width: 100% !important;
    box-sizing: border-box !important;
    height: 44px !important;
}
.form-control:focus, select.form-control:focus, input[type="text"].form-control:focus, input[type="date"].form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}
select.form-control option {
    background-color: #ffffff !important;
    color: #0f172a !important;
}
.form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}
label.form-label-custom {
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 6px !important;
    display: block !important;
    font-size: 13px !important;
}
/* Responsive action button container */
.btn-filter-group {
    display: flex !important;
    gap: 10px !important;
    align-items: center !important;
    width: 100% !important;
}
.btn-filter-apply {
    flex: 2 1 auto !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #0284c7 !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 16px !important;
}
.btn-filter-reset {
    flex: 1 1 auto !important;
    height: 44px !important;
    border-radius: 10px !important;
    background: #64748b !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 16px !important;
    text-decoration: none !important;
}
.btn-filter-reset:hover {
    background: #475569 !important;
    color: #ffffff !important;
}
@media (max-width: 767.98px) {
    .filter-card-body {
        padding: 16px !important;
    }
    .header-actions-group {
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
    }
    .header-actions-group .btn {
        width: 100% !important;
        margin-right: 0 !important;
    }
}
</style>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Banner -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
      <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">REPORTS & ANALYTICS</span>
          <h3 class="mb-1 text-white font-weight-bold"><?php echo htmlspecialchars($title); ?></h3>
          <p class="mb-0 text-white-50 small">Filter financial records by date range, user ID/name, and export official CSV or PDF reports.</p>
        </div>
        <div class="header-actions-group">
          <a href="export.php?module=<?php echo urlencode($type); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&user_search=<?php echo urlencode($user_search); ?>&format=csv" class="btn btn-light font-weight-bold px-3 py-2 mr-2" style="border-radius: 10px;">
            <i class="fa fa-file-excel-o text-success mr-1"></i> Export CSV / Excel
          </a>
          <a href="export.php?module=<?php echo urlencode($type); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&user_search=<?php echo urlencode($user_search); ?>&format=pdf" class="btn btn-outline-light font-weight-bold px-3 py-2" style="border-radius: 10px;" target="_blank">
            <i class="fa fa-file-pdf-o text-danger mr-1"></i> Export PDF
          </a>
        </div>
      </div>
    </div>

    <?php if (!empty($dateError)): ?>
      <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 600;">
        <i class="fa fa-exclamation-triangle mr-2"></i> <?php echo htmlspecialchars($dateError); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <!-- Filter Form with Calendar Datepicker & User Search -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-body p-4 filter-card-body">
        <form method="GET" class="row">
          
          <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <label class="form-label-custom">Report Category / Type</label>
            <select name="type" class="form-control" onchange="this.form.submit()">
              <option value="daily" <?php echo $type==='daily'?'selected':''; ?>>📅 Daily Financial Summary</option>
              <option value="monthly" <?php echo $type==='monthly'?'selected':''; ?>>📆 Monthly Financial Summary</option>
              <option value="wallet" <?php echo $type==='wallet'?'selected':''; ?>>💳 Wallet Transaction Summary</option>
              <option value="yearly" <?php echo $type==='yearly'?'selected':''; ?>>📊 Yearly Business Summary</option>
              <option value="user" <?php echo $type==='user'?'selected':''; ?>>👤 User-Wise Financial Statement</option>
              <option value="investment" <?php echo $type==='investment'?'selected':''; ?>>💼 Package Investment Report</option>
              <option value="withdrawal" <?php echo $type==='withdrawal'?'selected':''; ?>>💸 Withdrawal Settlement Report</option>
              <option value="income" <?php echo $type==='income'?'selected':''; ?>>💰 Income Distribution Report</option>
              <option value="business" <?php echo $type==='business'?'selected':''; ?>>🌐 Team Business Volume Report</option>
              <option value="company" <?php echo $type==='company'?'selected':''; ?>>🏢 Company Revenue ($11) Report</option>
            </select>
          </div>

          <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <label class="form-label-custom">🔍 Search User ID / Name</label>
            <input type="text" name="user_search" value="<?php echo htmlspecialchars($user_search); ?>" class="form-control" placeholder="Enter User ID or Member Name...">
          </div>

          <div class="col-xl-2 col-lg-2 col-md-6 col-6 mb-3">
            <label class="form-label-custom">📅 From Date</label>
            <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
          </div>

          <div class="col-xl-2 col-lg-2 col-md-6 col-6 mb-3">
            <label class="form-label-custom">📅 To Date</label>
            <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
          </div>

          <div class="col-xl-2 col-lg-2 col-md-12 col-12 mb-3 d-flex align-items-end">
            <div class="btn-filter-group">
              <button type="submit" class="btn btn-filter-apply">
                <i class="fa fa-filter mr-1"></i> Apply
              </button>
              <a href="reports.php?type=<?php echo htmlspecialchars($type); ?>" class="btn btn-filter-reset" title="Reset Filter to Default">
                <i class="fa fa-refresh mr-1"></i> Reset
              </a>
            </div>
          </div>

        </form>
        <div class="mt-2 text-muted small d-flex align-items-center flex-wrap gap-2">
          <strong>Active Filter Period:</strong> 
          <span class="badge badge-light border text-dark px-2 py-1" style="font-size: 12px; border-radius: 6px;"><?php echo htmlspecialchars($from_date); ?></span> 
          <span>to</span> 
          <span class="badge badge-light border text-dark px-2 py-1" style="font-size: 12px; border-radius: 6px;"><?php echo htmlspecialchars($to_date); ?></span>
        </div>
      </div>
    </div>

    <!-- Summary Metrics -->
    <?php if ($type === 'wallet'): ?>
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">GROSS BUSINESS INFLOW (USD)</span>
            <h4 class="mb-0 font-weight-bold text-success">$<?php echo number_format($walletInflowUSD, 2); ?></h4>
            <small class="text-muted">₹<?php echo number_format($walletInflowUSD * $rate, 2); ?> INR</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">APPROVED PAYOUTS (USD)</span>
            <h4 class="mb-0 font-weight-bold text-info">$<?php echo number_format($walletOutflowUSD, 2); ?></h4>
            <small class="text-muted">Settled Withdrawals</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">ADMIN ADJUSTMENTS (USD)</span>
            <h4 class="mb-0 font-weight-bold text-warning">$<?php echo number_format($walletAdminAdjUSD, 2); ?></h4>
            <small class="text-muted">Internal Ledger Balances</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">TOTAL AUDIT RECORDS</span>
            <h4 class="mb-0 font-weight-bold text-primary"><?php echo number_format($totalCount); ?> Txns</h4>
            <small class="text-muted">Excl. $<?php echo number_format($walletRejectedUSD, 2); ?> Rejected</small>
          </div>
        </div>
      </div>
    <?php elseif ($type === 'withdrawal'): ?>
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">SETTLED PAYOUTS (USD)</span>
            <h4 class="mb-0 font-weight-bold text-success">$<?php echo number_format($wdPaidUSD, 2); ?></h4>
            <small class="text-muted">₹<?php echo number_format($wdPaidUSD * $rate, 2); ?> INR</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">PENDING PAYOUTS (USD)</span>
            <h4 class="mb-0 font-weight-bold text-warning">$<?php echo number_format($wdPendingUSD, 2); ?></h4>
            <small class="text-muted">Awaiting Admin Action</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">REJECTED REQUESTS (USD)</span>
            <h4 class="mb-0 font-weight-bold text-danger">$<?php echo number_format($wdRejectedUSD, 2); ?></h4>
            <small class="text-muted">Excluded from Payouts</small>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="p-3 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">TOTAL REQUEST COUNT</span>
            <h4 class="mb-0 font-weight-bold text-primary"><?php echo number_format($totalCount); ?> Records</h4>
            <small class="text-muted">Filtered Period</small>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">TOTAL REPORT VOLUME / REVENUE</span>
            <h3 class="mb-0 font-weight-bold text-success"><?php echo formatCurrency($totalSum); ?></h3>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">TOTAL RECORD COUNT</span>
            <h3 class="mb-0 font-weight-bold text-primary"><?php echo number_format($totalCount); ?> Records</h3>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Specialized Table Output for Each Report Type -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-table text-primary mr-2"></i> Detailed <?php echo htmlspecialchars($title); ?> Records</h5>
        <?php if (!empty($user_search)): ?>
          <span class="badge badge-info px-3 py-1" style="border-radius: 20px;">Filtered by: "<?php echo htmlspecialchars($user_search); ?>"</span>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            
            <?php if (in_array($type, ['daily', 'monthly', 'yearly'])): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4"><?php echo ucfirst($type); ?> Period</th>
                  <th class="py-3">Active Transacting Members</th>
                  <th class="py-3">Total Investment</th>
                  <th class="py-3">Total Income Distributed</th>
                  <th class="py-3">Total Withdrawals Paid</th>
                  <th class="py-3 px-4">Transaction Count</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No summary records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold text-primary">
                      <?php echo htmlspecialchars($row['report_date'] ?? $row['report_month'] ?? $row['report_year'] ?? ''); ?>
                    </td>
                    <td><span class="badge badge-secondary px-2 py-1"><?php echo number_format($row['total_users']); ?> Users</span></td>
                    <td class="font-weight-bold text-success"><?php echo formatCurrency((float)$row['total_investment']); ?></td>
                    <td class="font-weight-bold text-info"><?php echo formatCurrency((float)$row['total_income']); ?></td>
                    <td class="font-weight-bold text-danger"><?php echo formatCurrency((float)$row['total_withdrawals']); ?></td>
                    <td class="px-4 font-weight-bold"><?php echo number_format($row['total_txns']); ?> Txns</td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'user'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Mobile Number</th>
                  <th class="py-3">Self Investment</th>
                  <th class="py-3">Total Income</th>
                  <th class="py-3">Total Withdrawn</th>
                  <th class="py-3 px-4">Joining Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="8" class="text-center py-5 text-muted">No user statements found matching filter criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['userid']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                    <td class="font-weight-bold text-success"><?php echo formatCurrency((float)$row['investment']); ?></td>
                    <td class="font-weight-bold text-info"><?php echo formatCurrency((float)$row['total_income']); ?></td>
                    <td class="font-weight-bold text-danger"><?php echo formatCurrency((float)$row['total_withdrawal']); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['joining_date']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'wallet'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Txn ID</th>
                  <th class="py-3">User ID & Name</th>
                  <th class="py-3">Type</th>
                  <th class="py-3">Stored Amount</th>
                  <th class="py-3">USD Equivalent ($)</th>
                  <th class="py-3">INR Equivalent (₹)</th>
                  <th class="py-3">Accounting Category</th>
                  <th class="py-3">Description / Subject</th>
                  <th class="py-3 px-4">Date & Time</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="9" class="text-center py-5 text-muted">No wallet transaction records found matching filter criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($row['user_id']); ?></strong>
                      <br><small class="text-muted"><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></small>
                    </td>
                    <td>
                      <span class="badge badge-<?php echo strtolower($row['type'] ?? '') === 'credit' ? 'success' : 'secondary'; ?>">
                        <?php echo htmlspecialchars($row['type'] ?? 'Txn'); ?>
                      </span>
                    </td>
                    <td class="font-weight-bold">
                      <?php echo ($row['currency'] === 'INR' ? '₹' : '$') . number_format((float)$row['amount'], 2); ?>
                    </td>
                    <td class="font-weight-bold text-success">$<?php echo number_format((float)$row['usd_amount'], 2); ?></td>
                    <td class="font-weight-bold text-info">₹<?php echo number_format((float)$row['inr_amount'], 2); ?></td>
                    <td><?php echo $row['cat_badge']; ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['subject'] ?? ''); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'investment'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Record ID</th>
                  <th class="py-3">User ID & Name</th>
                  <th class="py-3">Package Amount (USD)</th>
                  <th class="py-3">INR Equivalent (₹)</th>
                  <th class="py-3">Status</th>
                  <th class="py-3 px-4">Investment Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No package investment records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($row['user_id']); ?></strong>
                      <br><small class="text-muted"><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></small>
                    </td>
                    <td class="font-weight-bold text-success">$<?php echo number_format((float)$row['amount'], 2); ?></td>
                    <td class="font-weight-bold text-info">₹<?php echo number_format((float)$row['inr_amount'], 2); ?></td>
                    <td>
                      <span class="badge badge-<?php echo ($row['status'] == '0') ? 'success' : 'secondary'; ?>">
                        <?php echo ($row['status'] == '0') ? 'ACTIVE' : 'WITHDRAWN / CLOSED'; ?>
                      </span>
                    </td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'withdrawal'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Txn ID</th>
                  <th class="py-3">User ID & Name</th>
                  <th class="py-3">Amount (USD)</th>
                  <th class="py-3">Status</th>
                  <th class="py-3">Description / Subject</th>
                  <th class="py-3">Admin Remarks</th>
                  <th class="py-3 px-4">Date & Time</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="7" class="text-center py-5 text-muted">No withdrawal settlement records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($row['user_id']); ?></strong>
                      <br><small class="text-muted"><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></small>
                    </td>
                    <td class="font-weight-bold text-dark">$<?php echo number_format((float)$row['usd_amount'], 2); ?></td>
                    <td><?php echo $row['status_badge']; ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['subject'] ?? ''); ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['admin_remarks'] ?? '-'); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'business'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Self Investment</th>
                  <th class="py-3">Left Subtree User</th>
                  <th class="py-3">Right Subtree User</th>
                  <th class="py-3">Direct Sponsor Volume</th>
                  <th class="py-3 px-4">Joining Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="8" class="text-center py-5 text-muted">No business volume records found matching filter criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['user_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="font-weight-bold text-dark"><?php echo formatCurrency((float)$row['self_investment']); ?></td>
                    <td><span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($row['left_volume']); ?></span></td>
                    <td><span class="badge badge-warning px-2 py-1"><?php echo htmlspecialchars($row['right_volume']); ?></span></td>
                    <td class="font-weight-bold text-success"><?php echo formatCurrency((float)$row['total_team_volume']); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'company'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Revenue Amount (USD)</th>
                  <th class="py-3">INR Equivalent (₹)</th>
                  <th class="py-3">Fee Description</th>
                  <th class="py-3 px-4">Activation Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No unlock access revenue records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold"><strong><?php echo htmlspecialchars($row['user_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></td>
                    <td class="font-weight-bold text-success">$<?php echo number_format((float)$row['amount'], 2); ?></td>
                    <td class="font-weight-bold text-info">₹<?php echo number_format((float)$row['amount'] * $rate, 2); ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php else: ?>
              <!-- Income Distributions Report -->
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Record ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Amount (USD)</th>
                  <th class="py-3">Description / Details</th>
                  <th class="py-3 px-4">Date & Time</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No income distribution records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['user_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></td>
                    <td class="font-weight-bold text-success"><?php echo formatCurrency((float)($row['usd_amount'] ?? $row['amount'])); ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['subject'] ?? 'Income Record'); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            <?php endif; ?>

          </table>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
<?php include 'common/footer.php'; ?>
</body>
</html>
