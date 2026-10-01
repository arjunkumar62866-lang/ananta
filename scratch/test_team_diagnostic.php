<?php
chdir(__DIR__ . "/../dashboard/user1");
include_once("common/connection.php");
include_once("common/db_method.php");

// -------------------------------------------------------------------------
// COMPREHENSIVE EXACT ID-SET PRODUCTION TEAM AUDIT & DIAGNOSTIC SCRIPT
// -------------------------------------------------------------------------

// Bulk load user table and tree table into raw reference arrays for independent calculation
$stmtRaw = $pdo->query("
    SELECT u.userid, u.sponserid, u.underuserid, u.join_side, t.left_id, t.right_id 
    FROM user u 
    LEFT JOIN tree t ON t.userid = u.userid
");
$userRefMap = [];
$underRefMap = [];
$sponserRefMap = [];
while ($r = $stmtRaw->fetch(PDO::FETCH_ASSOC)) {
    $uid = (string)$r['userid'];
    $userRefMap[$uid] = $r;
    
    $pId = !empty($r['underuserid']) ? (string)$r['underuserid'] : '';
    if ($pId !== '') {
        $underRefMap[$pId][] = $r;
    }
    
    $spId = !empty($r['sponserid']) ? (string)$r['sponserid'] : '';
    if ($spId !== '') {
        $sponserRefMap[$spId][] = $uid;
    }
}

// Independent Reference Traversal Function to compute Placement Subtree
function computeIndependentPlacementSubtree($startNodeId, $userRefMap, $underRefMap, &$cyclesDetected) {
    if (empty($startNodeId) || !isset($userRefMap[$startNodeId])) return [];
    
    $result = [];
    $queue = [$startNodeId];
    $visited = [];
    
    while (!empty($queue)) {
        $currId = array_shift($queue);
        if (isset($visited[$currId])) {
            $cyclesDetected[] = $currId;
            continue;
        }
        $visited[$currId] = true;
        $result[] = $currId;
        
        $uData = $userRefMap[$currId];
        $children = [];
        $assignedChildIds = [];
        
        // 1. LEFT SLOT
        $leftId = (!empty($uData['left_id']) && isset($userRefMap[(string)$uData['left_id']])) ? (string)$uData['left_id'] : '';
        if (empty($leftId) && isset($underRefMap[$currId])) {
            foreach ($underRefMap[$currId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $currId && strtolower($uc['join_side'] ?? '') === 'left' && !isset($visited[$cId])) {
                    $leftId = $cId;
                    break;
                }
            }
        }
        if (!empty($leftId) && !isset($visited[$leftId])) {
            $children[] = $leftId;
            $assignedChildIds[$leftId] = true;
        }
        
        // 2. RIGHT SLOT
        $rightId = (!empty($uData['right_id']) && isset($userRefMap[(string)$uData['right_id']])) ? (string)$uData['right_id'] : '';
        if (empty($rightId) && isset($underRefMap[$currId])) {
            foreach ($underRefMap[$currId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $currId && !isset($assignedChildIds[$cId]) && strtolower($uc['join_side'] ?? '') === 'right' && !isset($visited[$cId])) {
                    $rightId = $cId;
                    break;
                }
            }
            if (empty($rightId)) {
                foreach ($underRefMap[$currId] as $uc) {
                    $cId = (string)$uc['userid'];
                    if ($cId !== $currId && !isset($assignedChildIds[$cId]) && !isset($visited[$cId])) {
                        $rightId = $cId;
                        break;
                    }
                }
            }
        }
        if (!empty($rightId) && !isset($visited[$rightId]) && !isset($assignedChildIds[$rightId])) {
            $children[] = $rightId;
            $assignedChildIds[$rightId] = true;
        }
        
        // 3. Additional placement children (where underuserid = currId)
        if (isset($underRefMap[$currId])) {
            foreach ($underRefMap[$currId] as $uc) {
                $cId = (string)$uc['userid'];
                if ($cId !== $currId && !isset($assignedChildIds[$cId]) && !isset($visited[$cId])) {
                    $children[] = $cId;
                    $assignedChildIds[$cId] = true;
                }
            }
        }
        
        foreach ($children as $chId) {
            if (!isset($visited[$chId])) {
                $queue[] = $chId;
            }
        }
    }
    
    return $result;
}

// Find all suitable root users to test
$allUserIds = array_keys($userRefMap);
sort($allUserIds);

$testedUsers = 0;
$myDirectPass = 0;
$myDirectFail = 0;
$leftPass = 0;
$leftFail = 0;
$rightPass = 0;
$rightFail = 0;
$totalOverlapCount = 0;
$cyclesDetected = [];
$conflictCasesTested = 0;

echo "=========================================================================================================\n";
echo "EXACT USER ID-SET PRODUCTION AUDIT REPORT (MY DIRECT, LEFT TEAM, RIGHT TEAM)\n";
echo "=========================================================================================================\n";

foreach ($allUserIds as $rootId) {
    // 1. INDEPENDENT EXPECTED DIRECT IDS
    $expectedDirectIds = $sponserRefMap[$rootId] ?? [];
    sort($expectedDirectIds);
    
    // 2. INDEPENDENT EXPECTED LEFT TEAM IDS
    $leftRootChildId = '';
    if (isset($userRefMap[$rootId])) {
        $uRow = $userRefMap[$rootId];
        $leftRootChildId = !empty($uRow['left_id']) && isset($userRefMap[(string)$uRow['left_id']]) ? (string)$uRow['left_id'] : '';
    }
    if (empty($leftRootChildId) && isset($underRefMap[$rootId])) {
        foreach ($underRefMap[$rootId] as $uc) {
            $cId = (string)$uc['userid'];
            if (strtolower($uc['join_side'] ?? '') === 'left') {
                $leftRootChildId = $cId;
                break;
            }
        }
    }
    $expectedLeftIds = computeIndependentPlacementSubtree($leftRootChildId, $userRefMap, $underRefMap, $cyclesDetected);
    sort($expectedLeftIds);

    // 3. INDEPENDENT EXPECTED RIGHT TEAM IDS
    $rightRootChildId = '';
    if (isset($userRefMap[$rootId])) {
        $uRow = $userRefMap[$rootId];
        $rightRootChildId = !empty($uRow['right_id']) && isset($userRefMap[(string)$uRow['right_id']]) ? (string)$uRow['right_id'] : '';
    }
    if (empty($rightRootChildId) && isset($underRefMap[$rootId])) {
        foreach ($underRefMap[$rootId] as $uc) {
            $cId = (string)$uc['userid'];
            if (strtolower($uc['join_side'] ?? '') === 'right') {
                $rightRootChildId = $cId;
                break;
            }
        }
    }
    $expectedRightIds = computeIndependentPlacementSubtree($rightRootChildId, $userRefMap, $underRefMap, $cyclesDetected);
    sort($expectedRightIds);

    if (empty($expectedDirectIds) && empty($expectedLeftIds) && empty($expectedRightIds)) {
        continue; // Skip users with no team
    }

    $testedUsers++;

    // Track Sponsor != Placement Parent Conflict Cases
    foreach ($expectedDirectIds as $dId) {
        $pId = $userRefMap[$dId]['underuserid'] ?? '';
        if (!empty($pId) && $pId !== $rootId) {
            $conflictCasesTested++;
        }
    }

    // APPLICATION ACTUALS FROM getUserTeamMembersDetailed()
    $actualDirectMembers = getUserTeamMembersDetailed($rootId, 'MY_DIRECT', $pdo);
    $actualDirectIds = array_column($actualDirectMembers, 'userid');
    sort($actualDirectIds);

    $actualLeftMembers = getUserTeamMembersDetailed($rootId, 'LEFT', $pdo);
    $actualLeftIds = array_column($actualLeftMembers, 'userid');
    sort($actualLeftIds);

    $actualRightMembers = getUserTeamMembersDetailed($rootId, 'RIGHT', $pdo);
    $actualRightIds = array_column($actualRightMembers, 'userid');
    sort($actualRightIds);

    // DIRECT ID VALIDATION
    $missingDirects = array_diff($expectedDirectIds, $actualDirectIds);
    $extraDirects = array_diff($actualDirectIds, $expectedDirectIds);
    $dupDirects = array_keys(array_filter(array_count_values($actualDirectIds), function($c) { return $c > 1; }));
    $directStatus = (empty($missingDirects) && empty($extraDirects) && empty($dupDirects)) ? "PASS" : "FAIL";
    if ($directStatus === "PASS") $myDirectPass++; else $myDirectFail++;

    // LEFT TEAM ID VALIDATION
    $missingLeft = array_diff($expectedLeftIds, $actualLeftIds);
    $extraLeft = array_diff($actualLeftIds, $expectedLeftIds);
    $dupLeft = array_keys(array_filter(array_count_values($actualLeftIds), function($c) { return $c > 1; }));
    $leftStatus = (empty($missingLeft) && empty($extraLeft) && empty($dupLeft)) ? "PASS" : "FAIL";
    if ($leftStatus === "PASS") $leftPass++; else $leftFail++;

    // RIGHT TEAM ID VALIDATION
    $missingRight = array_diff($expectedRightIds, $actualRightIds);
    $extraRight = array_diff($actualRightIds, $expectedRightIds);
    $dupRight = array_keys(array_filter(array_count_values($actualRightIds), function($c) { return $c > 1; }));
    $rightStatus = (empty($missingRight) && empty($extraRight) && empty($dupRight)) ? "PASS" : "FAIL";
    if ($rightStatus === "PASS") $rightPass++; else $rightFail++;

    // LEFT / RIGHT OVERLAP
    $overlapIds = array_intersect($actualLeftIds, $actualRightIds);
    if (!empty($overlapIds)) {
        $totalOverlapCount += count($overlapIds);
    }

    echo sprintf("ROOT USER: %s\n", $rootId);
    echo "  MY DIRECT  -> Expected: [" . implode(", ", $expectedDirectIds) . "]\n";
    echo "                Actual:   [" . implode(", ", $actualDirectIds) . "]\n";
    echo "                Missing: " . (empty($missingDirects) ? "NONE" : implode(", ", $missingDirects)) . " | Extra: " . (empty($extraDirects) ? "NONE" : implode(", ", $extraDirects)) . " | Dups: " . (empty($dupDirects) ? "NONE" : implode(", ", $dupDirects)) . " | Status: " . $directStatus . "\n";

    echo "  LEFT TEAM  -> Expected: [" . implode(", ", $expectedLeftIds) . "]\n";
    echo "                Actual:   [" . implode(", ", $actualLeftIds) . "]\n";
    echo "                Missing: " . (empty($missingLeft) ? "NONE" : implode(", ", $missingLeft)) . " | Extra: " . (empty($extraLeft) ? "NONE" : implode(", ", $extraLeft)) . " | Dups: " . (empty($dupLeft) ? "NONE" : implode(", ", $dupLeft)) . " | Status: " . $leftStatus . "\n";

    echo "  RIGHT TEAM -> Expected: [" . implode(", ", $expectedRightIds) . "]\n";
    echo "                Actual:   [" . implode(", ", $actualRightIds) . "]\n";
    echo "                Missing: " . (empty($missingRight) ? "NONE" : implode(", ", $missingRight)) . " | Extra: " . (empty($extraRight) ? "NONE" : implode(", ", $extraRight)) . " | Dups: " . (empty($dupRight) ? "NONE" : implode(", ", $dupRight)) . " | Status: " . $rightStatus . "\n";

    echo "  LEFT/RIGHT OVERLAP: " . (empty($overlapIds) ? "NONE" : implode(", ", $overlapIds)) . "\n";
    echo str_repeat("-", 95) . "\n";
}

echo "=========================================================================================================\n";
echo "FINAL AUDIT SUMMARY REPORT\n";
echo "=========================================================================================================\n";
echo "TOTAL USERS TESTED                       : " . $testedUsers . "\n";
echo "MY DIRECT  -> Passed: " . $myDirectPass . " | Failed: " . $myDirectFail . "\n";
echo "LEFT TEAM  -> Passed: " . $leftPass . " | Failed: " . $leftFail . "\n";
echo "RIGHT TEAM -> Passed: " . $rightPass . " | Failed: " . $rightFail . "\n";
echo "LEFT/RIGHT OVERLAP COUNT                 : " . $totalOverlapCount . "\n";
echo "CYCLES DETECTED                          : " . count($cyclesDetected) . "\n";
echo "SPONSOR/PLACEMENT CONFLICT CASES TESTED   : " . $conflictCasesTested . "\n";
echo "DATABASE WRITES                          : 0\n";
echo "=========================================================================================================\n";
