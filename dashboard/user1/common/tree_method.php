
<?php
include 'common/connection.php'; // $pdo connection file

function checkfreleg($sponserid, $userid, $spid, $password, $mobile)
{
    global $pdo;

    $sql = "SELECT * FROM tbl_pool_matrix 
            WHERE sponserid = :sponserid 
            AND status = '1'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':sponserid' => $sponserid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $position1  = $rowuser['position1'];
        $position2  = $rowuser['position2'];
        // $position3  = $rowuser['position3'];
        // $position4  = $rowuser['position4'];
        // $position5  = $rowuser['position5'];
        // $position6  = $rowuser['position6'];
        // $position7  = $rowuser['position7'];
        // $position8  = $rowuser['position8'];
        // $position9  = $rowuser['position9'];
        // $position10 = $rowuser['position10'];
        $fill_status = $rowuser['fill_status'];

        if ($fill_status == 0) {

            if ($position1 == "") {
                insertinfreelegstarnew($userid, "position1", $sponserid, $password, $mobile);
            } elseif ($position2 == "") {
                insertinfreelegstarnew($userid, "position2", $sponserid, $password, $mobile);
            } 
            // elseif ($position3 == "") {
            //     insertinfreelegstarnew($userid, "position3", $sponserid, $password, $mobile);
            // } elseif ($position4 == "") {
            //     insertinfreelegstarnew($userid, "position4", $sponserid, $password, $mobile);
            // } elseif ($position5 == "") {
            //     insertinfreelegstarnew($userid, "position5", $sponserid, $password, $mobile);
            // } elseif ($position6 == "") {
            //     insertinfreelegstarnew($userid, "position6", $sponserid, $password, $mobile);
            // } elseif ($position7 == "") {
            //     insertinfreelegstarnew($userid, "position7", $sponserid, $password, $mobile);
            // } elseif ($position8 == "") {
            //     insertinfreelegstarnew($userid, "position8", $sponserid, $password, $mobile);
            // } elseif ($position9 == "") {
            //     insertinfreelegstarnew($userid, "position9", $sponserid, $password, $mobile);
            // } elseif ($position10 == "") {
            //     insertinfreelegstarnew($userid, "position10", $sponserid, $password, $mobile);
            // }

        } 
        else {

            if (checkfillstatus($position1) == 1) {
                checkfreleg($position1, $userid, $spid, $password, $mobile);
            } elseif (checkfillstatus($position2) == 1) {
                checkfreleg($position2, $userid, $spid, $password, $mobile);
            } 
            // elseif (checkfillstatus($position3) == 1) {
            //     checkfreleg($position3, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position4) == 1) {
            //     checkfreleg($position4, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position5) == 1) {
            //     checkfreleg($position5, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position6) == 1) {
            //     checkfreleg($position6, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position7) == 1) {
            //     checkfreleg($position7, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position8) == 1) {
            //     checkfreleg($position8, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position9) == 1) {
            //     checkfreleg($position9, $userid, $spid, $password, $mobile);
            // } elseif (checkfillstatus($position10) == 1) {
            //     checkfreleg($position10, $userid, $spid, $password, $mobile);
            // } 
            else {

                $myuid = get_userdata($spid);
                $myid  = get_freeid($myuid, $spid);

                if (get_userdata_sponsor($myid, $spid) == 1) {
                    $chekingfreepositng = checkleftandrightcount($myid);
                    insertinfreelegstarnew($userid, $chekingfreepositng, $myid, $password, $mobile);
                } else {
                    for ($i = 1; $i <= 4; $i++) {
                        $myuid++;
                        $myid = get_freeid($myuid, $sponserid);

                        if (get_userdata_sponsor($myid, $sponserid) == 1) {
                            $chekingfreepositng = checkleftandrightcount($myid);
                            insertinfreelegstarnew($userid, $chekingfreepositng, $myid, $password, $mobile);
                            break;
                        }
                    }
                }
            }
        }
    }
}


