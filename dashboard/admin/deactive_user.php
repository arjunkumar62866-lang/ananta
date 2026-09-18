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
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title text-center">
                                    <h3>All Block Users</h3>
                                </div>
                                <hr>

                                <!-- Export Button -->
                                <button id="customExportBtn" class="btn btn-success mb-3">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </button>

                                <!-- Table -->
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover table-bordered" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>User ID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Sponsor ID</th>
                                                <th>Sponsor Name</th>
                                                
                                                <th>Action</th>
                                                <th>Joining Date</th>
                                                
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
                    data: { type: 'deactive_user' },
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
                    
                    { 
                        data: null,
                        render: function (data) {
                            if (data.status == '1') {
                                return `<a href="action.php?uid=${data.userid}&type=deact">Block</a>`;
                            } else {
                                return `<a href="action.php?uid=${data.userid}&type=act">UnBlock</a>`;
                            }
                        }
                    },

                    
                    { data: 'joining_date'}
                 

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
