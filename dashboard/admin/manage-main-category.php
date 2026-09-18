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
                                    <h3>Manage Product</h3>
                                </div>
                                <hr>

                                <!-- Export Button -->
                                <button id="customExportBtn" class="btn btn-success mb-3">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </button>

                                <!-- Table -->
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover table-bordered" id="productsTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>CATEGORY NAME</th>
                                                <th>Image</th>
                                                <th>SUB MENU STATUS</th>
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

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>
                <!-- End Overlay -->

            </div>
        </div>

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
            let table = $('#productsTable').DataTable({
                ajax: {
                    url: 'get_category.php', // ✅ new backend file to fetch product data
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'name' },
                    
                    { 
                        data: 'image',
                        render: function (data, type, row) {
                            return `<img src="../categoryimages/${data}" alt="${row.title}" width="80">`;
                        }
                    },
                    { 
                        data: 'sub_status',
                        render: function (data) {
                            return data == '1' ? "Yes" : "No";
                        }
                    },
                    
                    {
                        data: null,
                        render: function (data) {
                            let actions = `
                                <a href="add-main-category.php?id=${data.id}&title=Update" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                            `;

                            if (data.status == '1') {
                                actions += ` <a href="update-main-category.php?id=${data.id}&title=Deactive" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i></a>`;
                            } else {
                                actions += ` <a href="update-main-category.php?id=${data.id}&title=Active" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>`;
                            }

                            return actions;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Product Data'
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
