<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
<title>Ananta Executive Command Centre - Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg-main: #f8fafc;
  --card-bg: #ffffff;
  --border-color: #e2e8f0;
  --primary-navy: #0f172a;
  --text-muted: #64748b;
  --accent-teal: #10b981;
  --accent-teal-soft: #ecfdf5;
  --accent-blue: #0284c7;
  --accent-blue-soft: #e0f2fe;
  --accent-purple: #8b5cf6;
  --accent-purple-soft: #f3e8ff;
  --accent-rose: #f43f5e;
  --accent-rose-soft: #ffe4e6;
  --accent-amber: #f59e0b;
  --accent-amber-soft: #fef3c7;
}

body.ananta-admin-dashboard {
  background-color: var(--bg-main) !important;
  color: var(--primary-navy) !important;
  font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

.content-wrapper {
  background-color: var(--bg-main) !important;
  padding-top: 20px !important;
  padding-bottom: 80px !important;
}

/* Card Modern Style */
.executive-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 20px !important;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.25s ease-in-out;
  height: 100%;
}

.executive-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08) !important;
  border-color: #cbd5e1 !important;
}

/* Metric Tile Specifics */
.metric-card-link {
  text-decoration: none !important;
  display: block;
  color: inherit;
}

.metric-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.metric-title {
  font-size: 13px;
  color: var(--text-muted);
  font-weight: 600;
  margin-bottom: 4px;
}

.metric-value {
  font-size: 22px;
  font-weight: 800;
  color: var(--primary-navy);
  line-height: 1.2;
}

.metric-trend {
  font-size: 11.5px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-top: 6px;
}

.trend-up {
  color: #10b981;
}

.trend-down {
  color: #ef4444;
}

.trend-neutral {
  color: #64748b;
}

.trend-subtitle {
  color: #94a3b8;
  font-weight: 500;
  font-size: 11px;
}

/* Mobile Responsiveness for 2 equal compact cards per line */
@media (max-width: 767px) {
  .metric-grid-container {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 10px !important;
  }
  .metric-card-item {
    width: 100% !important;
    margin-bottom: 0 !important;
  }
  .executive-card.p-3 {
    padding: 12px 10px !important;
    border-radius: 14px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    height: 100% !important;
    min-height: 110px !important;
  }
  .metric-icon-box {
    width: 32px !important;
    height: 32px !important;
    border-radius: 10px !important;
    font-size: 14px !important;
  }
  .metric-title {
    font-size: 11px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    margin-bottom: 2px !important;
  }
  .metric-value {
    font-size: 15px !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }
}

/* Top Header Bar Pill Controls */
.header-pill-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  background: #ffffff;
  padding: 14px 20px;
  border-radius: 20px;
  border: 1px solid var(--border-color);
  box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
  margin-bottom: 20px;
}

