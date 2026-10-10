<?php 
ob_start(); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/db_method.php';

// Calculate Today's KYC count and Total KYC count
$todayCountStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM kyc k 
    LEFT JOIN user u ON (k.userid = u.userid OR k.userid = u.id)
    WHERE (k.status = '1' OR (TRIM(COALESCE(k.holder_name, '')) != '' OR TRIM(COALESCE(k.ac_number, '')) != '' OR TRIM(COALESCE(k.pan, '')) != '' OR TRIM(COALESCE(k.mimo, '')) != ''))
      AND DATE(COALESCE(k.updated_at, u.joining_date)) = CURDATE()
");
$todayKycCount = (int)$todayCountStmt->fetchColumn();

$totalCountStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM kyc k 
    WHERE (k.status = '1' OR (TRIM(COALESCE(k.holder_name, '')) != '' OR TRIM(COALESCE(k.ac_number, '')) != '' OR TRIM(COALESCE(k.pan, '')) != '' OR TRIM(COALESCE(k.mimo, '')) != ''))
");
$totalKycCount = (int)$totalCountStmt->fetchColumn();

$initialFilter = trim($_GET['filter'] ?? '');
$initialFrom   = trim($_GET['from_date'] ?? '');
$initialTo     = trim($_GET['to_date'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - TOTAL KYC UPDATED
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
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.08) 0%, rgba(22, 163, 74, 0.08) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.15) !important;
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

.stat-metric-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 20px 24px !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04) !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}

.stat-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08) !important;
}

.stat-metric-value {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 4px;
}

.stat-metric-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 24px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.filter-bar-container {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 18px 22px;
    margin-bottom: 24px;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
}

.form-control, input[type="date"].form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    height: 42px !important;
}

.form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

.quick-filter-btn {
    border-radius: 100px !important;
    padding: 6px 16px !important;
    font-size: 12.5px !important;
    font-weight: 700 !important;
    border: 1.5px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #334155 !important;
    transition: all 0.2s ease !important;
    cursor: pointer;
}

.quick-filter-btn:hover,
.quick-filter-btn.active {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}

