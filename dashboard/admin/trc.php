<?php ob_start();?>

<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>

<?php

$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE id = 2");
$stmt->execute();
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
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

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Added Fields -->
                            <div class="row ">
                                <div class="col-md-12 p-4">
                                    <div class="tile text-center">
                                        <h3 class="tile-title">Update QR Code</h3>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="tile">
                                            <div class="row">
                                                <div class="col-lg-10 mx-auto">
                                                    <div class="card" style="margin-top: 45px;">
                                                        <div class="card-body">
                                                            <div class="card-title">Add USDT TRC 20 Qr Code</div>

                                                            <form id="updateQrForm" enctype="multipart/form-data">
                                                                <div class="form-group">
                                                                    <label class="">UPI Id</label>
                                                                    <input type="text" name="upiid" id="upiid" value="<?php echo htmlspecialchars($row1['upi']); ?>" class="form-control text-white border-secondary" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="">Account Details</label>
                                                                    <textarea name="account" rows="3" class="form-control text-white border-secondary"><?php echo htmlspecialchars($row1['account']);?></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="">Upload QR Code Image</label><br>
                                                                    <?php if($row1['qr_code'] != ''): ?>
                                                                        <img src="../../img/<?php echo $row1['qr_code']; ?>" height="150" class="mb-2 rounded">
                                                                    <?php endif; ?>
                                                                    <input type="file" name="qr_codeimage" class="form-control text-white border-secondary">
                                                                </div>
                                                                <div class="text-center">
                                                                    <button type="submit" class="btn btn-success px-5">
                                                                    <i class="fa fa-lock"></i> Proceed
                                                                    </button>
                                                                </div>
                                                            </form>
                                                            <div id="resultMessage" class="mt-3 text-center"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Added Fields -->
                        </div>
                    </div>
                </div>
            </div><!--End Row-->

            <div class="overlay toggle-menu"></div>

        </div>
    </div>

    <a href="javaScript:void();" class="back-to-top">
        <i class="fa fa-angle-double-up"></i>
    </a>
	
    <?php include 'common/footer.php' ?>
	
</div><!--End wrapper-->

<script>
    function getinfo(val) {
    var x = document.getElementById("price").value;
    var y = document.getElementById("pin_no").value;
    var z= x*y;
    $('#fname').val(z);
    }

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#updateQrForm').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: 'update_qrcode.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function() {
            $('#resultMessage').html('<span class="text-info">Updating...</span>');
        },
        success: function(response) {
            if (response.status == 'success') {
                // If QR image updated, refresh the image
                if (response.qr_code) {
                    $('img.qr-preview').attr('src', '../../img/' + response.qr_code);
                }

                $('#resultMessage').html('<span class="text-success">' + response.message + '</span>');
            } else {
                $('#resultMessage').html('<span class="text-danger">' + response.message + '</span>');
            }
        },
        error: function() {
            $('#resultMessage').html('<span class="text-danger">AJAX request failed.</span>');
        }
    });
});

</script>

</body>
</html>
