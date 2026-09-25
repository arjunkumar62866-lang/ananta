<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - ALL USERS MANAGEMENT REDESIGN
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-admin-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-admin-dashboard.bg-theme,
body.ananta-admin-dashboard.bg-theme1 {
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
.badge-status-active {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    padding: 5px 12px;
    border-radius: 100px;
    font-weight: 700;
}
.badge-status-inactive {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    padding: 5px 12px;
    border-radius: 100px;
    font-weight: 700;
}
.badge-status-block {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    padding: 5px 12px;
    border-radius: 100px;
    font-weight: 700;
}
</style>

<body class="ananta-admin-dashboard">

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
    <div id="wrapper" class="ananta-admin-dashboard">
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
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">USER MANAGEMENT</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">ALL MEMBERS</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            All Registered <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Users</span> 👥
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            View, search and manage all system user accounts and permissions.
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="active_all_user.php" class="btn btn-outline-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">Active Users</a>
                                    <a href="inactive_all_user.php" class="btn btn-outline-danger font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">Inactive Users</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ananta-fintech-card">
                            
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4><i class="fa fa-list-alt text-primary me-2"></i> All Member Directory</h4>
                                    <p>Comprehensive register of all registered members</p>
                                </div>
                                <div>
                                    <button id="customExportBtn" class="btn btn-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                        <i class="fa fa-file-excel-o me-1"></i> Export to Excel
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label font-weight-bold text-muted small uppercase">Account Status Filter</label>
                                        <select id="statusFilter" class="form-control" style="border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 600;">
                                            <option value="">All Statuses</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Blocked">Blocked</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label font-weight-bold text-muted small uppercase">KYC Status Filter</label>
                                        <select id="kycFilter" class="form-control" style="border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 600;">
                                            <option value="">All KYC Statuses</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Not Submitted">Not Submitted</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover align-middle" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>User ID</th>
                                                <th>Sponsor ID</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th>KYC</th>
                                                <th>Reg. Date</th>
                                                <th>Main Wallet</th>
                                                <th>Net Balance</th>
                                                <th>Active Inv.</th>
                                                <th>Total Wd.</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables loads dynamically -->
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
                    url: 'get_user.php',
                    type: 'GET',
                    data: { type: '' },
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    {
                        data: 'name',
                        render: function (data, type, row) {
                            return `<a href="user_profile.php?uid=${row.userid}" class="font-weight-bold text-dark text-decoration-none">${data || 'N/A'}</a>`;
                        }
                    },
                    {
                        data: 'userid',
                        render: function (data) {
                            return `<a href="user_profile.php?uid=${data}" class="font-weight-bold text-primary"><?php echo $hmpre; ?>${data}</a>`;
                        }
                    },
                    {
                        data: 'sponserid',
                        render: function (data) {
                            return `<?php echo $hmpre; ?>${data || 'SYSTEM'}`;
                        }
                    },
                    { data: 'email', defaultContent: 'N/A' },
                    { data: 'mobile', defaultContent: 'N/A' },
                    {
                        data: null,
                        render: function (data) {
                            if (data.active == '1') {
                                return '<span class="badge-status-active"><i class="fa fa-check-circle me-1"></i>Active</span>';
                            } else if (data.status == '2') {
                                return '<span class="badge-status-block"><i class="fa fa-ban me-1"></i>Blocked</span>';
                            } else {
                                return '<span class="badge-status-inactive"><i class="fa fa-times-circle me-1"></i>Inactive</span>';
                            }
                        }
                    },
                    {
                        data: 'kyc',
                        render: function (data) {
                            if (data == '2' || data == '1') {
                                return '<span class="badge badge-success px-2 py-1" style="border-radius:100px;">Approved</span>';
                            } else if (data == '3') {
                                return '<span class="badge badge-danger px-2 py-1" style="border-radius:100px;">Rejected</span>';
                            } else {
                                return '<span class="badge badge-warning px-2 py-1 text-dark" style="border-radius:100px;">Not Submitted</span>';
                            }
                        }
                    },
                    { data: 'joining_date', defaultContent: 'N/A' },
                    {
                        data: 'amount',
                        render: (data) => formatAdminCurrency(parseFloat(data || 0))
                    },
                    {
                        data: 'net_balance',
                        render: (data) => formatAdminCurrency(parseFloat(data || 0))
                    },
                    {
                        data: 'active_investment',
                        render: (data) => formatAdminCurrency(parseFloat(data || 0))
                    },
                    {
                        data: 'total_withdrawal',
                        render: (data) => formatAdminCurrency(parseFloat(data || 0))
                    },
                    {
                        data: null,
                        render: function (data) {
                            let btnClass = (data.status == '1') ? 'btn btn-danger btn-sm font-weight-bold' : 'btn btn-success btn-sm font-weight-bold';
                            let text = (data.status == '1') ? 'Block' : 'Unblock';
                            let type = (data.status == '1') ? 'deact' : 'act';

                            return `
                                <div class="d-flex gap-1">
                                    <a href="user_profile.php?uid=${data.userid}" class="btn btn-sm btn-primary font-weight-bold" style="padding: 4px 10px; border-radius: 8px;">
                                        <i class="fa fa-eye me-1"></i>View
                                    </a>
                                    <a href="move_team.php?user_id=${data.userid}" class="btn btn-sm font-weight-bold text-dark" style="padding: 4px 10px; border-radius: 8px; background:#f59e0b; border:none;">
                                        <i class="fa fa-sitemap me-1"></i>Move
                                    </a>
                                    <a href="action.php?uid=${data.userid}&type=${type}" class="${btnClass}" style="padding: 4px 10px; border-radius: 8px; color:#fff;">
                                        ${text}
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'ANANTA_Members_Directory'
                    }
                ]
            });

            function numberFormat(val) {
                return val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Status Filter Change
            $('#statusFilter').on('change', function () {
                table.column(6).search(this.value).draw();
            });

            // KYC Filter Change
            $('#kycFilter').on('change', function () {
                table.column(7).search(this.value).draw();
            });

            // Custom export button
            $('#customExportBtn').on('click', function () {
                table.button('.buttons-excel').trigger();
            });
        });
    </script>

</body>
</html>

