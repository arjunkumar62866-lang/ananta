<?php
session_start();
require_once 'common/connection.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['auserid'])) {
    header('Location: index.php');
    exit;
}

// Filters
$filters = [
    'user_id'        => $_GET['user_id'] ?? '',
    'activator_id'   => $_GET['activator_id'] ?? '',
    'target_id'      => $_GET['target_id'] ?? '',
    'transaction_id' => $_GET['transaction_id'] ?? '',
    'status'         => $_GET['status'] ?? '',
    'from_date'      => $_GET['from_date'] ?? '',
    'to_date'        => $_GET['to_date'] ?? ''
];

$history = getAdminActivationHistory($filters, $pdo);
$revSummary = getAdminActivationRevenueTotal($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<style>
body.ananta-admin-dashboard {
    background: #f4f6f8 !important;
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
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}
</style>

<body class="ananta-admin-dashboard">

    <div id="wrapper" class="ananta-admin-dashboard">
        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Header Banner Card -->
                <div class="card income-header-card p-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="income-header-icon" style="width: 58px; height: 58px; border-radius: 18px; background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); color: #ffffff; font-size: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);">
                                <i class="fa fa-shield"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 font-weight-bold" style="color: #0f172a;">Admin Account Activation History ($11 Unlock Access)</h4>
                                <p class="text-muted small mb-0">Complete audit log of all self activations, renewals, and other-user activations</p>
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(22, 163, 74, 0.12); color: #16a34a; font-size: 14px;">
                                Total Revenue: $<?php echo number_format($revSummary['total_usd'], 2); ?> (₹<?php echo number_format($revSummary['total_inr'], 2); ?>) | Count: <?php echo $revSummary['count']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Admin Search & Date Filter Form -->
                <div class="ananta-fintech-card p-4 mb-4">
                    <h6 class="font-weight-bold mb-3" style="color: #0f172a;">
                        <i class="fa fa-filter text-primary me-2"></i> Filter Activation Records
                    </h6>
                    <form method="GET" action="activation_history.php">
                        <div class="form-row">
                            <div class="form-group col-md-3 mb-3">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">User ID / Search</label>
                                <input type="text" class="form-control" name="user_id" placeholder="Activator or Target User ID" value="<?php echo htmlspecialchars($filters['user_id']); ?>">
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">Transaction ID</label>
                                <input type="text" class="form-control" name="transaction_id" placeholder="UNLOCK-..." value="<?php echo htmlspecialchars($filters['transaction_id']); ?>">
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">From Date</label>
                                <input type="date" class="form-control" name="from_date" value="<?php echo htmlspecialchars($filters['from_date']); ?>">
                            </div>
                            <div class="form-group col-md-3 mb-3">
                                <label class="font-weight-bold small text-uppercase" style="color: #475569;">To Date</label>
                                <input type="date" class="form-control" name="to_date" value="<?php echo htmlspecialchars($filters['to_date']); ?>">
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 10px;">
                                <i class="fa fa-search me-1"></i> Filter Results
                            </button>
                            <a href="activation_history.php" class="btn btn-outline-secondary font-weight-bold px-4 py-2" style="border-radius: 10px;">
                                <i class="fa fa-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Activation Records Table Card -->
                <div class="ananta-fintech-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                        <h5 class="mb-0 font-weight-bold" style="color: #0f172a;">
                            <i class="fa fa-list text-primary me-2"></i> Activation & Renewal Logs
                        </h5>
                        <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: #f1f5f9; color: #475569;">
                            Showing <?php echo count($history); ?> Records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                            <thead style="background: #f8fafc; color: #334155; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-3 px-3">#</th>
                                    <th class="py-3 px-3">Txn ID</th>
                                    <th class="py-3 px-3">Activation Type</th>
                                    <th class="py-3 px-3">Activator User</th>
                                    <th class="py-3 px-3">Activated User</th>
                                    <th class="py-3 px-3 text-right">Amount (USD / INR)</th>
                                    <th class="py-3 px-3 text-center">Start Date</th>
                                    <th class="py-3 px-3 text-center">Expiry Date</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13.5px;">
                                <?php if (!empty($history)): ?>
                                    <?php $sr = 1; foreach ($history as $h): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="py-3 px-3 font-weight-bold text-muted"><?php echo $sr++; ?></td>
                                            <td class="py-3 px-3">
                                                <code style="background: #f1f5f9; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-weight: 700;"><?php echo htmlspecialchars($h['transaction_id']); ?></code>
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="badge px-3 py-1" style="background: rgba(147, 51, 234, 0.1); color: #9333ea; font-weight: 700; border-radius: 6px;">
                                                    <?php echo htmlspecialchars($h['activation_type']); ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="font-weight-bold" style="color: #0f172a;"><?php echo htmlspecialchars($h['activator_name'] ?: $h['activator_user_id']); ?></span>
                                                <small class="d-block text-muted">(<?php echo htmlspecialchars($h['activator_user_id']); ?>)</small>
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="font-weight-bold" style="color: #0284c7;"><?php echo htmlspecialchars($h['target_name'] ?: $h['target_user_id']); ?></span>
                                                <small class="d-block text-muted">(<?php echo htmlspecialchars($h['target_user_id']); ?>)</small>
                                            </td>
                                            <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                                $<?php echo number_format($h['amount_usd'], 2); ?> (₹<?php echo number_format($h['amount_inr'], 2); ?>)
                                            </td>
                                            <td class="py-3 px-3 text-center text-muted small">
                                                <?php echo date('d-M-Y H:i', strtotime($h['activation_start_date'])); ?>
                                            </td>
                                            <td class="py-3 px-3 text-center text-muted small">
                                                <?php echo date('d-M-Y H:i', strtotime($h['activation_expiry_date'])); ?>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;"><?php echo htmlspecialchars($h['status']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fa fa-folder-open-o fa-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                            No account activation records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <?php include 'common/footer.php'; ?>
    </div>
</body>
</html>
