<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$search_user = trim($_GET['user_id'] ?? 'AN1290');
$cleanUid = preg_replace('/^(AN|ANANTA)/i', '', $search_user);

$stmtUser = $pdo->prepare("SELECT * FROM user WHERE userid = :uid OR userid = :clean LIMIT 1");
$stmtUser->execute([':uid' => $search_user, ':clean' => $cleanUid]);
$targetUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$targetUser) {
    $targetUser = [
        'userid' => 'AN1290',
        'name' => 'Ananta Master',
        'sponserid' => 'SYSTEM',
        'sponsername' => 'System Root',
        'active' => '1',
        'joining_date' => date('Y-m-d')
    ];
}

$uid = $targetUser['userid'];

// Fetch Leg Details
$legStats = getBinaryLegDetails($uid, $pdo);

// Fetch Direct Referrals List
$stmtDirects = $pdo->prepare("
  SELECT u.userid, u.name, u.mobile, u.active, u.joining_date, COALESCE(SUM(r.package), 0) as total_inv
  FROM tbl_sponsor s
  INNER JOIN user u ON u.userid = s.referral_id
  LEFT JOIN tbl_roi_one r ON r.user_id = u.userid
  WHERE s.sponsor_id = :uid
  GROUP BY u.id, u.userid, u.name, u.mobile, u.active, u.joining_date
  ORDER BY u.id DESC
");
$stmtDirects->execute([':uid' => $uid]);
$directs = $stmtDirects->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Banner -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
      <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">REFERRAL & TEAM MANAGEMENT</span>
          <h3 class="mb-1 text-white font-weight-bold">Direct Referrals, Binary Legs & Team Business</h3>
          <p class="mb-0 text-white-50 small">Inspect sponsor relationships, direct referrals, left/right binary subtree counts, and team business volume.</p>
        </div>
      </div>
    </div>

    <!-- User Search Bar -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0;">
      <div class="card-body p-3">
        <form method="GET" class="row align-items-center">
          <div class="col-md-8 mb-2">
            <input type="text" name="user_id" value="<?php echo htmlspecialchars($search_user); ?>" class="form-control" placeholder="Enter User ID to Inspect Team (e.g. AN1290)" style="border-radius:10px;" required>
          </div>
          <div class="col-md-4 mb-2">
            <button type="submit" class="btn btn-primary font-weight-bold w-100" style="border-radius:10px; background:#0284c7; border:none;">
              <i class="fa fa-sitemap mr-1"></i> Inspect Team Structure
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Sponsor & User Summary Card -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0;">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-user-circle text-primary mr-2"></i> Account Sponsor & Team Summary for <?php echo htmlspecialchars($targetUser['name']); ?> (<?php echo htmlspecialchars($uid); ?>)</h5>
      </div>
      <div class="card-body p-4">
        <div class="row text-center">
          <div class="col-md-3 mb-3 border-right">
            <span class="text-muted small d-block">SPONSOR ID / NAME</span>
            <strong class="text-primary font-weight-bold"><?php echo htmlspecialchars($targetUser['sponserid'] ?? 'SYSTEM'); ?></strong>
            <div class="small text-muted"><?php echo htmlspecialchars($targetUser['sponsername'] ?? 'System Master'); ?></div>
          </div>
          <div class="col-md-3 mb-3 border-right">
            <span class="text-muted small d-block">TOTAL DIRECT REFERRALS</span>
            <h4 class="mb-0 font-weight-bold text-success"><?php echo count($directs); ?> Members</h4>
          </div>
          <div class="col-md-3 mb-3 border-right">
            <span class="text-muted small d-block">LEFT LEG (COUNT / BIZ)</span>
            <h5 class="mb-0 font-weight-bold text-info"><?php echo $legStats['left_ids_count']; ?> IDs / $<?php echo number_format($legStats['left_business_usd'], 2); ?></h5>
          </div>
          <div class="col-md-3 mb-3">
            <span class="text-muted small d-block">RIGHT LEG (COUNT / BIZ)</span>
            <h5 class="mb-0 font-weight-bold text-warning"><?php echo $legStats['right_ids_count']; ?> IDs / $<?php echo number_format($legStats['right_business_usd'], 2); ?></h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Direct Referrals Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-users text-success mr-2"></i> Direct Referrals List (<?php echo count($directs); ?>)</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">Direct User ID</th>
                <th class="py-3">Member Name</th>
                <th class="py-3">Mobile No</th>
                <th class="py-3">Activation Status ($11)</th>
                <th class="py-3">Total Investment (₹)</th>
                <th class="py-3 px-4">Joining Date</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($directs)): ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">No direct referrals recorded for this user.</td>
                </tr>
              <?php else: foreach ($directs as $d): ?>
                <tr>
                  <td class="px-4 font-weight-bold">
                    <a href="?user_id=<?php echo urlencode($d['userid']); ?>" class="text-primary">
                      <strong><?php echo htmlspecialchars($d['userid']); ?></strong> <i class="fa fa-external-link small ml-1"></i>
                    </a>
                  </td>
                  <td><?php echo htmlspecialchars($d['name']); ?></td>
                  <td><?php echo htmlspecialchars($d['mobile']); ?></td>
                  <td>
                    <span class="badge <?php echo $d['active']=='1'?'badge-success':'badge-secondary'; ?> px-2 py-1">
                      <?php echo $d['active']=='1'?'ACTIVE':'INACTIVE'; ?>
                    </span>
                  </td>
                  <td class="font-weight-bold text-success">₹<?php echo number_format((float)$d['total_inv'], 2); ?></td>
                  <td class="px-4 small text-muted"><?php echo htmlspecialchars($d['joining_date']); ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
<?php include 'common/footer.php'; ?>
</body>
</html>
