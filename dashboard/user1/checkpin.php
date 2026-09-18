<?php 
include 'common/connection.php';

$pin   = $_POST['pin_id'];
$price = $_POST['price'];

try {
    $stmt = $pdo->prepare("SELECT * FROM pin_list WHERE pin = :pin AND package = :price AND status = '0'");
    $stmt->execute([
        ':pin'   => $pin,
        ':price' => $price
    ]);

    if ($stmt->rowCount() > 0) {
        echo 1;
    } else {
        echo 0;
    }
} catch (PDOException $e) {
    die("SQL Query Failed: " . $e->getMessage());
}
?>
