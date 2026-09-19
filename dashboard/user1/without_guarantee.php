<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php
include "common/header.php";

$day = date("d");
$currentTime = date("H:i:s");

$percenset = getpercentage();
$level1 = $percenset["level1"];
$level2 = $percenset["level2"];
$level3 = $percenset["level3"];
$level4 = $percenset["level4"];
$level5 = $percenset["level5"];
$level6 = $percenset["level6"];
$level7 = $percenset["level7"];

$stmt = $pdo->prepare("SELECT * FROM tbl_package ORDER BY id ASC");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["submit"])) {

    $userid2 = trim($_POST['userid']);
    $activate_userid = substr($userid2, 2);

    $idactive = getuserdatabysponserid($activate_userid);
    $side1 = $idactive['join_side'];

    if ($idactive['idactive'] == "1") {
        echo '<script>alert("ID is already active.");window.location.href = "index.php";</script>';
        exit;
    }

    $package_id = $_POST['package_id'] ?? null;
    $price = $_POST["price"];
    $amount = $price;

    if ($package_id) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_package WHERE id = :id");
        $stmt->execute([":id" => $package_id]);
        $rowheader = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $rowheader = [];
    }

    $percentage = $rowheader["income"] ?? 10;
    $packname = "Without Guarantee";
    $days = 1350;
    $lockdays = 1350;

    if (1 == 1) {

        if ($price > 0) {

            if ($pin_wallet >= $amount) {

                if (1 == 1) {

                    $inc_limitpackage = (int) 2 * (int) $price;

                    // Update fund wallet of logged-in user
                    $stmt1 = $pdo->prepare("
                        UPDATE user
                        SET pin_wallet = pin_wallet - :price,
                            upgrade_date = :date,
                            atime = :time
                        WHERE userid = :userid
                    ");

                    $stmt1->execute([
                        ":price" => $price,
                        ":date" => $date,
                        ":time" => $time,
                        ":userid" => $userid,
                    ]);

                    // Update targeted user
                    $stmt = $pdo->prepare("
                        UPDATE user
                        SET inc_limit = :inc_limitpackage,
                            package = :price,
                            total_package = total_package + :price,
                            plan = :packname,
                            active = '1',
                            upgrade_date = :date,
                            atime = :time
                        WHERE userid = :userid
                    ");

                    $stmt->execute([
                        ":inc_limitpackage" => $inc_limitpackage,
                        ":price" => $price,
                        ":packname" => $packname,
                        ":date" => $date,
                        ":time" => $time,
                        ":userid" => $activate_userid,
                    ]);

                    $subject1 = "Id Activation Using Fund- $price";

                    $sql1 = "
                        INSERT INTO tbl_transaction
                        (user_id, amount, type, subject, time, created_date, status)
                        VALUES
                        (:userid, :price, 'Credit', :subject1, :time, :date, '1')
                    ";

                    $stmt1 = $pdo->prepare($sql1);

                    if (
                        $stmt1->execute([
                            ":userid" => $activate_userid,
                            ":price" => $price,
                            ":subject1" => $subject1,
                            ":time" => $time,
                            ":date" => $date,
                        ])
                    ) {

                        $sqluser1 = "
                            INSERT INTO tbl_roi_one
                            (user_id, name, package, percentage, date, time, status, lock_day, capping)
                            VALUES
                            (:userid, :name, :price, :percentage, :date, :time, '0', :lockdays, :inc_limitpackage)
                        ";

                        $stmt2 = $pdo->prepare($sqluser1);

                        $stmt2->execute([
                            ":userid" => $activate_userid,
                            ":name" => $packname,
                            ":price" => $price,
                            ":percentage" => $percentage,
                            ":date" => $date,
                            ":time" => $time,
                            ":lockdays" => $lockdays,
                            ":inc_limitpackage" => $inc_limitpackage,
                        ]);

                        $pinfinal = $activate_userid;

                        $mysponserid = getmysponserid($pinfinal);
                        $sponserdetails = getuserdatabysponserid($mysponserid);

                        $spcode1 = $sponserdetails["userid"];
                        $active = $sponserdetails['idactive'];

                        if ($active == "1") {

                            $percentage = 0;

                            $amount = (int) str_replace(',', '', $price);

                            if ($amount >= 12000 && $amount <= 80000) {
                                $percentage = 10;
                            } elseif ($amount >= 81000 && $amount <= 100000) {
                                $percentage = 8;
                            } elseif ($amount >= 101000 && $amount <= 300000) {
                                $percentage = 7;
                            } elseif ($amount >= 301000 && $amount <= 500000) {
                                $percentage = 5.5;
                            } elseif ($amount >= 501000 && $amount <= 900000) {
                                $percentage = 4.5;
                            } elseif ($amount >= 901000 && $amount <= 1200000) {
                                $percentage = 3.5;
                            } elseif ($amount >= 1201000 && $amount <= 2000000) {
                                $percentage = 3;
                            } else {
                                $percentage = 10;
                            }

                            $insert = $pdo->prepare("
                                INSERT INTO tbl_roi_two
                                (user_id, name, package, percentage, lock_day, date, time, status)
                                VALUES
                                (:user_id, :name, :package, :percentage, :lock_day, :date, :time, :status)
                            ");

                            $insert->execute([
                                ':user_id' => $spcode1,
                                ':name' => $packname,
                                ':package' => ($price * $percentage) / 100,
                                ':percentage' => $percentage,
                                ':lock_day' => 10,
                                ':date' => date("Y-m-d"),
                                ':time' => date("H:i:s"),
                                ':status' => 0
                            ]);
                        }
                    }

                    $pinfinal = $activate_userid;

                    for ($i = 0; $i < 1; $i++) {

                        $mysponserid = getmysponserid($pinfinal);
                        $sponserdetails = getuserdatabysponserid($mysponserid);

                        if ($pinfinal !== "1290") {

                            $spcode1 = $sponserdetails["userid"];
                            $spamont = $sponserdetails["amount"];
                            $isidactive = $sponserdetails["idactive"];
                            $directactive = getmydirectactive($spcode1);

                            if ($isidactive == "1") {

                                if ($i == "0") {

                                    if ($price >= 12000 && $price <= 80000) {
                                        $percentage = 2.5;
                                    } elseif ($price >= 81000 && $price <= 400000) {
                                        $percentage = 2;
                                    } elseif ($price >= 400001 && $price <= 800000) {
                                        $percentage = 1.8;
                                    } elseif ($price >= 800001 && $price <= 1200000) {
                                        $percentage = 1.3;
                                    } elseif ($price >= 1200001 && $price <= 2000000) {
                                        $percentage = 1;
                                    } else {
                                        $percentage = 0;
                                    }

                                    $transactionamount = ($price * $percentage) / 100;
                                }

                                $pinfinal = $spcode1;

                                if (isset($transactionamount)) {

                                    $new = $i + 1;
                                    $level = $new;
                                    $messagenew = "Direct Income of Id ($activate_userid)";

                                    insert_transction(
                                        "tbl_levelinc",
                                        $spcode1,
                                        $transactionamount,
                                        $date,
                                        $messagenew,
                                        $time,
                                        "Credit"
                                    );

                                    updatenonworkwallet(
                                        $spcode1,
                                        $transactionamount,
                                        $pdo
                                    );
                                }

                            } else {
                                $pinfinal = $spcode1;
                            }
                        }
                    }

                    echo '<script>alert("Thank you for ID ACTIVATION"); window.location.href = "index.php";</script>';

                } else {
                    echo '<script>alert("Please choose amount multiple of 50");</script>';
                }

            } else {
                echo '<script>alert("Your Fund Wallet Low");</script>';
            }
        }

    } else {
        echo '<script>alert("Please Activate Id after 5 AM");</script>';
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Without Guarantee Program</title>

    <style>/* =========================================================
   ACTIVATE WITHOUT GUARANTEE
   PREMIUM WHITE FINTECH UI
   FULL CSS
========================================================= */


/* =========================================================
   GLOBAL RESET
========================================================= */

html,
body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

html {
    background: #ffffff !important;
}

body {
    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;
    color: #0f172a !important;
}


/* =========================================================
   REMOVE OLD THEME BACKGROUNDS
========================================================= */

body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-user-dashboard.bg-theme,
body.ananta-user-dashboard.bg-theme1 {

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;

    color: #0f172a !important;
}


/* =========================================================
   REMOVE THEME PSEUDO ELEMENTS
========================================================= */

html::before,
html::after,
body::before,
body::after,
#wrapper::before,
#wrapper::after,
.content-wrapper::before,
.content-wrapper::after,
.activation-page-wrapper::before,
.activation-page-wrapper::after {

    content: none !important;
    display: none !important;

    background: none !important;
    background-color: transparent !important;
    background-image: none !important;
}


/* =========================================================
   MAIN WRAPPER
========================================================= */

#wrapper {

    width: 100% !important;
    min-height: 100vh !important;

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;

    color: #0f172a !important;
}


/* =========================================================
   CONTENT WRAPPER
========================================================= */

.content-wrapper {

    width: auto !important;
    min-height: calc(100vh - 70px) !important;

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;

    color: #0f172a !important;
}


/* =========================================================
   FORCE ALL DIRECT CONTENT CHILDREN WHITE
========================================================= */

.content-wrapper > div,
.content-wrapper > section,
.content-wrapper > main {

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;
}


/* =========================================================
   ACTIVATION PAGE WRAPPER
========================================================= */

.activation-page-wrapper {

    width: 100% !important;

    min-height: calc(100vh - 80px) !important;

    padding: 22px 24px 100px !important;

    box-sizing: border-box !important;

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;

    color: #0f172a !important;
}


/* =========================================================
   MAIN ACTIVATION CARD
========================================================= */

.activation-main-card {

    width: 100% !important;
    max-width: 900px !important;

    margin: 0 auto !important;

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;

    border: 1px solid #e2e8f0 !important;

    border-radius: 22px !important;

    box-shadow:
        0 10px 35px rgba(15, 23, 42, 0.06) !important;

    overflow: hidden !important;
}


/* =========================================================
   CARD HEADER
========================================================= */

.activation-card-header {

    display: flex !important;

    align-items: center !important;

    justify-content: space-between !important;

    gap: 20px !important;

    padding: 26px 30px !important;

    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fbfdff 60%,
            #f5fbf8 100%
        ) !important;

    border-bottom: 1px solid #e8edf3 !important;
}


/* =========================================================
   HEADER LEFT
========================================================= */

.activation-header-left {

    display: flex !important;

    align-items: center !important;

    gap: 16px !important;

    min-width: 0 !important;
}


/* =========================================================
   HEADER ICON
========================================================= */

.activation-header-icon {

    width: 52px !important;
    height: 52px !important;

    min-width: 52px !important;

    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    border-radius: 16px !important;

    background:
        linear-gradient(
            135deg,
            rgba(2, 132, 199, 0.12),
            rgba(22, 163, 74, 0.12)
        ) !important;

    border: 1px solid rgba(2, 132, 199, 0.15) !important;

    color: #0284c7 !important;

    font-size: 22px !important;
}


/* =========================================================
   TITLE
========================================================= */

.activation-title {

    margin: 0 !important;

    color: #0f172a !important;

    font-size: 22px !important;

    font-weight: 800 !important;

    line-height: 1.25 !important;

    letter-spacing: -0.3px !important;
}


/* =========================================================
   SUBTITLE
========================================================= */

.activation-subtitle {

    margin: 5px 0 0 !important;

    color: #64748b !important;

    font-size: 13px !important;

    font-weight: 500 !important;

    line-height: 1.5 !important;
}


/* =========================================================
   FUND WALLET BADGE
========================================================= */

.activation-wallet-badge {

    padding: 10px 18px !important;

    min-width: 150px !important;

    border-radius: 16px !important;

    background: rgba(2, 132, 199, 0.06) !important;

    background-color: rgba(2, 132, 199, 0.06) !important;

    border: 1px solid rgba(2, 132, 199, 0.18) !important;

    text-align: right !important;

    box-sizing: border-box !important;
}


.wallet-badge-label {

    display: block !important;

    font-size: 11px !important;

    font-weight: 800 !important;

    color: #64748b !important;

    text-transform: uppercase !important;

    letter-spacing: 0.5px !important;

    line-height: 1.4 !important;
}


.wallet-badge-amount {

    display: block !important;

    margin-top: 2px !important;

    font-size: 20px !important;

    font-weight: 800 !important;

    color: #0284c7 !important;

    line-height: 1.3 !important;
}


/* =========================================================
   FORM AREA
========================================================= */

.activation-form-area {

    padding: 30px !important;

    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;
}


/* =========================================================
   FORM FIELD
========================================================= */

.activation-field {

    margin-bottom: 22px !important;

    width: 100% !important;
}


/* =========================================================
   LABEL
========================================================= */

.activation-label {

    display: block !important;

    margin-bottom: 8px !important;

    color: #1e293b !important;

    font-size: 11.5px !important;

    font-weight: 800 !important;

    letter-spacing: 0.55px !important;

    text-transform: uppercase !important;

    line-height: 1.4 !important;
}


/* =========================================================
   INPUT GROUP
========================================================= */

.activation-input-group {

    position: relative !important;

    display: flex !important;

    align-items: center !important;

    width: 100% !important;
}


/* =========================================================
   INPUT ICON
========================================================= */

.activation-input-icon {

    position: absolute !important;

    left: 16px !important;

    top: 50% !important;

    transform: translateY(-50%) !important;

    z-index: 2 !important;

    color: #0284c7 !important;

    font-size: 17px !important;

    pointer-events: none !important;
}


/* =========================================================
   INPUT
========================================================= */

.activation-input {

    width: 100% !important;

    height: 52px !important;

    padding: 0 16px 0 46px !important;

    box-sizing: border-box !important;

    border-radius: 12px !important;

    border: 1px solid #cbd5e1 !important;

    background: #ffffff !important;

    background-color: #ffffff !important;

    color: #0f172a !important;

    font-size: 14px !important;

    font-weight: 600 !important;

    outline: none !important;

    box-shadow: none !important;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease !important;
}


/* =========================================================
   INPUT HOVER
========================================================= */

.activation-input:hover {

    border-color: #94a3b8 !important;
}


/* =========================================================
   INPUT FOCUS
========================================================= */

.activation-input:focus {

    border-color: #0284c7 !important;

    background: #ffffff !important;

    box-shadow:
        0 0 0 3px rgba(2, 132, 199, 0.10) !important;
}


/* =========================================================
   PLACEHOLDER
========================================================= */

.activation-input::placeholder {

    color: #94a3b8 !important;

    opacity: 1 !important;
}


/* =========================================================
   NUMBER INPUT ARROWS
========================================================= */

.activation-input[type="number"]::-webkit-inner-spin-button,
.activation-input[type="number"]::-webkit-outer-spin-button {

    opacity: 0.6;
}


/* =========================================================
   VERIFIED MEMBER BOX
========================================================= */

.member-verified-box {

    display: none;

    margin-top: -6px !important;

    margin-bottom: 22px !important;

    padding: 14px 18px !important;

    border-radius: 14px !important;

    background: #f0fdf4 !important;

    background-color: #f0fdf4 !important;

    border: 1px solid #bbf7d0 !important;

    box-sizing: border-box !important;
}


/* =========================================================
   VERIFIED LABEL
========================================================= */

.member-verified-label {

    display: block !important;

    margin-bottom: 4px !important;

    font-size: 11px !important;

    font-weight: 800 !important;

    color: #166534 !important;

    text-transform: uppercase !important;

    letter-spacing: 0.5px !important;
}


/* =========================================================
   VERIFIED INPUT
========================================================= */

.member-verified-input {

    width: 100% !important;

    padding: 0 !important;

    border: none !important;

    background: transparent !important;

    color: #15803d !important;

    font-size: 15px !important;

    font-weight: 800 !important;

    outline: none !important;

    box-shadow: none !important;
}


/* =========================================================
   SUBMIT BUTTON
========================================================= */

.activation-submit-btn {

    width: 100% !important;

    height: 52px !important;

    padding: 0 20px !important;

    border: none !important;

    border-radius: 12px !important;

    background:
        linear-gradient(
            135deg,
            #0284c7 0%,
            #16a34a 100%
        ) !important;

    color: #ffffff !important;

    font-size: 15px !important;

    font-weight: 800 !important;

    letter-spacing: 0.3px !important;

    box-shadow:
        0 10px 25px rgba(2, 132, 199, 0.20) !important;

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        opacity 0.25s ease !important;

    cursor: pointer !important;

    display: flex !important;

    align-items: center !important;

    justify-content: center !important;

    gap: 8px !important;
}


/* =========================================================
   BUTTON HOVER
========================================================= */

.activation-submit-btn:hover {

    color: #ffffff !important;

    transform: translateY(-2px) !important;

    box-shadow:
        0 14px 30px rgba(2, 132, 199, 0.28) !important;
}


/* =========================================================
   BUTTON ACTIVE
========================================================= */

.activation-submit-btn:active {

    transform: translateY(0) !important;

    box-shadow:
        0 8px 18px rgba(2, 132, 199, 0.20) !important;
}


/* =========================================================
   BUTTON DISABLED
========================================================= */

.activation-submit-btn:disabled {

    opacity: 0.55 !important;

    transform: none !important;

    cursor: not-allowed !important;

    box-shadow: none !important;
}


/* =========================================================
   BOOTSTRAP / THEME OVERRIDE
========================================================= */

.activation-main-card,
.activation-card-header,
.activation-form-area,
.activation-page-wrapper {

    background-image: none !important;
}


/* =========================================================
   DESKTOP
========================================================= */

@media (min-width: 992px) {

    .activation-page-wrapper {

        padding:
            28px
            28px
            100px
            28px !important;
    }

    .activation-main-card {

        max-width: 930px !important;
    }
}


/* =========================================================
   TABLET
========================================================= */

@media (min-width: 768px) and (max-width: 991px) {

    .activation-page-wrapper {

        padding:
            20px
            18px
            90px !important;
    }

    .activation-main-card {

        max-width: 100% !important;
    }

    .activation-card-header {

        padding: 22px !important;
    }

    .activation-form-area {

        padding: 24px !important;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 767px) {

    html,
    body,
    body.ananta-user-dashboard,
    #wrapper,
    .content-wrapper,
    .activation-page-wrapper {

        background: #ffffff !important;

        background-color: #ffffff !important;

        background-image: none !important;
    }


    .content-wrapper {

        min-height: 100vh !important;
    }


    .activation-page-wrapper {

        width: 100% !important;

        min-height: calc(100vh - 70px) !important;

        padding:
            12px
            10px
            90px !important;

        box-sizing: border-box !important;
    }


    .activation-main-card {

        width: 100% !important;

        max-width: 100% !important;

        border-radius: 18px !important;

        box-shadow:
            0 8px 25px rgba(15, 23, 42, 0.05) !important;
    }


    .activation-card-header {

        display: flex !important;

        flex-direction: column !important;

        align-items: flex-start !important;

        justify-content: flex-start !important;

        gap: 16px !important;

        padding: 18px !important;
    }


    .activation-header-left {

        width: 100% !important;

        gap: 12px !important;
    }


    .activation-header-icon {

        width: 46px !important;

        height: 46px !important;

        min-width: 46px !important;

        border-radius: 14px !important;

        font-size: 19px !important;
    }


    .activation-title {

        font-size: 18px !important;

        line-height: 1.3 !important;
    }


    .activation-subtitle {

        font-size: 12px !important;

        line-height: 1.45 !important;
    }


    .activation-wallet-badge {

        width: 100% !important;

        min-width: 0 !important;

        padding: 11px 14px !important;

        text-align: left !important;

        border-radius: 14px !important;
    }


    .wallet-badge-label {

        font-size: 10px !important;
    }


    .wallet-badge-amount {

        font-size: 18px !important;
    }


    .activation-form-area {

        padding: 18px !important;

        background: #ffffff !important;

        background-image: none !important;
    }


    .activation-field {

        margin-bottom: 18px !important;
    }


    .activation-label {

        margin-bottom: 7px !important;

        font-size: 10.5px !important;
    }


    .activation-input {

        height: 50px !important;

        font-size: 14px !important;

        padding-left: 44px !important;
    }


    .activation-input-icon {

        left: 14px !important;

        font-size: 16px !important;
    }


    .member-verified-box {

        margin-bottom: 18px !important;

        padding: 13px 14px !important;
    }


    .activation-submit-btn {

        height: 50px !important;

        font-size: 14px !important;

        border-radius: 11px !important;
    }
}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 380px) {

    .activation-page-wrapper {

        padding:
            10px
            8px
            80px !important;
    }


    .activation-card-header {

        padding: 15px !important;
    }


    .activation-form-area {

        padding: 15px !important;
    }


    .activation-title {

        font-size: 17px !important;
    }


    .activation-subtitle {

        font-size: 11.5px !important;
    }
}


/* =========================================================
   FINAL FORCE — NO COLORFUL PAGE BACKGROUND
========================================================= */

body.ananta-user-dashboard #wrapper,
body.ananta-user-dashboard #wrapper .content-wrapper,
body.ananta-user-dashboard #wrapper .activation-page-wrapper {

    background: #ffffff !important;

    background-color: #ffffff !important;

    background-image: none !important;
}
    </style>
</head>

<body class="ananta-user-dashboard">

<div id="wrapper">

    <div class="clearfix"></div>

    <div class="content-wrapper">

        <div class="activation-page-wrapper">

            <div class="activation-main-card">

                <!-- Header -->
                <div class="activation-card-header">
                    <div class="activation-header-left">
                        <div class="activation-header-icon">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div>
                            <h3 class="activation-title">Activate Without Guarantee Program</h3>
                            <p class="activation-subtitle">Enter member details and package amount for instant activation</p>
                        </div>
                    </div>

                    <div class="activation-wallet-badge">
                        <span class="wallet-badge-label">Fund Wallet Balance</span>
                        <span class="wallet-badge-amount">$<?php echo number_format((float)$pin_wallet, 2); ?></span>
                    </div>
                </div>

                <!-- Form -->
                <div class="activation-form-area">
                    <form method="post">

                        <div class="activation-field">
                            <label class="activation-label" for="referrerId">User ID</label>
                            <div class="activation-input-group">
                                <i class="fa fa-user activation-input-icon"></i>
                                <input type="text" name="userid" class="activation-input" id="referrerId" required placeholder="Enter User ID (e.g. AN1290)" autocomplete="off">
                            </div>
                        </div>

                        <div class="member-verified-box" id="sponsor_name">
                            <span class="member-verified-label">Verified Member Name</span>
                            <input type="text" name="refferalid" id="response2" class="member-verified-input" readonly>
                        </div>

                        <div class="activation-field">
                            <label class="activation-label" for="price">Package Amount ($)</label>
                            <div class="activation-input-group">
                                <i class="fa fa-dollar-sign activation-input-icon"></i>
                                <input type="number" name="price" id="price" class="activation-input" required placeholder="Enter Package Amount">
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" name="submit" class="activation-submit-btn">
                            <i class="fa fa-check-circle me-1"></i> Activate ID Now
                        </button>

                    </form>
                </div>

            </div>

        </div>

    </div>

    <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <?php include 'common/footer.php'; ?>

</div>

<!-- JavaScript -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/app-script.js"></script>

<!-- AJAX User Name Verification -->
<script>
$(document).ready(function () {
    $("#submitBtn").prop("disabled", true);

    $("#referrerId").on("blur", function () {
        let refId = $(this).val().trim();

        if (refId.length <= 2) {
            $("#sponsor_name").hide();
            $("#response2").val("");
            $("#submitBtn").prop("disabled", true);
            return;
        }

        $.ajax({
            type: "POST",
            url: "checkName.php",
            data: { data: refId },
            success: function (response) {
                response = $.trim(response);
                if (response !== "0" && response !== "") {
                    $("#response2").val(response);
                    $("#sponsor_name").stop(true, true).slideDown();
                    $("#submitBtn").prop("disabled", false);
                } else {
                    $("#sponsor_name").stop(true, true).slideUp();
                    $("#response2").val("");
                    $("#submitBtn").prop("disabled", true);
                    alert("Invalid or inactive user ID!");
                }
            },
            error: function () {
                $("#sponsor_name").hide();
                $("#response2").val("");
                $("#submitBtn").prop("disabled", true);
                alert("Unable to verify User ID. Please try again.");
            }
        });
    });
});
</script>

</body>
</html>