<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php 
include("common/header.php"); 

// getmysponserid($user_sponsor_code);

$percenset = getpercentage();
$level1=$percenset['level1'];
$level2=$percenset['level2'];
$level3=$percenset['level3'];
$level4=$percenset['level4'];
$level5=$percenset['level5'];
$level6=$percenset['level6'];
$level7=$percenset['level7'];

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $idpin = $_POST['idpin'] ?? '';
    $activateuserid = $_POST['userid'] ?? '';
    $packageid = $_POST['price'] ?? '';

    $activateuserid = substr($activateuserid, 2);

    // Get package
    $stmt = $pdo->prepare("SELECT * FROM tbl_package WHERE price = :price");
    $stmt->execute([':price' => $packageid]);
    $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

    $perday = $rowheader['income'];
    $totalincome = $rowheader['totalincome'];  
    $days = $rowheader['days']; 

    // Get user
    $stmt1 =$pdo->prepare("SELECT * FROM user WHERE userid = :userid");
    $stmt1->execute([':userid' => $activateuserid]);
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $cur_actives = $row1['active'];

    if($cur_actives == '0') {

        // Check pin
        $stmtPin = $pdo->prepare("SELECT * FROM pin_list WHERE pin = :pin AND package = :package AND status = '0'");
        $stmtPin->execute([':pin' => $idpin, ':package' => $packageid]);
        $countpin = $stmtPin->rowCount();

        if($countpin >= 1) {
            $error1 = 0;
        } else {
            $error1 = 1;
            echo '<script>alert("Pin is Invalid") </script>';
        }

        if($error1 == 0) {

            // Update user
            $sql = "UPDATE user SET pin = :pin, active = '1', upgrade_date = :date, package = :package 
                    WHERE userid = :userid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':pin' => $idpin,
                ':date' => $date,
                ':package' => $packageid,
                ':userid' => $activateuserid
            ]);

            // Update pin_list
            $stmt = $pdo->prepare("UPDATE pin_list SET status='1', usedby=:userid, used_date=:date WHERE pin=:pin");
            $stmt->execute([':userid' => $userid, ':date' => $date, ':pin' => $idpin]);

            $subject1 = "ID ACTIVATION USING PIN - $idpin";

            // Insert into tbl_transaction
            $sql1 = "INSERT INTO tbl_transaction (user_id,type,subject,time,created_date,status)
                     VALUES (:user_id,'Credit',:subject,:time,:date,'1')";
            $stmt1 = $pdo->prepare($sql1);
            if($stmt1->execute([
                ':user_id' => $userid,
                ':subject' => $subject1,
                ':time' => $time,
                ':date' => $date
            ])) {
                // Insert into tbl_roi_one
                $sqluser1 = "INSERT INTO tbl_roi_one(user_id,level,package,amount,percentage,date,time,status,count)
                             VALUES (:user_id,'1',:package,'0',:perday,:date,:time,'0',:days)";
                $stmt2 = $pdo->prepare($sqluser1);
                $stmt2->execute([
                    ':user_id' => $activateuserid,
                    ':package' => $packageid,
                    ':perday' => $perday,
                    ':date' => $date,
                    ':time' => $time,
                    ':days' => $days
                ]);
            }

            // Final user update
            $sql2 = "UPDATE pin_list SET usedby=:usedby, used_date=CURDATE(), status='1' WHERE pin=:pin";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([':usedby' => $activateuserid, ':pin' => $idpin]);

            $pinfinal = $activateuserid;
            
           
            updateTreeCounts($pdo, $activateuserid);

            echo "<script>alert('Your Id Activate Successfully');window.location.assign('binary-user-activate');</script>";
                
                
         
        
            

        } // end error1
    } else {
        echo "<script>alert('Your Id Activated Already');window.location.assign('binary-user-activate');</script>";
    }
}


?>

