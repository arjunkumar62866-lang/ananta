<?php
require 'common/connection.php';    

// If request is GET → fetch the news
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT news FROM tbl_news WHERE id = 1");
    $stmt->execute();
    $row = $stmt->fetch();
    echo json_encode($row);
    exit;
}

// If request is POST → update the news
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news = $_POST['text'] ?? '';

    $stmt = $pdo->prepare("UPDATE tbl_news SET news = :news WHERE id = 1");
    $success = $stmt->execute([':news' => $news]);

    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'News updated successfully' : 'Failed to update news'
    ]);
    exit;
}
?>