function checkfillstatus($sponserid)
{
    global $pdo;

    $sql = "SELECT fill_status 
            FROM tbl_pool_matrix 
            WHERE sponserid = :sponserid 
            AND status = '1'
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':sponserid' => $sponserid]);

    $rowuser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowuser) {
        if ($rowuser['fill_status'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    return 0; // default if no record found
}

function insertinfreelegstarnew($userid, $column, $sp, $password, $mobile)
{
    global $pdo;

    $todaytime = date("H:i:00");
    $todaydate = date("Y-m-d");
    $cenvertedTime = date('H:i:s', strtotime('+3 hour +30 minutes', strtotime($todaytime)));

    if (checkmycountnew($userid) != 0) {
        return;
    }

    /* ---------- UPDATE POOL MATRIX ---------- */
    if ($column === "position2") {
        $sql = "UPDATE tbl_pool_matrix 
                SET $column = :userid, fill_status = '1' 
                WHERE sponserid = :sp";
    } else {
        $sql = "UPDATE tbl_pool_matrix 
                SET $column = :userid 
                WHERE sponserid = :sp";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':userid' => $userid,
        ':sp'     => $sp
    ]);

    /* ---------- INSERT NEW POOL ENTRY ---------- */
    $sql2 = "INSERT INTO tbl_pool_matrix (sponserid, status) 
             VALUES (:userid, '1')";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([':userid' => $userid]);

    /* ---------- INSERT DOWNLINE ---------- */
    $sql3 = "INSERT INTO tbl_downline (upline_id, downline_id, date, time)
             VALUES (:sp, :userid, CURDATE(), :time)";
    $stmt3 = $pdo->prepare($sql3);
    $stmt3->execute([
        ':sp'     => $sp,
        ':userid' => $userid,
        ':time'   => $cenvertedTime
    ]);

    /* ---------- FETCH USER DATA ---------- */
    $stmt4 = $pdo->prepare("SELECT * FROM user WHERE userid = :userid LIMIT 1");
    $stmt4->execute([':userid' => $userid]);
    $row1 = $stmt4->fetch(PDO::FETCH_ASSOC);

    $mysponserid1 = $userid;

    /* ---------- LEVEL INCOME LOGIC ---------- */
    for ($i = 1; $i <= 15; $i++) {

        $mysponserid1 = getmydownlineid($mysponserid1);
        if ($mysponserid1 == "") {
            continue;
        }

        insert_userlevel($mysponserid1, $userid, $i);

        // switch ($i) {
        //     case 1: $amt = 50; break;
        //     case 2: $amt = 10; break;
        //     case 3: $amt = 10; break;
        //     case 4: $amt = 5;  break;
        //     case 5: $amt = 5;  break;
        //     case 6: $amt = 5;  break;
        //     case 7: $amt = 5;  break;
        //     case 8: $amt = 5;  break;
        //     default: $amt = 0;
        // }

        // updatedatabysponserid1($mysponserid1, $amt);

        // insert_transction1(
        //     $mysponserid1,
        //     $amt,
        //     'Level Income Added on level (' . $i . ') of joining (' . $userid . ')',
        //     'CREDIT'
        // );
    }

    updatecount($userid);
    ?>
    <script>
        alert('User Id Activated Successfully');
        window.location.assign('index.php');
    </script>
    <?php
    die();
}


function checkmycountnew($recviverid)
{
    global $pdo;

    $sqlac = "select COUNT(*) as alluser from tbl_pool_matrix where sponserid = :recviverid";
    $stmt = $pdo->prepare($sqlac);
    $stmt->execute([':recviverid' => $recviverid]);

    while ($rowac = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alluser = $rowac['alluser'];
        return $alluser;
    }
}

function updatecount($downlineid)
{
    global $pdo;

    $sqluser = "SELECT * from tbl_pool_matrix 
        where position1 = :downlineid 
        OR position2 = :downlineid"; 

    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':downlineid' => $downlineid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $CHESPID = $rowuser['sponserid'];

        $position1  = $rowuser['position1'];
        $position2  = $rowuser['position2'];
        // $position3  = $rowuser['position3'];
        // $position4  = $rowuser['position4'];
        // $position5  = $rowuser['position5'];
        // $position6  = $rowuser['position6'];
        // $position7  = $rowuser['position7'];
        // $position8  = $rowuser['position8'];
        // $position9  = $rowuser['position9'];
        // $position10 = $rowuser['position10'];

        if ($position1 == $downlineid) {
            $sql = "UPDATE tbl_pool_matrix set count1 = count1 + 1 where sponserid = :sp";
        } elseif ($position2 == $downlineid) {
            $sql = "UPDATE tbl_pool_matrix set count2 = count2 + 1 where sponserid = :sp";
        } 
        // elseif ($position3 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count3 = count3 + 1 where sponserid = :sp";
        // } elseif ($position4 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count4 = count4 + 1 where sponserid = :sp";
        // } elseif ($position5 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count5 = count5 + 1 where sponserid = :sp";
        // } elseif ($position6 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count6 = count6 + 1 where sponserid = :sp";
        // } elseif ($position7 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count7 = count7 + 1 where sponserid = :sp";
        // } elseif ($position8 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count8 = count8 + 1 where sponserid = :sp";
        // } elseif ($position9 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count9 = count9 + 1 where sponserid = :sp";
        // } elseif ($position10 == $downlineid) {
        //     $sql = "UPDATE tbl_pool_matrix set count10 = count10 + 1 where sponserid = :sp";
        // }

        $stmt2 = $pdo->prepare($sql);
        if ($stmt2->execute([':sp' => $CHESPID])) {
            if ($CHESPID !== '1290') {
                updatecount($CHESPID);
            }
        }
    }
}

