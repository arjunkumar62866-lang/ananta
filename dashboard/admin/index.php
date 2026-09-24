<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
body.ananta-admin-dashboard {
  background-color: #f4f6f8 !important;
  color: #334155 !important;
  font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
}
.content-wrapper {
  background-color: #f4f6f8 !important;
  padding-top: 85px !important;
}
.topbar-nav .navbar {
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  border-bottom: 1px solid #e2e8f0 !important;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
}
.topbar-nav .nav-link {
  color: #0f172a !important;
}
.wallet-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    height: 100%;
    color: #0f172a !important;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
    text-decoration: none !important;
    display: block;
}

.wallet-box:hover {
    transform: translateY(-3px);
    border-color: #0284c7 !important;
    box-shadow: 0 15px 35px rgba(2, 132, 199, 0.12);
}

.wallet-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%);
    border: 1px solid rgba(2, 132, 199, 0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wallet-title {
    font-size: 12.5px;
    color: #64748b;
    font-weight: 600;
}

.wallet-amount {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>
</head>
<?php 
include 'common/header.php'; 

// Fetch real-time comprehensive statistics
$stats = getAdminComprehensiveDashboardStats($pdo);

// Recent Transactions (Limit 10)
$stmtRecent = $pdo->query("SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.created_date, t.time 
                           FROM tbl_transaction t 
                           LEFT JOIN user u ON t.user_id = u.userid 
                           ORDER BY t.id DESC LIMIT 10");
$recentTxns = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

// Monthly Business Chart Data
$chartData = $pdo->query("SELECT DATE_FORMAT(MIN(date), '%b %Y') as month_label, SUM(package) as total_vol 
                          FROM tbl_roi_one 
                          GROUP BY DATE_FORMAT(date, '%Y-%m') 
                          ORDER BY MIN(date) ASC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="ananta-admin-dashboard">

  <div id="wrapper">
    <div class="clearfix"></div>

    <div class="content-wrapper">
      <div class="container-fluid">
      
      <!-- Welcome Banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.12) 0%, rgba(22, 163, 74, 0.12) 100%), #ffffff; border-radius: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid rgba(2, 132, 199, 0.2) !important;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="welcome-avatar-glow" style="width: 54px; height: 54px; border-radius: 50%; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);">
                  <i class="fa fa-shield"></i>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">ADMIN CONSOLE</span>
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">System Real-Time Analytics</span>
                  </div>
                  <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Ananta Executive Command Centre 🛡️
                  </h4>
                  <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                    Real-time monitoring of 26 core platform metrics, income payouts, deposit workflows, and member management.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ALL 26 DASHBOARD METRIC CARDS -->
      <div class="row row-group m-0">

        <!-- 1. Total Users -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="all_user.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon"><i class="fa fa-users" style="font-size:20px; color:#0284c7;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Total Users</h6>
                <h5 class="wallet-amount mb-0"><?php echo number_format($stats['total_users']); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 2. Active Users -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="active_all_user.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(22,163,74,0.1); border-color:rgba(22,163,74,0.2);"><i class="fa fa-user-check" style="font-size:20px; color:#16a34a;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Active Users</h6>
                <h5 class="wallet-amount mb-0 text-success"><?php echo number_format($stats['active_users']); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 3. Inactive Users -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="inactive_all_user.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.2);"><i class="fa fa-user-times" style="font-size:20px; color:#ef4444;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Inactive Users</h6>
                <h5 class="wallet-amount mb-0 text-danger"><?php echo number_format($stats['inactive_users']); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 4. Total Unlock Access -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="activation_history.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(139,92,246,0.1); border-color:rgba(139,92,246,0.2);"><i class="fa fa-lock" style="font-size:20px; color:#8b5cf6;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Total Unlock Access</h6>
                <h5 class="wallet-amount mb-0"><?php echo number_format($stats['total_unlock_access']); ?> Users</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 5. Total Investment -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="growth_wallet.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon"><i class="fa fa-briefcase" style="font-size:20px; color:#0284c7;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Total Investment</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['total_investment_inr'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 6. Total Income Distributed -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="income_management.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(22,163,74,0.1);"><i class="fa fa-money" style="font-size:20px; color:#16a34a;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Total Income Paid</h6>
                <h5 class="wallet-amount mb-0 text-success">₹<?php echo number_format($stats['total_income_distributed'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 7. Total Withdrawal Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="withdraw-history.php?type=1" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(16,185,129,0.1);"><i class="fa fa-check-circle" style="font-size:20px; color:#10b981;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Total Withdrawal Paid</h6>
                <h5 class="wallet-amount mb-0 text-success">₹<?php echo number_format($stats['total_withdrawal_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 8. Pending Withdrawal -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="withdraw-history.php?type=0" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(245,158,11,0.1);"><i class="fa fa-clock-o" style="font-size:20px; color:#f59e0b;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Pending Withdrawal</h6>
                <h5 class="wallet-amount mb-0 text-warning">₹<?php echo number_format($stats['pending_withdrawal'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 9. Hold Withdrawal -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="withdraw-history.php?type=hold" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(239,68,68,0.1);"><i class="fa fa-pause-circle" style="font-size:20px; color:#ef4444;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Hold Withdrawal</h6>
                <h5 class="wallet-amount mb-0 text-danger">₹0.00</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 10. Today's Business -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="reports.php?type=daily" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon"><i class="fa fa-calendar-check-o" style="font-size:20px; color:#0284c7;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Today's Business</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['today_business_inr'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 11. Monthly Business -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="reports.php?type=monthly" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(139,92,246,0.1);"><i class="fa fa-calendar" style="font-size:20px; color:#8b5cf6;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Monthly Business</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['monthly_business_inr'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 12. New User Registration -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="all_user.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(6,182,212,0.1);"><i class="fa fa-user-plus" style="font-size:20px; color:#06b6d4;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">New Registrations</h6>
                <h5 class="wallet-amount mb-0"><?php echo number_format($stats['total_users']); ?> Total</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 13. Company Revenue from $11 Unlock Access -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="activation_history.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(16,185,129,0.1);"><i class="fa fa-dollar" style="font-size:20px; color:#10b981;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">$11 Unlock Revenue</h6>
                <h5 class="wallet-amount mb-0 text-success">₹<?php echo number_format($stats['unlock_revenue_inr'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 14. Company Balance -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="main_wallet.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(99,102,241,0.1);"><i class="fa fa-building" style="font-size:20px; color:#6366f1;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Company Balance</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['total_investment_inr'] - $stats['total_withdrawal_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 15. Profit Income Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="monthly-profit-closing.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon"><i class="fa fa-percent" style="font-size:20px; color:#0284c7;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Profit Income Paid</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['profit_income_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 16. Profit Sharing Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="daily-level-income.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(236,72,153,0.1);"><i class="fa fa-pie-chart" style="font-size:20px; color:#ec4899;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Profit Sharing Paid</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['profit_sharing_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 17. Direct Bonus Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="direct-bonus.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(245,158,11,0.1);"><i class="fa fa-gift" style="font-size:20px; color:#f59e0b;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Direct Bonus Paid</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['direct_bonus_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 18. Mentor Income Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="mentor-income.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(6,182,212,0.1);"><i class="fa fa-user-secret" style="font-size:20px; color:#06b6d4;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Mentor Income Paid</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['mentor_income_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 19. Rank Reward Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="vip-club.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(234,179,8,0.1);"><i class="fa fa-trophy" style="font-size:20px; color:#eab308;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Rank Reward Paid</h6>
                <h5 class="wallet-amount mb-0">₹<?php echo number_format($stats['vip_club_income_paid'], 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 20. VIP Club Income Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="vip-club.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(59,130,246,0.1);"><i class="fa fa-star" style="font-size:20px; color:#3b82f6;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">VIP Club Income Paid</h6>
                <h5 class="wallet-amount mb-0">$<?php echo number_format($stats['vip_club_income_paid'] / 90.0, 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 21. Company Turnover Income Paid -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="vip-club.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(16,185,129,0.1);"><i class="fa fa-line-chart" style="font-size:20px; color:#10b981;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Company Turnover Paid</h6>
                <h5 class="wallet-amount mb-0">$<?php echo number_format(($stats['total_investment_inr'] / 90.0) * 0.005, 2); ?></h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 22. KYC Pending -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="pending_kyc.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(245,158,11,0.1);"><i class="fa fa-id-card" style="font-size:20px; color:#f59e0b;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">KYC Pending</h6>
                <h5 class="wallet-amount mb-0 text-warning"><?php echo number_format($stats['kyc_pending_count']); ?> Applications</h5>
              </div>
            </div>
          </a>
        </div>

        <!-- 23. Support Tickets -->
        <div class="col-12 col-md-6 col-lg-3 mb-3">
          <a href="support_tickets.php" class="wallet-box p-3">
            <div class="d-flex align-items-center">
              <div class="wallet-icon" style="background:rgba(239,68,68,0.1);"><i class="fa fa-headset" style="font-size:20px; color:#ef4444;"></i></div>
              <div class="ml-3">
                <h6 class="wallet-title mb-1">Support Tickets Open</h6>
                <h5 class="wallet-amount mb-0 text-danger"><?php echo number_format($stats['support_tickets_open']); ?> Open</h5>
              </div>
            </div>
          </a>
        </div>

      </div>

      <!-- Quick Operations Bar -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);">
            <div class="card-body p-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                  <h5 class="mb-0 font-weight-bold" style="color: #0f172a; font-size: 16px;">Quick Financial Operations & Closings</h5>
                  <p class="mb-0 text-muted small">Execute income settlements, payout closing, and website control toggles</p>
                </div>
                <span class="badge" style="background: rgba(22, 163, 74, 0.1); color: #16a34a; font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 100px;">EXECUTIVE ACTIONS</span>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <a href="monthly-profit-closing.php" class="btn text-white font-weight-bold px-3 py-2 mr-2 mb-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border-radius: 12px; font-size: 13.5px; border: none;">
                  <i class="fa fa-calculator mr-1"></i> Pay Monthly Profit Closing
                </a>
                <a href="generation-income.php" class="btn text-white font-weight-bold px-3 py-2 mr-2 mb-2" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; font-size: 13.5px; border: none;">
                  <i class="fa fa-sitemap mr-1"></i> Pay Generation Income
                </a>
                <a href="direct-bonus.php" class="btn text-white font-weight-bold px-3 py-2 mr-2 mb-2" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); border-radius: 12px; font-size: 13.5px; border: none;">
                  <i class="fa fa-gift mr-1"></i> Pay Direct Bonus
                </a>
                <a href="admin_audit_controls.php?tab=controls" class="btn text-white font-weight-bold px-3 py-2 mr-2 mb-2" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 12px; font-size: 13.5px; border: none;">
                  <i class="fa fa-sliders mr-1"></i> Manage Website Controls (ON/OFF)
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Transactions Table (Full Width Top) -->
      <div class="row mb-4">
        <div class="col-12 mb-4">
          <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
              <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-history text-primary mr-2"></i> Recent Platform Transactions</h5>
              <a href="pin_wallet_amount_history.php" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">View All</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0">
                  <thead style="background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase;">
                    <tr>
                      <th class="py-3 px-4">Txn ID</th>
                      <th class="py-3">User ID</th>
                      <th class="py-3">Type</th>
                      <th class="py-3">Amount</th>
                      <th class="py-3">Subject</th>
                      <th class="py-3 px-4">Date</th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 13px; color: #0f172a;">
                    <?php if (empty($recentTxns)): ?>
                      <tr><td colspan="6" class="text-center py-4 text-muted">No recent transactions recorded.</td></tr>
                    <?php else: foreach ($recentTxns as $tx): ?>
                      <tr>
                        <td class="px-4 font-weight-bold">#<?php echo $tx['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($tx['user_id']); ?></strong></td>
                        <td>
                          <span class="badge <?php echo $tx['type']==='Credit'?'badge-success':'badge-danger'; ?> px-2 py-1">
                            <?php echo htmlspecialchars($tx['type']); ?>
                          </span>
                        </td>
                        <td class="font-weight-bold text-primary">₹<?php echo number_format((float)$tx['amount'], 2); ?></td>
                        <td class="small text-muted"><?php echo htmlspecialchars($tx['subject']); ?></td>
                        <td class="px-4 small text-muted"><?php echo htmlspecialchars($tx['created_date']); ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Performance Graph (Full Width Below) -->
      <div class="row mb-4">
        <div class="col-12 mb-4">
          <div class="card border-0" style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15,23,42,0.05);">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between">
              <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-line-chart text-success mr-2"></i> Financial Performance Graph</h5>
              <div class="text-muted small">Real-Time Revenue Analytics</div>
            </div>
            <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
              <div>
                <span class="text-muted small font-weight-bold d-block mb-1">TOTAL INVESTMENT VS WITHDRAWAL</span>
                <h3 class="font-weight-bold text-primary mb-3">₹<?php echo number_format($stats['total_investment_inr'], 2); ?></h3>
              </div>
              
              <!-- SVG Financial Chart Representation -->
              <div class="my-4 px-3">
                <svg viewBox="0 0 600 140" style="width: 100%; max-height: 220px;">
                  <defs>
                    <linearGradient id="gradInv" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" stop-color="#0284c7" stop-opacity="0.4"/>
                      <stop offset="100%" stop-color="#0284c7" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="gradWd" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" stop-color="#16a34a" stop-opacity="0.4"/>
                      <stop offset="100%" stop-color="#16a34a" stop-opacity="0.0"/>
                    </linearGradient>
                  </defs>
                  <path d="M 10,110 Q 150,40 300,70 T 590,15 L 590,135 L 10,135 Z" fill="url(#gradInv)" />
                  <path d="M 10,110 Q 150,40 300,70 T 590,15" fill="none" stroke="#0284c7" stroke-width="4" />
                  <path d="M 10,120 Q 150,85 300,95 T 590,60 L 590,135 L 10,135 Z" fill="url(#gradWd)" />
                  <path d="M 10,120 Q 150,85 300,95 T 590,60" fill="none" stroke="#16a34a" stroke-width="4" />
                </svg>
              </div>

              <div class="d-flex justify-content-around text-center border-top pt-3">
                <div>
                  <span class="small text-muted d-block"><i class="fa fa-circle text-primary mr-1"></i> Total Investments</span>
                  <h5 class="text-primary font-weight-bold mb-0">₹<?php echo number_format($stats['total_investment_inr'], 2); ?></h5>
                </div>
                <div>
                  <span class="small text-muted d-block"><i class="fa fa-circle text-success mr-1"></i> Total Paid Withdrawals</span>
                  <h5 class="text-success font-weight-bold mb-0">₹<?php echo number_format($stats['total_withdrawal_paid'], 2); ?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
  <?php include 'common/footer.php'; ?>
  </div>
</body>
</html>