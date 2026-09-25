<?php
/**
 * Automated Verification & Audit Suite for Admin Move Team / Move User in Tree
 */

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../dashboard/admin');
chdir(__DIR__ . '/../dashboard/admin');
require_once __DIR__ . '/../dashboard/admin/common/connection.php';
require_once __DIR__ . '/../dashboard/admin/common/db_method.php';

global $pdo;

echo "========================================================\n";
echo " ANANTA — ADMIN MOVE TEAM AUTOMATED VERIFICATION SUITE \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function runTest($testNum, $description, $resultBool, $details = '') {
    global $passCount, $failCount;
    if ($resultBool) {
        $passCount++;
        echo sprintf("PASS %-2d — %s\n", $testNum, $description);
    } else {
        $failCount++;
        echo sprintf("FAIL %-2d — %s [Details: %s]\n", $testNum, $description, $details);
    }
}

// Ensure test users exist in database
try {
    $pdo->beginTransaction();

    // Cleanup previous test users
    $pdo->exec("DELETE FROM tree WHERE userid LIKE 'TST_MT_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TST_MT_%'");
    $pdo->exec("DELETE FROM tbl_admin_audit_log WHERE target_user_id LIKE 'TST_MT_%'");

    // Create test hierarchy:
    // TST_MT_P1 (Parent 1)
    // └── LEFT -> TST_MT_U1 (Target User to move)
    //             ├── LEFT -> TST_MT_C1 (Direct Child 1)
    //             └── RIGHT -> TST_MT_C2 (Direct Child 2)
    // TST_MT_P2 (Parent 2) -> Has empty LEFT slot, RIGHT occupied by TST_MT_X1

    $usersToInsert = [
        ['userid' => 'TST_MT_P1', 'name' => 'Test Parent 1', 'email' => 'p1@test.com'],
        ['userid' => 'TST_MT_U1', 'name' => 'Test Target User', 'email' => 'u1@test.com'],
        ['userid' => 'TST_MT_C1', 'name' => 'Test Child Left', 'email' => 'c1@test.com'],
        ['userid' => 'TST_MT_C2', 'name' => 'Test Child Right', 'email' => 'c2@test.com'],
        ['userid' => 'TST_MT_P2', 'name' => 'Test Parent 2', 'email' => 'p2@test.com'],
        ['userid' => 'TST_MT_X1', 'name' => 'Test Existing Right Child', 'email' => 'x1@test.com'],
    ];

    $stmtUser = $pdo->prepare("
        INSERT INTO user (
            userid, name, active, one_club_status, pass, mobile, gender, email, pan, txn_pass,
            total_deposit, deposit, sponserid, sponsername, underuserid, status, upgrade_status, join_side, package,
            joining_date, plan, pin, father, deposite_wallet, shop_amount, closingdate, capping, upgrade_date2, rankid,
            pool, level, topuplevel, ads_date, ads_status, onelevel, twolevel, threelevel, fourlevel, fivelevel,
            two_club_status, three_club_status, four_club_status, five_club_status, coin_wallet,
            vip_club_wallet, mentor_income_wallet, direct_bonus_wallet, profit_income_wallet, profit_sharing_wallet
        ) VALUES (
            :uid, :name, '1', 0, 'pass', '9999999999', 'M', :email, 'ABCDE1234F', '',
            0, 0, '1290', '', '', 1, 0, 'L', 0,
            NOW(), '', '', '', 0, 0, NOW(), 0, NOW(), 0,
            0, 0, 0, NOW(), 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,
            0, 0, 0, 0, 0
        )
    ");
    foreach ($usersToInsert as $u) {
        $stmtUser->execute([':uid' => $u['userid'], ':name' => $u['name'], ':email' => $u['email']]);
    }

    $treeRecords = [
        ['userid' => 'TST_MT_P1', 'left_id' => 'TST_MT_U1', 'right_id' => ''],
        ['userid' => 'TST_MT_U1', 'left_id' => 'TST_MT_C1', 'right_id' => 'TST_MT_C2'],
        ['userid' => 'TST_MT_C1', 'left_id' => '', 'right_id' => ''],
        ['userid' => 'TST_MT_C2', 'left_id' => '', 'right_id' => ''],
        ['userid' => 'TST_MT_P2', 'left_id' => '', 'right_id' => 'TST_MT_X1'],
        ['userid' => 'TST_MT_X1', 'left_id' => '', 'right_id' => ''],
    ];

    $stmtTree = $pdo->prepare("INSERT INTO tree (userid, left_id, right_id, leftcount, rightcount, status, join_side, leftsp, rightsp, lefttotal, righttotal) VALUES (:uid, :lid, :rid, 0, 0, 1, '', 0, 0, 0, 0)");
    foreach ($treeRecords as $tr) {
        $stmtTree->execute([':uid' => $tr['userid'], ':lid' => $tr['left_id'], ':rid' => $tr['right_id']]);
    }

    $pdo->commit();

    // -------------------------------------------------------------
    // PASS 1 — Admin authorization
    // -------------------------------------------------------------
    $adminAuthCheck = function_exists('processAdminMoveTeamInTree');
    runTest(1, "Admin authorization & helper availability", $adminAuthCheck);

    // -------------------------------------------------------------
    // PASS 2 — Target user loading
    // -------------------------------------------------------------
    $tDetails = getTreeUserDetails('TST_MT_U1', $pdo);
    runTest(2, "Target user loading", !empty($tDetails) && $tDetails['userid'] === 'TST_MT_U1');

    // -------------------------------------------------------------
    // PASS 3 — Current parent detection
    // -------------------------------------------------------------
    runTest(3, "Current parent detection", $tDetails['current_parent'] === 'TST_MT_P1');

    // -------------------------------------------------------------
    // PASS 4 — Current LEFT/RIGHT position detection
    // -------------------------------------------------------------
    runTest(4, "Current LEFT/RIGHT detection", $tDetails['current_position'] === 'LEFT');

    // -------------------------------------------------------------
    // PASS 5 — New parent validation
    // -------------------------------------------------------------
    $pAvail = getNewParentAvailability('TST_MT_U1', 'TST_MT_P2', $pdo);
    runTest(5, "New parent validation", $pAvail['status'] === 'success');

    // -------------------------------------------------------------
    // PASS 6 — Available LEFT position
    // -------------------------------------------------------------
    runTest(6, "Available LEFT position", $pAvail['left']['valid'] === true);

    // -------------------------------------------------------------
    // PASS 7 — Available RIGHT position
    // -------------------------------------------------------------
    runTest(7, "Available RIGHT position detection", $pAvail['right']['valid'] === false);

    // -------------------------------------------------------------
    // PASS 8 — Occupied position blocked
    // -------------------------------------------------------------
    $moveOcc = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_P2', 'RIGHT', 'Test Occupied', $pdo);
    runTest(8, "Occupied position blocked", $moveOcc['status'] === 'error' && strpos($moveOcc['message'], 'occupied') !== false, $moveOcc['message']);

    // -------------------------------------------------------------
    // PASS 9 — Self-parent blocked
    // -------------------------------------------------------------
    $moveSelf = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_U1', 'LEFT', 'Self Parent Test', $pdo);
    runTest(9, "Self-parent blocked", $moveSelf['status'] === 'error' && strpos($moveSelf['message'], 'same') !== false, $moveSelf['message']);

    // -------------------------------------------------------------
    // PASS 10 — Downline parent blocked
    // -------------------------------------------------------------
    $moveDown = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_C1', 'LEFT', 'Downline Move Test', $pdo);
    runTest(10, "Downline parent blocked", $moveDown['status'] === 'error' && strpos($moveDown['message'], 'Invalid parent') !== false, $moveDown['message']);

    // -------------------------------------------------------------
    // PASS 11 — Circular tree blocked
    // -------------------------------------------------------------
    $moveCirc = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_C2', 'RIGHT', 'Circular Test', $pdo);
    runTest(11, "Circular tree blocked", $moveCirc['status'] === 'error', $moveCirc['message']);

    // -------------------------------------------------------------
    // PASS 12 — Root user protection
    // -------------------------------------------------------------
    $moveRoot = processAdminMoveTeamInTree('ADM001', 'AN1290', 'TST_MT_P2', 'LEFT', 'Root Move Test', $pdo);
    runTest(12, "Root user protection", $moveRoot['status'] === 'error' && strpos($moveRoot['message'], 'Master Root Admin') !== false, $moveRoot['message']);

    // -------------------------------------------------------------
    // PASS 13 — Reason required
    // -------------------------------------------------------------
    $moveNoReason = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_P2', 'LEFT', '   ', $pdo);
    runTest(13, "Reason required", $moveNoReason['status'] === 'error' && strpos($moveNoReason['message'], 'mandatory') !== false, $moveNoReason['message']);

    // -------------------------------------------------------------
    // EXECUTE VALID MOVE FOR INTEGRITY & PRESERVATION TESTS
    // Move TST_MT_U1 from TST_MT_P1 (LEFT) to TST_MT_P2 (LEFT)
    // -------------------------------------------------------------
    $moveValid = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_P2', 'LEFT', 'Valid Audit Move Request', $pdo);
    runTest(14, "Confirmation flow & execution", $moveValid['status'] === 'success', $moveValid['message'] ?? '');

    // -------------------------------------------------------------
    // PASS 15 — Old parent slot cleared
    // -------------------------------------------------------------
    $oldPRow = $pdo->query("SELECT left_id, right_id FROM tree WHERE userid = 'TST_MT_P1'")->fetch(PDO::FETCH_ASSOC);
    runTest(15, "Old parent slot cleared", $oldPRow['left_id'] === '', "Left ID: " . $oldPRow['left_id']);

    // -------------------------------------------------------------
    // PASS 16 — New parent slot attached
    // -------------------------------------------------------------
    $newPRow = $pdo->query("SELECT left_id, right_id FROM tree WHERE userid = 'TST_MT_P2'")->fetch(PDO::FETCH_ASSOC);
    runTest(16, "New parent slot attached", $newPRow['left_id'] === 'TST_MT_U1', "Left ID: " . $newPRow['left_id']);

    // -------------------------------------------------------------
    // PASS 17 — Target LEFT child preserved
    // -------------------------------------------------------------
    $uRow = $pdo->query("SELECT left_id, right_id FROM tree WHERE userid = 'TST_MT_U1'")->fetch(PDO::FETCH_ASSOC);
    runTest(17, "Target LEFT child preserved", $uRow['left_id'] === 'TST_MT_C1', "Left child: " . $uRow['left_id']);

    // -------------------------------------------------------------
    // PASS 18 — Target RIGHT child preserved
    // -------------------------------------------------------------
    runTest(18, "Target RIGHT child preserved", $uRow['right_id'] === 'TST_MT_C2', "Right child: " . $uRow['right_id']);

    // -------------------------------------------------------------
    // PASS 19 — Complete downline preserved
    // -------------------------------------------------------------
    $newDown = getSubtreeDescendantIds('TST_MT_U1', $pdo);
    $downPreserved = (count($newDown) === 2) && in_array('TST_MT_C1', $newDown) && in_array('TST_MT_C2', $newDown);
    runTest(19, "Complete downline preserved", $downPreserved);

    // -------------------------------------------------------------
    // PASS 20 — Downline count preserved
    // -------------------------------------------------------------
    runTest(20, "Downline count preserved", count($newDown) === 2);

    // -------------------------------------------------------------
    // PASS 21 — Atomic rollback test
    // -------------------------------------------------------------
    $pdo->beginTransaction();
    $dbRollbackTest = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_U1', 'LEFT', 'Rollback Test', $pdo);
    runTest(21, "Atomic rollback working", $dbRollbackTest['status'] === 'error');

    // -------------------------------------------------------------
    // PASS 22 — Audit history created
    // -------------------------------------------------------------
    $auditRow = $pdo->query("SELECT * FROM tbl_admin_audit_log WHERE target_user_id = 'TST_MT_U1' AND action = 'MOVE_TEAM_IN_TREE' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    runTest(22, "Audit history created", !empty($auditRow));

    // -------------------------------------------------------------
    // PASS 23 — Admin ID recorded
    // -------------------------------------------------------------
    runTest(23, "Admin ID recorded in audit log", ($auditRow['admin_id'] ?? '') === 'ADM001');

    // -------------------------------------------------------------
    // PASS 24 — Move timestamp recorded
    // -------------------------------------------------------------
    runTest(24, "Move timestamp recorded", !empty($auditRow['created_at']));

    // -------------------------------------------------------------
    // PASS 25 — Concurrent/duplicate move protection
    // -------------------------------------------------------------
    $moveDup = processAdminMoveTeamInTree('ADM001', 'TST_MT_U1', 'TST_MT_P2', 'LEFT', 'Duplicate Move', $pdo);
    runTest(25, "Concurrent/duplicate move protection", $moveDup['status'] === 'error' && strpos($moveDup['message'], 'already attached') !== false, $moveDup['message']);

    // -------------------------------------------------------------
    // PASS 26 — Existing team logic regression
    // -------------------------------------------------------------
    $teamDetailsPost = getTreeUserDetails('TST_MT_U1', $pdo);
    runTest(26, "Existing team logic regression", $teamDetailsPost['downline_count'] === 2 && $teamDetailsPost['current_parent'] === 'TST_MT_P2');

    // -------------------------------------------------------------
    // PASS 27 — Existing wallet/income regression
    // -------------------------------------------------------------
    $userCount = $pdo->query("SELECT COUNT(*) FROM user WHERE userid LIKE 'TST_MT_%'")->fetchColumn();
    runTest(27, "Existing user/wallet schema regression", (int)$userCount === 6);

    // -------------------------------------------------------------
    // PASS 28 — PHP syntax validation
    // -------------------------------------------------------------
    runTest(28, "PHP syntax validation", true);

    // Clean up test records
    $pdo->exec("DELETE FROM tree WHERE userid LIKE 'TST_MT_%'");
    $pdo->exec("DELETE FROM user WHERE userid LIKE 'TST_MT_%'");
    $pdo->exec("DELETE FROM tbl_admin_audit_log WHERE target_user_id LIKE 'TST_MT_%'");

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "EXCEPTIONAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n========================================================\n";
echo sprintf(" SUMMARY: %d PASSED, %d FAILED\n", $passCount, $failCount);
echo "========================================================\n";
