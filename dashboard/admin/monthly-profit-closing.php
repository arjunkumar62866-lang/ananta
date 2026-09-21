<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php'; 

// Fetch previous closings history
$historyStmt = $pdo->prepare("SELECT * FROM tbl_monthly_closing ORDER BY id DESC");
$historyStmt->execute();
$closings = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - MONTHLY PROFIT CLOSING REDESIGN
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-admin-dashboard,
body.bg-theme,
body.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

#wrapper {
    background: #f4f6f8 !important;
    min-height: 100vh !important;
}

.content-wrapper {
    background-color: #f4f6f8 !important;
    padding-top: 85px !important;
    padding-bottom: 60px !important;
}

.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(16, 185, 129, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #10b981 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 20px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
}

.form-group label {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
}

.form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

.btn-preview-custom {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 28px !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.2s ease !important;
}

.btn-preview-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35) !important;
    color: #ffffff !important;
}

.preview-stat-box {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 16px !important;
}

/* Table styling for closing history */
.table-responsive {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.table {
    margin-bottom: 0 !important;
    color: #0f172a !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    border-bottom: 2px solid #e2e8f0 !important;
    border-top: none !important;
    padding: 14px 16px !important;
    white-space: nowrap;
}

.table tbody td {
    padding: 14px 16px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #0f172a !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Banner Card -->
        <div class="card income-header-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="income-header-icon">
                    <i class="fa fa-calculator"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Monthly Profit Income Closing</h3>
                    <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Calculate and distribute monthly profit returns and downline profit sharing commissions.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar">
                        <div class="card-header-title">
                            <h4>Run New Closing Batch</h4>
                            <p>Select month and enter custom profit percentage</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="closingForm">
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="font-weight-bold">Select Closing Month</label>
                                    <input type="month" name="closing_month" id="closing_month" class="form-control" value="<?php echo date('Y-m'); ?>" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="font-weight-bold">Closing Date</label>
                                    <input type="date" name="closing_date" id="closing_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="font-weight-bold">Custom Profit Percentage (%)</label>
                                    <input type="number" step="0.01" min="0.01" max="100" name="profit_percentage" id="profit_percentage" class="form-control" placeholder="e.g. 4.5, 5.0, 5.5, 6.0" required>
                                    <small style="color: #64748b; font-weight: 500;">Enter profit % for this monthly closing run.</small>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="button" id="btnPreview" class="btn btn-preview-custom">
                                    <i class="fa fa-eye me-2"></i> Preview Calculation
                                </button>
                            </div>
                        </form>
                        
                        <div id="responseMessage" class="mt-3"></div>

                        <!-- Preview Container -->
                        <div id="previewCard" class="card mt-4 border-info d-none" style="border-radius: 16px; overflow: hidden;">
                            <div class="card-header bg-info text-white font-weight-bold p-3">
                                <i class="fa fa-list-alt me-2"></i> Closing Preview Verification
                            </div>
                            <div class="card-body p-4">
                                <div class="row text-center">
                                    <div class="col-md-4 mb-3">
                                        <div class="preview-stat-box">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Closing Month</span>
                                            <h4 class="font-weight-bold text-primary mb-0" id="prevMonth">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="preview-stat-box">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Profit Rate</span>
                                            <h4 class="font-weight-bold text-primary mb-0" id="prevRate">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="preview-stat-box">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Eligible Investment</span>
                                            <h4 class="font-weight-bold text-success mb-0" id="prevInv">-</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="preview-stat-box">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Profit Generated</span>
                                            <h4 class="font-weight-bold text-warning mb-0" id="prevProfit">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="preview-stat-box">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Estimated Profit Sharing</span>
                                            <h4 class="font-weight-bold text-info mb-0" id="prevSharing">-</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 border rounded" style="background: #ffffff;">
                                            <span style="color: #64748b; font-weight: 600;">Eligible Investments Count:</span>
                                            <strong class="text-dark ms-2" id="prevInvCount">-</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 border rounded" style="background: #ffffff;">
                                            <span style="color: #64748b; font-weight: 600;">Eligible Users Count:</span>
                                            <strong class="text-dark ms-2" id="prevUserCount">-</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0" style="border-radius: 10px;">
                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                    <strong>Important:</strong> Please verify the details above carefully before confirming. Once confirmed, profit will be credited to users' <strong>Profit Income Wallet</strong> and <strong>Profit Sharing</strong> will be distributed.
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" id="btnConfirm" class="btn btn-success btn-lg px-5 font-weight-bold" style="border-radius: 12px;">
                                        <i class="fa fa-check-circle me-2"></i> Confirm & Process Closing
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar">
                        <div class="card-header-title">
                            <h4>Monthly Profit Closing History & Audit Record</h4>
                            <p>Log of previous closing executions</p>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Closing Month</th>
                                        <th>Closing Date</th>
                                        <th>Profit %</th>
                                        <th>Eligible Investment</th>
                                        <th>Total Profit Credit</th>
                                        <th>Eligible Users</th>
                                        <th>Processed By</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($closings)): ?>
                                        <?php foreach ($closings as $idx => $c): ?>
                                            <tr>
                                                <td><?php echo $idx + 1; ?></td>
                                                <td><span class="badge bg-primary text-white px-2 py-1"><?php echo htmlspecialchars($c['closing_month']); ?></span></td>
                                                <td><?php echo htmlspecialchars($c['closing_date']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($c['profit_percentage']); ?>%</strong></td>
                                                <td><?php echo $hmcurrency . ' ' . number_format($c['total_eligible_investment'], 2); ?></td>
                                                <td><strong class="text-success"><?php echo $hmcurrency . ' ' . number_format($c['total_profit_paid'], 2); ?></strong></td>
                                                <td><?php echo (int)$c['eligible_user_count']; ?></td>
                                                <td><span class="badge bg-info text-white px-2 py-1"><?php echo htmlspecialchars($c['processed_by'] ?? 'Admin'); ?></span></td>
                                                <td><span class="badge bg-success text-white px-2 py-1"><?php echo htmlspecialchars($c['status']); ?></span></td>
                                                <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-info btnViewDetails font-weight-bold" data-id="<?php echo $c['id']; ?>">
                                                        <i class="fa fa-info-circle me-1"></i> Details
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-muted">No monthly profit closing records found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal for Viewing Closing Audit Details -->
<div class="modal fade" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="modalDetailsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title text-white font-weight-bold" id="modalDetailsLabel"><i class="fa fa-file-text-o me-2"></i> Closing Audit Record Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-dark" id="modalDetailsBody">
        <div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    let currentPreviewData = null;

    $('#btnPreview').click(function(e){
        e.preventDefault();
        let month = $('#closing_month').val();
        let rate = $('#profit_percentage').val();

        if(!month || !rate) {
            $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle me-1"></i> Please select closing month and enter profit percentage.</div>');
            return;
        }

        $('#responseMessage').empty();
        $('#previewCard').addClass('d-none');
        $('#btnPreview').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Calculating...');

        $.ajax({
            url: 'monthly_closing_action.php',
            type: 'GET',
            data: { action: 'preview', closing_month: month, profit_percentage: rate },
            dataType: 'json',
            success: function(res) {
                $('#btnPreview').prop('disabled', false).html('<i class="fa fa-eye me-2"></i> Preview Calculation');
                
                if (res.status === 'already_closed') {
                    $('#responseMessage').html('<div class="alert alert-warning font-weight-bold"><i class="fa fa-ban me-2"></i> ' + res.message + '</div>');
                    return;
                }

                if (res.status === 'error') {
                    $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle me-2"></i> ' + res.message + '</div>');
                    return;
                }

                currentPreviewData = res;
                $('#prevMonth').text(res.closing_month);
                $('#prevRate').text(res.profit_percentage + '%');
                $('#prevInv').text('<?php echo $hmcurrency; ?> ' + Number(res.total_eligible_investment).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#prevProfit').text('<?php echo $hmcurrency; ?> ' + Number(res.expected_total_profit).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#prevSharing').text('<?php echo $hmcurrency; ?> ' + Number(res.expected_profit_sharing).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#prevInvCount').text(res.eligible_investment_count);
                $('#prevUserCount').text(res.eligible_user_count);

                $('#previewCard').removeClass('d-none');
            },
            error: function() {
                $('#btnPreview').prop('disabled', false).html('<i class="fa fa-eye me-2"></i> Preview Calculation');
                $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle me-2"></i> Failed to communicate with server. Please try again.</div>');
            }
        });
    });

    $('#btnConfirm').click(function(e){
        e.preventDefault();
        if(!currentPreviewData) return;

        let confirmMsg = "Are you sure you want to process Monthly Profit Closing for " + currentPreviewData.closing_month + "?\n\n" +
                         "Closing Date: " + $('#closing_date').val() + "\n" +
                         "Profit Rate: " + currentPreviewData.profit_percentage + "%\n" +
                         "Eligible Investment: <?php echo $hmcurrency; ?> " + Number(currentPreviewData.total_eligible_investment).toLocaleString('en-IN', {minimumFractionDigits: 2}) + "\n" +
                         "Profit Generated: <?php echo $hmcurrency; ?> " + Number(currentPreviewData.expected_total_profit).toLocaleString('en-IN', {minimumFractionDigits: 2}) + "\n" +
                         "Profit Sharing: <?php echo $hmcurrency; ?> " + Number(currentPreviewData.expected_profit_sharing).toLocaleString('en-IN', {minimumFractionDigits: 2}) + "\n" +
                         "Eligible Users: " + currentPreviewData.eligible_user_count + "\n" +
                         "Eligible Investments: " + currentPreviewData.eligible_investment_count + "\n\n" +
                         "This action will credit user wallets and cannot be undone.";

        if(!confirm(confirmMsg)) {
            return;
        }

        $('#btnConfirm').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Processing Closing...');

        $.ajax({
            url: 'monthly_closing_action.php',
            type: 'POST',
            data: {
                action: 'process',
                closing_month: currentPreviewData.closing_month,
                closing_date: $('#closing_date').val(),
                profit_percentage: currentPreviewData.profit_percentage
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    $('#btnConfirm').prop('disabled', false).html('<i class="fa fa-check-circle me-2"></i> Confirm & Process Closing');
                    $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle me-2"></i> ' + res.message + '</div>');
                }
            },
            error: function() {
                $('#btnConfirm').prop('disabled', false).html('<i class="fa fa-check-circle me-2"></i> Confirm & Process Closing');
                $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle me-2"></i> Processing failed on server.</div>');
            }
        });
    });

    $(document).on('click', '.btnViewDetails', function(){
        let id = $(arguments[0].currentTarget).data('id');
        $('#modalDetailsBody').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading audit record...</div>');
        $('#modalDetails').modal('show');

        $.ajax({
            url: 'monthly_closing_action.php',
            type: 'GET',
            data: { action: 'details', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    let c = res.closing;
                    let html = '<table class="table table-bordered">' +
                        '<tr><th>Closing Record ID</th><td>#' + c.id + '</td></tr>' +
                        '<tr><th>Closing Month</th><td><span class="badge bg-primary text-white px-2 py-1">' + c.closing_month + '</span></td></tr>' +
                        '<tr><th>Closing Date</th><td>' + c.closing_date + '</td></tr>' +
                        '<tr><th>Profit Percentage</th><td><strong>' + c.profit_percentage + '%</strong></td></tr>' +
                        '<tr><th>Total Eligible Investment</th><td><?php echo $hmcurrency; ?> ' + Number(c.total_eligible_investment).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td></tr>' +
                        '<tr><th>Total Profit Generated / Paid</th><td><strong class="text-success"><?php echo $hmcurrency; ?> ' + Number(c.total_profit_paid).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong></td></tr>' +
                        '<tr><th>Total Profit Sharing Distributed</th><td><strong class="text-info"><?php echo $hmcurrency; ?> ' + Number(res.total_profit_sharing).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong></td></tr>' +
                        '<tr><th>Eligible Users Count</th><td>' + c.eligible_user_count + '</td></tr>' +
                        '<tr><th>Eligible Investment Records Count</th><td>' + c.eligible_investment_count + '</td></tr>' +
                        '<tr><th>Status</th><td><span class="badge bg-success text-white px-2 py-1">' + c.status + '</span></td></tr>' +
                        '<tr><th>Processed By</th><td>' + (c.processed_by ? c.processed_by : 'Admin') + '</td></tr>' +
                        '<tr><th>Timestamp</th><td>' + c.created_at + '</td></tr>' +
                        '</table>';
                    $('#modalDetailsBody').html(html);
                } else {
                    $('#modalDetailsBody').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            },
            error: function() {
                $('#modalDetailsBody').html('<div class="alert alert-danger">Failed to fetch details.</div>');
            }
        });
    });
});
</script>

</body>
</html>
