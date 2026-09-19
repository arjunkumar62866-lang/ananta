<?php
session_start();
include "common/connection.php";
header('Content-Type: application/json');

$userid = $_SESSION['userid'] ?? '';

$data = [];
$countnew = 1;

/* Get user details */
function getUserByUserId($userid)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = ?");
    $stmt->execute([$userid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Fetch LEFT TEAM using tbl_userlevel_a */
$sql = "
    SELECT downline_id, MIN(level) AS level
    FROM tbl_userlevel_a
    WHERE sponser_id = ?
    GROUP BY downline_id
    ORDER BY level ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userid]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $details = getUserByUserId($row['downline_id']);

    if (!$details) continue;

    $data[] = [
        "sr" => $countnew++,
        "userid" => $details['userid'],
        "name" => $details['name'],
        "sponsorid" => $details['sponserid'],
        "joining_date" => $details['joining_date'],
        "status" => $details['active'] == '1'
            ? "<b style='color:green'>Active</b>"
            : "<b style='color:red'>Inactive</b>"
    ];
}

echo json_encode($data);
