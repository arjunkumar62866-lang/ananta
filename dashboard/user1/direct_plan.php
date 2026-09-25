<?php
ob_start();
session_start();

// Direct Plan user route has been disabled on the user side.
header("Location: index.php");
exit();
?>

// Calculate scheduled direct bonus summary
$stmt_db = $pdo->prepare("
    SELECT 
        COALESCE(SUM(total_bonus), 0) as total_scheduled,
        COALESCE(SUM(CASE WHEN status = 'CREDITED' THEN installment_amount ELSE 0 END), 0) as total_credited,
        COALESCE(SUM(CASE WHEN status = 'PENDING' THEN installment_amount ELSE 0 END), 0) as total_pending
    FROM tbl_direct_bonus_schedule 
    WHERE beneficiary_id = :userid
");
$stmt_db->execute([':userid' => $userid]);
$res_db = $stmt_db->fetch(PDO::FETCH_ASSOC);

$total_scheduled = round((float)($res_db['total_scheduled'] ?? 0), 2);
$total_credited = round((float)($res_db['total_credited'] ?? 0), 2);
$total_pending = round((float)($res_db['total_pending'] ?? 0), 2);

$activeCurrency = getUserCurrency();
?>

<div class="content-wrapper" style="background-color: #f4f6f8 !important;">
    <div class="container-fluid">
        
        <!-- Page Title & Breadcrumb Header -->
        <div class="row pt-3 pb-2 align-items-center">
            <div class="col-sm-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 class="mb-1" style="font-weight: 800; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">Direct Plan</h3>
                        <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap; margin-top: 4px;">
                            <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                            <span style="color: #94a3b8; font-weight: 400;">/</span>
                            <span style="color: #0f172a; font-weight: 700;">Direct Plan</span>
                        </div>
                    </div>
                    <div>
                        <span class="badge" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; font-size: 12px; font-weight: 700; border-radius: 100px; padding: 6px 16px; border: 1px solid rgba(2, 132, 199, 0.3);">
                            <i class="fa fa-flash me-1"></i> DIRECT PLAN REWARDS
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards Grid (Matching Dashboard FinTech Card Design) -->
        <div class="row mt-2">
            <div class="col-12 col-md-4 mb-4">
                <div class="ananta-fintech-card p-4 d-flex align-items-center" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%), #ffffff; border: 1.5px solid rgba(2, 132, 199, 0.3) !important;">
                    <div class="wallet-icon d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: rgba(2, 132, 199, 0.15); border-radius: 16px; flex-shrink: 0;">
                        <i class="fa fa-calculator" style="font-size: 22px; color: #0284c7;"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Scheduled Bonus</span>
                        <h4 class="font-weight-bold mb-0" style="color: #0284c7; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;">
                            <?php echo formatCurrency($total_scheduled); ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-4">
                <div class="ananta-fintech-card p-4 d-flex align-items-center" style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%), #ffffff; border: 1.5px solid rgba(22, 163, 74, 0.3) !important;">
                    <div class="wallet-icon d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: rgba(22, 163, 74, 0.15); border-radius: 16px; flex-shrink: 0;">
                        <i class="fa fa-check-circle" style="font-size: 22px; color: #16a34a;"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Credited Bonus</span>
                        <h4 class="font-weight-bold mb-0" style="color: #16a34a; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;">
                            <?php echo formatCurrency($total_credited); ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-4">
                <div class="ananta-fintech-card p-4 d-flex align-items-center" style="background: linear-gradient(135deg, rgba(234, 179, 8, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%), #ffffff; border: 1.5px solid rgba(234, 179, 8, 0.3) !important;">
                    <div class="wallet-icon d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px; background: rgba(234, 179, 8, 0.15); border-radius: 16px; flex-shrink: 0;">
                        <i class="fa fa-clock-o" style="font-size: 22px; color: #ca8a04;"></i>
                    </div>
                    <div>
                        <span class="text-muted small font-weight-bold d-block text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Pending Bonus</span>
                        <h4 class="font-weight-bold mb-0" style="color: #ca8a04; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800;">
                            <?php echo formatCurrency($total_pending); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Direct Referrals Qualification Table (Matching High-Contrast Modern Dashboard Card) -->
        <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-transparent py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-users" style="color: #0284c7; font-size: 18px;"></i>
                    <h5 class="mb-0" style="font-weight: 800; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px;">
                        Direct Plan Referral Qualifications
                    </h5>
                </div>
                <span class="badge" style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 12px; border-radius: 8px; padding: 6px 12px;">
                    Total Directs: <?php echo count($directDetails); ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <tr style="color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                <th class="py-3">#</th>
                                <th class="py-3">User ID</th>
                                <th class="py-3">User Name</th>
                                <th class="py-3">Activation ($11 / ₹990)</th>
                                <th class="py-3">Total Investment</th>
                                <th class="py-3">Qualification Status</th>
                                <th class="py-3">Details</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px; font-weight: 500;">
                            <?php if (!empty($directDetails)): ?>
                                <?php $sr = 1; foreach ($directDetails as $d): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 fw-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                        <td class="py-3">
                                            <span style="color: #0284c7; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">
                                                <?php echo htmlspecialchars($d['userid']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3" style="color: #0f172a; font-weight: 700;">
                                            <?php echo htmlspecialchars($d['name']); ?>
                                        </td>
                                        <td class="py-3">
                                            <?php if ($d['activation_status'] === 'YES'): ?>
                                                <span class="badge" style="background: rgba(22, 163, 74, 0.12); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.3); border-radius: 100px; padding: 5px 12px; font-weight: 700; font-size: 11px;">
                                                    <i class="fa fa-check-circle me-1"></i> ACTIVE
                                                </span>
                                            <?php else: ?>
                                                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 100px; padding: 5px 12px; font-weight: 700; font-size: 11px;">
                                                    <i class="fa fa-times-circle me-1"></i> INACTIVE
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3" style="color: #0f172a; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">
                                            <?php echo formatCurrency($d['total_investment']); ?>
                                        </td>
                                        <td class="py-3">
                                            <?php if ($d['is_qualified']): ?>
                                                <span class="badge" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; border: 1px solid rgba(22, 163, 74, 0.3); border-radius: 100px; padding: 6px 14px; font-weight: 800; font-size: 11.5px;">
                                                    <i class="fa fa-star me-1"></i> QUALIFIED
                                                </span>
                                            <?php else: ?>
                                                <span class="badge" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.3); border-radius: 100px; padding: 6px 14px; font-weight: 800; font-size: 11.5px;">
                                                    <i class="fa fa-clock-o me-1"></i> PENDING
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-muted" style="font-size: 12.5px;">
                                            <?php echo htmlspecialchars($d['reason']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-4 text-muted" style="font-weight: 600;">No direct referrals found.</td>
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

