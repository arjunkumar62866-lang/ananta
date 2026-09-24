<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$admin_id = $_SESSION['auserid'] ?? 'AN1290';
$status_filter = trim($_GET['status'] ?? '');
$ticket_id = (int)($_GET['ticket_id'] ?? 0);

// Handle Ticket Reply & Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply_ticket') {
    $t_id   = (int)($_POST['ticket_id'] ?? 0);
    $reply  = trim($_POST['reply_message'] ?? '');
    $new_st = trim($_POST['status'] ?? 'RESOLVED');

    if ($t_id > 0 && !empty($reply)) {
        // Insert into ticket replies table
        $insReply = $pdo->prepare("INSERT INTO tbl_ticket_replies (ticket_id, sender_type, sender_id, message) VALUES (:tid, 'ADMIN', :aid, :msg)");
        $insReply->execute([':tid' => $t_id, ':aid' => $admin_id, ':msg' => $reply]);

        // Update main ticket record
        $updTicket = $pdo->prepare("UPDATE tbl_support_tickets SET status = :st, admin_reply = :reply, replied_by = :aid, replied_at = NOW() WHERE id = :tid");
        $updTicket->execute([':st' => $new_st, ':reply' => $reply, ':aid' => $admin_id, ':tid' => $t_id]);

        logAdminAuditAction($admin_id, 'REPLY_SUPPORT_TICKET', null, 0.00, 'support_ticket', 0, 0, "Replied to ticket #{$t_id} (Status: {$new_st})", (string)$t_id, $pdo);

        echo '<script>alert("Ticket reply posted and status updated successfully!"); window.location.href="support_tickets.php?ticket_id=' . $t_id . '";</script>';
        exit;
    }
}

// Fetch single ticket details if selected
$selectedTicket = null;
$ticketReplies = [];
if ($ticket_id > 0) {
    $stmtT = $pdo->prepare("SELECT t.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile FROM tbl_support_tickets t LEFT JOIN user u ON t.user_id = u.userid WHERE t.id = :tid LIMIT 1");
    $stmtT->execute([':tid' => $ticket_id]);
    $selectedTicket = $stmtT->fetch(PDO::FETCH_ASSOC);

    if ($selectedTicket) {
        $stmtR = $pdo->prepare("SELECT * FROM tbl_ticket_replies WHERE ticket_id = :tid ORDER BY id ASC");
        $stmtR->execute([':tid' => $ticket_id]);
        $ticketReplies = $stmtR->fetchAll(PDO::FETCH_ASSOC);
    }
}

// List all tickets
$whereClause = " WHERE 1=1";
$params = [];
if (!empty($status_filter)) {
    $whereClause .= " AND t.status = :st";
    $params[':st'] = strtoupper($status_filter);
}

$query = "SELECT t.*, u.name as user_name FROM tbl_support_tickets t LEFT JOIN user u ON t.user_id = u.userid {$whereClause} ORDER BY t.id DESC LIMIT 200";
$stmtList = $pdo->prepare($query);
$stmtList->execute($params);
$tickets = $stmtList->fetchAll(PDO::FETCH_ASSOC);

