<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php';
include 'common/connection.php'; // must contain $pdo

// ---------------- CHANGE PASSWORD LOGIC ----------------
$stmt1 = $pdo->prepare("SELECT pass FROM admin WHERE id=1");
$stmt1->execute();
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

// if (!$row1) {
//     echo "<script>alert('User not found');window.location.assign('update-password.php');</script>";
//     exit;
// }

$current_pass = $row1['pass'];

if (isset($_POST['submit'])) {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new != $confirm) {
        echo "<script>alert('New Password & Confirm Password do not match');window.location.assign('password.php');</script>";
        exit;
    }

    if ($old != $current_pass) {
        echo "<script>alert('Invalid Old Password');window.location.assign('password.php');</script>";
        exit;
    }

    // Update password
    $stmt2 = $pdo->prepare("UPDATE admin SET pass = :newpass WHERE id=1");
    $stmt2->execute([
        ':newpass' => $new
    ]);

    echo "<script>alert('Password changed successfully');window.location.assign('password.php');</script>";
    exit;
}

?>


<body class="bg-theme bg-theme1">

<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner"><div class="loader"></div></div>
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
                      <div class="card-title text-center"><h3>Change Password</h3></div>
                      <hr>

                      <!-- CHANGE PASSWORD FORM -->
                      <form method="POST">
                        
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="old_password" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" required class="form-control">
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary">
                            Update Password
                        </button>

                      </form>
                      <!-- END FORM -->

                  </div>
              </div>
          </div>
      </div>

  </div>
</div>

<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<?php include 'common/footer.php'; ?>

</div>

</body>
</html>