.brand-title-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-icon-logo {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 20px;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.currency-toggle-group {
  display: inline-flex;
  background: #f1f5f9;
  border-radius: 100px;
  padding: 3px;
}

.currency-btn {
  border: none;
  background: transparent;
  padding: 6px 18px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
}

.currency-btn.active {
  background: #10b981;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.date-selector-btn {
  background: #ffffff;
  border: 1px solid var(--border-color);
  padding: 7px 16px;
  border-radius: 12px;
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

/* Welcome Hero Card */
.welcome-hero-card {
  background: linear-gradient(135deg, #e6f7f3 0%, #d1fae5 100%);
  border: 1px solid #a7f3d0 !important;
  border-radius: 20px;
  padding: 22px 28px;
  margin-bottom: 24px;
}

.welcome-hero-title {
  font-size: 22px;
  font-weight: 800;
  color: #065f46;
  margin-bottom: 4px;
}

.welcome-hero-sub {
  font-size: 13.5px;
  color: #047857;
  margin-bottom: 0;
  font-weight: 500;
}

/* Section Header Titles */
.section-heading-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--primary-navy);
  margin-bottom: 2px;
}

.section-heading-sub {
  font-size: 13px;
  color: var(--text-muted);
  margin-bottom: 16px;
  font-weight: 500;
}

/* Filter Time Pills */
.filter-time-pill {
  border: none;
  background: #f1f5f9;
  color: #64748b;
  padding: 5px 14px;
  border-radius: 100px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-time-pill.active {
  background: #10b981;
  color: #ffffff;
}

/* Quick Action Gradient Buttons */
.btn-quick-action {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  border-radius: 14px;
  color: #ffffff !important;
  font-weight: 700;
  font-size: 13.5px;
  text-decoration: none !important;
  border: none;
  margin-bottom: 12px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
  transition: all 0.2s ease;
}

.btn-quick-action:hover {
  transform: translateY(-2px);
  filter: brightness(1.05);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.qa-blue { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); }
.qa-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.qa-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.qa-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }

/* Status Badges */
.status-pill {
  padding: 4px 12px;
  border-radius: 100px;
  font-size: 11px;
  font-weight: 700;
  display: inline-block;
}
.status-active { background: #dcfce7; color: #15803d; }
.status-inactive { background: #ffe4e6; color: #be123c; }
.status-success { background: #d1fae5; color: #047857; }
.status-pending { background: #fef3c7; color: #b45309; }

/* Table Styling */
.table-executive {
  width: 100%;
  margin-bottom: 0;
}
.table-executive th {
  background: #f8fafc;
  color: #64748b;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid var(--border-color);
  padding: 12px 16px;
}
.table-executive td {
  padding: 14px 16px;
  font-size: 13px;
  color: var(--primary-navy);
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.table-executive tr:last-child td {
  border-bottom: none;
}

/* User Avatar Ring */
.user-avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e0f2fe;
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
}

/* Bottom Navigation Bar for Mobile */
.executive-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #ffffff;
  border-top: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 8px 0;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
  z-index: 1040;
}

.bottom-nav-item {
  display: flex;
  flex-column;
  align-items: center;
  text-decoration: none !important;
  color: #64748b;
  font-size: 10.5px;
  font-weight: 600;
  gap: 3px;
}

.bottom-nav-item i {
  font-size: 18px;
}

.bottom-nav-item.active {
  color: #10b981;
}

@media (min-width: 992px) {
  .executive-bottom-nav {
    display: none;
  }
}
</style>
</head>
<?php 
include 'common/header.php'; 

// Fetch real-time comprehensive statistics
$stats = getAdminComprehensiveDashboardStats($pdo);

// Recent Transactions (Limit 5)
$stmtRecent = $pdo->query("SELECT t.id, t.user_id, u.name, t.amount, t.subject, t.type, t.status, t.created_date, t.time 
                           FROM tbl_transaction t 
                           LEFT JOIN user u ON t.user_id = u.userid 
                           ORDER BY t.id DESC LIMIT 5");
$recentTxns = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

// Recent Users (Limit 5)
$stmtRecentUsers = $pdo->query("SELECT userid, name, email, active, status, joining_date FROM user ORDER BY id DESC LIMIT 5");
$recentUsers = $stmtRecentUsers->fetchAll(PDO::FETCH_ASSOC);

// Active vs Inactive calculation
$totalUsersCount = max(1, (int)$stats['total_users']);
$activeUsersCount = (int)$stats['active_users'];
$inactiveUsersCount = (int)$stats['inactive_users'];

$activePct = round(($activeUsersCount / $totalUsersCount) * 100);
$inactivePct = 100 - $activePct;
?>

<body class="ananta-admin-dashboard">

  <div id="wrapper">
    <div class="clearfix"></div>

    <div class="content-wrapper">
      <div class="container-fluid px-3 px-md-4">
      
        <!-- Top Executive Header Controls -->
        <!-- <div class="header-pill-container">
          <div class="brand-title-box">
            <div class="brand-icon-logo">
              <i class="fa fa-shield"></i>
            </div>
            <div>
              <h5 class="mb-0 font-weight-bold" style="font-weight: 800; font-size: 17px; color: #0f172a;">
                Anuata Executive Command Centre
              </h5>
              <span class="text-muted small" style="font-size: 11.5px; font-weight: 600;">Super Admin Overview</span>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="currency-toggle-group">
              <button class="currency-btn active">INR</button>
              <button class="currency-btn">USD</button>
            </div>

            <div class="date-selector-btn">
              <i class="fa fa-calendar text-primary"></i>
              <span><?php echo date('M d, Y'); ?></span>
              <i class="fa fa-chevron-down text-muted" style="font-size:10px;"></i>
            </div>

            <div class="d-flex align-items-center gap-2">
              <div style="position:relative; cursor:pointer;" class="p-2 bg-light rounded-circle">
                <i class="fa fa-bell-o text-secondary" style="font-size:18px;"></i>
                <span style="position:absolute; top:4px; right:4px; width:8px; height:8px; background:#ef4444; border-radius:50%;"></span>
              </div>
              <div class="d-flex align-items-center gap-2 px-2 py-1 bg-light rounded-pill border">
                <div style="width:28px; height:28px; border-radius:50%; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700;">A</div>
                <span class="small font-weight-bold text-dark d-none d-sm-inline">Admin</span>
                <i class="fa fa-angle-down text-muted small"></i>
              </div>
            </div>
          </div>
        </div> -->

        <!-- Welcome Hero Banner -->
        <div class="welcome-hero-card">
          <h4 class="welcome-hero-title">Welcome Back, Admin! 👋</h4>
          <p class="welcome-hero-sub">Here's what's happening with your platform today in real-time.</p>
        </div>

        <!-- 12 ESSENTIAL METRIC CARDS GRID (Exact Design from Reference Screenshot 1) -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4 metric-grid-container">

          <!-- 1. Total Users -->
          <div class="col mb-3 metric-card-item">
            <a href="all_user.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-blue-soft); color: var(--accent-blue);">
                    <i class="fa fa-users"></i>
                  </div>
                </div>
                <div class="metric-title">Total Users</div>
                <div class="metric-value"><?php echo number_format($stats['total_users']); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 2. Active Users -->
          <div class="col mb-3 metric-card-item">
            <a href="active_all_user.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-teal-soft); color: var(--accent-teal);">
                    <i class="fa fa-user-check"></i>
                  </div>
                </div>
                <div class="metric-title">Active Users</div>
                <div class="metric-value"><?php echo number_format($stats['active_users']); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 3. Inactive Users -->
          <div class="col mb-3 metric-card-item">
            <a href="inactive_all_user.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-rose-soft); color: var(--accent-rose);">
                    <i class="fa fa-user-times"></i>
                  </div>
                </div>
                <div class="metric-title">Inactive Users</div>
                <div class="metric-value"><?php echo number_format($stats['inactive_users']); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 4. Total Unlock Access -->
          <div class="col mb-3 metric-card-item">
            <a href="activation_history.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-purple-soft); color: var(--accent-purple);">
                    <i class="fa fa-lock"></i>
                  </div>
                </div>
                <div class="metric-title">Total Unlock Access</div>
                <div class="metric-value" style="font-size:18px;"><?php echo number_format($stats['total_unlock_access']); ?> Users</div>
                
              </div>
            </a>
          </div>

          <!-- 5. Total Investment -->
          <div class="col mb-3 metric-card-item">
            <a href="growth_wallet.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-blue-soft); color: var(--accent-blue);">
                    <i class="fa fa-briefcase"></i>
                  </div>
                </div>
                <div class="metric-title">Total Investment</div>
                <div class="metric-value"><?php echo formatCurrency($stats['total_investment_inr'] / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 6. Withdrawal Paid -->
          <div class="col mb-3 metric-card-item">
            <a href="withdraw-history.php?type=1" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-teal-soft); color: var(--accent-teal);">
                    <i class="fa fa-money"></i>
                  </div>
                </div>
                <div class="metric-title">Withdrawal Paid</div>
                <div class="metric-value" style="color:#10b981;"><?php echo formatCurrency($stats['total_withdrawal_paid'] / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 7. Pending Withdrawal -->
          <div class="col mb-3 metric-card-item">
            <a href="withdraw-history.php?type=0" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-amber-soft); color: var(--accent-amber);">
                    <i class="fa fa-clock-o"></i>
                  </div>
                </div>
                <div class="metric-title">Pending Withdrawal</div>
                <div class="metric-value" style="color:#f59e0b;"><?php echo formatCurrency($stats['pending_withdrawal'] / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 8. Total Withdrawal -->
          <div class="col mb-3 metric-card-item">
            <a href="withdraw-history.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-rose-soft); color: var(--accent-rose);">
                    <i class="fa fa-pause-circle"></i>
                  </div>
                </div>
                <div class="metric-title">Total Withdrawal</div>
                <div class="metric-value" style="color:#f43f5e;"><?php echo formatCurrency(($stats['total_withdrawal_paid'] + $stats['pending_withdrawal']) / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 9. Today's Business -->
          <div class="col mb-3 metric-card-item">
            <a href="reports.php?type=daily" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-blue-soft); color: var(--accent-blue);">
                    <i class="fa fa-calendar-check-o"></i>
                  </div>
                </div>
                <div class="metric-title">Today's Business</div>
                <div class="metric-value"><?php echo formatCurrency($stats['today_business_inr'] / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 10. Monthly Business -->
          <div class="col mb-3 metric-card-item">
            <a href="reports.php?type=monthly" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-purple-soft); color: var(--accent-purple);">
                    <i class="fa fa-calendar"></i>
                  </div>
                </div>
                <div class="metric-title">Monthly Business</div>
                <div class="metric-value"><?php echo formatCurrency($stats['monthly_business_inr'] / getUSDToINRRate($pdo)); ?></div>
                
              </div>
            </a>
          </div>

          <!-- 11. New Registration -->
          <div class="col mb-3 metric-card-item">
            <a href="all_user.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-blue-soft); color: var(--accent-blue);">
                    <i class="fa fa-user-plus"></i>
                  </div>
                </div>
                <div class="metric-title">New Registration</div>
                <div class="metric-value" style="font-size:18px;"><?php echo number_format($stats['total_users']); ?> Total</div>
                
              </div>
            </a>
          </div>

          <!-- 12. Total Withdrawals Summary -->
          <div class="col mb-3 metric-card-item">
            <a href="withdraw-history.php" class="metric-card-link">
              <div class="executive-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="metric-icon-box" style="background: var(--accent-teal-soft); color: var(--accent-teal);">
                    <i class="fa fa-dollar"></i>
                  </div>
                </div>
                <div class="metric-title">Total Withdrawals</div>
                <div class="metric-value" style="color:#10b981;"><?php echo formatCurrency($stats['total_withdrawal_paid'] / getUSDToINRRate($pdo)); ?></div>
               
              </div>
            </a>
          </div>

        </div>

        <!-- ANALYTICS & OPERATIONS SECTION (Exact Design from Reference Screenshot 2) -->
        <div class="mb-4">
          <h4 class="section-heading-title">Analytics & Operations</h4>
          <p class="section-heading-sub">Platform performance, users, transactions and more.</p>

          <div class="row">
            <!-- Platform Overview Line Chart Card -->
            <div class="col-12 col-lg-7 mb-4">
              <div class="executive-card p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                  <h5 class="mb-0 font-weight-bold" style="font-size:17px; color:#0f172a;">Platform Overview</h5>
                  <div class="d-flex gap-1">
                    <button class="filter-time-pill active">7 Days</button>
                    <button class="filter-time-pill">30 Days</button>
                    <button class="filter-time-pill">This Month</button>
                  </div>
                </div>

                <!-- Smooth SVG Line Chart Representation -->
                <div class="my-3">
                  <svg viewBox="0 0 600 180" style="width: 100%; height: auto; max-height: 220px;">
                    <defs>
                      <linearGradient id="chartTealGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"/>
                      </linearGradient>
                    </defs>
                    <!-- Grid Lines -->
                    <line x1="0" y1="30" x2="600" y2="30" stroke="#f1f5f9" stroke-width="1" />
                    <line x1="0" y1="80" x2="600" y2="80" stroke="#f1f5f9" stroke-width="1" />
                    <line x1="0" y1="130" x2="600" y2="130" stroke="#f1f5f9" stroke-width="1" />
                    <text x="5" y="25" fill="#94a3b8" font-size="10">200K</text>
                    <text x="5" y="75" fill="#94a3b8" font-size="10">100K</text>
                    <text x="5" y="125" fill="#94a3b8" font-size="10">0</text>

                    <!-- Path Area Fill -->
                    <path d="M 40,130 C 120,90 200,80 280,100 C 360,60 440,75 560,40 L 560,160 L 40,160 Z" fill="url(#chartTealGrad)" />
                    <!-- Curve Line -->
                    <path d="M 40,130 C 120,90 200,80 280,100 C 360,60 440,75 560,40" fill="none" stroke="#10b981" stroke-width="3.5" stroke-linecap="round" />

                    <!-- Data Nodes -->
                    <circle cx="40" cy="130" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="120" cy="98" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="200" cy="85" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="280" cy="100" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="360" cy="62" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="440" cy="72" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                    <circle cx="560" cy="40" r="4.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />

                    <!-- Dates Labels -->
                    <text x="30" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 20</text>
                    <text x="110" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 21</text>
                    <text x="190" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 22</text>
                    <text x="270" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 23</text>
                    <text x="350" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 24</text>
                    <text x="430" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 25</text>
                    <text x="545" y="175" fill="#64748b" font-size="11" font-weight="600">Apr 26</text>
                  </svg>
                </div>
              </div>
            </div>

            <!-- User Distribution Donut Chart Card -->
            <div class="col-12 col-lg-5 mb-4">
              <div class="executive-card p-4 d-flex flex-column justify-content-between">
                <h5 class="mb-3 font-weight-bold" style="font-size:17px; color:#0f172a;">User Distribution</h5>

                <div class="d-flex align-items-center justify-content-around flex-wrap gap-3 my-2">
                  <!-- Donut SVG representation -->
                  <div style="position:relative; width:150px; height:150px;">
                    <svg viewBox="0 0 36 36" style="width:100%; height:100%; transform: rotate(-90deg);">
                      <!-- Background ring -->
                      <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f43f5e" stroke-width="4.5" stroke-dasharray="100, 100" />
                      <!-- Active ring portion -->
                      <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="4.8" stroke-dasharray="<?php echo $activePct; ?>, 100" stroke-linecap="round" />
                    </svg>
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
                      <span style="font-size:22px; font-weight:800; color:#0f172a; line-height:1;"><?php echo number_format($stats['total_users']); ?></span>
                      <span style="font-size:11px; color:#64748b; font-weight:600; margin-top:2px;">Total Users</span>
                    </div>
                  </div>

                  <!-- Legend -->
                  <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                      <span style="width:12px; height:12px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                      <span style="font-size:13.5px; font-weight:700; color:#0f172a;">Active</span>
                      <span style="font-size:13px; font-weight:600; color:#64748b;"><?php echo number_format($stats['active_users']); ?> (<?php echo $activePct; ?>%)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <span style="width:12px; height:12px; border-radius:50%; background:#f43f5e; display:inline-block;"></span>
                      <span style="font-size:13.5px; font-weight:700; color:#0f172a;">Inactive</span>
                      <span style="font-size:13px; font-weight:600; color:#64748b;"><?php echo number_format($stats['inactive_users']); ?> (<?php echo $inactivePct; ?>%)</span>
                    </div>
                  </div>
                </div>

                <div class="border-top pt-3 text-center text-muted small" style="font-weight:600;">
                  Live ratio of activated vs pending platform accounts
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- DATA TABLES SECTION (Exact Design from Reference Screenshot 2) -->
        <div class="row">
          <!-- Recent Users Table -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="executive-card p-0 overflow-hidden">
              <div class="p-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 font-weight-bold" style="font-size:16px; color:#0f172a;">Recent Users</h5>
                <a href="all_user.php" class="text-success font-weight-bold small text-decoration-none">View All</a>
              </div>
              <div class="table-responsive">
                <table class="table-executive">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th>Joined On</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recentUsers)): ?>
                      <tr><td colspan="5" class="text-center py-4 text-muted">No users found.</td></tr>
                    <?php else: foreach ($recentUsers as $idx => $usr): ?>
                      <tr>
                        <td class="font-weight-bold"><?php echo $idx + 1; ?></td>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-circle">
                              <i class="fa fa-user"></i>
                            </div>
                            <span class="font-weight-bold"><?php echo htmlspecialchars($usr['name']); ?></span>
                          </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($usr['email']); ?></td>
                        <td>
                          <span class="status-pill <?php echo ($usr['active'] == '1' || $usr['status'] == '1') ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ($usr['active'] == '1' || $usr['status'] == '1') ? 'Active' : 'Inactive'; ?>
                          </span>
                        </td>
                        <td class="text-muted small"><?php echo htmlspecialchars($usr['joining_date'] ?? date('M d, Y')); ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Recent Transactions Table -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="executive-card p-0 overflow-hidden">
              <div class="p-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 font-weight-bold" style="font-size:16px; color:#0f172a;">Recent Transactions</h5>
                <a href="pin_wallet_amount_history.php" class="text-success font-weight-bold small text-decoration-none">View All</a>
              </div>
              <div class="table-responsive">
                <table class="table-executive">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>User</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recentTxns)): ?>
                      <tr><td colspan="6" class="text-center py-4 text-muted">No recent transactions recorded.</td></tr>
                    <?php else: foreach ($recentTxns as $idx => $tx): ?>
                      <tr>
                        <td class="font-weight-bold"><?php echo $idx + 1; ?></td>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-circle" style="background:#e0e7ff; color:#6366f1;">
                              <i class="fa fa-exchange"></i>
                            </div>
                            <span class="font-weight-bold"><?php echo htmlspecialchars($tx['name'] ?? $tx['user_id']); ?></span>
                          </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($tx['subject'] ?: $tx['type']); ?></td>
                        <td class="font-weight-bold text-dark"><?php echo formatCurrency((float)$tx['amount'] / getUSDToINRRate($pdo)); ?></td>
                        <td>
                          <span class="status-pill <?php echo strtolower($tx['status'])==='pending' ? 'status-pending' : 'status-success'; ?>">
                            <?php echo ucfirst(htmlspecialchars($tx['status'] ?: 'Success')); ?>
                          </span>
                        </td>
                        <td class="text-muted small"><?php echo htmlspecialchars($tx['created_date'] ?? date('M d, Y')); ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- BOTTOM SPLIT SECTION: SYSTEM ALERTS & QUICK ACTIONS (Exact Design from Reference Screenshot 2) -->
        <div class="row">
          <!-- System Alerts -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="executive-card p-4">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0 font-weight-bold" style="font-size:16px; color:#0f172a;">System Alerts</h5>
                <a href="notification_centre.php" class="text-success font-weight-bold small text-decoration-none">View All</a>
              </div>

              <div class="d-flex flex-column gap-3">
                <!-- Alert 1 -->
                <div class="d-flex align-items-center gap-3">
                  <div style="width:36px; height:36px; border-radius:50%; background:#d1fae5; color:#10b981; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-check-circle"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">New user registered</div>
                    <div style="font-size:11.5px; color:#64748b;">Member joined the platform.</div>
                  </div>
                  <span style="font-size:11px; color:#94a3b8; font-weight:600;">2 hours ago</span>
                </div>

                <!-- Alert 2 -->
                <div class="d-flex align-items-center gap-3">
                  <div style="width:36px; height:36px; border-radius:50%; background:#fef3c7; color:#f59e0b; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-exclamation-triangle"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">Withdrawal pending</div>
                    <div style="font-size:11.5px; color:#64748b;"><?php echo formatCurrency($stats['pending_withdrawal'] / getUSDToINRRate($pdo)); ?> withdrawal is under review.</div>
                  </div>
                  <span style="font-size:11px; color:#94a3b8; font-weight:600;">4 hours ago</span>
                </div>

                <!-- Alert 3 -->
                <div class="d-flex align-items-center gap-3">
                  <div style="width:36px; height:36px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-usd"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">Unlock revenue updated</div>
                    <div style="font-size:11.5px; color:#64748b;"><?php echo formatCurrency($stats['unlock_revenue_inr'] / getUSDToINRRate($pdo)); ?> collected.</div>
                  </div>
                  <span style="font-size:11px; color:#94a3b8; font-weight:600;">6 hours ago</span>
                </div>

                <!-- Alert 4 -->
                <div class="d-flex align-items-center gap-3">
                  <div style="width:36px; height:36px; border-radius:50%; background:#f3e8ff; color:#8b5cf6; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-shield"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">System update</div>
                    <div style="font-size:11.5px; color:#64748b;">All 12 isolated wallets functioning normally.</div>
                  </div>
                  <span style="font-size:11px; color:#94a3b8; font-weight:600;">1 day ago</span>
                </div>

                <!-- Alert 5 -->
                <div class="d-flex align-items-center gap-3">
                  <div style="width:36px; height:36px; border-radius:50%; background:#ffe4e6; color:#f43f5e; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-headphones"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">Support tickets open</div>
                    <div style="font-size:11.5px; color:#64748b;"><?php echo number_format($stats['support_tickets_open']); ?> pending user inquiries.</div>
                  </div>
                  <span style="font-size:11px; color:#94a3b8; font-weight:600;">1 day ago</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="col-12 col-lg-6 mb-4">
            <div class="executive-card p-4">
              <h5 class="mb-3 font-weight-bold" style="font-size:16px; color:#0f172a;">Quick Actions</h5>

              <a href="monthly-profit-closing.php" class="btn-quick-action qa-blue">
                <span><i class="fa fa-calendar-check-o me-2"></i> Pay Monthly Profit Closing</span>
                <i class="fa fa-chevron-right"></i>
              </a>

              <a href="generation-income.php" class="btn-quick-action qa-purple">
                <span><i class="fa fa-sitemap me-2"></i> Pay Generation Income</span>
                <i class="fa fa-chevron-right"></i>
              </a>

              <a href="direct-bonus.php" class="btn-quick-action qa-green">
                <span><i class="fa fa-gift me-2"></i> Pay Direct Bonus</span>
                <i class="fa fa-chevron-right"></i>
              </a>

              <a href="admin_audit_controls.php?tab=controls" class="btn-quick-action qa-indigo">
                <span><i class="fa fa-sliders me-2"></i> Manage Website Controls (ON/OFF)</span>
                <i class="fa fa-chevron-right"></i>
              </a>
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