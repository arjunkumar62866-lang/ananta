<?php
// PROTECTED AUDIT SCRIPT FOR ACTIVE MEMBERS MISMATCH
$isCli = (php_sapi_name() === "cli");
$key = $_GET["key"] ?? (isset($argv[1]) ? $argv[1] : "");
if (strpos($key, "key=") === 0) {
    $key = substr($key, 4);
}
if (!$isCli && $key !== "aX9zK7mQ") {
    http_response_code(403);
    die("Forbidden
");
}

header('Content-Type: text/plain; charset=utf-8');
@ini_set('max_execution_time', 180);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

global $pdo;

echo "====================================================
";
echo "       ACTIVE TEAM MISMATCH DEEP AUDIT
";
echo "====================================================

";

$targetUsers = ['789260', 'AN789260', '540599', '1290'];

foreach ($targetUsers as $testUid) {
    echo ">>> TESTING USER: {$testUid} <<<
";
    
    // 1. Fetch Left and Right Branch Members via getUserTeamMembersDetailed
    $leftM  = getUserTeamMembersDetailed($testUid, 'LEFT', $pdo);
    $rightM = getUserTeamMembersDetailed($testUid, 'RIGHT', $pdo);
    
    $allBranchMembers = array_merge($leftM, $rightM);
    $branchUserIds = array_unique(array_column($allBranchMembers, 'userid'));
    
    echo "Branch Members Total: Left=" . count($leftM) . ", Right=" . count($rightM) . ", Total=" . count($allBranchMembers) . ", Unique IDs=" . count($branchUserIds) . "
";
    
    $actLeft = count(array_filter($leftM, function($m) { return ($m['status'] ?? '') === 'Active'; }));
    $actRight = count(array_filter($rightM, function($m) { return ($m['status'] ?? '') === 'Active'; }));
    echo "Current Business Overview Active: Left={$actLeft}, Right={$actRight}, Total=" . ($actLeft + $actRight) . "
";
    
    // Analyze the branch members against user table and activation data
    if (!empty($branchUserIds)) {
        $inClause = implode(',', array_fill(0, count($branchUserIds), '?'));
        
        // user table active flag
        $stmtU = $pdo->prepare("SELECT userid, name, active, status, total_package, package, amount FROM user WHERE userid IN ($inClause)");
        $stmtU->execute(array_values($branchUserIds));
        $uRows = $stmtU->fetchAll(PDO::FETCH_ASSOC);
        
        $userActiveCount = 0;
        $userStatusActiveCount = 0;
        $userPackageCount = 0;
        $activeUserIdsFromUserTable = [];
        
        foreach ($uRows as $ur) {
            $isAct = ((string)$ur['active'] === '1' || (int)$ur['active'] === 1);
            $isStat = (strtolower((string)$ur['status']) === 'active');
            $hasPkg = ((float)$ur['total_package'] > 0 || (float)$ur['package'] > 0 || (float)$ur['amount'] > 0);
            
            if ($isAct) {
                $userActiveCount++;
                $activeUserIdsFromUserTable[] = $ur['userid'];
            }
            if ($isStat) $userStatusActiveCount++;
            if ($hasPkg) $userPackageCount++;
        }
        
        echo "In Branch ({$testUid}):
";
        echo "  - user.active = '1' count: {$userActiveCount}
";
        echo "  - user.status = 'Active' count: {$userStatusActiveCount}
";
        echo "  - user table has package/amount > 0: {$userPackageCount}
";
        
        // tbl_roi_one distinct users
        $stmtRoi = $pdo->prepare("SELECT DISTINCT user_id FROM tbl_roi_one WHERE user_id IN ($inClause)");
        $stmtRoi->execute(array_values($branchUserIds));
        $roiUserIds = $stmtRoi->fetchAll(PDO::FETCH_COLUMN);
        echo "  - tbl_roi_one distinct users: " . count($roiUserIds) . "
";
        
        // tbl_account_activation
        try {
            $stmtAct = $pdo->prepare("SELECT DISTINCT target_user_id FROM tbl_account_activation WHERE target_user_id IN ($inClause)");
            $stmtAct->execute(array_values($branchUserIds));
            $actIds = $stmtAct->fetchAll(PDO::FETCH_COLUMN);
            echo "  - tbl_account_activation distinct targets: " . count($actIds) . "
";
        } catch (Throwable $e) {
            echo "  - tbl_account_activation error: " . $e->getMessage() . "
";
        }
        
        // tbl_transaction unlock/activation debits
        try {
            $stmtTxn = $pdo->prepare("
                SELECT DISTINCT user_id FROM tbl_transaction 
                WHERE user_id IN ($inClause) AND (subject LIKE '%Unlock Access%' OR subject LIKE '%Activation%')
            ");
            $stmtTxn->execute(array_values($branchUserIds));
            $txnIds = $stmtTxn->fetchAll(PDO::FETCH_COLUMN);
            echo "  - tbl_transaction Unlock/Activation distinct users: " . count($txnIds) . "
";
        } catch (Throwable $e) {
            echo "  - tbl_transaction error: " . $e->getMessage() . "
";
        }
        
        echo "  Active IDs in user table: " . implode(', ', array_slice($activeUserIdsFromUserTable, 0, 30)) . "
";
        if (count($activeUserIdsFromUserTable) > 30) {
            echo "    ... and " . (count($activeUserIdsFromUserTable) - 30) . " more
";
        }
    }
    
    // 2. Fetch Fund Statement Data via getUserFundStatementData
    $fundData = getUserFundStatementData($testUid, null, null, $pdo);
    $invs = $fundData['investments'] ?? [];
    $debits = $fundData['unlock_debits'] ?? [];
    
    $invUsers = array_unique(array_column($invs, 'user_id'));
    $debUsers = array_unique(array_column($debits, 'user_id'));
    
    echo "Fund Statement for {$testUid}:
";
    echo "  - Total Investment records: " . count($invs) . " across " . count($invUsers) . " unique users
";
    echo "  - Total Unlock/Activation debits: " . count($debits) . " across " . count($debUsers) . " unique users
";
    echo "  - Unique users in investments: " . implode(', ', $invUsers) . "
";
    echo "  - Unique users in unlock debits (" . count($debUsers) . "): " . implode(', ', array_slice($debUsers, 0, 20)) . "
";
    
    // 3. Network Downline IDs via getUserNetworkDownlineIds
    $networkIds = getUserNetworkDownlineIds($testUid, $pdo);
    echo "getUserNetworkDownlineIds for {$testUid}: " . count($networkIds) . " total IDs
";
    if (!empty($networkIds)) {
        $inNet = implode(',', array_fill(0, count($networkIds), '?'));
        $stmtNetAct = $pdo->prepare("SELECT COUNT(*) FROM user WHERE userid IN ($inNet) AND (active = '1' OR active = 1)");
        $stmtNetAct->execute(array_values($networkIds));
        $netActiveCount = $stmtNetAct->fetchColumn();
        echo "  - user.active = '1' inside networkIds: {$netActiveCount}
";
    }
    
    echo "----------------------------------------------------

";
}

echo "=== GLOBAL STATS ===
";
$totalUsers = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalActiveUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE active = '1' OR active = 1")->fetchColumn();
$totalStatusActive = $pdo->query("SELECT COUNT(*) FROM user WHERE LOWER(status) = 'active'")->fetchColumn();
$totalRoiUsers = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM tbl_roi_one")->fetchColumn();

echo "Total Users in System: {$totalUsers}
";
echo "Total Users with active='1': {$totalActiveUsers}
";
echo "Total Users with status='Active': {$totalStatusActive}
";
echo "Total Users in tbl_roi_one: {$totalRoiUsers}
";

try {
    $totalUnlockDebits = $pdo->query("
        SELECT COUNT(DISTINCT user_id) FROM tbl_transaction 
        WHERE (subject LIKE '%Unlock Access%' OR subject LIKE '%Activation%')
    ")->fetchColumn();
    echo "Total Users with Unlock/Activation in tbl_transaction: {$totalUnlockDebits}
";
} catch (Throwable $e) {}

try {
    $totalAccAct = $pdo->query("SELECT COUNT(DISTINCT target_user_id) FROM tbl_account_activation")->fetchColumn();
    echo "Total target users in tbl_account_activation: {$totalAccAct}
";
} catch (Throwable $e) {}

echo "
=== SAMPLES OF ACTIVE USERS (user.active = 1 vs tbl_roi_one) ===
";
$stmtSample = $pdo->query("
    SELECT u.userid, u.name, u.sponserid, u.underuserid, u.join_side, u.active, u.status, u.package, u.total_package,
           (SELECT COALESCE(SUM(r.package), 0) FROM tbl_roi_one r WHERE r.user_id = u.userid) as roi_pkg,
           (SELECT COALESCE(SUM(r.real_fund_usd), 0) FROM tbl_roi_one r WHERE r.user_id = u.userid) as roi_usd
    FROM user u
    WHERE u.active = '1' OR u.active = 1
    LIMIT 25
");
$samples = $stmtSample->fetchAll(PDO::FETCH_ASSOC);
foreach ($samples as $s) {
    echo "User: {$s['userid']} | Name: {$s['name']} | Sp: {$s['sponserid']} | Under: {$s['underuserid']} | Side: {$s['join_side']} | act: {$s['active']} | pkg: {$s['package']} | roi_usd: {$s['roi_usd']}
";
}

echo "
=== END OF AUDIT ===
";