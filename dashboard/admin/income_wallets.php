<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$filter_user = trim($_GET['user_id'] ?? '');

$whereClause = " WHERE status = '1'";
$params = [];

if (!empty($filter_user)) {
    $whereClause .= " AND (userid LIKE :user OR name LIKE :user)";
    $params[':user'] = "%{$filter_user}%";
}

// Aggregated wallet balances
$totals = $pdo->query("SELECT 
  COALESCE(SUM(profit_income_wallet),0) as pi,
  COALESCE(SUM(profit_sharing_wallet),0) as ps,
  COALESCE(SUM(direct_bonus_wallet),0) as db,
  COALESCE(SUM(mentor_income_wallet),0) as mi,
  COALESCE(SUM(vip_club_wallet),0) as vip
  FROM user")->fetch(PDO::FETCH_ASSOC);

$query = "SELECT userid, name, profit_income_wallet, profit_sharing_wallet, direct_bonus_wallet, mentor_income_wallet, vip_club_wallet 
          FROM user 
          {$whereClause} 
          ORDER BY id DESC LIMIT 200";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">INCOME WALLETS OVERVIEW</span>
          <h3 class="mb-1 text-white font-weight-bold">All Isolated Income Wallet Balances</h3>
          <p class="mb-0 text-white-50 small">Monitor Profit Income, Profit Sharing, Direct Bonus, Mentor Income, Rank Reward, VIP Club, and Turnover Share Wallets.</p>
        </div>
        <div>
          <a href="income_management.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-dashboard mr-1"></i> Income System Dashboard
          </a>
        </div>
      </div>
    </div>

    <!-- 5 Wallet Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-4 col-lg-2 mb-3">
        <div class="p-3 bg-white border rounded-lg shadow-sm h-100" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">PROFIT INCOME</span>
          <h5 class="mb-0 font-weight-bold text-primary">₹<?php echo number_format($totals['pi'], 2); ?></h5>
        </div>
      </div>
      <div class="col-md-4 col-lg-2.5 mb-3">
        <div class="p-3 bg-white border rounded-lg shadow-sm h-100" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">PROFIT SHARING</span>
          <h5 class="mb-0 font-weight-bold text-success">₹<?php echo number_format($totals['ps'], 2); ?></h5>
        </div>
      </div>
      <div class="col-md-4 col-lg-2.5 mb-3">
        <div class="p-3 bg-white border rounded-lg shadow-sm h-100" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">DIRECT BONUS</span>
          <h5 class="mb-0 font-weight-bold text-warning">₹<?php echo number_format($totals['db'], 2); ?></h5>
        </div>
      </div>
      <div class="col-md-4 col-lg-2.5 mb-3">
        <div class="p-3 bg-white border rounded-lg shadow-sm h-100" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">MENTOR INCOME</span>
          <h5 class="mb-0 font-weight-bold text-info">₹<?php echo number_format($totals['mi'], 2); ?></h5>
        </div>
      </div>
      <div class="col-md-4 col-lg-2.5 mb-3">
        <div class="p-3 bg-white border rounded-lg shadow-sm h-100" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">VIP CLUB WALLET</span>
          <h5 class="mb-0 font-weight-bold text-danger">$<?php echo number_format($totals['vip'], 2); ?></h5>
        </div>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0;">
      <div class="card-body p-3">
        <form method="GET" class="row align-items-center">
          <div class="col-md-8 mb-2">
            <input type="text" name="user_id" value="<?php echo htmlspecialchars($filter_user); ?>" class="form-control" placeholder="Search by User ID or Name" style="border-radius:10px;">
          </div>
          <div class="col-md-4 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary font-weight-bold w-100" style="border-radius:10px; background:#0284c7; border:none;">
              <i class="fa fa-search mr-1"></i> Filter Income Balances
            </button>
            <a href="export.php?module=users&format=csv" class="btn btn-success font-weight-bold px-3" style="border-radius:10px; border:none;">Export</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Income Wallets Data Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-money text-success mr-2"></i> User Income Wallet Balances</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">User ID</th>
                <th class="py-3">Member Name</th>
                <th class="py-3">Profit Income</th>
                <th class="py-3">Profit Sharing</th>
                <th class="py-3">Direct Bonus</th>
                <th class="py-3">Mentor Income</th>
                <th class="py-3 px-4">VIP Club Wallet</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">No income wallet records found.</td>
                </tr>
              <?php else: foreach ($users as $u): ?>
                <tr>
                  <td class="px-4 font-weight-bold"><strong><?php echo htmlspecialchars($u['userid']); ?></strong></td>
                  <td><?php echo htmlspecialchars($u['name']); ?></td>
                  <td class="font-weight-bold text-primary">₹<?php echo number_format((float)($u['profit_income_wallet']??0), 2); ?></td>
                  <td class="font-weight-bold text-success">₹<?php echo number_format((float)($u['profit_sharing_wallet']??0), 2); ?></td>
                  <td class="font-weight-bold text-warning">₹<?php echo number_format((float)($u['direct_bonus_wallet']??0), 2); ?></td>
                  <td class="font-weight-bold text-info">₹<?php echo number_format((float)($u['mentor_income_wallet']??0), 2); ?></td>
                  <td class="px-4 font-weight-bold text-danger">$<?php echo number_format((float)($u['vip_club_wallet']??0), 2); ?></td>
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
