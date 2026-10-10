<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['auserid'])) {
    die("Unauthorized access. Admin session required.");
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

$module      = strtolower(trim($_GET['module'] ?? 'daily'));
$format      = strtolower(trim($_GET['format'] ?? 'csv'));
$from_date   = trim($_GET['from_date'] ?? date('Y-m-01'));
$to_date     = trim($_GET['to_date'] ?? date('Y-m-d'));
$user_search = trim($_GET['user_search'] ?? '');
$rate        = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
if ($rate <= 0) $rate = 90.0;

$filename = "ananta_" . $module . "_report_" . date('Ymd_His') . "." . ($format === 'excel' ? 'xls' : ($format === 'pdf' ? 'pdf' : 'csv'));

if ($format === 'csv' || $format === 'excel') {
    if (!headers_sent()) {
        if ($format === 'excel') {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        } else {
            header("Content-Type: text/csv; charset=utf-8");
        }
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
    }
    $output = fopen("php://output", "w");
} else {
    // Clean printable HTML format for PDF / Print
    if (!headers_sent()) {
        header("Content-Type: text/html; charset=utf-8");
    }
}

if (!function_exists('exitExport')) {
    function exitExport() {
        if (!defined('EXPORT_NO_EXIT')) {
            exit;
        }
    }
}

if (!function_exists('outputCSVRows')) {
    function outputCSVRows($output, $headers, $dataRows) {
        fputcsv($output, $headers, ",", '"', "\\");
        foreach ($dataRows as $dr) {
            fputcsv($output, $dr, ",", '"', "\\");
        }
    }
}

if (!function_exists('outputTableHTML')) {
    function outputTableHTML($title, $headers, $dataRows, $from_date, $to_date) {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>" . htmlspecialchars($title) . "</title>";
        echo "<style>body{font-family:system-ui,-apple-system,sans-serif;margin:24px;color:#0f172a;} table{width:100%;border-collapse:collapse;margin-top:16px;} th,td{border:1px solid #cbd5e1;padding:8px 12px;font-size:13px;text-align:left;} th{background:#f1f5f9;font-weight:700;} h2{margin:0 0 6px;} p{margin:0;color:#64748b;font-size:13px;}</style></head><body>";
        echo "<h2>" . htmlspecialchars($title) . "</h2>";
        echo "<p><strong>Filter Period:</strong> " . htmlspecialchars($from_date) . " to " . htmlspecialchars($to_date) . " | Generated: " . date('Y-m-d H:i:s') . "</p>";
        echo "<table><thead><tr>";
        foreach ($headers as $h) {
        echo "<th>" . htmlspecialchars($h) . "</th>";
    }
    echo "</tr></thead><tbody>";
    if (empty($dataRows)) {
        echo "<tr><td colspan='" . count($headers) . "' style='text-align:center;'>No records found for selected period.</td></tr>";
    } else {
        foreach ($dataRows as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars((string)$cell) . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</tbody></table><script>window.onload=function(){window.print();}</script></body></html>";
    exitExport();
    }
}

switch ($module) {
    case 'daily':
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
            $uSearchParam = '%' . $user_search . '%';
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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Date', 'Active Members', 'Total Investment (USD)', 'Total Income (USD)', 'Total Withdrawals (USD)', 'Txn Count'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['report_date'],
                $r['total_users'],
                '$' . number_format((float)$r['total_investment'], 2),
                '$' . number_format((float)$r['total_income'], 2),
                '$' . number_format((float)$r['total_withdrawals'], 2),
                $r['total_txns']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Daily Financial & Business Summary', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'monthly':
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
            $uSearchParam = '%' . $user_search . '%';
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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Month', 'Active Members', 'Total Investment (USD)', 'Total Income (USD)', 'Total Withdrawals (USD)', 'Txn Count'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['report_month'],
                $r['total_users'],
                '$' . number_format((float)$r['total_investment'], 2),
                '$' . number_format((float)$r['total_income'], 2),
                '$' . number_format((float)$r['total_withdrawals'], 2),
                $r['total_txns']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Monthly Financial Summary', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'yearly':
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
            $uSearchParam = '%' . $user_search . '%';
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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Year', 'Active Members', 'Total Investment (USD)', 'Total Income (USD)', 'Total Withdrawals (USD)', 'Txn Count'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['report_year'],
                $r['total_users'],
                '$' . number_format((float)$r['total_investment'], 2),
                '$' . number_format((float)$r['total_income'], 2),
                '$' . number_format((float)$r['total_withdrawals'], 2),
                $r['total_txns']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Yearly Business Summary', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'wallet':
    case 'transactions':
        $whereClause = "WHERE DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $query = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.a_status, t.created_date, t.time 
                  FROM tbl_transaction t 
                  LEFT JOIN user u ON t.user_id = u.userid 
                  {$whereClause} 
                  ORDER BY t.id DESC LIMIT 10000";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Txn ID', 'User ID', 'Member Name', 'Type', 'Stored Amount', 'Currency', 'USD Equivalent ($)', 'INR Equivalent (₹)', 'Status', 'Accounting Category', 'Description / Subject', 'Date', 'Time'];
        $dataRows = [];
        foreach ($rows as $r) {
            $rawAmt = (float)$r['amount'];
            $subj   = $r['subject'] ?? '';
            $st     = (int)($r['status'] ?? 1);
            $ast    = (string)($r['a_status'] ?? '');

            $isINR  = ((strpos($subj, 'Ananta Package Investment') !== false) || (strpos($subj, 'Unlock Access Fee') !== false));
            $usdVal = $isINR ? round($rawAmt / $rate, 2) : round($rawAmt, 2);
            $inrVal = $isINR ? round($rawAmt, 2) : round($usdVal * $rate, 2);
            $curr   = $isINR ? 'INR' : 'USD';

            if ($st === 2 || $ast === '2') {
                $stText  = 'REJECTED';
                $catText = 'REJECTED (EXCLUDED)';
            } elseif (strpos($subj, 'Admin Adjustment') !== false) {
                $stText  = 'COMPLETED';
                $catText = 'ADMIN ADJUSTMENT';
            } elseif (strpos($subj, 'Withdrawal') !== false || strpos($subj, 'Withdraw') !== false) {
                $stText  = ($st === 1 || $ast === '1') ? 'APPROVED/PAID' : 'PENDING';
                $catText = ($st === 1 || $ast === '1') ? 'APPROVED PAYOUT' : 'PENDING PAYOUT';
            } else {
                $stText  = 'COMPLETED';
                $catText = 'BUSINESS INFLOW';
            }

            $dataRows[] = [
                $r['id'], $r['user_id'], $r['name'] ?? 'Member', $r['type'],
                $r['amount'], $curr, number_format($usdVal, 2), number_format($inrVal, 2),
                $stText, $catText, $r['subject'], $r['created_date'], $r['time']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Wallet Transaction Audit Ledger', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'investment':
    case 'investments':
        $whereClause = "WHERE DATE(r.date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (r.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $sql = "SELECT r.id, r.user_id, u.name, 
                       CASE 
                           WHEN r.real_fund_usd > 0 THEN r.real_fund_usd 
                           ELSE (r.package / {$rate}) 
                       END as amount, 
                       r.package as inr_amount,
                       r.status, r.capital_withdrawal_status, r.date as created_at 
                FROM tbl_roi_one r 
                LEFT JOIN user u ON r.user_id = u.userid 
                {$whereClause} 
                ORDER BY r.id DESC LIMIT 5000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Record ID', 'User ID', 'Member Name', 'Package Amount (USD)', 'INR Equivalent (₹)', 'Status', 'Date'];
        $dataRows = [];
        foreach ($rows as $r) {
            $stText = ($r['status'] == '0') ? 'ACTIVE' : 'WITHDRAWN / CLOSED';
            $dataRows[] = [
                $r['id'], $r['user_id'], $r['name'] ?? 'Member',
                '$' . number_format((float)$r['amount'], 2),
                '₹' . number_format((float)$r['inr_amount'], 2),
                $stText, $r['created_at']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Package Investment & Activation Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'withdrawal':
    case 'withdrawals':
        $whereClause = "WHERE (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%') 
                        AND t.subject NOT LIKE 'Admin Adjustment%'
                        AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $query = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.a_status, t.admin_remarks, t.created_date, t.time 
                  FROM tbl_transaction t 
                  LEFT JOIN user u ON t.user_id = u.userid 
                  {$whereClause} 
                  ORDER BY t.id DESC LIMIT 5000";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Txn ID', 'User ID', 'Name', 'Amount (USD)', 'Subject', 'Status', 'Admin Remarks', 'Date', 'Time'];
        $dataRows = [];
        foreach ($rows as $r) {
            $st  = (int)($r['status'] ?? 0);
            $ast = (string)($r['a_status'] ?? '');
            $stText = ($st === 2 || $ast === '2') ? 'REJECTED' : (($st === 1 || $ast === '1') ? 'APPROVED/PAID' : 'PENDING');
            $dataRows[] = [
                $r['id'], $r['user_id'], $r['name'] ?? 'Member',
                '$' . number_format((float)$r['amount'], 2),
                $r['subject'], $stText, $r['admin_remarks'] ?? '-', $r['created_date'], $r['time']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Withdrawal Settlement Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'company':
        $whereClause = "WHERE u.active = '1' AND DATE(COALESCE(u.activation_start_date, u.upgrade_date, u.joining_date)) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $sql = "SELECT u.id, u.userid as user_id, u.name, 
                       COALESCE(DATE(u.activation_start_date), u.upgrade_date, u.joining_date) as created_at 
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 5000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['User ID', 'Member Name', 'Revenue Amount (USD)', 'INR Equivalent (₹)', 'Fee Description', 'Activation Date'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['user_id'], $r['name'] ?? 'Member',
                '$11.00', '₹' . number_format(11.00 * $rate, 2),
                'Account Unlock Access Fee ($11)', $r['created_at']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Company Revenue & Unlock Access Fee Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'income':
        $whereClause = "WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%') 
                        AND t.subject NOT LIKE 'Admin Adjustment%' 
                        AND t.status != 2
                        AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $sql = "SELECT t.id, t.user_id, u.name, (t.amount / {$rate}) as amount, t.subject, t.status, t.created_date, t.time 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 5000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Txn ID', 'User ID', 'Member Name', 'Income Amount (USD)', 'Subject / Description', 'Date', 'Time'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['id'], $r['user_id'], $r['name'] ?? 'Member',
                '$' . number_format((float)$r['amount'], 2),
                $r['subject'], $r['created_date'], $r['time']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Comprehensive Income Distribution Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'business':
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
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
                ORDER BY total_team_volume DESC, self_investment DESC LIMIT 5000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['ID', 'User ID', 'Member Name', 'Self Investment (USD)', 'Left Subtree', 'Right Subtree', 'Team Sponsor Volume (USD)', 'Joining Date'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['id'], $r['user_id'], $r['name'],
                '$' . number_format((float)$r['self_investment'], 2),
                $r['left_volume'], $r['right_volume'],
                '$' . number_format((float)$r['total_team_volume'], 2),
                $r['created_at']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Team Business Volume Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'user':
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch OR u.mobile LIKE :usearch)";
            $params[':usearch'] = '%' . $user_search . '%';
        }
        $sql = "SELECT 
                    u.id, u.userid, u.name, u.mobile, 
                    COALESCE((SELECT SUM(CASE WHEN r.real_fund_usd > 0 THEN r.real_fund_usd ELSE (r.package / {$rate}) END) FROM tbl_roi_one r WHERE r.user_id = u.userid AND r.status = '0'), 0) as investment, 
                    u.active, u.joining_date,
                    COALESCE((SELECT SUM(t.amount / {$rate}) FROM tbl_transaction t WHERE t.user_id = u.userid AND (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Profit%' OR t.subject LIKE '%Reward%') AND t.subject NOT LIKE 'Admin Adjustment%' AND t.status = 1), 0) as total_income,
                    COALESCE((SELECT SUM(t.amount) FROM tbl_transaction t WHERE t.user_id = u.userid AND (t.subject LIKE '%Withdrawal%' OR t.subject LIKE '%Withdraw%') AND t.subject NOT LIKE 'Admin Adjustment%' AND (t.status = 1 OR t.a_status = '1') AND t.status != 2), 0) as total_withdrawal
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 5000";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['ID', 'User ID', 'Name', 'Mobile', 'Self Investment (USD)', 'Total Income (USD)', 'Total Withdrawn (USD)', 'Joining Date'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['id'], $r['userid'], $r['name'], $r['mobile'],
                '$' . number_format((float)$r['investment'], 2),
                '$' . number_format((float)$r['total_income'], 2),
                '$' . number_format((float)$r['total_withdrawal'], 2),
                $r['joining_date']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('User-Wise Financial Statement Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'audit':
        $user_id = trim($_GET['user_id'] ?? '');
        $action  = trim($_GET['action'] ?? '');

        $query = "SELECT l.id, l.admin_id, l.action, l.target_user_id, u.name as target_name, l.amount, l.wallet_type, l.previous_balance, l.new_balance, l.reason, l.reference_id, l.ip_address, l.created_at 
                  FROM tbl_admin_audit_log l 
                  LEFT JOIN user u ON l.target_user_id = u.userid WHERE 1=1";
        $params = [];
        if (!empty($user_id)) {
            $query .= " AND (l.target_user_id LIKE :user OR l.admin_id LIKE :user)";
            $params[':user'] = "%{$user_id}%";
        }
        if (!empty($action)) {
            $query .= " AND l.action = :action";
            $params[':action'] = $action;
        }
        $query .= " ORDER BY l.id DESC LIMIT 5000";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Log ID', 'Admin ID', 'Action', 'Target User ID', 'Target Name', 'Amount (INR)', 'Wallet Type', 'Previous Balance', 'New Balance', 'Reason', 'Reference ID', 'IP Address', 'Date & Time'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['id'], $r['admin_id'], $r['action'], $r['target_user_id'], $r['target_name'],
                $r['amount'], $r['wallet_type'], $r['previous_balance'], $r['new_balance'],
                $r['reason'], $r['reference_id'], $r['ip_address'], $r['created_at']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Admin Audit Log Report', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'users':
        $query = "SELECT userid, name, email, mobile, sponserid, active, status, joining_date, amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet FROM user WHERE 1=1";
        $params = [];
        if (!empty($from_date) && !empty($to_date)) {
            $query .= " AND DATE(joining_date) BETWEEN :from_date AND :to_date";
            $params[':from_date'] = $from_date;
            $params[':to_date']   = $to_date;
        }
        $query .= " ORDER BY id DESC LIMIT 10000";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['User ID', 'Name', 'Email', 'Mobile', 'Sponsor ID', 'Active Status', 'Account Status', 'Joining Date', 'Main Wallet', 'Profit Income', 'Profit Sharing', 'Direct Bonus', 'Mentor Income', 'VIP Club Wallet'];
        $dataRows = [];
        foreach ($rows as $r) {
            $dataRows[] = [
                $r['userid'], $r['name'], $r['email'], $r['mobile'], $r['sponserid'],
                $r['active'] == '1' ? 'ACTIVE' : 'INACTIVE',
                $r['status'] == '1' ? 'NORMAL' : 'BLOCKED',
                $r['joining_date'], $r['amount'], $r['profit_income_wallet'],
                $r['profit_sharing_wallet'], $r['direct_bonus_wallet'],
                $r['mentor_income_wallet'], $r['vip_club_wallet']
            ];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('User Directory Statement', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    case 'deposits':
        $query = "SELECT f.id, f.userid, u.name, f.amount, f.tr_id as utr, f.status, f.date FROM tbl_payment f LEFT JOIN user u ON f.userid = u.userid WHERE f.subject LIKE '%FUND REQUEST%' ORDER BY f.id DESC LIMIT 5000";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $headers = ['Request ID', 'User ID', 'Name', 'Amount', 'Transaction Hash / UTR', 'Status', 'Submission Date'];
        $dataRows = [];
        foreach ($rows as $r) {
            $stText = ($r['status'] == '1') ? 'APPROVED' : (($r['status'] == '2') ? 'REJECTED' : 'PENDING');
            $dataRows[] = [$r['id'], $r['userid'], $r['name'], $r['amount'], $r['utr'], $stText, $r['date']];
        }

        if ($format === 'csv' || $format === 'excel') {
            outputCSVRows($output, $headers, $dataRows);
            fclose($output);
            exitExport();
        } else {
            outputTableHTML('Deposit & Fund Request History', $headers, $dataRows, $from_date, $to_date);
        }
        break;

    default:
        die("Invalid export module specified.");
}
