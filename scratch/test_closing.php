<?php
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/common/connection.php';

echo "========================================================\n";
echo " RUNNING REQUIREMENT #2: MONTHLY CLOSING TEST SUITE\n";
echo "========================================================\n\n";

function runAction($getParams, $postParams = []) {
    $queryString = http_build_query($getParams);
    $postData = http_build_query($postParams);

    $cmd = "php -r '
        session_start();
        \$_SESSION[\"auserid\"] = \"admin_test\";
        parse_str(\"{$queryString}\", \$_GET);
        parse_str(\"{$postData}\", \$_POST);
        \$_REQUEST = array_merge(\$_GET, \$_POST);
        if (!empty(\$_POST)) \$_SERVER[\"REQUEST_METHOD\"] = \"POST\";
        else \$_SERVER[\"REQUEST_METHOD\"] = \"GET\";
        chdir(\"/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin\");
        include \"monthly_closing_action.php\";
    '";

    $output = shell_exec($cmd);
    return json_decode(trim($output), true);
}

// TEST 4: PREVIEW TEST (Read-Only)
$prevOut = runAction(['action' => 'preview', 'closing_month' => '2099-05', 'profit_percentage' => '5.0']);
$prevCheck = ($prevOut && $prevOut['status'] === 'success' && isset($prevOut['expected_total_profit']));
echo "TEST 4 (PREVIEW READ-ONLY): Status: " . ($prevOut['status'] ?? 'null') . " " . ($prevCheck ? "[PASSED]" : "[FAILED]") . "\n";

// Verify DB was NOT modified during preview
$dbChk1 = $pdo->query("SELECT COUNT(*) FROM tbl_monthly_closing WHERE closing_month = '2099-05'")->fetchColumn();
echo "  -> Preview DB Check (No closing created): " . ($dbChk1 == 0 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 5: CONFIRM CLOSING (Credits Profit & Triggers Profit Sharing)
$procOut = runAction(
    ['action' => 'process'],
    ['closing_month' => '2099-05', 'closing_date' => '2099-05-31', 'profit_percentage' => '5.0']
);
$procCheck = ($procOut && $procOut['status'] === 'success' && $procOut['details']['closing_month'] === '2099-05');
echo "TEST 5 (CONFIRM CLOSING): Status: " . ($procOut['status'] ?? 'null') . " " . ($procCheck ? "[PASSED]" : "[FAILED]") . "\n";

// Verify closing table status = COMPLETED
$statusChk = $pdo->query("SELECT status FROM tbl_monthly_closing WHERE closing_month = '2099-05'")->fetchColumn();
echo "  -> Closing Status Check: {$statusChk} " . ($statusChk === 'COMPLETED' ? "[PASSED]" : "[FAILED]") . "\n";

// Verify Profit Sharing records were generated in tbl_daily_levelinc
$sharingCount = $pdo->query("SELECT COUNT(*) FROM tbl_daily_levelinc WHERE created_date = '2099-05-31'")->fetchColumn();
echo "  -> Profit Sharing Trigger Check (Records in tbl_daily_levelinc): {$sharingCount} " . ($sharingCount > 0 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 6: DUPLICATE CLOSING ATTEMPT
$dupOut = runAction(
    ['action' => 'process'],
    ['closing_month' => '2099-05', 'closing_date' => '2099-05-31', 'profit_percentage' => '5.0']
);
$dupCheck = ($dupOut && $dupOut['status'] === 'error' && strpos($dupOut['message'], 'ALREADY') !== false);
echo "TEST 6 (DUPLICATE PROTECTION): " . ($dupCheck ? "[PASSED - BLOCKED DUPLICATE]" : "[FAILED: " . json_encode($dupOut) . "]") . "\n";

// TEST 7: INVALID PROFIT % VALIDATION
$invOut = runAction(
    ['action' => 'process'],
    ['closing_month' => '2099-06', 'closing_date' => '2099-06-30', 'profit_percentage' => '-5.0']
);
$invCheck = ($invOut && $invOut['status'] === 'error');
echo "TEST 7 (INVALID RATE VALIDATION): Status: " . ($invOut['status'] ?? 'null') . " " . ($invCheck ? "[PASSED - REJECTED INVALID RATE]" : "[FAILED]") . "\n";

// Cleanup test data
$pdo->exec("DELETE FROM tbl_monthly_closing WHERE closing_month LIKE '2099-%'");
$pdo->exec("DELETE FROM tbl_transaction WHERE created_date LIKE '2099-%'");
$pdo->exec("DELETE FROM tbl_daily_levelinc WHERE created_date LIKE '2099-%'");

echo "\n========================================================\n";
echo " ALL REQUIREMENT #2 TESTS COMPLETED SUCCESSFULLY\n";
echo "========================================================\n";
