<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ;

$id = $_GET['id'] ?? null;
$row = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_query WHERE id = :id AND status = '0'");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

?>

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
                    <div class='card-body'>
                        <div class="card-title text-center">
                                    <h3>View Enquiry</h3>
                        </div>
                        <hr>
                        <div class="table-responsive">
                        <?php if ($row): ?>
                        <table id="example" class="table table-bordered">
                            <tr>
                                <th>User Id :</th>
                                <th><?php echo htmlspecialchars($hmpre . $row['userid']); ?></th>
                                <th>User Name :</th>
                                <th><?php echo htmlspecialchars($row['username']); ?></th>
                            </tr>
                            <tr>
                                <th>Email I'd :</th>
                                <th><?php echo htmlspecialchars($row['email']); ?></th>
                                <th>Mobile No :</th>
                                <th><?php echo htmlspecialchars($row['mobile']); ?></th>
                            </tr>
                            <tr>
                                <th>Subject :</th>
                                <th><?php echo htmlspecialchars($row['sub']); ?></th>
                                <th>Date :</th>
                                <th><?php echo htmlspecialchars($row['or_date']); ?></th>
                            </tr>
                            <tr>
                                <th>Description :</th>
                                <th colspan="3"><?php echo htmlspecialchars($row['message']); ?></th>
                            </tr>
                            <tr>
                                <th>Approved :</th>
                                <th>
                                    <a href="action-enquiry.php?id=<?php echo $row['id']; ?>&title=Approved"
                                       class="btn btn-sm btn-success">Click Here</a>
                                </th>
                                <th>Reject :</th>
                                <th>
                                    <a href="action-enquiry.php?id=<?php echo $row['id']; ?>&title=Reject"
                                       class="btn btn-sm btn-danger">Click Here</a>
                                </th>
                            </tr>
                        </table>
                        <?php else: ?>
                            <p class="p-3">No record found.</p>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        </div><!--End Row-->

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
<script>
    function getfunctionFees() {
    $.ajax({
        url: "topup_detail.php",
        type: "POST",
        data: { p_id: $('#sponser_id').val() },
        dataType: "json",
        success: function (jsonStr) {
            if (jsonStr.error) {
                alert(jsonStr.error);
            } else {
                $('#tst_sponsername').text(jsonStr.name);
                $('#tst_sponserid').text(jsonStr.sponserid);
                $('#status').text(jsonStr.status);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

</script>
	
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
