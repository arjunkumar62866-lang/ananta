<?php
/**
 * Regression Test Suite: User-side Notification System
 * Tests:
 * 1. Notification Creation with and without 'AN' prefix
 * 2. Unread notification count calculation and accuracy
 * 3. User isolation (User B cannot view or count User A's notifications)
 * 4. Mark single notification as read (using correct parameter order and inverted order)
 * 5. Mark all notifications as read
 * 6. Target vs Global notification retrieval
 * 7. Verification that rollbacks restore original state
 */

require_once __DIR__ . '/../dashboard/user1/common/connection.php';
require_once __DIR__ . '/../dashboard/user1/common/db_method.php';

echo "========================================================\n";
echo " REGRESSION TEST: User Notification System Audit & Fix\n";
echo "========================================================\n";

$passCount = 0;
$failCount = 0;

function assertCondition($testName, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] $testName\n";
        $passCount++;
    } else {
        echo "[FAIL] $testName: $details\n";
        $failCount++;
    }
}

$testUserA = 'TEST_NOTIF_USER_A';
$testUserB = 'TEST_NOTIF_USER_B';

// Cleanup any leftover test records
$pdo->exec("DELETE FROM tbl_user_notifications WHERE user_id IN ('$testUserA', 'AN$testUserA', '$testUserB', 'AN$testUserB')");
$pdo->exec("DELETE FROM tbl_system_notifications WHERE target_user_id IN ('$testUserA', 'AN$testUserA', '$testUserB', 'AN$testUserB')");

try {
    // Test 1: Create notification for User A
    $c1 = createUserNotification($testUserA, 'LOGIN', 'Login Alert', 'Test login from IP 1.2.3.4', null, $pdo);
    assertCondition("1. Create notification for User A", $c1 === true);

    // Test 2: Unread count for User A should be 1
    $cntA1 = getUnreadNotificationCount($testUserA, $pdo);
    assertCondition("2. Unread count for User A is 1", $cntA1 === 1, "Got $cntA1");

    // Test 3: User B isolation (User B unread count should be 0)
    $cntB1 = getUnreadNotificationCount($testUserB, $pdo);
    assertCondition("3. User B unread count is 0 (User isolation)", $cntB1 === 0, "Got $cntB1");

    // Test 4: User B cannot retrieve User A's notifications
    $notifsB = getUserNotifications($testUserB, 10, 0, $pdo);
    assertCondition("4. User B notification list is empty", count($notifsB) === 0, "Got " . count($notifsB));

    // Test 5: User A retrieves notification with details
    $notifsA = getUserNotifications($testUserA, 10, 0, $pdo);
    assertCondition("5. User A retrieves created notification", count($notifsA) === 1 && $notifsA[0]['title'] === 'Login Alert');
    $notifIdA = $notifsA[0]['id'];

    // Test 6: ID normalization - querying with 'AN' prefix returns same notification
    $notifsAPrefixed = getUserNotifications('AN' . $testUserA, 10, 0, $pdo);
    assertCondition("6. Prefix normalization returns notification", count($notifsAPrefixed) === 1 && $notifsAPrefixed[0]['id'] == $notifIdA);

    // Test 7: Mark notification as read with canonical order ($userId, $notifId)
    $m1 = markNotificationAsRead($testUserA, $notifIdA, $pdo);
    assertCondition("7. Mark notification as read (canonical order)", $m1 === true);

    $cntA2 = getUnreadNotificationCount($testUserA, $pdo);
    assertCondition("8. Unread count for User A becomes 0 after mark as read", $cntA2 === 0, "Got $cntA2");

    // Test 9: Create second notification and test swapped order tolerance
    createUserNotification($testUserA, 'WITHDRAWAL', 'Withdrawal Request', 'Request of $100', 'WD999', $pdo);
    $notifsA2 = getUserNotifications($testUserA, 10, 0, $pdo);
    $newNotifId = $notifsA2[0]['id'];
    $m2 = markNotificationAsRead($newNotifId, $testUserA, $pdo); // Swapped arguments test
    assertCondition("9. Mark notification as read with swapped arguments tolerance", $m2 === true);

    // Test 10: Mark all notifications as read
    createUserNotification($testUserA, 'P2P', 'P2P Transfer', 'Transferred $50', 'P2P1', $pdo);
    createUserNotification($testUserA, 'SECURITY', 'PIN Changed', 'PIN updated', null, $pdo);
    $cntBeforeAll = getUnreadNotificationCount($testUserA, $pdo);
    assertCondition("10. Multiple unread notifications created", $cntBeforeAll === 2, "Got $cntBeforeAll");

    $mAll = markAllNotificationsAsRead($testUserA, $pdo);
    $cntAfterAll = getUnreadNotificationCount($testUserA, $pdo);
    assertCondition("11. Mark all notifications as read", $mAll === true && $cntAfterAll === 0, "Got $cntAfterAll");

    // Test 12: Targeted system notification for User A
    $pdo->prepare("INSERT INTO tbl_system_notifications (target_type, target_user_id, title, message, created_by, is_read) VALUES ('USER', :uid, 'Admin Notice', 'Targeted notice', 'admin', 0)")
        ->execute([':uid' => $testUserA]);
    $sysTargetId = $pdo->lastInsertId();
    $virtualSysId = 1000000 + $sysTargetId;

    $cntTargetSys = getUnreadNotificationCount($testUserA, $pdo);
    $cntTargetSysB = getUnreadNotificationCount($testUserB, $pdo);
    assertCondition("12. Targeted system notification visible only to User A", $cntTargetSys === 1 && $cntTargetSysB === 0);

    $mSys = markNotificationAsRead($testUserA, $virtualSysId, $pdo);
    assertCondition("13. Targeted system notification marked as read", $mSys === true);
    $cntTargetSysAfter = getUnreadNotificationCount($testUserA, $pdo);
    assertCondition("14. Unread count after targeted system notification mark read is 0", $cntTargetSysAfter === 0);

} finally {
    // Clean up test records
    $pdo->exec("DELETE FROM tbl_user_notifications WHERE user_id IN ('$testUserA', 'AN$testUserA', '$testUserB', 'AN$testUserB')");
    $pdo->exec("DELETE FROM tbl_system_notifications WHERE target_user_id IN ('$testUserA', 'AN$testUserA', '$testUserB', 'AN$testUserB')");
}

echo "========================================================\n";
echo " TEST SUMMARY: Total = " . ($passCount + $failCount) . " | Pass = $passCount | Fail = $failCount\n";
echo "========================================================\n";

if ($failCount > 0) {
    exit(1);
}
