<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php'; // contains $pdo
$date = date('Y-m-d');
$pdate = date('Y-m-d', strtotime("-30 days"));
?>

<body class="bg-theme bg-theme1">

<div id="pageloader-overlay" class="visible incoming">
   <div class="loader-wrapper-outer"><div class="loader-wrapper-inner">
       <div class="loader"></div>
   </div></div>
</div>

<div id="wrapper">

<div class="clearfix"></div>

<?php

// 1) TOTAL USERS FOR ROYALTY ONE
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tbl_royalty_user 
    WHERE full_status = 0
");
$stmt->execute();
$total_club_user = $stmt->fetchColumn();

// 2) Get Current Month CTO
// Get current year and month
    $currentYear = date('Y');
    $currentMonth = date('m');

    // Query for current month's CTO
    $stmt = $pdo->prepare("
        SELECT 
            SUM(package ) AS total_cto,
            COUNT(id) AS total_entries
        FROM tbl_roi_one
        WHERE YEAR(`date`) = :year AND MONTH(`date`) = :month
    ");

    $stmt->execute([
        ':year' => $currentYear,
        ':month' => $currentMonth
    ]);

    $currentMonthCTO = $stmt->fetch(PDO::FETCH_ASSOC);

$currentMonthCTO1 = ($currentMonthCTO['total_cto']*0.2)/100;
// 3) ONE PERSON AMOUNT
$payamount = ($total_club_user > 0) 
             ? round(($currentMonthCTO1 / $total_club_user), 2) 
             : 0;


?>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- SUMMARY CARD -->
        <div class="row mt-4">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body">

                        <div class="card-title"><h4>Leaderhip Income</h4></div>
                        <hr>

                        <div class="form-group">
                            <label>Total CTO in 30 Days:</label>
                            <?= $hmcurrency.$currentMonthCTO['total_cto']; ?>
                        </div>

                        <div class="form-group">
                            <label>Total ID for Leadership Income:</label>
                            <?= $total_club_user ?>
                        </div>

                        <div class="form-group">
                            <label>Total Amount to Distribute:</label>
                            <?= $hmcurrency.$currentMonthCTO1 ?>
                        </div>

                        <div class="form-group">
                            <label>One Person Amount:</label>
                            <?= $hmcurrency.$payamount ?>
                        </div>

                        <form method="get" action="royalty-one-pay.php?amount=<?php echo $payamount?>;">
                            <div class="form-group">
                                <input class="form-control form-control-rounded" 
                                       name="amount" 
                                       value="<?= $payamount ?>">
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" name="submit"
                                    class="btn btn-primary shadow-primary btn-round px-5">
                                    <i class="icon-lock"></i> Leadership Income Pay
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-table"></i> Account History</div>
        
                    <div class="card-body">
        
                        <button id="customExportBtn" class="btn btn-success mb-3">
                            <i class="fa fa-file-excel-o"></i> Export to Excel
                        </button>
        
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>User ID</th>
                                        <th>Transaction</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody></tbody>
                            </table>
                        </div>
        
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</div>

<a href="javaScript:void();" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
</a>

<?php include 'common/footer.php'; ?>

<!-- jQuery (REQUIRED FIRST!!) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {

    let table = $('#usersTable').DataTable({
        ajax: {
            url: 'get_royalty_one.php',
            type: 'GET',
            dataSrc: ''
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            {
                data: 'userid',
                render: function (data) {
                    return `<?php echo $hmpre; ?>${data}`;
                }
            },
            { data: 'time' },
            {
                data: 'amount',
                render: function (data) {
                    return `<?php echo $hmcurrency; ?>${data}`;
                }
            },
            { data: 'created_date' }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100, 1000]
    });

    $('#customExportBtn').on('click', function () {
        table.button('.buttons-excel').trigger();
    });

});
</script>

</div>
</body>
</html>
