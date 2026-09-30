<?php
require 'common/connection.php'; // Your PDO connection
require_once 'common/db_method.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';
$rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;
if ($rate <= 0) $rate = 90.0;

switch ($type) {
    case 'level_income':
        $stmt = $pdo->prepare("SELECT user_id,subject,amount,time,created_date FROM tbl_levelinc WHERE subject Like '%Direct Income%' ORDER BY id DESC");
        break;
    case 'daily_level_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_daily_levelinc where subject Like '%Profit Sharing Income%' order by id desc");
        break;
    case 'roi_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_roiinc where subject Like '%Daily Profit Sharing Income%' order by id desc");
        break;
    case 'reward_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_rewardinc where subject Like '%Reward Income%' order by id desc");
        break;
    case 'generation_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_transaction where subject Like '%Generation Income%' order by id desc");
        break;
    case 'direct_bonus':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_transaction where subject Like '%Direct Bonus%' order by id desc");
        break;
    case 'direct_bonus_schedule':
        $stmt = $pdo->prepare("
            SELECT 
                s.id,
                s.investment_id,
                s.beneficiary_id,
                u1.name as beneficiary_name,
                u1.direct_bonus_wallet as beneficiary_wallet,
                s.source_user_id,
                u2.name as source_user_name,
                s.investment_amount,
                s.total_bonus,
                s.installment_amount,
                s.installment_number,
                s.installment_month,
                s.status,
                s.credited_at
            FROM tbl_direct_bonus_schedule s
            LEFT JOIN user u1 ON u1.userid = s.beneficiary_id
            LEFT JOIN user u2 ON u2.userid = s.source_user_id
            ORDER BY s.id DESC
        ");
        break;
    case 'ranking_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_transaction where subject Like '%Ranking Income%' order by id desc");
        break;
    case 'reward_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_transaction where subject Like '%Reward Income%' order by id desc");
        break;
    case 'leadership_income':
        $stmt = $pdo->prepare("select user_id, subject, amount, created_date from tbl_transaction where subject Like '%Leadership Income%' order by id desc");
        break;
    
    default:
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user ORDER BY id DESC");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Normalization layer: Ensure all output monetary values represent base USD to prevent duplicate currency conversion
foreach ($users as &$row) {
    if (isset($row['amount'])) {
        $rawInr = (float)$row['amount'];
        $row['amount_inr'] = round($rawInr, 2);
        $row['amount_usd'] = round($rawInr / $rate, 2);
        // Normalize amount to base USD so formatAdminCurrency(amount) performs exactly one conversion
        $row['amount']     = round($rawInr / $rate, 2);
    }
    
    if (isset($row['installment_amount'])) {
        $instInr = (float)$row['installment_amount'];
        $row['installment_amount_inr'] = round($instInr, 2);
        $row['installment_amount_usd'] = round($instInr / $rate, 2);
        $row['installment_amount']     = round($instInr / $rate, 2);
    }
    if (isset($row['investment_amount'])) {
        $invInr = (float)$row['investment_amount'];
        $row['investment_amount_inr'] = round($invInr, 2);
        $row['investment_amount_usd'] = round($invInr / $rate, 2);
        $row['investment_amount']     = round($invInr / $rate, 2);
    }
    if (isset($row['total_bonus'])) {
        $tbInr = (float)$row['total_bonus'];
        $row['total_bonus_inr'] = round($tbInr, 2);
        $row['total_bonus_usd'] = round($tbInr / $rate, 2);
        $row['total_bonus']     = round($tbInr / $rate, 2);
    }
    if (isset($row['beneficiary_wallet'])) {
        $bwInr = (float)$row['beneficiary_wallet'];
        $row['beneficiary_wallet_inr'] = round($bwInr, 2);
        $row['beneficiary_wallet_usd'] = round($bwInr / $rate, 2);
        $row['beneficiary_wallet']     = round($bwInr / $rate, 2);
    }
}
unset($row);

echo json_encode($users);
exit;

