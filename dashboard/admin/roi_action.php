<?php
include 'common/connection.php';
include 'common/db_method.php';

// GET request → Fetch ROI data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $row = getroipercentage($pdo); 
    echo json_encode($row);
    exit;
}

// POST request → Update ROI data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roi_one = $_POST['roi_one'] ?? '';
    $daily_level = ''; 

    $stmt = $pdo->prepare("UPDATE tbl_roipercentage 
                            SET percentage = :percentage, 
                                level_percentage = :level 
                            WHERE id = 1");
    $success = $stmt->execute([
        ':percentage' => $roi_one,
        ':level' => $daily_level
    ]);

    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'ROI Updated Successfully' : 'Failed to update ROI'
    ]);
    exit;
}
?>
