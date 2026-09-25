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

// Summary stats
$totMainWallet  = (float)($pdo->query("SELECT COALESCE(SUM(pin_wallet), 0) FROM user")->fetchColumn() ?? 0);
$totDepositWallet = (float)($pdo->query("SELECT COALESCE(SUM(deposite_wallet), 0) FROM user")->fetchColumn() ?? 0);
$totApprovedDeposits = (float)($pdo->query("SELECT COALESCE(SUM(amount), 0) FROM tbl_payment WHERE status = '1'")->fetchColumn() ?? 0);

$query = "SELECT userid, name, email, mobile, pin_wallet, deposite_wallet, amount, active, joining_date 
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">MAIN WALLET MANAGEMENT</span>
          <h3 class="mb-1 text-white font-weight-bold">Main Wallet, Deposits & Admin Fund Transfers</h3>
          <p class="mb-0 text-white-50 small">Monitor user main universal wallet balances, deposit requests, P2P transfers, and unlock access balances.</p>
        </div>
        <div>
          <a href="pin_wallet_amount.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-paper-plane mr-1"></i> Admin Fund Credit / Transfer
          </a>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL MAIN PIN WALLET BALANCES</span>
          <h3 class="mb-0 font-weight-bold text-primary"><?php echo formatCurrency($totMainWallet); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL DEPOSIT WALLET BALANCES</span>
          <h3 class="mb-0 font-weight-bold text-info"><?php echo formatCurrency($totDepositWallet); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL APPROVED DEPOSITS</span>
          <h3 class="mb-0 font-weight-bold text-success"><?php echo formatCurrency($totApprovedDeposits); ?></h3>
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
              <i class="fa fa-search mr-1"></i> Search Balances
            </button>
            <a href="export.php?module=users&format=csv" class="btn btn-success font-weight-bold px-3" style="border-radius:10px; border:none;">Export</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Main Wallet User Balances Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-wallet text-primary mr-2"></i> User Main Wallet & Deposit Balance Records</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">User ID</th>
                <th class="py-3">Member Name</th>
                <th class="py-3">Main / Pin Wallet</th>
                <th class="py-3">Deposit Wallet</th>
                <th class="py-3">Unlock Access</th>
                <th class="py-3 px-4">Action</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">No user main wallet records found.</td>
                </tr>
              <?php else: foreach ($users as $u): ?>
                <tr>
                  <td class="px-4 font-weight-bold"><strong><?php echo htmlspecialchars($u['userid']); ?></strong></td>
                  <td><?php echo htmlspecialchars($u['name']); ?></td>
                  <td class="font-weight-bold text-primary"><?php echo formatCurrency((float)$u['pin_wallet']); ?></td>
                  <td class="font-weight-bold text-info"><?php echo formatCurrency((float)($u['deposite_wallet'] ?? 0)); ?></td>
                  <td>
                    <span class="badge <?php echo $u['active']=='1'?'badge-success':'badge-secondary'; ?> px-2 py-1">
                      <?php echo $u['active']=='1'?'ACTIVE ($11)':'INACTIVE'; ?>
                    </span>
                  </td>
                  <td class="px-4">
                    <a href="pin_wallet_amount.php?uid=<?php echo urlencode($u['userid']); ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                      <i class="fa fa-plus-circle mr-1"></i> Credit / Debit
                    </a>
                  </td>
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
