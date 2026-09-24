<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - DIRECT BONUS REDESIGN & ADMIN MANAGEMENT
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
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(168, 85, 247, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(168, 85, 247, 0.3);
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

.btn-export-excel:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.35) !important;
    color: #ffffff !important;
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
    color: #0284c7;
    background: #ffffff;
    border-bottom: 3px solid #0284c7;
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

.table-hover tbody tr:hover {
    background-color: #f8fafc !important;
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

.btn-action-inspect {
    background: rgba(2, 132, 199, 0.1);
    color: #0284c7;
    border: 1px solid rgba(2, 132, 199, 0.2);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 700;
    transition: all 0.2s;
}

.btn-action-inspect:hover {
    background: #0284c7;
    color: #ffffff;
}

.btn-action-adjust {
    background: rgba(147, 51, 234, 0.1);
    color: #9333ea;
    border: 1px solid rgba(147, 51, 234, 0.2);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 700;
    transition: all 0.2s;
}

.btn-action-adjust:hover {
    background: #9333ea;
    color: #ffffff;
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
    <!-- End Loader -->

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
                                <i class="fa fa-gift"></i>
                            </div>
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Direct Bonus Management </h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Monitor 10-month schedules, inspect qualified directs & manage admin wallet adjustments.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Settings Summary Card -->
                <div class="card ananta-fintech-card mb-4 p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <h5 class="fw-bold mb-3" style="color: #0f172a;"><i class="fa fa-sliders text-primary me-2"></i> Program Configuration & Rules</h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Direct Bonus Rate</span>
                                <span class="h5 fw-extrabold text-primary mb-0">6% Total</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Distribution Window</span>
                                <span class="h5 fw-extrabold text-purple mb-0" style="color:#9333ea;">10 Months (0.6%/mo)</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Min Referral Investment</span>
                                <span class="h5 fw-extrabold text-success mb-0"><?php echo $hmcurrency; ?> 13,000</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Sponsor Qualification</span>
                                <span class="h6 fw-bold text-dark mb-0">$11 Active + 2 Qualified Directs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                <i class="fa fa-list-alt"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL SCHEDULES</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalSchedules" style="color: #0f172a;">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL CREDITED AMOUNT</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalCredited" style="color: #16a34a;"><?php echo $hmcurrency; ?> 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04;">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL PENDING SCHEDULES</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalPending" style="color: #ca8a04;">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">UNIQUE BENEFICIARIES</span>
                                <h4 class="mb-0 fw-extrabold" id="statBeneficiaries" style="color: #9333ea;">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" id="adminBonusTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="schedules-tab" data-bs-toggle="tab" data-bs-target="#schedulesTabContent" type="button" role="tab">
                            <i class="fa fa-calendar me-1"></i> 10-Month Distribution Schedule
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#auditTabContent" type="button" role="tab">
                            <i class="fa fa-history me-1"></i> Admin Adjustment Audit Trail
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="adminBonusTabsContent">
                    <!-- Tab 1: Schedules -->
                    <div class="tab-pane fade show active" id="schedulesTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Direct Bonus Schedule & Management</h4>
                                    <p>Live schedule tracking, eligibility inspection & manual adjustments</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="customExportBtn" class="btn btn-export-excel">
                                        <i class="fa fa-file-excel-o"></i> Export to Excel
                                    </button>
                                </div>
                            </div>

                            <!-- Filters Bar -->
                            <div class="p-3 bg-light border-bottom d-flex align-items-center flex-wrap gap-3">
                                <div>
                                    <label class="form-label mb-1 text-muted fw-bold" style="font-size:12px;">STATUS FILTER</label>
                                    <select id="statusFilter" class="form-select form-select-sm fw-bold" style="width: 150px; border-radius: 8px;">
                                        <option value="">All Statuses</option>
                                        <option value="CREDITED">CREDITED</option>
                                        <option value="PENDING">PENDING</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label mb-1 text-muted fw-bold" style="font-size:12px;">MONTH FILTER</label>
                                    <select id="monthFilter" class="form-select form-select-sm fw-bold" style="width: 150px; border-radius: 8px;">
                                        <option value="">All Months</option>
                                        <?php for($m=1; $m<=10; $m++): ?>
                                            <option value="<?php echo $m; ?>">Installment #<?php echo $m; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover table-bordered" id="usersTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Beneficiary</th>
                                                <th>Direct Wallet</th>
                                                <th>Source Referral</th>
                                                <th>Investment</th>
                                                <th>Installment</th>
                                                <th>Month</th>
                                                <th>Status</th>
                                                <th>Credit Date</th>
                                                <th>Actions</th>
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

                    <!-- Tab 2: Audit Log -->
                    <div class="tab-pane fade" id="auditTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Admin Financial Adjustment Audit Log</h4>
                                    <p>Complete historical log of manual CREDIT and DEBIT actions</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="auditTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Audit ID</th>
                                                <th>Admin</th>
                                                <th>Target User</th>
                                                <th>Action Type</th>
                                                <th>Amount</th>
                                                <th>Prev Balance</th>
                                                <th>New Balance</th>
                                                <th>Reason</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loaded via AJAX -->
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

        <!-- Qualified Direct Inspector Modal -->
        <div class="modal fade" id="inspectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <h5 class="modal-header-title mb-0 fw-bold"><i class="fa fa-user-check me-2"></i> Qualified Direct Referrals Inspector</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold" id="inspectUserName" style="color: #0f172a;">User ID: --</h6>
                                <span class="text-muted" style="font-size:13px;" id="inspectUserStatus">Checking qualification status...</span>
                            </div>
                            <span id="inspectQualificationBadge" class="badge bg-secondary fs-6 p-2">Checking...</span>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered" id="inspectDirectsTable">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Direct User</th>
                                        <th>Account Status</th>
                                        <th>Total Investment</th>
                                        <th>Qualified Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rendered dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Balance Adjustment Modal -->
        <div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);">
                        <h5 class="modal-header-title mb-0 fw-bold"><i class="fa fa-exchange me-2"></i> Manual Direct Bonus Wallet Adjustment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="adjustForm">
                        <input type="hidden" name="action" value="adjust_balance">
                        <input type="hidden" name="user_id" id="adjustUserIdInput">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color:#0f172a;">Target User ID</label>
                                <input type="text" id="adjustUserIdDisplay" class="form-control fw-bold" readonly style="background:#f1f5f9;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color:#0f172a;">Current Direct Bonus Wallet</label>
                                <input type="text" id="adjustUserWalletDisplay" class="form-control fw-bold text-success" readonly style="background:#f1f5f9;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color:#0f172a;">Adjustment Type</label>
                                <select name="type" class="form-select fw-bold" required>
                                    <option value="CREDIT">CREDIT (Add Funds)</option>
                                    <option value="DEBIT">DEBIT (Deduct Funds)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color:#0f172a;">Amount (<?php echo $hmcurrency; ?>)</label>
                                <input type="number" step="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required min="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color:#0f172a;">Reason / Reference (Required Audit Note)</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Enter detailed reason for manual adjustment..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn text-white fw-bold" style="background:#9333ea;">Confirm Adjustment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'common/footer.php'; ?>
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
            let table = $('#usersTable').DataTable({
                ajax: {
                    url: 'get_income.php',
                    type: 'GET',
                    data: { type: 'direct_bonus_schedule' },
                    dataSrc: function (json) {
                        // Calculate stats dynamically
                        let totalCount = json.length;
                        let totalCredited = 0;
                        let pendingCount = 0;
                        let beneficiaries = new Set();

                        json.forEach(row => {
                            if (row.beneficiary_id) beneficiaries.add(row.beneficiary_id);
                            if (row.status === 'CREDITED') {
                                totalCredited += parseFloat(row.installment_amount || 0);
                            } else if (row.status === 'PENDING') {
                                pendingCount++;
                            }
                        });

                        $('#statTotalSchedules').text(totalCount);
                        $('#statTotalCredited').text('<?php echo $hmcurrency; ?> ' + totalCredited.toLocaleString('en-IN', {minimumFractionDigits:2}));
                        $('#statTotalPending').text(pendingCount);
                        $('#statBeneficiaries').text(beneficiaries.size);

                        return json;
                    }
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => '<span style="font-weight:700; color:#64748b;">#' + (meta.row + 1) + '</span>' },
                    {
                        data: 'beneficiary_id',
                        render: (data, type, row) => '<strong><?php echo $hmpre; ?>' + data + '</strong>' + (row.beneficiary_name ? '<br><small class="text-muted">' + row.beneficiary_name + '</small>' : '')
                    },
                    {
                        data: 'beneficiary_wallet',
                        render: (data) => '<span class="amount-text" style="color:#16a34a; font-weight:700;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'source_user_id',
                        render: (data, type, row) => '<span style="color:#0284c7; font-weight:600;"><?php echo $hmpre; ?>' + data + '</span>' + (row.source_user_name ? '<br><small class="text-muted">' + row.source_user_name + '</small>' : '')
                    },
                    {
                        data: 'investment_amount',
                        render: (data) => '<span style="font-weight:600; color:#475569;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>'
                    },
                    {
                        data: 'installment_amount',
                        render: (data) => '<span style="font-weight:700; color:#ea580c;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'installment_month',
                        render: (data, type, row) => '<span style="font-weight:600; color:#1e293b;">Month ' + row.installment_number + ' / 10</span>'
                    },
                    {
                        data: 'status',
                        render: (data) => {
                            if (data === 'CREDITED') {
                                return '<span class="badge" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight:700; padding: 4px 10px; border-radius: 100px;">CREDITED</span>';
                            } else if (data === 'PENDING') {
                                return '<span class="badge" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04; font-weight:700; padding: 4px 10px; border-radius: 100px;">PENDING</span>';
                            } else {
                                return '<span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; font-weight:700; padding: 4px 10px; border-radius: 100px;">' + data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'credited_at',
                        render: (data) => '<span style="color:#64748b; font-size:13px;">' + (data || 'Not Credited') + '</span>'
                    },
                    {
                        data: null,
                        render: (data, type, row) => {
                            return `<div class="d-flex gap-1">
                                <button class="btn btn-action-inspect btn-inspect" data-userid="${row.beneficiary_id}"><i class="fa fa-search"></i> Inspect</button>
                                <button class="btn btn-action-adjust btn-adjust" data-userid="${row.beneficiary_id}" data-wallet="${row.beneficiary_wallet || 0}"><i class="fa fa-edit"></i> Adjust</button>
                            </div>`;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    { extend: 'excelHtml5', title: 'Direct_Bonus_10M_Schedule_Report' }
                ]
            });

            // Filters
            $('#statusFilter').on('change', function() {
                let val = $(this).val();
                table.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
            });

            $('#monthFilter').on('change', function() {
                let val = $(this).val();
                table.column(6).search(val ? 'Month ' + val + ' / 10' : '', false, true).draw();
            });

            $('#customExportBtn').on('click', function () {
                table.button('.buttons-excel').trigger();
            });

            // Inspect Directs Modal Trigger
            $(document).on('click', '.btn-inspect', function() {
                let userId = $(this).data('userid');
                $('#inspectUserName').text('User ID: <?php echo $hmpre; ?>' + userId);
                $('#inspectUserStatus').text('Loading qualified direct referrals...');
                $('#inspectQualificationBadge').attr('class', 'badge bg-warning p-2').text('Checking...');
                $('#inspectDirectsTable tbody').html('<tr><td colspan="4" class="text-center text-muted">Fetching direct referrals...</td></tr>');
                
                let modal = new bootstrap.Modal(document.getElementById('inspectModal'));
                modal.show();

                $.getJSON('direct_bonus_adjustment_action.php', { action: 'get_qualified_details', user_id: userId }, function(res) {
                    if (res.status === 'success') {
                        let data = res.data;
                        let qStatus = data.is_qualified ? 
                            '<span class="badge bg-success p-2">QUALIFIED (' + data.qualified_count + ' Qualified Directs)</span>' : 
                            '<span class="badge bg-danger p-2">NOT QUALIFIED (' + data.qualified_count + ' / 2 Qualified Directs)</span>';
                        
                        $('#inspectUserStatus').text('Sponsor Active ($11): ' + (data.sponsor_active ? 'YES' : 'NO'));
                        $('#inspectQualificationBadge').html(qStatus);

                        let rowsHtml = '';
                        if (data.directs && data.directs.length > 0) {
                            data.directs.forEach(d => {
                                let isQ = d.is_active_11usd && d.total_investment >= 13000;
                                rowsHtml += `<tr>
                                    <td class="fw-bold"><?php echo $hmpre; ?>${d.userid} (${d.name})</td>
                                    <td><span class="badge ${d.is_active_11usd ? 'bg-success' : 'bg-secondary'}">${d.status_label}</span></td>
                                    <td class="fw-bold"><?php echo $hmcurrency; ?> ${parseFloat(d.total_investment).toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
                                    <td>${isQ ? '<span class="badge bg-success"><i class="fa fa-check"></i> QUALIFIED</span>' : '<span class="badge bg-danger"><i class="fa fa-times"></i> INELIGIBLE</span>'}</td>
                                </tr>`;
                            });
                        } else {
                            rowsHtml = '<tr><td colspan="4" class="text-center text-muted">No direct referrals found.</td></tr>';
                        }
                        $('#inspectDirectsTable tbody').html(rowsHtml);
                    }
                });
            });

            // Adjust Balance Modal Trigger
            $(document).on('click', '.btn-adjust', function() {
                let userId = $(this).data('userid');
                let wallet = $(this).data('wallet');

                $('#adjustUserIdInput').val(userId);
                $('#adjustUserIdDisplay').val('<?php echo $hmpre; ?>' + userId);
                $('#adjustUserWalletDisplay').val('<?php echo $hmcurrency; ?> ' + parseFloat(wallet).toFixed(2));
                
                let modal = new bootstrap.Modal(document.getElementById('adjustModal'));
                modal.show();
            });

            // Handle Adjust Form Submit
            $('#adjustForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.post('direct_bonus_adjustment_action.php', formData, function(res) {
                    if (res.status === 'success') {
                        alert(res.message);
                        bootstrap.Modal.getInstance(document.getElementById('adjustModal')).hide();
                        table.ajax.reload(null, false);
                        loadAuditTrail();
                    } else {
                        alert('Error: ' + res.message);
                    }
                }, 'json');
            });

            // Load Audit Trail
            function loadAuditTrail() {
                $.getJSON('direct_bonus_adjustment_action.php', { action: 'get_audit_history' }, function(res) {
                    if (res.status === 'success' && res.data) {
                        let rows = '';
                        res.data.forEach(row => {
                            let typeBadge = row.action === 'CREDIT' ? 
                                '<span class="badge bg-success">CREDIT</span>' : 
                                '<span class="badge bg-danger">DEBIT</span>';
                            rows += `<tr>
                                <td>#${row.id}</td>
                                <td>${row.admin_name || 'Admin #'+row.admin_id}</td>
                                <td class="fw-bold"><?php echo $hmpre; ?>${row.user_id}</td>
                                <td>${typeBadge}</td>
                                <td class="fw-bold"><?php echo $hmcurrency; ?> ${parseFloat(row.amount).toFixed(2)}</td>
                                <td><?php echo $hmcurrency; ?> ${parseFloat(row.previous_balance).toFixed(2)}</td>
                                <td class="fw-bold text-success"><?php echo $hmcurrency; ?> ${parseFloat(row.new_balance).toFixed(2)}</td>
                                <td>${row.reason}</td>
                                <td>${row.created_at}</td>
                            </tr>`;
                        });
                        $('#auditTable tbody').html(rows);
                    }
                });
            }

            // Load audit trail on tab switch
            $('#audit-tab').on('click', function() {
                loadAuditTrail();
            });
        });
    </script>

</body>
</html>
