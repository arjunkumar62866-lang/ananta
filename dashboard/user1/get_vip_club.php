<?php
session_start();
header('Content-Type: application/json');

require_once '../../config/config.php';
require_once '../admin/common/db_method.php';

// Ensure user is logged in using project-standard session variable
if (!isset($_SESSION['userid'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. User session required.'
    ]);
    exit;
}

$userid = $_SESSION['userid'];

// Evaluate user's latest binary qualifications dynamically
$qualRes = evaluateUserVIPQualifications($userid, $pdo);

// User Wallet & Details
$stmtUser = $pdo->prepare("SELECT userid, name, active, vip_club_wallet FROM user WHERE userid = :uid LIMIT 1");
$stmtUser->execute([':uid' => $userid]);
$userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Qualification History
$stmtQuals = $pdo->prepare("SELECT * FROM tbl_vip_user_qualification WHERE user_id = :uid ORDER BY vip_level ASC");
$stmtQuals->execute([':uid' => $userid]);
$quals = $stmtQuals->fetchAll(PDO::FETCH_ASSOC);

// Monthly Payout History
$stmtSchedules = $pdo->prepare("SELECT * FROM tbl_vip_monthly_schedule WHERE user_id = :uid ORDER BY id DESC");
$stmtSchedules->execute([':uid' => $userid]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);

// Highest level
$highestLevel = 0;
$totalRewardsEarned = 0.0;
foreach ($quals as $q) {
    if ((int)$q['vip_level'] > $highestLevel) {
        $highestLevel = (int)$q['vip_level'];
    }
    $totalRewardsEarned += (float)$q['reward_amount'];
}

// Next level config
$nextLevelConfig = null;
if ($highestLevel < 10) {
    $nextLvl = $highestLevel + 1;
    $stmtNext = $pdo->prepare("SELECT * FROM tbl_vip_level_config WHERE level_id = :lvl LIMIT 1");
    $stmtNext->execute([':lvl' => $nextLvl]);
    $nextLevelConfig = $stmtNext->fetch(PDO::FETCH_ASSOC);
}

// All VIP Level Configs
$allConfigs = $pdo->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'data' => [
        'user' => [
            'userid'          => $userInfo['userid'],
            'name'            => $userInfo['name'],
            'vip_club_wallet' => (float)$userInfo['vip_club_wallet'],
            'highest_level'   => $highestLevel
        ],
        'leg_details'          => $qualRes['leg_details'],
        'total_rewards_earned' => round($totalRewardsEarned, 2),
        'next_level'           => $nextLevelConfig,
        'qualifications'       => $quals,
        'monthly_payouts'      => $schedules,
        'configs'              => $allConfigs
    ]
]);
