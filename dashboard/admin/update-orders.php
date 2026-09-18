<?php
include('common/connection.php');
include "common/db_method.php";

$userid = $_GET['uid'];
$tid = $_GET['id'];
$title = $_GET['title'];
$price = $_GET['price'];

$userdata = gettransactiondata($tid);
$productdata = getproduct();

if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set("Asia/Kolkata");
}

$date = date('Y-m-d');
$time = date('h:i a');

// Fetch user data with PDO
$stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$rowheader = $stmt->fetch(PDO::FETCH_ASSOC);

$useremail = $rowheader['email'];
$userpass = $rowheader['pass'];
$userstatus = $rowheader['status'];
$username = $rowheader['name'];
$usersponser = $rowheader['sponserid'];
$usersponsername = $rowheader['sponsername'];
$userpackage = $rowheader['package'];
$pv = $userpackage;
$idactive = $rowheader['active'];
$usermobile = $rowheader['mobile'];
$dateofjoining = $rowheader['joining_date'];
$id = $rowheader['id'];
$club = $rowheader['club'];
$upgrade_date = $rowheader['upgrade_date'];
$kyc = $rowheader['kyc'];
$userbv_amount = $rowheader['bv_amount'];
$useramount = $rowheader['amount'];
$user_wallet_amount = $useramount;
$shop_wallet_amount = $rowheader['shop_amount'];
$rank_amount = $rowheader['rank_amount'];
$recharge_wallet = $shop_wallet_amount;
$rank = $rowheader['rank'];
$pool = $rowheader['pool'];
$capping = $rowheader['capping'];
$side = $rowheader['join_side'];
$level = $rowheader['level'];
$my_level = $level;
$onelevel = $rowheader['onelevel'];
$twolevel = $rowheader['twolevel'];
$threelevel = $rowheader['threelevel'];
$fourlevel = $rowheader['fourlevel'];
$fivelevel = $rowheader['fivelevel'];
$one_club_status = $rowheader['one_club_status'];
$two_club_status = $rowheader['two_club_status'];
$three_club_status = $rowheader['three_club_status'];
$four_club_status = $rowheader['four_club_status'];
$five_club_status = $rowheader['five_club_status'];
$atime = $rowheader['atime'];

// Home settings
$homeset = getHomeSettings($pdo);
$hmmobile = $homeset['mobile'];
$hmemail = $homeset['email'];
$hmaddress = $homeset['address'];
$hmtitle = $homeset['title'];
$hmurl = $homeset['url'];
$hmpackage = $homeset['package'];
$hmwebsite = $homeset['website'];
$hmcapping = $homeset['capping'];
$hmpackage_bv = $homeset['package_bv'];
$hmpre = $homeset['pre'];
$hmbitly = $homeset['bitly'];
$hmemailfrom = $homeset['emailfrom'];
$hmbg = $homeset['background'];
$hmlogo = $homeset['logo'];
$hmfavicon = $homeset['favicon'];
$hmcolor = $homeset['color'];
$hmmatching_amount = $homeset['matching_amount'];
$self_repurpercent = $homeset['self_repurpercent'];
$teammatching_repurpercent = $homeset['teammatching_repurpercent'];

// Handle reject
if ($title == "Reject") {
    $stmt = $pdo->prepare("UPDATE tbl_order SET ac_status = 2 WHERE tr_id = :tid");
    $res = $stmt->execute(['tid' => $tid]);

    if ($res) {
        echo "<script>alert('Reject !! Your order rejected successfully');window.location.assign('recent-orders.php');</script>";
    } else {
        echo "<script>alert('Sorry !! Your order rejection failed');window.location.assign('recent-orders.php');</script>";
    }
}

// Handle approved
elseif ($title == "Approved") {

    if ($idactive == 0) {
        // Fetch cart items
        $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
        $stmt->execute(['userid' => $userid]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cartItems as $cartDt) {
            $cartProId = $cartDt['product_id'];
            $cartProQty = $cartDt['qty'];

            // Fetch product details
            $stmt1 = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id AND status = 1");
            $stmt1->execute(['id' => $cartProId]);
            $row = $stmt1->fetch(PDO::FETCH_ASSOC);

            $pro_title = $row['title'];
            $mrp_cost = $row['mrp_cost'];
            $mrp_total = $row['mrp_total'];
            $pro_code = $row['code'];
            $pro_gst = $row['gst_price'];
            $pro_rate = $row['mrp_taxrate'];
            $sp_price = $row['sp_price'];
            $bv_price = $row['bv_price'];
            $dp_cost = $row['dp_price'];
            $pv = $row['pv'];
            
            // // update the user pv ammount
            
            // $stmt3 = $pdo->prepare("UPDATE user SET pv_amount = pv_amount + :pv_amount WHERE userid = :userid");
            // $stmt3->execute(['pv_amount' => $dp_cost, 'userid' => $userid]);

            // Insert into transaction details
            $stmt2 = $pdo->prepare("INSERT INTO tbl_transaction_details (tr_id, pro_id, pro_code, pro_name, pro_qty, pro_price, pro_dp_price, pro_gst, gst_rate) 
                                    VALUES (:tr_id, :pro_id, :pro_code, :pro_name, :pro_qty, :pro_price, :pro_dp_price, :pro_gst, :gst_rate)");
            $stmt2->execute([
                'tr_id' => $tid,
                'pro_id' => $cartProId,
                'pro_code' => $pro_code,
                'pro_name' => $pro_title,
                'pro_qty' => $cartProQty,
                'pro_price' => $dp_cost,
                'pro_dp_price' => $mrp_total,
                'pro_gst' => $pro_gst,
                'gst_rate' => $pro_rate
            ]);
        }

        // Delete cart items
        $stmtDel = $pdo->prepare("DELETE FROM tbl_cart WHERE userid = :userid");
        $stmtDel->execute(['userid' => $userid]);

        // Update user wallet & purchase
        $stmtUpd = $pdo->prepare("UPDATE user SET 
                                    bv_amount = bv_amount + :bvtotal, 
                                    one_club_status = 1, 
                                    total_purchase = total_purchase + :finalPay, 
                                    total_bv = total_bv + :bvtotal, 
                                    pv_amount = pv_amount + :pv 
                                  WHERE userid = :userid");

        $stmtUpd->execute([
            'bvtotal' => $bvtotalPay ?? 0,
            'finalPay' => $finalPay ?? 0,
            'pv' => $pv ?? 0,
            'userid' => $userid
        ]);

        // Update order status
        $stmtOrder = $pdo->prepare("UPDATE tbl_order SET ac_status = 1 WHERE tr_id = :tid");
        $resOrder = $stmtOrder->execute(['tid' => $tid]);

        if ($resOrder) {
            echo "<script>alert('Your Order Placed Successfully! Thank you');window.location.assign('approved_orders');</script>";
        } else {
            echo "<script>alert('Sorry !! Order approval failed');window.location.assign('recent-orders.php');</script>";
        }
    }

    // If idactive != 0 (user already active)
    else {
        // Directly approve the order
        
        $stmtOrder = $pdo->prepare("UPDATE tbl_order SET ac_status = 1 WHERE tr_id = :tid");
        $resOrder = $stmtOrder->execute(['tid' => $tid]);

        if ($resOrder) {
            echo "<script>alert('Approved !! Your order Approved successfully');window.location.assign('recent-orders.php');</script>";
        } else {
            echo "<script>alert('Sorry !! Your order approval failed');window.location.assign('recent-orders.php');</script>";
        }
    }
}
?>
