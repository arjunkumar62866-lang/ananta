<?php
require_once 'common/connection.php';
require_once 'common/db_method.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? 'all';
$rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;

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
        $stmt = $pdo->prepare("
            SELECT u.userid, u.mobile, u.name, u.total_package, u.sponserid, u.sponsername, u.upgrade_date, u.joining_date, u.status, u.active,
                   COALESCE(inv.total_real_usd, 0) as active_roi_usd,
                   COALESCE(inv.total_roi_inr, 0) as active_roi_inr
            FROM user u
            LEFT JOIN (
                SELECT user_id, 
                       SUM(real_fund_usd) as total_real_usd,
                       SUM(package) as total_roi_inr
                FROM tbl_roi_one
                WHERE status = '0' AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
                GROUP BY user_id
            ) inv ON inv.user_id = u.userid
            WHERE u.active = '1' 
            ORDER BY u.id DESC
        ");
        break;

    case 'inactive_user':
        $stmt = $pdo->prepare("
            SELECT u.userid, u.mobile, u.name, u.total_package, u.sponserid, u.sponsername, u.upgrade_date, u.joining_date, u.status, u.active,
                   COALESCE(inv.total_real_usd, 0) as active_roi_usd,
                   COALESCE(inv.total_roi_inr, 0) as active_roi_inr
            FROM user u
            LEFT JOIN (
                SELECT user_id, 
                       SUM(real_fund_usd) as total_real_usd,
                       SUM(package) as total_roi_inr
                FROM tbl_roi_one
                WHERE status = '0' AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
                GROUP BY user_id
            ) inv ON inv.user_id = u.userid
            WHERE u.active = '0' 
            ORDER BY u.id DESC
        ");
        break;
        
    case 'deactive_user':
        $stmt = $pdo->prepare("SELECT userid,name,email,mobile,sponserid,sponsername,joining_date,status FROM user WHERE status='2' ORDER BY id DESC");
        break;
        
    case 'pending_user':
        $stmt = $pdo->prepare("SELECT userid,name,email,mobile,sponserid,sponsername,joining_date,status FROM user WHERE active ='0' and status!='2' ORDER BY id DESC");
        break;
        
    case 'pending_kyc':
    case 'completed_kyc':
        $filter   = trim($_GET['filter'] ?? '');
        $fromDate = trim($_GET['from_date'] ?? '');
        $toDate   = trim($_GET['to_date'] ?? '');

        $sql = "SELECT 
                    k.userid, 
                    COALESCE(NULLIF(TRIM(k.holder_name), ''), u.name, 'N/A') AS holder_name,
                    COALESCE(u.name, 'N/A') AS user_name,
                    COALESCE(k.bank, 'N/A') AS bank,
                    COALESCE(k.ac_number, 'N/A') AS ac_number,
                    COALESCE(k.ifsc, 'N/A') AS ifsc,
                    COALESCE(k.pan, 'N/A') AS pan,
                    COALESCE(k.mimo, 'N/A') AS aadhar_number,
                    COALESCE(k.bhim, 'N/A') AS upi_id,
                    COALESCE(u.bep20_address, '') AS bep20_address,
                    k.status,
                    DATE_FORMAT(COALESCE(k.updated_at, u.joining_date), '%Y-%m-%d %H:%i') AS updated_date
                FROM kyc k 
                LEFT JOIN user u ON (k.userid = u.userid OR k.userid = u.id) 
                WHERE (k.status = '1' OR (TRIM(COALESCE(k.holder_name, '')) != '' OR TRIM(COALESCE(k.ac_number, '')) != '' OR TRIM(COALESCE(k.pan, '')) != '' OR TRIM(COALESCE(k.mimo, '')) != '')) ";

        $params = [];
        if ($filter === 'today') {
            $sql .= " AND DATE(COALESCE(k.updated_at, u.joining_date)) = CURDATE() ";
        } elseif (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND DATE(COALESCE(k.updated_at, u.joining_date)) BETWEEN :from_date AND :to_date ";
            $params[':from_date'] = $fromDate;
            $params[':to_date']   = $toDate;
        } elseif (!empty($fromDate)) {
            $sql .= " AND DATE(COALESCE(k.updated_at, u.joining_date)) >= :from_date ";
            $params[':from_date'] = $fromDate;
        } elseif (!empty($toDate)) {
            $sql .= " AND DATE(COALESCE(k.updated_at, u.joining_date)) <= :to_date ";
            $params[':to_date']   = $toDate;
        }

        $sql .= " ORDER BY COALESCE(k.updated_at, u.joining_date) DESC, k.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        exit();

        
    case 'active':
        $typeValue = ($_GET['type'] == "active") ? 1 : 0;
        $stmt = $pdo->prepare("SELECT userid, name, sponserid, sponsername, package, upgrade_date2, active 
                               FROM user  
                               WHERE id > 0 AND active = :typeValue
                               ORDER BY id DESC");
        $stmt->bindParam(':typeValue', $typeValue, PDO::PARAM_INT);
        break;
        
    default:
        $stmt = $pdo->prepare("
            SELECT u.userid, u.name, u.email, u.mobile, u.sponserid, u.sponsername, u.joining_date, u.status, u.active, u.kyc, 
                   u.amount, u.net_balance, u.active_investment, u.total_withdrawal, u.total_package,
                   u.profit_income_wallet, u.profit_sharing_wallet, u.direct_bonus_wallet, u.mentor_income_wallet, 
                   u.rank_reward_wallet, u.vip_club_wallet, u.user_growth_wallet, u.company_turnover_wallet,
                   COALESCE(inv.total_real_usd, 0) as active_roi_usd,
                   COALESCE(inv.total_roi_inr, 0) as active_roi_inr,
                   COALESCE(wd.total_wd_usd, 0) as computed_total_wd
            FROM user u
            LEFT JOIN (
                SELECT user_id, 
                       SUM(real_fund_usd) as total_real_usd,
                       SUM(package) as total_roi_inr
                FROM tbl_roi_one
                WHERE status = '0' AND (capital_withdrawal_status IS NULL OR capital_withdrawal_status != 'WITHDRAWN')
                GROUP BY user_id
            ) inv ON inv.user_id = u.userid
            LEFT JOIN (
                SELECT user_id,
                       SUM(amount) as total_wd_usd
                FROM tbl_transaction
                WHERE (subject LIKE '%Withdraw%' OR type = 'Withdrawal Request') AND status != 2
                GROUP BY user_id
            ) wd ON wd.user_id = u.userid
            ORDER BY u.id DESC
        ");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Normalization layer: Ensure all output monetary values represent base USD to prevent duplicate currency conversion
foreach ($users as &$u) {
    if (isset($u['total_package']) || isset($u['active_roi_usd'])) {
        $invUsd = (float)($u['active_roi_usd'] ?? 0);
        if ($invUsd <= 0 && (float)($u['active_roi_inr'] ?? 0) > 0) {
            $invUsd = round((float)$u['active_roi_inr'] / $rate, 2);
        }
        if ($invUsd <= 0 && (float)($u['active_investment'] ?? 0) > 0) {
            $invUsd = (float)$u['active_investment'];
        }
        if ($invUsd <= 0 && !empty($u['total_package']) && (float)$u['total_package'] > 0) {
            $invUsd = round((float)$u['total_package'] / $rate, 2);
        }

        $u['total_package_usd'] = round($invUsd, 2);
        $u['total_package_inr'] = round($invUsd * $rate, 2);
        // Normalize total_package to base USD so formatAdminCurrency(amountInUSD) works seamlessly
        $u['total_package']     = round($invUsd, 2);
        $u['active_investment'] = round($invUsd, 2);
    }

    if (isset($u['total_withdrawal']) || isset($u['computed_total_wd'])) {
        $wdUsd = (float)($u['total_withdrawal'] ?? 0);
        if ($wdUsd <= 0) {
            $wdUsd = (float)($u['computed_total_wd'] ?? 0);
        }
        $u['total_withdrawal'] = round($wdUsd, 2);
    }
}
unset($u);

echo json_encode($users);
exit;
