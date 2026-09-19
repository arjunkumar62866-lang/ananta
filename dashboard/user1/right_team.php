<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - RIGHT TEAM REDESIGN
   Matches Dashboard (index.php) and Profile (profile.php)
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

/* =========================================================
   HEADER WELCOME BANNER
========================================================= */
.team-header-card {
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.10) 0%, rgba(2, 132, 199, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(22, 163, 74, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.team-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
    flex-shrink: 0;
}

/* =========================================================
   MAIN DATA CARD
========================================================= */
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

/* =========================================================
   TABLE & DATATABLES CUSTOM STYLING
========================================================= */
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
    background-color: rgba(22, 163, 74, 0.03) !important;
}

table.ananta-custom-table tbody td {
    padding: 14px 16px !important;
    color: #1e293b !important;
    font-size: 13.5px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    border-top: none !important;
}

/* User ID Pill */
.user-id-pill {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    color: #16a34a;
    background: rgba(22, 163, 74, 0.08);
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 13px;
    display: inline-block;
}

/* Status Badges */
.badge-status.active {
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

.badge-status.inactive {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
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
    border-color: #16a34a !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15) !important;
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
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
    color: #ffffff !important;
    border-color: #16a34a !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important;
    color: #16a34a !important;
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
                        <div class="card team-header-card border-0 p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="team-header-icon">
                                        <i class="fa fa-arrow-circle-right"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">BINARY NETWORK</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">RIGHT SIDE</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            Right Team <span style="background: linear-gradient(135deg, #16a34a 0%, #0284c7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Directory</span> 👉
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            Manage and monitor all member accounts registered on your right binary leg.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(22, 163, 74, 0.2); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                                        <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Right Team</span>
                                        <span class="font-weight-bold" style="font-size: 18px; color: #16a34a; font-weight: 800;"><?php echo number_format($right_team ?? 0); ?> Members</span>
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
                                    <h4><i class="fa fa-users text-success me-2"></i> Right Team Members List</h4>
                                    <p>Real-time downline genealogy data for your right branch</p>
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
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Sponsor ID</th>
                                            <th>Joining Date</th>
                                            <th>Status</th>
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
                    url: 'get_right_team.php',
                    type: 'GET',
                    data: { type: '' },
                    dataSrc: ''
                },
                columns: [
                    { 
                        data: 'sr',
                        render: function(data) {
                            return '<span style="font-weight: 700; color: #64748b;">#' + data + '</span>';
                        }
                    },
                    { 
                        data: 'userid',
                        render: function(data) {
                            return '<span class="user-id-pill"><i class="fa fa-user-circle-o me-1"></i><?php echo $hmpre; ?>' + data + '</span>';
                        }
                    },
                    { 
                        data: 'name',
                        render: function(data) {
                            return '<span style="font-weight: 700; color: #0f172a;"><i class="fa fa-user-o me-2 text-muted"></i>' + data + '</span>';
                        }
                    },
                    { 
                        data: 'sponsorid',
                        render: function(data) {
                            return '<span style="font-weight: 600; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12.5px;"><?php echo $hmpre; ?>' + data + '</span>';
                        }
                    },
                    { 
                        data: 'joining_date',
                        render: function(data) {
                            return '<span style="color: #64748b; font-size: 13px;"><i class="fa fa-calendar-o me-1 text-success"></i>' + data + '</span>';
                        }
                    },
                    { data: 'status' }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Right_Team_Members'
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search right team members...",
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
