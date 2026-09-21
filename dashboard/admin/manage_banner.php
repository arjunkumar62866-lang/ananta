<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1" style="background-color: #f1f5f9 !important; color: #0f172a !important; font-family: 'Inter', sans-serif;">

<style>
  body, .content-wrapper, .container-fluid {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
  }
  .page-banner {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.25);
  }
  .page-banner h3 {
    color: #ffffff !important;
    font-weight: 700;
    margin: 0;
    font-size: 1.5rem;
  }
  .page-banner p {
    color: #e0f2fe !important;
    margin: 4px 0 0 0;
    font-size: 0.9rem;
  }
  .ananta-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    padding: 24px !important;
  }
  .dataTables_wrapper {
    color: #0f172a !important;
  }
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter,
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_processing,
  .dataTables_wrapper .dataTables_paginate {
    color: #0f172a !important;
    margin-bottom: 12px;
  }
  .dataTables_wrapper .dataTables_length select,
  .dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    outline: none !important;
  }
  .table-custom {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    margin-top: 15px !important;
  }
  .table-custom thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.05em !important;
    border-bottom: 2px solid #e2e8f0 !important;
    padding: 14px 16px !important;
  }
  .table-custom tbody td {
    color: #0f172a !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 14px 16px !important;
    vertical-align: middle !important;
    font-size: 0.9rem !important;
  }
  .table-custom tbody tr:hover {
    background-color: #f8fafc !important;
  }
  .btn-add-banner {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 600 !important;
    padding: 8px 18px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.2s ease !important;
  }
  .btn-add-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35) !important;
  }
</style>

    <div id="wrapper">

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid" style="padding: 24px;">

                <div class="page-banner">
                    <h3><i class="fa fa-picture-o mr-2"></i> Banner Management</h3>
                    <p>Upload and manage homepage promotional banners</p>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <div class="ananta-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 style="color:#0f172a; font-weight:700; margin:0;">Banners List</h5>
                                <!-- Add New Banner Button -->
                                <a href="banner.php" class="btn btn-add-banner">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New Banner
                                </a>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-custom" id="bannerTable">
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
        </div>

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
                        render: data => `<img src="${data}" width="120" height="60" style="object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 2px 8px rgba(0,0,0,0.08);">`
                    },

                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return `<span class="badge" style="background-color:#f0fdf4; color:#15803d; border:1px solid #dcfce7; padding:6px 12px; border-radius:20px; font-weight:600;">Active</span>`;
                            } else {
                                return `<span class="badge" style="background-color:#fef2f2; color:#b91c1c; border:1px solid #fee2e2; padding:6px 12px; border-radius:20px; font-weight:600;">Inactive</span>`;
                            }
                        }
                    },

                    { 
                        data: null,
                        render: function (row) {
                            return `
                                <a href="banner.php?id=${row.id}" class="btn btn-sm btn-outline-warning" style="border-radius:6px; font-weight:600;">Update</a>
                                <a href="delete_banner.php?id=${row.id}" onclick="return confirm('Delete this banner?');" class="btn btn-sm btn-outline-danger" style="border-radius:6px; font-weight:600;">Delete</a>
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

