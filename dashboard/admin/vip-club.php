<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - VIP CLUB & REWARD SYSTEM (REQ #16)
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
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(217, 119, 6, 0.08) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(245, 158, 11, 0.25) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
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
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 8px 18px !important;
    font-size: 13.5px !important;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important;
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
    color: #f59e0b;
    background: #ffffff;
    border-bottom: 3px solid #f59e0b;
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
                                <i class="fa fa-trophy"></i>
                            </div>
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">VIP Club & Reward System </h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">10 VIP Levels with Left/Right ID & Business criteria, Rewards & Monthly Repeat Income.</p>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-adjust-modal" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                                <i class="fa fa-pencil-square-o"></i> Authorized VIP Wallet Adjustment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL REWARDS DISTRIBUTED</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalRewards" style="color: #f59e0b;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fa fa-calendar-check-o"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL MONTHLY PAYOUTS</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalMonthly" style="color: #10b981;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">QUALIFIED VIP USERS</span>
                                <h4 class="mb-0 fw-extrabold" id="statQualifiedUsers" style="color: #0f172a;">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                                <i class="fa fa-balance-scale"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL ADMIN ADJUSTMENTS</span>
                                <h4 class="mb-0 fw-extrabold" id="statTotalAdjusted" style="color: #9333ea;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" id="vipTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="config-tab" data-bs-toggle="tab" data-bs-target="#configTabContent" type="button" role="tab">
                            <i class="fa fa-list-alt me-1"></i> 10 VIP Levels Configuration
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="quals-tab" data-bs-toggle="tab" data-bs-target="#qualsTabContent" type="button" role="tab">
                            <i class="fa fa-check-circle me-1"></i> User Qualification & Reward Ledger
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

                <div class="tab-content" id="vipTabsContent">
                    <!-- Tab 1: Configuration -->
                    <div class="tab-pane fade show active" id="configTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>VIP Levels Configuration Matrix</h4>
                                    <p>Official 10-Level VIP rules, Left/Right ID requirements, Business criteria, Rewards, and Monthly Repeat thresholds.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="configTable">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Left : Right IDs</th>
                                                <th>Left : Right Business ($)</th>
                                                <th>One-Time Reward ($)</th>
                                                <th>VIP Club Income Rate</th>
                                                <th>Company Turnover Share</th>
                                                <th>Monthly Repeat Business ($)</th>
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

                    <!-- Tab 2: User Qualifications -->
                    <div class="tab-pane fade" id="qualsTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>User VIP Level Qualifications & Rewards Ledger</h4>
                                    <p>Live history of users who qualified for VIP levels and earned one-time rewards.</p>
                                </div>
                            </div>
                            <div class="p-3 bg-light border-bottom d-flex align-items-center flex-wrap gap-3">
                                <div>
                                    <label class="form-label mb-1 text-muted fw-bold" style="font-size:12px;">EVALUATE USER ID QUALIFICATION</label>
                                    <div class="d-flex gap-2">
                                        <input type="text" id="evalUserIdInput" class="form-control form-control-sm fw-bold" placeholder="e.g. 1001" style="width: 170px; border-radius: 8px;">
                                        <button id="btnEvaluateUser" class="btn btn-sm text-white fw-bold" style="background:#f59e0b; border-radius:8px;">
                                            <i class="fa fa-cog me-1"></i> Evaluate Qualification
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="qualsTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>User ID</th>
                                                <th>VIP Level</th>
                                                <th>Left : Right IDs Achieved</th>
                                                <th>Left : Right Business Achieved</th>
                                                <th>Weaker Leg Business</th>
                                                <th>Reward Amount</th>
                                                <th>Reward Status</th>
                                                <th>Qualified Date</th>
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

                    <!-- Tab 3: Payout History -->
                    <div class="tab-pane fade" id="historyTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Monthly VIP Club Income Payout Ledger</h4>
                                    <p>Live history of all VIP weaker-leg income and company turnover share payouts credited during monthly closings.</p>
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
                                                <th>User ID</th>
                                                <th>VIP Level</th>
                                                <th>Closing Month</th>
                                                <th>Weaker Leg Biz</th>
                                                <th>Weaker Leg Payout</th>
                                                <th>Company Turnover</th>
                                                <th>Turnover Share Payout</th>
                                                <th>Total Payout</th>
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

                    <!-- Tab 4: Admin Adjustments Audit Trail -->
                    <div class="tab-pane fade" id="auditTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Admin Financial Adjustments Audit Log</h4>
                                    <p>Immutable log of all authorized CREDIT and DEBIT adjustments made to user VIP Club Wallets.</p>
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
                                                <th>Amount ($)</th>
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

    <!-- Financial Adjustment Modal (Req #16) -->
    <div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="adjustmentModalLabel"><i class="fa fa-pencil-square-o me-2"></i> Authorized VIP Club Wallet Adjustment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="adjustmentForm">
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 fw-bold mb-3" style="font-size: 13px;">
                            <i class="fa fa-info-circle me-1"></i> Affects <strong>vip_club_wallet</strong> ONLY. All operations are logged to audit trail. Negative balance is strictly blocked.
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
                            <label class="form-label fw-bold" style="color: #0f172a;">Amount ($)</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required style="border-radius: 10px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Mandatory Reason</label>
                            <textarea name="reason" class="form-control fw-bold" rows="2" placeholder="State reason for manual adjustment..." required style="border-radius: 10px;"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #0f172a;">Reference / Note (Optional)</label>
                            <input type="text" name="reference" class="form-control fw-bold" placeholder="e.g. REF-VIP-123" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                        <button type="submit" id="btnSubmitAdjustment" class="btn text-white fw-bold" style="background: #f59e0b; border-radius: 10px;">Submit Adjustment</button>
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
            // Load Report Stats
            function loadReportStats() {
                $.getJSON('vip_club_action.php', { action: 'get_report_stats' }, function(res) {
                    if (res.status === 'success' && res.data) {
                        $('#statTotalRewards').text('$ ' + parseFloat(res.data.total_rewards).toLocaleString('en-US', {minimumFractionDigits:2}));
                        $('#statTotalMonthly').text('$ ' + parseFloat(res.data.total_monthly_payout).toLocaleString('en-US', {minimumFractionDigits:2}));
                        $('#statQualifiedUsers').text(res.data.total_qualified_users);
                        $('#statTotalAdjusted').text('$ ' + parseFloat(res.data.total_adjusted).toLocaleString('en-US', {minimumFractionDigits:2}));
                    }
                });
            }
            loadReportStats();

            // Load VIP Configurations Matrix
            $.getJSON('vip_club_action.php', { action: 'get_vip_configs' }, function(res) {
                if (res.status === 'success' && res.data) {
                    let rows = '';
                    res.data.forEach(c => {
                        let isTurnover = parseInt(c.has_turnover_share) === 1;
                        let turnoverBadge = isTurnover ? '<span class="badge bg-purple text-white fw-bold">+ 0.50% Company Turnover</span>' : '<span class="badge bg-secondary">None</span>';
                        let bizText = (parseFloat(c.req_left_business) >= 1000000) ? ('$ ' + (parseFloat(c.req_left_business)/10000000).toFixed(2) + ' Crore') : ('$ ' + parseFloat(c.req_left_business).toLocaleString());
                        let rptText = (parseFloat(c.monthly_repeat_business) >= 1000000) ? ('$ ' + (parseFloat(c.monthly_repeat_business)/10000000).toFixed(2) + ' Crore') : ('$ ' + parseFloat(c.monthly_repeat_business).toLocaleString());

                        rows += `<tr>
                            <td class="fw-bold text-primary">Level ${c.level_id} (${c.name})</td>
                            <td><span class="badge bg-light text-dark border">${c.req_left_ids} : ${c.req_right_ids}</span></td>
                            <td class="fw-bold text-dark">${bizText} : ${bizText}</td>
                            <td class="fw-bold text-warning">$ ${parseFloat(c.reward_amount).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                            <td class="fw-bold text-info">${parseFloat(c.vip_income_rate).toFixed(2)}% Weaker Leg</td>
                            <td>${turnoverBadge}</td>
                            <td class="fw-bold text-success">${rptText}</td>
                        </tr>`;
                    });
                    $('#configTable tbody').html(rows);
                }
            });

            // Qualifications Table
            let qualsTable = $('#qualsTable').DataTable({
                ajax: {
                    url: 'vip_club_action.php',
                    type: 'GET',
                    data: { action: 'get_qualifications' },
                    dataSrc: 'data'
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => '<span class="text-muted fw-bold">#' + (meta.row + 1) + '</span>' },
                    { data: 'user_id', render: (data, type, row) => '<strong><?php echo $hmpre; ?>' + data + '</strong>' + (row.username ? '<br><small class="text-muted">' + row.username + '</small>' : '') },
                    { data: 'vip_level', render: (data) => '<span class="badge bg-warning text-dark fw-bold">VIP Level ' + data + '</span>' },
                    { data: 'left_ids_achieved', render: (data, type, row) => row.left_ids_achieved + ' : ' + row.right_ids_achieved },
                    { data: 'left_business_achieved', render: (data, type, row) => '$' + parseFloat(data).toLocaleString() + ' : $' + parseFloat(row.right_business_achieved).toLocaleString() },
                    { data: 'weaker_leg_business', render: (data) => '<span class="fw-bold text-dark">$' + parseFloat(data).toLocaleString() + '</span>' },
                    { data: 'reward_amount', render: (data) => '<span class="fw-bold text-success">$' + parseFloat(data).toFixed(2) + '</span>' },
                    { data: 'reward_status', render: (data) => '<span class="badge bg-success">' + data + '</span>' },
                    { data: 'qualified_at', render: (data) => '<small class="text-muted">' + data + '</small>' }
                ],
                order: [[0, 'desc']],
                pageLength: 10
            });

            // Payout History Table
            let historyTable = $('#historyTable').DataTable({
                ajax: {
                    url: 'vip_club_action.php',
                    type: 'GET',
                    data: function(d) {
                        d.action = 'get_schedules';
                        d.closing_month = $('#monthFilterInput').val();
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => '<span class="text-muted fw-bold">#' + (meta.row + 1) + '</span>' },
                    { data: 'user_id', render: (data, type, row) => '<strong><?php echo $hmpre; ?>' + data + '</strong>' + (row.username ? '<br><small class="text-muted">' + row.username + '</small>' : '') },
                    { data: 'vip_level', render: (data) => '<span class="badge bg-warning text-dark fw-bold">VIP Level ' + data + '</span>' },
                    { data: 'closing_month', render: (data) => '<span class="badge bg-light text-dark border">' + data + '</span>' },
                    { data: 'weaker_leg_business', render: (data) => '$' + parseFloat(data).toLocaleString() },
                    { data: 'weaker_leg_payout', render: (data) => '<span class="text-info fw-bold">$' + parseFloat(data).toFixed(2) + '</span>' },
                    { data: 'company_turnover', render: (data) => '$' + parseFloat(data).toLocaleString() },
                    { data: 'turnover_payout', render: (data) => '<span class="text-purple fw-bold">$' + parseFloat(data).toFixed(2) + '</span>' },
                    { data: 'total_payout', render: (data) => '<span class="text-success fw-bold">$' + parseFloat(data).toFixed(2) + '</span>' },
                    { data: 'status', render: (data) => '<span class="badge bg-success">' + data + '</span>' },
                    { data: 'credited_at', render: (data) => '<small class="text-muted">' + data + '</small>' }
                ],
                pageLength: 10,
                dom: 'lfrtip',
                buttons: [
                    { extend: 'excelHtml5', title: 'VIP_Club_Payout_Report' }
                ]
            });

            // Audit Trail Table
            let auditTable = $('#auditTable').DataTable({
                ajax: {
                    url: 'vip_club_action.php',
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
                    { data: 'amount', render: (data) => '$' + parseFloat(data).toFixed(2) },
                    { data: 'previous_balance', render: (data) => '$' + parseFloat(data).toFixed(2) },
                    { data: 'new_balance', render: (data) => '$' + parseFloat(data).toFixed(2) },
                    { data: 'reason' },
                    { data: 'reference', render: (data) => data || '-' },
                    { data: 'created_at' }
                ],
                order: [[0, 'desc']],
                pageLength: 10
            });

            // Filter & Export handlers
            $('#btnApplyHistoryFilter').on('click', function() { historyTable.ajax.reload(); });
            $('#btnClearHistoryFilter').on('click', function() { $('#monthFilterInput').val(''); historyTable.ajax.reload(); });
            $('#customExportBtn').on('click', function () { historyTable.button('.buttons-excel').trigger(); });

            // Evaluate User Qualification Button
            $('#btnEvaluateUser').on('click', function() {
                let uid = $('#evalUserIdInput').val().trim();
                if (!uid) {
                    alert('Please enter a User ID.');
                    return;
                }

                $.post('vip_club_action.php', { action: 'evaluate_qualifications', user_id: uid }, function(res) {
                    if (res.status === 'success') {
                        let newCount = res.data.newly_qualified ? res.data.newly_qualified.length : 0;
                        alert('Qualification evaluation complete! Newly qualified levels: ' + newCount);
                        qualsTable.ajax.reload();
                        loadReportStats();
                    } else {
                        alert('Evaluation failed: ' + res.message);
                    }
                }, 'json');
            });

            // Handle Financial Adjustment Form Submit
            $('#adjustmentForm').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize() + '&action=adjust_balance';

                $('#btnSubmitAdjustment').prop('disabled', true).text('Processing...');

                $.post('vip_club_action.php', formData, function (res) {
                    $('#btnSubmitAdjustment').prop('disabled', false).text('Submit Adjustment');
                    if (res.status === 'success') {
                        alert(res.message);
                        $('#adjustmentModal').modal('hide');
                        $('#adjustmentForm')[0].reset();
                        qualsTable.ajax.reload();
                        historyTable.ajax.reload();
                        auditTable.ajax.reload();
                        loadReportStats();
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