function checkleftandrightcount($sponserid)
{
    global $pdo;

    $sqluser = "SELECT * from tbl_pool_matrix 
                where sponserid = :sponserid 
                and status = '1'";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':sponserid' => $sponserid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $count1  = $rowuser['count1'];
        $count2  = $rowuser['count2'];
        // $count3  = $rowuser['count3'];
        // $count4  = $rowuser['count4'];
        // $count5  = $rowuser['count5'];
        // $count6  = $rowuser['count6'];
        // $count7  = $rowuser['count7'];
        // $count8  = $rowuser['count8'];
        // $count9  = $rowuser['count9'];
        // $count10 = $rowuser['count10'];

        if ($count1 < $count2 ) {
            return "position1";
        } 
        // else if ($count2 < $count1 && $count2 < $count3 && $count2 < $count4 && $count2 < $count5 && $count2 < $count6 && $count2 < $count7 && $count2 < $count8 && $count2 < $count9 && $count2 < $count10) {
        //     return "position2";
        // } else if ($count3 < $count1 && $count3 < $count2 && $count3 < $count4 && $count3 < $count5 && $count3 < $count6 && $count3 < $count7 && $count3 < $count8 && $count3 < $count9 && $count3 < $count10) {
        //     return "position3";
        // } else if ($count4 < $count1 && $count4 < $count3 && $count4 < $count2 && $count4 < $count5 && $count4 < $count6 && $count4 < $count7 && $count4 < $count8 && $count4 < $count9 && $count4 < $count10) {
        //     return "position4";
        // } else if ($count5 < $count1 && $count5 < $count2 && $count5 < $count3 && $count5 < $count4 && $count5 < $count6 && $count5 < $count7 && $count5 < $count8 && $count5 < $count9 && $count5 < $count10) {
        //     return "position5";
        // } else if ($count6 < $count1 && $count6 < $count2 && $count6 < $count3 && $count6 < $count4 && $count6 < $count5 && $count6 < $count7 && $count6 < $count8 && $count6 < $count9 && $count6 < $count10) {
        //     return "position6";
        // } else if ($count7 < $count1 && $count7 < $count2 && $count7 < $count3 && $count7 < $count4 && $count7 < $count5 && $count7 < $count6 && $count7 < $count8 && $count7 < $count9 && $count7 < $count10) {
        //     return "position7";
        // } else if ($count8 < $count1 && $count8 < $count2 && $count8 < $count3 && $count8 < $count4 && $count8 < $count5 && $count8 < $count6 && $count8 < $count7 && $count8 < $count9 && $count8 < $count10) {
        //     return "position8";
        // } else if ($count9 < $count1 && $count9 < $count2 && $count9 < $count3 && $count9 < $count4 && $count9 < $count5 && $count9 < $count6 && $count9 < $count7 && $count9 < $count8 && $count9 < $count10) {
        //     return "position9";
        // } else if ($count10 < $count1 && $count10 < $count2 && $count10 < $count3 && $count10 < $count4 && $count10 < $count5 && $count10 < $count6 && $count10 < $count7 && $count10 < $count8 && $count10 < $count9) {
        //     return "position10";
        // } 
        else {
            return "position2";
        }
    }
}

