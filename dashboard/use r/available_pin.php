<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php' ?>

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

  <!--Start sidebar-wrapper-->
  <!--End sidebar-wrapper-->

  <!--Start topbar header-->
  <!--End topbar header-->

  <div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

      <div class="row mt-3">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">

              <div class="card-title d-flex justify-content-between align-items-center">
                <h3><i class="fa fa-table"></i> Unused Pin</h3>
                <button class="btn btn-primary" type="button">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i> Activate User Id
                </button>
              </div>
              <hr>

              <div class="table-responsive" id="tblData">
                            <table class="table table-hover table-bordered" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Userid</th>
                                        <th>Pin</th>
                                        <th>Package</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will load this directly -->
                                </tbody>
                            </table>
                        </div>

            </div>
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
  <?php include 'common/footer.php' ?>
  <!--End footer-->
   
</div><!--End wrapper-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<script>
$(document).ready(function () {
    let table = $('#usersTable').DataTable({
        ajax: {
            url: 'get_available_pin.php',
            type: 'GET',
            data: { type: 'get_available_pins' },
            dataSrc: ''
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 }, // S.No.
            { data: 'userid', render: function (data) { return "<?php echo $hmpre; ?>" + data; } }, 
            { 
                data: 'pin',
                render: function (data, type, row) {
                    return `<a href="${data}" class="copy_text" data-toggle="tooltip" title="Copy to Clipboard">${data}</a>`;
                }
            },
            { data: 'package' },
            { data: 'date' }
        ],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100, 1000],
        dom: 'lfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Members Data'
            }
        ]
    });

    // Copy functionality
    $('#sampleTable').on('click', '.copy_text', function (e) {
        e.preventDefault();
        var copyText = $(this).attr('href');

        navigator.clipboard.writeText(copyText).then(function () {
            alert('Copied Pin: ' + copyText);
        });
    });
});
</script>

</body>
</html>
