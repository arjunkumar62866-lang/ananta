<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!headers_sent()) {
    header('Content-Type: application/json');
}

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

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

// Currency Handling
$currency = !empty($_REQUEST['currency']) ? strtoupper(trim($_REQUEST['currency'])) : (function_exists('getUserCurrency') ? getUserCurrency($userid, $pdo) : ($_SESSION['currency'] ?? 'USD'));
$currSymbol = function_exists('getCurrencySymbol') ? getCurrencySymbol($currency) : ($currency === 'INR' ? '₹' : '$');
$usdToInrRate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;

// User Wallet & Details
$stmtUser = $pdo->prepare("SELECT userid, name, active, vip_club_wallet FROM user WHERE userid = :uid LIMIT 1");
$stmtUser->execute([':uid' => $userid]);
$userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

$vipWalletUSD = (float)($userInfo['vip_club_wallet'] ?? 0);
$vipWalletDisp = function_exists('convertCurrency') ? convertCurrency($vipWalletUSD, $currency, $pdo) : ($currency === 'INR' ? round($vipWalletUSD * $usdToInrRate, 2) : $vipWalletUSD);
$vipWalletFmt  = function_exists('formatCurrency') ? formatCurrency($vipWalletUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($vipWalletDisp, 2));

// Qualification History
$stmtQuals = $pdo->prepare("SELECT * FROM tbl_vip_user_qualification WHERE user_id = :uid ORDER BY vip_level ASC");
$stmtQuals->execute([':uid' => $userid]);
$quals = $stmtQuals->fetchAll(PDO::FETCH_ASSOC);

// Monthly Payout History
$stmtSchedules = $pdo->prepare("SELECT * FROM tbl_vip_monthly_schedule WHERE user_id = :uid ORDER BY id DESC");
$stmtSchedules->execute([':uid' => $userid]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);

// Highest level & Total rewards earned
$highestLevel = 0;
$totalRewardsEarnedUSD = 0.0;
foreach ($quals as &$q) {
    if ((int)$q['vip_level'] > $highestLevel) {
        $highestLevel = (int)$q['vip_level'];
    }
    $rwAmtUSD = (float)($q['reward_amount'] ?? 0);
    $totalRewardsEarnedUSD += $rwAmtUSD;
    $wkBizUSD = (float)($q['weaker_leg_business'] ?? 0);

    $q['display_reward_amount']     = function_exists('convertCurrency') ? convertCurrency($rwAmtUSD, $currency, $pdo) : ($currency === 'INR' ? round($rwAmtUSD * $usdToInrRate, 2) : $rwAmtUSD);
    $q['formatted_reward_amount']   = function_exists('formatCurrency') ? formatCurrency($rwAmtUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($q['display_reward_amount'], 2));
    $q['display_weaker_leg_business']   = function_exists('convertCurrency') ? convertCurrency($wkBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($wkBizUSD * $usdToInrRate, 2) : $wkBizUSD);
    $q['formatted_weaker_leg_business'] = function_exists('formatCurrency') ? formatCurrency($wkBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($q['display_weaker_leg_business'], 2));
}
unset($q);

$totalRewardsEarnedDisp = function_exists('convertCurrency') ? convertCurrency($totalRewardsEarnedUSD, $currency, $pdo) : ($currency === 'INR' ? round($totalRewardsEarnedUSD * $usdToInrRate, 2) : $totalRewardsEarnedUSD);
$totalRewardsEarnedFmt  = function_exists('formatCurrency') ? formatCurrency($totalRewardsEarnedUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($totalRewardsEarnedDisp, 2));

// Process Monthly Schedules
foreach ($schedules as &$m) {
    $wkBizUSD   = (float)($m['weaker_leg_business'] ?? 0);
    $wkPayUSD   = (float)($m['weaker_leg_payout'] ?? 0);
    $toPayUSD   = (float)($m['turnover_payout'] ?? 0);
    $totPayUSD  = (float)($m['total_payout'] ?? 0);

    $m['display_weaker_leg_business']   = function_exists('convertCurrency') ? convertCurrency($wkBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($wkBizUSD * $usdToInrRate, 2) : $wkBizUSD);
    $m['formatted_weaker_leg_business'] = function_exists('formatCurrency') ? formatCurrency($wkBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($m['display_weaker_leg_business'], 2));

    $m['display_weaker_leg_payout']   = function_exists('convertCurrency') ? convertCurrency($wkPayUSD, $currency, $pdo) : ($currency === 'INR' ? round($wkPayUSD * $usdToInrRate, 2) : $wkPayUSD);
    $m['formatted_weaker_leg_payout'] = function_exists('formatCurrency') ? formatCurrency($wkPayUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($m['display_weaker_leg_payout'], 2));

    $m['display_turnover_payout']   = function_exists('convertCurrency') ? convertCurrency($toPayUSD, $currency, $pdo) : ($currency === 'INR' ? round($toPayUSD * $usdToInrRate, 2) : $toPayUSD);
    $m['formatted_turnover_payout'] = function_exists('formatCurrency') ? formatCurrency($toPayUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($m['display_turnover_payout'], 2));

    $m['display_total_payout']   = function_exists('convertCurrency') ? convertCurrency($totPayUSD, $currency, $pdo) : ($currency === 'INR' ? round($totPayUSD * $usdToInrRate, 2) : $totPayUSD);
    $m['formatted_total_payout'] = function_exists('formatCurrency') ? formatCurrency($totPayUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($m['display_total_payout'], 2));
}
unset($m);

