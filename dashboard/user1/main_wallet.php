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
$selectedCurrency = getUserCurrency();

// Fetch single source of truth wallet balance & transactions
$mainBalanceUSD = getUserWalletBalance($userid, $pdo);

$fromDate   = $_GET['from_date'] ?? '';
$toDate     = $_GET['to_date'] ?? '';
$typeFilter = $_GET['type'] ?? '';

$transactions = getUserMainWalletTransactions($userid, $fromDate, $toDate, $typeFilter, $pdo);

$totalCreditINR = 0.0;
$totalDebitINR  = 0.0;

foreach ($transactions as $t) {
    $amt = (float)($t['amount'] ?? 0);
    $type = strtoupper($t['type'] ?? '');
    if ($type === 'CREDIT') {
        $totalCreditINR += $amt;
    } elseif ($type === 'DEBIT') {
        $totalDebitINR += $amt;
    }
}
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3" style="border-bottom: 2px solid #e2e8f0;">
            <div class="mb-2 mb-md-0">
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="zmdi zmdi-balance-wallet mr-2" style="color: #0284c7;"></i> Main Wallet
                </h4>
                <p class="small mb-0" style="color: #64748b !important;">Complete Main Wallet balance overview and full transaction history</p>
            </div>
            <div>
                <nav aria-label="breadcrumb">
                    <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                        <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #475569; font-weight: 600;">Wallet</span>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #0f172a; font-weight: 700;">Main Wallet</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- 3 Stat Cards Row: Main Wallet Balance, Total Credit, Total Debit -->
        <div class="row mb-4">
            <!-- Main Wallet Available Balance Card -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #0284c7 !important; border-radius: 16px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                                <i class="zmdi zmdi-balance-wallet zmdi-hc-2x"></i>
                            </div>
                            <div>
                                <span class="small text-uppercase font-weight-bold" style="color: #64748b; letter-spacing: 0.5px;">Current Main Balance</span>
                                <h3 class="mb-0 font-weight-bold" style="color: #0f172a;">
                                    <?php echo formatCurrency($mainBalanceUSD, $selectedCurrency); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Credit History Card -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #16a34a !important; border-radius: 16px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3" style="background: rgba(22, 163, 74, 0.12); color: #16a34a;">
                                <i class="zmdi zmdi-trending-up zmdi-hc-2x"></i>
                            </div>
                            <div>
                                <span class="small text-uppercase font-weight-bold" style="color: #16a34a; letter-spacing: 0.5px;">Filtered Total Credit</span>
                                <h3 class="mb-0 font-weight-bold" style="color: #16a34a;">
                                    <?php echo formatCurrency(parseInputToUSD($totalCreditINR, 'INR'), $selectedCurrency); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Debit History Card -->
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #dc2626 !important; border-radius: 16px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 mr-3" style="background: rgba(220, 38, 38, 0.12); color: #dc2626;">
                                <i class="zmdi zmdi-trending-down zmdi-hc-2x"></i>
                            </div>
                            <div>
                                <span class="small text-uppercase font-weight-bold" style="color: #dc2626; letter-spacing: 0.5px;">Filtered Total Debit</span>
                                <h3 class="mb-0 font-weight-bold" style="color: #dc2626;">
                                    <?php echo formatCurrency(parseInputToUSD($totalDebitINR, 'INR'), $selectedCurrency); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range & Type Filter Form -->
        <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-filter-list mr-2" style="color: #0284c7;"></i> Transaction Filter
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="main_wallet.php">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3 mb-3 mb-md-0">
                            <label for="from_date" class="font-weight-bold small text-uppercase" style="color: #475569;">From Date</label>
                            <input type="date" class="form-control form-control-lg" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>" style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
                        </div>
                        <div class="form-group col-md-3 mb-3 mb-md-0">
                            <label for="to_date" class="font-weight-bold small text-uppercase" style="color: #475569;">To Date</label>
                            <input type="date" class="form-control form-control-lg" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>" style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
                        </div>
                        <div class="form-group col-md-3 mb-3 mb-md-0">
                            <label for="type" class="font-weight-bold small text-uppercase" style="color: #475569;">Transaction Type</label>
                            <select class="form-control form-control-lg" id="type" name="type" style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 14px;">
                                <option value="">All Transactions</option>
                                <option value="Credit" <?php echo ($typeFilter === 'Credit') ? 'selected' : ''; ?>>Credit Only</option>
                                <option value="Debit" <?php echo ($typeFilter === 'Debit') ? 'selected' : ''; ?>>Debit Only</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-block py-2 font-weight-bold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border-radius: 10px; height: 48px;">
                                    <i class="zmdi zmdi-search mr-1"></i> Filter
                                </button>
                                <?php if (!empty($fromDate) || !empty($toDate) || !empty($typeFilter)): ?>
                                    <a href="main_wallet.php" class="btn btn-light py-2 font-weight-bold d-flex align-items-center justify-content-center" style="border-radius: 10px; height: 48px; border: 1px solid #cbd5e1; min-width: 90px;" title="Reset Filter">
                                        Reset
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Statement Table Card -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 2px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-receipt mr-2" style="color: #0284c7;"></i> Main Wallet Transaction Statement
                </h6>
                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                    Total: <?php echo count($transactions); ?> Records
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; color: #0f172a; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="py-3 px-4 text-center" style="font-weight: 800;">#</th>
                                <th class="py-3 px-3" style="font-weight: 800;">Txn ID</th>
                                <th class="py-3 px-3 text-center" style="font-weight: 800;">Type</th>
                                <th class="py-3 px-3 text-right" style="font-weight: 800;">Amount</th>
                                <th class="py-3 px-3" style="font-weight: 800;">Description / Subject</th>
                                <th class="py-3 px-3 text-center" style="font-weight: 800;">Date & Time</th>
                                <th class="py-3 px-3 text-center" style="font-weight: 800;">Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color: #0f172a;">
                            <?php if (!empty($transactions)): ?>
                                <?php $sr = 1; foreach ($transactions as $t): 
                                    $isCredit = (strtoupper($t['type'] ?? '') === 'CREDIT');
                                    $amtINR = (float)($t['amount'] ?? 0);
                                    $amtUSD = parseInputToUSD($amtINR, 'INR', $pdo);
                                ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 px-4 text-center font-weight-bold" style="color: #475569;"><?php echo $sr++; ?></td>
                                        <td class="py-3 px-3 font-weight-bold" style="color: #0284c7;">#<?php echo $t['id']; ?></td>
                                        <td class="py-3 px-3 text-center">
                                            <?php if ($isCredit): ?>
                                                <span class="badge badge-pill px-3 py-1 font-weight-bold" style="background: rgba(22, 163, 74, 0.15); color: #16a34a;">CREDIT</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill px-3 py-1 font-weight-bold" style="background: rgba(220, 38, 38, 0.15); color: #dc2626;">DEBIT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 px-3 text-right font-weight-bold" style="color: <?php echo $isCredit ? '#16a34a' : '#dc2626'; ?>;">
                                            <?php echo ($isCredit ? '+' : '-') . formatCurrency($amtUSD, $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 font-weight-semibold" style="color: #334155;">
                                            <?php echo htmlspecialchars($t['subject'] ?: 'Main Wallet Transaction'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center font-weight-medium" style="color: #64748b; font-size: 13px;">
                                            <?php echo htmlspecialchars(($t['created_date'] ?? '') . ' ' . ($t['time'] ?? '')); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <?php if (($t['status'] ?? '') == 1 || ($t['status'] ?? '') === 'SUCCESS'): ?>
                                                <span class="badge badge-pill px-3 py-1 font-weight-bold" style="background: rgba(22, 163, 74, 0.15); color: #16a34a;">COMPLETED</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill px-3 py-1 font-weight-bold" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">PENDING</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5" style="color: #64748b;">
                                        <i class="zmdi zmdi-receipt zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No Main Wallet transactions found for the selected criteria.
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
