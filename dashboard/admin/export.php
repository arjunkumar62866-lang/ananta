<?php
session_start();

if (!isset($_SESSION['auserid'])) {
    die("Unauthorized access. Admin session required.");
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

$module = $_GET['module'] ?? 'audit';
$format = strtolower($_GET['format'] ?? 'csv');

$filename = "ananta_" . $module . "_report_" . date('Ymd_His') . "." . ($format === 'excel' ? 'xls' : ($format === 'pdf' ? 'pdf' : 'csv'));

if ($format === 'csv' || $format === 'excel') {
    if ($format === 'excel') {
        header("Content-Type: application/vnd.ms-excel");
    } else {
        header("Content-Type: text/csv; charset=utf-8");
    }
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    $output = fopen("php://output", "w");
} else {
    // Basic clean HTML print fallback for PDF / print format
    header("Content-Type: text/html; charset=utf-8");
}

switch ($module) {
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

        if ($format === 'csv' || $format === 'excel') {
            fputcsv($output, ['Log ID', 'Admin ID', 'Action', 'Target User ID', 'Target Name', 'Amount (INR)', 'Wallet Type', 'Previous Balance', 'New Balance', 'Reason', 'Reference ID', 'IP Address', 'Date & Time']);
            foreach ($rows as $r) {
                fputcsv($output, [
                    $r['id'], $r['admin_id'], $r['action'], $r['target_user_id'], $r['target_name'],
                    $r['amount'], $r['wallet_type'], $r['previous_balance'], $r['new_balance'],
                    $r['reason'], $r['reference_id'], $r['ip_address'], $r['created_at']
                ]);
            }
            fclose($output);
            exit;
        } else {
            echo "<h2>Ananta Admin Audit Log Report</h2>";
            echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr><th>ID</th><th>Admin</th><th>Action</th><th>Target User</th><th>Amount</th><th>Reason</th><th>Date</th></tr></thead><tbody>";
            foreach ($rows as $r) {
                echo "<tr><td>{$r['id']}</td><td>{$r['admin_id']}</td><td>{$r['action']}</td><td>{$r['target_user_id']}</td><td>₹{$r['amount']}</td><td>{$r['reason']}</td><td>{$r['created_at']}</td></tr>";
            }
            echo "</tbody></table>";
            exit;
        }
        break;

    case 'users':
        $query = "SELECT userid, name, email, mobile, sponserid, active, status, joining_date, amount, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet FROM user ORDER BY id DESC LIMIT 10000";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'csv' || $format === 'excel') {
            fputcsv($output, ['User ID', 'Name', 'Email', 'Mobile', 'Sponsor ID', 'Active Status', 'Account Status', 'Joining Date', 'Main Wallet', 'Profit Income', 'Profit Sharing', 'Direct Bonus', 'Mentor Income', 'VIP Club Wallet']);
            foreach ($rows as $r) {
                fputcsv($output, [
                    $r['userid'], $r['name'], $r['email'], $r['mobile'], $r['sponserid'],
                    $r['active'] == '1' ? 'ACTIVE' : 'INACTIVE',
                    $r['status'] == '1' ? 'NORMAL' : 'BLOCKED',
                    $r['joining_date'], $r['amount'], $r['profit_income_wallet'],
                    $r['profit_sharing_wallet'], $r['direct_bonus_wallet'],
                    $r['mentor_income_wallet'], $r['vip_club_wallet']
                ]);
            }
            fclose($output);
            exit;
        }
        break;

    case 'withdrawals':
        $query = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.created_date, t.time FROM tbl_transaction t LEFT JOIN user u ON t.user_id = u.userid WHERE t.subject LIKE '%Withdraw%' ORDER BY t.id DESC LIMIT 5000";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'csv' || $format === 'excel') {
            fputcsv($output, ['Txn ID', 'User ID', 'Name', 'Amount', 'Subject', 'Type', 'Status', 'Date', 'Time']);
            foreach ($rows as $r) {
                $stText = ($r['status'] == '1') ? 'APPROVED/PAID' : (($r['status'] == '2') ? 'REJECTED' : 'PENDING');
                fputcsv($output, [$r['id'], $r['user_id'], $r['name'], $r['amount'], $r['subject'], $r['type'], $stText, $r['created_date'], $r['time']]);
            }
            fclose($output);
            exit;
        }
        break;

    case 'deposits':
        $query = "SELECT f.id, f.userid, u.name, f.amount, f.tr_id as utr, f.status, f.date FROM tbl_payment f LEFT JOIN user u ON f.userid = u.userid WHERE f.subject LIKE '%FUND REQUEST%' ORDER BY f.id DESC LIMIT 5000";
        $stmt = $pdo->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'csv' || $format === 'excel') {
            fputcsv($output, ['Request ID', 'User ID', 'Name', 'Amount', 'Transaction Hash / UTR', 'Status', 'Submission Date']);
            foreach ($rows as $r) {
                $stText = ($r['status'] == '1') ? 'APPROVED' : (($r['status'] == '2') ? 'REJECTED' : 'PENDING');
                fputcsv($output, [$r['id'], $r['userid'], $r['name'], $r['amount'], $r['utr'], $stText, $r['date']]);
            }
            fclose($output);
            exit;
        }
        break;

    default:
        die("Invalid export module specified.");
}
