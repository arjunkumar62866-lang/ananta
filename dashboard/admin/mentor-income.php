<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - MENTOR INCOME MONTHLY LEG BUSINESS MODEL
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

.btn-adjust-modal {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    font-weight: 700;
    font-size: 13.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    transition: all 0.2s ease;
}

.btn-adjust-modal:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
}

.btn-export-excel {
    background: #10b981;
    color: #ffffff !important;
    border: none;
    border-radius: 10px;
    padding: 8px 16px;
    font-weight: 700;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
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
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Mentor Income Management</h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Monthly Leg Business Contribution Model (Dynamic Rate Control, Live Preview & Automated Closing).</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary fw-bold" id="btnOpenRateModal" style="border-radius: 12px;">
                                <i class="fa fa-percent me-1"></i> Rate Config (<span id="bannerActiveRate">2.00%</span>)
                            </button>
                            <button class="btn btn-adjust-modal" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                <i class="fa fa-pencil-square-o"></i> Wallet Adjustment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Program Configuration Card -->
                <div class="card ananta-fintech-card mb-4 p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <h5 class="fw-bold mb-0" style="color: #0f172a;"><i class="fa fa-cogs text-success me-2"></i> Monthly Leg Business Model Configuration</h5>
                        <button class="btn btn-sm btn-primary fw-bold" id="btnEditRateDirect" style="border-radius: 8px;">
                            <i class="fa fa-edit me-1"></i> Change Mentor Income Rate
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Active Mentor Income Rate</span>
                                <span class="h4 fw-extrabold text-success mb-0" id="cardActiveRate">2.00%</span>
                                <small class="text-muted d-block mt-1">Default 2%, Admin Controlled</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Pool Formula</span>
                                <span class="h6 fw-bold text-dark mb-0">Monthly Profit Income × Rate%</span>
                                <small class="text-muted d-block mt-1">From canonical Profit Income transactions</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Contribution Percentage</span>
                                <span class="h6 fw-bold text-primary mb-0">(Leg Business / Total Legs) × 100</span>
                                <small class="text-muted d-block mt-1">Eligible direct legs share 100% pool</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size: 11px;">Eligibility Rule</span>
                                <span class="h6 fw-bold text-dark mb-0">Active ID + Min $145 Package</span>
                                <small class="text-muted d-block mt-1">Ineligible legs excluded from denominator</small>
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
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">BLOCKED / INELIGIBLE</span>
                                <h4 class="mb-0 fw-extrabold" id="statBlockedMentors" style="color: #dc2626;">0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" id="mentorTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="closing-tab" data-bs-toggle="tab" data-bs-target="#closingTabContent" type="button" role="tab">
                            <i class="fa fa-calculator me-1"></i> Monthly Closing & Preview
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
                    <!-- Tab 1: Monthly Closing & Preview -->
                    <div class="tab-pane fade show active" id="closingTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Monthly Mentor Income Closing Preview</h4>
                                    <p>Preview Mentor-wise Profit Income, calculated pool, direct leg business, contribution % and calculated payout before processing.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Control form -->
                                <div class="row g-3 align-items-end mb-4 p-3 bg-light rounded-3 border">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold" style="color:#0f172a; font-size:13px;">SELECT CLOSING MONTH</label>
                                        <input type="month" id="closingMonthInput" class="form-control fw-bold" value="<?php echo date('Y-m'); ?>" style="border-radius:10px;">
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnGeneratePreview" class="btn text-white fw-bold w-100" style="background:#0284c7; border-radius:10px; padding:9px;">
                                            <i class="fa fa-eye me-1"></i> Preview Calculation
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnProcessClosing" class="btn text-white fw-bold w-100" style="background:#10b981; border-radius:10px; padding:9px;" disabled>
                                            <i class="fa fa-check-circle me-1"></i> Approve & Process Closing
                                        </button>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <span class="text-muted d-block" style="font-size:11px;">SAFETY GUARANTEE</span>
                                        <span class="badge bg-secondary">Read-only preview</span>
                                    </div>
                                </div>

                                <!-- Preview Summary Banner -->
                                <div id="previewSummaryContainer" style="display:none;" class="mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="p-3 bg-white border rounded-3 shadow-sm">
                                                <span class="text-muted text-uppercase fw-bold d-block" style="font-size:11px;">Qualifying Mentors</span>
                                                <h4 class="mb-0 fw-bold text-dark" id="prevQualMentors">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-white border rounded-3 shadow-sm">
                                                <span class="text-muted text-uppercase fw-bold d-block" style="font-size:11px;">Eligible Mentors</span>
                                                <h4 class="mb-0 fw-bold text-success" id="prevEligMentors">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-white border rounded-3 shadow-sm">
                                                <span class="text-muted text-uppercase fw-bold d-block" style="font-size:11px;">Total Mentor Pool</span>
                                                <h4 class="mb-0 fw-bold text-primary" id="prevTotalPool">₹0.00</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-white border rounded-3 shadow-sm">
                                                <span class="text-muted text-uppercase fw-bold d-block" style="font-size:11px;">Total Calculated Payout</span>
                                                <h4 class="mb-0 fw-bold text-success" id="prevTotalPayout">₹0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mentors Table -->
                                <div id="previewTableContainer" style="display:none;">
                                    <h6 class="fw-bold mb-3" style="color:#0f172a;"><i class="fa fa-list me-1"></i> Mentor-Wise Breakdown</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-hover table-bordered" id="previewMentorsTable" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Mentor ID</th>
                                                    <th>Mentor Name</th>
                                                    <th>Month Profit Income</th>
                                                    <th>Applied Rate</th>
                                                    <th>Mentor Pool</th>
                                                    <th>Eligible / Total Legs</th>
                                                    <th>Total Leg Business</th>
                                                    <th>Expected Payout</th>
                                                    <th>Closing Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamic via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
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
                                    <p>Live history of all Mentor Income payouts generated during monthly closings with leg business and rate snapshots.</p>
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
                                    <label class="form-label mb-1 text-muted fw-bold" style="font-size:12px;">MENTOR USER ID</label>
                                    <input type="text" id="mentorFilterInput" class="form-control form-control-sm fw-bold" placeholder="e.g. 1001" style="width: 150px; border-radius: 8px;">
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
                                                <th>Month</th>
                                                <th>Mentor Profit</th>
                                                <th>Rate</th>
                                                <th>Leg Business</th>
                                                <th>Total Legs Business</th>
                                                <th>Mentor Pool</th>
                                                <th>Contrib %</th>
                                                <th>Payout Amount</th>
                                                <th>Closing ID</th>
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

    <!-- Rate Configuration Modal -->
    <div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="rateModalLabel"><i class="fa fa-percent me-2"></i> Mentor Income Rate Configuration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rateConfigForm">
                    <input type="hidden" name="action" value="update_rate">
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 fw-bold mb-3" style="font-size: 13px;">
                            <i class="fa fa-info-circle me-1"></i> Default rate is <strong>2.00%</strong>. Any rate change is logged in <strong>tbl_mentor_income_rate_history</strong> with admin ID and timestamp.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Mentor Income Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="rate_percent" id="modalRateInput" class="form-control fw-bold" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Effective From Month</label>
                            <input type="month" name="effective_from_month" id="modalEffMonthInput" class="form-control fw-bold" value="<?php echo date('Y-m'); ?>" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Automatic Monthly Closing</label>
                            <select name="is_auto_closing" id="modalAutoCloseSelect" class="form-select fw-bold" style="border-radius: 10px;">
                                <option value="0">Manual Admin Closing Only (Recommended)</option>
                                <option value="1">Automatic Monthly Closing Enabled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Mandatory Reason / Remarks</label>
                            <textarea name="reason" class="form-control fw-bold" rows="2" placeholder="State reason for changing the rate..." required style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                        <button type="submit" id="btnSaveRate" class="btn text-white fw-bold" style="background: #10b981; border-radius: 10px;">Update Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Leg Breakdown Modal -->
    <div class="modal fade" id="legBreakdownModal" tabindex="-1" aria-labelledby="legBreakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="legBreakdownModalLabel"><i class="fa fa-sitemap me-2"></i> Direct Legs Business & Contribution Breakdown</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0" id="modalMentorHeader" style="color:#0f172a;">Mentor: --</h6>
                            <small class="text-muted" id="modalMonthHeader">Closing Month: --</small>
                        </div>
                        <div id="modalMentorEligibilityBadge"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="legBreakdownTable">
                            <thead>
                                <tr class="bg-light">
                                    <th>Direct User</th>
                                    <th>Status</th>
                                    <th>Eligibility</th>
                                    <th>Leg Members</th>
                                    <th>Monthly Business</th>
                                    <th>Contrib %</th>
                                    <th>Calculated Payout</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Close</button>
                </div>
            </div>
        </div>
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
            let cachedPreviewData = null;

            // Load Rate Config
            function loadRateConfig() {
                $.getJSON('mentor_income_action.php', { action: 'get_rate_config' }, function(res) {
                    if (res.status === 'success' && res.config) {
                        let r = parseFloat(res.config.rate_percent || 2.0).toFixed(2) + '%';
                        $('#bannerActiveRate').text(r);
                        $('#cardActiveRate').text(r);
                        $('#modalRateInput').val(res.config.rate_percent);
                        $('#modalEffMonthInput').val(res.config.effective_from_month || '<?php echo date('Y-m'); ?>');
                        $('#modalAutoCloseSelect').val(res.config.is_auto_closing || 0);
                    }
                });
            }
            loadRateConfig();

            $('#btnOpenRateModal, #btnEditRateDirect').on('click', function() {
                $('#rateModal').modal('show');
            });

            $('#rateConfigForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $('#btnSaveRate').prop('disabled', true).text('Updating...');

                $.post('mentor_income_action.php', formData, function(res) {
                    $('#btnSaveRate').prop('disabled', false).text('Update Rate');
                    if (res.status === 'success') {
                        alert(res.message);
                        $('#rateModal').modal('hide');
                        loadRateConfig();
                    } else {
                        alert('Error: ' + res.message);
                    }
                }, 'json').fail(function() {
                    $('#btnSaveRate').prop('disabled', false).text('Update Rate');
                    alert('Network error while updating rate.');
                });
            });

            // Preview Mentor Income Closing
            $('#btnGeneratePreview').on('click', function() {
                let month = $('#closingMonthInput').val();
                if (!month) {
                    alert('Please select a valid closing month.');
                    return;
                }

                $('#btnGeneratePreview').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Calculating...');
                $('#btnProcessClosing').prop('disabled', true);

                $.getJSON('mentor_income_action.php', { action: 'preview_closing', closing_month: month }, function(res) {
                    $('#btnGeneratePreview').prop('disabled', false).html('<i class="fa fa-eye me-1"></i> Preview Calculation');

                    if (res.status === 'success') {
                        cachedPreviewData = res;
                        $('#prevQualMentors').text(res.total_mentors || 0);
                        $('#prevEligMentors').text(res.eligible_mentors || 0);
                        $('#prevTotalPool').text(formatAdminCurrency(res.total_pool_usd || 0));
                        $('#prevTotalPayout').text(formatAdminCurrency(res.total_expected_payout_usd || 0));
                        $('#previewSummaryContainer').show();

                        let rows = '';
                        if (res.mentors && res.mentors.length > 0) {
                            res.mentors.forEach(m => {
                                let statusBadge = '';
                                if (m.status === 'ELIGIBLE') {
                                    statusBadge = '<span class="badge bg-success">ELIGIBLE</span>';
                                } else {
                                    statusBadge = `<span class="badge bg-danger" title="${m.status_reason}">${m.status}</span><br><small class="text-danger">${m.status_reason}</small>`;
                                }

                                rows += `<tr>
                                    <td class="fw-bold"><?php echo $hmpre; ?>${m.mentor_id}</td>
                                    <td>${m.mentor_name || 'Member'}</td>
                                    <td class="fw-bold">${formatAdminCurrency(m.monthly_profit_usd)}</td>
                                    <td class="fw-bold text-primary">${parseFloat(m.mentor_income_rate).toFixed(2)}%</td>
                                    <td class="fw-bold text-purple" style="color:#9333ea;">${formatAdminCurrency(m.mentor_pool_usd)}</td>
                                    <td><span class="badge bg-light text-dark border">${m.eligible_legs} / ${m.total_directs} Legs</span></td>
                                    <td>${formatAdminCurrency(m.total_leg_business_usd)}</td>
                                    <td class="fw-bold text-success">${formatAdminCurrency(m.total_payout_usd)}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info fw-bold btn-view-legs" data-mentor="${m.mentor_id}">
                                            <i class="fa fa-eye"></i> View Legs
                                        </button>
                                    </td>
                                </tr>`;
                            });
                            $('#btnProcessClosing').prop('disabled', false);
                        } else {
                            rows = '<tr><td colspan="10" class="text-center text-muted p-4">No Mentors earned Profit Income in ' + month + '.</td></tr>';
                        }

                        $('#previewMentorsTable tbody').html(rows);
                        $('#previewTableContainer').show();
                    } else {
                        alert('Preview Error: ' + res.message);
                    }
                }).fail(function() {
                    $('#btnGeneratePreview').prop('disabled', false).html('<i class="fa fa-eye me-1"></i> Preview Calculation');
                    alert('Network error while generating preview.');
                });
            });

            // View Mentor Legs Breakdown Modal
            $(document).on('click', '.btn-view-legs', function() {
                let mentorId = $(this).data('mentor');
                let month = $('#closingMonthInput').val();

                if (!cachedPreviewData || !cachedPreviewData.mentors) return;
                let mentorData = cachedPreviewData.mentors.find(m => m.mentor_id == mentorId);
                if (!mentorData) return;

                $('#modalMentorHeader').text('Mentor: <?php echo $hmpre; ?>' + mentorData.mentor_id + ' (' + (mentorData.mentor_name || 'Member') + ')');
                $('#modalMonthHeader').text('Closing Month: ' + month + ' | Mentor Pool: ' + formatAdminCurrency(mentorData.mentor_pool_usd));

                if (mentorData.status === 'ELIGIBLE') {
                    $('#modalMentorEligibilityBadge').html('<span class="badge bg-success fs-6">Mentor Eligible</span>');
                } else {
                    $('#modalMentorEligibilityBadge').html(`<span class="badge bg-danger fs-6">${mentorData.status_reason}</span>`);
                }

                let legRows = '';
                if (mentorData.legs && mentorData.legs.length > 0) {
                    mentorData.legs.forEach(l => {
                        let eligBadge = l.is_eligible 
                            ? '<span class="badge bg-success">ELIGIBLE</span>' 
                            : `<span class="badge bg-danger" title="${l.exclusion_reason}">EXCLUDED</span><br><small class="text-danger">${l.exclusion_reason}</small>`;

                        legRows += `<tr>
                            <td class="fw-bold"><?php echo $hmpre; ?>${l.direct_user_id}<br><small class="text-muted">${l.direct_user_name || 'Direct'}</small></td>
                            <td><span class="badge ${l.is_active ? 'bg-success' : 'bg-secondary'}">${l.is_active ? 'Active' : 'Inactive'}</span></td>
                            <td>${eligBadge}</td>
                            <td>${l.leg_members_count} Members</td>
                            <td class="fw-bold">${formatAdminCurrency(l.monthly_business_usd)}</td>
                            <td class="fw-bold text-primary">${parseFloat(l.contribution_percentage).toFixed(2)}%</td>
                            <td class="fw-bold text-success">${formatAdminCurrency(l.calculated_payout_usd)}</td>
                        </tr>`;
                    });
                } else {
                    legRows = '<tr><td colspan="7" class="text-center text-muted">No direct referrals found for this Mentor.</td></tr>';
                }

                $('#legBreakdownTable tbody').html(legRows);
                $('#legBreakdownModal').modal('show');
            });

            // Process Closing
            $('#btnProcessClosing').on('click', function() {
                let month = $('#closingMonthInput').val();
                if (!confirm(`Are you sure you want to approve and execute Mentor Income Monthly Closing for ${month}?\n\nThis will credit wallets and record irreversible ledger entries.`)) {
                    return;
                }

                $('#btnProcessClosing').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing Closing...');

                $.post('mentor_income_action.php', {
                    action: 'process_closing',
                    closing_month: month,
                    closing_date: new Date().toISOString().slice(0, 10)
                }, function(res) {
                    $('#btnProcessClosing').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Approve & Process Closing');
                    if (res.status === 'success') {
                        alert(res.message);
                        historyTable.ajax.reload();
                        auditTable.ajax.reload();
                        $('#btnGeneratePreview').trigger('click');
                    } else {
                        alert('Closing Failed: ' + res.message);
                    }
                }, 'json').fail(function() {
                    $('#btnProcessClosing').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Approve & Process Closing');
                    alert('Network error while processing closing.');
                });
            });

            // Payout History DataTable
            let historyTable = $('#historyTable').DataTable({
                ajax: {
                    url: 'mentor_income_action.php',
                    type: 'GET',
                    data: function(d) {
                        d.action = 'get_schedules';
                        d.closing_month = $('#monthFilterInput').val();
                        d.mentor_id = $('#mentorFilterInput').val();
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

                            $('#statTotalPaid').text(formatAdminCurrency(totalPaid));
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
                        render: (data) => '<span style="font-weight:600; color:#475569;">' + formatAdminCurrency(parseFloat(data || 0)) + '</span>'
                    },
                    {
                        data: 'mentor_income_rate',
                        render: (data) => '<span style="font-weight:700; color:#0284c7;">' + parseFloat(data || 2.0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'leg_monthly_business',
                        render: (data) => '<span style="font-weight:600; color:#0f172a;">' + formatAdminCurrency(parseFloat(data || 0)) + '</span>'
                    },
                    {
                        data: 'total_eligible_legs_business',
                        render: (data) => '<span style="font-weight:600; color:#64748b;">' + formatAdminCurrency(parseFloat(data || 0)) + '</span>'
                    },
                    {
                        data: 'total_mentor_income',
                        render: (data) => '<span style="font-weight:700; color:#9333ea;">' + formatAdminCurrency(parseFloat(data || 0)) + '</span>'
                    },
                    {
                        data: 'contribution_percentage',
                        render: (data) => '<span class="badge bg-info text-white fw-bold">' + parseFloat(data || 0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'payout_amount',
                        render: (data) => '<span style="font-weight:800; color:#10b981;">' + formatAdminCurrency(parseFloat(data || 0)) + '</span>'
                    },
                    {
                        data: 'closing_id',
                        render: (data) => '<small class="text-muted">' + (data || '-') + '</small>'
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

            // Adjustments Audit Table
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
                    { data: 'amount', render: (data) => formatAdminCurrency(parseFloat(data || 0)) },
                    { data: 'previous_balance', render: (data) => formatAdminCurrency(parseFloat(data || 0)) },
                    { data: 'new_balance', render: (data) => formatAdminCurrency(parseFloat(data || 0)) },
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
                $('#mentorFilterInput').val('');
                historyTable.ajax.reload();
            });

            $('#customExportBtn').on('click', function () {
                historyTable.button('.buttons-excel').trigger();
            });

            // Financial Adjustment Form Submit
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
