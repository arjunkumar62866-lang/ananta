<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - WITHDRAWAL HISTORY REDESIGN
========================================================= */
html, body {
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
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
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

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
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
    font-size: 14px !important;
    font-weight: 600 !important;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc !important;
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

.dataTables_wrapper .dataTables_length select {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-weight: 700 !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 8px 14px !important;
    font-weight: 600 !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-weight: 700 !important;
    margin: 0 3px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}

/* Status Badges */
.badge-status {
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.badge-status-approved {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.badge-status-pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-status-canceled {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
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
            <div class="container-fluid pt-3 px-4">
                <!-- Breadcrumb Bar -->
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                            <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #475569; font-weight: 600;">Withdrawal</span>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #0f172a; font-weight: 700;">Withdraw History</span>
                        </div>
                    </nav>
                </div>

                <!-- Header Welcome Banner -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card income-header-card border-0 p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="income-header-icon">
                                        <i class="fa fa-history"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">WITHDRAWAL HISTORY</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">RECORD LOGS</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            Withdrawal <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">History</span> 📋
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            Track and monitor all your past wallet and investment withdrawal requests.
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="withdraw.php" class="btn btn-primary font-weight-bold px-3 py-2 me-2" style="border-radius: 12px; font-size: 13px;">
                                        <i class="fa fa-bank me-1"></i> Wallet Withdraw
                                    </a>
                                    <a href="nonworking1-withdraw1.php" class="btn btn-outline-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                                        <i class="fa fa-line-chart me-1"></i> Investment Withdraw
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Table Card -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ananta-fintech-card">
                            
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4><i class="fa fa-list-alt text-primary me-2"></i> All Withdrawal Records</h4>
                                    <p>Comprehensive statement of submitted requests and processing status</p>
                                </div>
                                <div>
                                    <button id="customExportBtn" class="btn btn-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                        <i class="fa fa-file-excel-o me-1"></i> Export to Excel
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover align-middle" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Total Amount</th>
                                                <th>TDS 5% + Admin 3%</th>
                                                <th>Net Amount</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loaded via AJAX DataTables -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><!-- End Row -->

                <div class="overlay toggle-menu"></div>

            </div>
        </div>

        <a href="javaScript:void();" class="back-to-top">
            <i class="fa fa-angle-double-up"></i>
        </a>

        <?php include 'common/footer.php'; ?>

    </div>

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
                    url: 'get_withdraw_history.php',
                    type: 'GET',
                    data: { type: '' },
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { 
                        data: 'act_amount',
                        render: function(data) {
                            return '₹' + parseFloat(data || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            let value = (row.act_amount || 0) - (row.amount || 0);
                            return '₹' + parseFloat(value || 0).toFixed(2);
                        }
                    },
                    { 
                        data: 'amount',
                        render: function(data) {
                            return '<strong class="text-success">₹' + parseFloat(data || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>';
                        }
                    },
                    { 
                        data: 'type',
                        render: function(data) {
                            return '<span class="badge bg-light text-dark font-weight-bold border px-2 py-1">' + (data || 'Debit') + '</span>';
                        }
                    },
                    { data: 'created_date' },
                    { data: 'time' },
                    { 
                        data: 'a_status',
                        render: function(data, type, row) {
                            if (data == '0') {
                                return '<span class="badge-status badge-status-pending"><i class="fa fa-clock-o"></i> Pending</span>';
                            } else if (data == '1') {
                                return '<span class="badge-status badge-status-approved"><i class="fa fa-check-circle"></i> Approved</span>';
                            } else {
                                return '<span class="badge-status badge-status-canceled"><i class="fa fa-times-circle"></i> Canceled</span>';
                            }
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Withdrawal_History'
                    }
                ]
            });

            // Custom export button
            $('#customExportBtn').on('click', function () {
                table.button('.buttons-excel').trigger();
            });
        });
    </script>

</body>
</html>

