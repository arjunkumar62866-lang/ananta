<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php'; // contains $pdo
$date = date('Y-m-d');
$pdate = date('Y-m-d', strtotime("-30 days"));
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - LEADERSHIP INCOME REDESIGN
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

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden;
    margin-bottom: 24px;
}

.card-header-bar {
    padding: 20px 24px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.card-header-title h4 {
    margin: 0;
    font-weight: 800;
    color: #0f172a;
    font-size: 18px;
}

.card-header-title p {
    margin: 2px 0 0 0;
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
}

.btn-export-excel {
    background: #16a34a !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 13.5px !important;
    padding: 8px 18px !important;
    border-radius: 10px !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
    transition: all 0.2s ease !important;
}

.stat-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    height: 100%;
}

.stat-box .stat-label {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.stat-box .stat-value {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

/* DataTables Controls Overrides - Explicit Black Text */
.dataTables_wrapper {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 14px !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #0f172a !important;
    font-weight: 700 !important;
    margin-bottom: 16px;
}

.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-weight: 700 !important;
    outline: none !important;
}

.amount-text {
    color: #16a34a !important;
    font-weight: 800 !important;
}
</style>

<div id="pageloader-overlay" class="visible incoming">
   <div class="loader-wrapper-outer"><div class="loader-wrapper-inner">
       <div class="loader"></div>
   </div></div>
</div>

<div id="wrapper">
<div class="clearfix"></div>

<?php
// 1) TOTAL USERS FOR ROYALTY ONE
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_royalty_user WHERE full_status = 0");
$stmt->execute();
$total_club_user = $stmt->fetchColumn();

// 2) Get Current Month CTO
$currentYear = date('Y');
$currentMonth = date('m');

$stmt = $pdo->prepare("
    SELECT 
        SUM(package) AS total_cto,
        COUNT(id) AS total_entries
    FROM tbl_roi_one
    WHERE YEAR(`date`) = :year AND MONTH(`date`) = :month
");

$stmt->execute([
    ':year' => $currentYear,
    ':month' => $currentMonth
]);

$currentMonthCTO = $stmt->fetch(PDO::FETCH_ASSOC);
$rawCTO = (float)($currentMonthCTO['total_cto'] ?? 0);
$currentMonthCTO1 = ($rawCTO * 0.2) / 100;

// 3) ONE PERSON AMOUNT
$payamount = ($total_club_user > 0) 
             ? round(($currentMonthCTO1 / $total_club_user), 2) 
             : 0;
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Banner Card -->
        <div class="card income-header-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="income-header-icon">
                    <i class="fa fa-trophy"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Leadership Income Management</h3>
                    <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Distribute monthly CTO leadership pool & track credited account statements.</p>
                </div>
            </div>
        </div>

        <!-- SUMMARY STATS CARD -->
        <div class="card ananta-fintech-card mb-4">
            <div class="card-header-bar">
                <div class="card-header-title">
                    <h4>Leadership Pool Distribution</h4>
                    <p>Current Month CTO Statistics</p>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Total CTO (Current Month)</div>
                            <div class="stat-value"><?= formatCurrency($rawCTO, getUserCurrency()) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Eligible Members</div>
                            <div class="stat-value"><?= number_format($total_club_user) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Total Pool Amount</div>
                            <div class="stat-value" style="color: #0284c7;"><?= formatCurrency($currentMonthCTO1, getUserCurrency()) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Per Member Amount</div>
                            <div class="stat-value" style="color: #16a34a;"><?= formatCurrency($payamount, getUserCurrency()) ?></div>
                        </div>
                    </div>
                </div>

                <form method="get" action="royalty-one-pay.php" class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold" style="color: #0f172a; font-size: 13.5px;">Distribution Amount Per Eligible Member:</label>
                        <input class="form-control" name="amount" value="<?= $payamount ?>" style="border-radius: 12px; font-weight: 700; font-size: 16px; border: 1.5px solid #cbd5e1; color: #0f172a; background: #ffffff;">
                    </div>
                    <button type="submit" name="submit" class="btn btn-success text-white font-weight-bold px-4 py-2" style="border-radius: 12px; font-size: 14px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); border: none; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);" onclick="return confirm('Do you want to process Leadership Income Payout now?');">
                        <i class="fa fa-paper-plane mr-2"></i> Execute Leadership Income Pay
                    </button>
                </form>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="card ananta-fintech-card">
            <div class="card-header-bar">
                <div class="card-header-title">
                    <h4>Account History</h4>
                    <p>Leadership Payout Statements & Logs</p>
                </div>
                <button id="customExportBtn" class="btn btn-export-excel">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </button>
            </div>

            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-bordered table-hover" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th>User ID</th>
                                <th>Transaction Time</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<a href="javaScript:void();" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
</a>

<?php include 'common/footer.php'; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
    let table = $('#usersTable').DataTable({
        ajax: {
            url: 'get_royalty_one.php',
            type: 'GET',
            dataSrc: ''
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            {
                data: 'userid',
                render: function (data) {
                    return `<strong><?php echo $hmpre; ?>${data}</strong>`;
                }
            },
            { data: 'time' },
            {
                data: 'amount',
                render: function (data) {
                    return `<span class="amount-text"><?php echo $hmcurrency; ?>${data}</span>`;
                }
            },
            { data: 'created_date' }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100, 1000],
        dom: 'lfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Leadership_Income_Report'
            }
        ]
    });

    $('#customExportBtn').on('click', function () {
        table.button('.buttons-excel').trigger();
    });
});
</script>

</div>
</body>
</html>

