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

    <div id="wrapper">

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <div class="row mt-3">
                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">

                                <div class="card-title text-center">
                                    <h3>All Banners</h3>
                                </div>
                                <hr>

                                <!-- Add New Banner Button -->
                                <a href="banner.php" class="btn btn-primary mb-3">
                                    + Add New Banner
                                </a>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="bannerTable">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Image</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

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

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#bannerTable').DataTable({
                ajax: {
                    url: 'get_banners.php',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (d, t, r, meta) => meta.row + 1 },

                    { 
                        data: 'img',
                        render: data => `<img src="${data}" width="120" height="60" style="object-fit:cover;">`
                    },

                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return `<span class="badge badge-success">Active</span>`;
                            } else {
                                return `<span class="badge badge-danger">Inactive</span>`;
                            }
                        }
                    },

                    { 
                        data: null,
                        render: function (row) {
                            return `
                                <a href="banner.php?id=${row.id}" class="btn btn-warning btn-sm">Update</a>
                                <a href="delete_banner.php?id=${row.id}" onclick="return confirm('Delete this banner?');" class="btn btn-danger btn-sm">Delete</a>
                            `;
                        }
                    }
                ],
                pageLength: 10
            });
        });
    </script>

</body>
</html>
