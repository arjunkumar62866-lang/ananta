<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include 'common/header.php';

$presetUserId = trim($_GET['user_id'] ?? '');
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* Centered Bottom Footer Styling */
.footer {
    position: relative !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    text-align: center !important;
    padding: 24px 15px !important;
    margin-top: 40px !important;
    background: transparent !important;
    border-top: 1px solid #e2e8f0 !important;
}
.footer .container {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
}
.footer .text-center {
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    text-align: center !important;
    width: 100% !important;
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Banner -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
      <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">TREE HIERARCHY MANAGEMENT</span>
          <h3 class="mb-1 text-white font-weight-bold"><i class="fa fa-sitemap mr-2"></i> Move Team & Subtree Placement</h3>
          <p class="mb-0 text-white-50 small">Re-attach a user and their complete downline team to a new parent position with zero data corruption or circular tree loops.</p>
        </div>
        <div>
          <a href="all_user.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fa fa-users mr-1"></i> Members Directory
          </a>
        </div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="row">
      <!-- Left Column: User & Parent Selection Form -->
      <div class="col-lg-6 mb-4">
        <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-user-circle text-primary mr-2"></i> Step 1: Select User & New Parent</h5>
          </div>
          <div class="card-body p-4">

            <!-- Target User Selection -->
            <div class="form-group mb-4">
              <label class="font-weight-bold text-dark small text-uppercase mb-2">Target User to Move</label>
              <div class="input-group">
                <input type="text" id="targetUserIdInput" class="form-control font-weight-bold" placeholder="Enter User ID (e.g. AN1001 or 1001)" value="<?php echo htmlspecialchars($presetUserId); ?>" style="border-radius:10px 0 0 10px;">
                <button type="button" class="btn btn-primary font-weight-bold px-3" onclick="fetchTargetUserDetails()" style="border-radius:0 10px 10px 0; background:#0284c7; border:none;">
                  <i class="fa fa-search mr-1"></i> Fetch Details
                </button>
              </div>
            </div>

            <!-- Target User Live Card -->
            <div id="targetUserCard" class="card p-3 mb-4 border d-none" style="background:#f8fafc; border-radius:14px; border-color:#cbd5e1 !important;">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted font-weight-bold small">Target Member Name:</span>
                <span class="font-weight-bold text-dark" id="tuName"></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted font-weight-bold small">User ID:</span>
                <span class="font-weight-bold text-primary" id="tuId"></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted font-weight-bold small">Current Parent:</span>
                <span class="font-weight-bold text-dark" id="tuParent"></span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted font-weight-bold small">Current Position:</span>
                <span class="badge badge-info px-2 py-1" id="tuPos"></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted font-weight-bold small">Complete Downline Team:</span>
                <span class="badge badge-success px-3 py-1 font-weight-bold" id="tuTeam" style="border-radius:100px;"></span>
              </div>
            </div>

            <!-- New Parent Selection -->
            <div class="form-group mb-4">
              <label class="font-weight-bold text-dark small text-uppercase mb-2">New Parent User ID</label>
              <div class="input-group">
                <input type="text" id="newParentIdInput" class="form-control font-weight-bold" placeholder="Enter New Parent User ID (e.g. AN1002)" style="border-radius:10px 0 0 10px;">
                <button type="button" class="btn btn-secondary font-weight-bold px-3" onclick="checkParentAvailability()" style="border-radius:0 10px 10px 0;">
                  <i class="fa fa-check-circle mr-1"></i> Check Slots
                </button>
              </div>
            </div>

            <!-- New Parent Live Card & Position Selection -->
            <div id="newParentCard" class="card p-3 mb-4 border d-none" style="background:#f0fdf4; border-radius:14px; border-color:#bbf7d0 !important;">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted font-weight-bold small">New Parent Name:</span>
                <span class="font-weight-bold text-dark" id="npName"></span>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted font-weight-bold small">New Parent ID:</span>
                <span class="font-weight-bold text-success" id="npId"></span>
              </div>

              <label class="font-weight-bold text-dark small text-uppercase mb-2">Select Attachment Position</label>
              <div class="d-flex gap-3">
                <div class="form-check flex-grow-1 p-3 border rounded bg-white" id="boxLeftSlot" style="border-radius:10px;">
                  <input class="form-check-input" type="radio" name="targetPosition" id="posLeft" value="LEFT" disabled>
                  <label class="form-check-label font-weight-bold text-dark" for="posLeft" style="cursor:pointer;">
                    LEFT Slot
                    <span class="d-block small" id="statusLeftSlot"></span>
                  </label>
                </div>
                <div class="form-check flex-grow-1 p-3 border rounded bg-white" id="boxRightSlot" style="border-radius:10px;">
                  <input class="form-check-input" type="radio" name="targetPosition" id="posRight" value="RIGHT" disabled>
                  <label class="form-check-label font-weight-bold text-dark" for="posRight" style="cursor:pointer;">
                    RIGHT Slot
                    <span class="d-block small" id="statusRightSlot"></span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Reason / Remarks -->
            <div class="form-group mb-4">
              <label class="font-weight-bold text-dark small text-uppercase mb-2">Admin Remarks / Reason</label>
              <textarea id="moveReasonInput" class="form-control" rows="2" placeholder="Mandatory remarks explaining the tree movement..." style="border-radius:10px;"></textarea>
            </div>

            <!-- Error / Warning Alerts -->
            <div id="formAlert" class="alert alert-danger d-none p-3 mb-4" style="border-radius:12px; font-size:13.5px;"></div>

            <!-- Action Button -->
            <button type="button" id="btnPreviewMove" class="btn btn-warning font-weight-bold w-100 py-3 text-dark" onclick="openMoveConfirmationModal()" disabled style="border-radius:12px; background:#f59e0b; border:none; box-shadow:0 4px 15px rgba(245,158,11,0.3);">
              <i class="fa fa-sitemap mr-1"></i> Preview & Confirm Team Move
            </button>

          </div>
        </div>
      </div>

      <!-- Right Column: Safety Rules & Visual Representation -->
      <div class="col-lg-6 mb-4">
        <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-shield text-success mr-2"></i> Atomic Move Protection Rules</h5>
          </div>
          <div class="card-body p-4" style="font-size:13.5px; color:#475569;">
            <div class="mb-3 d-flex align-items-start gap-2">
              <i class="fa fa-check-circle text-success mt-1"></i>
              <div><strong>Subtree Structure Preserved:</strong> Moving a user attaches their complete downline team automatically without altering internal children links.</div>
            </div>
            <div class="mb-3 d-flex align-items-start gap-2">
              <i class="fa fa-ban text-danger mt-1"></i>
              <div><strong>Circular Tree Loop Prevention:</strong> System strictly blocks moving a user under any member who exists in their own downline.</div>
            </div>
            <div class="mb-3 d-flex align-items-start gap-2">
              <i class="fa fa-lock text-primary mt-1"></i>
              <div><strong>Atomic Database Rollback:</strong> Move executes in a single isolated transaction. Any mismatch triggers an instant rollback.</div>
            </div>
            <div class="d-flex align-items-start gap-2">
              <i class="fa fa-file-text-o text-info mt-1"></i>
              <div><strong>Audit Logging:</strong> All tree movement events are permanently logged in the Admin Audit Trail with timestamps.</div>
            </div>
          </div>
        </div>

        <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
          <div class="card-header bg-white border-bottom p-4">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-info-circle text-info mr-2"></i> Structural Move Example</h5>
          </div>
          <div class="card-body p-4 text-center">
            <div class="p-3 border rounded bg-light mb-2 font-weight-bold text-muted small" style="border-radius:12px;">
              BEFORE MOVE: <br>Old Parent (A) &rarr; [LEFT] &rarr; User (B) &rarr; Downline (D, E, F...)
            </div>
            <div class="my-2 text-primary font-weight-bold" style="font-size:18px;">&dArr;</div>
            <div class="p-3 border rounded bg-light font-weight-bold text-success small" style="border-radius:12px;">
              AFTER MOVE: <br>Old Parent (A) &rarr; [LEFT] &rarr; EMPTY<br>New Parent (C) &rarr; [RIGHT] &rarr; User (B) &rarr; Downline (D, E, F...)
            </div>
          </div>
        </div>
      </div>
    </div> <!-- /row -->
  </div> <!-- /container-fluid -->

  <!-- Footer -->
  <?php include 'common/footer.php'; ?>
</div> <!-- /content-wrapper -->
</div> <!-- /wrapper -->

<!-- ================================================== -->
<!-- MOVE TEAM CONFIRMATION MODAL -->
<!-- ================================================== -->
<div class="modal fade" id="moveTeamModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(15,23,42,0.25);">
      <div class="modal-header border-bottom p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <h5 class="modal-title font-weight-bold text-white mb-0">
          <i class="fa fa-sitemap me-2 text-warning"></i> Confirm Move Team Action
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="outline:none; opacity:0.8;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-warning p-3 mb-4" style="border-radius:12px; background:#fffbeb; color:#b45309; font-size:13px; border:1px solid #fde68a;">
          <i class="fa fa-exclamation-triangle me-1 font-weight-bold"></i> Are you sure you want to move this user and their complete downline team to the selected new parent and position?
        </div>

        <div class="card p-3 border mb-4" style="background:#f8fafc; border-radius:14px;">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted font-weight-bold small">User to Move:</span>
            <span id="confirmTu" class="font-weight-bold text-primary"></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted font-weight-bold small">Current Parent:</span>
            <span id="confirmOldParent" class="font-weight-bold text-dark"></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted font-weight-bold small">New Parent:</span>
            <span id="confirmNp" class="font-weight-bold text-success"></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted font-weight-bold small">Attachment Position:</span>
            <span id="confirmPos" class="badge badge-primary px-3 py-1 font-weight-bold" style="border-radius:100px;"></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted font-weight-bold small">Total Team Members:</span>
            <span id="confirmTeam" class="font-weight-bold text-dark"></span>
          </div>
          <hr class="my-2">
          <div>
            <span class="text-muted font-weight-bold small d-block mb-1">Reason:</span>
            <div id="confirmReason" class="p-2 border rounded bg-white font-weight-semibold text-dark" style="font-size:13px;"></div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary font-weight-bold px-4 py-2" data-dismiss="modal" style="border-radius:10px;">Cancel</button>
          <button type="button" id="btnExecuteMove" class="btn btn-success font-weight-bold px-4 py-2" onclick="executeMoveTeamInTree()" style="border-radius:10px;">
            <i class="fa fa-check-circle me-1"></i> Confirm & Move Team
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var currentTargetData = null;
var currentParentData = null;

$(document).ready(function() {
    var preset = "<?php echo htmlspecialchars($presetUserId); ?>";
    if (preset !== "") {
        fetchTargetUserDetails();
    }
});

function fetchTargetUserDetails() {
    var uid = $.trim($('#targetUserIdInput').val());
    if (!uid) {
        alert("Please enter a Target User ID.");
        return;
    }

    $('#formAlert').addClass('d-none').text('');
    $('#targetUserCard').addClass('d-none');
    $('#btnPreviewMove').prop('disabled', true);

    $.ajax({
        url: 'admin_control_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'get_user_tree_details',
            user_id: uid
        },
        success: function(res) {
            if (res && res.status === 'success' && res.data) {
                currentTargetData = res.data;
                $('#tuName').text(res.data.name);
                $('#tuId').text(res.data.userid);
                $('#tuParent').text(res.data.current_parent);
                $('#tuPos').text(res.data.current_position);
                $('#tuTeam').text((res.data.downline_count + 1) + " Members Total");
                $('#targetUserCard').removeClass('d-none');

                if (currentParentData) {
                    checkParentAvailability();
                }
            } else {
                currentTargetData = null;
                alert(res.message || "User not found.");
            }
        },
        error: function() {
            currentTargetData = null;
            alert("Error fetching user tree details.");
        }
    });
}

