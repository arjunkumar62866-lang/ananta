<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$filter_user = trim($_GET['user_id'] ?? '');

$whereClause = " WHERE 1=1";
$params = [];

if (!empty($filter_user)) {
    $whereClause .= " AND (r.user_id LIKE :user OR u.name LIKE :user)";
    $params[':user'] = "%{$filter_user}%";
}

// Summary stats
$totGrowthBalance = (float)$pdo->query("SELECT COALESCE(SUM(profit_income_wallet), 0) FROM user")->fetchColumn();
$totGrowthPaid    = (float)$pdo->query("SELECT COALESCE(SUM(CAST(amount AS DECIMAL(15,2))), 0) FROM tbl_roiinc")->fetchColumn();
$totActiveInvest  = (float)$pdo->query("SELECT COALESCE(SUM(package), 0) FROM tbl_roi_one")->fetchColumn();

// List Growth Packages & Income Credits
$query = "SELECT r.id, r.user_id, u.name, r.package, r.percentage, r.date, u.profit_income_wallet 
          FROM tbl_roi_one r 
          LEFT JOIN user u ON r.user_id = u.userid 
          {$whereClause} 
          ORDER BY r.id DESC LIMIT 200";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">GROWTH WALLET MANAGEMENT</span>
          <h3 class="mb-1 text-white font-weight-bold">Growth Wallet & ROI Income Ledger</h3>
          <p class="mb-0 text-white-50 small">Monitor user growth wallet balances, active ROI package investments, and permanent credit/debit records.</p>
        </div>
        <div>
          <a href="monthly-profit-closing.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-calculator mr-1"></i> Execute Monthly Profit Closing
          </a>
        </div>
      </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL GROWTH WALLET BALANCES</span>
          <h3 class="mb-0 font-weight-bold text-success"><?php echo formatCurrency($totGrowthBalance); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL ROI PROFIT PAID</span>
          <h3 class="mb-0 font-weight-bold text-primary"><?php echo formatCurrency($totGrowthPaid); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL ACTIVE GROWTH PACKAGES</span>
          <h3 class="mb-0 font-weight-bold text-dark"><?php echo formatCurrency($totActiveInvest); ?></h3>
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
              <i class="fa fa-search mr-1"></i> Filter Records
            </button>
            <a href="export.php?module=users&format=csv" class="btn btn-success font-weight-bold px-3" style="border-radius:10px; border:none;">Export</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Growth Wallet Data Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-line-chart text-success mr-2"></i> Active Growth Packages & Wallet Balance Ledger</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">Package ID</th>
                <th class="py-3">User ID</th>
                <th class="py-3">Member Name</th>
                <th class="py-3">Package Investment</th>
                <th class="py-3">Profit %</th>
                <th class="py-3">Growth Wallet Balance</th>
                <th class="py-3 px-4">Investment Date</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($packages)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">No growth wallet package records found.</td>
                </tr>
              <?php else: foreach ($packages as $pkg): ?>
                <tr>
                  <td class="px-4 font-weight-bold">#PKG-<?php echo $pkg['id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($pkg['user_id']); ?></strong></td>
                  <td><?php echo htmlspecialchars($pkg['name'] ?? 'N/A'); ?></td>
                  <td class="font-weight-bold text-primary"><?php echo formatCurrency((float)$pkg['package']); ?></td>
                  <td><span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($pkg['percentage']); ?>% Monthly</span></td>
                  <td class="font-weight-bold text-success"><?php echo formatCurrency((float)($pkg['profit_income_wallet'] ?? 0)); ?></td>
                  <td class="px-4 small text-muted"><?php echo htmlspecialchars($pkg['date']); ?></td>
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
