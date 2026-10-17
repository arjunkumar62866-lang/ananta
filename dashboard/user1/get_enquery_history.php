<?php
require 'common/connection.php';
header('Content-Type: application/json');
session_start();


try {
    $cleanUid = (stripos($_SESSION['userid'] ?? '', 'AN') === 0) ? substr($_SESSION['userid'], 2) : ($_SESSION['userid'] ?? '');
    $anUid = (stripos($_SESSION['userid'] ?? '', 'AN') === 0) ? $_SESSION['userid'] : 'AN' . ($_SESSION['userid'] ?? '');
    $actualUid = $_SESSION['userid'] ?? '';

    $stmt = $pdo->prepare("
        SELECT id, userid, sub, message, status, admin_reply, reply_date, or_date 
        FROM tbl_query 
        WHERE userid = :uid OR userid = :clean OR userid = :an
        ORDER BY id DESC
    ");
    $stmt->execute([
        ':uid'   => $actualUid,
        ':clean' => $cleanUid,
        ':an'    => $anUid
    ]);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;
