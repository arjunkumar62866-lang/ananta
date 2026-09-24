<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<body class="bg-theme bg-theme1">

<style>
/* =========================================================
   USER DASHBOARD - MENTOR INCOME UI (REQ #14)
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.bg-theme,
body.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
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
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.10) 0%, rgba(2, 132, 199, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(16, 185, 129, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
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
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.table-responsive {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.table thead th {
    background: #f8fafc !important;
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    border-bottom: 2px solid #e2e8f0 !important;
    padding: 14px 16px !important;
}

.table tbody td {
    padding: 14px 16px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #0f172a !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
}
</style>

    <!-- Wrapper -->
    <div id="wrapper">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb Bar -->
                <div class="mb-3">
                    <nav aria-label="breadcrumb">
                        <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                            <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #475569; font-weight: 600;">User Growth</span>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #0f172a; font-weight: 700;">4. Mentor Income</span>
                        </div>
                    </nav>
                </div>

                <!-- Header Banner Card -->
                <div class="card income-header-card p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="income-header-icon">
                                <i class="fa fa-handshake-o"></i>
                            </div>
                            <div>
                                <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">Mentor Income Payouts</h3>
                                <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">Log of received 2% Mentor Income payouts based on your recorded contribution percentage.</p>
                            </div>
                        </div>
                        <div>
                            <div class="p-3 bg-white border rounded-3 text-end shadow-sm">
                                <span class="text-muted d-block text-uppercase fw-bold" style="font-size:11px;">Mentor Income Wallet</span>
                                <span class="h4 fw-extrabold text-success mb-0"><?php echo $hmcurrency . " " . number_format((float)($mentor_income_wallet ?? 0), 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card ananta-fintech-card">
                            <div class="card-header-bar">
                                <div>
                                    <h4 class="mb-0 fw-bold" style="color:#0f172a;">Received Mentor Income Ledger</h4>
                                    <p class="mb-0 text-muted" style="font-size:13px;">Detailed monthly distribution payouts credited from your Mentor</p>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="userMentorTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr</th>
                                                <th>Mentor ID</th>
                                                <th>Closing Month</th>
                                                <th>Mentor Monthly Income</th>
                                                <th>Rate</th>
                                                <th>Total Mentor Income</th>
                                                <th>My Contrib %</th>
                                                <th>Payout Amount</th>
                                                <th>Credit Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- DataTables loads this -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include 'common/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#userMentorTable').DataTable({
                ajax: {
                    url: 'get_mentor_income.php',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => '<span style="font-weight:700; color:#64748b;">#' + (meta.row + 1) + '</span>' },
                    {
                        data: 'mentor_id',
                        render: (data, type, row) => '<span style="color:#0284c7; font-weight:700;"><?php echo $hmpre; ?>' + data + '</span>' + (row.mentor_name ? '<br><small class="text-muted">' + row.mentor_name + '</small>' : '')
                    },
                    {
                        data: 'closing_month',
                        render: (data) => '<span class="badge bg-light text-dark fw-bold border">' + data + '</span>'
                    },
                    {
                        data: 'mentor_monthly_income',
                        render: (data) => '<span><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</span>'
                    },
                    {
                        data: 'mentor_income_rate',
                        render: (data) => '<span style="color:#0284c7; font-weight:700;">' + parseFloat(data || 2.0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'total_mentor_income',
                        render: (data) => '<span><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'contribution_percentage',
                        render: (data) => '<span class="badge bg-info text-white fw-bold">' + parseFloat(data || 0).toFixed(2) + '%</span>'
                    },
                    {
                        data: 'payout_amount',
                        render: (data) => '<span style="font-weight:800; color:#10b981;"><?php echo $hmcurrency; ?> ' + parseFloat(data || 0).toFixed(2) + '</span>'
                    },
                    {
                        data: 'credited_at',
                        render: (data) => '<span style="color:#64748b; font-size:13px;">' + (data || 'Not Credited') + '</span>'
                    }
                ],
                pageLength: 10,
                dom: 'lfrtip'
            });
        });
    </script>

</body>
</html>
