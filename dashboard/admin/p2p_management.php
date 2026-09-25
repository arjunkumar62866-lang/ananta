<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$tab = $_GET['tab'] ?? 'overview';
$status_filter = trim($_GET['status'] ?? '');
$user_filter = trim($_GET['user_id'] ?? '');

$controls = getSystemControls($pdo);
$p2p_on = (($controls['p2p_enable'] ?? '1') === '1');

// Fetch P2P transfers from DB
$whereClause = " WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $whereClause .= " AND p.status = :st";
    $params[':st'] = strtoupper($status_filter);
}

if (!empty($user_filter)) {
    $whereClause .= " AND (p.sender_id LIKE :user OR p.receiver_id LIKE :user OR p.transaction_id LIKE :user)";
    $params[':user'] = "%{$user_filter}%";
}

$query = "SELECT p.*, s.name as sender_name, r.name as receiver_name 
          FROM tbl_p2p_transfer p 
          LEFT JOIN user s ON p.sender_id = s.userid 
          LEFT JOIN user r ON p.receiver_id = r.userid 
          {$whereClause} 
          ORDER BY p.id DESC LIMIT 200";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary stats
$totVolume = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM tbl_p2p_transfer WHERE status='COMPLETED'")->fetchColumn();
$totCount  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_p2p_transfer")->fetchColumn();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM tbl_p2p_transfer WHERE status='PENDING'")->fetchColumn();
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">P2P MANAGEMENT & AUDIT</span>
          <h3 class="mb-1 text-white font-weight-bold">P2P Transfer & Investment Controls</h3>
          <p class="mb-0 text-white-50 small">Manage global P2P ON/OFF switch, review peer-to-peer transfers, and audit user transaction history.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge <?php echo $p2p_on?'badge-success':'badge-danger'; ?> px-3 py-2" style="border-radius: 100px; font-size:13px;">
            P2P SYSTEM: <?php echo $p2p_on?'ENABLED (ON)':'DISABLED (OFF)'; ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Summary Row -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL P2P COMPLETED VOLUME</span>
          <h3 class="mb-0 font-weight-bold text-success"><?php echo formatCurrency($totVolume); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">TOTAL P2P TRANSACTIONS</span>
          <h3 class="mb-0 font-weight-bold text-primary"><?php echo number_format($totCount); ?></h3>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
          <span class="text-muted small font-weight-bold d-block mb-1">PENDING P2P TRANSFERS</span>
          <h3 class="mb-0 font-weight-bold text-warning"><?php echo number_format($pendingCount); ?></h3>
        </div>
      </div>
    </div>

    <!-- P2P Control Card -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0;">
      <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <h5 class="mb-1 font-weight-bold text-dark"><i class="fa fa-power-off text-primary mr-2"></i> Global P2P Switch Setting</h5>
          <p class="mb-0 text-muted small">Enable or suspend member-to-member wallet transfers server-side across the platform.</p>
        </div>
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" id="p2p_global_toggle" <?php echo $p2p_on?'checked':''; ?>>
          <label class="custom-control-label font-weight-bold text-dark" for="p2p_global_toggle" style="cursor:pointer; font-size:15px;">
            <span id="p2p_toggle_text"><?php echo $p2p_on?'P2P Transfers ACTIVE':'P2P Transfers DISABLED'; ?></span>
          </label>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0;">
      <div class="card-body p-3">
        <form method="GET" class="row align-items-end">
          <input type="hidden" name="tab" value="history">
          <div class="col-md-4 mb-2">
            <label class="small font-weight-bold text-dark">Search User ID / Txn ID</label>
            <input type="text" name="user_id" value="<?php echo htmlspecialchars($user_filter); ?>" class="form-control" placeholder="Search Sender, Receiver, or Txn ID" style="border-radius:10px;">
          </div>
          <div class="col-md-4 mb-2">
            <label class="small font-weight-bold text-dark">Status Filter</label>
            <select name="status" class="form-control" style="border-radius:10px;">
              <option value="">All Statuses</option>
              <option value="COMPLETED" <?php echo $status_filter==='COMPLETED'?'selected':''; ?>>Completed</option>
              <option value="PENDING" <?php echo $status_filter==='PENDING'?'selected':''; ?>>Pending</option>
              <option value="CANCELLED" <?php echo $status_filter==='CANCELLED'?'selected':''; ?>>Cancelled</option>
            </select>
          </div>
          <div class="col-md-4 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary font-weight-bold w-100" style="border-radius:10px; background:#0284c7; border:none;">
              <i class="fa fa-filter mr-1"></i> Filter History
            </button>
            <a href="export.php?module=p2p&format=csv" class="btn btn-success font-weight-bold px-3" style="border-radius:10px; border:none;">Export</a>
          </div>
        </form>
      </div>
    </div>

    <!-- P2P Transaction Log Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-exchange text-success mr-2"></i> P2P Transaction & Investment History</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">Transaction ID</th>
                <th class="py-3">Sender User</th>
                <th class="py-3">Receiver User</th>
                <th class="py-3">Amount</th>
                <th class="py-3">Fee / Net</th>
                <th class="py-3">Status</th>
                <th class="py-3 px-4">Date & Time</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($transfers)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">No P2P transfer records found.</td>
                </tr>
              <?php else: foreach ($transfers as $p): ?>
                <tr>
                  <td class="px-4 font-weight-bold"><code><?php echo htmlspecialchars($p['transaction_id']); ?></code></td>
                  <td>
                    <strong><?php echo htmlspecialchars($p['sender_id']); ?></strong>
                    <div class="small text-muted"><?php echo htmlspecialchars($p['sender_name']??''); ?></div>
                  </td>
                  <td>
                    <strong><?php echo htmlspecialchars($p['receiver_id']); ?></strong>
                    <div class="small text-muted"><?php echo htmlspecialchars($p['receiver_name']??''); ?></div>
                  </td>
                  <td class="font-weight-bold text-primary"><?php echo formatCurrency((float)$p['amount']); ?></td>
                  <td class="small">Fee: <?php echo formatCurrency((float)($p['fee']??0)); ?><br>Net: <strong><?php echo formatCurrency((float)($p['net_amount']??$p['amount'])); ?></strong></td>
                  <td>
                    <span class="badge <?php echo $p['status']==='COMPLETED'?'badge-success':($p['status']==='PENDING'?'badge-warning':'badge-danger'); ?> px-2 py-1">
                      <?php echo htmlspecialchars($p['status']); ?>
                    </span>
                  </td>
                  <td class="px-4 small text-muted"><?php echo date('d-M-Y H:i:s', strtotime($p['created_at'])); ?></td>
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

<script>
$(document).ready(function() {
  $('#p2p_global_toggle').on('change', function() {
    let val = $(this).is(':checked') ? '1' : '0';
    let elem = $(this);

    $.ajax({
      url: 'admin_control_action.php',
      type: 'POST',
      data: {
        action: 'toggle_system_control',
        key: 'p2p_enable',
        value: val
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          $('#p2p_toggle_text').text(val === '1' ? 'P2P Transfers ACTIVE' : 'P2P Transfers DISABLED');
          alert(res.message);
        } else {
          alert('Error: ' + res.message);
          elem.prop('checked', !elem.is(':checked'));
        }
      },
      error: function() {
        alert('Server communication error!');
        elem.prop('checked', !elem.is(':checked'));
      }
    });
  });
});
</script>
</body>
</html>
