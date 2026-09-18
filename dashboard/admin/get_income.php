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
