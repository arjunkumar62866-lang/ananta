<?php
require 'common/connection.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Unknown error occurred'];

try {
    $action = $_REQUEST['action'] ?? '';

    if ($action === 'delete') {
        // Fetch current image to remove file from disk
        $stmtSel = $pdo->prepare("SELECT offer_image FROM tbl_homest WHERE id = 1 LIMIT 1");
        $stmtSel->execute();
        $currRow = $stmtSel->fetch(PDO::FETCH_ASSOC);

        if ($currRow && !empty($currRow['offer_image'])) {
            $filePath = "../img/" . $currRow['offer_image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $stmtDel = $pdo->prepare("UPDATE tbl_homest SET offer_image = '' WHERE id = 1");
        if ($stmtDel->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Offer banner deleted successfully!';
        } else {
            $response['message'] = 'Failed to delete offer image from database.';
        }
    } elseif (!empty($_FILES['qr_codeimage']['name'])) {

        $allowedTypes = ['jpg','jpeg','png','gif','webp','JPG','JPEG','PNG','GIF','WEBP'];
        $ext = pathinfo($_FILES['qr_codeimage']['name'], PATHINFO_EXTENSION);

        if (!in_array($ext, $allowedTypes)) {
            $response['message'] = 'Only JPG, JPEG, PNG, GIF, and WEBP formats are allowed.';
            echo json_encode($response);
            exit;
        }

        $imagename = uniqid() . '.' . strtolower($ext);
        $uploadPath = "../img/" . $imagename;

        // Fetch old image to unlink
        $stmtOld = $pdo->prepare("SELECT offer_image FROM tbl_homest WHERE id = 1 LIMIT 1");
        $stmtOld->execute();
        $oldRow = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (move_uploaded_file($_FILES['qr_codeimage']['tmp_name'], $uploadPath)) {
            if ($oldRow && !empty($oldRow['offer_image']) && $oldRow['offer_image'] !== $imagename) {
                $oldPath = "../img/" . $oldRow['offer_image'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $stmt = $pdo->prepare("UPDATE tbl_homest SET offer_image = ? WHERE id = 1");
            if ($stmt->execute([$imagename])) {
                $response['status'] = 'success';
                $response['message'] = 'Offer image updated successfully!';
                $response['image_name'] = $imagename;
            } else {
                $response['message'] = 'Database update failed.';
            }
        } else {
            $response['message'] = 'Failed to upload file to server.';
        }

    } else {
        $response['message'] = 'No file selected for upload or invalid action.';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
