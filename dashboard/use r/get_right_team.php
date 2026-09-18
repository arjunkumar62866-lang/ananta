<?php
session_start();
include "common/connection.php";
header('Content-Type: application/json');

$userid = $_SESSION['userid'];

$data = [];
$countnew = 1;

/* -----------------------------
   FETCH USER DETAILS
------------------------------ */
function getuserdatabysponserid($userid)
{
    global $pdo;
    $sql = "SELECT * FROM user WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

/* -----------------------------
   RECURSIVE FUNCTION (RIGHT SIDE)
------------------------------ */
function getuserdata_right($userid1, $pdo, &$data, &$countnew)
{
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
    $stmt->execute([$userid1]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return;

    $left  = $row['left_id'];
    $right = $row['right_id'];

    /* ---- LEFT CHILD ---- */
    if (!empty($left)) {
        $details = getuserdatabysponserid($left);
        if ($details) {
            $data[] = [
                "sr" => $countnew++,
                "userid" => $left,
                "name" => $details['name'],
                "sponsorid" => $details['sponserid'],
                "joining_date" => $details['joining_date'],
                "status" => $details['active'] == '1'
                    ? "<b style='color:green'>Active</b>"
                    : "<b style='color:red'>Inactive</b>"
            ];
        }
        getuserdata_right($left, $pdo, $data, $countnew);
    }

    /* ---- RIGHT CHILD ---- */
    if (!empty($right)) {
        $details = getuserdatabysponserid($right);
        if ($details) {
            $data[] = [
                "sr" => $countnew++,
                "userid" => $right,
                "name" => $details['name'],
                "sponsorid" => $details['sponserid'],
                "joining_date" => $details['joining_date'],
                "status" => $details['active'] == '1'
                    ? "<b style='color:green'>Active</b>"
                    : "<b style='color:red'>Inactive</b>"
            ];
        }
        getuserdata_right($right, $pdo, $data, $countnew);
    }
}

/* -----------------------------------
   START FROM ROOT → RIGHT SIDE USERS
------------------------------------ */
$stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
$stmt->execute([$userid]);
$root = $stmt->fetch(PDO::FETCH_ASSOC);

if ($root && !empty($root['right_id'])) {

    $right = $root['right_id'];
    $details = getuserdatabysponserid($right);

    if ($details) {
        $data[] = [
            "sr" => $countnew++,
            "userid" => $right,
            "name" => $details['name'],
            "sponsorid" => $details['sponserid'],
            "joining_date" => $details['joining_date'],
            "status" => $details['active'] == '1'
                ? "<b style='color:green'>Active</b>"
                : "<b style='color:red'>Inactive</b>"
        ];
    }

    // recursive fetch
    getuserdata_right($right, $pdo, $data, $countnew);
}

/* ------------------------
   RETURN JSON RESPONSE
------------------------- */
echo json_encode($data);
?>
