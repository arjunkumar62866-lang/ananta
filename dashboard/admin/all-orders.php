<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>
<?php include 'common/connection.php'; ?>

<?php 
$type = isset($_GET['type']) ? $_GET['type'] : '';
?>

<body class="bg-theme bg-theme1">

<!-- Loader -->
<div id="pageloader-overlay" class="visible incoming">
  <div class="loader-wrapper-outer">
    <div class="loader-wrapper-inner"><div class="loader"></div></div>
  </div>
</div>
<!-- End Loader -->

<div id="wrapper">
  <div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="container-fluid">

      <div class="row mt-3">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="card-title text-center">
                <h3><?php echo $type ? htmlspecialchars($type) : 'All'; ?> Orders</h3>
              </div>
              <hr>

              <!-- Export Button -->
              <button id="customExportBtn" class="btn btn-success mb-3">
                <i class="fa fa-file-excel-o"></i> Export to Excel
              </button>

              <!-- Table -->
              <div class="table-responsive" id="tblData">
                <table class="table table-hover table-bordered" id="ordersTable">
                  <thead>
                    <tr>
                      <th>S.No.</th>
                      <th>Invoice No.</th>
                      <th>User ID</th>
                      <th>User Name</th>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Price</th>
                      <th>Current Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- DataTables loads here -->
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Overlay -->
      <div class="overlay toggle-menu"></div>
    </div>
  </div>

  <!-- Back To Top -->
  <a href="javaScript:void();" class="back-to-top">
    <i class="fa fa-angle-double-up"></i>
  </a>

  <?php include 'common/footer.php'; ?>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
  let table = $('#ordersTable').DataTable({
    ajax: {
      url: 'get_orders_list.php',
      type: 'GET',
      data: { type: '<?php echo $type; ?>' },
      dataSrc: ''
    },
    columns: [
      { data: null, render: (data, type, row, meta) => meta.row + 1 },
      { data: 'tr_id', render: data => `#${data}` },
      { data: 'userid', render: data => `<?php echo $hmpre; ?>${data}` },
      { data: 'name' },
      {
        data: 'pro_img',
        render: function (data) {
          return `<img src="../productimage/${data}" style="width:100px;height:80px;border-radius:5px;">`;
        }
      },
      { data: 'pro_title' },
      { data: 'amount', render: data => `<i class='fa fa-inr'></i> ${data}` },
      {
        data: 'ac_status',
        render: function (data) {
          if (data == '1') return "Approved";
          if (data == '0') return "Pending";
          if (data == '2') return "Rejected";
          return "Unknown";
        }
      },
      {
        data: null,
        render: function (data) {
          return `
            <a href="update-orders.php?id=${data.tr_id}&title=Reject" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i></a>
            <a href="update-orders.php?price=${data.amount}&uid=${data.userid}&id=${data.tr_id}&title=Approved" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
          `;
        }
      }
    ],
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    dom: 'lfrtip',
    buttons: [
      { extend: 'excelHtml5', title: 'Orders Data' }
    ]
  });

  $('#customExportBtn').on('click', function () {
    table.button('.buttons-excel').trigger();
  });
});
</script>

</body>
</html>
