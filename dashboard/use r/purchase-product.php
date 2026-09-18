<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>


<body class="bg-theme bg-theme1">

<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner"><div class="loader"></div></div></div></div>
<!-- end loader -->

<!-- Start wrapper-->
<div id="wrapper">

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3>Place an Order</h3></div>
                        <hr>

                        <?php
                        // Check if cart has products
                        $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
                        $stmt->execute(['userid' => $userid]);
                        if ($stmt->rowCount() == 0) {
                            echo "<script>alert('No products in your cart');window.location.assign('cart.php');</script>";
                            exit;
                        }

                        if (isset($_POST['submit'])) {
                            $tr_code = rand(10000000000, 99999999);
                            $name = $_POST['name'];
                            $email = $_POST['email'];
                            $number = $_POST['number'];
                            $side_bonus = $_POST['side'] ?? 'left';
                            $address = $_POST['address'];
                            $city = $_POST['city'];
                            $state = $_POST['state'];
                            $pincode = $_POST['pincode'];
                            $franchiseeid = $_POST['franchiseeid'];
                            $mode = $_POST['mode'] ?? '';
                            $tr_id = '';
                            $tr_date = '';
                            $date = date('Y-m-d');
                            $time = date('H:i:s');

                            // Joining logic
                            if ($idactive == 0) {
                                // Calculate total payment
                                $totalPay = 0;
                                $sptotalPay = 0;
                                $bvtotalPay = 0;
                                $pv = 0;

                                $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
                                $stmt->execute(['userid' => $userid]);
                                while ($cartDt = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $cartProId = $cartDt['product_id'];
                                    $cartProQty = $cartDt['qty'];

                                    $stmt1 = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id AND status = '1'");
                                    $stmt1->execute(['id' => $cartProId]);
                                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                                    if ($row) {
                                        $mrp_totale = (int)$cartProQty * (int)$row['mrp_total'];
                                        $pv += (int)$row['pv'];
                                        $totalPay += $mrp_totale;
                                        $sp_total = (int)$cartProQty * (int)$row['sp_price'];
                                        $sptotalPay += $sp_total;
                                        $bv_total = (int)$cartProQty * (int)$row['pv'];
                                        $bvtotalPay += $bv_total;
                                        echo "bvvvvvvvvvvvvvvvvvvvvvvvvvvvv ".$bvtotalPay;
                                    }
                                }

                                $finalPay = $totalPay;

                                // Check wallet balance (simplified for clarity)
                                if (true) { // Replace with actual wallet check if needed
                                    $purchasetype = "Joining";
                                    try {
                                        

                                        // Insert into tbl_order
                                        $ins_query = "INSERT INTO tbl_order (tr_id, amount, userid, name, email, mobile, address, city, state, pincode, ac_status, status, franchiseeid, date, time, type)
                                                      VALUES (:tr_id, :amount, :userid, :name, :email, :mobile, :address, :city, :state, :pincode, '0', '1', :franchiseeid, :date, :time, :type)";
                                        $stmt = $pdo->prepare($ins_query);
                                        $stmt->execute([
                                            'tr_id' => $tr_code,
                                            'amount' => $totalPay,
                                            'userid' => $userid,
                                            'name' => $name,
                                            'email' => $email,
                                            'mobile' => $number,
                                            'address' => $address,
                                            'city' => $city,
                                            'state' => $state,
                                            'pincode' => $pincode,
                                            'franchiseeid' => $franchiseeid,
                                            'date' => $date,
                                            'time' => $time,
                                            'type' => $purchasetype
                                        ]);

                                        // Process cart items
                                        $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
                                        $stmt->execute(['userid' => $userid]);
                                        while ($cartDt = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $cartProId = $cartDt['product_id'];
                                            $cartProQty = $cartDt['qty'];

                                            $stmt1 = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id AND status = '1'");
                                            $stmt1->execute(['id' => $cartProId]);
                                            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                                            if ($row) {
                                                $pro_title = $row['title'];
                                                $mrp_cost = $row['mrp_cost'];
                                                $mrp_total = $row['mrp_total'];
                                                $pro_code = $row['code'];
                                                $pro_gst = $row['gst_price'];
                                                $pro_rate = $row['gst'];
                                                $sp_price = $row['sp_price'];
                                                $bv_price = $row['bv_price'];
                                                $dp_cost = $row['dp_price'];
                                                $pv = $row['pv'];

                                                // Insert into tbl_transaction_details
                                                $ins_details = "INSERT INTO tbl_transaction_details (tr_id, pro_id, pro_code, pro_name, pro_qty, pro_price, pro_dp_price, pro_gst, gst_rate,date)
                                                                VALUES (:tr_id, :pro_id, :pro_code, :pro_name, :pro_qty, :pro_price, :pro_dp_price, :pro_gst, :gst_rate, :date)";
                                                $stmt2 = $pdo->prepare($ins_details);
                                                $stmt2->execute([
                                                    'tr_id' => $tr_code,
                                                    'pro_id' => $cartProId,
                                                    'pro_code' => $pro_code,
                                                    'pro_name' => $pro_title,
                                                    'pro_qty' => $cartProQty,
                                                    'pro_price' => $dp_cost,
                                                    'pro_dp_price' => $mrp_total,
                                                    'pro_gst' => $pro_gst,
                                                    'gst_rate' => $pro_rate,
                                                    'date' => $date
                                                ]);
                                            }
                                        }

                                        // Delete from cart
                                        $stmt = $pdo->prepare("DELETE FROM tbl_cart WHERE userid = :userid");
                                        $stmt->execute(['userid' => $userid]);

                                        // Update user wallet
                                        $update_user = "UPDATE user SET bv_amount = bv_amount + :bvtotalPay, one_club_status = '1', total_purchase = total_purchase + :finalPay, total_bv = total_bv + :bvtotalPay, pv_amount = pv_amount + :pv WHERE userid = :userid";
                                        $stmt = $pdo->prepare($update_user);
                                        $stmt->execute([
                                            'bvtotalPay' => $bvtotalPay,
                                            'finalPay' => $finalPay,
                                            'pv' => $pv,
                                            'userid' => $userid
                                        ]);

                                        
                                        // echo "<script>alert('Your Order Placed Successfully! Thank you');window.location.assign('all-order');</script>";
                                    } catch (Exception $e) {
                                        
                                        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
                                    }
                                } else {
                                    echo "<script>alert('Low Balance');</script>";
                                }
                            } else {
                                // Repurchase logic
                                $totalPay = 0;
                                $bvtotalPay = 0;

                                $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
                                $stmt->execute(['userid' => $userid]);
                                while ($cartDt = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $cartProId = $cartDt['product_id'];
                                    $cartProQty = $cartDt['qty'];

                                    $stmt1 = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id AND status = '1'");
                                    $stmt1->execute(['id' => $cartProId]);
                                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                                    if ($row) {
                                        $dp_price = (int)$cartProQty * (int)$row['dp_price'];
                                        $totalPay += $dp_price;
                                        $bv_total = (int)$cartProQty * (int)$row['pv'];
                                        $bvtotalPay += $bv_total;
                                    }
                                }

                                $finalPay = $totalPay;

                                if (true) { // Replace with actual wallet check if needed
                                    $purchasetype = "Repurchase";
                                    
                                        $pdo->beginTransaction();

                                        // Insert into tbl_order
                                        $ins_query = "INSERT INTO tbl_order (tr_id, amount, userid, name, email, mobile, address, city, state, pincode, ac_status, status, franchiseeid, date, time, type)
                                                      VALUES (:tr_id, :amount, :userid, :name, :email, :mobile, :address, :city, :state, :pincode, '0', '1', :franchiseeid, :date, :time, :type)";
                                        $stmt = $pdo->prepare($ins_query);
                                        $stmt->execute([
                                            'tr_id' => $tr_code,
                                            'amount' => $totalPay,
                                            'userid' => $userid,
                                            'name' => $name,
                                            'email' => $email,
                                            'mobile' => $number,
                                            'address' => $address,
                                            'city' => $city,
                                            'state' => $state,
                                            'pincode' => $pincode,
                                            'franchiseeid' => $franchiseeid,
                                            'date' => $date,
                                            'time' => $time,
                                            'type' => $purchasetype
                                        ]);
                                        
                                        echo"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";

                                        // Process cart items
                                        $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userid");
                                        $stmt->execute(['userid' => $userid]);
                                        while ($cartDt = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            
                                            $cartProId = $cartDt['product_id'];
                                            $cartProQty = $cartDt['qty'];

                                            $stmt1 = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id AND status = '1'");
                                            $stmt1->execute(['id' => $cartProId]);
                                            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                                            if ($row) {
                                                $pro_title = $row['title'];
                                                $dp_cost = $row['dp_price'];
                                                $dp_price = $row['mrp_total'];
                                                $pro_code = $row['code'];
                                                $pro_gst = $row['gst_price'];
                                                $pro_rate = $row['gst'];
                                                $bv_price = $row['bv_price'];
                                                $pv = $row['pv'];
                                                echo"bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb";

                                                // Insert into tbl_transaction_details
                                                $ins_details = "INSERT INTO tbl_transaction_details (tr_id, pro_id, pro_code, pro_name, pro_qty, pro_price, pro_dp_price, pro_gst, gst_rate,date)
                                                                VALUES (:tr_id, :pro_id, :pro_code, :pro_name, :pro_qty, :pro_price, :pro_dp_price, :pro_gst, :gst_rate,:date)";
                                                $stmt2 = $pdo->prepare($ins_details);
                                                $stmt2->execute([
                                                    'tr_id' => $tr_code,
                                                    'pro_id' => $cartProId,
                                                    'pro_code' => $pro_code,
                                                    'pro_name' => $pro_title,
                                                    'pro_qty' => $cartProQty,
                                                    'pro_price' => $dp_cost,
                                                    'pro_dp_price' => $dp_price,
                                                    'pro_gst' => $pro_gst,
                                                    'gst_rate' => $pro_rate,
                                                    'date' => $date
                                                ]);
                                                echo"ccccccccccccccccccccccccccccccccc";
                                            }
                                        }

                                        // Delete from cart
                                        $stmt = $pdo->prepare("DELETE FROM tbl_cart WHERE userid = :userid");
                                        $stmt->execute(['userid' => $userid]);

                                        // Update user wallet
                                        $update_user = "UPDATE user SET amount = amount - :finalPay, total_purchase = total_purchase + :finalPay, total_bv = total_bv + :bvtotalPay WHERE userid = :userid";
                                        $stmt = $pdo->prepare($update_user);
                                        $stmt->execute([
                                            'finalPay' => $finalPay,
                                            'bvtotalPay' => $bvtotalPay,
                                            'userid' => $userid
                                        ]);

                                        // Update tree BV
                                        $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
                                        $stmt->execute(['userid' => $userid]);
                                        $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $side = $rowheader['join_side'];

                                        $stmt = $pdo->prepare("SELECT * FROM tree WHERE `left_id` = :userid OR `right_id` = :userid");
                                        $stmt->execute(['userid' => $userid]);
                                        $rfa = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $temp_underuseridsp = $rfa['userid'] ?? '';
                                        $temp_side_sp = $side_bonus . 'sp';

                                        $temp_sidesp = $side;
                                        $total_countsp = 1;
                                        while ($total_countsp > 0) {
                                            $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid");
                                            $stmt->execute(['userid' => $temp_underuseridsp]);
                                            $ra = $stmt->fetch(PDO::FETCH_ASSOC);
                                            if ($ra) {
                                                $current_temp_side_sp = $ra[$temp_side_sp] + $bvtotalPay;
                                                $stmt2 = $pdo->prepare("UPDATE tree SET `$temp_side_sp` = :current_temp_side_sp WHERE userid = :userid");
                                                $stmt2->execute([
                                                    'current_temp_side_sp' => $current_temp_side_sp,
                                                    'userid' => $temp_underuseridsp
                                                ]);

                                                $next_under_useridsp = getUnderId($pdo,$temp_underuseridsp);
                                                $temp_sidesp = getUnderIdPlace($pdo,$temp_underuseridsp);
                                                $temp_side_sp = $temp_sidesp . 'sp';
                                                $temp_underuseridsp = $next_under_useridsp;

                                                if (empty($temp_underuseridsp)) {
                                                    $total_countsp = 0;
                                                }
                                            } else {
                                                $total_countsp = 0;
                                            }
                                        }

                                        $pdo->commit();
                                        echo "<script>alert('Your Order Placed Successfully! Thank you');window.location.assign('all-order');</script>";
                                     
                                } else {
                                    echo "<script>alert('Low Balance');</script>";
                                }
                            }
                        }

                        // Fetch user details
                        $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
                        $stmt->execute(['userid' => $userid]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $status = ($row['status'] == 1) ? "Active" : "Inactive";
                        ?>

                        <h5>Place an Order <span id="productCount" title="Total Products">0</span><span id="cartIcon"><a href="cart.php">Cart <i class="fa fa-shopping-cart"></i></a></span><span id="response"></span></h5>

                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
                        $stmt->execute(['userid' => $userid]);
                        $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row1['active'] == 1) {
                        ?>
                            <span style="background-color: skyblue; color: white; padding: 0.5rem; border-radius: 0.25rem; display: inline-block;">
                                Shopping balance <?php echo $shop_wallet_amount; ?>
                            </span>
                        <?php } ?>

                        <div class="card-title">Place Order</div>
                        <p>Please update details if you want to change <span id="totalAmount" class="float-right bg-info p-2 text-white">0</span></p>
                        <hr>

                        <form method="POST">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">User Id</label>
                                            <input type="text" name="userid" value="<?php echo $hmpre . $userid; ?>" class="form-control " id="input-7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">Franchisee</label>
                                            <?php
                                            $stmt = $pdo->prepare("SELECT * FROM tbl_franchisee WHERE status = '1'");
                                            $stmt->execute();
                                            ?>
                                            <select class="form-control " name="franchiseeid" required>
                                                <option value="">--Select Franchisee--</option>
                                                <?php while ($rowpack = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value="<?php echo $rowpack['franchiseeid']; ?>">
                                                        <?php echo $rowpack['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">User Name</label>
                                            <input type="text" value="<?php echo $row['name']; ?>" name="name" required class="form-control " id="input-7">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">Mobile Number</label>
                                            <input type="text" value="<?php echo $row['mobile']; ?>" name="number" required class="form-control " id="input-7">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">Email</label>
                                            <input type="email" value="<?php echo $row['email']; ?>" name="email" required class="form-control " id="input-7">
                                        </div>
                                    </div>
                                    <?php if ($row1['active'] == 1) { ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="side">Select Side</label>
                                                <select name="side" id="side" class="form-control ">
                                                    <option value="left" selected>Sales Bonus</option>
                                                    <option value="right">Sales Incentive</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">City</label>
                                            <input type="text" name="city" required class="form-control " id="input-7">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <select name="state" id="state" class="form-control " required>
                                                <option value="">-- Select State--</option>
                                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                                <option value="Assam">Assam</option>
                                                <option value="Bihar">Bihar</option>
                                                <option value="Chandigarh">Chandigarh</option>
                                                <option value="Chhattisgarh">Chhattisgarh</option>
                                                <option value="Dadar and Nagar Haveli">Dadar and Nagar Haveli</option>
                                                <option value="Daman and Diu">Daman and Diu</option>
                                                <option value="Delhi">Delhi</option>
                                                <option value="Lakshadweep">Lakshadweep</option>
                                                <option value="Puducherry">Puducherry</option>
                                                <option value="Goa">Goa</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Haryana">Haryana</option>
                                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                                <option value="Jharkhand">Jharkhand</option>
                                                <option value="Karnataka">Karnataka</option>
                                                <option value="Kerala">Kerala</option>
                                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Manipur">Manipur</option>
                                                <option value="Meghalaya">Meghalaya</option>
                                                <option value="Mizoram">Mizoram</option>
                                                <option value="Nagaland">Nagaland</option>
                                                <option value="Odisha">Odisha</option>
                                                <option value="Punjab">Punjab</option>
                                                <option value="Rajasthan">Rajasthan</option>
                                                <option value="Sikkim">Sikkim</option>
                                                <option value="Tamil Nadu">Tamil Nadu</option>
                                                <option value="Telangana">Telangana</option>
                                                <option value="Tripura">Tripura</option>
                                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                                <option value="Uttarakhand">Uttarakhand</option>
                                                <option value="West Bengal">West Bengal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">Pin Code</label>
                                            <input type="number" name="pincode" class="form-control " id="input-7">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="input-7">Address</label>
                                            <textarea required name="address" class="form-control "></textarea>
                                        </div>
                                    </div>
                                    <?php if (true) { ?>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <center><button type="submit" value="submit" name="submit" class="btn btn-primary shadow-primary btn-round px-5"><i class="fa fa-lock" aria-hidden="true"></i> Submit</button></center>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
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
<?php include 'common/footer.php'; ?>
<!--End footer-->

</div><!--End wrapper-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/custom-new-script.js"></script>

<script type="text/javascript">
function suggest() {
    var self_id = "5392408854";
    var spo_id = document.getElementById('username').value;
    if (spo_id == "") {
        document.getElementById("inputEmail1").innerHTML = "<span style=color:red;>Enter any Account ID</span>";
        return;
    }
    var xmlhttp;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var status = xmlhttp.responseText;
            document.getElementById("inputEmail1").innerHTML = "Name : " + status;
        }
    }
    xmlhttp.open('GET', 'suggest.php?spo_id=' + spo_id, true);
    xmlhttp.send();
}
</script>

</body>
</html>