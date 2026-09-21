<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1" style="background-color: #f8fafc !important; color: #0f172a !important; font-family: 'Inter', system-ui, -apple-system, sans-serif;">

<style>
/* Global light theme overrides for Admin News Page */
body, .content-wrapper, .container-fluid {
    background-color: #f8fafc !important;
    color: #0f172a !important;
}

.card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    margin-bottom: 24px !important;
}

.card-title-banner {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    border-radius: 10px 10px 0 0 !important;
    padding: 18px 24px !important;
    color: #ffffff !important;
}

.card-title-banner h3 {
    color: #ffffff !important;
    margin: 0 !important;
    font-weight: 700 !important;
    font-size: 1.25rem !important;
}

.form-group label {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control, textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
    font-size: 0.95rem !important;
    transition: all 0.2s ease !important;
}

.form-control:focus, textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
    outline: none !important;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2) !important;
    transition: all 0.2s ease !important;
}

.btn-primary-custom:hover {
    background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
    box-shadow: 0 4px 6px rgba(2, 132, 199, 0.3) !important;
    transform: translateY(-1px) !important;
}

.alert-success {
    background-color: #dcfce7 !important;
    color: #166534 !important;
    border: 1px solid #bbf7d0 !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
}

.alert-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border: 1px solid #fecaca !important;
    border-radius: 8px !important;
    padding: 12px 16px !important;
}
</style>

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
                    <div class="card-title-banner">
                        <h3><i class="fa fa-bullhorn me-2"></i> Update News & Announcements</h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="newsForm">
                            <div class="form-group mb-4">
                                <label for="exampleTextarea"><i class="fa fa-pencil-square-o me-1"></i> News Content (Displayed on User Dashboards)</label>
                                <textarea name="text" required class="form-control" id="exampleTextarea" rows="5" placeholder="Enter broadcast news text..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Save & Broadcast News
                            </button>
                        </form>
                        <div id="responseMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div><!--End Row-->

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->

    </div>
</div>
<!--End content-wrapper-->

<!--Start Back To Top Button-->
<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
<!--End Back To Top Button-->

<?php include 'common/footer.php'; ?>

</div><!--End wrapper-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Load current news text
    $.get('news_action.php', function(data){
        let response = JSON.parse(data);
        $('#exampleTextarea').val(response.news);
    });

    // Submit form via AJAX
    $('#newsForm').submit(function(e){
        e.preventDefault();
        $.post('news_action.php', $(this).serialize(), function(data){
            let res = JSON.parse(data);
            $('#responseMessage').html(
                `<div class="alert alert-${res.status === 'success' ? 'success' : 'danger'}">${res.message}</div>`
            );
        });
    });
});
</script>

</body>
</html>