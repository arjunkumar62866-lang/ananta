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
                        
                        $inv_id = $pdo->lastInsertId();
                        if (function_exists('generateDirectBonusSchedule') && $inv_id) {
                            generateDirectBonusSchedule($inv_id, $userid, $price, $date, $pdo);
                        }
                        
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

<style>
/* =========================================================
   ANANTA FINTECH THEME - PACKAGE BUY REDESIGN
   Matches Dashboard (index.php) & Profile Styling
========================================================= */

html,
body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-user-dashboard.bg-theme,
body.ananta-user-dashboard.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* Remove old legacy dark overlays */
html::before,
html::after,
body::before,
body::after,
#wrapper::before,
#wrapper::after,
.content-wrapper::before,
.content-wrapper::after {
    content: none !important;
    display: none !important;
    background: none !important;
    background-color: transparent !important;
}

#wrapper {
    background: #f4f6f8 !important;
    min-height: 100vh !important;
}

.content-wrapper {
    background-color: #f4f6f8 !important;
    padding-top: 85px !important;
    padding-bottom: 60px !important;
}

/* Header Banner */
.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

/* Main Card Container */
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 24px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
}

/* Form Controls Styling */
label.form-label,
label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control,
input.form-control,
select.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14.5px !important;
    font-weight: 600 !important;
    padding: 10px 16px !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: none !important;
}

.form-control:focus,
input.form-control:focus,
select.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    outline: none !important;
}

/* Submit Button */
.btn-ananta-submit {
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 52px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

.btn-ananta-submit:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.35) !important;
    color: #ffffff !important;
}
</style>

<body class="ananta-user-dashboard">

<!-- loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>
<!-- end loader -->

<!-- Start wrapper-->
<div id="wrapper">

  <div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

      <!-- Header Welcome Banner -->
      <div class="row mb-4">
          <div class="col-12">
              <div class="card income-header-card border-0 p-4">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                      <div class="d-flex align-items-center gap-3">
                          <div class="income-header-icon">
                              <i class="fa fa-shopping-cart"></i>
                          </div>
                          <div>
                              <div class="d-flex align-items-center gap-2 mb-1">
                                  <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">INVESTMENT PURCHASE</span>
                                  <span style="font-size: 12px; color: #64748b; font-weight: 600;">MY ACCOUNT</span>
                              </div>
                              <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                  Buy <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Investment Package</span> 🚀
                              </h4>
                              <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                  Select and activate an investment plan using your fund wallet balance.
                              </p>
                          </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                          <div class="px-3 py-2" style="background: #ffffff; border-radius: 14px; border: 1px solid rgba(2, 132, 199, 0.25); box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                              <span class="text-muted d-block" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Fund Balance</span>
                              <span class="font-weight-bold" style="font-size: 18px; color: #0284c7; font-weight: 800;">$<?php echo number_format((float)($pin_wallet ?? 0), 2); ?></span>
                          </div>
                          <a href="fund-request.php" class="btn btn-outline-success font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                              <i class="fa fa-plus-circle me-1"></i> Add Fund
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="ananta-fintech-card">
            
            <div class="card-header-bar">
                <div class="card-header-title">
                    <h4><i class="fa fa-check-circle text-primary me-2"></i> Package Purchase Details</h4>
                    <p>Select your package and enter the investment amount</p>
                </div>
            </div>

            <div class="p-4 p-md-5">

              <form method="post" id="form-data">
                
                <div class="form-group mb-4">
                  <label>User ID</label>
                  <input type="text" name="userid" class="form-control font-weight-bold" value="<?php echo $hmpre; ?><?php echo $userid;?>" readonly style="height: 48px; background: #f8fafc;">    
                </div>
                
                <div class="form-group mb-4">
                  <label>Select Package</label>
                  <select name="package_id" id="package" class="form-control" required style="height: 48px;">
                      <option value="">-- Select Package --</option>
                      <?php foreach($packages as $pkg): ?>
                      <option value="<?= $pkg['id']; ?>">
                          <?= htmlspecialchars($pkg['name']); ?> — ₹<?= htmlspecialchars($pkg['price']); ?>
                      </option>
                      <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group mb-4">
                  <label>Investment Amount (₹ / $)</label>
                  <input type="number" name="price" class="form-control" placeholder="Enter Amount" value="<?= isset($_POST['amount']) ? $_POST['amount'] : '' ?>" required style="height: 48px;">
                </div>

                <button type="submit" name="submit" class="btn-ananta-submit mt-2">
                  <i class="fa fa-shopping-cart me-1"></i> Buy Investment Now
                </button>
                
              </form>

            </div>
          </div>
        </div>
      </div><!--End Row-->

      <div class="overlay toggle-menu"></div>

    </div>
  </div>

  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
  <?php include 'common/footer.php' ?>

</div><!--End wrapper-->

<script>
    $('document').ready(function(){
       
       $('#price').on("change",function(){
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
