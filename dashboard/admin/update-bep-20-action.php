<?php
include("common/connection.php");
date_default_timezone_set('Asia/Kolkata');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $upi = $_POST['upiid'] ?? '';
        $account = $_POST['account'] ?? '';
        $imagename = null;

        // Handle file upload
        if (!empty($_FILES['qr_codeimage']['name'])) {
            $targetDir = "../../img/";
            $imagename = uniqid() . basename($_FILES['qr_codeimage']['name']);
            $targetFile = $targetDir . $imagename;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Allowed file types
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileType, $allowed)) {
                throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed.");
            }

            // Move uploaded file
            if (!move_uploaded_file($_FILES['qr_codeimage']['tmp_name'], $targetFile)) {
                throw new Exception("File upload failed.");
            }
        }

        // Update database
        if ($imagename) {
            $sql = "UPDATE tbl_bank SET upi = ?, qr_code = ?, account = ?, type = 'BEP' WHERE id = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$upi, $imagename, $account]);
        } else {
            $sql = "UPDATE tbl_bank SET upi = ?, account = ? WHERE id = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$upi, $account]);
        }

        // Send updated values back
        $response = [
            'status'  => 'success',
            'message' => 'QR Code updated successfully.',
            'upi'     => $upi,
            'account' => $account,
            'qr_code' => $imagename ?? '' // optional
        ];
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>