$openCount     = (int)$pdo->query("SELECT COUNT(*) FROM tbl_support_tickets WHERE status='OPEN'")->fetchColumn();
$pendingCount  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_support_tickets WHERE status='PENDING'")->fetchColumn();
$resolvedCount = (int)$pdo->query("SELECT COUNT(*) FROM tbl_support_tickets WHERE status='RESOLVED'")->fetchColumn();
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
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">TICKET SUPPORT CENTRE</span>
          <h3 class="mb-1 text-white font-weight-bold">Member Support Tickets & Helpdesk</h3>
          <p class="mb-0 text-white-50 small">Manage open, pending, and resolved support tickets, review user queries, and send admin responses.</p>
        </div>
        <div>
          <a href="user_enquiry.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-envelope-o mr-1"></i> Website Contact Enquiries
          </a>
        </div>
      </div>
    </div>

    <!-- Ticket Summary Row -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <a href="?status=OPEN" class="text-decoration-none">
          <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">OPEN TICKETS</span>
            <h3 class="mb-0 font-weight-bold text-danger"><?php echo number_format($openCount); ?></h3>
          </div>
        </a>
      </div>
      <div class="col-md-4 mb-3">
        <a href="?status=PENDING" class="text-decoration-none">
          <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">PENDING TICKETS</span>
            <h3 class="mb-0 font-weight-bold text-warning"><?php echo number_format($pendingCount); ?></h3>
          </div>
        </a>
      </div>
      <div class="col-md-4 mb-3">
        <a href="?status=RESOLVED" class="text-decoration-none">
          <div class="p-4 bg-white border rounded-lg shadow-sm" style="border-radius:16px; border-color:#e2e8f0 !important;">
            <span class="text-muted small font-weight-bold d-block mb-1">RESOLVED TICKETS</span>
            <h3 class="mb-0 font-weight-bold text-success"><?php echo number_format($resolvedCount); ?></h3>
          </div>
        </a>
      </div>
    </div>

    <?php if ($selectedTicket): ?>
      <!-- Single Ticket View & Reply Thread -->
      <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
          <div>
            <h5 class="mb-1 font-weight-bold text-dark"><i class="fa fa-ticket text-primary mr-2"></i> Ticket #<?php echo htmlspecialchars($selectedTicket['ticket_no']); ?>: <?php echo htmlspecialchars($selectedTicket['subject']); ?></h5>
            <span class="small text-muted">Submitted by <strong><?php echo htmlspecialchars($selectedTicket['user_name']); ?> (<?php echo htmlspecialchars($selectedTicket['user_id']); ?>)</strong> | Category: <?php echo htmlspecialchars($selectedTicket['category']); ?></span>
          </div>
          <span class="badge <?php echo $selectedTicket['status']==='OPEN'?'badge-danger':($selectedTicket['status']==='PENDING'?'badge-warning':'badge-success'); ?> px-3 py-2" style="border-radius:100px; font-size:13px;">
            <?php echo htmlspecialchars($selectedTicket['status']); ?>
          </span>
        </div>
        <div class="card-body p-4">
          <!-- User Initial Message -->
          <div class="p-3 mb-3 border rounded-lg" style="background: #f8fafc; border-radius: 12px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <strong><i class="fa fa-user mr-1 text-primary"></i> <?php echo htmlspecialchars($selectedTicket['user_name']); ?> (User)</strong>
              <small class="text-muted"><?php echo date('d-M-Y H:i:s', strtotime($selectedTicket['created_at'])); ?></small>
            </div>
            <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($selectedTicket['message'])); ?></p>
          </div>

          <!-- Existing Conversation Replies -->
          <?php foreach ($ticketReplies as $r): ?>
            <div class="p-3 mb-3 border rounded-lg <?php echo $r['sender_type']==='ADMIN'?'bg-light border-primary':'bg-white'; ?>" style="border-radius: 12px; margin-left: <?php echo $r['sender_type']==='ADMIN'?'20px':'0'; ?>;">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <strong>
                  <i class="fa <?php echo $r['sender_type']==='ADMIN'?'fa-user-shield text-success':'fa-user text-primary'; ?> mr-1"></i>
                  <?php echo $r['sender_type']==='ADMIN'?'Ananta Admin Support':htmlspecialchars($selectedTicket['user_name']); ?>
                </strong>
                <small class="text-muted"><?php echo date('d-M-Y H:i:s', strtotime($r['created_at'])); ?></small>
              </div>
              <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($r['message'])); ?></p>
            </div>
          <?php endforeach; ?>

          <!-- Admin Reply Form -->
          <form method="POST" class="mt-4 pt-3 border-top">
            <input type="hidden" name="action" value="reply_ticket">
            <input type="hidden" name="ticket_id" value="<?php echo $selectedTicket['id']; ?>">
            
            <div class="form-group mb-3">
              <label class="font-weight-bold text-dark">Admin Response Message</label>
              <textarea name="reply_message" class="form-control" rows="4" placeholder="Type your response to the user..." style="border-radius:12px;" required></textarea>
            </div>

            <div class="row align-items-center">
              <div class="col-md-4 mb-2">
                <label class="font-weight-bold text-dark">Update Ticket Status</label>
                <select name="status" class="form-control" style="border-radius:10px;">
                  <option value="RESOLVED" selected>Mark RESOLVED</option>
                  <option value="PENDING">Mark PENDING</option>
                  <option value="OPEN">Keep OPEN</option>
                </select>
              </div>
              <div class="col-md-8 mb-2 d-flex justify-content-end gap-2">
                <a href="support_tickets.php" class="btn btn-secondary font-weight-bold px-4" style="border-radius:10px;">Back to Ticket List</a>
                <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius:10px; background:#0284c7; border:none;">
                  <i class="fa fa-paper-plane mr-1"></i> Send Reply & Update Ticket
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    <?php endif; ?>

    <!-- Ticket List Table -->
    <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-primary mr-2"></i> All Support Tickets</h5>
        <div class="btn-group">
          <a href="support_tickets.php" class="btn btn-sm <?php echo empty($status_filter)?'btn-primary':'btn-outline-primary'; ?>">All</a>
          <a href="support_tickets.php?status=OPEN" class="btn btn-sm <?php echo $status_filter==='OPEN'?'btn-danger':'btn-outline-danger'; ?>">Open</a>
          <a href="support_tickets.php?status=PENDING" class="btn btn-sm <?php echo $status_filter==='PENDING'?'btn-warning':'btn-outline-warning'; ?>">Pending</a>
          <a href="support_tickets.php?status=RESOLVED" class="btn btn-sm <?php echo $status_filter==='RESOLVED'?'btn-success':'btn-outline-success'; ?>">Resolved</a>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
              <tr>
                <th class="py-3 px-4">Ticket No</th>
                <th class="py-3">User ID</th>
                <th class="py-3">User Name</th>
                <th class="py-3">Category</th>
                <th class="py-3">Subject</th>
                <th class="py-3">Status</th>
                <th class="py-3">Created Date</th>
                <th class="py-3 px-4">Action</th>
              </tr>
            </thead>
            <tbody style="font-size: 13.5px; color: #0f172a;">
              <?php if (empty($tickets)): ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">No support tickets found.</td>
                </tr>
              <?php else: foreach ($tickets as $t): ?>
                <tr>
                  <td class="px-4 font-weight-bold"><code>#<?php echo htmlspecialchars($t['ticket_no']); ?></code></td>
                  <td><strong><?php echo htmlspecialchars($t['user_id']); ?></strong></td>
                  <td><?php echo htmlspecialchars($t['user_name']??'User'); ?></td>
                  <td><span class="badge badge-light"><?php echo htmlspecialchars($t['category']); ?></span></td>
                  <td><?php echo htmlspecialchars($t['subject']); ?></td>
                  <td>
                    <span class="badge <?php echo $t['status']==='OPEN'?'badge-danger':($t['status']==='PENDING'?'badge-warning':'badge-success'); ?> px-2 py-1">
                      <?php echo htmlspecialchars($t['status']); ?>
                    </span>
                  </td>
                  <td class="small text-muted"><?php echo date('d-M-Y H:i', strtotime($t['created_at'])); ?></td>
                  <td class="px-4">
                    <a href="?ticket_id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                      <i class="fa fa-reply mr-1"></i> View & Reply
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
