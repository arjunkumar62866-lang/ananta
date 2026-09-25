<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - FUND REQUEST HISTORY REDESIGN
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

.btn-export-excel {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 20px !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.btn-export-excel:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.35) !important;
    color: #ffffff !important;
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

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    display: inline-block;
    text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
.status-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.status-cancelled { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

.user-link {
    color: #0284c7 !important;
    font-weight: 700;
    text-decoration: none;
}

.user-link:hover {
    text-decoration: underline;
}

.amount-display {
    font-weight: 800;
    color: #16a34a;
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
    <!-- End Loader -->

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Header Banner Card -->
                <div class="card income-header-card p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="income-header-icon">
                            <i class="fa fa-history"></i>
                        </div>
                        <div>
                            <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Fund Request History</h3>
                            <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Historical archive of all user fund deposit requests and status logs.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Fund Request Ledger</h4>
                                    <p>Complete historical deposit request records</p>
                                </div>
                                <button id="customExportBtn" class="btn btn-export-excel">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </button>
                            </div>
                            <div class="card-body p-4">

                                <!-- Table -->
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover table-bordered" id="usersTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Sno.</th>
                                                <th>User ID</th>
                                                <th>Txn ID</th>
                                                <th>Transaction</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables will load this directly -->
                                        </tbody>
                                    </table>
                                </div>

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
                    url: 'get_fund_request.php',
                    type: 'GET',
                    data: { type: 'fund_request_history' },
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    {
                        data: 'userid',
                        render: function (data) {
                            return `<a class="user-link" href="user_profile.php?uid=${data}"><?php echo $hmpre; ?>${data}</a>`;
                        }
                    },
                    { data: 'tr_id' },
                    { data: 'subject' },
                    {
                        data: 'amount',
                        render: function (data) {
                            return `<span class="amount-display">${formatAdminCurrency(parseFloat(data || 0))}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function (data, type, row) {
                            if (data == "0") {
                                return `<span class="status-badge status-pending"><i class="fa fa-clock-o me-1"></i>Pending</span>`;
                            } else if (data == "1") {
                                return `<span class="status-badge status-approved"><i class="fa fa-check-circle me-1"></i>Approved</span>`;
                            } else if (data == "2") {
                                return `<span class="status-badge status-cancelled"><i class="fa fa-times-circle me-1"></i>Cancelled</span>`;
                            } else {
                                return "";
                            }
                        }
                    },
                    { data:'date'}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Fund_Request_History_Report'
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
