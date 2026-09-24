<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include __DIR__ . '/common/header.php';

$admin_id = $_SESSION['auserid'] ?? 'AN1290';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_notification') {
    $target_type = strtoupper(trim($_POST['target_type'] ?? 'GLOBAL'));
    $target_user = trim($_POST['target_user_id'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $message     = trim($_POST['message'] ?? '');

    if (!empty($title) && !empty($message)) {
        if ($target_type === 'USER' && empty($target_user)) {
            echo '<script>alert("Error: Target User ID is required for targeted notifications.");</script>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO tbl_system_notifications (target_type, target_user_id, title, message, created_by) VALUES (:type, :user, :title, :msg, :admin)");
            $stmt->execute([
                ':type'  => $target_type,
                ':user'  => ($target_type === 'USER' ? $target_user : null),
                ':title' => $title,
                ':msg'   => $message,
                ':admin' => $admin_id
            ]);

            logAdminAuditAction($admin_id, 'CREATE_NOTIFICATION', ($target_type === 'USER' ? $target_user : null), 0.00, 'notification', 0, 0, "Dispatched {$target_type} Notification: {$title}", null, $pdo);

            echo '<script>alert("Notification dispatched successfully!"); window.location.href="notification_centre.php";</script>';
            exit;
        }
    }
}

// Fetch historical notifications
$stmtNotif = $pdo->query("SELECT * FROM tbl_system_notifications ORDER BY id DESC LIMIT 100");
$notifications = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.form-control, select.form-control, textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    opacity: 1 !important;
}
.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}
select.form-control option {
    background-color: #ffffff !important;
    color: #0f172a !important;
}
.form-control::placeholder, textarea.form-control::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">NOTIFICATION CENTRE</span>
          <h3 class="mb-1 text-white font-weight-bold">System Announcements & User Notifications</h3>
          <p class="mb-0 text-white-50 small">Dispatch platform-wide global broadcasts or send targeted notifications to specific members.</p>
        </div>
      </div>
    </div>

    <!-- Dispatch Notification Form -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-paper-plane text-primary mr-2"></i> Compose & Dispatch New Notification</h5>
      </div>
      <div class="card-body p-4">
        <form method="POST">
          <input type="hidden" name="action" value="send_notification">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="font-weight-bold text-dark">Notification Scope / Target Group</label>
              <select name="target_type" id="target_type" class="form-control" style="border-radius:10px;" required>
                <option value="GLOBAL">Global Broadcast (All Members)</option>
                <option value="USER">User-Specific Targeted Notice</option>
              </select>
            </div>
            <div class="col-md-6 mb-3" id="target_user_field" style="display:none;">
              <label class="font-weight-bold text-dark">Target User ID</label>
              <input type="text" name="target_user_id" class="form-control" placeholder="Enter User ID (e.g. AN1290)" style="border-radius:10px;">
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark">Notification Title / Headline</label>
            <input type="text" name="title" class="form-control" placeholder="Enter notice headline..." style="border-radius:10px;" required>
          </div>

          <div class="form-group mb-4">
            <label class="font-weight-bold text-dark">Notification Content / Details</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Write notification content..." style="border-radius:12px;" required></textarea>
          </div>

          <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius:10px; background:#0284c7; border:none;">
            <i class="fa fa-send mr-1"></i> Dispatch Notification Now
          </button>
        </form>
      </div>
    </div>

    <!-- Notification History Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-history text-success mr-2"></i> Dispatched Notification History</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">ID</th>
                <th class="py-3">Scope</th>
                <th class="py-3">Target User</th>
                <th class="py-3">Title</th>
                <th class="py-3">Message</th>
                <th class="py-3">Sent By Admin</th>
                <th class="py-3 px-4">Date & Time</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($notifications)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">No system notifications dispatched yet.</td>
                </tr>
              <?php else: foreach ($notifications as $n): ?>
                <tr>
                  <td class="px-4 font-weight-bold">#<?php echo $n['id']; ?></td>
                  <td>
                    <span class="badge <?php echo $n['target_type']==='GLOBAL'?'badge-primary':'badge-info'; ?> px-2 py-1">
                      <?php echo htmlspecialchars($n['target_type']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($n['target_user_id'] ?? 'ALL MEMBERS'); ?></td>
                  <td><strong><?php echo htmlspecialchars($n['title']); ?></strong></td>
                  <td class="small text-muted" style="max-width:300px;"><?php echo htmlspecialchars($n['message']); ?></td>
                  <td><span class="badge badge-secondary"><?php echo htmlspecialchars($n['created_by']); ?></span></td>
                  <td class="px-4 small text-muted"><?php echo date('d-M-Y H:i:s', strtotime($n['created_at'])); ?></td>
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
  $('#target_type').on('change', function() {
    if ($(this).val() === 'USER') {
      $('#target_user_field').slideDown();
    } else {
      $('#target_user_field').slideUp();
    }
  });
});
</script>
</body>
</html>
