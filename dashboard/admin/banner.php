<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>

<body class="bg-theme bg-theme1">

<!-- start loader -->
<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner"><div class="loader"></div></div>
    </div>
</div>
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
                            <div class="card-title text-center"><h3>Update Dashboard Banner</h3></div>
                            <hr>

                            <!--your code here-->

                            <form action="save_banner.php" method="POST" enctype="multipart/form-data">

                                <div class="form-group">
                                    <label><strong>Select Banner Image (Max 2MB)</strong></label>
                                    <input type="file" name="banner_image" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    Upload Banner
                                </button>

                            </form>

                            <!--end your code-->

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

</html>
