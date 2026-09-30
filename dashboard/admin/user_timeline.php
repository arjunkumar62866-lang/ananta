<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$uid = trim($_GET['uid'] ?? 'AN1290');
$cleanUid = preg_replace('/^(AN|ANANTA)/i', '', $uid);

$stmtUser = $pdo->prepare("SELECT * FROM user WHERE userid = :uid OR userid = :clean LIMIT 1");
$stmtUser->execute([':uid' => $uid, ':clean' => $cleanUid]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $user = [
        'userid' => $uid,
        'name' => 'User Account',
        'email' => 'N/A',
        'mobile' => 'N/A',
        'joining_date' => date('Y-m-d'),
        'active' => '0',
        'status' => '1'
    ];
}

$target_user = $user['userid'];

// Fetch all unified timeline events (Transactions, Audits, Activations, Investments, KYC)
$events = [];
$rate = function_exists('getUSDToINRRate') ? getUSDToINRRate($pdo) : 90.0;

// 1. Investments & Package Purchases (tbl_roi_one)
try {
    $stmtInv = $pdo->prepare("
        SELECT id, package_code, real_fund_usd, package, date, time, status, capital_withdrawal_status
        FROM tbl_roi_one 
        WHERE user_id = :uid OR user_id = :clean
        ORDER BY id DESC LIMIT 50
    ");
    $stmtInv->execute([':uid' => $target_user, ':clean' => $cleanUid]);
    foreach ($stmtInv->fetchAll(PDO::FETCH_ASSOC) as $inv) {
        $usd = (float)$inv['real_fund_usd'];
        $inr = (float)$inv['package'];
        if ($usd <= 0 && $inr > 0) $usd = round($inr / $rate, 2);
        if ($inr <= 0 && $usd > 0) $inr = round($usd * $rate, 2);

        $statusText = ($inv['capital_withdrawal_status'] === 'WITHDRAWN' || $inv['status'] == '1') ? 'COMPLETED / WITHDRAWN' : 'ACTIVE';
        $badgeClass = ($statusText === 'ACTIVE') ? 'badge-success' : 'badge-secondary';

        $events[] = [
            'type'  => 'INVESTMENT',
            'title' => "Package Investment: " . ($inv['package_code'] ?: 'Investment') . " (\$" . number_format($usd, 2) . " / ₹" . number_format($inr, 2) . ")",
            'desc'  => "Capital Status: {$statusText} | Package: " . ($inv['package_code'] ?: 'ANANTA_PACKAGE'),
            'time'  => ($inv['date'] ?: date('Y-m-d')) . ' ' . ($inv['time'] ?: '00:00:00'),
            'icon'  => 'fa-line-chart',
            'badge' => $badgeClass
        ];
    }
} catch (Exception $eInv) {}

// 2. Account Activations & Renewals (tbl_account_activation)
try {
    $stmtAct = $pdo->prepare("
        SELECT transaction_id, activation_type, amount_usd, amount_inr, activator_user_id, target_user_id, status, created_at
        FROM tbl_account_activation
        WHERE target_user_id = :uid OR target_user_id = :clean OR activator_user_id = :uid OR activator_user_id = :clean
        ORDER BY id DESC LIMIT 50
    ");
    $stmtAct->execute([':uid' => $target_user, ':clean' => $cleanUid]);
    foreach ($stmtAct->fetchAll(PDO::FETCH_ASSOC) as $act) {
        $usd = (float)$act['amount_usd'];
        $inr = (float)$act['amount_inr'];
        if ($inr <= 0 && $usd > 0) $inr = round($usd * $rate, 2);
        if ($usd <= 0 && $inr > 0) $usd = round($inr / $rate, 2);

        $isTarget = ($act['target_user_id'] === $target_user || $act['target_user_id'] === $cleanUid);
        $actRole = $isTarget ? "Self Account Activated" : "Activated Member {$act['target_user_id']}";

        $events[] = [
            'type'  => 'ACTIVATION',
            'title' => "Unlock Access ({$act['activation_type']}): \$" . number_format($usd, 2) . " (₹" . number_format($inr, 2) . ")",
            'desc'  => "{$actRole} | Txn: {$act['transaction_id']} | Status: {$act['status']}",
            'time'  => $act['created_at'],
            'icon'  => 'fa-shield',
            'badge' => 'badge-info'
        ];
    }
} catch (Exception $eAct) {}

// 3. Transactions (tbl_transaction)
$stmtTxn = $pdo->prepare("SELECT id, amount, act_amount, subject, type, status, created_date, time FROM tbl_transaction WHERE user_id = :uid ORDER BY id DESC LIMIT 100");
$stmtTxn->execute([':uid' => $target_user]);
foreach ($stmtTxn->fetchAll(PDO::FETCH_ASSOC) as $t) {
    $rawAmt = (float)$t['amount'];
    // Determine currency representation cleanly
    $isUnlockFee = (stripos($t['subject'], 'Unlock Access') !== false || stripos($t['subject'], '$11') !== false);
    if ($isUnlockFee) {
        $inrVal = $rawAmt;
        $usdVal = round($inrVal / $rate, 2);
    } elseif ($rawAmt > 500 && (stripos($t['subject'], 'P2P') !== false || stripos($t['subject'], 'Yield') !== false || stripos($t['subject'], 'Sharing') !== false)) {
        // Legacy INR transactions
        $inrVal = $rawAmt;
        $usdVal = round($inrVal / $rate, 2);
    } else {
        // Base USD transactions
        $usdVal = $rawAmt;
        $inrVal = round($rawAmt * $rate, 2);
    }

    $events[] = [
        'type' => 'TRANSACTION',
        'title' => "Transaction: {$t['type']} (\$" . number_format($usdVal, 2) . " / ₹" . number_format($inrVal, 2) . ")",
        'desc' => $t['subject'],
        'time' => $t['created_date'] . ' ' . $t['time'],
        'icon' => 'fa-exchange',
        'badge' => ($t['type'] === 'Credit' ? 'badge-success' : 'badge-danger')
    ];
}

// 4. Admin Audits (tbl_admin_audit_log)
$stmtAudit = $pdo->prepare("SELECT id, action, amount, wallet_type, reason, created_at FROM tbl_admin_audit_log WHERE target_user_id = :uid ORDER BY id DESC LIMIT 100");
$stmtAudit->execute([':uid' => $target_user]);
foreach ($stmtAudit->fetchAll(PDO::FETCH_ASSOC) as $a) {
    $adjAmt = (float)($a['amount'] ?? 0);
    $amtLabel = ($adjAmt > 0) ? " (\$" . number_format($adjAmt, 2) . " / ₹" . number_format($adjAmt * $rate, 2) . ")" : "";

    $events[] = [
        'type' => 'ADMIN_AUDIT',
        'title' => "Admin Action: {$a['action']} (" . ($a['wallet_type'] ?? 'Account') . "){$amtLabel}",
        'desc' => $a['reason'],
        'time' => $a['created_at'],
        'icon' => 'fa-shield',
        'badge' => 'badge-primary'
    ];
}

// 5. KYC Status
try {
    $stmtKyc = $pdo->prepare("SELECT * FROM kyc WHERE userid = :uid ORDER BY id DESC LIMIT 10");
    $stmtKyc->execute([':uid' => $target_user]);
    foreach ($stmtKyc->fetchAll(PDO::FETCH_ASSOC) as $k) {
        $stVal = $k['status'] ?? '0';
        $stText = ($stVal == '1') ? 'PENDING' : (($stVal == '2') ? 'APPROVED' : ($stVal == '3' ? 'REJECTED' : 'NOT SUBMITTED'));
        $events[] = [
            'type' => 'KYC',
            'title' => "KYC Application Status: {$stText}",
            'desc' => "KYC verification record status: {$stText}",
            'time' => $k['date'] ?? $k['created_at'] ?? $k['updated_at'] ?? date('Y-m-d H:i:s'),
            'icon' => 'fa-id-card',
            'badge' => ($stVal == '2' ? 'badge-success' : ($stVal == '3' ? 'badge-danger' : 'badge-warning'))
        ];
    }
} catch (Exception $eKyc) {
    // Ignore if kyc table schema lacks optional timestamp fields
}

// Sort all timeline events descending by time
usort($events, function($a, $b) {
    return strtotime($b['time']) <=> strtotime($a['time']);
});
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Card -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
      <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">USER ACTIVITY HISTORY</span>
          <h3 class="mb-1 text-white font-weight-bold">Complete User Activity Timeline</h3>
          <p class="mb-0 text-white-50 small">Historical log of transactions, balance adjustments, KYC submissions, and account status changes for <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($target_user); ?>).</p>
        </div>
        <div>
          <a href="user_profile.php?uid=<?php echo urlencode($target_user); ?>" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-arrow-left mr-1"></i> Back to User Profile
          </a>
        </div>
      </div>
    </div>

    <!-- User Information Summary Bar -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;">
      <div class="card-body p-3 d-flex align-items-center justify-content-around flex-wrap gap-2 text-center">
        <div>
          <span class="text-muted small d-block">User ID</span>
          <strong class="text-dark"><?php echo htmlspecialchars($target_user); ?></strong>
        </div>
        <div>
          <span class="text-muted small d-block">Member Name</span>
          <strong class="text-dark"><?php echo htmlspecialchars($user['name']); ?></strong>
        </div>
        <div>
          <span class="text-muted small d-block">Account Status</span>
          <span class="badge <?php echo $user['active']=='1'?'badge-success':'badge-secondary'; ?> px-2 py-1"><?php echo $user['active']=='1'?'ACTIVE':'INACTIVE'; ?></span>
        </div>
        <div>
          <span class="text-muted small d-block">Joining Date</span>
          <strong class="text-dark"><?php echo htmlspecialchars($user['joining_date']); ?></strong>
        </div>
      </div>
    </div>

    <!-- Timeline Event Stream -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-clock-o text-primary mr-2"></i> Chronological Activity Stream</h5>
      </div>
      <div class="card-body p-4">
        <?php if (empty($events)): ?>
          <div class="text-center py-5 text-muted">
            <i class="fa fa-folder-open-o fa-3x mb-3 text-secondary"></i>
            <h5>No activity events recorded for this account.</h5>
          </div>
        <?php else: ?>
          <ul class="timeline-list pl-3" style="list-style:none; border-left: 2px solid #e2e8f0; margin-left: 15px;">
            <?php foreach ($events as $ev): ?>
              <li class="mb-4 position-relative pl-4">
                <div class="position-absolute" style="left: -11px; top: 0; width: 20px; height: 20px; border-radius: 50%; background: #0284c7; border: 3px solid #ffffff;"></div>
                <div class="p-3 border rounded-lg" style="background: #f8fafc; border-color: #cbd5e1 !important; border-radius: 12px;">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fa <?php echo $ev['icon']; ?> mr-2 text-primary"></i> <?php echo htmlspecialchars($ev['title']); ?></h6>
                    <span class="badge <?php echo $ev['badge']; ?> px-2 py-1"><?php echo $ev['type']; ?></span>
                  </div>
                  <p class="mb-2 text-secondary small"><?php echo htmlspecialchars($ev['desc']); ?></p>
                  <small class="text-muted"><i class="fa fa-calendar mr-1"></i> <?php echo date('d-M-Y H:i:s', strtotime($ev['time'])); ?></small>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
</div>
<?php include 'common/footer.php'; ?>
</body>
</html>
