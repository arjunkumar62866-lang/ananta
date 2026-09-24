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

// Fetch Company Turnover Income history
$table = "tbl_daily_levelinc";
$turnoverTotal = incometotalnew($pdo, $table, $userid, 'Company Turnover Income');
$turnoverTotal = round((float)($turnoverTotal ?? 0), 2);

$stmt = $pdo->prepare("SELECT * FROM $table WHERE user_id = :userid AND (subject LIKE '%Turnover%' OR subject LIKE '%Company%') ORDER BY id DESC");
$stmt->execute([':userid' => $userid]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">Company Turnover Income</h4>
                <p class="text-muted small mb-0">Global company turnover share dividend statement</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">User Growth</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">Company Turnover Income</span>
                </div>
            </nav>
        </div>

        <!-- Metric Stat Banner -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.08) 0%, rgba(2, 132, 199, 0.08) 100%), #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 20px;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-3 mr-3" style="background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); color: #ffffff; box-shadow: 0 8px 20px rgba(147, 51, 234, 0.3);">
                        <i class="zmdi zmdi-balance-wallet zmdi-hc-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase font-weight-bold">Total Company Turnover Share</span>
                        <h3 class="mb-0 font-weight-bold" style="color: #0f172a;">
                            <?php echo formatCurrency($turnoverTotal, $selectedCurrency); ?>
                        </h3>
                    </div>
                </div>
                <div>
                    <a href="user_growth.php" class="btn btn-outline-primary px-4 py-2 font-weight-bold" style="border-radius: 12px;">
                        <i class="zmdi zmdi-eye mr-1"></i> View All Growth Income
                    </a>
                </div>
            </div>
        </div>

        <!-- Statement Table Card -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-receipt mr-2" style="color: #9333ea;"></i> Company Turnover Income Statement
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <tr>
                                <th class="py-3 px-4 text-center">#</th>
                                <th class="py-3 px-3">Transaction Subject</th>
                                <th class="py-3 px-3 text-right">Amount Credited</th>
                                <th class="py-3 px-3 text-center">Credit Date</th>
                                <th class="py-3 px-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            <?php if (!empty($history)): ?>
                                <?php $sr = 1; foreach ($history as $h): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 px-4 text-center font-weight-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                        <td class="py-3 px-3 font-weight-semibold" style="color: #0f172a;">
                                            <?php echo htmlspecialchars($h['subject'] ?? 'Company Turnover Income'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                            <?php echo formatCurrency((float)($h['amount'] ?? 0), $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center text-muted small">
                                            <?php echo htmlspecialchars($h['created_date'] ?? $h['date'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;">Credited</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="zmdi zmdi-file-text zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No company turnover income transactions found yet.
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
