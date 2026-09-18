<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>


<?php
date_default_timezone_set('Asia/Kolkata');

$date = date('d-m-Y');

if (isset($_POST['submit'])) {
    $sponser = $_POST['userid'];
    $sponser = substr($sponser, 2);
    $total_pin = $_POST['total_pin'];

    $stmt = $pdo->prepare("SELECT name FROM user WHERE userid = :sponser");
    $stmt->execute([':sponser' => $sponser]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $r['name'] ?? '';

    if ($total_pin >= 1) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pin_list WHERE userid = :userid AND status = '0'");
        $stmt->execute([':userid' => $userid]);
        $available_pins = $stmt->fetchColumn();

        if ($available_pins >= $total_pin) {
            $stmt = $pdo->prepare("UPDATE pin_list 
                                   SET userid = :sponser 
                                   WHERE userid = :userid AND status = '0' 
                                   ORDER BY id ASC 
                                   LIMIT {$total_pin}");
            $update_success = $stmt->execute([':sponser' => $sponser, ':userid' => $userid]);

            if ($update_success) {
                $stmt = $pdo->prepare("INSERT INTO pin_transfer 
                    (reciever_sponser, reciever_name, sender_sponser, sender_name, total_pin, type, date) 
                    VALUES (:reciever_sponser, :reciever_name, :sender_sponser, :sender_name, :total_pin, :type, :date)");
                
                $stmt->execute([
                    ':reciever_sponser' => $sponser,
                    ':reciever_name'    => $name,
                    ':sender_sponser'   => $userid,
                    ':sender_name'      => $username,
                    ':total_pin'        => $total_pin,
                    ':type'             => '',
                    ':date'             => $date
                ]);

                echo "<script>alert('Pin transferred successfully');window.location.assign('pin_transfer.php');</script>";
            } else {
                echo "<script>alert('Something went wrong');window.location.assign('pin_transfer.php');</script>";
            }
        } else {
            echo "<script>alert('Sorry!! You have no enough pins.');window.location.assign('pin_transfer.php');</script>";
        }
    } else {
        echo "<script>alert('Please Enter Minimum 1 Pin');window.location.assign('pin_transfer.php');</script>";
    }
}
?>


<script>
function getfunctionFees()
	{
		        $.ajax({
                       url: "topup_detail.php",
                       type: "POST",
                       data: {
						    'p_id':$('#sponser_id').val()
					       
                       },
                       dataType: "JSON",
                       success: function (jsonStr) {
                          
						  $('#tst_sponsername').text(jsonStr.name);
						  $('#tst_sponserid').text(jsonStr.sponserid);
						  $('#status').text(jsonStr.status);
                          //  alert(jsonStr.result);
                       }
                   });
	}
	
	</script>
<body class="bg-theme bg-theme1">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <!--Start sidebar-wrapper-->

   <!--End sidebar-wrapper-->
  

<!--Start topbar header-->

<!--End topbar header-->
<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3><i class="fa fa-table" aria-hidden="true"></i> Pin Transfer</h3></div>
                        <hr>
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="userid">User Id</label>
                                <input type="text" name="userid" id="sponser_id" class="form-control" onblur="getfunctionFees();">
                            </div>
                            <div class="form-group">
                                <span style="color:blue;" id="tst_sponsername"></span>
                            </div>
                            <div class="form-group">
                                <label for="total_pin">Number of Pin</label>
                                <input type="number" name="total_pin" id="total_pin" class="form-control">
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" name="submit" value="Transfer" class="btn btn-success">Transfer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!--End Row-->
        
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3> Pin Transfer Detail</h3></div>
                        <hr>
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
        </div>

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

    </div>
    <!-- End container-fluid-->
</div>
<!--End content-wrapper-->
   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	<!--Start footer-->
	<?php include 'common/footer.php' ?>
	<!--End footer-->
	
	
   
  </div><!--End wrapper-->


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        let table = $('#usersTable').DataTable({
            ajax: {
                url: 'get_pin_tranfer_detail.php',
                type: 'GET',
                data: { type: 'get_pin_transfer_detail' },
                dataSrc: ''
            },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    {
                        data: 'sender_sponser',
                        render: function (data) {
                            return `<?php echo $hmpre; ?>${data}`;
                        }
                    },
                    { data: 'sender_name' },
                    {
                        data: 'reciever_sponser',
                        render: function (data) {
                            return `<?php echo $hmpre; ?>${data}`;
                        }
                    },
                    { data: 'reciever_name'},
                    { data: 'total_pin'},
                    { data: 'package'},
                    { data: 'date'},
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

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
