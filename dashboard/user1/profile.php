<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>
<?php 

if (isset($_POST['submit'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];
    $father   = '';
    $country  = '';
    $state    = '';
    $address  = '';
    $gender   = '';
    $pin_code = '';

    // // heck if mobile exists for another user
    // $stmt = $pdo->prepare("SELECT mobile FROM user WHERE userid != :userid AND mobile = :mobile");
    // $stmt->execute(['userid' => $userid, 'mobile' => $mobile]);
    
    // Handle image upload with 200KB check
    $maxFileSize = 200 * 1024; // 200 KB
    $adhar_front_img_name = $_FILES['image']['name'] ?? '';
    $adhar_front_img_size = $_FILES['image']['size'] ?? 0;

    if ($adhar_front_img_name != '') {
        if ($adhar_front_img_size <= $maxFileSize) {
            $targetPath = 'images/' . basename($adhar_front_img_name);
            $upload = move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

            if ($upload) {
                // pdate image in DB
                $stmt = $pdo->prepare("UPDATE user SET user_image = :image WHERE userid = :userid");
                $stmt->execute(['image' => $adhar_front_img_name, 'userid' => $userid]);
                echo '<script>alert("Image uploaded successfully.");window.location="index.php";</script>';
            } else {
                echo '<script>alert("Error uploading image. Please try again.");</script>';
            }
        } else {
            echo '<script>alert("Please upload an image smaller than 200KB.");window.location="profile.php";</script>';
        }
    }

    // Update other profile details
    $stmt = $pdo->prepare("UPDATE user SET 
        name     = :name, 
        mobile   = :mobile,
        gender   = :gender, 
        email    = :email, 
        father   = :father, 
        country  = :country, 
        state    = :state, 
        address  = :address, 
        pin_code = :pin_code
        WHERE userid = :userid");

    $updated = $stmt->execute([
        'name'     => $name,
        'mobile'   => $mobile,
        'gender'   => $gender,
        'email'    => $email,
        'father'   => $father,
        'country'  => $country,
        'state'    => $state,
        'address'  => $address,
        'pin_code' => $pin_code,
        'userid'   => $userid
    ]);

    if ($updated) {
        echo '<script>alert("Profile Updated Successfully");window.location.href = "index.php";</script>';
    }
}

?>






<body class="bg-theme bg-theme1">

    <!-- start loader -->
    <div id="pageloader-overlay" class="visible incoming">
        <div class="loader-wrapper-outer">
            <div class="loader-wrapper-inner">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <!-- end loader -->

    <!-- Start wrapper-->
    <div id="wrapper">

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">
                
                <div class="card">
                    <div class="card-body">

                <div class="card-title text-center"><h3>Update Profile</h3></div>
                <hr>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>USER ID</label>
                                <input class="form-control" type="text" name="userid" id="userid"
                                    value="<?php echo $hmpre; ?><?php echo $userid; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Name</label>
                                <input class="form-control" type="text" name="name" id="name"
                                    value="<?php echo $username; ?>">
                            </div>

                            <div class="form-group">
                                <label>Mobile No</label>
                                <input class="form-control" type="text" name="mobile" id="mobile"
                                    value="<?php echo $usermobile; ?>">
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type="text" name="email" id="email"
                                    value="<?php echo $useremail; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Profile Photo</label>
                                <input class="form-control" type="file" name="image" id="image">
                                <span class="text-danger">Please upload image up to 200 KB only</span>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>JOINING DATE</label>
                                <input class="form-control" type="text" name="joining_date" id="joining_date"
                                    value="<?php echo $dateofjoining; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>SPONSOR ID</label>
                                <input class="form-control" type="text" name="sponserid" id="sponserid"
                                    value="<?php echo $hmpre; ?><?php echo $usersponser; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>STATUS</label>
                                <input class="form-control" type="text" name="status" id="status"
                                    value="<?php echo $status; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>SPONSOR NAME</label>
                                <input class="form-control" type="text" name="sponsername" id="sponsername"
                                    value="<?php echo $usersponsername; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Old Profile Image</label><br>
                                <img width="100px" src="images/<?php echo $userimage; ?>" alt="Old Profile Image">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Row -->
                    <div class="form-group text-center mt-4">
                        <input type="reset" class="btn btn-secondary" value="Cancel">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                </div>

            </div>
        </div><!--End content-wrapper-->
        </div>

        <!--Start Back To Top Button-->
        <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

        <!--Start footer-->
        <?php include 'common/footer.php' ?>

    </div><!--End wrapper-->

</body>

<!-- Mirrored from themewagon.github.io/dashtreme/profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 05 Aug 2025 06:01:59 GMT -->

</html>