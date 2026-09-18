<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>
<?php 

if (isset($_POST['submit'])) {
    $price  = trim($_POST['price']);
    $income = trim($_POST['income']);

    if (!empty($price) && !empty($income)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_package (price, income, days, status) 
                                   VALUES (:price, :income, 365, 1)");
            $stmt->execute([
                'price'  => $price,
                'income' => $income
            ]);
            echo '<script>alert("Package added successfully!");</script>';
        } catch (PDOException $e) {
            echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
        }
    } else {
        echo '<script>alert("Please fill all fields.");</script>';
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

                <h4 class="text-center mb-4">Add New Package</h4>

                <form method="POST">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6 offset-md-3">

                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="price" class="form-control" placeholder="Enter package price" required>
                            </div>

                            <div class="form-group">
                                <label>Income</label>
                                <input type="number" step="0.01" name="income" class="form-control" placeholder="Enter package income" required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Row -->
                    <div class="form-group text-center mt-4">
                        <input type="reset" class="btn btn-secondary" value="Cancel">
                        <button type="submit" name="submit" class="btn btn-primary">Add Package</button>
                    </div>
                </form>

            </div>
        </div><!--End content-wrapper-->

        <!--Start Back To Top Button-->
        <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

        <!--Start footer-->
        <?php include 'common/footer.php' ?>

    </div><!--End wrapper-->

</body>

</html>
