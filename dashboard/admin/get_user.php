<?php
require 'common/connection.php'; // Your PDO connection
header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';

switch ($type) {
    case 'pool_one':
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user WHERE one_club_status = '1' ORDER BY id DESC");
        break;
        
    case 'pool_two':
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user WHERE two_club_status = '1' ORDER BY id DESC");
        break;
        
    case 'pool_three':
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user WHERE three_club_status = '1' ORDER BY id DESC");
        break;
        
    case 'pool_four':
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user WHERE four_club_status = '1' ORDER BY id DESC");
        break;
        
    case 'pool_five':
        $stmt = $pdo->prepare("SELECT userid,name,mobile,sponserid,sponsername,joining_date,status FROM user WHERE five_club_status = '1' ORDER BY id DESC");
        break;
        
    case 'active_user':
        $stmt = $pdo->prepare("SELECT userid,mobile,name,total_package,sponserid,sponsername,upgrade_date,joining_date,status,active,total_package FROM user WHERE active = '1' ORDER BY id DESC");
        break;
    case 'inactive_user':
        $stmt = $pdo->prepare("SELECT userid,mobile,name,total_package,sponserid,sponsername,upgrade_date,joining_date,status,active,total_package FROM user WHERE active = '0' ORDER BY id DESC");
        break;
        
    case 'deactive_user':
        $stmt = $pdo->prepare("SELECT userid,name,email,mobile,sponserid,sponsername,joining_date FROM user WHERE status='2' ORDER BY id DESC");
        break;
        
    case 'pending_user':
        $stmt = $pdo->prepare("SELECT userid,name,email,mobile,sponserid,sponsername,joining_date FROM user WHERE active ='0' and status!='2' ORDER BY id DESC");
        break;
        
    case 'pending_kyc':
        $stmt = $pdo->prepare("SELECT k.userid, COALESCE(NULLIF(TRIM(k.holder_name), ''), u.name, 'N/A') AS holder_name FROM kyc k LEFT JOIN user u ON (k.userid = u.userid OR k.userid = u.id) WHERE k.status = '0' ORDER BY k.id DESC");
        break;
        
    case 'completed_kyc':
        $stmt = $pdo->prepare("SELECT k.userid, COALESCE(NULLIF(TRIM(k.holder_name), ''), u.name, 'N/A') AS holder_name FROM kyc k LEFT JOIN user u ON (k.userid = u.userid OR k.userid = u.id) WHERE k.status = '1' ORDER BY k.id DESC");
        break;
        
    case 'active':
        $typeValue = ($_GET['type'] == "active") ? 1 : 0;
        $stmt = $pdo->prepare("SELECT userid, name, sponserid, sponsername, package, upgrade_date2, active 
                               FROM user  
                               WHERE id > 0 AND active = :typeValue
                               ORDER BY id DESC");
        $stmt->bindParam(':typeValue', $typeValue, PDO::PARAM_INT);
        break;
        
    default:
        $stmt = $pdo->prepare("SELECT userid, name, email, mobile, sponserid, sponsername, joining_date, status, active, kyc, amount, net_balance, active_investment, total_withdrawal, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, rank_reward_wallet, vip_club_wallet, user_growth_wallet, company_turnover_wallet FROM user ORDER BY id DESC");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
exit;
