<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$growth = getUserGrowthBreakdown($userid, $pdo);
?>

<div class="content-wrapper py-3 px-2 px-md-4">
    <div class="container-fluid">
        <div class="row pt-2 pb-2 mb-3">
            <div class="col-sm-9">
                <h4 class="page-title font-weight-bold" style="color: #0f172a !important; font-size: 22px;">User Growth Overview</h4>
                <nav aria-label="breadcrumb">
                    <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                        <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #0f172a; font-weight: 700;">User Growth</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- 7 Category Summary Cards -->
        <div class="row g-3">
            <div class="col-12 col-md-4 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">1. Profit Income</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo formatCurrency($growth['profit_income']['total_balance']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">2. Profit Sharing</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo formatCurrency($growth['profit_sharing']['total_balance']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">3. Direct Bonus</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo formatCurrency($growth['direct_bonus']['total_balance']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">4. Mentor Income</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo formatCurrency($growth['mentor_income']['total_balance']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">5. Rank Reward</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo count($growth['rank_reward']['history']); ?> Claimed</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">6. VIP Club Income</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo formatCurrency($growth['vip_club']['total_balance']); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-3">
                <div class="card border-0 shadow-sm user-growth-hero-card" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important; border-radius: 18px; color: #ffffff !important;">
                    <div class="card-body p-3">
                        <span class="d-block small text-white-50 font-weight-bold uppercase mb-1" style="font-size: 11px;">7. Company Turnover</span>
                        <h4 class="mb-0 text-white font-weight-bold" style="font-size: 20px;"><?php echo count($growth['company_turnover']['history']); ?> Records</h4>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .user-growth-nav-pills {
            gap: 8px;
            border-bottom: none !important;
            flex-wrap: wrap;
        }
        .user-growth-nav-pills .nav-link {
            border-radius: 12px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 8px 16px !important;
            transition: all 0.2s ease !important;
        }
        .user-growth-nav-pills .nav-link.active,
        .user-growth-nav-pills .nav-link:hover {
            background: #16a34a !important;
            color: #ffffff !important;
            border-color: #16a34a !important;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
        }
        </style>

        <!-- 7 Category Detailed Tabs -->
        <ul class="nav nav-pills user-growth-nav-pills mt-3 mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#g-profit">Profit Income</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-sharing">Profit Sharing</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-direct">Direct Bonus</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-mentor">Mentor Income</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-reward">Rank Reward</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-vip">VIP Club</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#g-turnover">Turnover</a></li>
        </ul>

        <div class="tab-content pt-3">
            <div id="g-profit" class="tab-pane active">
                <div class="card">
                    <div class="card-header">Profit Income Statement</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Subject</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['profit_income']['history'])): ?>
                                        <?php $sr=1; foreach($growth['profit_income']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo formatCurrency($r['amount']); ?></td><td><?php echo $r['created_date']; ?></td><td><?php echo htmlspecialchars($r['subject']); ?></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="4" class="text-muted">No profit income records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-sharing" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">Profit Sharing Statement</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Subject</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['profit_sharing']['history'])): ?>
                                        <?php $sr=1; foreach($growth['profit_sharing']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo formatCurrency($r['amount']); ?></td><td><?php echo $r['created_date']; ?></td><td><?php echo htmlspecialchars($r['subject']); ?></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="4" class="text-muted">No profit sharing records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-direct" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">Direct Bonus Schedule</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Source User</th><th>Total Bonus</th><th>Installment</th><th>Month</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['direct_bonus']['history'])): ?>
                                        <?php $sr=1; foreach($growth['direct_bonus']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo htmlspecialchars($r['source_user_id']); ?></td><td><?php echo formatCurrency($r['total_bonus']); ?></td><td><?php echo formatCurrency($r['installment_amount']); ?> (Inst <?php echo $r['installment_number']; ?>/10)</td><td><?php echo $r['installment_month']; ?></td><td><span class="badge <?php echo ($r['status']==='CREDITED')?'badge-success':'badge-warning'; ?>"><?php echo $r['status']; ?></span></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="6" class="text-muted">No direct bonus records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-mentor" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">Mentor Income Schedule</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Source User</th><th>Contribution %</th><th>Payout Amount</th><th>Closing Month</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['mentor_income']['history'])): ?>
                                        <?php $sr=1; foreach($growth['mentor_income']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo htmlspecialchars($r['source_user_id']); ?></td><td><?php echo $r['contribution_percentage']; ?>%</td><td><?php echo formatCurrency($r['total_payout_amount']); ?></td><td><?php echo $r['closing_month']; ?></td><td><span class="badge <?php echo ($r['status']==='CREDITED')?'badge-success':'badge-warning'; ?>"><?php echo $r['status']; ?></span></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="6" class="text-muted">No mentor income records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-reward" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">Rank & Rewards History</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Subject</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['rank_reward']['history'])): ?>
                                        <?php $sr=1; foreach($growth['rank_reward']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo formatCurrency($r['amount']); ?></td><td><?php echo $r['created_date']; ?></td><td><?php echo htmlspecialchars($r['subject']); ?></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="4" class="text-muted">No rank reward records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-vip" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">VIP Club Qualification History</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>VIP Level</th><th>Weaker Business</th><th>Reward Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['vip_club']['history'])): ?>
                                        <?php $sr=1; foreach($growth['vip_club']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td>Level <?php echo $r['vip_level']; ?></td><td><?php echo formatCurrency($r['weaker_leg_business']); ?></td><td><?php echo formatCurrency($r['reward_amount']); ?></td><td><span class="badge badge-success"><?php echo $r['reward_status']; ?></span></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="5" class="text-muted">No VIP club qualification records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="g-turnover" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">Company Turnover Income Statement</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead><tr><th>#</th><th>Amount</th><th>Date</th><th>Subject</th></tr></thead>
                                <tbody>
                                    <?php if (!empty($growth['company_turnover']['history'])): ?>
                                        <?php $sr=1; foreach($growth['company_turnover']['history'] as $r): ?>
                                            <tr><td><?php echo $sr++; ?></td><td><?php echo formatCurrency($r['amount']); ?></td><td><?php echo $r['created_date']; ?></td><td><?php echo htmlspecialchars($r['subject']); ?></td></tr>
                                        <?php endforeach; ?>
                                    <?php else: ?><tr><td colspan="4" class="text-muted">No company turnover income records.</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'common/footer.php'; ?>
