<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; 
include("common/connection.php");
?>
<?php
// Initialize variables
$tid = isset($_GET['id']) ? $_GET['id'] : null;
$title = isset($_GET['title']) ? $_GET['title'] : '';
$menu = '';
$submenu_status = '';
$success = '';
$error1 = '';
$error2 = '';

// Fetch existing category data for editing
if ($tid) {
    $sql1 = "SELECT * FROM tbl_category WHERE id = ?";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute([$tid]);
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $menu = $row['name'];
        $submenu_status = $row['sub_status'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu = trim($_POST['menu']);
    $submenu_status = trim($_POST['sub_status']);

    // Slug creation
    $slug = str_ireplace(['!', "'", '@', '#', '$', '%', '^', '&', '*', '(', ')', ':', '’', ' ', '='], '-', $menu);
    $slug = strtolower($slug);

    // Image upload handling
    $testname = basename($_FILES['image']['name']);
    $imagename = uniqid() . $testname;
    // $target1 = "../categoryimages/" . $imagename;
    $targetDir = __DIR__ . '/../categoryimages/'; 
    $target1 = $targetDir . $imagename;
    $type = strtolower(pathinfo($target1, PATHINFO_EXTENSION));
    
    if(!is_dir($targetDir)){
        mkdir($targetDir, 0755, true); 
    }
    

    // Validate image type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($type, $allowed_types)) {
        $error1 = "Only JPG, JPEG, PNG, GIF, and WEBP file formats are allowed to upload.";
    } elseif(file_exists($target1)){
            $error2 = "File Already Exists";
    } else {
        // Move uploaded file
        $upload_success = move_uploaded_file($_FILES['image']['tmp_name'], $target1);
        if ($upload_success) {
            if ($tid) {
                // Update existing category
                $sql = "UPDATE tbl_category SET name = ?, image = ?, sub_status = ?, status = '1' WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $exec = $stmt->execute([$menu, $imagename, $submenu_status, $tid]);
            } else {
                // Insert new category
                $sql = "INSERT INTO tbl_category (name, slug, sub_status, status, image) VALUES (?, ?, ?, '1', ?)";
                $stmt = $pdo->prepare($sql);
                $exec = $stmt->execute([$menu, $slug, $submenu_status, $imagename]);
            }

            if ($exec) {
                $menu = '';
                $submenu_status = '';
                $success = $tid ? "Main Category Updated Successfully" : "Main Category Inserted Successfully";
                echo "<script>alert('$success');window.location.assign('add-main-category');</script>";
            } else {
                $error2 = "Database operation failed.";
            }
        } else {
            $error2 = "Failed to upload image.";
        }
    }
}
?>


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
                        <div class="card-title text-center"><h3><?php echo $tid ? "Update" : "Add"; ?> Main Category</h3></div>
                        <hr>
                        <?php if ($success) { ?>
                            <h4 class="text-success" style="text-align:center;font-weight:bold;"><?php echo $success; ?></h4>
                        <?php } ?>
                        <?php if ($error1) { ?>
                            <h4 class="text-danger" style="text-align:center;font-weight:bold;"><?php echo $error1; ?></h4>
                        <?php } ?>
                        <?php if ($error2) { ?>
                            <h4 class="text-danger" style="text-align:center;font-weight:bold;"><?php echo $error2; ?></h4>
                        <?php } ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="menu">Category Name</label>
                                <input type="text" id="menu" name="menu" class="form-control form-control-rounded" placeholder="Enter Category Name" value="<?php echo htmlspecialchars($menu); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Image Upload</label>
                                <input type="file" id="image" name="image" class="form-control form-control-rounded" <?php echo $tid ? '' : 'required'; ?>>
                            </div>
                            <div class="form-group">
                                <label for="sub_status">Sub Category Status</label>
                                <select name="sub_status" class="form-control form-control-rounded" id="sub_status">
                                    <option value="">--Select--</option>
                                    <option value="1" <?php echo $submenu_status == '1' ? 'selected' : ''; ?>>Yes</option>
                                    <option value="0" <?php echo $submenu_status == '0' ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-primary shadow-primary btn-round px-5">
                                        <i class="icon-lock"></i> Submit
                                    </button>
                                </center>
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
<a href="javascript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
<!--End Back To Top Button-->
<!--Start footer-->
<?php include 'common/footer.php'; ?>
<!--End footer-->
</div><!--End wrapper-->

<!-- JavaScript dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.js"></script>
<script src="assets/js/waves.js"></script>
<script src="assets/js/sidebar-menu.js"></script>

</body>
</html>