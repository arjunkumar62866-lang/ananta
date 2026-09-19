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

$stmt = $pdo->prepare("SELECT * FROM tbl_package ORDER BY id ASC");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["submit"])) {
    
    if($day=="31"){
        echo("<script>alert('On 31 day Registration is not allowed.'); window.location='index.php'; </script>");
        exit();
    }
    
    $userid2 = trim($_POST['userid']);
    $activate_userid = substr($userid2, 2);
    
    $idactive=getuserdatabysponserid($activate_userid);
    $side1 = $idactive['join_side'];
    if ($idactive['idactive'] == "1") {
        echo '<script>alert("ID is already active.");window.location.href = "index.php";</script>';
        exit;
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
                $error = "Amount must be between ₹12000 and ₹80000 for package '$pkg_name'";
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
            if ($amount < 1200000 ) {
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
                    
                    // update pin_wallet of loggedin user
                    $stmt1 = $pdo->prepare("
                    UPDATE user 
                    SET pin_wallet = pin_wallet - :price,
                    upgrade_date = :date,
                    atime = :time
                    WHERE userid = :userid
                    ");
                    $stmt1->execute([
                        ":price" => $price,
                        ":date" => $date,
                        ":time" => $time,
                        ":userid" => $userid,
                    ]);
                    
                    // update targated user
                    $stmt = $pdo->prepare("
                    UPDATE user 
                    SET inc_limit = :inc_limitpackage,
                    package = :price,
                    total_package = total_package + :price,
                    plan = :packname,
                    active = '1',
                    upgrade_date = :date,
                    atime = :time
                    
                    WHERE userid = :userid
                    ");
                    $stmt->execute([
                        ":inc_limitpackage" => $inc_limitpackage,
                        ":price" => $price,
                        ":packname" => $packname,
                        ":date" => $date,
                        ":time" => $time,
                        ":userid" => $activate_userid,
                    ]);

                    
                    $subject1 = "Id Activation Using Fund- $price";
                    $sql1 = "INSERT INTO tbl_transaction (user_id, amount, type, subject, time, created_date, status) VALUES 
                            (:userid, :price, 'Credit', :subject1, :time, :date, '1')";
                    $stmt1 = $pdo->prepare($sql1);
                    if (
                        $stmt1->execute([
                            ":userid" => $activate_userid,
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
                            ":userid" => $activate_userid,
                            ':name' => $packname,
                            ":price" => $price,
                            ":percentage" => $percentage,
                            ":date" => $date,
                            ":time" => $time,
                            // ":days" => $days,
                            ":lockdays" => $lockdays,
                            ":inc_limitpackage" => $inc_limitpackage,
                        ]);
                        
                        $pinfinal = $activate_userid;
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
                            elseif ($amount > 1201000 ) {
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

                   
                    $pinfinal = $activate_userid;

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
                                    $messagenew = "Direct Income of Id ($activate_userid)";
                                    
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

                   
                    
                    // /* ================================================
                    // UPDATE TREE COUNT
                    // ================================================ */
                    
                    // $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                    // $stmt->execute([":uid" => $activate_userid]);
                    // $rf = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // if ($rf) {
                    
                    //     $temp_underuserid = $rf["userid"];
                        
                    //     $temp_side_count = $side1 . 'count';
                    //     $temp_side = $side1;
                    
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
                    
                    // /* ================================================
                    //     UPDATE TREE PV (PDO VERSION)
                    // ================================================ */
                    
                    // $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                    // $stmt->execute([":uid" => $activate_userid]);
                    // $rf = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // if ($rf) {
                    
                    //     $temp_underuserid = $rf["userid"];
                    //     $temp_side_count = $side1 . 'pv';
                    //     $temp_side = $side1;
                    
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

if ($idactive == 0) {
    echo '<script>alert("First activate self...");window.location.href = "index.php";</script>';
}

?>


<body class="bg-theme bg-theme1">

<div id="wrapper" class="ananta-user-dashboard">

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-8 offset-lg-2">
                <div class="card shadow-lg border-0" style="border-radius: 20px; background: #ffffff;">
                    <div class="card-body p-4 p-md-5">

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                            <div>
                                <h4 class="font-weight-bold text-dark mb-1" style="color: #0f172a;">Activate Other User ID</h4>
                                <p class="text-muted small mb-0">Activate team member IDs directly using your fund wallet</p>
                            </div>
                            <div class="mt-3 mt-md-0 px-3 py-2" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%); border-radius: 12px; border: 1px solid rgba(2, 132, 199, 0.2);">
                                <span class="text-muted small font-weight-bold d-block">Fund Balance</span>
                                <span class="h5 font-weight-bold mb-0" style="color: #0284c7;">$<?php echo number_format((float)$pin_wallet, 2); ?></span>
                            </div>
                        </div>

                        <form method="post">

                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569; letter-spacing: 0.5px;">Target User ID</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-radius: 12px 0 0 12px; border-color: #cbd5e1;"><i class="zmdi zmdi-account text-primary"></i></span>
                                    </div>
                                    <input type="text" name="userid" class="form-control border-left-0" id="referrerId" required placeholder="Enter User ID (e.g. AN1290)" style="border-radius: 0 12px 12px 0; border-color: #cbd5e1; height: 48px;">
                                </div>
                            </div>
                            
                            <div class="form-group mb-4" id="sponsor_name" style="display: none;">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569; letter-spacing: 0.5px;">Member Name</label>
                                <div class="position-relative">
                                    <input type="text" name="refferalid" id="response2" class="form-control font-weight-bold" readonly style="border-radius: 12px; border-color: #22c55e; background-color: #f0fdf4; color: #166534; height: 48px;">
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569; letter-spacing: 0.5px;">Select Package</label>
                                <select name="package_id" class="form-control" required style="border-radius: 12px; border-color: #cbd5e1; height: 48px;">
                                    <option value="">-- Select Package --</option>
                                    <?php foreach ($packages as $pkg): ?>
                                        <option value="<?= $pkg['id'] ?>">
                                            <?= $pkg['name'] ?> — ₹<?= number_format((float)$pkg['price'], 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569; letter-spacing: 0.5px;">Amount ($)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-radius: 12px 0 0 12px; border-color: #cbd5e1;"><i class="zmdi zmdi-money text-success"></i></span>
                                    </div>
                                    <input type="number" name="price" class="form-control border-left-0" required placeholder="Enter Amount" style="border-radius: 0 12px 12px 0; border-color: #cbd5e1; height: 48px;">
                                </div>
                            </div>

                            <button type="submit" id="submitBtn" name="submit" class="btn btn-block font-weight-bold text-white shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); border: none; height: 50px; font-size: 16px;">
                                <i class="zmdi zmdi-account-add me-1"></i> Activate Account Now
                            </button>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</div>
 <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

  <!-- Ajax for auto-matic Name fetching -->
<script>
$(document).ready(function () {

    $('#referrerId').on('blur', function () {
        let refId = $(this).val().trim();

        if (refId.length <= 2) {
            $("#sponsor_name").hide();
            $("#response2").val("");
            return;
        }

        $.ajax({
            type: "POST",
            url: "checkName.php",
            data: { data: refId },
            success: function (response) {
                if (response != "0") {
                    $("#response2").val(response);
                    $("#sponsor_name").slideDown();
                    $("#submitBtn").prop("disabled", false);
                } else {
                    $("#sponsor_name").hide();
                    $("#response2").val("");
                    $("#submitBtn").prop("disabled", true);
                    alert("Invalid or inactive user ID!");
                }
            }
        });
    });

});
</script>

</body>
</html>
