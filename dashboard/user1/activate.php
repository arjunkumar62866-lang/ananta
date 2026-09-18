<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include "common/header.php";

$percenset = getpercentage();
$level1 = $percenset["level1"];
$level2 = $percenset["level2"];
$level3 = $percenset["level3"];
$level4 = $percenset["level4"];
$level5 = $percenset["level5"];
$level6 = $percenset["level6"];
$level7 = $percenset["level7"];

if (isset($_POST["submit"])) {
    $price = $_POST["price"];
    $pinmy = $_POST["pinmy"];

    $sqlhmst = "SELECT * FROM pin_list WHERE pin = :pin AND package = :package AND status = 0";
    $stmt = $pdo->prepare($sqlhmst);
    $stmt->execute([':pin' => $pinmy, ':package' => $price]);
    if ($stmt->rowCount() > 0) {
        while ($rowhmst = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($rowhmst) {
            $package = $rowhmst["package"];
                // pin base price check condition
                if ($package == $price) {
                    $query = $pdo->prepare("SELECT * FROM tbl_package WHERE price = :price");
                    $query->execute([':price' => $price]);
                    $rowheader = $query->fetch(PDO::FETCH_ASSOC);

                    $perday = $rowheader["income"];
                    $totalincome = $rowheader["totalincome"];
                    $days = $rowheader["days"];

                    $updateUser = $pdo->prepare("UPDATE user 
                        SET active = '1', upgrade_date = :date, package = :price, pin = :pin 
                        WHERE userid = :userid");
                    $updateUser->execute([
                        ':date' => $date,
                        ':price' => $price,
                        ':pin' => $pinmy,
                        ':userid' => $userid
                    ]);

                    $updatePin = $pdo->prepare("UPDATE pin_list 
                        SET status = '1', usedby = :userid, used_date = :date 
                        WHERE pin = :pin");
                    $updatePin->execute([
                        ':userid' => $userid,
                        ':date' => $date,
                        ':pin' => $pinmy
                    ]);

                    $subject1 = "ID ACTIVATION USING PIN - $pinmy";

                    $sql1 = $pdo->prepare("INSERT INTO tbl_transaction (user_id, type, subject, time, created_date, status)
                        VALUES (:userid, 'Credit', :subject, :time, :date, '1')");
                    if ($sql1->execute([
                        ':userid' => $userid,
                        ':subject' => $subject1,
                        ':time' => $time,
                        ':date' => $date
                    ])) {
                        $sqluser1 = $pdo->prepare("INSERT INTO tbl_roi_one(user_id, level, package, amount, percentage, date, time, status, count)
                            VALUES (:userid, '1', :price, '0', :perday, :date, :time, '0', :days)");
                        $sqluser1->execute([
                            ':userid' => $userid,
                            ':price' => $price,
                            ':perday' => $perday,
                            ':date' => $date,
                            ':time' => $time,
                            ':days' => $days
                        ]);

                        $date = date("Y-m-d");
                        // id sponsorid
                        $mysponsernew = getmysponserid($userid);
                        $totalsponserdirect = getmydirectactive($mysponsernew);
                    }

                    $pinfinal = $userid;
                    for ($i = 0; $i < 5; $i++) {
                        $mysponserid = getmysponserid($pinfinal);
                        $sponserdetails = getuserdatabysponserid($mysponserid);

                        if ($pinfinal !== "1290") {
                            $spcode1 = $sponserdetails["userid"];
                            $spamont = $sponserdetails["amount"];
                            $isidactive = $sponserdetails["idactive"];
                            $directactive = getmydirectactive($spcode1);

                            if ($isidactive == 1) {
                                if ($i == "0") {
                                    $transactionamount = getpercent($price, $Level1);
                                } elseif ($i == "1") {
                                    $transactionamount = getpercent($price, $Level2);
                                } elseif ($i == "2") {
                                    $transactionamount = getpercent($price, $Level3);
                                } elseif ($i == "3") {
                                    $transactionamount = getpercent($price, $Level4);
                                } elseif ($i == "4") {
                                    $transactionamount = getpercent($price, $Level5);
                                } elseif ($i == "5") {
                                    $transactionamount = getpercent($price, $Level6);
                                } elseif ($i == "6") {
                                    $transactionamount = getpercent($price, $Level7);
                                }

                                $spamont = $spamont + $transactionamount;

                                updatedatabysponserid($spcode1, $spamont);
                                $pinfinal = $spcode1;

                                if (isset($transactionamount)) {
                                    $level = $i + 1;
                                    $messagenew = "Team Building Income on Level-$level.of Id($userid)";
                                    insert_transction($spcode1, $transactionamount, $messagenew, $time, "CREDIT");
                                    insert_userlevel($mysponserid, $userid, $level);
                                }
                            } else {
                                $pinfinal = $spcode1;
                            }
                        }
                    }

                    $mobileno = $mobile;
                    $messagenew = "Hello !!! Thank You For Join " . $title . " :\n User Id :- $hmpre" . $userid . "\n Password :- " . $pass;
                    // sendsms($mobileno, $messagenew);

                    echo '<script>alert("Thank you for ID ACTIVATION ");</script>';
                    echo '<script>window.location.href = "dashboard.php";</script>';
                } else {
                    echo '<script>alert("Please Choose Pin Equal to Package  ");</script>';
                }
            }
        }
    } else {
        echo '<script>alert("PIN ARE NOT VALID KINDLY CHECK WITH ADMIN");</script>';
    }
}
if ($idactive == 1) {
    echo '<script>window.location.href = "index.php";</script>';
}

$stmt = $pdo->prepare("SELECT * FROM tbl_payment WHERE tr_id = :tid AND status = '1'");
$stmt->execute([':tid' => $tid]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


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
                <h3>User ID Activate</h3>
              </div>
              <hr>

              <div class="row ">
                <div class="col-md-12 p-4">
                  

                  <div class="tile">
                       
                      <h6 style="text-align:center;color:red">
                          <a href="fund_request"style="text-align:center;color:red"> 
                        Send Payment Request For Id Activation Click Here.
                        </a>
                      </h6>
                     

                    <form method="post" id="form-data">
                      <!-- USER ID -->
                      <div class="form-group">
                        <label class="form-label">USER Id</label>
                        <input type="text" name="userid" class="form-control"
                          placeholder="Please Enter Receiver Account Id"
                          value="<?php echo $hmpre; ?><?php echo $userid;?>" readonly>
                      </div>

                      <!-- PRICE -->
                      <div class="form-group">
                        <label for="input-1">Choose Price</label>
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
                        <label class="form-label">PIN</label>
                        <input type="text" name="pinmy" class="form-control" id="pin"
                          placeholder="Please Enter Pin"> 
                        <span id="about_pin" style="color:blue;"></span>
                      </div>

                      <!-- SUBMIT -->
                      <button type="submit" name="submit" class="btn btn-success">Submit</button>
                    </form>

                  </div>
                </div>
              </div>
              <!-- ✅ End User ID Activate Form -->

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

<script>
    $('document').ready(function(){
        $('#price').on("change",function(){
            //alert('Hi');
            var price = $('#price').val();
            var search_term = $('#pin').val();
            if(search_term.length>0){
                $.ajax({
                        url  : "checkpin.php",
                        type : "POST",
                        data : {pin_id:search_term , price : price},
                        success : function(data){
                                    console.log(data);
                                    if(data == 1){
                                        $('#about_pin').html('Pin is Valid');
                                    }else{
                                        $('#about_pin').html('Pin is not valid'); 
                                    }
                        }
                })
            }
                            
        });
                        
                        $('#pin').on("blur",function(){
                            //alert('Hi2');
                            var price = $('#price').val();
                            var search_term = $('#pin').val();
                           $.ajax({
                               url  : "checkpin.php",
                               type : "POST",
                               data : {pin_id:search_term , price : price},
                               success : function(data){
                                   console.log(data);
                                   if(data == 1){
                                       $('#about_pin').html('Pin is Valid');
                                   }else{
                                       $('#about_pin').html('Pin is not valid'); 
                                   }
                               }
                           }) 
                        });
    });
</script>

</body>
</html>
