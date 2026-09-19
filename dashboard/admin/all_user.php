<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1">

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

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                                    <div>
                                        <h4 class="font-weight-bold text-dark mb-1" style="color: #0f172a;">All Registered Users</h4>
                                        <p class="text-muted small mb-0">View, search and manage all system user accounts</p>
                                    </div>
                                    <div class="mt-3 mt-md-0">
                                        <button id="customExportBtn" class="btn font-weight-bold text-white shadow-sm" style="background: #16a34a; border-radius: 10px; border: none; padding: 8px 18px;">
                                            <i class="fa fa-file-excel-o me-1"></i> Export to Excel
                                        </button>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover align-middle border" id="usersTable" style="border-radius: 12px; overflow: hidden;">
                                        <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase;">
                                            <tr>
                                                <th>Sr</th>
                                                <th>User ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Sponsor ID</th>
                                                <th>Sponsor Name</th>
                                                <th>Joining Date</th>
                                                <th>Login</th>
                                                <th>Status</th>
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
                        data: 'userid',
                        render: function (data) {
                            return `<a href="user_profile.php?uid=${data}"><?php echo $hmpre; ?>${data}</a>`;
                        }
                    },
                    { data: 'name' },
                    { data: 'mobile' },
                    {
                        data: 'sponserid',
                        render: function (data) {
                            return `<?php echo $hmpre; ?>${data}`;
                        }
                    },
                    { data: 'sponsername' },
                    { data: 'joining_date' },
                    {
                        data: 'userid',
                        render: function (data) {
                            return `<a target="_blank" href="../user/index.php?uid=${data}">Login</a>`;
                        }
                    },
                    {
    data: null,
    render: function (data) {
        if (data.active == '1') {
            return "Active";
        } else if (data.status == '2') {
            return "Block";
        } else {
            return "Inactive";
        }
    }
},

        {
            data: null,
            render: function (data) {
        
                let btnClass = (data.status == '1') 
                    ? 'btn btn-danger btn-sm' 
                    : 'btn btn-success btn-sm';
        
                let text = (data.status == '1') 
                    ? 'Block' 
                    : 'Unblock';
        
                let type = (data.status == '1') 
                    ? 'deact' 
                    : 'act';
        
                return `
                    <a href="action.php?uid=${data.userid}&type=${type}" 
                       class="${btnClass}" 
                       style="padding: 5px 10px; border-radius: 5px; color:#fff;">
                       ${text}
                    </a>
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
                        title: 'Members Data'
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
