<?php
include 'common/connection.php';

// Get parameters safely
$id = $_GET['id'] ?? null;
$title = $_GET['title'] ?? null;

if ($id && $title) {
    try {
        if ($title == "Approved") {
            $stmt = $pdo->prepare("UPDATE tbl_query SET status = '1' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<script>alert('Enquiry Approved Successfully');window.location.assign('user_enquiry.php');</script>";

        } elseif ($title == "Reject") {
            $stmt = $pdo->prepare("UPDATE tbl_query SET status = '2' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<script>alert('Enquiry Rejected Successfully');window.location.assign('user_enquiry.php');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "');</script>";
    }
} else {
    echo "<script>alert('Invalid Request');window.location.assign('user-enquiry.php');</script>";
}
?>
