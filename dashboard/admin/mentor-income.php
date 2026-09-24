<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - MENTOR INCOME SYSTEM (REQ #14 & #15)
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
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.10) 0%, rgba(2, 132, 199, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(16, 185, 129, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    flex-shrink: 0;
}

.stat-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 20px 24px;
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
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.btn-export-excel {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 8px 18px !important;
    font-size: 13.5px !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.btn-adjust-modal {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 8px 18px !important;
    font-size: 13.5px !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.nav-tabs-custom {
    border-bottom: 2px solid #e2e8f0;
    gap: 12px;
}

.nav-tabs-custom .nav-link {
    border: none;
    background: transparent;
    color: #64748b;
    font-weight: 700;
    font-size: 14px;
    padding: 12px 20px;
    border-radius: 12px 12px 0 0;
    transition: all 0.2s;
}

.nav-tabs-custom .nav-link.active {
    color: #10b981;
    background: #ffffff;
    border-bottom: 3px solid #10b981;
}

/* DataTables Light Fintech Table Styling */
.table-responsive {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

table.dataTable.no-footer {
    border-bottom: 1px solid #e2e8f0 !important;
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
    font-size: 13.5px !important;
    font-weight: 600 !important;
}

.dataTables_wrapper {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 14px !important;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-weight: 600 !important;
    outline: none !important;
}
</style>

    <!-- Loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Header Banner Card -->
                <div class="card income-header-card p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="income-header-icon">
                                <i class="fa fa-handshake-o"></i>
                            </div>
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Mentor Income Management (Requirements #14 & #15)</h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">2% Mentor Income distribution & financial adjustment admin control portal.</p>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-adjust-modal" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                <i class="fa fa-pencil-square-o"></i> Authorized Wallet Adjustment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Program Configuration Card -->
                <div class="card ananta-fintech-card mb-4 p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <h5 class="fw-bold mb-3" style="color: #0f172a;"><i class="fa fa-cogs text-success me-2"></i> Program Configuration & Validation Rules</h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Mentor Income Rate</span>
                                <span class="h5 fw-extrabold text-success mb-0">2.00% of Monthly Income</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Contribution Source</span>
                                <span class="h6 fw-bold text-dark mb-0">tbl_mentor_direct_contribution (Manual Admin Input)</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Total Contribution Rule</span>
                                <span class="h5 fw-extrabold text-primary mb-0">MUST Equal Exactly 100%</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Validation Failure Policy</span>
                                <span class="h6 fw-bold text-danger mb-0">BLOCKED (Zero Payouts Executed)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fa fa-money"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL MENTOR DISTRIBUTED</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalPaid" style="color: #10b981;"><?php echo $hmcurrency; ?> 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL PAYOUT RECORDS</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalRecords" style="color: #0f172a;">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                                <i class="fa fa-user-circle"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">BENEFICIARY DIRECT USERS</span>
                                <h4 class="mb-0 fw-extrabold" id="statDirectUsers" style="color: #9333ea;">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">VALIDATION BLOCKED MENTORS</span>
                                <h4 class="mb-0 fw-extrabold" id="statBlockedMentors" style="color: #dc2626;">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" id="mentorTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="manager-tab" data-bs-toggle="tab" data-bs-target="#managerTabContent" type="button" role="tab">
                            <i class="fa fa-sliders me-1"></i> Direct User Contribution % Manager
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTabContent" type="button" role="tab">
                            <i class="fa fa-history me-1"></i> Monthly Payout History & Ledger
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#auditTabContent" type="button" role="tab">
                            <i class="fa fa-shield me-1"></i> Admin Adjustments Audit Trail
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="mentorTabsContent">
                    <!-- Tab 1: Contribution Manager -->
                    <div class="tab-pane fade show active" id="managerTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Mentor Direct Contribution Manager</h4>
                                    <p>Select a Mentor User ID to view direct referrals and manage contribution percentages.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Mentor Search Form -->
                                <div class="row g-3 align-items-end mb-4">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold" style="color:#0f172a;">Mentor User ID</label>
                                        <input type="text" id="mentorSearchInput" class="form-control fw-bold" placeholder="e.g. 1001" style="border-radius:10px;">
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnLoadMentorDirects" class="btn text-white fw-bold w-100" style="background:#10b981; border-radius:10px; padding:9px;">
                                            <i class="fa fa-search me-1"></i> Load Direct Referrals
                                        </button>
                                    </div>
                                </div>

                                <!-- Contribution Editor Section -->
                                <div id="contributionSection" style="display:none;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-light rounded-3 mb-3 border">
                                        <div>
                                            <h6 class="mb-0 fw-bold" id="contribMentorTitle" style="color:#0f172a;">Mentor: --</h6>
                                            <small class="text-muted">Total sum of all direct user contributions must equal exactly 100.00%</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div id="validationBadgeContainer">
                                                <span class="badge bg-secondary fs-6 p-2">Total: 0.00%</span>
                                            </div>
                                            <button id="btnSaveAllContributions" class="btn text-white fw-bold" style="background:#0284c7; border-radius:10px;">
                                                <i class="fa fa-save me-1"></i> Save All Contributions
                                            </button>
                                        </div>
                                    </div>

                                    <form id="bulkContribForm">
                                        <input type="hidden" name="action" value="save_bulk_contributions">
                                        <input type="hidden" name="mentor_id" id="contribMentorIdInput">
                                        
                                        <div class="table-responsive mb-3">
                                            <table class="table table-hover table-bordered" id="contribTable">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th>Direct User ID</th>
                                                        <th>Direct User Name</th>
                                                        <th>Account Status</th>
                                                        <th>Contribution Percentage (%)</th>
                                                        <th>Last Updated</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Loaded via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Payout History -->
                    <div class="tab-pane fade" id="historyTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Monthly Mentor Income Payout Ledger</h4>
                                    <p>Live history of all 2% Mentor Income payouts generated during monthly closings.</p>
                                </div>
                                <button id="customExportBtn" class="btn btn-export-excel">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </button>
                            </div>

                            <!-- Filter Controls -->
                            <div class="p-3 bg-light border-bottom d-flex align-items-center flex-wrap gap-3">
                                <div>
                                    <label class="form-label mb-1 text-muted fw-bold" style="font-size:12px;">CLOSING MONTH FILTER</label>
                                    <input type="month" id="monthFilterInput" class="form-control form-control-sm fw-bold" style="width: 170px; border-radius: 8px;">
                                </div>
                                <div>
                                    <button id="btnApplyHistoryFilter" class="btn btn-sm text-white fw-bold mt-4" style="background:#0284c7; border-radius:8px;">
                                        <i class="fa fa-filter me-1"></i> Apply Filter
                                    </button>
                                    <button id="btnClearHistoryFilter" class="btn btn-sm btn-secondary fw-bold mt-4" style="border-radius:8px;">
                                        Clear
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="historyTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Beneficiary Direct</th>
                                                <th>Source Mentor</th>
                                                <th>Closing Month</th>
                                                <th>Mentor Monthly Income</th>
                                                <th>Rate</th>
                                                <th>Total Mentor Income</th>
                                                <th>Contrib %</th>
                                                <th>Payout Amount</th>
                                                <th>Status</th>
                                                <th>Credit Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables loads this -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Admin Adjustments Audit Trail -->
                    <div class="tab-pane fade" id="auditTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Admin Financial Adjustments Audit Log</h4>
                                    <p>Immutable log of all authorized CREDIT and DEBIT adjustments made to user Mentor Income Wallets.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="auditTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Admin User ID</th>
                                                <th>Target User ID</th>
                                                <th>Action Type</th>
                                                <th>Amount</th>
                                                <th>Previous Balance</th>
                                                <th>New Balance</th>
                                                <th>Mandatory Reason</th>
                                                <th>Reference</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables loads this -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'common/footer.php'; ?>
    </div>

    <!-- Financial Adjustment Modal (Req #15) -->
    <div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="adjustmentModalLabel"><i class="fa fa-pencil-square-o me-2"></i> Authorized Mentor Income Wallet Adjustment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adjustmentForm">
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 fw-bold mb-3" style="font-size: 13px;">
                            <i class="fa fa-info-circle me-1"></i> Affects <strong>mentor_income_wallet</strong> ONLY. All operations are logged to audit trail. Negative balance is strictly blocked.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Target User ID</label>
                            <input type="text" name="user_id" class="form-control fw-bold" placeholder="e.g. 1002" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Adjustment Type</label>
                            <select name="type" class="form-select fw-bold" required style="border-radius: 10px;">
                                <option value="CREDIT">CREDIT (+)</option>
                                <option value="DEBIT">DEBIT (-)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Amount (<?php echo $hmcurrency; ?>)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Mandatory Reason</label>
                            <textarea name="reason" class="form-control fw-bold" rows="2" placeholder="State reason for manual adjustment..." required style="border-radius: 10px;"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Reference / Note (Optional)</label>
                            <input type="text" name="reference" class="form-control fw-bold" placeholder="e.g. REF-12345" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                        <button type="submit" id="btnSubmitAdjustment" class="btn text-white fw-bold" style="background: #0284c7; border-radius: 10px;">Submit Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {
            let historyTable = $('#historyTable').DataTable({
                ajax: {
                    url: 'mentor_income_action.php',
                    type: 'GET',
                    data: function(d) {
                        d.action = 'get_schedules';
                        d.closing_month = $('#monthFilterInput').val();
                    },
                    dataSrc: function (json) {
                        if (json.status === 'success' && json.data) {
                            let totalPaid = 0;
                            let totalRecords = json.data.length;
                            let directUsers = new Set();

                            json.data.forEach(row => {
                                totalPaid += parseFloat(row.payout_amount || 0);
                                if (row.direct_user_id) directUsers.add(row.direct_user_id);
                            });

                            $('#statTotalPaid').text('<?php echo $hmcurrency; ?> ' + totalPaid.toLocaleString('en-IN', {minimumFractionDigits:2}));
                            $('#statTotalRecords').text(totalRecords);
                            $('#statDirectUsers').text(directUsers.size);

                            return json.data;
                        }
                        return [];
                    }
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => '<span style="font-weight:700; color:#64748b;">#' + (meta.row + 1) + '</span>' },
                    {
                        data: 'direct_user_id',
                        render: (data, type, row) => '<strong><?php echo $hmpre; ?>' + data + '</strong>' + (row.direct_user_name ? '<br><small class="text-muted">' + row.direct_user_name + '</small>' : '')
                    },
                    {
                        data: 'mentor_id',
                        render: (data, type, row) => '<span style="color:#0284c7; font-weight:600;"><?php echo $hmpre; ?>' + data + '</span>' + (row.mentor_name ? '<br><small class="text-muted">' + row.mentor_name + '</small>' : '')
                    },
                    {
                        data: 'closing_month',
                        render: (data) => '<span class="badge bg-light text-dark fw-bold border">' + data + '</span>'
                    },
                    {
                        data: 'mentor_monthly_income',
                        render: (data) => '<span style="font-weight:600; color:#475569;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>'
                    },
                    {
                        data: 'mentor_income_rate',
                        render: (data) => '<span style="font-weight:700; color:#0284c7;">' + parseFloat(data || 2.0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'total_mentor_income',
                        render: (data) => '<span style="font-weight:700; color:#9333ea;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'contribution_percentage',
                        render: (data) => '<span class="badge bg-info text-white fw-bold">' + parseFloat(data || 0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'payout_amount',
                        render: (data) => '<span style="font-weight:800; color:#10b981;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'status',
                        render: (data) => '<span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight:700; padding: 4px 10px; border-radius: 100px;">' + data + '</span>'
                    },
                    {
                        data: 'credited_at',
                        render: (data) => '<span style="color:#64748b; font-size:13px;">' + (data || 'Not Credited') + '</span>'
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    { extend: 'excelHtml5', title: 'Mentor_Income_Payout_Report' }
                ]
            });

            let auditTable = $('#auditTable').DataTable({
                ajax: {
                    url: 'mentor_income_action.php',
                    type: 'GET',
                    data: { action: 'get_audit_history' },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id', render: (data) => '#' + data },
                    { data: 'admin_name', render: (data, type, row) => (data || row.admin_id) },
                    { data: 'user_id', render: (data, type, row) => '<strong><?php echo $hmpre; ?>' + data + '</strong>' + (row.target_username ? '<br><small class="text-muted">' + row.target_username + '</small>' : '') },
                    {
                        data: 'action',
                        render: (data) => data === 'CREDIT' ? '<span class="badge bg-success">CREDIT (+)</span>' : '<span class="badge bg-danger">DEBIT (-)</span>'
                    },
                    { data: 'amount', render: (data) => '<?php echo $hmcurrency; ?> ' + parseFloat(data).toFixed(2) },
                    { data: 'previous_balance', render: (data) => '<?php echo $hmcurrency; ?> ' + parseFloat(data).toFixed(2) },
                    { data: 'new_balance', render: (data) => '<?php echo $hmcurrency; ?> ' + parseFloat(data).toFixed(2) },
                    { data: 'reason' },
                    { data: 'reference', render: (data) => data || '-' },
                    { data: 'created_at' }
                ],
                order: [[0, 'desc']],
                pageLength: 10
            });

            $('#btnApplyHistoryFilter').on('click', function() {
                historyTable.ajax.reload();
            });

            $('#btnClearHistoryFilter').on('click', function() {
                $('#monthFilterInput').val('');
                historyTable.ajax.reload();
            });

            $('#customExportBtn').on('click', function () {
                historyTable.button('.buttons-excel').trigger();
            });

            // Load Mentor Direct Referrals & Contributions
            $('#btnLoadMentorDirects').on('click', function() {
                let mentorId = $('#mentorSearchInput').val().trim();
                if (!mentorId) {
                    alert('Please enter a valid Mentor User ID.');
                    return;
                }

                $.getJSON('mentor_income_action.php', { action: 'get_mentor_directs', mentor_id: mentorId }, function(res) {
                    if (res.status === 'success' && res.data) {
                        $('#contribMentorTitle').text('Mentor ID: <?php echo $hmpre; ?>' + res.data.mentor_id);
                        $('#contribMentorIdInput').val(res.data.mentor_id);
                        $('#contributionSection').show();

                        let rows = '';
                        if (res.data.directs && res.data.directs.length > 0) {
                            res.data.directs.forEach(d => {
                                rows += `<tr>
                                    <td class="fw-bold"><?php echo $hmpre; ?>${d.direct_user_id}</td>
                                    <td>${d.direct_user_name}</td>
                                    <td><span class="badge ${d.is_active == 1 ? 'bg-success' : 'bg-secondary'}">${d.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm fw-bold contrib-pct-input" name="contributions[${d.direct_user_id}]" value="${parseFloat(d.contribution_percentage).toFixed(2)}" style="width:120px;">
                                    </td>
                                    <td><small class="text-muted">${d.updated_at || 'Never'}</small></td>
                                </tr>`;
                            });
                        } else {
                            rows = '<tr><td colspan="5" class="text-center text-muted">No direct referrals found for this Mentor.</td></tr>';
                        }
                        $('#contribTable tbody').html(rows);
                        updateLiveValidation(res.data.validation);
                    } else {
                        alert(res.message || 'Failed to load direct referrals.');
                    }
                });
            });

            // Live contribution total validation calculator
            $(document).on('input', '.contrib-pct-input', function() {
                recalcLiveValidation();
            });

            function recalcLiveValidation() {
                let sum = 0;
                $('.contrib-pct-input').each(function() {
                    sum += parseFloat($(this).val() || 0);
                });
                sum = Math.round(sum * 100) / 100;

                let isValid = (Math.abs(sum - 100.00) < 0.001);
                let badgeClass = isValid ? 'bg-success' : 'bg-danger';
                let text = isValid ? 'Total: 100.00% (VALID)' : 'Total: ' + sum.toFixed(2) + '% (INVALID - MUST EQUAL 100%)';

                $('#validationBadgeContainer').html(`<span class="badge ${badgeClass} fs-6 p-2">${text}</span>`);
            }

            function updateLiveValidation(val) {
                if (!val) return;
                let isValid = val.valid;
                let badgeClass = isValid ? 'bg-success' : 'bg-danger';
                let text = isValid ? 'Total: 100.00% (VALID)' : 'Total: ' + parseFloat(val.total_percentage).toFixed(2) + '% (INVALID - MUST EQUAL 100%)';
                $('#validationBadgeContainer').html(`<span class="badge ${badgeClass} fs-6 p-2">${text}</span>`);
            }

            // Save All Contributions Form
            $('#btnSaveAllContributions').on('click', function(e) {
                e.preventDefault();
                let formData = $('#bulkContribForm').serialize();

                $.post('mentor_income_action.php', formData, function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        updateLiveValidation(res.validation);
                    } else {
                        alert('Error: ' + res.message);
                    }
                }, 'json');
            });

            // Handle Financial Adjustment Form Submit
            $('#adjustmentForm').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize() + '&action=adjust_balance';

                $('#btnSubmitAdjustment').prop('disabled', true).text('Processing...');

                $.post('mentor_income_action.php', formData, function (res) {
                    $('#btnSubmitAdjustment').prop('disabled', false).text('Submit Adjustment');
                    if (res.status === 'success') {
                        alert(res.message);
                        $('#adjustmentModal').modal('hide');
                        $('#adjustmentForm')[0].reset();
                        historyTable.ajax.reload();
                        auditTable.ajax.reload();
                    } else {
                        alert('Adjustment Failed: ' + res.message);
                    }
                }, 'json').fail(function () {
                    $('#btnSubmitAdjustment').prop('disabled', false).text('Submit Adjustment');
                    alert('Network error occurred while submitting adjustment.');
                });
            });
        });
    </script>

</body>
</html>
