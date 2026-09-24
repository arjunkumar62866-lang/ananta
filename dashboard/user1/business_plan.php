<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$pdfPath = '/assets/docs/Ananta_Business_Plan.pdf';
$pdfExists = file_exists(__DIR__ . '/../../assets/docs/Ananta_Business_Plan.pdf');

// Safe download handler preventing path traversal
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    if ($pdfExists) {
        $realPath = realpath(__DIR__ . '/../../assets/docs/Ananta_Business_Plan.pdf');
        $allowedDir = realpath(__DIR__ . '/../../assets/docs');
        
        if ($realPath && strpos($realPath, $allowedDir) === 0) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Ananta_Business_Plan.pdf"');
            header('Content-Length: ' . filesize($realPath));
            readfile($realPath);
            exit();
        }
    }
}
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">Business Plan</h4>
                <p class="text-muted small mb-0">Official ANANTA Multi Trade presentation document & income stream breakdown</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">Business Plan</span>
                </div>
            </nav>
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-file-text mr-2" style="color: #0284c7;"></i> ANANTA Official Business Presentation
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center py-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; width: 90px; height: 90px;">
                        <i class="zmdi zmdi-file-text zmdi-hc-4x"></i>
                    </div>
                    <h4 class="font-weight-bold mb-2" style="color: #0f172a;">ANANTA Nivesh & Fintech Compensation Plan</h4>
                    <p class="text-muted max-w-lg mx-auto" style="max-width: 550px;">
                        Download or preview the complete official ANANTA presentation deck covering package features, lock periods, and all 7 income streams.
                    </p>

                    <div class="mt-4">
                        <?php if ($pdfExists): ?>
                            <a href="business_plan.php?action=download" class="btn btn-lg px-4 py-3 font-weight-bold mr-2 mb-2" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: #ffffff; border-radius: 12px; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);">
                                <i class="zmdi zmdi-download mr-2"></i> Download Business Plan PDF
                            </a>
                            <a href="/assets/docs/Ananta_Business_Plan.pdf" target="_blank" class="btn btn-lg px-4 py-3 font-weight-bold btn-outline-secondary mb-2" style="border-radius: 12px;">
                                <i class="zmdi zmdi-eye mr-2"></i> Preview PDF
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info d-inline-block text-left p-3 mb-0" style="max-width: 650px; border-radius: 12px; background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.2); color: #0369a1;">
                                <i class="zmdi zmdi-info-outline mr-2" style="font-size: 18px;"></i>
                                <strong>Official Business Plan Document Status</strong><br>
                                The downloadable PDF presentation deck is scheduled for update. You can review the complete Package & Income system rules in the breakdown cards below.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr style="border-top: 1px solid #f1f5f9;" class="my-4">

                <!-- 2 Column Breakdown Row -->
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 16px;">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <h6 class="font-weight-bold mb-0" style="color: #0f172a;">
                                    <i class="zmdi zmdi-card mr-2" style="color: #0284c7;"></i> Ananta Nivesh Packages
                                </h6>
                            </div>
                            <div class="card-body p-4" style="color: #334155; font-size: 14px; line-height: 1.7;">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="zmdi zmdi-check-circle text-success mr-2"></i><strong>BASIC:</strong> $145 – $1,000 (48 Months Lock, 15% Deduction)</li>
                                    <li class="mb-2"><i class="zmdi zmdi-check-circle text-success mr-2"></i><strong>ADVANCE:</strong> $1,001 – $12,500 (48 Months Lock, 15% Deduction)</li>
                                    <li class="mb-2"><i class="zmdi zmdi-check-circle text-success mr-2"></i><strong>PREMIUM:</strong> $12,501+ No Limit (48 Months Lock, 15% Deduction)</li>
                                    <li class="mb-2"><i class="zmdi zmdi-check-circle text-success mr-2"></i><strong>30% BONUS:</strong> $145+ (6 Months Lock, 30% Bonus Wallet)</li>
                                    <li class="mb-0"><i class="zmdi zmdi-check-circle text-success mr-2"></i><strong>TOUR:</strong> $145+ (48 Months Lock, 15% Deduction)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 16px;">
                            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                                <h6 class="font-weight-bold mb-0" style="color: #0f172a;">
                                    <i class="zmdi zmdi-trending-up mr-2" style="color: #16a34a;"></i> 7 Income Streams
                                </h6>
                            </div>
                            <div class="card-body p-4" style="color: #334155; font-size: 14px; line-height: 1.7;">
                                <ol class="pl-3 mb-0">
                                    <li class="mb-2"><strong>Profit Income</strong> (Daily Yield Up to Cap)</li>
                                    <li class="mb-2"><strong>Profit Sharing Income</strong> (Levels 1–15 Monthly)</li>
                                    <li class="mb-2"><strong>Direct Bonus 10M</strong> (10 Months Installment)</li>
                                    <li class="mb-2"><strong>2% Mentor Income</strong> (Monthly Active Directs Share)</li>
                                    <li class="mb-2"><strong>Rank & Rewards</strong> (Qualifications & Milestones)</li>
                                    <li class="mb-2"><strong>VIP Club Income</strong> (Levels 1–10 Weaker Business)</li>
                                    <li class="mb-0"><strong>Company Turnover Income</strong> (Global Dividend)</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include 'common/footer.php'; ?>

