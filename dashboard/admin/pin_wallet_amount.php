<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1" style="background-color: #f1f5f9 !important; color: #0f172a !important; font-family: 'Inter', sans-serif;">

<style>
  body, .content-wrapper, .container-fluid {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
  }
  .page-banner {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.25);
  }
  .page-banner h3 {
    color: #ffffff !important;
    font-weight: 700;
    margin: 0;
    font-size: 1.5rem;
  }
  .page-banner p {
    color: #e0f2fe !important;
    margin: 4px 0 0 0;
    font-size: 0.9rem;
  }
  .ananta-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    padding: 24px !important;
    max-width: 650px;
    margin: 0 auto;
  }
  .form-group label {
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    margin-bottom: 8px !important;
  }
  .form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 12px 16px !important;
    font-size: 0.95rem !important;
    transition: all 0.2s ease !important;
  }
  .form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15) !important;
    outline: none !important;
  }
  .btn-submit-custom {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    padding: 12px 28px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3) !important;
    transition: all 0.2s ease !important;
    cursor: pointer;
  }
  .btn-submit-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(2, 132, 199, 0.4) !important;
  }
</style>

<div id="wrapper">
  <div class="clearfix"></div>
  <div class="content-wrapper">
    <div class="container-fluid" style="padding: 24px;">

      <div class="page-banner">
        <h3><i class="fa fa-paper-plane mr-2"></i> Add / Send Fund</h3>
        <p>Transfer wallet funds directly to member accounts</p>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="ananta-card">
            <h4 class="mb-4" style="color:#0f172a; font-weight:700; border-bottom: 2px solid #f1f5f9; padding-bottom:12px;">Fund Transfer Form</h4>
            <form id="walletForm">
              <div class="form-group mb-3">
                <label>User ID</label>
                <input type="text" name="userid" id="userid" class="form-control" placeholder="Enter User ID (e.g. AN1290)" required>
              </div>
              <div class="form-group mb-3" id="sponsor_name" style="display: none;">
                <label style="color: #0369a1 !important;">Member Name Verification</label>
                <div class="position-relative has-icon-right">
                  <input type="text" name="refferalid" id="response2" class="form-control input-shadow" style="background-color: #f0f9ff !important; color: #0369a1 !important; font-weight: 600;" readonly>
                </div>
              </div>
              <div class="form-group mb-4">
                <label>Amount (₹)</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="Enter Amount to Send" required>
              </div>
              <button type="submit" id="submitBtn" class="btn btn-submit-custom w-100">
                <i class="fa fa-check-circle mr-1"></i> Send Fund Now
              </button>
            </form>
            <div id="responseMsg" class="mt-3"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    $('#userid').on('blur', function () {
        let refId = $(this).val().trim();

        if (refId.length <= 2) {
            $("#sponsor_name").hide();
            $("#response2").val("");
            return;
        }

        $.ajax({
            type: "POST",
            url: "checkName.php",
            data: { data: refId },
            success: function (response) {
                if (response != "0") {
                    $("#response2").val(response);
                    $("#sponsor_name").slideDown();
                    $("#submitBtn").prop("disabled", false);
                } else {
                    $("#sponsor_name").hide();
                    $("#response2").val("");
                    $("#submitBtn").prop("disabled", true);
                    alert("Invalid or inactive user ID!");
                }
            }
        });
    });

});
</script>
<script>
$("#walletForm").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
        url: "pin_wallet_action.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            alert(response.message);

            if (response.status === "success") {
                $("#walletForm")[0].reset();
                $("#sponsor_name").hide();
            }
        },
        error: function() {
            alert("Something went wrong!");
        }
    });
});

</script>

</body>
</html>

