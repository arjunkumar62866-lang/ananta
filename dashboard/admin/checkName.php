<?php
include 'common/connection.php';

$tid = $_POST['data'];
$tid = substr($tid, 2);

$stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid AND status = '1'");
$stmt->bindParam(':userid', $tid, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['name'];
} else {
    echo 0;
}

?>
