<?php
include 'common/connection.php';

// Your insertUser function
function insertUser($pdo, $data) 
{
    $sql = "INSERT INTO user (
        userid, name, mobile, email, pan, pass, txn_pass, sponserid, sponsername, underuserid,
        active, status, join_side, package, joining_date, plan, pin, kyc, club, upgrade_date,
        time, country, amount, capping, rank, closingdate, country_code, level, atime, pool,
        state, father, gender, pin_code, address, otp, coin_wallet
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?
    )";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// Example loop to run insertUser 50,000 times
for ($i = 1; $i <= 50000; $i++) {
    $userid = rand(100000, 999999) . $i; // Unique-ish ID
    $name = "User $i";
    $mobile = "91" . rand(6000000000, 9999999999);
    $email = "user{$i}@example.com";
    $password = "pass" . rand(1000, 9999);
    $transaction_password = rand(100000, 999999);
    $sponserid1 = "SP" . rand(1000, 9999);
    $sponsername = "Sponsor $i";
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $otpreg = rand(1000, 9999);
    $coinbonus = 0;

    $userData = [
        $userid, $name, $mobile, $email, '', $password, $transaction_password,
        $sponserid1, $sponsername, $sponserid1, '0', '1', '', '', $date, '', '', '0', 'active',
        '', $time, '', '', '0', '', '', '', $userid, '', '0', '', '', '', '', '', $otpreg, $coinbonus
    ];

    insertUser($pdo, $userData);

    if ($i % 1000 === 0) {
        echo "Inserted $i records...\n";
        flush();
    }
}

echo "✅ Done inserting 50,000 records!";
?>
