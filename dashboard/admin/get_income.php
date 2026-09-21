<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

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

echo json_encode($users);
exit;
