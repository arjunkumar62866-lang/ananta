<?php
include('common/connection.php');

$p_id = $_POST["p_id"];
$p_id = substr($p_id, 2);

try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userid");
    $stmt->bindParam(':userid', $p_id, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $data['name'] = $row['name'];
        $data['sponserid'] = $row['sponserid'];
        $data['status'] = $row['status'] == 1 ? "Allready Paid" : "Please make Payment";
    } 
    // else {
    //     $data['name'] = "";
    //     $data['sponserid'] = "";
    //     $data['status'] = "User Not Found";
    // }

    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode([
        "error" => "Database Error",
        "message" => $e->getMessage()
    ]);
}
?>
