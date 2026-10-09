<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include __DIR__ . '/common/header.php';

// Security: Admin Authentication Guard
if (!isset($_SESSION['auserid'])) {
    header("Location: login.php");
    exit();
}

// -------------------------------------------------------------
// 1. DATE RANGE HANDLING & VALIDATION (Default to Today for recent registrations)
// -------------------------------------------------------------
$cToday        = date('Y-m-d');
$fromDateInput = trim($_GET['from_date'] ?? $cToday);
$toDateInput   = trim($_GET['to_date'] ?? $cToday);

$validationError = '';
$isValidRange    = true;

// Validate date format YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDateInput) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDateInput)) {
    $validationError = "Invalid date format specified. Please use YYYY-MM-DD.";
    $isValidRange    = false;
} elseif ($fromDateInput > $toDateInput) {
    $validationError = "Validation Error: 'From Date' ({$fromDateInput}) cannot be greater than 'To Date' ({$toDateInput}). Please select a valid date range.";
    $isValidRange    = false;
}

$reportData    = [];
$dailyMap      = [];
$totalPeriod   = 0;
$userList      = [];
$daysCount     = 1;
$dailyAverage  = 0.0;
$todayCount    = 0;

$stmtToday = $pdo->prepare("SELECT COUNT(*) FROM user WHERE (joining_date = :today OR DATE(joining_date) = :today)");
$stmtToday->execute([':today' => $cToday]);
$todayCount = (int)$stmtToday->fetchColumn();

if ($isValidRange) {
    // -------------------------------------------------------------
    // 2. QUERY DAILY BREAKDOWN
    // -------------------------------------------------------------
    $stmtDaily = $pdo->prepare("
        SELECT 
            DATE(joining_date) as reg_date,
            COUNT(*) as reg_count
        FROM user
        WHERE (joining_date BETWEEN :from_dt AND :to_dt)
           OR (DATE(joining_date) BETWEEN :from_dt AND :to_dt)
        GROUP BY DATE(joining_date)
    ");
    $stmtDaily->execute([':from_dt' => $fromDateInput, ':to_dt' => $toDateInput]);
    $rawDaily = $stmtDaily->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawDaily as $r) {
        if (!empty($r['reg_date'])) {
            $dailyMap[$r['reg_date']] = (int)$r['reg_count'];
        }
    }

    // -------------------------------------------------------------
    // 3. GENERATE FULL SEQUENTIAL DATE RANGE (INCLUDE ZERO DAYS)
    // -------------------------------------------------------------
    $dtStart = new DateTime($fromDateInput);
    $dtEnd   = new DateTime($toDateInput);
    $dtEnd->modify('+1 day'); // inclusive

    $interval = new DateInterval('P1D');
    $datePeriod = new DatePeriod($dtStart, $interval, $dtEnd);

    $dailyBreakdown = [];
    foreach ($datePeriod as $dt) {
        $dStr = $dt->format('Y-m-d');
        $cnt  = $dailyMap[$dStr] ?? 0;
        $dailyBreakdown[] = [
            'date'       => $dStr,
            'day_name'   => $dt->format('D'),
            'formatted'  => $dt->format('d M Y'),
            'count'      => $cnt,
            'is_today'   => ($dStr === $cToday),
            'is_future'  => ($dStr > $cToday)
        ];
        $totalPeriod += $cnt;
    }

    $daysCount = count($dailyBreakdown);
    $dailyAverage = ($daysCount > 0) ? round($totalPeriod / $daysCount, 2) : 0.0;

    // -------------------------------------------------------------
    // 4. FETCH DETAILED USER RECORDS FOR SELECTED PERIOD
    // -------------------------------------------------------------
    $userSearch = trim($_GET['user_search'] ?? '');
    $searchSql  = "";
    $params     = [':from_dt' => $fromDateInput, ':to_dt' => $toDateInput];

    if (!empty($userSearch)) {
        $searchSql = " AND (u.userid LIKE :search OR u.name LIKE :search OR u.sponserid LIKE :search) ";
        $params[':search'] = '%' . $userSearch . '%';
    }

    $stmtUsers = $pdo->prepare("
        SELECT 
            u.id,
            u.userid,
            u.name,
            u.mobile,
            u.email,
            u.sponserid,
            u.sponsername,
            u.active,
            u.joining_date,
            u.time
        FROM user u
        WHERE ((u.joining_date BETWEEN :from_dt AND :to_dt) OR (DATE(u.joining_date) BETWEEN :from_dt AND :to_dt))
        {$searchSql}
        ORDER BY u.id DESC
        LIMIT 500
    ");
    $stmtUsers->execute($params);
    $userList = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
/* Responsive High contrast form control styling */
.form-control, select.form-control, input[type="text"].form-control, input[type="date"].form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 12px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    opacity: 1 !important;
    width: 100% !important;
    box-sizing: border-box !important;
    height: 44px !important;
}
.form-control:focus, select.form-control:focus, input[type="text"].form-control:focus, input[type="date"].form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
}
label.form-label-custom {
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 6px !important;
    display: block !important;
    font-size: 13px !important;
}

