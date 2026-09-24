<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$ranks = $pdo->query("SELECT * FROM tbl_vip_level_config ORDER BY level_id ASC")->fetchAll(PDO::FETCH_ASSOC);
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">RANK & VIP CLUB CONFIGURATION</span>
          <h3 class="mb-1 text-white font-weight-bold">Rank Matrix, Qualifications & Reward Rules</h3>
          <p class="mb-0 text-white-50 small">View and manage binary qualification criteria, one-time rank rewards, weaker leg income %, and turnover share rates (Levels 1 to 10).</p>
        </div>
        <div>
          <a href="vip-club.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-trophy mr-1"></i> VIP Club Qualifications & Payouts
          </a>
        </div>
      </div>
    </div>

    <!-- Rank Matrix Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-star text-warning mr-2"></i> VIP Level Qualification Matrix (Levels 1 - 10)</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">Level</th>
                <th class="py-3">Rank Name</th>
                <th class="py-3">Left / Right ID Req</th>
                <th class="py-3">Left / Right Volume ($)</th>
                <th class="py-3">One-Time Reward</th>
                <th class="py-3">Monthly Repeat ($)</th>
                <th class="py-3">VIP Income %</th>
                <th class="py-3 px-4">Turnover Share (0.5%)</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php foreach ($ranks as $r): ?>
                <tr>
                  <td class="px-4 font-weight-bold"><span class="badge badge-primary px-2 py-1">Level <?php echo $r['level_id']; ?></span></td>
                  <td><strong><?php echo htmlspecialchars($r['name'] ?? ('VIP Level ' . $r['level_id'])); ?></strong></td>
                  <td><?php echo number_format($r['req_left_ids']); ?> / <?php echo number_format($r['req_right_ids']); ?> IDs</td>
                  <td>$<?php echo number_format($r['req_left_business'], 2); ?> / $<?php echo number_format($r['req_right_business'], 2); ?></td>
                  <td class="font-weight-bold text-success">$<?php echo number_format((float)$r['reward_amount'], 2); ?></td>
                  <td>$<?php echo number_format((float)$r['monthly_repeat_business'], 2); ?></td>
                  <td class="font-weight-bold text-info"><?php echo number_format((float)$r['vip_income_rate'], 2); ?>%</td>
                  <td class="px-4">
                    <?php if ($r['has_turnover_share'] == 1): ?>
                      <span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i> 0.5% Share</span>
                    <?php else: ?>
                      <span class="badge badge-light text-muted">N/A</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
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
