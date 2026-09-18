<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php 
include("common/header.php"); 

$day = date('d');
$currentTime = date("H:i:s");

$percenset = getpercentage($percenset);
$level1=$percenset['level1'];
$level2=$percenset['level2'];
$level3=$percenset['level3'];
$level4=$percenset['level4'];
$level5=$percenset['level5'];
$level6=$percenset['level6'];
$level7=$percenset['level7'];

if($_SERVER["REQUEST_METHOD"] == "POST") {
// if($currentTime >= "05:00" && $currentTime <= "23:60")
    if(1==1) 
    {
        $price = $_POST['price']; 
        $activateuserid = substr($_POST['userid'], 2); // strip first 2 chars

        if($activateuserid != $userid) {

            
            $stmt = $pdo->prepare("SELECT * FROM tbl_package WHERE price = :price");
            $stmt->execute([':price' => $price]);
            $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$rowheader){
                echo "<script>alert('Package not found');</script>";
                exit;
            }

            $percentage   = $rowheader['income'];
            $packname     = $rowheader['pack'];
            $boosterincome= $rowheader['inc_limit'];
            $totalincome  = $rowheader['totalincome'];  
            $days         = $rowheader['days']; 
            $lockdays     = $rowheader['lock']; 

            // fetch user info
            $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :uid");
            $stmt->execute([':uid' => $activateuserid]);
            $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

            $cur_actives = $row1['active'];

            if($cur_actives == '0') {

                if($pin_wallet >= $price) {

                    if($pin_wallet > 0) {

                        if($price % 50 == 0) {

                            $inc_limitpackage = (int)2 * (int)$price;

                            // deduct from pin_wallet
                            $stmt = $pdo->prepare("UPDATE user SET pin_wallet = pin_wallet - :price WHERE userid = :userid");
                            $stmt->execute([':price' => $price, ':userid' => $userid]);

                            // update activation user
                            $stmt = $pdo->prepare("UPDATE user 
                                SET inc_limit = :inc_limit, count='365', plan = :plan, package = :package, 
                                    total_package = total_package + :package, active='1', upgrade_date=:udate, 
                                    activate_datetime=:udate, total_package=:package 
                                WHERE userid = :uid");
                            $stmt->execute([
                                ':inc_limit' => $inc_limitpackage,
                                ':plan'      => $packname,
                                ':package'   => $price,
                                ':udate'     => $date,
                                ':uid'       => $activateuserid
                            ]);

                            // insert transaction
                            $subject1 = "Id Activation Using Fund - $price";
                            $stmt = $pdo->prepare("INSERT INTO tbl_transaction (user_id,type,subject,time,created_date,status)
                                VALUES (:uid,'Credit',:subj,:time,:cdate,'1')");
                            $stmt->execute([
                                ':uid'   => $userid,
                                ':subj'  => $subject1,
                                ':time'  => $time,
                                ':cdate' => $date
                            ]);

                            // insert ROI
                            $stmt = $pdo->prepare("INSERT INTO tbl_roi_one 
                                (user_id,level,package,amount,percentage,date,time,closingdate,status,count,lock_day,capping)
                                VALUES (:uid,'1',:package,'0',:perc,:date,:time,:day,'0',:days,:lockdays,:cap)");
                            $stmt->execute([
                                ':uid'      => $activateuserid,
                                ':package'  => $price,
                                ':perc'     => $percentage,
                                ':date'     => $date,
                                ':time'     => $time,
                                ':day'      => $day,
                                ':days'     => $days,
                                ':lockdays' => $lockdays,
                                ':cap'      => $inc_limitpackage
                            ]);

                            // sponsor income distribution (shortened for example)
                            $pinfinal = $activateuserid;
                            for($i=0;$i<1;$i++){
                                $mysponserid = getmysponserid($pinfinal);
                                $sponserdetails = getuserdatabysponserid($mysponserid);

                                if($pinfinal !== '1290'){
                                    $spcode1 = $sponserdetails['userid'];
                                    $spamont = $sponserdetails['amount'];

                                    $transactionamount = $price * 5 / 100;
                                    updatenonworkwallet($spcode1,$transactionamount);

                                    $pinfinal = $spcode1;

                                    if(isset($transactionamount)){
                                        $new=$i+1;
                                        $level=$new;
                                        $messagenew="Direct Income of Id ($activateuserid)";
                                        insert_transction('tbl_levelinc',$spcode1,$transactionamount,$messagenew,$time,'Credit');
                                    }
                                }
                            }

                            echo "<script>alert('Your Id Activated Successfully');window.location.assign('user_activatefund');</script>";
                            exit;
                        } else {
                            echo "<script>alert('Please choose amount multiple of 50');</script>";
                        }

                    } else {
                        echo "<script>alert('Your Fund Wallet is Low');</script>";
                    }

                } else {
                    echo "<script>alert('Your Fund Wallet is Low');</script>";
                }

            } else {
                echo "<script>alert('Your Id Activated Already');window.location.assign('user_activatefund');</script>";
            }

        } else {
            echo "<script>alert('You cannot activate your own ID from here');window.location.assign('user_activatefund');</script>";
        }
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
						    'p_id':$('#sponserid').val()
					       
                       },
                       dataType: "JSON",
                       success: function (jsonStr) {
                          
						  $('#tst_sponsername').text(jsonStr.name);
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
                        <div class="card-title text-center"><h3>Other User ID Activate</h3></div>
                        <hr>
                        <h6>  Fund Wallet:<?php echo $pin_wallet; ?></h6>
                        <form method="post" id="form-data">
                    <!-- USER ID -->
                        <div class="form-group">
                        <label class="form-label">USER ID</label>
                        <input type="text" name="userid" id="sponserid" 
                            class="form-control" 
                            placeholder="Enter User ID" 
                            onblur="getfunctionFees();" required
                        >
                        <p id="tst_sponsername"></p>
                    </div>

                    <!-- PRICE -->
                    <div class="form-group">
                        <label class="form-label">
                            Choose Amount 
                            <span class="text-danger">(You can make a deposit of at least $50 or any multiples of $50.)</span>
                        </label>
                        <input type="number" min="50" step="50" 
                            name="price" 
                            class="form-control" 
                            placeholder="Enter Amount" 
                            required
                        >
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

	
</body>

<!-- Mirrored from themewagon.github.io/dashtreme/forms.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:55 GMT -->
</html>
