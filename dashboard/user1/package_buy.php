<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php
include "common/header.php";

$day = date("d");
$currentTime = date("H:i:s");

$percenset = getpercentage();
$level1 = $percenset["level1"];
$level2 = $percenset["level2"];
$level3 = $percenset["level3"];
$level4 = $percenset["level4"];
$level5 = $percenset["level5"];
$level6 = $percenset["level6"];
$level7 = $percenset["level7"];

// Fetch packages
$stmt = $pdo->prepare("SELECT * FROM tbl_package ORDER BY id ASC");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["submit"])) {
    if($day=="31"){
        echo("<script>alert('On 31 day Registration is not allowed.'); window.location='index.php'; </script>");
        exit();
        
    }
    
    $package_id = $_POST['package_id'];
    $price = $_POST["price"];

    $stmt = $pdo->prepare("SELECT * FROM tbl_package WHERE id = :id");
    $stmt->execute([":id" => $package_id]);
    $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

    // ---------------------- ADDING MIN–MAX VALIDATION HERE ----------------------
    if ($rowheader) {

        $pkg_id = $rowheader['id'];
        $pkg_name = $rowheader['name'];
        $amount = $price;

        $error = [];

        if ($pkg_id == 1) {
            if ($amount < 12000 || $amount > 80000) {
                $error[] = "Amount must be between ₹12000 and ₹80000 for package '$pkg_name'";
            }
        } elseif ($pkg_id == 2) {
            if ($amount < 81000 || $amount > 400000) {
                $error[] = "Amount must be between ₹81000 and ₹400000 for package '$pkg_name'";
            }
        } elseif ($pkg_id == 3) {
            if ($amount < 400000 || $amount > 800000) {
                $error[] = "Amount must be between ₹400000 and ₹800000 for package '$pkg_name'";
            }
        } elseif ($pkg_id == 4) {
            if ($amount < 800000 || $amount > 1200000) {
                $error[] = "Amount must be between ₹800000 and ₹1200000 for package '$pkg_name'";
            }
        } elseif ($pkg_id == 5) {
            if ($amount < 1200000) {
                $error[] = "Amount must be greater then ₹1200000 for package '$pkg_name'";
            }
        }

        // If error, stop immediately
        if (!empty($error)) {
            echo "<script>alert('$error');window.location.href='index.php';</script>";
            exit;
        }
    }
    // ---------------------- END VALIDATION ----------------------


    $percentage = $rowheader["income"];
    $packname = $rowheader["name"];
    $days = $rowheader["days"];
    $lockdays = $rowheader["days"];

    // if ($currentTime >= "05:00" && $currentTime <= "23:60")
    if (1 == 1) {
        if ($price > 0) {
            if ($pin_wallet >= $amount) {
                // if ($price % 50 == 0) {
                if (1==1) {
                    $inc_limitpackage = (int) 2 * (int) $price;
                    $stmt = $pdo->prepare("
                    UPDATE user 
                    SET inc_limit = :inc_limitpackage,
                    
                    total_package = total_package + :price,
                    
                    
                    
                    upgrade_date = :date,
                    atime = :time,
                    pin_wallet = pin_wallet - :price
                    WHERE userid = :userid
                    ");
                    $stmt->execute([
                        ":inc_limitpackage" => $inc_limitpackage,
                        ":price" => $price,
                        
                        ":date" => $date,
                        ":time" => $time,
                        ":userid" => $userid,
                    ]);

                    
                    $subject1 = "Id Activation Using Fund- $price";
                    $sql1 = "INSERT INTO tbl_transaction (user_id, amount, type, subject, time, created_date, status) VALUES 
                            (:userid, :price, 'Credit', :subject1, :time, :date, '1')";
                    $stmt1 = $pdo->prepare($sql1);
                    if (
                        $stmt1->execute([
                            ":userid" => $userid,
                            ":price" => $price,
                            ":subject1" => $subject1,
                            ":time" => $time,
                            ":date" => $date,
                        ])
                    ) {
                        
                        $sqluser1 = "INSERT INTO tbl_roi_one
                        (user_id, name, package, percentage, date, closingdate, time, status, lock_day, capping)
                        VALUES 
                        (:userid, :name, :price, :percentage, :date, :date, :time, '0', :lockdays, :inc_limitpackage)";
                        $stmt2 = $pdo->prepare($sqluser1);
                        $stmt2->execute([
                            ":userid" => $userid,
                            ':name' => $packname,
                            ":price" => $price,
                            ":percentage" => $percentage,
                            ":date" => $date,
                            ":time" => $time,
                            // ":days" => $days,
                            ":lockdays" => $lockdays,
                            ":inc_limitpackage" => $inc_limitpackage,
                        ]);
                        
                        $pinfinal = $userid;
                        // get sponsor code by user id
                        $mysponserid = getmysponserid($pinfinal);
                        // get user data code by sponsor id
                        $sponserdetails = getuserdatabysponserid($mysponserid);
                        $spcode1 = $sponserdetails["userid"];
                        $active = $sponserdetails['idactive'];
                        
                        if($active=="1"){
                        
                            // Insert into tbl_roi_two
                            
                            $percentage = 0;
    
                            // Convert price to integer (remove commas if any)
                            $amount = (int) str_replace(',', '', $price);
                            
                            // Apply percentage according to the package table
                            if ($amount >= 12000 && $amount <= 80000) {
                            $percentage = 10;
                        }
                            elseif ($amount >= 81000 && $amount <= 100000) { // 1 lakh
                            $percentage = 8;
                        }
                            elseif ($amount >= 101000 && $amount <= 300000) {
                            $percentage = 7;
                        }
                            elseif ($amount >= 301000 && $amount <= 500000) {
                            $percentage = 5.5;
                        }
                            elseif ($amount >= 501000 && $amount <= 900000) {
                            $percentage = 4.5;
                        }
                            elseif ($amount >= 901000 && $amount <= 1200000) {
                            $percentage = 3.5;
                        }
                            elseif ($amount > 1201000) {
                            $percentage = 3;
                        }
                            else {
                            $percentage = 0; // Default if outside all ranges
                        }
                            
                            $insert = $pdo->prepare("
                            INSERT INTO tbl_roi_two
                            (user_id, name, package, percentage, lock_day, date, time, status)
                            VALUES
                            (:user_id, :name, :package, :percentage, :lock_day, :date, :time, :status)
                        ");
    
                            $insert->execute([
                            ':user_id'      => $spcode1,
                            ':name'         => $packname,
                            ':package'      => ($price*$percentage)/100,
                            ':percentage'   => 10,
                            ':lock_day'     => 10,
                            ':date'         => date("Y-m-d"),
                            ':time'         => date("H:i:s"),
                            ':status'       => 0
                        ]);
                        
                        }
                    }

                   
                    $pinfinal = $userid;

                    for ($i = 0; $i < 1; $i++) {
                        // get sponsor code by user id
                        $mysponserid = getmysponserid($pinfinal);
                        // get user data code by sponsor id
                        $sponserdetails = getuserdatabysponserid($mysponserid);

                        if ($pinfinal !== "1290") {
                            $spcode1 = $sponserdetails["userid"];
                            $spamont = $sponserdetails["amount"];
                            $isidactive = $sponserdetails["idactive"];
                            $directactive = getmydirectactive($spcode1);

                            if ($isidactive=="1") {
                                if ($i == "0") {
                                    if ($price >= 12000 && $price <= 80000) {
                                    // 12k to 80k
                                    $percentage = 2.5;
                                
                                } elseif ($price >= 81000 && $price <= 400000) {
                                    // 81k to 4 lakhs
                                    $percentage = 2;
                                
                                } elseif ($price >= 400001 && $price <= 800000) {
                                    // 4 lakhs to 8 lakhs
                                    $percentage = 1.8;
                                
                                } elseif ($price >= 800001 && $price <= 1200000) {
                                    // 8 lakhs to 12 lakhs
                                    $percentage = 1.3;
                                
                                } elseif ($price > 1200001) {
                                    // 12 lakhs to 20 lakhs
                                    $percentage = 1;
                                
                                } else {
                                    // fallback if amount doesn't match range
                                    $percentage = 0;
                                }
                                
                                $transactionamount = ($price * $percentage) / 100;
                                }

                                $pinfinal = $spcode1;

                                if (isset($transactionamount)) {
                                    $new = $i + 1;
                                    $level = $new;
                                    $messagenew = "Direct Income of Id ($userid)";
                                    
                                    insert_transction(
                                        "tbl_levelinc",
                                        $spcode1,
                                        $transactionamount,
                                        $messagenew,
                                        $time,
                                        "Credit"
                                    );
                                    updatenonworkwallet($spcode1,$transactionamount,$pdo);
                                }
                            } else {
                                $pinfinal = $spcode1;
                            }
                        }
                    }

                    
                    
                    /* ================================================
                    UPDATE TREE COUNT
                    ================================================ */
                    
                    // $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                    // $stmt->execute([":uid" => $userid]);
                    // $rf = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // if ($rf) {
                    
                    //     $temp_underuserid = $rf["userid"];
                        
                    //     $temp_side_count = $side . 'count';
                    //     $temp_side = $side;
                    
                    //     $total_count = 1;
                    
                    //     while ($total_count > 0) {
                    
                    //         // Fetch current tree record
                    //         $stmt2 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid LIMIT 1");
                    //         $stmt2->execute([":uid" => $temp_underuserid]);
                    //         $r = $stmt2->fetch(PDO::FETCH_ASSOC);
                    
                    //         if (!$r) { break; }
                    
                    //         // Update count
                    //         $current_temp_side_count = $r[$temp_side_count] + 1;
                    
                    //         $stmt3 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = :cnt WHERE userid = :uid");
                    //         $stmt3->execute([
                    //             ":cnt" => $current_temp_side_count,
                    //             ":uid" => $temp_underuserid
                    //         ]);
                    
                    //         // Move to next user up the tree
                    //         if ($temp_underuserid != "") {
                    //             $next_under_userid = getUnderId($pdo,$temp_underuserid);
                    //             $temp_side = getUnderIdPlace($pdo,$temp_underuserid);
                    //             $temp_side_count = $temp_side . "count";
                    //             $temp_underuserid = $next_under_userid;
                    //         }
                    
                    //         if ($temp_underuserid == "") {
                    //             $total_count = 0;
                    //         }
                    //     }
                    // }
                    
                    /* ================================================
                        UPDATE TREE PV (PDO VERSION)
                    ================================================ */
                    
                    // $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                    // $stmt->execute([":uid" => $userid]);
                    // $rf = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // if ($rf) {
                    
                    //     $temp_underuserid = $rf["userid"];
                    //     $temp_side_count = $side . 'pv';
                    //     $temp_side = $side;
                    
                    //     $total_count = 1;
                    
                    //     while ($total_count > 0) {
                    
                    //         // Fetch tree record
                    //         $stmt4 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid LIMIT 1");
                    //         $stmt4->execute([":uid" => $temp_underuserid]);
                    //         $r = $stmt4->fetch(PDO::FETCH_ASSOC);
                    
                    //         if (!$r) { break; }
                    
                    //         // Add PV
                    //         $current_temp_side_count = $r[$temp_side_count] + $price;
                    
                    //         $stmt5 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = :val WHERE userid = :uid");
                    //         $stmt5->execute([
                    //             ":val" => $current_temp_side_count,
                    //             ":uid" => $temp_underuserid
                    //         ]);
                    
                    //         // Move upward
                    //         if ($temp_underuserid != "") {
                    //             $next_under_userid = getUnderId($pdo,$temp_underuserid);
                    //             $temp_side = getUnderIdPlace($pdo,$temp_underuserid);
                    //             $temp_side_count = $temp_side . "pv";
                    //             $temp_underuserid = $next_under_userid;
                    //         }
                    
                    //         if ($temp_underuserid == "") {
                    //             $total_count = 0;
                    //         }
                    //     }
                    // }
                    
                    echo '<script>alert("Thank you for ID ACTIVATION "); window.location.href = "index.php";</script>';
                } else {
                    echo '<script>alert("Please choose amount multiple of 50");</script>';
                }
            } else {
                echo '<script>alert("Your Fund Wallet Low");</script>';
            }
        }
    } else {
        echo '<script>alert("Please Activate Id after 5 AM");</script>';
    }
}

// if ($idactive == 1) {
//     echo '<script>window.location.href = "index.php";</script>';
// }

?>




<body class="bg-theme bg-theme1">

<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>
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
              <div class="card-title text-center">
                <h3 class="tile-title">
                      Buy Package
                      <span class="float-right" style="font-size:14pt;">
                        Fund Balance:  $<?php echo $pin_wallet; ?>
                      </span>
                    </h3>
              </div>
              <hr>
              <div class="row ">
                <div class="col-md-12 p-4">
                  <div class="tile text-center">
                    
                  </div>
                  <div class="tile">
                    <form method="post" id="form-data">
                      
                      <div class="form-group">
                        <a href="fund-request.php">Click here to Send Fund Request</a>
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label">USER Id</label>
                        <input type="text" 
                               name="userid"  
                               class="form-control" 
                               placeholder="Please Enter Account Id" 
                               value="<?php echo $hmpre; ?><?php echo $userid;?>" 
                               readonly>    
                      </div>
                      
                      <!--<div class="form-group">-->
                      <!--  <label for="input-1">Choose Amount -->
                      <!--    <span class="text-danger">-->
                      <!--      (You can make a deposit of at least $50 or any multiples of $50.)-->
                      <!--    </span>-->
                      <!--  </label>-->
                      <!--  <input type="text" -->
                      <!--         min="50" -->
                      <!--         name="price" -->
                      <!--         class="form-control" -->
                      <!--         required>-->
                      <!--</div>-->
                      
                     <div class="form-group">
                        <label for="package">Select Package</label>
                            <select name="package_id" id="package" class="form-control" required>
                                <option value="">-- Select Package --</option>
                                <?php foreach($packages as $pkg): ?>
                                <option value="<?= $pkg['id']; ?>">
                                    <?= htmlspecialchars($pkg['name']); ?> — ₹<?= htmlspecialchars($pkg['price']); ?>
                                </option>
                                    <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="price" class="form-control" 
                            value="<?= isset($_POST['amount']) ? $_POST['amount'] : '' ?>" required
                        >
                    </div>

                      <button type="submit" name="submit" class="btn btn-success text-center">Submit</button>
                      
                    </form>
                  </div>
                </div>
              </div>
              <!-- ✅ End inserted form -->

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
