<?php
include 'common/header.php'; // gives $pdo

if (!isset($_GET['amount'])) {
    die("Amount is required.");
}

$amount = floatval($_GET['amount']);
$currentYear = date("Y");
$currentMonth = date("m");
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

// fetch all users of royalty-one who are active
$stmt = $pdo->prepare("SELECT * FROM tbl_royalty_user WHERE full_status = 0");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$paidUsers = 0;

foreach ($users as $user) {

    $uid = $user['userid'];

    // check if already paid this month
    if (!empty($user['updated_date'])) {
        $paidMonth = date('m', strtotime($user['updated_date']));
        $paidYear  = date('Y', strtotime($user['updated_date']));

        if ($paidMonth == $currentMonth && $paidYear == $currentYear) {
            continue; // already got royalty this month
        }
    }

    // 1) Insert transaction into tbl_transaction
    $ins = $pdo->prepare("
        INSERT INTO tbl_transaction 
        (user_id, type, subject, time, created_date, status, amount)
        VALUES 
        (:uid, 'Credit', 'Leadership Income', :time, :cdate, 1, :amount)
    ");

    $ins->execute([
        ':uid'    => $uid,
        ':time'   => $currentTime,
        ':cdate'  => $currentDate,
        ':amount' => $amount,
    ]);

    // 2) Update royalty user table
    $upd = $pdo->prepare("
        UPDATE tbl_royalty_user 
        SET amount = amount + :amt,
            total_amount = total_amount + :amt,
            paid_count = paid_count + 1,
            updated_date = :udate,
            update_time = :utime
        WHERE userid = :uid
    ");

    $upd->execute([
        ':amt'   => $amount,
        ':udate' => $currentDate,
        ':utime' => $currentTime,
        ':uid'   => $uid
    ]);

    $paidUsers++;
}

echo "<script>
    alert('Leadership Income Paid Successfully to $paidUsers Users');
    window.location.href = 'index.php';
</script>";
exit;

