<?php
require 'common/connection.php';
header('Content-Type: application/json');

$title = $_GET['title'] ?? 'all';

if ($title == 'Pending Enquiry') {
    $stmt = $pdo->prepare("SELECT * FROM tbl_query WHERE status = '0' ORDER BY id DESC");
} elseif ($title == 'Viewed Enquiry') {
    $stmt = $pdo->prepare("SELECT * FROM tbl_query WHERE status != '0' ORDER BY id DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM tbl_query ORDER BY id DESC");
}

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sn = 1;

foreach ($rows as $row) {
    
    if ($row['status'] == '1') {
        $statusBadge = '<span class="badge badge-success">Viewed</span>';
    } elseif ($row['status'] == '0') {
        $statusBadge = '<span class="badge badge-warning">Pending</span>';
    } elseif ($row['status'] == '2') {
        $statusBadge = '<span class="badge badge-danger">Reject</span>';
    } else {
        $statusBadge = '<span class="badge badge-secondary">Unknown</span>';
    }

    // Action button for pending enquiry
    $action = '';
    if ($title == "Pending Enquiry") {
        $action = '<a href="view_enquiry.php?id=' . $row['id'] . '" class="btn btn-info btn-sm">Viewed</a>';
    }

    
    $data[] = [
        'sn' => $sn++,
        'userid' => $row['userid'],
        'subject' => htmlspecialchars($row['sub']),
        'status' => $statusBadge,
        'date' => $row['or_date'],
        'action' => $action
    ];
}

echo json_encode($data);
