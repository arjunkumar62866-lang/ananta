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
                                    <h3>Manage Withdrawal</h3>
                                </div>
                                <hr>

                                <!-- Export Button -->
                                <button id="customExportBtn" class="btn btn-success mb-3">
                                    <i class="fa fa-download"></i> 
                                </button>

                                <!-- Table -->
                                <div class="table-responsive" id="tblData">
                                    <table class="table table-hover table-bordered" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>Sno.</th>
                                                <th>Transaction Id</th>
                                                <th>User </th>
                                                <th>Name </th>
                                                <th>Transaction</th>
                                                <!--<th>Mode</th>-->
                                                <th>R Amount</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>A/C No.</th>
                                                <th>IFSC</th>
                                                <!--<th>Bank</th>-->
                                                <!-- <th>Memo</th>-->
                                                <!--<th>Mobile</th>-->
                                                <th>Wallet Address</th>
                                                <th>Status</th>
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
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type') || 'all';
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
                            return `<a href="user_profile.php?uid=${data}"><?php echo $hmpre; ?>${data}</a>`;
                        }
                    },
                    { data: 'name' },
                    { data: 'subject' },
                    {
                        data: 'act_amount',
                        render: function (data) {
                            return `<?php echo $hmcurrency; ?>${data}</a>`;
                        }
                    },
                    {
                        data: 'amount',
                        render: function (data) {
                            return `<?php echo $hmcurrency; ?>${data}</a>`;
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
                            return "Pending";
                        } else if(data=='1') {
                            return "Approved";
                        }
                        else {
                            return "Canceled";
                        }
                        }
                    },
                    { data: 'created_date'},
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (row.a_status == "0") {
                                return `
                                    <a href="approved-withdrawal.php?tid=${row.id}&beneficiary_id=${row.api_txn_no}&user_id=${row.user_id}&amt=${row.amount}">
                                        <button type="button" class="btn btn-success btn-sm">
                                            Approve <i class="far fa-check-circle"></i>
                                        </button>
                                    </a> ||

                                    <a href="cancel-investment-withdraw.php?uid=${row.user_id}&amt=${row.act_amount}&tid=${row.id}&investment_id=${row.api_txn_no}">
                                        <button type="button" class="btn btn-danger btn-sm">
                                            Cancel <i class="m-r-10 mdi mdi-keyboard-return"></i>
                                        </button>
                                    </a> ||

                                    <a target="_blank" href="https://zozowallet.com/web-api/check-status-byclient_id?api_token=k9vFTXcPJAhgcswDb5Q1RimMw2ErSydwJ0inVtMhe6TLHrwpuZNF7MmznFP7&client_id=${row.api_txn_no}">
                                    </a>
                                `;
                            } else {
                                return '';
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
