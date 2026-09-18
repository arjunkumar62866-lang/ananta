<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php
include "common/header.php";
include 'common/connection.php'; // contains $pdo

$userid = $_SESSION['userid'];   // FIX as per your system

date_default_timezone_set("Asia/Kolkata");
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$todayObj   = new DateTime();

// =============================
// FETCH USER DATA
// =============================

// =============================
// FUNCTION: CHECK TODAY WITHDRAWAL
// =============================
function GETWITHDREWAL($pdo,$userid){
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) FROM tbl_transaction 
            WHERE user_id=? AND created_date=? AND subject='Investment Withdrawal Request'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userid,$today]);
    return $stmt->fetchColumn();
}

// =============================
// SUBMIT WITHDRAWAL
// =============================
if(isset($_POST['submit'])){

    // if($interval->days < 45){
    //     echo "<script>alert('Withdraw allowed only after 45 days. Completed: {$interval->days} days');</script>";
    //     exit;
    // }

    // SPLITTING amount,stackid FROM SELECT BOX
    list($amount,$stackid) = explode(",", $_POST['amount']);
    

    // if(GETWITHDREWAL($pdo,$userid) != 0){
    //     echo "<script>alert('You already submitted withdrawal today');</script>";
    //     exit;
    // }

    $uniqueId = rand(100000,999999);

    // MARK stack as withdrawn
    $stmt = $pdo->prepare("UPDATE tbl_roi_one SET status='1' WHERE id=?");
    $stmt->execute([$stackid]);

    // Amount after deduction 10%
    $leftamount = $amount - ($amount * 8 / 100);

    // INSERT TRANSACTION
    $sql = "INSERT INTO tbl_transaction 
        (amount, act_amount, user_id, subject, type, status, a_status, created_date, time, api_txn_no)
        VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $leftamount,
        $amount,
        $userid,
        'Investment Withdrawal Request',
        'Debit',
        '1',
        '0',
        $currentDate,
        $currentTime,
        $stackid
    ]);
    
    if($stmt){
        $updateAmount=$pdo->prepare("UPDATE user SET total_package=total_package-:amt WHERE userid=:uid");
        $updateAmount->execute([':amt'=>$amount,':uid'=>$userid]);
    }

    echo "<script>alert('Withdrawal Request Submitted Successfully');</script>";
    echo "<script>window.location.href = 'nonworking1-withdraw1.php';</script>";
    exit();
}

// =============================
// FETCH INVESTMENTS LIST
// =============================
$list = $pdo->prepare("SELECT * FROM tbl_roi_one WHERE user_id=? AND status=0");
$list->execute([$userid]);
$investments = $list->fetchAll(PDO::FETCH_ASSOC);

?>



<body class="bg-theme bg-theme1">

<div id="wrapper">
<div class="content-wrapper">
<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <h3 class="text-center">Investment Withdrawal Request</h3>
                    <hr>

                    <!-- SUCCESS / ERROR MESSAGES -->
                    <?php if(isset($success)) { ?>
                        <div class="alert alert-success text-center"><?php echo $success; ?></div>
                    <?php } ?>

                    <?php if(isset($error)) { ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php } ?>

                    <p class="text-center">Total Investment: <b>₹<?php echo $usertotal_package; ?></b></p>
                    <p class="text-center">[TDS + Admin Charge : <b>5% + 3%]</b></p>

                    <!-- WITHDRAW FORM -->
                    <form method="POST">
                        <div class="form-group">
                            <label>Choose Investment</label>
                            <select name="amount" class="form-control" required>
                                <option value="">--Select--</option>
                                <?php foreach($investments as $inv){ ?>
                                    <option value="<?php echo $inv['package'].",".$inv['id']; ?>">
                                        <?php echo $inv['package']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <button class="btn btn-success btn-block" name="submit">Submit Request</button>
                    </form>

                    <hr>

                </div>
            </div>
        </div>
    </div>

</div></div></div>

<?php include 'common/footer.php'; ?>
</body>
</html>