/* Stat Metric Box */
.stat-metric-card {
    background: #ffffff !important;
    border-radius: 16px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 16px !important;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04) !important;
    height: 100%;
}
.stat-metric-value {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a !important;
    margin-top: 4px;
    word-break: break-word;
}
.stat-metric-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Table Card & Responsive Table Rules */
.ananta-table-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden;
}
.table-responsive {
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}
.table {
    margin-bottom: 0 !important;
    width: 100% !important;
}
.table thead th {
    white-space: nowrap !important;
    padding: 12px 14px !important;
}
.table tbody td {
    white-space: nowrap !important;
    padding: 12px 14px !important;
    vertical-align: middle !important;
}

.zero-day-badge {
    background: #f1f5f9;
    color: #64748b;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 11px;
}

.active-day-badge {
    background: #dcfce7;
    color: #15803d;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 12px;
}

.today-highlight {
    background: #f0f9ff !important;
    border-left: 4px solid #0284c7 !important;
}

@media (max-width: 767.98px) {
    .content-wrapper {
        padding-top: 75px !important;
        padding-bottom: 110px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    .header-actions-group {
        width: 100% !important;
        margin-top: 10px;
    }
    .header-actions-group .btn {
        width: 100% !important;
    }
    .stat-metric-card {
        padding: 12px !important;
    }
    .stat-metric-value {
        font-size: 18px !important;
    }
    .stat-metric-label {
        font-size: 10px !important;
    }
}
</style>

<body class="ananta-admin-dashboard bg-theme bg-theme1">
<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- HEADER BANNER -->
        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0284c7 0%, #0f172a 100%); border-radius: 20px; box-shadow: 0 10px 25px rgba(2, 132, 199, 0.2);">
            <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge badge-light text-primary px-3 py-1 mb-2" style="border-radius: 100px; font-weight: 700;">REGISTRATION ANALYTICS</span>
                    <h3 class="mb-1 text-white font-weight-bold">Daily New Registration Report</h3>
                    <p class="mb-0 text-white-50 small">Showing recent new user registrations. Use date filter to view past historical data.</p>
                </div>
                <div class="header-actions-group">
                    <a href="all_user.php" class="btn btn-light font-weight-bold px-3 py-2" style="border-radius: 10px;">
                        <i class="zmdi zmdi-accounts-list mr-1"></i> All Users Directory
                    </a>
                </div>
            </div>
        </div>

        <!-- ERROR ALERT FOR INVALID DATE RANGE -->
        <?php if (!empty($validationError)): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 14px;">
                <i class="zmdi zmdi-alert-triangle mr-2"></i> <?php echo htmlspecialchars($validationError); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- DATE FILTER CARD -->
        <div class="card ananta-table-card mb-4 p-4">
            <form method="GET" action="registration-report.php" class="row">
                <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
                    <label class="form-label-custom">📅 FROM DATE</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($fromDateInput); ?>" required>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
                    <label class="form-label-custom">📅 TO DATE</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($toDateInput); ?>" required>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-6 col-12 mb-3">
                    <label class="form-label-custom">🔍 SEARCH USER / SPONSOR</label>
                    <input type="text" name="user_search" class="form-control" placeholder="User ID, Name, or Sponsor..." value="<?php echo htmlspecialchars($_GET['user_search'] ?? ''); ?>">
                </div>

                <div class="col-xl-2 col-lg-2 col-md-6 col-12 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary font-weight-bold w-100" style="height: 44px; border-radius: 10px; background: #0284c7; border: none;">
                        <i class="zmdi zmdi-filter-list mr-1"></i> Filter Report
                    </button>
                </div>
            </form>
        </div>

        <?php if ($isValidRange): ?>
            <!-- STATS METRICS SUMMARY CARDS -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-3 col-md-6 col-6 mb-3">
                    <div class="stat-metric-card" style="border-left: 4px solid #0284c7 !important;">
                        <div class="stat-metric-label">TODAY'S REGISTRATIONS</div>
                        <div class="stat-metric-value" style="color: #0284c7 !important;"><?php echo number_format($todayCount); ?></div>
                        <div class="small text-muted font-weight-bold mt-1">Date: <?php echo date('d M Y'); ?></div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-6 mb-3">
                    <div class="stat-metric-card" style="border-left: 4px solid #16a34a !important;">
                        <div class="stat-metric-label">PERIOD TOTAL</div>
                        <div class="stat-metric-value" style="color: #16a34a !important;"><?php echo number_format($totalPeriod); ?></div>
                        <div class="small text-muted font-weight-bold mt-1"><?php echo date('d M', strtotime($fromDateInput)); ?> - <?php echo date('d M Y', strtotime($toDateInput)); ?></div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-6 mb-3">
                    <div class="stat-metric-card" style="border-left: 4px solid #9333ea !important;">
                        <div class="stat-metric-label">DAILY AVERAGE</div>
                        <div class="stat-metric-value" style="color: #9333ea !important;"><?php echo number_format($dailyAverage, 2); ?></div>
                        <div class="small text-muted font-weight-bold mt-1">Across <?php echo $daysCount; ?> Days</div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-6 mb-3">
                    <div class="stat-metric-card" style="border-left: 4px solid #0f172a !important;">
                        <div class="stat-metric-label">REPORT RANGE</div>
                        <div class="stat-metric-value" style="font-size: 15px; margin-top: 8px; color: #0f172a !important;">
                            <?php echo date('d M', strtotime($fromDateInput)); ?> &rarr; <?php echo date('d M Y', strtotime($toDateInput)); ?>
                        </div>
                        <div class="small text-muted font-weight-bold mt-1">Total Days: <?php echo $daysCount; ?></div>
                    </div>
                </div>
            </div>

            <!-- DAILY BREAKDOWN TABLE -->
            <div class="card ananta-table-card mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 16px;">
                        <i class="zmdi zmdi-calendar-alt text-primary mr-1"></i> Daily Registration Breakdown
                    </h5>
                    <span class="badge badge-light border text-dark font-weight-bold px-3 py-1" style="border-radius: 100px; font-size: 11px;">
                        Includes Days With 0 Registrations
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-items-center mb-0">
                        <thead style="background: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-uppercase;">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3">Day</th>
                                <th class="py-3 text-center">New Registrations</th>
                                <th class="py-3 text-center">Share of Period</th>
                                <th class="py-3 text-right px-4">Status & Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px; color: #0f172a;">
                            <?php foreach ($dailyBreakdown as $row): ?>
                                <?php 
                                $pct = ($totalPeriod > 0) ? round(($row['count'] / $totalPeriod) * 100, 1) : 0;
                                ?>
                                <tr class="<?php echo $row['is_today'] ? 'today-highlight' : ''; ?>">
                                    <td class="px-4 font-weight-bold" style="color: #0f172a;">
                                        <?php echo $row['formatted']; ?>
                                        <?php if ($row['is_today']): ?>
                                            <span class="badge badge-primary font-weight-bold ml-1 px-2 py-1" style="font-size: 10px; background: #0284c7;">TODAY</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-semibold text-muted"><?php echo $row['day_name']; ?></td>
                                    <td class="text-center font-weight-bold" style="font-size: 15px;">
                                        <?php if ($row['count'] > 0): ?>
                                            <span class="active-day-badge"><?php echo $row['count']; ?> New</span>
                                        <?php else: ?>
                                            <span class="zero-day-badge">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2" style="min-width: 120px;">
                                            <div class="progress w-100" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $pct; ?>%; border-radius: 10px;"></div>
                                            </div>
                                            <span class="small font-weight-bold text-muted ml-2"><?php echo $pct; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-right px-4">
                                        <?php if ($row['count'] > 0): ?>
                                            <a href="registration-report.php?from_date=<?php echo $row['date']; ?>&to_date=<?php echo $row['date']; ?>" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-1" style="border-radius: 100px; font-size: 12px;">
                                                View Day Users &rarr;
                                            </a>
                                        <?php else: ?>
                                            <span class="small text-muted font-weight-semibold">No Activity</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DETAILED USER LIST TABLE -->
            <div class="card ananta-table-card mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 16px;">
                        <i class="zmdi zmdi-accounts-list-alt text-primary mr-1"></i> Registered Users Directory (<?php echo count($userList); ?> Users)
                    </h5>
                    <span class="small text-muted font-weight-bold">
                        Between <?php echo date('d-M-Y', strtotime($fromDateInput)); ?> and <?php echo date('d-M-Y', strtotime($toDateInput)); ?>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-items-center mb-0">
                        <thead style="background: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-uppercase;">
                            <tr>
                                <th class="py-3 px-4">#</th>
                                <th class="py-3">User ID</th>
                                <th class="py-3">User Name</th>
                                <th class="py-3">Sponsor ID / Name</th>
                                <th class="py-3">Registration Date</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-right px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px; color: #0f172a;">
                            <?php if (empty($userList)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="zmdi zmdi-accounts-off mr-1" style="font-size: 36px; color: #cbd5e1;"></i>
                                        <p class="mt-2 mb-0 font-weight-bold">No user registrations found for the selected date range.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $idx = 1; foreach ($userList as $u): ?>
                                    <tr>
                                        <td class="px-4 font-weight-bold text-muted"><?php echo $idx++; ?></td>
                                        <td>
                                            <a href="user_profile.php?uid=<?php echo urlencode($u['userid']); ?>" class="font-weight-bold text-primary" style="text-decoration: none;">
                                                <?php echo htmlspecialchars($u['userid']); ?>
                                            </a>
                                        </td>
                                        <td class="font-weight-bold" style="color: #0f172a;">
                                            <?php echo htmlspecialchars($u['name']); ?>
                                            <?php if (!empty($u['mobile'])): ?>
                                                <div class="small text-muted font-weight-normal"><?php echo htmlspecialchars($u['mobile']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold" style="color: #334155;"><?php echo htmlspecialchars($u['sponserid'] ?: 'N/A'); ?></span>
                                            <?php if (!empty($u['sponsername'])): ?>
                                                <div class="small text-muted font-weight-semibold"><?php echo htmlspecialchars($u['sponsername']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="font-weight-semibold" style="color: #0f172a;">
                                            <?php echo date('d-M-Y', strtotime($u['joining_date'])); ?>
                                            <?php if (!empty($u['time'])): ?>
                                                <span class="small text-muted font-weight-normal ml-1">(<?php echo htmlspecialchars($u['time']); ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ((string)$u['active'] === '1'): ?>
                                                <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 100px;">ACTIVE</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning px-3 py-1 font-weight-bold" style="border-radius: 100px;">INACTIVE</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right px-4">
                                            <a href="user_profile.php?uid=<?php echo urlencode($u['userid']); ?>" class="btn btn-sm btn-outline-secondary font-weight-bold px-3 py-1" style="border-radius: 100px; font-size: 11px;">
                                                <i class="zmdi zmdi-eye mr-1"></i> Profile
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
</div>

<?php include __DIR__ . '/common/footer.php'; ?>
</body>
</html>
