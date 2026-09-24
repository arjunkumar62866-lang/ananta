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
$rightMembers = getUserTeamMembersDetailed($userid, 'RIGHT', $pdo);

$totalRight = count($rightMembers);
$activeRight = 0;
$totalRightVolumeUSD = 0;

foreach ($rightMembers as $m) {
    if (($m['status'] ?? '') === 'Active') {
        $activeRight++;
    }
    $totalRightVolumeUSD += (float)($m['investment_usd'] ?? 0);
}
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3" style="border-bottom: 2px solid #e2e8f0;">
            <div class="mb-2 mb-md-0">
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="zmdi zmdi-long-arrow-right mr-2" style="color: #9333ea;"></i> Right Team Downline
                </h4>
                <p class="small mb-0" style="color: #64748b !important;">Detailed list of all members in your right binary organization</p>
            </div>
            <div>
                <nav aria-label="breadcrumb">
                    <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                        <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #475569; font-weight: 600;">My Team</span>
                        <span style="color: #94a3b8; font-weight: 400;">/</span>
                        <span style="color: #0f172a; font-weight: 700;">Right Team</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- 3 Color Stat Cards: Black, Green & Purple -->
        <div class="row mb-4">
            <!-- Card 1: Black Accent -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #0f172a !important; border-radius: 16px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: #f1f5f9; color: #0f172a;">
                            <i class="zmdi zmdi-accounts-list zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="small text-uppercase font-weight-bold" style="color: #64748b; letter-spacing: 0.5px;">Total Right Team</span>
                            <h3 class="mb-0 font-weight-bold" style="color: #0f172a;"><?php echo number_format($totalRight); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Green Accent -->
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #16a34a !important; border-radius: 16px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: rgba(22, 163, 74, 0.12); color: #16a34a;">
                            <i class="zmdi zmdi-check-all zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="small text-uppercase font-weight-bold" style="color: #16a34a; letter-spacing: 0.5px;">Active Right Members</span>
                            <h3 class="mb-0 font-weight-bold" style="color: #16a34a;"><?php echo number_format($activeRight); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Purple Accent -->
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-left: 5px solid #9333ea !important; border-radius: 16px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: rgba(147, 51, 234, 0.12); color: #9333ea;">
                            <i class="zmdi zmdi-balance-wallet zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="small text-uppercase font-weight-bold" style="color: #9333ea; letter-spacing: 0.5px;">Right Team Business</span>
                            <h3 class="mb-0 font-weight-bold" style="color: #9333ea;">
                                <?php echo formatCurrency($totalRightVolumeUSD, $selectedCurrency); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Data Table Card -->
        <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 2px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-long-arrow-right mr-2" style="color: #9333ea;"></i> Right Team Members List
                </h6>
                <span class="badge badge-pill px-3 py-2 font-weight-bold" style="background: rgba(147, 51, 234, 0.1); color: #9333ea; border: 1px solid rgba(147, 51, 234, 0.2);">
                    Total: <?php echo $totalRight; ?> Members
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                        <thead style="background: #f8fafc; color: #0f172a; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="py-3 px-4 text-center" style="color: #0f172a; font-weight: 800;">#</th>
                                <th class="py-3 px-3" style="color: #0f172a; font-weight: 800;">User ID</th>
                                <th class="py-3 px-3" style="color: #0f172a; font-weight: 800;">Name</th>
                                <th class="py-3 px-3 text-center" style="color: #0f172a; font-weight: 800;">Level</th>
                                <th class="py-3 px-3 text-center" style="color: #0f172a; font-weight: 800;">Rank</th>
                                <th class="py-3 px-3 text-center" style="color: #0f172a; font-weight: 800;">Package</th>
                                <th class="py-3 px-3 text-right" style="color: #0f172a; font-weight: 800;">Investment</th>
                                <th class="py-3 px-3 text-center" style="color: #0f172a; font-weight: 800;">Joining Date</th>
                                <th class="py-3 px-3 text-center" style="color: #0f172a; font-weight: 800;">Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px; color: #0f172a;">
                            <?php if (!empty($rightMembers)): ?>
                                <?php foreach ($rightMembers as $m): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="py-3 px-4 text-center font-weight-bold" style="color: #475569;"><?php echo $m['sr']; ?></td>
                                        <td class="py-3 px-3">
                                            <span class="font-weight-bold" style="color: #9333ea;"><?php echo htmlspecialchars($m['userid']); ?></span>
                                        </td>
                                        <td class="py-3 px-3 font-weight-bold" style="color: #0f172a;">
                                            <?php echo htmlspecialchars($m['name']); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge px-2 py-1" style="background: #f1f5f9; color: #0f172a; font-weight: 700; border-radius: 6px;">
                                                Lvl <?php echo $m['level']; ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge px-3 py-1" style="background: rgba(147, 51, 234, 0.1); color: #9333ea; font-weight: 700; border-radius: 6px;">
                                                <?php echo htmlspecialchars($m['rank'] ?: 'Member'); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center font-weight-semibold" style="color: #334155;">
                                            <?php echo htmlspecialchars($m['package'] ?: 'N/A'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                            <?php echo formatCurrency((float)($m['investment_usd'] ?? 0), $selectedCurrency); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center font-weight-medium" style="color: #475569;">
                                            <?php echo htmlspecialchars($m['joining_date'] ?: 'N/A'); ?>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <?php if (($m['status'] ?? '') === 'Active'): ?>
                                                <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 800;">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill px-3 py-1" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; font-weight: 800;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5" style="color: #64748b;">
                                        <i class="zmdi zmdi-long-arrow-right zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                        No team members found in your Right Team yet.
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
