<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$tab = $_GET['tab'] ?? 'audit';
$admin_id = $_SESSION['auserid'] ?? 'AN1290';

$controls = getSystemControls($pdo);

$page = (int)($_GET['page'] ?? 1);
$limit = 50;
$offset = ($page - 1) * $limit;

$filter_user = trim($_GET['user_id'] ?? '');
$filter_action = trim($_GET['action'] ?? '');

$whereClause = " WHERE 1=1";
$params = [];

if (!empty($filter_user)) {
    $whereClause .= " AND (l.target_user_id LIKE :user OR l.admin_id LIKE :user)";
    $params[':user'] = "%{$filter_user}%";
}

if (!empty($filter_action)) {
    $whereClause .= " AND l.action = :action";
    $params[':action'] = $filter_action;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_admin_audit_log l {$whereClause}");
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$logQuery = "SELECT l.*, u.name as target_username 
             FROM tbl_admin_audit_log l 
             LEFT JOIN user u ON l.target_user_id = u.userid 
             {$whereClause} 
             ORDER BY l.id DESC LIMIT {$limit} OFFSET {$offset}";
$logStmt = $pdo->prepare($logQuery);
$logStmt->execute($params);
$auditLogs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">SYSTEM ADMINISTRATION</span>
          <h3 class="mb-1 text-white font-weight-bold">Admin Audit Log & Website Controls</h3>
          <p class="mb-0 text-white-50 small">Monitor all administrative actions and manage global website functionality switches.</p>
        </div>
        <div>
          <a href="?tab=audit" class="btn btn-sm <?php echo $tab==='audit'?'btn-light text-primary font-weight-bold':'btn-outline-light'; ?> px-3 py-2 mr-2" style="border-radius: 10px;">
            <i class="fa fa-list-alt mr-1"></i> Admin Audit Trail
          </a>
          <a href="?tab=controls" class="btn btn-sm <?php echo $tab==='controls'?'btn-light text-primary font-weight-bold':'btn-outline-light'; ?> px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-toggle-on mr-1"></i> Website Controls (ON/OFF)
          </a>
        </div>
      </div>
    </div>

    <?php if ($tab === 'controls'): ?>
      <!-- Website Controls Section -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
              <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-sliders text-primary mr-2"></i> Global System Controls & Feature Switches</h5>
              <span class="badge badge-success px-3 py-1" style="border-radius:100px;">REAL-TIME SERVER ENFORCEMENT</span>
            </div>
            <div class="card-body p-4">
              <div class="row">

                <?php
                $controlItems = [
                  'website_maintenance'   => ['title' => 'Website Maintenance Mode', 'desc' => 'Blocks standard user access and displays maintenance banner.'],
                  'user_registration'     => ['title' => 'User Registration System', 'desc' => 'Allows or blocks new user account sign-ups.'],
                  'user_login'            => ['title' => 'User Login Access', 'desc' => 'Enables or disables member sign-in portal.'],
                  'unlock_access'         => ['title' => '$11 Account Unlock Access', 'desc' => 'Controls $11 account activation & renewal processing.'],
                  'investment_enable'     => ['title' => 'Package Investment System', 'desc' => 'Enables or suspends new package deposit investments.'],
                  'p2p_enable'            => ['title' => 'P2P Fund Transfer & Investment', 'desc' => 'Controls peer-to-peer user wallet transfers.'],
                  'deposit_enable'        => ['title' => 'Deposit Request System', 'desc' => 'Allows or pauses new deposit fund requests.'],
                  'withdrawal_enable'     => ['title' => 'Global Withdrawal Payouts', 'desc' => 'Enables or locks global member withdrawal submissions.'],
                  'global_income_enable'  => ['title' => 'Global Income Calculations', 'desc' => 'Controls daily & monthly income distribution engines.'],
                  'offer_popup_enable'    => ['title' => 'Website Offer Popup', 'desc' => 'Toggles global promotional offer popup display on user login.']
                ];

                foreach ($controlItems as $key => $meta):
                  $isON = (($controls[$key] ?? '1') === '1');
                ?>
                  <div class="col-md-6 col-lg-4 mb-4">
                    <div class="p-3 border rounded-lg h-100 d-flex flex-column justify-content-between" style="background:#f8fafc; border-color:#cbd5e1 !important; border-radius: 16px;">
                      <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <h6 class="mb-0 font-weight-bold text-dark"><?php echo $meta['title']; ?></h6>
                          <span class="badge <?php echo $isON?'badge-success':'badge-danger'; ?> px-2 py-1 status-badge-<?php echo $key; ?>">
                            <?php echo $isON?'ENABLED':'DISABLED'; ?>
                          </span>
                        </div>
                        <p class="text-muted small mb-3"><?php echo $meta['desc']; ?></p>
                      </div>
                      <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="small font-weight-bold text-secondary">Key: <code><?php echo $key; ?></code></span>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input control-toggle-switch" id="switch_<?php echo $key; ?>" data-key="<?php echo $key; ?>" <?php echo $isON?'checked':''; ?>>
                          <label class="custom-control-label font-weight-bold text-dark" for="switch_<?php echo $key; ?>" style="cursor:pointer;"></label>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- Admin Audit Log Section -->
      <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
        <div class="card-body p-4">
          <form method="GET" class="row align-items-end gap-2">
            <input type="hidden" name="tab" value="audit">
            <div class="col-md-4 mb-2">
              <label class="small font-weight-bold text-dark">Search User / Admin ID</label>
              <input type="text" name="user_id" value="<?php echo htmlspecialchars($filter_user); ?>" class="form-control" placeholder="Search by User ID or Admin ID" style="border-radius:10px;">
            </div>
            <div class="col-md-4 mb-2">
              <label class="small font-weight-bold text-dark">Filter Action Type</label>
              <select name="action" class="form-control" style="border-radius:10px;">
                <option value="">All Action Types</option>
                <option value="CREDIT" <?php echo $filter_action==='CREDIT'?'selected':''; ?>>CREDIT</option>
                <option value="DEBIT" <?php echo $filter_action==='DEBIT'?'selected':''; ?>>DEBIT</option>
                <option value="USER_STATUS_CHANGE" <?php echo $filter_action==='USER_STATUS_CHANGE'?'selected':''; ?>>USER_STATUS_CHANGE</option>
                <option value="USER_WITHDRAWAL_TOGGLE" <?php echo $filter_action==='USER_WITHDRAWAL_TOGGLE'?'selected':''; ?>>USER_WITHDRAWAL_TOGGLE</option>
                <option value="TOGGLE_SETTING" <?php echo $filter_action==='TOGGLE_SETTING'?'selected':''; ?>>TOGGLE_SETTING</option>
              </select>
            </div>
            <div class="col-md-4 mb-2 d-flex gap-2">
              <button type="submit" class="btn btn-primary font-weight-bold px-4 w-100" style="border-radius:10px; background:#0284c7; border:none;">
                <i class="fa fa-search mr-1"></i> Filter Logs
              </button>
              <a href="export.php?module=audit&format=csv&user_id=<?php echo urlencode($filter_user); ?>&action=<?php echo urlencode($filter_action); ?>" class="btn btn-success font-weight-bold px-3" style="border-radius:10px; border:none;">
                <i class="fa fa-download mr-1"></i> Export
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
          <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-shield text-success mr-2"></i> Permanent Admin Action Audit Trail Log</h5>
          <span class="badge badge-info px-3 py-1" style="border-radius:100px;">TOTAL RECORDS: <?php echo number_format($totalRecords); ?></span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
              <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                <tr>
                  <th class="py-3 px-4">Log ID</th>
                  <th class="py-3">Admin ID</th>
                  <th class="py-3">Action</th>
                  <th class="py-3">Target User</th>
                  <th class="py-3">Wallet</th>
                  <th class="py-3">Amount</th>
                  <th class="py-3">Prev Bal → New Bal</th>
                  <th class="py-3">Reason / Ref</th>
                  <th class="py-3 px-4">Date & Time</th>
                </tr>
              </thead>
              <tbody style="font-size: 13.5px; color: #0f172a;">
                <?php if (empty($auditLogs)): ?>
                  <tr>
                    <td colspan="9" class="text-center py-5 text-muted">No administrative audit records found.</td>
                  </tr>
                <?php else: foreach ($auditLogs as $l): ?>
                  <tr>
                    <td class="px-4 font-weight-bold">#<?php echo $l['id']; ?></td>
                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($l['admin_id']); ?></span></td>
                    <td>
                      <span class="badge <?php echo in_array($l['action'],['CREDIT','USER_STATUS_CHANGE'])?'badge-success':'badge-warning'; ?> px-2 py-1">
                        <?php echo htmlspecialchars($l['action']); ?>
                      </span>
                    </td>
                    <td>
                      <?php if ($l['target_user_id']): ?>
                        <strong><?php echo htmlspecialchars($l['target_user_id']); ?></strong>
                        <div class="small text-muted"><?php echo htmlspecialchars($l['target_username'] ?? ''); ?></div>
                      <?php else: ?>
                        <span class="text-muted">SYSTEM</span>
                      <?php endif; ?>
                    </td>
                    <td><code><?php echo htmlspecialchars($l['wallet_type'] ?? 'N/A'); ?></code></td>
                    <td class="font-weight-bold text-primary">₹<?php echo number_format((float)$l['amount'], 2); ?></td>
                    <td class="small">₹<?php echo number_format((float)$l['previous_balance'], 2); ?> → <strong>₹<?php echo number_format((float)$l['new_balance'], 2); ?></strong></td>
                    <td style="max-width:250px;" class="small text-muted">
                      <div><?php echo htmlspecialchars($l['reason'] ?? ''); ?></div>
                      <?php if ($l['reference_id']): ?><div class="badge badge-light">Ref: <?php echo htmlspecialchars($l['reference_id']); ?></div><?php endif; ?>
                    </td>
                    <td class="px-4 small text-muted"><?php echo date('d-M-Y H:i:s', strtotime($l['created_at'])); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            <ul class="pagination mb-0">
              <?php for ($p=1; $p<=$totalPages; $p++): ?>
                <li class="page-item <?php echo $p===$page?'active':''; ?>">
                  <a class="page-link" href="?tab=audit&page=<?php echo $p; ?>&user_id=<?php echo urlencode($filter_user); ?>&action=<?php echo urlencode($filter_action); ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</div>
</div>

<?php include 'common/footer.php'; ?>

<script>
$(document).ready(function() {
  $('.control-toggle-switch').on('change', function() {
    let key = $(this).data('key');
    let val = $(this).is(':checked') ? '1' : '0';
    let switchElem = $(this);

    $.ajax({
      url: 'admin_control_action.php',
      type: 'POST',
      data: {
        action: 'toggle_system_control',
        key: key,
        value: val
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          let badge = $('.status-badge-' + key);
          if (val === '1') {
            badge.removeClass('badge-danger').addClass('badge-success').text('ENABLED');
          } else {
            badge.removeClass('badge-success').addClass('badge-danger').text('DISABLED');
          }
          alert(res.message);
        } else {
          alert('Error: ' + res.message);
          switchElem.prop('checked', !switchElem.is(':checked'));
        }
      },
      error: function() {
        alert('Server error updating setting control!');
        switchElem.prop('checked', !switchElem.is(':checked'));
      }
    });
  });
});
</script>
</body>
</html>
