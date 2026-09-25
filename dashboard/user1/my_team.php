<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'common/connection.php';
include 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header('location:login.php');
    exit;
}

$userid = $_SESSION['userid'];
$uProfile = getuserdatabysponserid($userid);

// Fetch Team Data
$myDirects = getUserTeamMembersDetailed($userid, 'MY_DIRECT', $pdo);
$leftTeam   = getUserTeamMembersDetailed($userid, 'LEFT', $pdo);
$rightTeam  = getUserTeamMembersDetailed($userid, 'RIGHT', $pdo);

// Calculate Totals
$totalDirectBusinessUsd = array_sum(array_column($myDirects, 'investment_usd'));
$totalLeftBusinessUsd   = array_sum(array_column($leftTeam, 'investment_usd'));
$totalRightBusinessUsd  = array_sum(array_column($rightTeam, 'investment_usd'));

$activeTab = trim($_GET['tab'] ?? 'left');
if (!in_array($activeTab, ['direct', 'left', 'right'])) {
    $activeTab = 'left';
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'common/header.php'; ?>

<body class="ananta-user-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA USER DASHBOARD - MY TEAM HIERARCHY STYLING
========================================================= */
body.ananta-user-dashboard,
.content-wrapper {
    background-color: #f8fafc !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif !important;
}

.team-header-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
}
.team-header-card h3,
.team-header-card p,
.team-header-card span,
.team-header-card div,
.team-header-card i {
    color: #000000 !important;
}

.team-stat-card {
    background: #ffffff !important;
    border-radius: 16px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 20px !important;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.team-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.1) !important;
}

.team-stat-title {
    color: #334155 !important;
    font-weight: 800 !important;
    font-size: 13px !important;
    letter-spacing: 0.5px;
    text-transform: uppercase !important;
}

.team-stat-num {
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 24px !important;
}

.team-stat-sub {
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
}

.team-nav-pills .nav-link {
    border-radius: 12px !important;
    font-weight: 700 !important;
    padding: 12px 20px !important;
    color: #0f172a !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    transition: all 0.2s ease !important;
}

.team-nav-pills .nav-link.active {
    background: linear-gradient(135deg, #0284c7 0%, #0f172a 100%) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3) !important;
}

/* Hierarchical Tree Cards Styling */
.tree-node-card {
    background: #ffffff !important;
    border-radius: 14px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 18px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05) !important;
    position: relative;
    color: #0f172a !important;
}

.tree-node-card span,
.tree-node-card div,
.tree-node-card strong,
.tree-node-card p {
    color: #0f172a !important;
}

