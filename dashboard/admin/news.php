<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

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
                    <div class="card-title text-center">
                        <h3>Update News</h3>
                    </div>
                    <hr>
                    
                        <form id="newsForm">
                            <div class="form-group">
                                <label>Update News</label>
                                <textarea name="text" required class="form-control" id="exampleTextarea" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
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