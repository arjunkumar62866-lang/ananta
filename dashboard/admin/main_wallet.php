<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>
<?php include 'common/connection.php'; ?>

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

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card p-4">
                        <div class="card-title text-center">
                            <h3>Send Main Wallet Fund</h3>
                        </div>

                        <form id="fundForm">
                            <div class="form-group mb-3">
                                <label for="userid">User ID</label>
                                <input type="text" name="userid" id="userid" class="form-control" placeholder="Enter User ID" required>
                                <p style="color: blue;" id="userName"></p>
                            </div>

                            <div class="form-group mb-3">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter Amount" required>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>

                        <div id="response" class="mt-3"></div>
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
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
    <!--End Back To Top Button-->

    <!--Start footer-->
    <?php include 'common/footer.php'; ?>
    <!--End footer-->

</div><!--End wrapper-->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Fetch user details when user ID loses focus
    $('#userid').on('blur', function() {
        var userid = $(this).val();
        if(userid !== '') {
            $.ajax({
                url: 'topup_detail.php', // returns JSON { name, sponserid, status }
                type: 'POST',
                data: { p_id: userid },
                dataType: 'json',
                success: function(jsonStr) {
                    $('#userName').text('User Name: ' + jsonStr.name);
                },
                error: function() {
                    $('#userName').text('');
                }
            });
        }
    });

    // Submit fund form
    $('#fundForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'fund_wallet_process.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    $('#response').html('<span class="text-success">' + response.message + '</span>');
                    $('#fundForm')[0].reset();
                    $('#userName').text('');
                } else {
                    $('#response').html('<span class="text-danger">' + response.message + '</span>');
                }
            },
            error: function() {
                $('#response').html('<span class="text-danger">AJAX request failed</span>');
            }
        });
    });

});
</script>

</body>
</html>