function getmydownlineid($userid)
{
    global $pdo;

    $sqluser = "SELECT * from tbl_downline where downline_id = :userid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':userid' => $userid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mysponserid = $rowuser['upline_id'];
        return $mysponserid;
    }
}

// function insert_userlevel($sponserid, $downlineid, $level)
// {
//     global $pdo;

//     $sql = "INSERT INTO tbl_userlevel (sponser_id, downline_id, level, date)
//             VALUES (:sponserid, :downlineid, :level, CURDATE())";

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([
//         ':sponserid'  => $sponserid,
//         ':downlineid' => $downlineid,
//         ':level'      => $level
//     ]);
// }

function insert_transction1($userid, $amount, $trasction_type, $cdtype)
{
    global $pdo;

    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }

    $date = date('Y-m-d');
    $time = date('h:i a');

    $sql = "INSERT INTO tbl_transaction 
        (user_id, subject, type, amount, status, a_status, time, created_date)
        VALUES (:userid, :trasction_type, :cdtype, :amount, '1', '0', :time, :date)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':userid'          => $userid,
        ':trasction_type'  => $trasction_type,
        ':cdtype'          => $cdtype,
        ':amount'          => $amount,
        ':time'            => $time,
        ':date'            => $date
    ]);
}

function updatedatabysponserid1($userid, $amount)
{
    global $pdo;

    $sqluser = "UPDATE user SET amount = amount + :amount WHERE userid = :userid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([
        ':amount' => $amount,
        ':userid' => $userid
    ]);
}

function update_user_amount_minus($userid, $amount)
{
    global $pdo;

    $sqluser = "UPDATE user SET amount = amount - :amount WHERE userid = :userid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([
        ':amount' => $amount,
        ':userid' => $userid
    ]);
}

function get_userdata($spid)
{
    global $pdo;

    $sqluser = "SELECT * from tbl_pool_matrix where sponserid = :spid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':spid' => $spid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $rowuser['id'];
    }
}

function get_freeid($uid, $spid)
{
    global $pdo;

    $sqluser1 = "SELECT * from tbl_pool_matrix 
                 WHERE fill_status = '0' AND id > :uid 
                 ORDER BY id ASC";
    $stmt = $pdo->prepare($sqluser1);
    $stmt->execute([':uid' => $uid]);

    while ($rowuser1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $upline_id = $rowuser1['sponserid'];
        if ($upline_id !== '') {
            return $upline_id;
        } else {
            return 0;
        }
    }
}

function get_userdata_sponsor($userid, $uplineid)
{
    global $pdo;

    $sqluser = "SELECT * from tbl_downline where downline_id = :userid";
    $stmt = $pdo->prepare($sqluser);
    $stmt->execute([':userid' => $userid]);

    while ($rowuser = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $upline_id = $rowuser['upline_id'];
        if ($upline_id == $uplineid) {
            return 1;
        } else {
            if ($upline_id !== '') {
                return get_userdata_sponsor($upline_id, $uplineid);
            } else {
                return 0;
            }
        }
    }
}

function checkTXNentry($userid, $level)
{
    global $pdo;

    $sqlac = "SELECT COUNT(*) as alluser  
              FROM tbl_transaction 
              WHERE user_id = :userid 
                AND subject = :level 
                AND status = '1'";

    $stmt = $pdo->prepare($sqlac);
    $stmt->execute([
        ':userid' => $userid,
        ':level'  => $level
    ]);

    while ($rowac = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $alluser = $rowac['alluser'];
        return $alluser;
    }
}

 ?>