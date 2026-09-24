<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-user-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - USER VIP CLUB & REWARD SYSTEM (REQ #16)
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-user-dashboard,
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

.progress {
    height: 10px;
    border-radius: 100px;
    background-color: #e2e8f0;
}

.progress-bar {
    border-radius: 100px;
    background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
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
                <!-- Breadcrumb Bar -->
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                            <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #475569; font-weight: 600;">User Growth</span>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #0f172a; font-weight: 700;">6. VIP Club Income</span>
                        </div>
                    </nav>
                </div>

                <!-- Header Banner Card -->
                <div class="card income-header-card p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="income-header-icon">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">VIP Club & Reward Portal</h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Track your binary network progression, unlock one-time VIP rewards & monthly VIP club income.</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-extrabold shadow-sm" id="userVipBadge">
                                Current: VIP Level 0
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Summary Overview Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fa fa-wallet"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">VIP CLUB WALLET</span>
                                <h4 class="mb-0 fw-extrabold" id="statVipWallet" style="color: #f59e0b;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fa fa-gift"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">TOTAL REWARDS EARNED</span>
                                <h4 class="mb-0 fw-extrabold" id="statRewardsEarned" style="color: #10b981;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                                <i class="fa fa-balance-scale"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">WEAKER LEG BUSINESS</span>
                                <h4 class="mb-0 fw-extrabold" id="statWeakerBiz" style="color: #0284c7;">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                                <i class="fa fa-arrow-circle-right"></i>
                            </div>
                            <div>
                                <span class="text-muted fw-bold d-block" style="font-size: 12px;">NEXT TARGET LEVEL</span>
                                <h4 class="mb-0 fw-extrabold" id="statNextLevel" style="color: #9333ea;">VIP Level 1</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Binary Legs Progress Section -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card ananta-fintech-card p-4">
                            <h5 class="fw-bold mb-3" style="color:#0f172a;"><i class="fa fa-arrow-left text-primary me-2"></i> Left Leg Performance</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold">Active Left IDs</span>
                                <span class="fw-extrabold text-dark" id="txtLeftIds">0 IDs</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-bold">Left Subtree Business</span>
                                <span class="fw-extrabold text-primary" id="txtLeftBiz">$ 0.00</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" id="barLeft" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ananta-fintech-card p-4">
                            <h5 class="fw-bold mb-3" style="color:#0f172a;"><i class="fa fa-arrow-right text-success me-2"></i> Right Leg Performance</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold">Active Right IDs</span>
                                <span class="fw-extrabold text-dark" id="txtRightIds">0 IDs</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-bold">Right Subtree Business</span>
                                <span class="fw-extrabold text-success" id="txtRightBiz">$ 0.00</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" id="barRight" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" id="userVipTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="roadmap-tab" data-bs-toggle="tab" data-bs-target="#roadmapTabContent" type="button" role="tab">
                            <i class="fa fa-map-signs me-1"></i> VIP Levels Roadmap & Status
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="quals-tab" data-bs-toggle="tab" data-bs-target="#qualsTabContent" type="button" role="tab">
                            <i class="fa fa-gift me-1"></i> Earned Rewards Ledger
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTabContent" type="button" role="tab">
                            <i class="fa fa-history me-1"></i> Monthly Payout History
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="userVipTabsContent">
                    <!-- Tab 1: VIP Roadmap -->
                    <div class="tab-pane fade show active" id="roadmapTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>10 VIP Levels Qualifications Matrix</h4>
                                    <p>Your current status across all 10 VIP levels.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="userRoadmapTable">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Required Left : Right IDs</th>
                                                <th>Required Left : Right Business ($)</th>
                                                <th>Reward ($)</th>
                                                <th>VIP Income Rate</th>
                                                <th>Monthly Repeat ($)</th>
                                                <th>Status</th>
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

                    <!-- Tab 2: Rewards Ledger -->
                    <div class="tab-pane fade" id="qualsTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Earned One-Time VIP Rewards</h4>
                                    <p>History of all one-time VIP Level rewards credited to your VIP Club Wallet.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="userQualsTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>VIP Level</th>
                                                <th>Weaker Leg Business</th>
                                                <th>Reward Amount</th>
                                                <th>Status</th>
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

                    <!-- Tab 3: Monthly Payout History -->
                    <div class="tab-pane fade" id="historyTabContent" role="tabpanel">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Monthly VIP Club Income Ledger</h4>
                                    <p>History of all monthly weaker-leg income and company turnover share payouts.</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="userHistoryTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Closing Month</th>
                                                <th>VIP Level</th>
                                                <th>Weaker Leg Biz</th>
                                                <th>Weaker Leg Payout</th>
                                                <th>Turnover Share Payout</th>
                                                <th>Total Payout ($)</th>
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

                </div>

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            let userQualsTable = $('#userQualsTable').DataTable({ pageLength: 10 });
            let userHistoryTable = $('#userHistoryTable').DataTable({ pageLength: 10 });

            $.getJSON('get_vip_club.php', function(res) {
                if (res.status === 'success' && res.data) {
                    let u = res.data.user;
                    let leg = res.data.leg_details;

                    $('#userVipBadge').text('Current: VIP Level ' + u.highest_level);
                    $('#statVipWallet').text('$ ' + parseFloat(u.vip_club_wallet).toLocaleString('en-US', {minimumFractionDigits:2}));
                    $('#statRewardsEarned').text('$ ' + parseFloat(res.data.total_rewards_earned).toLocaleString('en-US', {minimumFractionDigits:2}));
                    $('#statWeakerBiz').text('$ ' + parseFloat(leg.weaker_leg_business_usd).toLocaleString('en-US', {minimumFractionDigits:2}));

                    if (res.data.next_level) {
                        $('#statNextLevel').text('VIP Level ' + res.data.next_level.level_id);
                    } else {
                        $('#statNextLevel').text('MAX LEVEL ACHIEVED!');
                    }

                    // Leg Progress
                    $('#txtLeftIds').text(leg.left_ids_count + ' IDs');
                    $('#txtRightIds').text(leg.right_ids_count + ' IDs');
                    $('#txtLeftBiz').text('$ ' + parseFloat(leg.left_business_usd).toLocaleString());
                    $('#txtRightBiz').text('$ ' + parseFloat(leg.right_business_usd).toLocaleString());

                    let maxBiz = Math.max(parseFloat(leg.left_business_usd), parseFloat(leg.right_business_usd), 1);
                    let pctLeft = Math.min(100, Math.round((parseFloat(leg.left_business_usd) / maxBiz) * 100));
                    let pctRight = Math.min(100, Math.round((parseFloat(leg.right_business_usd) / maxBiz) * 100));

                    $('#barLeft').css('width', pctLeft + '%');
                    $('#barRight').css('width', pctRight + '%');

                    // Roadmap Matrix
                    let achievedLevels = new Set(res.data.qualifications.map(q => parseInt(q.vip_level)));
                    let roadmapRows = '';
                    res.data.configs.forEach(c => {
                        let isAchieved = achievedLevels.has(parseInt(c.level_id));
                        let badgeHtml = isAchieved ? '<span class="badge bg-success"><i class="fa fa-check me-1"></i> Achieved</span>' : '<span class="badge bg-secondary">Pending</span>';
                        let bizText = (parseFloat(c.req_left_business) >= 1000000) ? ('$ ' + (parseFloat(c.req_left_business)/10000000).toFixed(2) + ' Crore') : ('$ ' + parseFloat(c.req_left_business).toLocaleString());
                        let rptText = (parseFloat(c.monthly_repeat_business) >= 1000000) ? ('$ ' + (parseFloat(c.monthly_repeat_business)/10000000).toFixed(2) + ' Crore') : ('$ ' + parseFloat(c.monthly_repeat_business).toLocaleString());
                        let isTurnover = parseInt(c.has_turnover_share) === 1;

                        roadmapRows += `<tr class="${isAchieved ? 'table-success' : ''}">
                            <td class="fw-bold text-primary">Level ${c.level_id}</td>
                            <td>${c.req_left_ids} : ${c.req_right_ids}</td>
                            <td>${bizText} : ${bizText}</td>
                            <td class="fw-bold text-warning">$ ${parseFloat(c.reward_amount).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                            <td class="fw-bold text-info">${parseFloat(c.vip_income_rate).toFixed(2)}% Weaker Leg ${isTurnover ? ' + 0.5% Turnover' : ''}</td>
                            <td>${rptText}</td>
                            <td>${badgeHtml}</td>
                        </tr>`;
                    });
                    $('#userRoadmapTable tbody').html(roadmapRows);

                    // Qualifications Data
                    userQualsTable.clear();
                    res.data.qualifications.forEach(q => {
                        userQualsTable.row.add([
                            '<span class="badge bg-warning text-dark fw-bold">VIP Level ' + q.vip_level + '</span>',
                            '$' + parseFloat(q.weaker_leg_business).toLocaleString(),
                            '<span class="text-success fw-bold">$' + parseFloat(q.reward_amount).toFixed(2) + '</span>',
                            '<span class="badge bg-success">' + q.reward_status + '</span>',
                            '<small class="text-muted">' + q.qualified_at + '</small>'
                        ]);
                    });
                    userQualsTable.draw();

                    // Monthly Payout Data
                    userHistoryTable.clear();
                    res.data.monthly_payouts.forEach(m => {
                        userHistoryTable.row.add([
                            '<span class="badge bg-light text-dark border">' + m.closing_month + '</span>',
                            '<span class="badge bg-warning text-dark fw-bold">VIP Level ' + m.vip_level + '</span>',
                            '$' + parseFloat(m.weaker_leg_business).toLocaleString(),
                            '<span class="text-info fw-bold">$' + parseFloat(m.weaker_leg_payout).toFixed(2) + '</span>',
                            '<span class="text-purple fw-bold">$' + parseFloat(m.turnover_payout).toFixed(2) + '</span>',
                            '<span class="text-success fw-bold">$' + parseFloat(m.total_payout).toFixed(2) + '</span>',
                            '<span class="badge bg-success">' + m.status + '</span>',
                            '<small class="text-muted">' + m.credited_at + '</small>'
                        ]);
                    });
                    userHistoryTable.draw();
                }
            });
        });
    </script>

</body>
</html>
