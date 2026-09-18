<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1">

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                    <div class="card-header text-center">
                        <h4>ROI Percantage Update</h4>
                    </div>
                    
                        <form id="roiForm">
                            <div class="form-group">
                                <label>Per day ROI%</label>
                                <input type="number" step="0.01" name="roi_one" id="roi_one" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
                            </button>
                        </form>
                        <div id="responseMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'common/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Load current ROI
$(document).ready(function(){
    $.get('roi_action.php', function(data){
        let res = JSON.parse(data);
        $('#roi_one').val(res.percentage);
    });

    // Update ROI via AJAX
    $('#roiForm').submit(function(e){
        e.preventDefault();
        $.post('roi_action.php', $(this).serialize(), function(data){
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