function checkParentAvailability() {
    if (!currentTargetData) {
        alert("Please fetch target user details first.");
        return;
    }

    var npId = $.trim($('#newParentIdInput').val());
    if (!npId) {
        alert("Please enter a New Parent User ID.");
        return;
    }

    $('#formAlert').addClass('d-none').text('');
    $('#newParentCard').addClass('d-none');
    $('#btnPreviewMove').prop('disabled', true);

    $.ajax({
        url: 'admin_control_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'get_new_parent_availability',
            target_user_id: currentTargetData.userid,
            new_parent_id: npId
        },
        success: function(res) {
            if (res && res.status === 'success') {
                currentParentData = res.new_parent;
                $('#npName').text(res.new_parent.name);
                $('#npId').text(res.new_parent.userid);

                // Configure Left Slot
                if (res.left.valid) {
                    $('#posLeft').prop('disabled', false);
                    $('#statusLeftSlot').html('<span class="text-success font-weight-bold">Available Slot</span>');
                } else {
                    $('#posLeft').prop('disabled', true).prop('checked', false);
                    $('#statusLeftSlot').html('<span class="text-danger font-weight-bold">Occupied by ' + res.left.occupant + '</span>');
                }

                // Configure Right Slot
                if (res.right.valid) {
                    $('#posRight').prop('disabled', false);
                    $('#statusRightSlot').html('<span class="text-success font-weight-bold">Available Slot</span>');
                } else {
                    $('#posRight').prop('disabled', true).prop('checked', false);
                    $('#statusRightSlot').html('<span class="text-danger font-weight-bold">Occupied by ' + res.right.occupant + '</span>');
                }

                $('#newParentCard').removeClass('d-none');

                // Enable radio click trigger
                $('input[name="targetPosition"]').off('change').on('change', function() {
                    $('#btnPreviewMove').prop('disabled', false);
                });

            } else {
                currentParentData = null;
                $('#formAlert').removeClass('d-none').text(res.message || "Invalid new parent selection.");
            }
        },
        error: function() {
            currentParentData = null;
            alert("Error checking parent slot availability.");
        }
    });
}

