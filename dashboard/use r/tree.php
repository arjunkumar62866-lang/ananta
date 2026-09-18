<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php';
include("common/connection.php");
date_default_timezone_set('Asia/kolkata');

$search = $userid;

// Search functionality for tree view
if (isset($_GET['search-id'])) {
    $search_id = trim($_GET['search-id']);

    if ($search_id != "") {
        $stmt = $pdo->prepare("SELECT userid FROM user WHERE userid = :userid LIMIT 1");
        $stmt->execute([':userid' => $search_id]);

        if ($stmt->rowCount() > 0) {
            $search = $search_id;
        } else {
            echo "<script>alert('No downline Users');window.location.assign('tree.php');</script>";
            exit;
        }
    } else {
        echo "<script>alert('No downline users');window.location.assign('tree.php');</script>";
        exit;
    }
}


// Function to fetch tree details
function tree_data($userid){
    global $pdo; // $con should be a PDO connection
    $data = [];
    

    try {
        $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid LIMIT 1");
        $stmt->execute([':userid' => $userid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $data['left']       = $result['left_id'];
            $data['right']      = $result['right_id'];
            $data['leftcount']  = $result['leftcount'];
            $data['rightcount'] = $result['rightcount'];
            $data['lefttotal']  = $result['lefttotal'];
            $data['righttotal'] = $result['righttotal'];
            // $data['status']     = $result['status'];
        }
    } catch (PDOException $e) {
        // Log error in real apps, don’t echo directly
        echo "Database Error: " . $e->getMessage();
    }

    return $data;
}

?>


<head>
<style>
.userimg {
    background: url(images/usera.png) no-repeat center;
    display: block;
    width: 70px;
    height: 70px;
    margin: auto;
}
.userimg2int {
    background: url(images/inactive.png) no-repeat center;
    display: block;
    width: 70px;
    height: 70px;
    margin: auto;
}
.usrdetail {
    background: #f3f1f1;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    width: 250px;
    text-align:center;
    padding:10px;
    margin-top:10px;
    color:blue;
}
.mobinew { overflow:auto; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
// AJAX Popup for user details
function bigImg(userid,divId) {
    $.ajax({
        url: "popup.php",
        type: "POST",
        data: { Assid: userid },
        dataType: "JSON",
        success: function (jsonStr) {
            $.each(jsonStr.result, function (i, items) {
                document.getElementById("demo"+divId).innerHTML =
                "<div class='usrdetail'>"+
                "Name - "+items['name']+"<br>"+
                "Sponser Id - "+items['sponserid']+"<br>"+
                "Joining Date - "+items['joining_date']+"<br>"+
                "Mobile No - "+items['mobile']+"<br>"+
                "Left Count - "+items['leftcount']+"<br>"+
                "Right Count - "+items['rightcount']+"</div>";
                var x = document.getElementById("demo"+divId);
                x.style.display = (x.style.display === "none") ? "block" : "none";
            });
        }
    });
}
function normalImg(divId) {
    document.getElementById("demo"+divId).style.display = "none";
}
</script>
</head>


<body class="bg-theme bg-theme1">

<!-- start loader -->
   <!--<div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>-->
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
                        <div class="card-title text-center"><h3>Tree View</h3></div>
                        <hr>
                        
                        
                        
              <div class="row mobinew">
    <table class="table" align="center" border="0" style="text-align:center;margin-top:20px;">
        <?php
        // Fetch root user
        $data = tree_data($search);

        try {
            $stmt = $pdo->prepare("SELECT name, status, active, package FROM user WHERE userid = :userid LIMIT 1");
            $stmt->execute([':userid' => $search]);
            $ro1 = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ro1) {
                $name     = $ro1['name'];
                $status   = $ro1['status'];
                $idactive = $ro1['active'];
            } else {
                $name = "Unknown";
                $status = "";
                $idactive = 0;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
        <tr>
            <td colspan="4">
                <i class="<?php echo ($idactive == '1') ? 'userimg' : 'userimg2int'; ?>"></i>
                <div>
                    <b><?php echo htmlspecialchars($name); ?><br>(<?php echo htmlspecialchars($search); ?>)</b>
                </div>
            </td>
        </tr>
        <!-- Add your child nodes here using same reference logic -->
        
        <?php  
            // === First level children ===
            $first_left_user  = $data['left'];
            $first_right_user = $data['right'];
            
            // Left child
            $leftUser = [];
            if ($first_left_user) {
                $stmt = $pdo->prepare("SELECT name,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_left_user]);
                $leftUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // Right child
            $rightUser = [];
            if ($first_right_user) {
                $stmt = $pdo->prepare("SELECT name,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_right_user]);
                $rightUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            
            
            
            // === Second level children ===
            $first_left_data = tree_data($first_left_user);
            $first_right_data = tree_data($first_right_user);
            
            $first_left_left_user  = $first_left_data['left'];
            $first_left_right_user = $first_left_data['right'];
            
            $first_right_left_user  = $first_right_data['left'];
            $first_right_right_user = $first_right_data['right'];
            
            // FIRST Left Left child
            $firstleftleftUser = [];
            if ($first_left_left_user) {
                $stmt = $pdo->prepare("SELECT name,userid,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_left_left_user]);
                $firstleftleftUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // FIRST Left Right child
            $firstleftrightUser = [];
            if ($first_left_right_user) {
                $stmt = $pdo->prepare("SELECT name,userid,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_left_right_user]);
                $firstleftrightUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            
            
        
            
            // Left child
            $firstrightleftUser = [];
            if ($first_right_left_user) {
                $stmt = $pdo->prepare("SELECT name,userid,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_right_left_user]);
                $firstrightleftUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // Right child
            $firstrightrightUser = [];
            if ($first_right_right_user) {
                $stmt = $pdo->prepare("SELECT name,userid,status,package,active FROM user WHERE userid = :id");
                $stmt->execute([':id' => $first_right_right_user]);
                $firstrightrightUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        
        <tr>
                <!-- LEFT CHILD -->
                <td colspan="2">
                <?php if (!empty($leftUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_left_user; ?>">
                        <i class="<?php echo ($leftUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_left_user; ?>',2)" 
                           onmouseout="normalImg(2)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($leftUser['name']); ?><br>(<?php echo $first_left_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo2"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $search; ?>&type=left">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>

                <!-- RIGHT CHILD -->
                <td colspan="2">
                <?php if (!empty($rightUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_right_user; ?>">
                        <i class="<?php echo ($rightUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_right_user; ?>',3)" 
                           onmouseout="normalImg(3)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($rightUser['name']); ?><br>(<?php echo $first_right_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo3"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $search; ?>&type=right">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>
            </tr>
            
            
            
            <tr>
                <!--FIRST LEFT LEFT CHILD -->
                <td colspan="1">
                <?php if (!empty($firstleftleftUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_left_left_user; ?>">
                        <i class="<?php echo ($firstleftleftUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_left_left_user; ?>',1)" 
                           onmouseout="normalImg(2)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($firstleftleftUser['name']); ?><br>(<?php echo $first_left_left_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo2"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $first_left_user; ?>&type=left">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>
                
                <!--FIRST LEFT RIGHT CHILD-->
                <td colspan="1">
                <?php if (!empty($firstleftrightUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_left_right_user; ?>">
                        <i class="<?php echo ($firstleftrightUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_left_right_user; ?>',2)" 
                           onmouseout="normalImg(2)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($firstleftrightUser['name']); ?><br>(<?php echo $first_left_right_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo2"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $first_left_user; ?>&type=left">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>
                
                <!--FIRST RIGHT LEFT CHILD -->
                <td colspan="1">
                <?php if (!empty($firstrightleftUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_right_left_user; ?>">
                        <i class="<?php echo ($firstrightleftUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_right_left_user; ?>',2)" 
                           onmouseout="normalImg(2)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($firstrightleftUser['name']); ?><br>(<?php echo $first_right_left_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo2"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $first_right_user; ?>&type=left">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>

                <!--FISRT RIGHT RIGHT CHILD -->
                <td colspan="1">
                <?php if (!empty($firstrightrightUser)): ?>
                    <a href="tree.php?search-id=<?php echo $first_right_right_user; ?>">
                        <i class="<?php echo ($firstrightrightUser['active']=='1') ? 'userimg' : 'userimg2int'; ?>" 
                           onmouseover="bigImg('<?php echo $first_right_right_user; ?>',3)" 
                           onmouseout="normalImg(3)"></i>
                        <div>
                            <b><?php echo htmlspecialchars($firstrightrightUser['name']); ?><br>(<?php echo $first_right_right_user; ?>)</b>
                        </div>
                    </a>
                    <div id="demo3"></div>
                <?php else: ?>
                    <a href="add_user_binary_registration_form.php?sponsorid=<?php echo $userid; ?>&underuserid=<?php echo $first_right_user; ?>&type=right">
                        <img src="images/red2a.png"><br>Add New User
                    </a>
                <?php endif; ?>
                </td>
            </tr>
            
            
            
            
        
    </table>
</div>

              <!-- ✅ Tree Layout End -->
                        
                        
                    </div>
                </div>
            </div>
        </div><!--End Row-->

        <!--start overlay-->
        <!--<div class="overlay toggle-menu"></div>-->
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
