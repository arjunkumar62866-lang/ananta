<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

$stmtData = getUserFundStatementData($userid, $fromDate, $toDate, $pdo);
$investments = $stmtData['investments'];
$unlockDebits = $stmtData['unlock_debits'];

$totalInvestedUSD = 0;
foreach ($investments as $inv) {
    $totalInvestedUSD += (float)($inv['real_fund_usd'] ?? 0);
}
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">Fund Statement</h4>
                <p class="text-muted small mb-0">Detailed breakdown of capital investments, lock periods, and activation debits</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">Fund Statement</span>
                </div>
            </nav>
        </div>

        <!-- Date Range Filter Form -->
        <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-filter-list mr-2" style="color: #0284c7;"></i> Date Range Filter
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="fund_statement.php">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-5 mb-3 mb-md-0">
                            <label for="from_date" class="font-weight-bold small text-uppercase" style="color: #475569;">From Date</label>
                            <input type="date" class="form-control form-control-lg" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
                        </div>
                        <div class="form-group col-md-5 mb-3 mb-md-0">
                            <label for="to_date" class="font-weight-bold small text-uppercase" style="color: #475569;">To Date</label>
                            <input type="date" class="form-control form-control-lg" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
                        </div>
                        <div class="form-group col-md-2 mb-0">
                            <button type="submit" class="btn btn-block py-2 font-weight-bold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border-radius: 10px; height: 48px;">
                                <i class="zmdi zmdi-search mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Investments Statement Table Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-money-box mr-2" style="color: #0284c7;"></i> Investment Records Statement
                </h6>
                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                    Total Invested: <?php echo formatCurrency($totalInvestedUSD, $selectedCurrency); ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="py-3 px-4 text-center">#</th>
                                <th class="py-3 px-3">Inv ID</th>
                                <th class="py-3 px-3 text-center">Package Code</th>
                                <th class="py-3 px-3 text-right">Fund Investment</th>
                                <th class="py-3 px-3 text-right">Bonus Amount</th>
                                <th class="py-3 px-3 text-center">Lock Period</th>
                                <th class="py-3 px-3 text-center">Maturity Date</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-center">Investment Date</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            <?php if (!empty($investments)): ?>
                                <?php $sr = 1; foreach ($investments as $inv): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 px-4 text-center font-weight-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                        <td class="py-3 px-3 font-weight-bold" style="color: #0284c7;">#<?php echo $inv['id']; ?></td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge px-3 py-1" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-weight: 600; border-radius: 6px;">
                                                <?php echo htmlspecialchars($inv['package_code'] ?: 'ANANTA'); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right font-weight-bold" style="color: #0f172a;">
                                            <?php echo formatCurrency((float)$inv['real_fund_usd'], $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 text-right font-weight-semibold" style="color: #16a34a;">
                                            <?php echo formatCurrency((float)$inv['bonus_amount_usd'], $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center font-weight-semibold" style="color: #475569;">
                                            <?php echo $inv['lock_period_months']; ?> Months
                                        </td>
                                        <td class="py-3 px-3 text-center text-muted small">
                                            <?php echo htmlspecialchars($inv['maturity_date'] ?: 'N/A'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <?php if (($inv['capital_withdrawal_status'] ?? '') === 'WITHDRAWN'): ?>
                                                <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;">Withdrawn</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill px-3 py-1" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04; font-weight: 700;">Active / Locked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-center text-muted small"><?php echo htmlspecialchars($inv['date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="zmdi zmdi-file-text zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No investment records found for the selected date range.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Unlock Access Debit History Table Card -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-lock-open mr-2" style="color: #16a34a;"></i> Unlock Access Debit History ($11 / ₹990 Activation)
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="py-3 px-4 text-center">#</th>
                                <th class="py-3 px-3">Transaction ID</th>
                                <th class="py-3 px-3 text-right">Amount Debited</th>
                                <th class="py-3 px-3">Subject / Description</th>
                                <th class="py-3 px-3 text-center">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            <?php if (!empty($unlockDebits)): ?>
                                <?php $sr = 1; foreach ($unlockDebits as $deb): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 px-4 text-center font-weight-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                        <td class="py-3 px-3 font-weight-bold" style="color: #0284c7;">#<?php echo $deb['id']; ?></td>
                                        <td class="py-3 px-3 text-right font-weight-bold" style="color: #dc2626;">
                                            <?php echo formatCurrency(((float)$deb['amount']) / 90, $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 font-weight-semibold" style="color: #334155;">
                                            <?php echo htmlspecialchars($deb['subject']); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center text-muted small">
                                            <?php echo htmlspecialchars($deb['created_date'] . ' ' . $deb['time']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="zmdi zmdi-shield-check zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No Unlock Access debit history records found for the selected date range.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'common/footer.php'; ?>