function openMoveConfirmationModal() {
    var pos = $('input[name="targetPosition"]:checked').val();
    var reason = $.trim($('#moveReasonInput').val());

    if (!currentTargetData || !currentParentData || !pos) {
        alert("Please complete Target User, New Parent User, and Position selection.");
        return;
    }

    if (!reason || reason.length < 3) {
        $('#formAlert').removeClass('d-none').text("Please provide a mandatory reason for the team move.");
        return;
    }

    $('#confirmTu').text(currentTargetData.userid + " (" + currentTargetData.name + ")");
    $('#confirmOldParent').text(currentTargetData.current_parent + " [" + currentTargetData.current_position + "]");
    $('#confirmNp').text(currentParentData.userid + " (" + currentParentData.name + ")");
    $('#confirmPos').text(pos);
    $('#confirmTeam').text((currentTargetData.downline_count + 1) + " Members Total");
    $('#confirmReason').text(reason);

    if (typeof $.fn.modal === 'function') {
        $('#moveTeamModal').modal('show');
    } else {
        $('#moveTeamModal').addClass('show').css({ display: 'block', zIndex: 1050 });
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade show"></div>');
        }
    }
}

function closeMoveTeamModal() {
    if (typeof $.fn.modal === 'function') {
        $('#moveTeamModal').modal('hide');
    }
    $('#moveTeamModal').removeClass('show').css('display', 'none');
    $('.modal-backdrop').remove();
}

$(document).on('click', '#moveTeamModal .close, #moveTeamModal [data-dismiss="modal"]', function() {
    closeMoveTeamModal();
});

function executeMoveTeamInTree() {
    var pos = $('input[name="targetPosition"]:checked').val();
    var reason = $.trim($('#moveReasonInput').val());

    $('#btnExecuteMove').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Executing Move...');

    $.ajax({
        url: 'admin_control_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'move_team_in_tree',
            target_user_id: currentTargetData.userid,
            new_parent_id: currentParentData.userid,
            target_position: pos,
            reason: reason
        },
        success: function(res) {
            if (res && res.status === 'success') {
                alert(res.message);
                window.location.href = 'team_management.php';
            } else {
                alert(res.message || "Team move failed.");
                $('#btnExecuteMove').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Confirm & Move Team');
            }
        },
        error: function(xhr, status, err) {
            console.error("Move Team AJAX Error:", xhr.responseText);
            alert("Team move failed. Server response: " + (xhr.responseText || status || err));
            $('#btnExecuteMove').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Confirm & Move Team');
        }
    });
}
</script>

</body>
</html>
