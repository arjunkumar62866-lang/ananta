<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<?php
$stmt = $pdo->prepare("SELECT * FROM tbl_homest WHERE id = 1");
$stmt->execute();
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<head>
<style>
/* Modern Fintech Light UI - Offer Image Update Page */
html, body {
    background-color: #f8fafc !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

body.bg-theme, body.bg-theme1 {
    background: #f8fafc !important;
    background-color: #f8fafc !important;
    background-image: none !important;
}

#wrapper {
    background: #f8fafc !important;
    min-height: 100vh !important;
}

.content-wrapper {
    background-color: #f8fafc !important;
    padding-top: 100px !important;
    padding-bottom: 60px !important;
}

/* Main Offer Card */
.ananta-offer-card {
    background: #ffffff !important;
    border-radius: 24px !important;
    border: 2px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

.ananta-offer-card:hover {
    border-color: #10b981 !important;
    box-shadow: 0 14px 35px rgba(16, 185, 129, 0.12) !important;
}

.offer-card-header {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(147, 51, 234, 0.08) 100%), #ffffff;
    padding: 24px 30px;
    border-bottom: 2px solid #f1f5f9;
}

.offer-card-title {
    font-weight: 800;
    color: #0f172a;
    font-size: 22px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.offer-card-title i {
    color: #9333ea;
    font-size: 24px;
}

/* Upload & Preview Zone */
.offer-preview-wrapper {
    background: #f8fafc;
    border: 2px dashed #9333ea;
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.offer-preview-wrapper:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.preview-img-container {
    max-width: 100%;
    max-height: 280px;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    border: 3px solid #10b981;
    object-fit: cover;
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.form-group label {
    font-weight: 700;
    color: #0f172a;
    font-size: 14px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-file-input-stylish {
    border-radius: 14px !important;
    border: 2px solid #cbd5e1 !important;
    padding: 10px 16px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
    transition: all 0.25s ease !important;
}

.custom-file-input-stylish:focus {
    border-color: #9333ea !important;
    box-shadow: 0 0 0 4px rgba(147, 51, 234, 0.15) !important;
}

/* Modern Submit Button */
.btn-submit-offer {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border: 2px solid #10b981 !important;
    border-radius: 14px !important;
    padding: 12px 36px !important;
    font-weight: 800 !important;
    font-size: 16px !important;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25) !important;
    transition: all 0.3s ease !important;
}

.btn-submit-offer:hover {
    background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%) !important;
    border-color: #9333ea !important;
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.35) !important;
    transform: translateY(-2px);
    color: #ffffff !important;
}

.status-alert {
    padding: 12px 20px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 20px;
}
.status-alert-success {
    background-color: #dcfce7;
    color: #166534;
    border: 1.5px solid #bbf7d0;
}
.status-alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1.5px solid #fecaca;
}
</style>
</head>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<div id="wrapper">
    <div class="clearfix"></div>
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card ananta-offer-card">
                        
                        <!-- Header -->
                        <div class="offer-card-header text-center">
                            <h3 class="offer-card-title justify-content-center">
                                <i class="fa fa-gift"></i> Update Promotional Offer Banner
                            </h3>
                            <p class="mb-0 mt-1" style="color: #64748b; font-weight: 600; font-size: 13.5px;">
                                Manage live promotional offer images displayed across user dashboards
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5">

                            <!-- Status Message Container -->
                            <div id="statusMessage"></div>

                            <!-- Form -->
                            <form id="offerImageForm" enctype="multipart/form-data">
                                
                                <div class="form-group mb-4">
                                    <label class="d-block mb-3" style="color:#0f172a; font-weight:800;">Current Offer Banner & Preview</label>
                                    
                                    <div class="offer-preview-wrapper">
                                        <?php if (!empty($row1['offer_image'])) { ?>
                                            <img src="../img/<?php echo htmlspecialchars($row1['offer_image']); ?>" 
                                                 class="preview-img-container" id="previewImage" alt="Offer Image Preview"/>
                                        <?php } else { ?>
                                            <img src="" id="previewImage" style="display:none;" class="preview-img-container" alt="Offer Image Preview"/>
                                            <div id="noImgText" class="py-4">
                                                <i class="fa fa-picture-o text-muted" style="font-size:48px;"></i>
                                                <p class="mt-2 text-muted font-weight-bold">No offer image currently uploaded</p>
                                            </div>
                                        <?php } ?>

                                        <div class="mt-3">
                                            <label for="fileInput" class="btn btn-outline-primary px-4 py-2" style="border-radius:12px; font-weight:700; border-color:#9333ea; color:#9333ea; cursor:pointer;">
                                                <i class="fa fa-upload me-2"></i> Select New Offer Image
                                            </label>
                                            <input type="file" name="qr_codeimage" accept="image/*" class="d-none" id="fileInput">
                                            <p class="small text-muted mt-2 mb-0" style="font-weight:600;">Allowed formats: JPG, JPEG, PNG, GIF (Recommended size: 800x400px)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" id="btnSubmitOffer" class="btn btn-submit-offer">
                                        <i class="fa fa-cloud-upload me-2"></i> Upload & Update Offer Banner
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
                $('#noImgText').hide();
            }
            reader.readAsDataURL(file);
        }
    });

    // Handle form submit with AJAX
    $('#offerImageForm').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);

        var $btn = $('#btnSubmitOffer');
        var originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Uploading...');

        $.ajax({
            url: 'offer-update-action.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response){
                $btn.prop('disabled', false).html(originalBtnHtml);
                if(response.status === 'success'){
                    $('#statusMessage').html('<div class="status-alert status-alert-success"><i class="fa fa-check-circle me-2"></i>' + response.message + '</div>');
                } else {
                    $('#statusMessage').html('<div class="status-alert status-alert-danger"><i class="fa fa-exclamation-triangle me-2"></i>' + response.message + '</div>');
                }
            },
            error: function(){
                $btn.prop('disabled', false).html(originalBtnHtml);
                $('#statusMessage').html('<div class="status-alert status-alert-danger"><i class="fa fa-exclamation-triangle me-2"></i>An error occurred during upload. Please try again.</div>');
            }
        });
    });

});
</script>
</body>
</html>
