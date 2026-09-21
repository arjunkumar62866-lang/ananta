<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php'; 

// Calculate Total Scheduled & Credited Direct Bonus for header stat
$stmt_db = $pdo->prepare("SELECT 
    COALESCE(SUM(total_bonus), 0) as total_scheduled,
    COALESCE(SUM(CASE WHEN status = 'CREDITED' THEN installment_amount ELSE 0 END), 0) as total_credited,
    COALESCE(SUM(CASE WHEN status = 'PENDING' THEN installment_amount ELSE 0 END), 0) as total_pending,
    COUNT(CASE WHEN status = 'CREDITED' THEN 1 END) as completed_inst,
    COUNT(CASE WHEN status = 'PENDING' THEN 1 END) as pending_inst
    FROM tbl_direct_bonus_schedule WHERE beneficiary_id = :userid");
$stmt_db->execute([':userid' => $userid]);
$res_db = $stmt_db->fetch(PDO::FETCH_ASSOC);

$direct_bonus_total = round((float)($res_db['total_scheduled'] ?? 0), 2);
$direct_bonus_credited = round((float)($res_db['total_credited'] ?? 0), 2);
$direct_bonus_pending = round((float)($res_db['total_pending'] ?? 0), 2);
$completed_inst = (int)($res_db['completed_inst'] ?? 0);
$pending_inst = (int)($res_db['pending_inst'] ?? 0);

$q_count = getQualifiedDirectCount($userid, $pdo);
?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - DIRECT BONUS REDESIGN
   Matches Dashboard (index.php), Profile & Team Pages
========================================================= */

html,
body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-user-dashboard.bg-theme,
body.ananta-user-dashboard.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* Remove old legacy dark overlays */
html::before,
html::after,
body::before,
body::after,
#wrapper::before,
#wrapper::after,
.content-wrapper::before,
.content-wrapper::after {
    content: none !important;
    display: none !important;
    background: none !important;
    background-color: transparent !important;
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

/* Header Banner */
.income-header-card {
    background: linear-gradient(135deg, rgba(234, 88, 12, 0.10) 0%, rgba(2, 132, 199, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(234, 88, 12, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(234, 88, 12, 0.3);
    flex-shrink: 0;
}

/* Card Container */
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    padding: 24px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
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

/* Export Button */
.ananta-btn-export {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 22px !important;
    font-weight: 700 !important;
    font-size: 13.5px !important;
    box-shadow: 0 6px 20px rgba(22, 163, 74, 0.25) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.ananta-btn-export:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 25px rgba(22, 163, 74, 0.35) !important;
    color: #ffffff !important;
}

/* Table Styling */
.table-responsive {
    padding: 20px 28px 28px !important;
}

table.ananta-custom-table {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    margin-top: 15px !important;
    border: none !important;
}

table.ananta-custom-table thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
    border-bottom: 2px solid #e2e8f0 !important;
    border-top: none !important;
    padding: 14px 16px !important;
}

table.ananta-custom-table tbody tr {
    transition: background-color 0.2s ease;
}

table.ananta-custom-table tbody tr:hover {
    background-color: rgba(234, 88, 12, 0.03) !important;
}

table.ananta-custom-table tbody td {
    padding: 14px 16px !important;
    color: #1e293b !important;
    font-size: 13.5px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    border-top: none !important;
}

/* Amount & Badges */
.amount-badge {
    font-weight: 800;
    color: #ea580c;
    font-size: 14.5px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.badge-status.active,
.badge-status.credit {
    background: rgba(22, 163, 74, 0.12) !important;
    color: #16a34a !important;
    border: 1px solid rgba(22, 163, 74, 0.25) !important;
    padding: 5px 14px !important;
    border-radius: 30px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
}

/* DataTables Controls */
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px !important;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 7px 14px !important;
    font-size: 13px !important;
    outline: none !important;
    background: #f8fafc !important;
    transition: all 0.2s ease !important;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ea580c !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15) !important;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    outline: none !important;
    background: #f8fafc !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #334155 !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    padding: 5px 12px !important;
    margin: 0 2px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
    color: #ffffff !important;
    border-color: #ea580c !important;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important;
    color: #ea580c !important;
    border-color: #cbd5e1 !important;
}

.dataTables_wrapper .dataTables_info {
    color: #64748b !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    padding-top: 14px !important;
}
</style>

<body class="ananta-user-dashboard">

    <!-- Loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <!-- End Loader -->

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Header Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card income-header-card border-0 p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="income-header-icon">
                                        <i class="fa fa-gift"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(234, 88, 12, 0.15); color: #ea580c; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">BONUS STATEMENT</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">DIRECT BONUS 10M</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            Direct Bonus <span style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Ledger</span> 🎁
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            Statement of direct bonus packages and 10M program reward allocations.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(234, 88, 12, 0.2); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                                        <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Direct Bonus</span>
                                        <span class="font-weight-bold" style="font-size: 18px; color: #ea580c; font-weight: 800;"><?php echo $hmcurrency . " " . number_format($direct_bonus_total, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Table Section -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ananta-fintech-card">
                            
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4><i class="fa fa-list-alt text-warning me-2"></i> Direct Bonus Transactions</h4>
                                    <p>Real-time log of direct bonus reward payouts</p>
                                </div>
                                <button id="customExportBtn" class="ananta-btn-export">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </button>
                            </div>

                            <div class="table-responsive" id="tblData">
                                <table class="table table-hover ananta-custom-table" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>Installment</th>
                                            <th>Month</th>
                                            <th>Direct Referral</th>
                                            <th>Investment</th>
                                            <th>Monthly Amount</th>
                                            <th>Status</th>
                                            <th>Credit Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables dynamic content -->
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div><!-- End Row -->

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>
                <!-- End Overlay -->

            </div>
            <!-- End container-fluid -->
        </div>
        <!-- End content-wrapper -->

        <!-- Back To Top -->
        <a href="javaScript:void();" class="back-to-top">
            <i class="fa fa-angle-double-up"></i>
        </a>

        <!-- Footer -->
        <?php include 'common/footer.php'; ?>
        <!-- End Footer -->

    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- JSZip for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {
            let table = $('#usersTable').DataTable({
                ajax: {
                    url: 'get_direct_bonus.php',
                    type: 'GET',
                    data: { type: '' },
                    dataSrc: ''
                },
                columns: [
                    { 
                        data: null, 
                        render: (data, type, row, meta) => '<span style="font-weight: 700; color: #64748b;">#' + (meta.row + 1) + '</span>'
                    },
                    { 
                        data: 'installment_number',
                        render: (data) => '<span style="font-weight: 700; color: #ea580c;">Month ' + (data || 1) + ' / 10</span>'
                    },
                    { 
                        data: 'installment_month',
                        render: (data) => '<span style="font-weight: 600; color: #1e293b;"><i class="fa fa-calendar me-1 text-muted"></i>' + (data || '-') + '</span>'
                    },
                    { 
                        data: 'source_user_id',
                        render: (data, type, row) => '<span style="font-weight: 600; color: #0284c7;">' + (row.source_user_name ? row.source_user_name + ' (' + data + ')' : data) + '</span>'
                    },
                    { 
                        data: 'investment_amount',
                        render: (data) => '<span style="font-weight: 600; color: #475569;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>'
                    },
                    { 
                        data: 'installment_amount',
                        render: (data) => '<span class="amount-badge" style="background: rgba(234, 88, 12, 0.1); color: #ea580c;">+ <?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    { 
                        data: 'status',
                        render: (data) => {
                            if (data === 'CREDITED') {
                                return '<span class="badge-status credit" style="background: rgba(22, 163, 74, 0.15); color: #16a34a;"><i class="fa fa-check-circle me-1"></i>CREDITED</span>';
                            } else if (data === 'PENDING') {
                                return '<span class="badge-status pending" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;"><i class="fa fa-clock-o me-1"></i>PENDING</span>';
                            } else {
                                return '<span class="badge-status" style="background: rgba(239, 68, 68, 0.15); color: #dc2626;"><i class="fa fa-ban me-1"></i>' + data + '</span>';
                            }
                        }
                    },
                    { 
                        data: 'credited_at',
                        render: (data) => '<span style="color: #64748b; font-size: 13px;"><i class="fa fa-clock-o me-1 text-muted"></i>' + (data || 'Not Credited') + '</span>'
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Direct_Bonus_Statement'
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search direct bonus...",
                    lengthMenu: "Show _MENU_ entries"
                }
            });

            // Custom export button
            $('#customExportBtn').on('click', function () {
                table.button('.buttons-excel').trigger();
            });
        });
    </script>

</body>
</html>