<script>
    function getfunctionFees(){
        $.ajax({
            url: "topup_detail.php",
            type: "POST",
            data: {
                'p_id':$('#sponserid').val()
                
            },
            dataType: "JSON",
            success: function (jsonStr) {
                      
			$('#tst_sponsername').text(jsonStr.name);
			//alert("hello");
			//$('#tst_sponserid').text(jsonStr.sponserid);
			//$('#status').text(jsonStr.status);
            //  alert(jsonStr.result);
            }
            
        });
	}
	
    function getpin(val) {
        $.ajax({
        type: "POST",
        url: "pindata.php",
        data:'id='+val,
        success: function(value){
            $("#pin").html(data);
            {
                var data = value.split(",");
                if (data[0] != 0) {
                    $("#btnSubmit").attr("disabled", false);
                    $("#mlmname1").html("Pin is Valid");
                    
                }
                else{
                    $("#btnSubmit").attr("disabled", true);
                    $("p").show();
                    $("#mlmname1").html("Pin is Invalid.");
                }
            }
        }
        });
    }
</script>


<body class="bg-theme bg-theme1">

<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner">
      <div class="loader"></div>
    </div>
  </div>
</div>
<!-- end loader -->

<!-- Start wrapper-->
<div id="wrapper">

  <div class="clearfix"></div>
  
  <div class="content-wrapper">
    <div class="container-fluid">

      <div class="row mt-3">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="card-title text-center">
          <h3>Other User ID Activate</h3>
        </div>
        <hr>

        <div class="row ">
          <div class="col-md-12 p-4">

            <div class="tile">

              
              <form method="post" id="form-data" action="">
                <!-- USER ID -->
                <div class="form-group">
                  <label class="form-label">USER ID</label>
                  <input type="text" name="userid" id="sponserid" 
                         class="form-control" 
                         placeholder="Enter User ID" 
                         onblur="getfunctionFees();" required>
                  <p style="color:blue" id="tst_sponsername"></p>
                </div>

                <!-- PRICE -->
                <div class="form-group">
                  <label class="form-label">Choose Price</label>
                  <select name="price" class="form-control" required id="price">
                    <option value="0">--Choose Price--</option>
                    <?php 
                      try {
                          $sql_select = "SELECT * FROM tbl_package WHERE status = :status";
                          $stmt = $pdo->prepare($sql_select);
                          $stmt->execute([':status' => 1]);
                          if ($stmt->rowCount() > 0) {
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                  <option value="<?php echo htmlspecialchars($row['price']); ?>">
                                      <?php echo htmlspecialchars($row['price']); ?>
                                  </option>
                              <?php }
                          }
                      } catch (PDOException $e) {
                          echo "<option disabled>Error loading prices</option>";
                      }
                    ?> 
                  </select>
                </div>

                <!-- PIN -->
                <div class="form-group">
                  <label class="form-label">Choose Pin</label>
                  <select name="idpin" class="form-control" required>
                    <option value="">--Please Select Pin--</option>
                    <?php 
                      try {
                          $sql_pin = "SELECT * FROM pin_list WHERE status = :status AND userid = :userid ORDER BY id DESC";
                          $stmt_pin = $pdo->prepare($sql_pin);
                          $stmt_pin->execute([':status' => 0, ':userid' => $userid]);
                          while ($row = $stmt_pin->fetch(PDO::FETCH_ASSOC)) { ?>
                              <option value="<?php echo htmlspecialchars($row['pin']); ?>">
                                <?php echo htmlspecialchars($row['pin']); ?>
                              </option>
                          <?php }
                      } catch (PDOException $e) {
                          echo "<option disabled>Error loading pins</option>";
                      }
                    ?> 
                  </select>
                </div>

                <!-- SUBMIT -->
                <div class="tile-footer text-center">
                  <button type="submit" name="submit" class="btn btn-success">
                    <i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
                  </button>
                </div>
              </form>

            </div>
          </div>
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
  <a href="javaScript:void();" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
  </a>
  <!--End Back To Top Button-->

  <!--Start footer-->
  <?php include 'common/footer.php' ?>
  <!--End footer-->
  
</div><!--End wrapper-->


</body>
</html>
