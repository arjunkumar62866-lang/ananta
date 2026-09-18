<?php
session_start();
include "common/connection.php"; // Only DB connection
header('Content-Type: application/json');

$userid = $_SESSION['userid'];

$data = [];
$countnew = 1;

function getuserdatabysponserid($userid)
{ 
    global $pdo;
    $sql = "SELECT * FROM user WHERE userid = :userid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "id" => $rowuser['id'],
            "name" => $rowuser["name"],
            "mobile" => $rowuser["mobile"],
            "email" => $rowuser["email"],
            "pass" => $rowuser["pass"],
            "txn_pass" => $rowuser["txn_pass"],
            "amount" => $rowuser["amount"],
            "userid" => $rowuser["userid"],
            "status" => $rowuser["status"],
            "pool" => $rowuser["pool"],
            "sponsername" => $rowuser["sponsername"],
            "sponserid" => $rowuser["sponserid"],
            "total_package" => $rowuser["total_package"],
            "idactive" => $rowuser["active"],
            "upgrade_date" => $rowuser["upgrade_date"],
            "joining_date" => $rowuser["joining_date"],
            "inc_limit" => $rowuser["inc_limit"],
            "total_inc" => $rowuser["total_inc"]
        ];

        
    }

    return null;
}

function getuserdata($userid1, $pdo, &$data, &$countnew)
{
    $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
    $stmt->execute([$userid1]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$row) return;

    $left  = $row['left_id'];
    $right = $row['right_id'];

    // LEFT CHILD
    if($left != "") {
        $details = getuserdatabysponserid($left);
        if ($details) {
            $data[] = [
                "sr" => $countnew++,
                "userid" => $left,
                "name" => $details['name'],
                "sponsorid" => $details['sponserid'],
                "joining_date" => $details['joining_date'],
                "status" => $details['idactive'] == '1'
                    ? "<b style='color:green'>Active</b>"
                    : "<b style='color:red'>Inactive</b>"
            ];
        }
        getuserdata($left, $pdo, $data, $countnew);
    }

    // RIGHT CHILD
    if($right != "") {
        $details = getuserdatabysponserid($right, $pdo);
        if ($details) {
            $data[] = [
                "sr" => $countnew++,
                "userid" => $right,
                "name" => $details['name'],
                "sponsorid" => $details['sponserid'],
                "joining_date" => $details['joining_date'],
                "status" => $details['idactive'] == '1'
                    ? "<b style='color:green'>Active</b>"
                    : "<b style='color:red'>Inactive</b>"
            ];
        }
        getuserdata($right, $pdo, $data, $countnew);
    }
}



// ROOT USER (LEFT SIDE START)
$stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = ?");
$stmt->execute([$userid]);
$root = $stmt->fetch(PDO::FETCH_ASSOC);

if($root && $root['left_id'] != "") {
    $left = $root['left_id'];
    $details = getuserdatabysponserid($left);

    if ($details) {
        $data[] = [
            "sr" => $countnew++,
            "userid" => $left,
            "name" => $details['name'],
            "sponsorid" => $details['sponserid'],
            "joining_date" => $details['joining_date'],
            "status" => $details['idactive'] == '1'
                ? "<b style='color:green'>Active</b>"
                : "<b style='color:red'>Inactive</b>"
        ];
    }

    getuserdata($left, $pdo, $data, $countnew);
}

// RETURN JSON
echo json_encode($data);
?>
