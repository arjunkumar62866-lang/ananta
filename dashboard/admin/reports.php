<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include __DIR__ . '/common/header.php';

$type        = strtolower(trim($_GET['type'] ?? 'daily'));
$user_search = trim($_GET['user_search'] ?? '');
$from_date   = trim($_GET['from_date'] ?? date('Y-m-01'));
$to_date     = trim($_GET['to_date'] ?? date('Y-m-d'));

// Search parameter string for user query filtering
$uSearchParam = '%' . $user_search . '%';

// Report title & meta mapping
$reportTitles = [
    'daily'      => 'Daily Financial & Business Summary',
    'monthly'    => 'Monthly Financial & Payout Summary',
    'yearly'     => 'Yearly Business & Tax Statement',
    'user'       => 'User-Wise Financial Statement Report',
    'investment' => 'Package Investment & Activation Report',
    'withdrawal' => 'Withdrawal Payout & Settlement Report',
    'income'     => 'Comprehensive Income Distribution Report',
    'business'   => 'Team & System Business Volume Report',
    'company'    => 'Company Revenue & $11 Unlock Access Report'
];

$title = $reportTitles[$type] ?? 'Financial System Report';

$reportData = [];
$totalSum   = 0.0;
$totalCount = 0;

switch ($type) {
    case 'daily':
        // Group by Date for Daily Summary
        $sql = "SELECT 
                    DATE(created_date) as report_date,
                    COUNT(DISTINCT user_id) as total_users,
                    SUM(CASE WHEN subject LIKE '%Investment%' OR subject LIKE '%Package%' OR subject LIKE '%Deposit%' THEN amount ELSE 0 END) as total_investment,
                    SUM(CASE WHEN subject LIKE '%Income%' OR subject LIKE '%Bonus%' OR subject LIKE '%ROI%' OR subject LIKE '%Reward%' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN subject LIKE '%Withdraw%' THEN amount ELSE 0 END) as total_withdrawals,
                    COUNT(*) as total_txns
                FROM tbl_transaction 
                WHERE DATE(created_date) BETWEEN :from_date AND :to_date
                GROUP BY DATE(created_date) 
                ORDER BY report_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':from_date' => $from_date, ':to_date' => $to_date]);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'monthly':
        // Group by Month for Monthly Summary
        $sql = "SELECT 
                    DATE_FORMAT(created_date, '%Y-%m') as report_month,
                    COUNT(DISTINCT user_id) as total_users,
                    SUM(CASE WHEN subject LIKE '%Investment%' OR subject LIKE '%Package%' OR subject LIKE '%Deposit%' THEN amount ELSE 0 END) as total_investment,
                    SUM(CASE WHEN subject LIKE '%Income%' OR subject LIKE '%Bonus%' OR subject LIKE '%ROI%' OR subject LIKE '%Reward%' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN subject LIKE '%Withdraw%' THEN amount ELSE 0 END) as total_withdrawals,
                    COUNT(*) as total_txns
                FROM tbl_transaction 
                WHERE DATE(created_date) BETWEEN :from_date AND :to_date
                GROUP BY DATE_FORMAT(created_date, '%Y-%m') 
                ORDER BY report_month DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':from_date' => $from_date, ':to_date' => $to_date]);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'yearly':
        // Group by Year for Yearly Summary
        $sql = "SELECT 
                    YEAR(created_date) as report_year,
                    COUNT(DISTINCT user_id) as total_users,
                    SUM(CASE WHEN subject LIKE '%Investment%' OR subject LIKE '%Package%' OR subject LIKE '%Deposit%' THEN amount ELSE 0 END) as total_investment,
                    SUM(CASE WHEN subject LIKE '%Income%' OR subject LIKE '%Bonus%' OR subject LIKE '%ROI%' OR subject LIKE '%Reward%' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN subject LIKE '%Withdraw%' THEN amount ELSE 0 END) as total_withdrawals,
                    COUNT(*) as total_txns
                FROM tbl_transaction 
                WHERE DATE(created_date) BETWEEN :from_date AND :to_date
                GROUP BY YEAR(created_date) 
                ORDER BY report_year DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':from_date' => $from_date, ':to_date' => $to_date]);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'user':
        // User-wise Statement Report
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch OR u.mobile LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT 
                    u.id, u.userid, u.name, u.mobile, u.amount as investment, u.active, u.joining_date,
                    COALESCE((SELECT SUM(amount) FROM tbl_transaction WHERE user_id = u.userid AND (subject LIKE '%Income%' OR subject LIKE '%Bonus%' OR subject LIKE '%ROI%')), 0) as total_income,
                    COALESCE((SELECT SUM(amount) FROM tbl_transaction WHERE user_id = u.userid AND subject LIKE '%Withdraw%'), 0) as total_withdrawal
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['investment']; 
        }
        $totalCount = count($reportData);
        break;

    case 'investment':
        // Package Investment & Activations
        $whereClause = "WHERE DATE(r.date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (r.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT r.id, r.user_id, u.name, r.package as amount, r.percentage, r.date as created_at 
                FROM tbl_roi_one r 
                LEFT JOIN user u ON r.user_id = u.userid 
                {$whereClause} 
                ORDER BY r.id DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['amount']; 
        }
        $totalCount = count($reportData);
        break;

    case 'withdrawal':
        // Withdrawal Payout & Settlements
        $whereClause = "WHERE t.subject LIKE '%Withdraw%' AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.status, t.created_date as created_at 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['amount']; 
        }
        $totalCount = count($reportData);
        break;

    case 'income':
        // Comprehensive Income Distributions
        $whereClause = "WHERE (t.subject LIKE '%Income%' OR t.subject LIKE '%Bonus%' OR t.subject LIKE '%ROI%' OR t.subject LIKE '%Reward%' OR t.subject LIKE '%Profit%') AND DATE(t.created_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (t.user_id LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.status, t.created_date as created_at 
                FROM tbl_transaction t 
                LEFT JOIN user u ON t.user_id = u.userid 
                {$whereClause} 
                ORDER BY t.id DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['amount']; 
        }
        $totalCount = count($reportData);
        break;

    case 'business':
        // Business Volume Report
        $whereClause = "WHERE DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT u.id, u.userid as user_id, u.name, u.amount as self_investment, 
                       COALESCE(t.left_id, 'None') as left_volume, 
                       COALESCE(t.right_id, 'None') as right_volume,
                       COALESCE((SELECT SUM(amount) FROM user WHERE sponserid = u.userid), 0) as total_team_volume,
                       u.joining_date as created_at
                FROM user u 
                LEFT JOIN tree t ON u.userid = t.userid 
                {$whereClause} 
                ORDER BY total_team_volume DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reportData as $r) { 
            $totalSum += (float)$r['total_team_volume']; 
        }
        $totalCount = count($reportData);
        break;

    case 'company':
        // Company Revenue & $11 Unlock Access Report
        $whereClause = "WHERE u.active = '1' AND DATE(u.joining_date) BETWEEN :from_date AND :to_date";
        $params = [':from_date' => $from_date, ':to_date' => $to_date];
        if (!empty($user_search)) {
            $whereClause .= " AND (u.userid LIKE :usearch OR u.name LIKE :usearch)";
            $params[':usearch'] = $uSearchParam;
        }
        $sql = "SELECT u.id, u.userid as user_id, u.name, u.joining_date as created_at 
                FROM user u 
                {$whereClause} 
                ORDER BY u.id DESC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $u) {
            $totalSum += 990.00; // ₹990 per $11 unlock access
            $reportData[] = [
                'id' => $u['id'],
                'user_id' => $u['user_id'],
                'name' => $u['name'],
                'amount' => 990.00,
                'subject' => '$11 (₹990) Unlock Access Revenue',
                'created_at' => $u['created_at']
            ];
        }
        $totalCount = count($reportData);
        break;
}
?>

