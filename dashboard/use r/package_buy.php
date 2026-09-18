<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';

// Fetch ALL packages
$stmt = $pdo->prepare("SELECT * FROM tbl_package ORDER BY id ASC");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $package_id = $_POST['package_id'];
    $amount     = $_POST['amount'];

    // Fetch roi percentage(generation income percentage)
    $stmt3 = $pdo->prepare("SELECT percentage FROM tbl_roipercentage");
    $stmt3->execute();
    $roi_percentage = $stmt3->fetch(PDO::FETCH_ASSOC);
    
    // Fetch selected package
    $stmt2 = $pdo->prepare("SELECT * FROM tbl_package WHERE id = ?");
    $stmt2->execute([$package_id]);
    $pkg = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($pkg) {

        $pkg_name = $pkg['name'];

        // ----------------- VALIDATION ------------------
        $minMax = [
            1 => [12000, 80000],
            2 => [81000, 400000],
            3 => [400000, 800000],
            4 => [800000, 1200000],
            5 => [1200000, 2000000],
        ];

        if (isset($minMax[$pkg['id']])) {

            $min = $minMax[$pkg['id']][0];
            $max = $minMax[$pkg['id']][1];

            if ($amount < $min || $amount > $max) {
                $error = "Amount must be between ₹$min and ₹$max for package '$pkg_name'.";
            }
        }

        // ----------------- IF VALID, INSERT ------------------
        if ($pin_wallet >= $amount){
            if (empty($error)) {
                $inc_limitpackage = (int) 2 * (int) $amount;
                $stmt = $pdo->prepare("
                UPDATE user 
                SET inc_limit = inc_limit + :inc_limitpackage,
                package = :price,
                total_package = total_package + :price,
                
                plan = :packname,
                active = '1',
                upgrade_date = :date,
                atime = :time,
                pin_wallet = pin_wallet - :price
                WHERE userid = :userid
                ");
                $stmt->execute([
                    ":inc_limitpackage" => $inc_limitpackage,
                    ":price" => $amount,
                    ":packname" => $pkg_name,
                    ":date" => $date,
                    ":time" => $time,
                    ":userid" => $userid,
                ]);
                $success = "✔ Amount accepted successfully!";
    
                // INSERT INTO tbl_roi_one
                $insert = $pdo->prepare("
                    INSERT INTO tbl_roi_one
                    (user_id, name, package, percentage, capping, lock_day, date, time, status)
                    VALUES
                    (:user_id, :name, :package, :percentage, :capping, :lock_day, :date, :time, :status)
                ");
    
                $insert->execute([
                    ':user_id'      => $userid, 
                    ':name'         => $pkg['name'],
                    ':package'      => $amount,
                    // ':percentage'   => $pkg['income'],
                    ':percentage'   => $roi_percentage,
                    ':capping'      => $amount*2,
                    ':lock_day'     => $pkg['days'],
                    ':date'         => date("Y-m-d"),
                    ':time'         => date("H:i:s"),
                    ':status'       => 0
                ]);
                
                $pinfinal = $userid;
                // get sponsor code by user id
                $mysponserid = getmysponserid($pinfinal);
                
                // get user details of sponsor
                $sponserdetails = getuserdatabysponserid($mysponserid);
                $spcode1        = $sponserdetails["userid"];   // sponsor ID
                
                // INSERT INTO tbl_roi_two
                $insert = $pdo->prepare("
                    INSERT INTO tbl_roi_two
                    (user_id, name, package, percentage, lock_day, date, time, status)
                    VALUES
                    (:user_id, :name, :package, :percentage, :lock_day, :date, :time, :status)
                ");
    
                $insert->execute([
                    ':user_id'      => $spcode1,
                    ':name'         => $pkg['name'],
                    ':package'      => $amount,
                    ':percentage'   => 10,
                    ':lock_day'     => 10,
                    ':date'         => date("Y-m-d"),
                    ':time'         => date("H:i:s"),
                    ':status'       => 0
                ]);
                
                
                /* ======================================================
                START — LEVEL INCOME (DIRECT 2.5%)
                ====================================================== */
                
                $pinfinal = $userid;  // start from buyer ID
                
                for ($i = 0; $i < 1; $i++) {   // currently only Level 1
                
                    // get sponsor code by user id
                    $mysponserid = getmysponserid($pinfinal);
                
                    // get user details of sponsor
                    $sponserdetails = getuserdatabysponserid($mysponserid);
                
                    if ($pinfinal !== "1290" && !empty($sponserdetails)) {
                
                        $spcode1        = $sponserdetails["userid"];   // sponsor ID
                        $isidactive     = $sponserdetails["idactive"]; // active status
                
                        if ($isidactive == 1) {
                
                            // Level 1 income = 2.5%
                            if ($i == 0) {
                                $transactionamount = ($amount * 2.5) / 100;
                            }
                
                            $pinfinal = $spcode1;
                
                            if (isset($transactionamount)) {
                
                                $level = $i + 1;
                                $messagenew = "Level $level Income from ID ($userid)";
                
                                insert_transction(
                                    "tbl_levelinc",
                                    $spcode1,
                                    $transactionamount,
                                    $messagenew,
                                    $time,
                                    "Credit"
                                );
                            }
                        } else {
                            // even if inactive, move upline
                            $pinfinal = $spcode1;
                        }
                    }
                }
                
                /* ======================================================
                    END — LEVEL INCOME
                ====================================================== */
                
    
    
    
                /* ======================================================
                    START — UPDATE TREE COUNT (BUY PACKAGE)
                ====================================================== */
    
                // Get sponsor upline from tree
                $stmt = $pdo->prepare("SELECT * FROM tree 
                    WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                $stmt->execute([":uid" => $userid]);
                $rf = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Fetch side (left/right)
                $side = "";
                if ($rf) {
                    if ($rf['left_id'] == $userid) $side = "left";
                    if ($rf['right_id'] == $userid) $side = "right";
                }
    
                if ($rf && $side != "") {
    
                    $temp_underuserid = $rf["userid"];
                    $temp_side_count = $side . 'count';
                    $temp_side = $side;
    
                    $total_count = 1;
    
                    // while ($total_count > 0) {
    
                    //     $stmt2 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid LIMIT 1");
                    //     $stmt2->execute([":uid" => $temp_underuserid]);
                    //     $r = $stmt2->fetch(PDO::FETCH_ASSOC);
    
                    //     if (!$r) { break; }
    
                    //     $current_temp_side_count = $r[$temp_side_count] + 1;
    
                    //     $stmt3 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = :cnt WHERE userid = :uid");
                    //     $stmt3->execute([
                    //         ":cnt" => $current_temp_side_count,
                    //         ":uid" => $temp_underuserid
                    //     ]);
    
                    //     $next_under_userid = getUnderId($pdo, $temp_underuserid);
                    //     $temp_side = getUnderIdPlace($pdo, $temp_underuserid);
                    //     $temp_side_count = $temp_side . "count";
                    //     $temp_underuserid = $next_under_userid;
    
                    //     if ($temp_underuserid == "") {
                    //         $total_count = 0;
                    //     }
                    // }
                }
    
                /* ======================================================
                    END — UPDATE TREE COUNT
                ====================================================== */
    
    
    
                /* ======================================================
                    START — UPDATE TREE PV (BUY PACKAGE)
                ====================================================== */
    
                $stmt = $pdo->prepare("SELECT * FROM tree 
                    WHERE `left_id` = :uid OR `right_id` = :uid LIMIT 1");
                $stmt->execute([":uid" => $userid]);
                $rf = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($rf && $side != "") {
    
                    $temp_underuserid = $rf["userid"];
                    $temp_side_count = $side . 'pv';
                    $temp_side = $side;
    
                    $total_count = 1;
    
                    while ($total_count > 0) {
    
                        $stmt4 = $pdo->prepare("SELECT * FROM tree WHERE userid = :uid LIMIT 1");
                        $stmt4->execute([":uid" => $temp_underuserid]);
                        $r = $stmt4->fetch(PDO::FETCH_ASSOC);
    
                        if (!$r) { break; }
    
                        $current_temp_side_count = $r[$temp_side_count] + $amount;
    
                        $stmt5 = $pdo->prepare("UPDATE tree SET `$temp_side_count` = :val WHERE userid = :uid");
                        $stmt5->execute([
                            ":val" => $current_temp_side_count,
                            ":uid" => $temp_underuserid
                        ]);
    
                        $next_under_userid = getUnderId($pdo, $temp_underuserid);
                        $temp_side = getUnderIdPlace($pdo, $temp_underuserid);
                        $temp_side_count = $temp_side . "pv";
                        $temp_underuserid = $next_under_userid;
    
                        if ($temp_underuserid == "") {
                            $total_count = 0;
                        }
                    }
                }
    
                /* ======================================================
                    END — UPDATE TREE PV
                ====================================================== */
                
                echo '<script>window.location="package_buy.php";</script>';
                }
            } else {
            echo '<script>alert("Your Fund Wallet is Low");</script>';
        }
    }
}
?>

<body class="bg-theme bg-theme1">

<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner">
            <div class="loader"></div>
        </div>
    </div>
</div>

<div id="wrapper">

<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center">
                            <h3 class="tile-title">
                            Buy package
                            <span class="float-right" style="font-size:14pt;">
                                Fund Balance:  $<?php echo $pin_wallet; ?>
                            </span>
                        </h3>
                        </div>
                        <hr>

                        <form action="" method="post">

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
                                <input type="number" name="amount" class="form-control" 
                                    value="<?= isset($_POST['amount']) ? $_POST['amount'] : '' ?>" required>
                            </div>

                            <?php if($error): ?>
                                <div class="alert alert-danger mt-2"><?= $error ?></div>
                            <?php endif; ?>

                            <?php if($success): ?>
                                <div class="alert alert-success mt-2"><?= $success ?></div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary mt-3">Submit</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="overlay toggle-menu"></div>

    </div>
</div>

<a href="javascript:void(0);" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
</a>

<?php include 'common/footer.php' ?>

</div>

</body>
</html>
