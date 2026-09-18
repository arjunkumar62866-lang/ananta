<?php
include 'common/connection.php'; // DB connection file

$maxFileSize = 2 * 1024 * 1024;  // 2MB
$imgName = $_FILES['banner_image']['name'] ?? '';
$imgSize = $_FILES['banner_image']['size'] ?? 0;

if ($imgName != '') {

    if ($imgSize <= $maxFileSize) {

        // Create unique filename
        $uniqueName = time() . "_" . $imgName;
        $targetPath = "../images/" . $uniqueName;

        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {

            // Insert into DB
            $stmt = $pdo->prepare("INSERT INTO tbl_banner (img, status) VALUES (:img, 1)");
            $stmt->execute(['img' => $targetPath]);

            echo '<script>alert("Banner uploaded successfully."); window.location="manage_banner.php";</script>';

        } else {
            echo '<script>alert("Image upload failed. Try again."); window.location="banner.php";</script>';
        }

    } else {
        echo '<script>alert("Please upload an image smaller than 2MB."); window.location="banner.php";</script>';
    }

} else {
    echo '<script>alert("Please select an image."); window.location="update_banner.php";</script>';
}
?>
