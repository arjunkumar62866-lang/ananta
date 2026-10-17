<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ;

$id = $_GET['id'] ?? null;
$row = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_query WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
          <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
              <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">SUPPORT HELPDESK</span>
              <h3 class="mb-1 text-white font-weight-bold">View & Reply Support Enquiry</h3>
              <p class="mb-0 text-white-50 small">Respond directly to member ticket #<?php echo $id; ?>, update resolution status, and notify user.</p>
            </div>
            <div>
              <a href="user_enquiry.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fa fa-arrow-left mr-1"></i> Back to Enquiries
              </a>
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
                    <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-ticket text-primary mr-2"></i> Enquiry Details #<?php echo htmlspecialchars($id); ?></h5>
                        <?php
                            $st = $row['status'] ?? '0';
                            if ($st == '1') {
                                echo '<span class="badge badge-success px-3 py-2" style="border-radius:100px; font-size:13px;"><i class="fa fa-check-circle mr-1"></i> Resolved / Approved</span>';
                            } elseif ($st == '2') {
                                echo '<span class="badge badge-danger px-3 py-2" style="border-radius:100px; font-size:13px;"><i class="fa fa-times-circle mr-1"></i> Rejected</span>';
                            } else {
                                echo '<span class="badge badge-warning px-3 py-2" style="border-radius:100px; font-size:13px;"><i class="fa fa-clock-o mr-1"></i> Pending Review</span>';
                            }
                        ?>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($row): ?>
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3 border-right">
                                <span class="text-muted small d-block">USER ID</span>
                                <strong class="text-primary font-weight-bold" style="font-size:16px;"><?php echo htmlspecialchars($hmpre . $row['userid']); ?></strong>
                            </div>
                            <div class="col-md-3 mb-3 border-right">
                                <span class="text-muted small d-block">MEMBER NAME</span>
                                <strong class="text-dark font-weight-bold" style="font-size:15px;"><?php echo htmlspecialchars($row['username'] ?: 'User'); ?></strong>
                            </div>
                            <div class="col-md-3 mb-3 border-right">
                                <span class="text-muted small d-block">CONTACT (EMAIL / MOBILE)</span>
                                <div class="small font-weight-bold text-dark"><?php echo htmlspecialchars($row['mobile']); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <span class="text-muted small d-block">SUBMISSION DATE</span>
                                <strong class="text-secondary font-weight-bold"><?php echo htmlspecialchars($row['or_date']); ?></strong>
                            </div>
                        </div>

                        <!-- User Query Message Box -->
                        <div class="p-3 mb-4 rounded-lg" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px;">
                            <span class="badge badge-primary px-2 py-1 mb-2 font-weight-bold"><i class="fa fa-question-circle mr-1"></i> USER ENQUIRY SUBJECT: <?php echo htmlspecialchars($row['sub']); ?></span>
                            <p class="mb-0 text-dark" style="font-size: 14.5px; line-height: 1.6; white-space: pre-wrap;"><?php echo htmlspecialchars($row['message']); ?></p>
                        </div>

                        <!-- Previous Admin Reply if exists -->
                        <?php if (!empty($row['admin_reply'])): ?>
                        <div class="p-3 mb-4 rounded-lg" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fa fa-reply mr-1"></i> PREVIOUS ADMIN REPLY</span>
                                <small class="text-muted font-weight-bold"><?php echo !empty($row['reply_date']) ? date('d-M-Y H:i', strtotime($row['reply_date'])) : ''; ?></small>
                            </div>
                            <p class="mb-0 text-dark" style="font-size: 14px; white-space: pre-wrap;"><?php echo htmlspecialchars($row['admin_reply']); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Admin Reply & Resolve Form -->
                        <div class="card border-0" style="background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                            <div class="card-header bg-light p-3">
                                <h6 class="mb-0 font-weight-bold text-dark"><i class="fa fa-edit text-success mr-2"></i> Write Admin Response & Update Status</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="action-enquiry.php" method="POST">
                                    <input type="hidden" name="action" value="reply_enquiry">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark">Admin Response Note / Message <span class="text-danger">*</span></label>
                                        <textarea name="admin_reply" class="form-control" rows="4" placeholder="Write resolution note or answer for the user..." style="border-radius:10px;" required><?php echo htmlspecialchars($row['admin_reply'] ?? ''); ?></textarea>
                                        <small class="text-muted">This response note will be visible to the user in their Enquiry History and an instant in-app notification will be sent.</small>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col-md-4 mb-3">
                                            <label class="font-weight-bold text-dark">Enquiry Status</label>
                                            <select name="status" class="form-control" style="border-radius:10px;">
                                                <option value="1" <?php echo ($row['status'] == '1') ? 'selected' : ''; ?>>Mark Resolved / Approved (1)</option>
                                                <option value="0" <?php echo ($row['status'] == '0') ? 'selected' : ''; ?>>Keep Pending (0)</option>
                                                <option value="2" <?php echo ($row['status'] == '2') ? 'selected' : ''; ?>>Reject Enquiry (2)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8 mb-3 d-flex justify-content-end gap-2 pt-md-4">
                                            <a href="user_enquiry.php" class="btn btn-secondary font-weight-bold px-3" style="border-radius:10px;">Cancel</a>
                                            <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius:10px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border:none;">
                                                <i class="fa fa-paper-plane mr-1"></i> Send Reply & Update Status
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-warning">No enquiry record found with ID #<?php echo htmlspecialchars($id); ?>.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div><!--End Row-->

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

    </div>
    <!-- End container-fluid-->
</div>
<!--End content-wrapper-->
   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	<!--Start footer-->
	<?php include 'common/footer.php' ?>
	<!--End footer-->
	
	
   
  </div><!--End wrapper-->
<script>
    function getfunctionFees() {
    $.ajax({
        url: "topup_detail.php",
        type: "POST",
        data: { p_id: $('#sponser_id').val() },
        dataType: "json",
        success: function (jsonStr) {
            if (jsonStr.error) {
                alert(jsonStr.error);
            } else {
                $('#tst_sponsername').text(jsonStr.name);
                $('#tst_sponserid').text(jsonStr.sponserid);
                $('#status').text(jsonStr.status);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

</script>
	
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
