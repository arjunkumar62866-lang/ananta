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
          <div class="card p-4">
            <h4 class="mb-3">Send Fund</h4>
            <form id="walletForm">
              <div class="form-group">
                <label>User ID</label>
                <input type="text" name="userid" id="userid" class="form-control" placeholder="Enter User ID" required>
              </div>
              <div class="form-group" id="sponsor_name" style="display: none;">
                    <div class="position-relative has-icon-right">
                      <input type="text" name="refferalid" id="response2" class="form-control input-shadow" readonly>
                    </div>
              </div>
              <div class="form-group">
                <label>Amount</label>
                <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter Amount" required>
              </div>
              <button type="submit" id="submitBtn" class="btn btn-success">Submit</button>
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

  <!-- Ajax for auto-matic Name fetching -->
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
