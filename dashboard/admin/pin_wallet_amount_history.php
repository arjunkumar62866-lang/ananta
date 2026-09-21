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
</style>

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid" style="padding: 24px;">

                <div class="page-banner">
                    <h3><i class="fa fa-history mr-2"></i> Manage Fund History</h3>
                    <p>Complete record of funds added or sent to user wallets</p>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="ananta-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 style="color:#0f172a; font-weight:700; margin:0;">Fund Transfer Records</h5>
                                <button id="customExportBtn" class="btn btn-outline-success btn-sm style-btn" style="border-radius:8px; font-weight:600;">
                                    <i class="fa fa-download mr-1"></i> Export Excel
                                </button>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive" id="tblData">
                                <table class="table table-custom" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>User ID</th>
                                            <th>User Name</th>
                                            <th>Message</th>
                                            <th>Amount</th>
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
                </div><!-- End Row -->

            </div>
            <!-- End container-fluid -->
        </div>
        <!-- End content-wrapper -->

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
                    url: 'get_manage_fund_history.php',
                    type: 'GET',
                    data: { type: 'pin_wallet_amount_history' },
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    {
                        data: 'user_id',
                        render: function (data) {
                            return `<strong style="color:#0284c7;"><?php echo $hmpre; ?>${data}</strong>`;
                        }
                    },
                    { data: 'name'},
                    { data: 'subject'},
                    {
                        data: 'amount',
                        render: function (data) {
                            return `<span style="font-weight:700; color:#16a34a;">₹${data}</span>`;
                        }
                    },
                    { data: 'created_date'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100, 1000],
                dom: 'lfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Fund History Data'
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