// Leg Details Formatting
$legDetails = $qualRes['leg_details'] ?? [];
$leftBizUSD   = (float)($legDetails['left_business_usd'] ?? 0);
$rightBizUSD  = (float)($legDetails['right_business_usd'] ?? 0);
$weakerBizUSD = (float)($legDetails['weaker_leg_business_usd'] ?? 0);

$legDetails['display_left_business']   = function_exists('convertCurrency') ? convertCurrency($leftBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($leftBizUSD * $usdToInrRate, 2) : $leftBizUSD);
$legDetails['formatted_left_business'] = function_exists('formatCurrency') ? formatCurrency($leftBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($legDetails['display_left_business'], 2));

$legDetails['display_right_business']   = function_exists('convertCurrency') ? convertCurrency($rightBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($rightBizUSD * $usdToInrRate, 2) : $rightBizUSD);
$legDetails['formatted_right_business'] = function_exists('formatCurrency') ? formatCurrency($rightBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($legDetails['display_right_business'], 2));

$legDetails['display_weaker_leg_business']   = function_exists('convertCurrency') ? convertCurrency($weakerBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($weakerBizUSD * $usdToInrRate, 2) : $weakerBizUSD);
$legDetails['formatted_weaker_leg_business'] = function_exists('formatCurrency') ? formatCurrency($weakerBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($legDetails['display_weaker_leg_business'], 2));

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
foreach ($allConfigs as &$c) {
    $reqLeftUSD   = (float)($c['req_left_business'] ?? 0);
    $reqRightUSD  = (float)($c['req_right_business'] ?? 0);
    $rwAmtUSD     = (float)($c['reward_amount'] ?? 0);
    $rptBizUSD    = (float)($c['monthly_repeat_business'] ?? 0);

    $c['display_req_left_business']    = function_exists('convertCurrency') ? convertCurrency($reqLeftUSD, $currency, $pdo) : ($currency === 'INR' ? round($reqLeftUSD * $usdToInrRate, 2) : $reqLeftUSD);
    $c['formatted_req_left_business']  = function_exists('formatCurrency') ? formatCurrency($reqLeftUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($c['display_req_left_business'], 2));

    $c['display_req_right_business']   = function_exists('convertCurrency') ? convertCurrency($reqRightUSD, $currency, $pdo) : ($currency === 'INR' ? round($reqRightUSD * $usdToInrRate, 2) : $reqRightUSD);
    $c['formatted_req_right_business'] = function_exists('formatCurrency') ? formatCurrency($reqRightUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($c['display_req_right_business'], 2));

    $c['display_reward_amount']        = function_exists('convertCurrency') ? convertCurrency($rwAmtUSD, $currency, $pdo) : ($currency === 'INR' ? round($rwAmtUSD * $usdToInrRate, 2) : $rwAmtUSD);
    $c['formatted_reward_amount']      = function_exists('formatCurrency') ? formatCurrency($rwAmtUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($c['display_reward_amount'], 2));

    $c['display_monthly_repeat_business']   = function_exists('convertCurrency') ? convertCurrency($rptBizUSD, $currency, $pdo) : ($currency === 'INR' ? round($rptBizUSD * $usdToInrRate, 2) : $rptBizUSD);
    $c['formatted_monthly_repeat_business'] = function_exists('formatCurrency') ? formatCurrency($rptBizUSD, $currency, true, $pdo) : ($currSymbol . ' ' . number_format($c['display_monthly_repeat_business'], 2));
}
unset($c);

echo json_encode([
    'status' => 'success',
    'currency' => [
        'code'   => $currency,
        'symbol' => $currSymbol,
        'rate'   => $usdToInrRate
    ],
    'data' => [
        'user' => [
            'userid'                  => $userInfo['userid'],
            'name'                    => $userInfo['name'],
            'vip_club_wallet'         => $vipWalletUSD,
            'display_vip_club_wallet' => $vipWalletDisp,
            'formatted_vip_club_wallet' => $vipWalletFmt,
            'highest_level'           => $highestLevel
        ],
        'leg_details'                => $legDetails,
        'total_rewards_earned'       => round($totalRewardsEarnedUSD, 2),
        'display_total_rewards_earned' => $totalRewardsEarnedDisp,
        'formatted_total_rewards_earned' => $totalRewardsEarnedFmt,
        'next_level'                 => $nextLevelConfig,
        'qualifications'             => $quals,
        'monthly_payouts'            => $schedules,
        'configs'                    => $allConfigs
    ]
]);
