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

<body class="bg-theme bg-theme1">

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header text-center bg-primary text-white py-3">
                        <h4 class="mb-0 text-white"><i class="fa fa-calculator mr-2"></i>Monthly Profit Income Closing</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="closingForm">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold">Select Closing Month</label>
                                    <input type="month" name="closing_month" id="closing_month" class="form-control form-control-lg" value="<?php echo date('Y-m'); ?>" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold">Closing Date</label>
                                    <input type="date" name="closing_date" id="closing_date" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold">Custom Profit Percentage (%)</label>
                                    <input type="number" step="0.01" min="0.01" max="100" name="profit_percentage" id="profit_percentage" class="form-control form-control-lg" placeholder="e.g. 4.5, 5.0, 5.5, 6.0" required>
                                    <small class="text-muted">Enter custom percentage for this monthly closing run (supports decimals).</small>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="button" id="btnPreview" class="btn btn-info btn-lg px-4">
                                    <i class="fa fa-eye mr-2"></i> Preview Calculation
                                </button>
                            </div>
                        </form>
                        
                        <div id="responseMessage" class="mt-3"></div>

                        <!-- Preview Container -->
                        <div id="previewCard" class="card mt-4 border-info d-none">
                            <div class="card-header bg-info text-white font-weight-bold">
                                <i class="fa fa-list-alt mr-2"></i> Closing Preview Verification
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4 mb-3">
                                        <div class="p-3 bg-light rounded text-dark">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Closing Month</span>
                                            <h4 class="font-weight-bold text-primary mb-0" id="prevMonth">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="p-3 bg-light rounded text-dark">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Profit Rate</span>
                                            <h4 class="font-weight-bold text-primary mb-0" id="prevRate">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="p-3 bg-light rounded text-dark">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Eligible Investment</span>
                                            <h4 class="font-weight-bold text-success mb-0" id="prevInv">-</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 bg-light rounded text-dark border-warning">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Profit Generated</span>
                                            <h4 class="font-weight-bold text-warning mb-0" id="prevProfit">-</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 bg-light rounded text-dark border-info">
                                            <span class="d-block text-muted small uppercase font-weight-bold">Total Estimated Profit Sharing</span>
                                            <h4 class="font-weight-bold text-info mb-0" id="prevSharing">-</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 border rounded">
                                            <span class="text-muted">Eligible Investments Count:</span>
                                            <strong class="text-dark ml-2" id="prevInvCount">-</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 border rounded">
                                            <span class="text-muted">Eligible Users Count:</span>
                                            <strong class="text-dark ml-2" id="prevUserCount">-</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="fa fa-exclamation-triangle mr-2"></i>
                                    <strong>Important:</strong> Please verify the details above carefully before confirming. Once confirmed, profit will be credited to users' <strong>Profit Income Wallet</strong> and <strong>Profit Sharing</strong> will be distributed.
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" id="btnConfirm" class="btn btn-success btn-lg px-5 font-weight-bold">
                                        <i class="fa fa-check-circle mr-2"></i> Confirm & Process Closing
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
                <div class="card">
                    <div class="card-header font-weight-bold">
                        <i class="fa fa-history mr-2"></i> Monthly Profit Closing History & Audit Record
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center">
                                <thead class="thead-dark">
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
                                                <td><span class="badge badge-primary px-2 py-1"><?php echo htmlspecialchars($c['closing_month']); ?></span></td>
                                                <td><?php echo htmlspecialchars($c['closing_date']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($c['profit_percentage']); ?>%</strong></td>
                                                <td><?php echo $hmcurrency . ' ' . number_format($c['total_eligible_investment'], 2); ?></td>
                                                <td><strong class="text-success"><?php echo $hmcurrency . ' ' . number_format($c['total_profit_paid'], 2); ?></strong></td>
                                                <td><?php echo (int)$c['eligible_user_count']; ?></td>
                                                <td><span class="badge badge-info px-2 py-1"><?php echo htmlspecialchars($c['processed_by'] ?? 'Admin'); ?></span></td>
                                                <td><span class="badge badge-success px-2 py-1"><?php echo htmlspecialchars($c['status']); ?></span></td>
                                                <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-info btnViewDetails" data-id="<?php echo $c['id']; ?>">
                                                        <i class="fa fa-info-circle mr-1"></i> Details
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
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title text-white font-weight-bold" id="modalDetailsLabel"><i class="fa fa-file-text-o mr-2"></i> Closing Audit Record Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-dark" id="modalDetailsBody">
        <div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle mr-1"></i> Please select closing month and enter profit percentage.</div>');
            return;
        }

        $('#responseMessage').empty();
        $('#previewCard').addClass('d-none');
        $('#btnPreview').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Calculating...');

        $.ajax({
            url: 'monthly_closing_action.php',
            type: 'GET',
            data: { action: 'preview', closing_month: month, profit_percentage: rate },
            dataType: 'json',
            success: function(res) {
                $('#btnPreview').prop('disabled', false).html('<i class="fa fa-eye mr-2"></i> Preview Calculation');
                
                if (res.status === 'already_closed') {
                    $('#responseMessage').html('<div class="alert alert-warning font-weight-bold"><i class="fa fa-ban mr-2"></i> ' + res.message + '</div>');
                    return;
                }

                if (res.status === 'error') {
                    $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle mr-2"></i> ' + res.message + '</div>');
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
                $('#btnPreview').prop('disabled', false).html('<i class="fa fa-eye mr-2"></i> Preview Calculation');
                $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle mr-2"></i> Failed to communicate with server. Please try again.</div>');
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

        $('#btnConfirm').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing Closing...');

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
                    $('#btnConfirm').prop('disabled', false).html('<i class="fa fa-check-circle mr-2"></i> Confirm & Process Closing');
                    $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-times-circle mr-2"></i> ' + res.message + '</div>');
                }
            },
            error: function() {
                $('#btnConfirm').prop('disabled', false).html('<i class="fa fa-check-circle mr-2"></i> Confirm & Process Closing');
                $('#responseMessage').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle mr-2"></i> Processing failed on server.</div>');
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
                        '<tr><th>Closing Month</th><td><span class="badge badge-primary px-2 py-1">' + c.closing_month + '</span></td></tr>' +
                        '<tr><th>Closing Date</th><td>' + c.closing_date + '</td></tr>' +
                        '<tr><th>Profit Percentage</th><td><strong>' + c.profit_percentage + '%</strong></td></tr>' +
                        '<tr><th>Total Eligible Investment</th><td><?php echo $hmcurrency; ?> ' + Number(c.total_eligible_investment).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td></tr>' +
                        '<tr><th>Total Profit Generated / Paid</th><td><strong class="text-success"><?php echo $hmcurrency; ?> ' + Number(c.total_profit_paid).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong></td></tr>' +
                        '<tr><th>Total Profit Sharing Distributed</th><td><strong class="text-info"><?php echo $hmcurrency; ?> ' + Number(res.total_profit_sharing).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</strong></td></tr>' +
                        '<tr><th>Eligible Users Count</th><td>' + c.eligible_user_count + '</td></tr>' +
                        '<tr><th>Eligible Investment Records Count</th><td>' + c.eligible_investment_count + '</td></tr>' +
                        '<tr><th>Status</th><td><span class="badge badge-success px-2 py-1">' + c.status + '</span></td></tr>' +
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