<style>
/* High contrast form control styling for input boxes, date inputs, selects, and text */
.form-control, select.form-control, input[type="text"].form-control, input[type="date"].form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    opacity: 1 !important;
}
.form-control:focus, select.form-control:focus, input[type="text"].form-control:focus, input[type="date"].form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}
select.form-control option {
    background-color: #ffffff !important;
    color: #0f172a !important;
}
.form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}
label.form-label-custom {
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 6px !important;
    display: block !important;
}
</style>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Banner -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
      <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">REPORTS & ANALYTICS</span>
          <h3 class="mb-1 text-white font-weight-bold"><?php echo htmlspecialchars($title); ?></h3>
          <p class="mb-0 text-white-50 small">Filter financial records by date range, user ID/name, and export official CSV or PDF reports.</p>
        </div>
        <div>
          <a href="export.php?module=<?php echo $type==='withdrawal'?'withdrawals':($type==='investment'?'users':'audit'); ?>&format=csv" class="btn btn-light font-weight-bold px-3 py-2 mr-2" style="border-radius: 10px;">
            <i class="fa fa-file-excel-o text-success mr-1"></i> Export CSV / Excel
          </a>
          <a href="export.php?module=<?php echo $type==='withdrawal'?'withdrawals':($type==='investment'?'users':'audit'); ?>&format=pdf" class="btn btn-outline-light font-weight-bold px-3 py-2" style="border-radius: 10px;" target="_blank">
            <i class="fa fa-file-pdf-o text-danger mr-1"></i> Export PDF
          </a>
        </div>
      </div>
    </div>

    <!-- Filter Form with Calendar Datepicker & User Search -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-body p-4">
        <form method="GET" class="row align-items-end">
          
          <div class="col-lg-3 col-md-6 mb-3">
            <label class="form-label-custom">Report Category / Type</label>
            <select name="type" class="form-control" onchange="this.form.submit()">
              <option value="daily" <?php echo $type==='daily'?'selected':''; ?>>📅 Daily Financial Summary</option>
              <option value="monthly" <?php echo $type==='monthly'?'selected':''; ?>>📆 Monthly Financial Summary</option>
              <option value="yearly" <?php echo $type==='yearly'?'selected':''; ?>>📊 Yearly Business Summary</option>
              <option value="user" <?php echo $type==='user'?'selected':''; ?>>👤 User-Wise Financial Statement</option>
              <option value="investment" <?php echo $type==='investment'?'selected':''; ?>>💼 Package Investment Report</option>
              <option value="withdrawal" <?php echo $type==='withdrawal'?'selected':''; ?>>💸 Withdrawal Settlement Report</option>
              <option value="income" <?php echo $type==='income'?'selected':''; ?>>💰 Income Distribution Report</option>
              <option value="business" <?php echo $type==='business'?'selected':''; ?>>🌐 Team Business Volume Report</option>
              <option value="company" <?php echo $type==='company'?'selected':''; ?>>🏢 Company Revenue ($11) Report</option>
            </select>
          </div>

          <div class="col-lg-3 col-md-6 mb-3">
            <label class="form-label-custom">🔍 Search User ID / Name</label>
            <input type="text" name="user_search" value="<?php echo htmlspecialchars($user_search); ?>" class="form-control" placeholder="Enter User ID or Member Name...">
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label class="form-label-custom">📅 From Date</label>
            <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" class="form-control">
          </div>

          <div class="col-lg-2 col-md-6 mb-3">
            <label class="form-label-custom">📅 To Date</label>
            <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" class="form-control">
          </div>

          <div class="col-lg-2 col-md-12 mb-3">
            <button type="submit" class="btn btn-primary font-weight-bold w-100 py-2" style="border-radius:10px; background:#0284c7; border:none; height: 44px;">
              <i class="fa fa-filter mr-1"></i> Apply Filter
            </button>
          </div>

        </form>
      </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row mb-4">
      <div class="col-md-6 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL REPORT VOLUME / REVENUE</span>
          <h3 class="mb-0 font-weight-bold text-success">₹<?php echo number_format($totalSum, 2); ?></h3>
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL RECORD COUNT</span>
          <h3 class="mb-0 font-weight-bold text-primary"><?php echo number_format($totalCount); ?> Records</h3>
        </div>
      </div>
    </div>

    <!-- Specialized Table Output for Each Report Type -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-table text-primary mr-2"></i> Detailed <?php echo htmlspecialchars($title); ?> Records</h5>
        <?php if (!empty($user_search)): ?>
          <span class="badge badge-info px-3 py-1" style="border-radius: 20px;">Filtered by: "<?php echo htmlspecialchars($user_search); ?>"</span>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            
            <?php if (in_array($type, ['daily', 'monthly', 'yearly'])): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4"><?php echo ucfirst($type); ?> Period</th>
                  <th class="py-3">Active Transacting Members</th>
                  <th class="py-3">Total Investment (₹)</th>
                  <th class="py-3">Total Income Distributed (₹)</th>
                  <th class="py-3">Total Withdrawals Paid (₹)</th>
                  <th class="py-3 px-4">Transaction Count</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No summary records found for selected period.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold text-primary">
                      <?php echo htmlspecialchars($row['report_date'] ?? $row['report_month'] ?? $row['report_year'] ?? ''); ?>
                    </td>
                    <td><span class="badge badge-secondary px-2 py-1"><?php echo number_format($row['total_users']); ?> Users</span></td>
                    <td class="font-weight-bold text-success">₹<?php echo number_format((float)$row['total_investment'], 2); ?></td>
                    <td class="font-weight-bold text-info">₹<?php echo number_format((float)$row['total_income'], 2); ?></td>
                    <td class="font-weight-bold text-danger">₹<?php echo number_format((float)$row['total_withdrawals'], 2); ?></td>
                    <td class="px-4 font-weight-bold"><?php echo number_format($row['total_txns']); ?> Txns</td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'user'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Mobile Number</th>
                  <th class="py-3">Self Investment (₹)</th>
                  <th class="py-3">Total Income (₹)</th>
                  <th class="py-3">Total Withdrawn (₹)</th>
                  <th class="py-3 px-4">Joining Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="8" class="text-center py-5 text-muted">No user statements found matching filter criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['userid']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                    <td class="font-weight-bold text-success">₹<?php echo number_format((float)$row['investment'], 2); ?></td>
                    <td class="font-weight-bold text-info">₹<?php echo number_format((float)$row['total_income'], 2); ?></td>
                    <td class="font-weight-bold text-danger">₹<?php echo number_format((float)$row['total_withdrawal'], 2); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['joining_date']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php elseif ($type === 'business'): ?>
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Self Investment (₹)</th>
                  <th class="py-3">Left Subtree User</th>
                  <th class="py-3">Right Subtree User</th>
                  <th class="py-3">Direct Sponsor Volume (₹)</th>
                  <th class="py-3 px-4">Joining Date</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="8" class="text-center py-5 text-muted">No business volume records found matching filter criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['user_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="font-weight-bold text-dark">₹<?php echo number_format((float)$row['self_investment'], 2); ?></td>
                    <td><span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($row['left_volume']); ?></span></td>
                    <td><span class="badge badge-warning px-2 py-1"><?php echo htmlspecialchars($row['right_volume']); ?></span></td>
                    <td class="font-weight-bold text-success">₹<?php echo number_format((float)$row['total_team_volume'], 2); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>

            <?php else: ?>
              <!-- Investment, Withdrawal, Income, Company Reports -->
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Record ID</th>
                  <th class="py-3">User ID</th>
                  <th class="py-3">Member Name</th>
                  <th class="py-3">Amount (₹ / $)</th>
                  <th class="py-3">Description / Details</th>
                  <th class="py-3 px-4">Date & Time</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($reportData)): ?>
                  <tr><td colspan="6" class="text-center py-5 text-muted">No records found for the selected criteria.</td></tr>
                <?php else: foreach ($reportData as $row): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['user_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? 'Member'); ?></td>
                    <td class="font-weight-bold text-success">₹<?php echo number_format((float)$row['amount'], 2); ?></td>
                    <td class="small text-muted"><?php echo htmlspecialchars($row['subject'] ?? 'Transaction Record'); ?></td>
                    <td class="px-4 small text-muted"><?php echo htmlspecialchars($row['created_at']); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            <?php endif; ?>

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
