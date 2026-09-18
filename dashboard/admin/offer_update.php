<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<?php
$stmt = $pdo->prepare("SELECT * FROM tbl_homest WHERE id = 1");
$stmt->execute();
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<body class="bg-theme bg-theme1">

<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner">
            <div class="loader"></div>
        </div>
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
                            <div class="card-title text-center">
                                <h3>Update Offer Image</h3>
                            </div>
                            <hr>

                            <!-- Status Message -->
                            <div id="statusMessage" class="text-center mb-3"></div>

                            <!-- Form -->
                            <form id="offerImageForm" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Upload Offer Image</label><br>
                                    <?php if (!empty($row1['offer_image'])) { ?>
                                        <img src="../img/<?php echo $row1['offer_image']; ?>" 
                                             height="200" width="300" class="mb-2" id="previewImage"/>
                                    <?php } else { ?>
                                        <img src="" id="previewImage" style="display:none;" height="200" width="300" class="mb-2"/>
                                    <?php } ?>
                                    <input type="file" name="qr_codeimage" class="form-control" id="fileInput">
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="overlay toggle-menu"></div>
        </div>
    </div>

    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <?php include 'common/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Preview selected image before upload
    $('#fileInput').on('change', function(){
        const file = this.files[0];
        if (file){
            let reader = new FileReader();
            reader.onload = function(event){
                $('#previewImage').attr('src', event.target.result).show();
            }
            reader.readAsDataURL(file);
        }
    });

    // Handle form submit with AJAX
    $('#offerImageForm').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'offer-update-action.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response){
                if(response.status === 'success'){
                    $('#statusMessage').html('<span style="color:green;">'+response.message+'</span>');
                } else {
                    $('#statusMessage').html('<span style="color:red;">'+response.message+'</span>');
                }
            },
            error: function(){
                $('#statusMessage').html('<span style="color:red;">Error in AJAX request.</span>');
            }
        });
    });

});
</script>
</body>
</html>
