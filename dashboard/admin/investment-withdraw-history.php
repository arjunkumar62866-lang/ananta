<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - INVESTMENT WITHDRAW HISTORY CLEAN MOBILE REDESIGN
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
    background: #ffffff !important;
    border-radius: 16px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04) !important;
    margin-bottom: 20px;
}

.income-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);
    color: #ffffff;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.25);
    flex-shrink: 0;
}

.ananta-fintech-card {
    background: transparent !important;
    border-radius: 0 !important;
    border: none !important;
    box-shadow: none !important;
}

.card-header-bar {
    padding: 16px 0;
    border-bottom: none;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.card-header-title h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 2px 0 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.btn-export-excel {
    background: #16a34a !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 8px 16px !important;
    font-size: 13px !important;
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2) !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}

.btn-export-excel:hover {
    background: #15803d !important;
    color: #ffffff !important;
}

/* DataTables Light Fintech Table Styling */
.table-responsive {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #ffffff !important;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    margin-top: 10px;
    width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

table.dataTable.no-footer {
    border-bottom: 1px solid #e2e8f0 !important;
}

.table {
    margin-bottom: 0 !important;
    color: #0f172a !important;
    width: 100% !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    border-bottom: 2px solid #e2e8f0 !important;
    border-top: none !important;
    padding: 12px 14px !important;
    white-space: nowrap;
}

.table tbody td {
    padding: 12px 14px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #0f172a !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
    white-space: nowrap;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc !important;
}

/* DataTables Controls Overrides - Responsive & Clean */
.dataTables_wrapper {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    width: 100% !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    color: #0f172a !important;
    font-weight: 700 !important;
    margin-bottom: 14px;
}

.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap;
    margin: 0;
}

.dataTables_wrapper .dataTables_length select {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    font-weight: 700 !important;
    outline: none !important;
    height: 38px !important;
}

.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-weight: 600 !important;
    outline: none !important;
    height: 38px !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 14px !important;
    color: #64748b !important;
    font-size: 13px !important;
    font-weight: 600 !important;
}

.dataTables_wrapper .dataTables_paginate {
    padding-top: 10px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-weight: 700 !important;
    margin: 2px !important;
    padding: 4px 10px !important;
    font-size: 13px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
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
    color: #0f172a;
}

/* Mobile Responsiveness Improvements */
@media (max-width: 767.98px) {
    .content-wrapper {
        padding-left: 12px !important;
        padding-right: 12px !important;
        padding-top: 75px !important;
    }
    .income-header-card {
        padding: 16px !important;
        border-radius: 14px !important;
    }
    .card-header-bar {
        flex-direction: column !important;
        align-items: flex-start !important;
        padding: 10px 0 !important;
    }
    .btn-export-excel {
        width: 100% !important;
        justify-content: center !important;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        float: none !important;
        text-align: left !important;
        width: 100% !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        margin-left: 0 !important;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        text-align: center !important;
        width: 100% !important;
    }
    .dataTables_wrapper .dataTables_paginate {
        display: flex !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
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
                            <i class="fa fa-briefcase"></i>
                        </div>
                        <div>
                            <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Investment Withdrawal Management</h3>
                            <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Review, approve, or cancel member capital principal withdrawal requests.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4>Investment Withdrawal Requests Log</h4>
                                    <p>Live capital payout records & audit log</p>
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
                                                <th>Txn ID</th>
                                                <th>User</th>
                                                <th>Name</th>
                                                <th>Transaction</th>
                                                <th>R Amount</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>A/C No.</th>
                                                <th>IFSC</th>
                                                <th>Wallet Address</th>
                                                <th>Status</th>
                                                <th>Admin Remarks</th>
                                                <th>Date</th>
                                                <th>Action</th> 
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
        function triggerApproveCapital(id, uid, amt) {
            let rem = prompt("Optional Admin Approval Remarks:", "Capital withdrawal approved successfully.");
            if (rem === null) return;
            let url = "approved-withdrawal.php?tid=" + encodeURIComponent(id) + "&user_id=" + encodeURIComponent(uid) + "&amt=" + encodeURIComponent(amt) + "&admin_remarks=" + encodeURIComponent(rem);
            window.location.href = url;
        }

        function triggerRejectCapital(id, uid, amt, invId) {
            let reason = prompt("Mandatory Rejection Reason / Remarks:");
            if (reason === null) return;
            if (reason.trim() === "") {
                alert("Rejection reason is MANDATORY when rejecting a capital withdrawal request.");
                return;
            }
            let url = "cancel-withdrawal.php?tid=" + encodeURIComponent(id) + "&uid=" + encodeURIComponent(uid) + "&amt=" + encodeURIComponent(amt) + "&admin_remarks=" + encodeURIComponent(reason);
            window.location.href = url;
        }

        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type') || '3';
            let table = $('#usersTable').DataTable({
                ajax: {
                    url: 'get_withdrawal.php',
                    type: 'GET',
                    data: { type: typeParam },
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'id' },
                    {
                        data: 'user_id',
                        render: function (data) {
                            return `<a class="user-link" href="user_profile.php?uid=${data}"><?php echo $hmpre; ?>${data}</a>`;
                        }
                    },
                    { data: 'name' },
                    { data: 'subject' },
                    {
                        data: 'act_amount',
                        render: function (data) {
                            return `<span class="amount-display">${formatAdminCurrency(parseFloat(data || 0))}</span>`;
                        }
                    },
                    {
                        data: 'amount',
                        render: function (data) {
                            return `<span class="amount-display" style="color: #9333ea;">${formatAdminCurrency(parseFloat(data || 0))}</span>`;
                        }
                    },
                    { data: 'type'},
                    { data: 'holder_name' },
                    { data: 'ac_number'},
                    { data: 'ifsc' },
                    { data: 'bit_coin' },
                    {
                      data: 'a_status',
                      render: function (data, type, row) {
                        if (data == "0") {
                            return `<span class="status-badge status-pending"><i class="fa fa-clock-o me-1"></i>Pending</span>`;
                        } else if(data=='1') {
                            return `<span class="status-badge status-approved"><i class="fa fa-check-circle me-1"></i>Approved</span>`;
                        } else {
                            return `<span class="status-badge status-cancelled"><i class="fa fa-times-circle me-1"></i>Cancelled</span>`;
                        }
                      }
                    },
                    {
                        data: 'admin_remarks',
                        render: function(data) {
                            return data ? `<span class="small font-weight-semibold text-dark">${data}</span>` : '—';
                        }
                    },
                    { data: 'created_date'},
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (row.a_status == "0") {
                                return `
                                    <div class="d-flex gap-2">
                                        <button type="button" onclick="triggerApproveCapital('${row.id}', '${row.user_id}', '${row.amount}')" class="btn btn-success btn-sm font-weight-bold">
                                            <i class="fa fa-check me-1"></i> Approve
                                        </button>
                                        <button type="button" onclick="triggerRejectCapital('${row.id}', '${row.user_id}', '${row.act_amount}', '${row.api_txn_no}')" class="btn btn-danger btn-sm font-weight-bold">
                                            <i class="fa fa-times me-1"></i> Reject
                                        </button>
                                    </div>
                                `;
                            } else {
                                return '<span class="text-muted font-weight-bold">-</span>';
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
                        title: 'Investment_Withdrawal_Report'
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
