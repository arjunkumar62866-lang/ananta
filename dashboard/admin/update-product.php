<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php 

include "common/header.php"; 
include "common/connection.php"; // DB connection (PDO)

$tid = $_GET['id'] ?? null;
$sub = $_GET['title'] ?? null;

if ($sub == "De_Product") {
    $stmt = $pdo->prepare("UPDATE tbl_product SET status = '0' WHERE id = :id");
    $ok = $stmt->execute([':id' => $tid]);
    echo $ok
        ? "<script>alert('Product Deactivated Successfully');window.location.assign('manage-product.php');</script>" 
        : "<script>alert('Error while deactivating product');window.location.assign('manage-product.php');</script>";

} elseif ($sub == "Ac_Product") {
    $stmt = $pdo->prepare("UPDATE tbl_product SET status = '1' WHERE id = :id");
    $ok = $stmt->execute([':id' => $tid]);
    echo $ok
        ? "<script>alert('Product Activated Successfully');window.location.assign('manage-product.php');</script>" 
        : "<script>alert('Error while activating product');window.location.assign('manage-product.php');</script>";

} elseif ($sub == "Instock") {
    $stmt = $pdo->prepare("UPDATE tbl_product SET pro_status = '1' WHERE id = :id");
    $ok = $stmt->execute([':id' => $tid]);
    echo $ok
        ? "<script>alert('Product marked In Stock');window.location.assign('manage-product.php');</script>" 
        : "<script>alert('Error while updating stock status');window.location.assign('manage-product.php');</script>";

} elseif ($sub == "OutStock") {
    $stmt = $pdo->prepare("UPDATE tbl_product SET pro_status = '0' WHERE id = :id");
    $ok = $stmt->execute([':id' => $tid]);
    echo $ok
        ? "<script>alert('Product marked Out of Stock');window.location.assign('manage-product.php');</script>" 
        : "<script>alert('Error while updating stock status');window.location.assign('manage-product.php');</script>";

} elseif ($sub == "Update") {
    $stmt = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :id");
    $stmt->execute([':id' => $tid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data safely
        $code        = $_POST['code'] ?? '';
        $title       = $_POST['title'] ?? '';
        $price       = $_POST['price'] ?? '';
        $pv          = $_POST['pv'] ?? '';
        $qty         = $_POST['qty'] ?? '';
        $newdpprice  = $_POST['newdpprice'] ?? '';
        $gst_amt     = $_POST['gst_amt'] ?? '';
        $pro_gst     = $_POST['pro_gst'] ?? '';
        $hsn         = $_POST['hsn'] ?? '';
        $description = $_POST['description'] ?? '';
        $main_cat    = $_POST['main_cat'] ?? '';

        $imagename = $row['img'];
        if (!empty($_FILES['image']['name'])) {
            $target = "productimage/";
            $imagename = uniqid().basename($_FILES['image']['name']);
            $target_file = "../".$target.$imagename;

            $type = pathinfo($target_file, PATHINFO_EXTENSION);
            if (in_array(strtolower($type), ['jpg','jpeg','png','gif'])) {
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            }
        }

        $sql = "UPDATE tbl_product 
                SET code = :code, 
                    title = :title, 
                    price = :price, 
                    pv = :pv,
                    dp_price = :newdpprice, 
                    gst_price = :gst_amt, 
                    gst = :pro_gst,
                    hsn = :hsn, 
                    qty = :qty, 
                    des = :description, 
                    main_cat = :main_cat,
                    img = :img
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':code'        => $code,
            ':title'       => $title,
            ':price'       => $price,
            ':pv'          => $pv,
            ':newdpprice'  => $newdpprice,
            ':gst_amt'     => $gst_amt,
            ':pro_gst'     => $pro_gst,
            ':hsn'         => $hsn,
            ':qty'         => $qty,
            ':description' => $description,
            ':main_cat'    => $main_cat,
            ':img'         => $imagename,
            ':id'          => $tid
        ]);

        if ($ok) {
            echo "<script>alert('Product updated successfully');window.location.assign('manage-product.php');</script>";
        } else {
            echo "<script>alert('Error updating product');</script>";
        }
    }
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

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3>Update Product</h3></div>
                        <hr>
                        <!--your code here-->
                        
                        <?php if($sub=="Update" && isset($row)) { ?>
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Main Category</label>
        <select class="form-control" name="main_cat" required>
            <option value="">--Select Category--</option>
            <?php
$stmt = $pdo->prepare("SELECT * FROM tbl_category WHERE status = '1'");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($categories as $row1) {
    $isSelected = ($row1['slug'] == $row['main_cat']) ? 'selected' : '';
    echo "<option value='{$row1['slug']}' $isSelected>{$row1['name']}</option>";
}
?>

        </select>
    </div>

    <div class="form-group">
        <label>Product Code</label>
        <input type="text" name="code" class="form-control" value="<?php echo $row['code']; ?>">
    </div>

    <div class="form-group">
        <label>HSN Code</label>
        <input type="text" name="hsn" class="form-control" value="<?php echo $row['hsn']; ?>">
    </div>

    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>">
    </div>

    <div class="form-group">
        <label>Quantity</label>
        <input type="number" name="qty" class="form-control" value="<?php echo $row['qty']; ?>">
    </div>

    <div class="form-group">
        <label>DP Price</label>
        <input type="text" name="newdpprice" class="form-control" value="<?php echo $row['dp_price']; ?>">
    </div>

    <div class="form-group">
        <label>GST %</label>
        <input type="text" name="pro_gst" class="form-control" value="<?php echo $row['gst']; ?>">
    </div>

    <div class="form-group">
        <label>GST Amount</label>
        <input type="text" name="gst_amt" class="form-control" value="<?php echo $row['gst_price']; ?>">
    </div>

    <div class="form-group">
        <label>MRP</label>
        <input type="text" name="price" class="form-control" value="<?php echo $row['price']; ?>">
    </div>

    <div class="form-group">
        <label>PV</label>
        <input type="text" name="pv" class="form-control" value="<?php echo $row['pv']; ?>">
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4"><?php echo $row['des']; ?></textarea>
    </div>

    <div class="form-group">
        <label>Upload New Image</label>
        <input type="file" name="image" class="form-control">
        <small>Old Image:</small><br>
        <img src="../productimage/<?php echo $row['img'];?>" width="100">
    </div>

    <div class="form-group text-center">
        <button type="submit" class="btn btn-primary">Update Product</button>
    </div>
</form>
<?php } ?>

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