.tree-indent-level-1 { margin-left: 0px; border-left: 5px solid #0284c7 !important; }
.tree-indent-level-2 { margin-left: 20px; border-left: 5px solid #16a34a !important; }
.tree-indent-level-3 { margin-left: 40px; border-left: 5px solid #f59e0b !important; }
.tree-indent-level-4 { margin-left: 60px; border-left: 5px solid #8b5cf6 !important; }
.tree-indent-level-5 { margin-left: 80px; border-left: 5px solid #ec4899 !important; }
.tree-indent-level-max { margin-left: 90px; border-left: 5px solid #64748b !important; }

@media (max-width: 768px) {
    .tree-indent-level-2 { margin-left: 10px; }
    .tree-indent-level-3 { margin-left: 18px; }
    .tree-indent-level-4 { margin-left: 26px; }
    .tree-indent-level-5 { margin-left: 32px; }
    .tree-indent-level-max { margin-left: 35px; }
}

.badge-position-left {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    font-weight: 800 !important;
    border-radius: 100px;
    padding: 4px 12px;
}

.badge-position-right {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #bbf7d0 !important;
    font-weight: 800 !important;
    border-radius: 100px;
    padding: 4px 12px;
}

/* High Contrast DataTables Styling */
#teamDataTable {
    background: #ffffff !important;
    color: #0f172a !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    border: 1px solid #cbd5e1 !important;
}
#teamDataTable thead th {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 13px !important;
    text-transform: uppercase !important;
    border-bottom: 2px solid #cbd5e1 !important;
    padding: 14px !important;
}
#teamDataTable tbody td {
    color: #0f172a !important;
    font-weight: 600 !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 14px !important;
}
</style>

<div id="wrapper">
<div class="content-wrapper">
  <div class="container-fluid">

    <!-- Header Banner -->
    <div class="card mb-4 team-header-card">
      <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <span class="badge px-3 py-1 mb-2" style="background: #f1f5f9; color: #000000 !important; border: 1px solid #cbd5e1; border-radius: 100px; font-weight: 800; font-size: 11.5px;">ROOT-BASED DOWNLINE NETWORK</span>
          <h3 class="mb-1 font-weight-bold" style="color: #000000 !important;"><i class="fa fa-users mr-2" style="color: #000000 !important;"></i> My Team Structure</h3>
          <p class="mb-0 small" style="color: #000000 !important; font-weight: 600;">View your direct referrals, left root branch, and right root branch downline hierarchy with zero categorization ambiguity.</p>
        </div>
        <div>
          <a href="tree.php" class="btn font-weight-bold px-3 py-2" style="background: #000000; color: #f7f4f4ff !important; border-radius: 10px; border: none;">
            <i class="fa fa-sitemap mr-1" style="color: #f0eaeaff !important;"></i> Visual Tree View
          </a>
        </div>
      </div>
    </div>

    <!-- Summary Stats Grid -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3 mb-md-0">
        <div class="team-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted font-weight-bold small text-uppercase">My Direct Referrals</span>
            <span class="badge badge-primary px-2 py-1"><?php echo count($myDirects); ?> Members</span>
          </div>
          <h4 class="font-weight-bold text-primary mb-1"><?php echo count($myDirects); ?> Members</h4>
          <span class="small text-muted font-weight-bold">Total Direct Volume: <?php echo formatCurrency($totalDirectBusinessUsd); ?></span>
        </div>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <div class="team-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted font-weight-bold small text-uppercase">Left Root Team</span>
            <span class="badge badge-info px-2 py-1"><?php echo count($leftTeam); ?> Downlines</span>
          </div>
          <h4 class="font-weight-bold text-info mb-1"><?php echo count($leftTeam); ?> Members</h4>
          <span class="small text-muted font-weight-bold">Left Root Volume: <?php echo formatCurrency($totalLeftBusinessUsd); ?></span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="team-stat-card">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted font-weight-bold small text-uppercase">Right Root Team</span>
            <span class="badge badge-success px-2 py-1"><?php echo count($rightTeam); ?> Downlines</span>
          </div>
          <h4 class="font-weight-bold text-success mb-1"><?php echo count($rightTeam); ?> Members</h4>
          <span class="small text-muted font-weight-bold">Right Root Volume: <?php echo formatCurrency($totalRightBusinessUsd); ?></span>
        </div>
      </div>
    </div>

    <!-- Category Nav Pills -->
    <ul class="nav nav-pills team-nav-pills mb-4 gap-2">
      <li class="nav-item">
        <a class="nav-link <?php echo ($activeTab === 'left') ? 'active' : ''; ?>" href="my_team.php?tab=left">
          <i class="fa fa-arrow-circle-left me-1"></i> Left Team Branch (<?php echo count($leftTeam); ?>)
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($activeTab === 'right') ? 'active' : ''; ?>" href="my_team.php?tab=right">
          <i class="fa fa-arrow-circle-right me-1"></i> Right Team Branch (<?php echo count($rightTeam); ?>)
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($activeTab === 'direct') ? 'active' : ''; ?>" href="my_team.php?tab=direct">
          <i class="fa fa-user-plus me-1"></i> My Direct Referrals (<?php echo count($myDirects); ?>)
        </a>
      </li>
    </ul>

    <!-- Main Content Card -->
    <div class="card border-0" style="background:#ffffff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 4px 20px rgba(15,23,42,0.05);">
      <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 font-weight-bold text-dark">
          <?php if ($activeTab === 'left'): ?>
            <i class="fa fa-arrow-circle-left text-info mr-2"></i> Left Team Root Branch Downline (<?php echo count($leftTeam); ?> Members)
          <?php elseif ($activeTab === 'right'): ?>
            <i class="fa fa-arrow-circle-right text-success mr-2"></i> Right Team Root Branch Downline (<?php echo count($rightTeam); ?> Members)
          <?php else: ?>
            <i class="fa fa-user-plus text-primary mr-2"></i> My Direct Referrals (<?php echo count($myDirects); ?> Members)
          <?php endif; ?>
        </h5>
      </div>

      <div class="card-body p-4">

        <?php
        $targetList = [];
        if ($activeTab === 'left') $targetList = $leftTeam;
        elseif ($activeTab === 'right') $targetList = $rightTeam;
        else $targetList = $myDirects;
        ?>

        <?php if (empty($targetList)): ?>
          <div class="text-center py-5">
            <i class="fa fa-users text-muted mb-3" style="font-size: 45px;"></i>
            <h5 class="font-weight-bold text-muted mb-1">No Team Members Found</h5>
            <p class="text-muted small mb-0">No team records exist in this branch yet.</p>
          </div>
        <?php else: ?>

          <!-- View Mode Toggle & Hierarchical Cards -->
          <?php if ($activeTab === 'left' || $activeTab === 'right'): ?>
            <div class="mb-4">
              <h6 class="font-weight-bold text-dark mb-3"><i class="fa fa-sitemap text-primary mr-2"></i> Root-Based Downline Tree View</h6>
              <?php foreach ($targetList as $m): ?>
                <?php
                $lvl = (int)($m['level'] ?? 1);
                $indentClass = ($lvl <= 5) ? ("tree-indent-level-" . $lvl) : "tree-indent-level-max";
                $posClass = (strtoupper($m['position'] ?? 'LEFT') === 'RIGHT') ? 'badge-position-right' : 'badge-position-left';
                ?>
                <div class="tree-node-card <?php echo $indentClass; ?>">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                      <span class="badge badge-secondary px-2 py-1 me-2 font-weight-bold" style="border-radius:6px; font-size:11px;">Level <?php echo $lvl; ?></span>
                      <span class="font-weight-bold text-primary" style="font-size:15px;"><?php echo htmlspecialchars($m['userid']); ?></span>
                      <span class="font-weight-bold text-dark ms-2">(<?php echo htmlspecialchars($m['name']); ?>)</span>
                    </div>
                    <div>
                      <span class="<?php echo $posClass; ?>">Node Position: <?php echo htmlspecialchars($m['position'] ?? 'LEFT'); ?></span>
                      <span class="badge <?php echo ($m['status'] === 'Active') ? 'badge-success' : 'badge-danger'; ?> px-3 py-1 ms-2" style="border-radius:100px;"><?php echo $m['status']; ?></span>
                    </div>
                  </div>

                  <div class="row g-2 text-muted small mt-2">
                    <div class="col-md-3 col-6">
                      <strong>Parent Node:</strong> <?php echo htmlspecialchars($m['parent_id'] ?: 'ROOT'); ?>
                    </div>
                    <div class="col-md-3 col-6">
                      <strong>Total Business:</strong> <span class="font-weight-bold text-success"><?php echo formatCurrency($m['investment_usd']); ?></span>
                    </div>
                    <div class="col-md-3 col-6">
                      <strong>Direct Referrals:</strong> <?php echo (int)($m['direct_count'] ?? 0); ?> Members
                    </div>
                    <div class="col-md-3 col-6">
                      <strong>Subtree Downline:</strong> <?php echo (int)($m['downline_count'] ?? 0); ?> Members
                    </div>
                  </div>

                  <div class="mt-2 text-muted small">
                    <i class="fa fa-calendar me-1"></i> Joining Date: <?php echo date('d M Y h:i A', strtotime($m['joining_date'])); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <hr class="my-4">
          <?php endif; ?>

          <!-- Detailed Data Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="teamDataTable">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>User ID</th>
                  <th>Member Name</th>
                  <th>Node Position</th>
                  <?php if ($activeTab !== 'direct'): ?>
                    <th>Parent ID</th>
                    <th>Level</th>
                  <?php endif; ?>
                  <th>Total Business</th>
                  <th>Direct Count</th>
                  <th>Downline Count</th>
                  <th>Activation Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $sr = 1; foreach ($targetList as $m): ?>
                  <tr>
                    <td class="font-weight-bold"><?php echo $sr++; ?></td>
                    <td class="font-weight-bold text-primary"><?php echo htmlspecialchars($m['userid']); ?></td>
                    <td class="font-weight-bold text-dark"><?php echo htmlspecialchars($m['name']); ?></td>
                    <td>
                      <span class="<?php echo (strtoupper($m['position'] ?? 'LEFT') === 'RIGHT') ? 'badge-position-right' : 'badge-position-left'; ?>">
                        <?php echo htmlspecialchars($m['position'] ?? 'LEFT'); ?>
                      </span>
                    </td>
                    <?php if ($activeTab !== 'direct'): ?>
                      <td class="font-weight-bold text-secondary"><?php echo htmlspecialchars($m['parent_id'] ?: 'ROOT'); ?></td>
                      <td><span class="badge badge-secondary px-2 py-1">L<?php echo (int)($m['level'] ?? 1); ?></span></td>
                    <?php endif; ?>
                    <td class="font-weight-bold text-success"><?php echo formatCurrency($m['investment_usd']); ?></td>
                    <td class="font-weight-bold"><?php echo (int)($m['direct_count'] ?? 0); ?></td>
                    <td class="font-weight-bold"><?php echo (int)($m['downline_count'] ?? 0); ?></td>
                    <td class="text-muted" style="font-size:13px;"><?php echo date('d M Y h:i A', strtotime($m['joining_date'])); ?></td>
                    <td>
                      <span class="badge <?php echo ($m['status'] === 'Active') ? 'badge-success' : 'badge-danger'; ?> px-3 py-1" style="border-radius:100px;">
                        <?php echo $m['status']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        <?php endif; ?>

      </div>
    </div>

  </div>
</div>
</div>

<?php include 'common/footer.php'; ?>

<!-- DataTables JS & CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    if ($('#teamDataTable').length) {
        $('#teamDataTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            ordering: false,
            language: {
                search: "Search Members:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    }
});
</script>

</body>
</html>