.btn-export-excel {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 20px !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

/* DataTables Light Fintech Table Styling */
.table-responsive {
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

.table {
    margin-bottom: 0 !important;
    color: #0f172a !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #1e293b !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    border-bottom: 2px solid #e2e8f0 !important;
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

.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label {
    color: #0f172a !important;
    font-weight: 700 !important;
}

.status-badge-verified {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    padding: 5px 12px;
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.user-link {
    color: #0284c7 !important;
    font-weight: 800;
    text-decoration: none;
}

.user-link:hover {
    text-decoration: underline;
}

.action-btn-view {
    background: #0284c7;
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}

.action-btn-view:hover {
    background: #0369a1;
    color: #ffffff !important;
}

.action-btn-edit {
    background: #f1f5f9;
    color: #334155 !important;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid #cbd5e1;
    transition: all 0.2s ease;
}

.action-btn-edit:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- HEADER BANNER -->
        <div class="card income-header-card p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="income-header-icon">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 font-weight-bold" style="color: #0f172a;">Total KYC Updated</h3>
                        <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">
                            Auto-verified member KYC database with instant verification and custom date range filters.
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge px-3 py-2 font-weight-bold" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; border-radius:100px; font-size:12px;">
                        <i class="fa fa-check-circle mr-1"></i> Auto-Verification Mode Active
                    </span>
                </div>
            </div>
        </div>

        <!-- 3 SUMMARY STAT CARDS -->
        <div class="row mb-4">
            <!-- Card 1: Today's Updated KYC -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-metric-card" role="button" onclick="applyQuickFilter('today')">
                    <div>
                        <div class="stat-metric-value text-success"><?php echo number_format($todayKycCount); ?></div>
                        <div class="stat-metric-label">Today's Updated KYC</div>
                        <div class="small text-muted mt-1"><i class="fa fa-clock-o mr-1"></i> Updated today (<?php echo date('d M Y'); ?>)</div>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 16px; background: #ecfdf5; color: #10b981; display:flex; align-items:center; justify-content:center; font-size: 22px;">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total KYC Updated -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-metric-card" role="button" onclick="applyQuickFilter('all')">
                    <div>
                        <div class="stat-metric-value text-primary"><?php echo number_format($totalKycCount); ?></div>
                        <div class="stat-metric-label">Total KYC Updated</div>
                        <div class="small text-muted mt-1"><i class="fa fa-users mr-1"></i> All-time verified member records</div>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 16px; background: #e0f2fe; color: #0284c7; display:flex; align-items:center; justify-content:center; font-size: 22px;">
                        <i class="fa fa-id-card-o"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Auto-Verified Status -->
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="stat-metric-card">
                    <div>
                        <div class="stat-metric-value" style="color: #8b5cf6;">100%</div>
                        <div class="stat-metric-label">Zero Approval Backlog</div>
                        <div class="small text-muted mt-1"><i class="fa fa-bolt text-warning mr-1"></i> No manual admin approval needed</div>
                    </div>
                    <div style="width: 50px; height: 50px; border-radius: 16px; background: #f3e8ff; color: #8b5cf6; display:flex; align-items:center; justify-content:center; font-size: 22px;">
                        <i class="fa fa-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATE FILTER BAR -->
        <div class="filter-bar-container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <strong class="text-dark small mr-2" style="color:#0f172a !important;"><i class="fa fa-filter text-primary mr-1"></i> Quick Filters:</strong>
                        <button type="button" class="quick-filter-btn" id="qfToday" onclick="applyQuickFilter('today')">Today</button>
                        <button type="button" class="quick-filter-btn" id="qf7days" onclick="applyQuickFilter('7days')">Last 7 Days</button>
                        <button type="button" class="quick-filter-btn" id="qfMonth" onclick="applyQuickFilter('month')">This Month</button>
                        <button type="button" class="quick-filter-btn" id="qfAll" onclick="applyQuickFilter('all')">All Time</button>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12">
                    <form id="filterForm" class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap m-0" onsubmit="event.preventDefault(); reloadKycTable();">
                        <div class="d-flex align-items-center gap-1">
                            <span class="small font-weight-bold text-muted">From:</span>
                            <input type="date" id="fromDate" class="form-control" value="<?php echo htmlspecialchars($initialFrom); ?>" style="width: 145px;">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small font-weight-bold text-muted">To:</span>
                            <input type="date" id="toDate" class="form-control" value="<?php echo htmlspecialchars($initialTo); ?>" style="width: 145px;">
                        </div>
                        <button type="submit" class="btn btn-primary px-3 font-weight-bold" style="border-radius: 10px; height: 42px; background:#0284c7; border:none;">
                            <i class="fa fa-search mr-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-light border px-3 font-weight-bold text-dark" style="border-radius: 10px; height: 42px;" onclick="resetFilters()">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar">
                        <div>
                            <h4 class="mb-1 font-weight-bold text-dark" style="font-size:18px; color:#0f172a !important;">
                                <i class="fa fa-list-alt text-primary mr-2"></i> Verified KYC Records
                            </h4>
                            <p class="mb-0 text-muted small" id="activeFilterLabel">Showing all verified KYC submissions</p>
                        </div>
                        <button id="customExportBtn" class="btn btn-export-excel">
                            <i class="fa fa-file-excel-o"></i> Export to Excel
                        </button>
                    </div>
                    <div class="card-body p-4">

                        <div class="table-responsive" id="tblData">
                            <table class="table table-hover table-bordered" id="kycTable" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>Member ID</th>
                                        <th>Member Name</th>
                                        <th>Bank Name & A/C No</th>
                                        <th>IFSC Code</th>
                                        <th>PAN Number</th>
                                        <th>Aadhaar Number</th>
                                        <th>Updated Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
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

    </div>
</div>
</div>

<!-- Back To Top -->
<a href="javaScript:void(0);" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
</a>

<?php include __DIR__ . '/common/footer.php'; ?>

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
let kycTable = null;
let currentQuickFilter = '<?php echo ($initialFilter === "today") ? "today" : "all"; ?>';

function updateActiveButton() {
    $('.quick-filter-btn').removeClass('active');
    if (currentQuickFilter === 'today') $('#qfToday').addClass('active');
    else if (currentQuickFilter === '7days') $('#qf7days').addClass('active');
    else if (currentQuickFilter === 'month') $('#qfMonth').addClass('active');
    else if (currentQuickFilter === 'all') $('#qfAll').addClass('active');
}

function applyQuickFilter(type) {
    currentQuickFilter = type;
    updateActiveButton();

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;

    if (type === 'today') {
        $('#fromDate').val(todayStr);
        $('#toDate').val(todayStr);
        $('#activeFilterLabel').text("Showing KYC records updated today (" + todayStr + ")");
    } else if (type === '7days') {
        const past = new Date();
        past.setDate(past.getDate() - 7);
        const pMm = String(past.getMonth() + 1).padStart(2, '0');
        const pDd = String(past.getDate()).padStart(2, '0');
        $('#fromDate').val(`${past.getFullYear()}-${pMm}-${pDd}`);
        $('#toDate').val(todayStr);
        $('#activeFilterLabel').text("Showing KYC records updated in the last 7 days");
    } else if (type === 'month') {
        $('#fromDate').val(`${yyyy}-${mm}-01`);
        $('#toDate').val(todayStr);
        $('#activeFilterLabel').text("Showing KYC records updated this month");
    } else {
        $('#fromDate').val('');
        $('#toDate').val('');
        $('#activeFilterLabel').text("Showing all verified KYC submissions");
    }

    reloadKycTable();
}

function resetFilters() {
    applyQuickFilter('all');
}

function reloadKycTable() {
    if (kycTable) {
        kycTable.ajax.reload();
    }
}

$(document).ready(function () {
    updateActiveButton();

    kycTable = $('#kycTable').DataTable({
        ajax: {
            url: 'get_user.php',
            type: 'GET',
            data: function (d) {
                d.type = 'completed_kyc';
                const fromVal = $('#fromDate').val();
                const toVal = $('#toDate').val();
                if (fromVal) d.from_date = fromVal;
                if (toVal) d.to_date = toVal;
                if (currentQuickFilter === 'today' && !fromVal) {
                    d.filter = 'today';
                }
            },
            dataSrc: ''
        },
        columns: [
            { 
                data: null, 
                render: (data, type, row, meta) => meta.row + 1 
            },
            {
                data: 'userid',
                render: function (data) {
                    return `<a class="user-link" href="verify_kyc.php?uid=${data}"><?php echo $hmpre; ?>${data}</a>`;
                }
            },
            {
                data: 'holder_name',
                render: function (data, type, row) {
                    const uName = row.user_name && row.user_name !== 'N/A' ? row.user_name : data;
                    return `<div><strong style="color:#0f172a;">${uName}</strong><br><small class="text-muted">A/C: ${data || 'N/A'}</small></div>`;
                }
            },
            {
                data: 'bank',
                render: function (data, type, row) {
                    const bankName = data && data !== 'N/A' ? data : 'N/A';
                    const acNo = row.ac_number && row.ac_number !== 'N/A' ? row.ac_number : '-';
                    return `<div><strong>${bankName}</strong><br><small class="text-muted font-weight-bold">${acNo}</small></div>`;
                }
            },
            {
                data: 'ifsc',
                render: function (data) {
                    return `<span class="badge badge-light border font-weight-bold" style="color:#0f172a; font-size:12px;">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'pan',
                render: function (data) {
                    return `<span class="font-weight-bold text-dark">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'aadhar_number',
                render: function (data) {
                    return `<span class="font-weight-bold text-dark">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'updated_date',
                render: function (data) {
                    return `<span class="small font-weight-bold text-secondary">${data || 'N/A'}</span>`;
                }
            },
            {
                data: null,
                render: function () {
                    return `<span class="status-badge-verified"><i class="fa fa-check-circle"></i> Verified</span>`;
                }
            },
            {
                data: 'userid',
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center gap-1">
                            <a class="action-btn-view" href="verify_kyc.php?uid=${data}">
                                <i class="fa fa-eye"></i> View
                            </a>
                            <a class="action-btn-edit" href="update_kyc.php?uid=${data}">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100, 500],
        dom: 'lfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Total_KYC_Updated_Records',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        language: {
            emptyTable: "No KYC records found for the selected date range."
        }
    });

    $('#customExportBtn').on('click', function () {
        kycTable.button('.buttons-excel').trigger();
    });

    // Check if initial query parameter was today
    <?php if ($initialFilter === 'today'): ?>
        applyQuickFilter('today');
    <?php endif; ?>
});
</script>

</body>
</html>
