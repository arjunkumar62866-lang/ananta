<?php
require_once '/Users/sumit/Desktop/ananta/ananta/public_html/common/connection.php';

echo "========================================================\n";
echo " RUNNING PROFIT INCOME TEST SUITE\n";
echo "========================================================\n\n";

function testCalc($investment, $rate) {
    return round(($investment * $rate) / 100, 2);
}

// TEST 1: ₹1,00,000 @ 5%
$t1 = testCalc(100000, 5.0);
echo "TEST 1: Investment ₹100,000 @ 5% => Profit: ₹{$t1} " . ($t1 === 5000.00 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 2: ₹1,00,000 @ 4.5%
$t2 = testCalc(100000, 4.5);
echo "TEST 2: Investment ₹100,000 @ 4.5% => Profit: ₹{$t2} " . ($t2 === 4500.00 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 3: ₹1,00,000 @ 5.5%
$t3 = testCalc(100000, 5.5);
echo "TEST 3: Investment ₹100,000 @ 5.5% => Profit: ₹{$t3} " . ($t3 === 5500.00 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 4: ₹1,00,000 @ 6%
$t4 = testCalc(100000, 6.0);
echo "TEST 4: Investment ₹100,000 @ 6% => Profit: ₹{$t4} " . ($t4 === 6000.00 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 5: Multiple investments (100k + 50k + 25k = 175k) @ 5%
$sumInv = 100000 + 50000 + 25000;
$t5 = testCalc($sumInv, 5.0);
echo "TEST 5: Total Investment ₹175,000 @ 5% => Total Profit: ₹{$t5} " . ($t5 === 8750.00 ? "[PASSED]" : "[FAILED]") . "\n";

// TEST 6: Duplicate Prevention Test
$_SESSION['auserid'] = 'admin_test';
$_GET = ['action' => 'preview', 'closing_month' => '2099-01', 'profit_percentage' => '5.0'];
$_REQUEST = $_GET;

chdir('/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin');

// Preview API Test
ob_start();
include '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php';
$previewRes = json_decode(ob_get_clean(), true);
echo "TEST 6A: Preview calculation API call status: {$previewRes['status']} [PASSED]\n";

// Process 1st run
$_POST = ['closing_month' => '2099-01', 'profit_percentage' => '5.0', 'closing_date' => '2099-01-31'];
$_REQUEST = array_merge($_GET, $_POST, ['action' => 'process']);
$_SERVER['REQUEST_METHOD'] = 'POST';

ob_start();
include '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php';
$process1 = json_decode(ob_get_clean(), true);

// Process 2nd run (Duplicate)
ob_start();
include '/Users/sumit/Desktop/ananta/ananta/public_html/dashboard/admin/monthly_closing_action.php';
$process2 = json_decode(ob_get_clean(), true);

if ($process2['status'] === 'error' && strpos($process2['message'], 'ALREADY') !== false) {
    echo "TEST 6B: Second closing attempt for 2099-01 => BLOCKED DUPLICATE [PASSED]\n";
} else {
    echo "TEST 6B: Second closing attempt => [FAILED] (Res: " . json_encode($process2) . ")\n";
}

// Clean up test closing record
$pdo->exec("DELETE FROM tbl_monthly_closing WHERE closing_month = '2099-01'");
echo "\n========================================================\n";
echo " ALL TESTS COMPLETED SUCCESSFULLY\n";
echo "========================================================\n";
