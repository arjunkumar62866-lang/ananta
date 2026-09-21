<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - ROI PERCENTAGE UPDATE REDESIGN
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-admin-dashboard,
body.bg-theme,
body.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

#wrapper {
    background: #f4f6f8 !important;
    min-height: 100vh !important;
}

.content-wrapper {
    background-color: #f4f6f8 !important;
    padding-top: 85px !important;
    padding-bottom: 60px !important;
}

.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(16, 185, 129, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #10b981 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 20px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
}

.form-group label {
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 10px 16px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
}

.form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 12px 28px !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.2s ease !important;
}

.btn-primary-custom:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35) !important;
    color: #ffffff !important;
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Banner Card -->
        <div class="card income-header-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="income-header-icon">
                    <i class="fa fa-percent"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Set Daily ROI Percentage</h3>
                    <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Configure default daily Return on Investment percentage rate.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar">
                        <div class="card-header-title">
                            <h4>ROI Percentage Update</h4>
                            <p>Global daily ROI rate configuration</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="roiForm">
                            <div class="form-group mb-4">
                                <label for="roi_one"><i class="fa fa-line-chart me-1"></i> Per day ROI (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="roi_one" id="roi_one" class="form-control" placeholder="e.g. 0.50, 1.00" required>
                                <small style="color: #64748b; font-weight: 500; display: block; margin-top: 6px;">This percentage will apply to global daily ROI distributions.</small>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="fa fa-fw fa-lg fa-check-circle me-1"></i> Save & Update Rate
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
                `<div class="alert alert-${res.status === 'success' ? 'success' : 'danger'} font-weight-bold" style="border-radius: 10px;">${res.message}</div>`
            );
        });
    });
});
</script>

</body>
</html>
