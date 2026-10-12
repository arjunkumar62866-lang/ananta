<?php
/**
 * scratch/audit_all_users_growth.php
 * Comprehensive Read-Only Audit of All Users for User Growth Wallet & 7 Income Streams
 */

require_once __DIR__ . '/../common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

$stmt = $pdo->query("
    SELECT 
        userid, name, active, status, joining_date,
        amount, 
        user_growth_wallet,
        profit_income_wallet,
        profit_sharing_wallet,
        direct_bonus_wallet,
        mentor_income_wallet,
        rank_reward_wallet,
        vip_club_wallet,
        company_turnover_wallet
    FROM user 
    ORDER BY id ASC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = count($users);
$noDiscrepancy = 0;
$positiveDiscrepancies = 0;
$negativeDiscrepancies = 0;
$displayOnlyIssues = 0;
$confirmedStaleSummary = 0;
$sourceRecordIssues = 0;
$unresolvedCases = 0;

$auditRows = [];

foreach ($users as $u) {
    $uid = (string)$u['userid'];
    $storedUG = (float)($u['user_growth_wallet'] ?? 0.00);
    $storedAmount = (float)($u['amount'] ?? 0.00);

    // Call live dashboard summary logic
    $summary = getUserIncomeWalletSummary($uid, $pdo);
    $displayedUG = (float)($summary['total_income_balance'] ?? 0.00);

    $pi  = (float)($summary['profit_income'] ?? 0.00);
    $ps  = (float)($summary['profit_sharing'] ?? 0.00);
    $db  = (float)($summary['direct_bonus'] ?? 0.00);
    $mi  = (float)($summary['mentor_income'] ?? 0.00);
    $rr  = (float)($summary['rank_reward'] ?? 0.00);
    $vip = (float)($summary['vip_club'] ?? 0.00);
    $ct  = (float)($summary['company_turnover'] ?? 0.00);

    // Independent sum of the 7 verified income streams
    $expectedTotal = round($pi + $ps + $db + $mi + $rr + $vip + $ct, 2);

    $diffStored   = round($storedUG - $expectedTotal, 2);
    $diffDisplayed = round($displayedUG - $expectedTotal, 2);

    // Classification
    $category = 'No Discrepancy';
    $recCorrection = 'None (Exact match)';

    if ($diffStored == 0 && $diffDisplayed == 0) {
        $noDiscrepancy++;
    } else {
        if ($diffStored > 0) {
            $positiveDiscrepancies++;
        } elseif ($diffStored < 0) {
            $negativeDiscrepancies++;
        }

        if ($diffDisplayed != 0 && $diffStored == 0) {
            $displayOnlyIssues++;
            $category = 'Confirmed display-only bug';
            $recCorrection = 'Fix display calculation in dashboard function';
        } elseif ($diffStored != 0 && $diffDisplayed == 0) {
            // Display is now clean (sum of 7 streams), but stored column has stale/historical balance
            $confirmedStaleSummary++;
            $category = 'Confirmed stale/incorrect user_growth_wallet';
            $recCorrection = "Update stored user_growth_wallet from {$storedUG} to {$expectedTotal} (Safe summary sync)";
        } elseif ($diffDisplayed != 0 && $diffStored != 0) {
            $sourceRecordIssues++;
            $category = 'Duplicate or incorrectly recorded income';
            $recCorrection = 'Investigate source transaction/ledger records';
        } else {
            $unresolvedCases++;
            $category = 'Unresolved';
            $recCorrection = 'Requires manual investigation';
        }
    }

    $auditRows[] = [
        'userid'         => $uid,
        'name'           => $u['name'],
        'status'         => ($u['active'] == '1' || $u['status'] == 1) ? 'Active' : 'Inactive',
        'pi'             => $pi,
        'ps'             => $ps,
        'db'             => $db,
        'mi'             => $mi,
        'rr'             => $rr,
        'vip'            => $vip,
        'ct'             => $ct,
        'expected'       => $expectedTotal,
        'stored_ug'      => $storedUG,
        'displayed_ug'   => $displayedUG,
        'diff_stored'    => $diffStored,
        'diff_displayed' => $diffDisplayed,
        'net_balance'    => $storedAmount,
        'category'       => $category,
        'recommendation' => $recCorrection
    ];
}

// Generate Markdown Audit Report
$md = "# User Growth Wallet & 7 Income Streams — Full Database Audit Report\n\n";
$md .= "**Date & Time:** " . date('Y-m-d H:i:s T') . "\n";
$md .= "**Environment:** Isolated Audit Environment (Public HTML Database)\n";
$md .= "**Mode:** PHASE 1 — READ-ONLY (Zero DB mutations performed)\n\n";

$md .= "## 1. Executive Summary\n\n";
$md .= "| Metric | Count |\n";
$md .= "|---|---|\n";
$md .= "| **Total Users Audited** | **{$totalUsers}** |\n";
$md .= "| **Users with No Discrepancy** | **{$noDiscrepancy}** |\n";
$md .= "| **Users with Positive Discrepancies** (`stored > expected`) | **{$positiveDiscrepancies}** |\n";
$md .= "| **Users with Negative Discrepancies** (`stored < expected`) | **{$negativeDiscrepancies}** |\n";
$md .= "| **Category 1: Confirmed Display-Only Issues** | **{$displayOnlyIssues}** |\n";
$md .= "| **Category 2: Confirmed Stale/Incorrect `user_growth_wallet`** | **{$confirmedStaleSummary}** |\n";
$md .= "| **Category 3: Duplicate / Source Record Issues** | **{$sourceRecordIssues}** |\n";
$md .= "| **Category 4: Unresolved Cases** | **{$unresolvedCases}** |\n\n";

$totalProposedCorrection = 0.0;
foreach ($auditRows as $r) {
    if ($r['diff_stored'] != 0) {
        $totalProposedCorrection += abs($r['diff_stored']);
    }
}
$md .= "**Total Proposed Discrepancy Value:** $" . number_format($totalProposedCorrection, 2) . "\n\n";

$md .= "## 2. Discrepancy Details (Users with Differences)\n\n";
$md .= "| User ID | Name | Status | 7 Streams Breakdown (PI / PS / DB / MI / RR / VIP / CT) | Expected Sum | Stored `user_growth_wallet` | Displayed User Growth | Diff (Stored - Exp) | Category | Recommended Action |\n";
$md .= "|---|---|---|---|---|---|---|---|---|---|\n";

$discrepancyCount = 0;
foreach ($auditRows as $r) {
    if ($r['category'] !== 'No Discrepancy') {
        $discrepancyCount++;
        $breakdownStr = "{$r['pi']} / {$r['ps']} / {$r['db']} / {$r['mi']} / {$r['rr']} / {$r['vip']} / {$r['ct']}";
        $md .= "| `{$r['userid']}` | " . htmlspecialchars($r['name']) . " | {$r['status']} | {$breakdownStr} | **\${$r['expected']}** | \${$r['stored_ug']} | \${$r['displayed_ug']} | **\${$r['diff_stored']}** | {$r['category']} | {$r['recommendation']} |\n";
    }
}

if ($discrepancyCount === 0) {
    $md .= "| - | None | - | - | - | - | - | - | All user records match exact 7 streams | No action required |\n";
}

$md .= "\n\n## 3. Complete User Roster (All {$totalUsers} Users)\n\n";
$md .= "| User ID | Status | Expected Total ($) | Stored `user_growth_wallet` ($) | Displayed ($) | Net Balance ($) | Discrepancy Category |\n";
$md .= "|---|---|---|---|---|---|---|\n";
foreach ($auditRows as $r) {
    $md .= "| `{$r['userid']}` | {$r['status']} | \${$r['expected']} | \${$r['stored_ug']} | \${$r['displayed_ug']} | \${$r['net_balance']} | {$r['category']} |\n";
}

file_put_contents(__DIR__ . '/user_growth_full_audit.md', $md);

// Generate CSV export
$csvFile = __DIR__ . '/user_growth_full_audit.csv';
$fp = fopen($csvFile, 'w');
fputcsv($fp, [
    'User ID', 'Name', 'Status', 'Profit Income', 'Profit Sharing', 'Direct Bonus', 
    'Mentor Income', 'Rank Reward', 'VIP Club', 'Company Turnover', 'Expected 7 Streams Sum', 
    'Stored user_growth_wallet', 'Displayed User Growth', 'Diff Stored', 'Diff Displayed', 
    'Net Balance Amount', 'Category', 'Recommendation'
]);
foreach ($auditRows as $r) {
    fputcsv($fp, [
        $r['userid'], $r['name'], $r['status'], $r['pi'], $r['ps'], $r['db'],
        $r['mi'], $r['rr'], $r['vip'], $r['ct'], $r['expected'],
        $r['stored_ug'], $r['displayed_ug'], $r['diff_stored'], $r['diff_displayed'],
        $r['net_balance'], $r['category'], $r['recommendation']
    ]);
}
fclose($fp);

echo "AUDIT COMPLETE!\n";
echo "Total Users Audited: {$totalUsers}\n";
echo "No Discrepancy: {$noDiscrepancy}\n";
echo "Confirmed Stale Summary: {$confirmedStaleSummary}\n";
echo "Display Only Issues: {$displayOnlyIssues}\n";
echo "Source Record Issues: {$sourceRecordIssues}\n";
echo "Report written to scratch/user_growth_full_audit.md\n";
echo "CSV written to scratch/user_growth_full_audit.csv\n";
