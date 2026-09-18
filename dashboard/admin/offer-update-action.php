<?php
require 'common/connection.php';

$response = ['status' => 'error', 'message' => 'Unknown error occurred'];

try {
    if (!empty($_FILES['qr_codeimage']['name'])) {

        $allowedTypes = ['jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF'];
        $ext = pathinfo($_FILES['qr_codeimage']['name'], PATHINFO_EXTENSION);

        if (!in_array($ext, $allowedTypes)) {
            $response['message'] = 'Only JPG, JPEG, PNG, and GIF formats are allowed.';
            echo json_encode($response);
            exit;
        }

        $imagename = uniqid() . '.' . $ext;
        $uploadPath = "../img/" . $imagename;

        if (move_uploaded_file($_FILES['qr_codeimage']['tmp_name'], $uploadPath)) {
            $stmt = $pdo->prepare("UPDATE tbl_homest SET offer_image = ? WHERE id = 1");
            if ($stmt->execute([$imagename])) {
                $response['status'] = 'success';
                $response['message'] = 'Offer image updated successfully!';
            } else {
                $response['message'] = 'Database update failed.';
            }
        } else {
            $response['message'] = 'Failed to upload file.';
        }

    } else {
        $response['message'] = 'No file selected.';
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
