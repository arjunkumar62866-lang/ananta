<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $title=$_POST["title"];
    $tr_id=$_POST["tr_id"];
    $order_id=$_POST["order_id"];
    $mode=$_POST["mode"];
    $amount=$_POST["amount"];
    $tr_date=$_POST["tr_date"];
    $remark=$_POST["remark"];
    $subject=$title." ".$userid;
    date_default_timezone_set('Asia/Kolkata');
    
    $time=date('h i:a');
    $date=date('Y-m-d');
    
    $selectCheckTxnId=$pdo->prepare("SELECT * FROM tbl_payment WHERE tr_id=:tr_id");
    $selectCheckTxnId->execute([':tr_id'=>$tr_id]);
    if($selectCheckTxnId->fetch())
    {
        echo "<script>alert('This Transaction Id Is Already Exist');window.location.assign('fund-request.php');</script>"; 
    }
    else
    {
        $insertRequestPayment=$pdo->prepare("INSERT INTO tbl_payment(userid,tr_id,mode,subject,image,amount,remark,plantype,date,time,status)
        VALUES(:userid,:tr_id,:mode,:subject,:image,:amount,:remark,:plantype,:date,:time,:status)");
        $insertRequestPayment->execute([
                ':userid'=>$userid,
                ':tr_id'=>$tr_id,
                ':mode'=>$mode,
                ':subject'=>$subject,
                ':image'=>'',
                ':amount'=>$amount,
                ':remark'=>$remark,
                ':plantype'=>'',
                ':date'=>$date,
                ':time'=>$time,
                ':status'=>0
            ]);
        if($insertRequestPayment)
        {
            echo "<script>alert('Fund Request Generated Successfully Done.');window.location.assign('request-history.php');</script>";
        }
        else
        {
            echo "<script>alert('Fund Request Not Generated Successfully.');</script>";
        }
    }
}
?>

<style>
/* =========================================================
   ANANTA FINTECH THEME - FUND REQUEST REDESIGN
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
select.form-control,
textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    padding: 10px 16px !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: none !important;
}

.form-control:focus,
input.form-control:focus,
select.form-control:focus,
textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    outline: none !important;
}

.form-control[readonly],
.form-control:disabled {
    background-color: #f8fafc !important;
    color: #475569 !important;
    border-color: #e2e8f0 !important;
}

.form-control::placeholder {
    color: #94a3b8 !important;
    font-weight: 500 !important;
}

select.form-control option {
    background-color: #ffffff !important;
    color: #0f172a !important;
    font-weight: 600 !important;
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

/* DataTables Controls Fix */
.dataTables_wrapper,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
}

.dataTables_wrapper .dataTables_length select {
    color: #0f172a !important;
    font-weight: 700 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    background: #ffffff !important;
    outline: none !important;
}

.dataTables_wrapper .dataTables_filter input {
    color: #0f172a !important;
    font-weight: 600 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 7px 14px !important;
    font-size: 13px !important;
    background: #ffffff !important;
    outline: none !important;
}
</style>

<body class="ananta-user-dashboard">

    <!-- Loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <!-- End Loader -->

    <!-- Wrapper -->
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
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">FUND DEPOSIT</span>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 600;">PAYMENT REQUEST</span>
                                        </div>
                                        <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                            Request Fund <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Deposit</span> 💳
                                        </h4>
                                        <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                            Submit transaction details and payment proof for instant wallet credit approval.
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <a href="request-history.php" class="btn btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                                        <i class="fa fa-history me-1"></i> View Request History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card Section -->
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="ananta-fintech-card">
                            <div class="card-header-bar">
                                <div class="card-header-title">
                                    <h4><i class="fa fa-credit-card text-primary me-2"></i> Deposit Request Form</h4>
                                    <p>Fill out the payment details accurately as per your payment receipt</p>
                                </div>
                            </div>

                            <div class="p-4 p-md-5">

                                <form method="POST" enctype="multipart/form-data">
                                    <?php if(isset($_GET['responce'])) { ?>
                                        <div class="alert alert-info border-0 mb-4" style="border-radius: 12px;">
                                            <?php echo ($_GET['responce'] =="SUCCESS") ? "Withdrawal/Fund Request Sent Successfully" : "Something went wrong. Please contact admin!"; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if(isset($error)){ ?>
                                        <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
                                            <?php echo $error; ?>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Payment Request For</label>
                                                <input type="text" name="title" class="form-control" id="price" value="Fund Request" readonly style="height: 48px;">
                                            </div>
                                            
                                            <div class="form-group mb-4">
                                                <label>Transaction ID</label>
                                                <input type="text" name="tr_id" class="form-control" placeholder="Enter Transaction Txn ID" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Order ID</label>
                                                <input type="text" name="order_id" class="form-control" placeholder="Enter Reference Order ID" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Select Mode Of Transaction</label>
                                                <select class="form-control" required="" name="mode" style="height: 48px;">
                                                   <option value="">-- Select Mode --</option>
                                                   <option value="Cash">Cash</option>
                                                   <option value="Google Pay">Google Pay</option>
                                                   <option value="Phone Pay">Phone Pay</option>
                                                   <option value="UPI">UPI</option>
                                                   <option value="Paytm">Paytm</option>
                                                   <option value="IMPS">IMPS</option>
                                                   <option value="NEFT">NEFT</option>
                                                </select>   
                                            </div>
                                        </div>
                                        
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label>Amount ($ / ₹)</label>
                                                <input type="number" step="any" name="amount" class="form-control" placeholder="Enter Amount" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Transaction Date</label>
                                                <input type="date" name="tr_date" class="form-control" required style="height: 48px;">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label>Remark</label>
                                                <textarea name="remark" rows="4" class="form-control" placeholder="Enter any notes or remarks..." required style="min-height: 120px;"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn-ananta-submit mt-3">
                                        <i class="fa fa-paper-plane me-1"></i> Submit Payment Request
                                    </button>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overlay -->
                <div class="overlay toggle-menu"></div>

            </div>
        </div>

        <!-- Back To Top Button -->
        <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

        <!-- Footer -->
        <?php include 'common/footer.php' ?>

    </div>

</body>
</html>