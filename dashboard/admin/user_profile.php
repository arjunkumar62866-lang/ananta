<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php';
$uid=$_GET['uid'];
if(isset($_POST['submit'])){
    $sponsername=$_POST['sponsername'];
    
    //  $amount=$_POST['walletamount'];
	$name=$_POST['name'];
	$email=$_POST['email'];
	$mobile=$_POST['mobile'];
// 		$pin_code=$_POST['pin_code'];
	$pass=$_POST['pass'];

	
	$select=$pdo->prepare("SELECT mobile FROM user WHERE userid!=:userid AND mobile=:mobile");
	$select->execute([':userid'=>$uid, ':mobile'=>$mobile]);
	
	//if(mysqli_num_rows($query1)==0){
        
        $update=$pdo->prepare("UPDATE user SET sponsername=:sponsername, name=:name, mobile=:mobile, email=:email,
        pass=:pass WHERE userid=:userid");
        $update->execute([
                ':sponsername'=>$sponsername,
                ':name'=>$name,
                ':mobile'=>$mobile,
                ':email'=>$email,
                ':pass'=>$pass,
                
                ':userid'=>$uid
            ]);
        
        if($update){
		  echo '<script>alert("Profile Updated Successfully");</script>';	
		}		
// 	}else{
// 		echo '<script>alert("Mobile Number Already Exit");</script>';
// 	}
}

$select=$pdo->prepare("SELECT * FROM user WHERE userid=:userid");
$select->execute([':userid'=>$uid]);
$row=$select->fetch(PDO::FETCH_ASSOC);
if($row['status']==1){
	$status="Active";
}else{
	$status="Inactive";
}
?>

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

        <main class="app-content">
      <!--<div class="app-title">-->
      <!--  <div>-->
      <!--    <h1><i class="fa fa-edit"></i> Form Samples</h1>-->
      <!--    <p>Sample forms</p>-->
      <!--  </div>-->
      <!--  <ul class="app-breadcrumb breadcrumb">-->
      <!--    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>-->
      <!--    <li class="breadcrumb-item">Forms</li>-->
      <!--    <li class="breadcrumb-item"><a href="#">Sample Forms</a></li>-->
      <!--  </ul>-->
      <!--</div>-->
      <div class="row">
          <div class="col-md-12 p-4">
              <div class="tile text-center">
                    <h3 class="tile-title ">Edit Profile</h3>
              </div>
              <div class="row">
                 
                    <div class="col-md-6">
          <div class="tile">
            <!--<h3 class="tile-title text-center">Edit Profile</h3>-->
            <div class="tile-body">
             <form method="POST">
                <div class="form-group">
                  <label class="control-label">USER ID</label>
                  <input type="text" value="<?php echo $hmpre; ?><?php echo $row['userid']; ?>" readonly class="form-control" type="text" >
                </div>
                <!--<div class="form-group">-->
                <!--  <label class="control-label">FATHER / HUSBAND NAME</label>-->
                <!--  <input type="text" name="father" value="<php echo $row['father']; ?>"  class="form-control" >-->
                <!--</div>-->
                 <div class="form-group">
                  <label class="control-label">Mobile No</label>
                  <input type="text" name="mobile" value="<?php echo $row['mobile']; ?>"  class="form-control" >
                </div>
                
 <!--               <div class="form-group">-->
 <!--                 <label class="control-label">Gender</label>-->
                 
 <!--                 <select class="form-control"  name="gender" s>-->
	<!--				     <php if($row['gender'] !=''){?>-->
	<!--   <option><php echo $row['gender']; ?></option>-->
	<!--<php } else { ?>-->
	<!--   <option>--SELECT--</option>-->
	<!--<php } ?>-->
	<!--   <option value="Male">Male</option>-->
	<!--   <option value="Female">Female</option>-->
                  
 <!--               </select>-->
                 
                  
 <!--               </div>-->
                 <div class="form-group">
                  <label class="control-label">Email</label>
                  <input type="email" name="email" value="<?php echo $row['email']; ?>"  class="form-control" >
                </div>
                     </div>
                <div class="form-group">
                  <label class="control-label">Password</label>
                  <input  type="text" name="pass" value="<?php echo $row['pass']; ?>"  class="form-control" >
                </div>
                <!--<div class="form-group">-->
                <!--  <label class="control-label">Trx. Password</label>-->
                <!--  <input  type="text" name="txn_pass" value="<php echo $row['txn_pass']; ?>"  class="form-control" >-->
                <!--</div>-->
                <!--  <div class="form-group">-->
                <!--  <label class="control-label">Pin Code</label>-->
                <!--  <input type="text" name="pin_code" value="<php echo $row['pin_code']; ?>"  class="form-control" >-->
                <!--</div>-->
               
             
            </div>
        </div>
          <div class="col-md-6">
          <div class="tile">
            <!--<h3 class="tile-title text-center">Edit Profile</h3>-->
            <div class="tile-body">
              <form>
                <div class="form-group">
                  <label class="control-label">Name</label>
                  <input type="text" name="name" value="<?php echo $row['name']; ?>"  class="form-control"  >
                </div>
                <div class="form-group">
                  <label class="control-label">JOINING DATE</label>
                  <input type="text" value="<?php echo $row['joining_date']; ?>" readonly class="form-control" >
                </div>
                 <div class="form-group">
                  <label class="control-label">SPONSOR ID</label>
                  <input type="text" value="<?php echo  $hmpre  ?><?php echo $row['sponserid']; ?>" readonly class="form-control">
                </div>
               
                 <div class="form-group">
                  <label class="control-label">STATUS</label>
                  <input  type="text" value="<?php echo $status; ?>" readonly class="form-control" >
                </div>
                     </div>
                <div class="form-group">
                  <label class="control-label">SPONSOR NAME</label>
                  <input type="text" name="sponsername" value="<?php echo $row['sponsername']; ?>" readonly class="form-control" >
                </div>
                  
               <!--<div class="form-group">-->
               <!--   <label class="control-label">Wallet Amount</label>-->
               <!--   <input readonly type="number" name="walletamount" value="<php echo $row['amount']; ?>" class="form-control" >-->
               <!-- </div>-->
             
            </div>
         
          </div>
        </div>
         <div class="tile text-center">
                     <div class="form-group">
                  <!--<label class="control-label">Address</label>-->
                  <!--<textarea name="address"  rows="3" cols="4"class="form-control" ><php echo $row['address']; ?></textarea>-->
                   <div class="tile-footer">
              <button class="btn btn-primary" type="submit" value="submit" name="submit">Submit</button>
            </div>
                </div>
              </div>
                   </form> 
               </div>     
          </div>
       
      </div>
    </main>
        
	<?php include 'common/footer.php' ?>