<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php'; ?>

<?php

if (isset($_POST['submit'])) {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $mobile   = $_POST['mobile'];
    $father   = '';
    $country  = '';
    $state    = '';
    $address  = '';
    $gender   = '';
    $pin_code = '';

    // Handle image upload with 200KB check
    $maxFileSize = 200 * 1024;
    $adhar_front_img_name = $_FILES['image']['name'] ?? '';
    $adhar_front_img_size = $_FILES['image']['size'] ?? 0;

    if ($adhar_front_img_name != '') {

        if ($adhar_front_img_size <= $maxFileSize) {

            $targetPath = 'images/' . basename($adhar_front_img_name);
            $upload = move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $targetPath
            );

            if ($upload) {

                $stmt = $pdo->prepare(
                    "UPDATE user SET user_image = :image WHERE userid = :userid"
                );

                $stmt->execute([
                    'image'  => $adhar_front_img_name,
                    'userid' => $userid
                ]);

                echo '<script>
                    alert("Image uploaded successfully.");
                    window.location="index.php";
                </script>';

            } else {

                echo '<script>
                    alert("Error uploading image. Please try again.");
                </script>';
            }

        } else {

            echo '<script>
                alert("Please upload an image smaller than 200KB.");
                window.location="profile.php";
            </script>';
        }
    }

    // Update profile details
    $stmt = $pdo->prepare("UPDATE user SET
        name     = :name,
        mobile   = :mobile,
        gender   = :gender,
        email    = :email,
        father   = :father,
        country  = :country,
        state    = :state,
        address  = :address,
        pin_code = :pin_code
        WHERE userid = :userid
    ");

    $updated = $stmt->execute([
        'name'     => $name,
        'mobile'   => $mobile,
        'gender'   => $gender,
        'email'    => $email,
        'father'   => $father,
        'country'  => $country,
        'state'    => $state,
        'address'  => $address,
        'pin_code' => $pin_code,
        'userid'   => $userid
    ]);

    if ($updated) {

        echo '<script>
            alert("Profile Updated Successfully");
            window.location.href = "index.php";
        </script>';
    }
}

$txn_msg = '';
$txn_msg_type = 'info';

if (isset($_POST['send_txn_otp'])) {
    $resOtp = sendTransactionKeyOTP($userid);
    $txn_msg = $resOtp['message'];
    $txn_msg_type = ($resOtp['status'] === 'success') ? 'success' : 'danger';
}

if (isset($_POST['verify_txn_otp'])) {
    $otpCode = trim($_POST['otp_code'] ?? '');
    $resVer = verifyTransactionKeyOTP($userid, $otpCode);
    $txn_msg = $resVer['message'];
    if ($resVer['status'] === 'success') {
        $_SESSION['txn_otp_verified'] = true;
        $txn_msg_type = 'success';
    } else {
        $txn_msg_type = 'danger';
    }
}

if (isset($_POST['reset_txn_key'])) {
    if (empty($_SESSION['txn_otp_verified'])) {
        $txn_msg = "Please verify OTP sent to your registered email first.";
        $txn_msg_type = "danger";
    } else {
        $newKey = trim($_POST['new_txn_key'] ?? '');
        $confirmKey = trim($_POST['confirm_txn_key'] ?? '');

        if ($newKey === '' || $confirmKey === '') {
            $txn_msg = "Please enter and confirm your new Transaction Key.";
            $txn_msg_type = "danger";
        } elseif ($newKey !== $confirmKey) {
            $txn_msg = "New Transaction Key and Confirm Key do not match.";
            $txn_msg_type = "danger";
        } else {
            $resSet = setTransactionKey($userid, $newKey);
            if ($resSet['status'] === 'success') {
                unset($_SESSION['txn_otp_verified']);
                echo "<script>alert('Transaction Key changed successfully.');window.location.assign('profile.php');</script>";
                exit;
            } else {
                $txn_msg = $resSet['message'];
                $txn_msg_type = "danger";
            }
        }
    }
}
?>

<style>

/* =========================================================
   PROFILE PAGE
========================================================= */

html,
body {
    min-height: 100%;
}

body.ananta-user-dashboard {
    background: #f6f8fb !important;
    margin: 0;
}

/* Main profile area */
.profile-page-wrapper {
    width: 100%;
    min-height: calc(100vh - 80px);
    background: #f6f8fb;
    padding: 22px 24px 110px;
}

/* Remove old colorful background if inherited */
.profile-page-wrapper,
.profile-page-wrapper * {
    box-sizing: border-box;
}

/* =========================================================
   MAIN PROFILE CARD
========================================================= */

.profile-main-card {
    width: 100%;
    max-width: none;
    margin: 0;
    background: #ffffff;

    border: 1px solid #e5eaf0;
    border-radius: 22px;

    box-shadow:
        0 10px 35px rgba(15, 23, 42, 0.07),
        0 2px 8px rgba(15, 23, 42, 0.03);

    overflow: hidden;
}

/* =========================================================
   CARD HEADER
========================================================= */

.profile-card-header {
    display: flex;
    align-items: center;
    gap: 16px;

    padding: 26px 30px;

    border-bottom: 1px solid #e8edf3;

    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fbfdff 60%,
            #f5fbf8 100%
        );
}

.profile-header-icon {
    width: 52px;
    height: 52px;

    min-width: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 16px;

    background: linear-gradient(
        135deg,
        rgba(11, 94, 215, 0.10),
        rgba(34, 164, 71, 0.12)
    );

    color: #0B5ED7;

    font-size: 21px;

    border: 1px solid rgba(11, 94, 215, 0.10);
}

.profile-header-title {
    margin: 0;

    color: #111827;

    font-size: 22px;
    font-weight: 800;

    letter-spacing: -0.3px;
}

.profile-header-subtitle {
    margin: 4px 0 0;

    color: #64748b;

    font-size: 13px;
    font-weight: 500;
}

/* =========================================================
   FORM AREA
========================================================= */

.profile-form-area {
    padding: 30px;
}

/* Two column layout */
.profile-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 22px 26px;
}

/* =========================================================
   FORM GROUP
========================================================= */

.profile-field {
    width: 100%;
}

.profile-label {
    display: block;

    margin-bottom: 8px;

    color: #1e293b;

    font-size: 11px;
    font-weight: 800;

    letter-spacing: 0.65px;
    text-transform: uppercase;
}

/* Inputs */
.profile-input {
    width: 100%;

    height: 50px;

    padding: 0 15px;

    border-radius: 11px;

    border: 1px solid #d7dee8 !important;

    background: #ffffff !important;

    color: #0f172a !important;

    font-size: 14px;
    font-weight: 600;

    outline: none;

    box-shadow: none !important;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.profile-input:focus {
    border-color: #0B5ED7 !important;

    background: #ffffff !important;

    box-shadow:
        0 0 0 3px rgba(11, 94, 215, 0.08) !important;
}

/* Readonly fields */
.profile-input[readonly] {
    background: #f8fafc !important;
    color: #334155 !important;
    cursor: default;
}

/* Account status */
.profile-status-active {
    color: #16a34a !important;
    font-weight: 800 !important;
}

.profile-status-pending {
    color: #ef4444 !important;
    font-weight: 800 !important;
}

/* =========================================================
   FILE INPUT
========================================================= */

.profile-file-input {
    width: 100%;

    height: 50px;

    padding: 7px 10px;

    border-radius: 11px;

    border: 1px solid #d7dee8 !important;

    background: #ffffff !important;

    color: #334155 !important;

    font-size: 13px;
    font-weight: 500;
}

.profile-file-input::file-selector-button {
    margin-right: 10px;

    height: 34px;

    padding: 0 14px;

    border: 0;

    border-radius: 8px;

    background: #0B5ED7;

    color: #ffffff;

    font-size: 12px;
    font-weight: 700;

    cursor: pointer;
}

.profile-upload-note {
    display: block;

    margin-top: 7px;

    color: #ef4444;

    font-size: 11px;

    font-weight: 700;
}

/* =========================================================
   UPDATE BUTTON AREA
========================================================= */

.profile-submit-area {
    grid-column: 1 / -1;

    display: flex;

    align-items: center;
    justify-content: center;

    padding-top: 10px;
}

.profile-update-btn {
    min-width: 230px;

    height: 48px;

    padding: 0 28px;

    border: 0;

    border-radius: 12px;

    background: linear-gradient(
        135deg,
        #0B5ED7 0%,
        #0788c9 48%,
        #22A447 100%
    );

    color: #ffffff;

    font-size: 13px;
    font-weight: 800;

    letter-spacing: 0.3px;

    box-shadow:
        0 8px 20px rgba(11, 94, 215, 0.20);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.profile-update-btn:hover {
    color: #ffffff;

    transform: translateY(-2px);

    box-shadow:
        0 12px 26px rgba(11, 94, 215, 0.27);
}

/* =========================================================
   FOOTER
========================================================= */

/*
   Footer stays at bottom without covering the form.
*/

.profile-footer-space {
    width: 100%;

    min-height: 80px;
}

/* =========================================================
   REMOVE OLD COLORFUL BACKGROUNDS
========================================================= */

.content-wrapper:has(.profile-page-wrapper) {
    background: #f6f8fb !important;
}

.content-wrapper:has(.profile-page-wrapper) .container-fluid {
    background: #f6f8fb !important;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 991px) {

    .profile-page-wrapper {
        padding: 18px 18px 100px;
    }

    .profile-card-header {
        padding: 22px;
    }

    .profile-form-area {
        padding: 22px;
    }

    .profile-grid {
        gap: 18px;
    }
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 767px) {

    .profile-page-wrapper {
        padding: 12px 10px 95px;
    }

    .profile-main-card {
        border-radius: 17px;
    }

    .profile-card-header {
        padding: 18px;

        gap: 12px;
    }

    .profile-header-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;

        border-radius: 13px;

        font-size: 18px;
    }

    .profile-header-title {
        font-size: 18px;
    }

    .profile-header-subtitle {
        font-size: 11px;
    }

    .profile-form-area {
        padding: 18px;
    }

    /* One column */
    .profile-grid {
        grid-template-columns: 1fr;

        gap: 16px;
    }

    .profile-submit-area {
        grid-column: 1;
    }

    .profile-update-btn {
        width: 100%;
        min-width: 0;
    }
}

/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .profile-page-wrapper {
        padding: 8px 7px 90px;
    }

    .profile-form-area {
        padding: 14px;
    }

    .profile-card-header {
        padding: 16px;
    }

    .profile-header-title {
        font-size: 17px;
    }

    .profile-header-subtitle {
        font-size: 10.5px;
    }

    .profile-input,
    .profile-file-input {
        height: 47px;
    }

    .profile-label {
        font-size: 10.5px;
    }
}

</style>


<body class="ananta-user-dashboard">

<div id="wrapper">

    <div class="clearfix"></div>

    <!-- =====================================================
         PROFILE CONTENT
    ====================================================== -->

    <div class="content-wrapper">
        <div class="container-fluid pt-3 px-4">
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">Settings</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">View Profile</span>
                </div>
            </nav>
        </div>

        <div class="profile-page-wrapper">

            <div class="profile-main-card">

                <!-- =================================================
                     PROFILE HEADER
                ================================================== -->

                    <!-- Profile Header -->
                    <div class="profile-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-header-avatar">
                                <img
                                    src="<?php echo !empty($userimage) ? 'images/' . htmlspecialchars($userimage) : 'images/usera.png'; ?>"
                                    alt="Profile Photo"
                                    onerror="this.src='images/usera.png';"
                                >
                            </div>

                            <div class="profile-header-content">
                                <h1 class="profile-header-title">
                                    Profile Details &amp; Settings
                                </h1>
                                <p class="profile-header-subtitle mb-0">
                                    Manage your account details and profile photo
                                </p>
                            </div>
                        </div>

                        <!-- Referral Toggle Button in Profile Header -->
                        <div>
                            <button type="button" class="btn text-white font-weight-bold px-3.5 py-2 d-inline-flex align-items-center shadow-sm" onclick="toggleProfileReferralSection()" style="background: linear-gradient(135deg, #0284c7 0%, #00b4d8 100%); border-radius: 12px; border: none; font-size: 13px; letter-spacing: 0.3px;">
                                <i class="zmdi zmdi-share mr-2" style="font-size: 16px;"></i> Referral Links & QR
                            </button>
                        </div>
                    </div>

                    <!-- =================================================
                         REFERRAL LINKS & QR CODES SECTION (PROFILE)
                    ================================================== -->
                    <?php 
                    $left_link = $hmurl . "user1/register.php?refferalId=" . $userid . "&position=left";
                    $left_qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($left_link);
                    
                    $right_link = $hmurl . "user1/register.php?refferalId=" . $userid . "&position=right";
                    $right_qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($right_link);
                    ?>

                    <div id="profileReferralSection" class="p-3 p-md-4 mb-4" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 0 0 22px 22px; display: block;">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
                            <div>
                                <h5 class="font-weight-bold mb-1" style="color: #0f172a; font-size: 18px;">Your Referral Links &amp; QR Codes</h5>
                                <p class="text-muted small mb-0" style="font-size: 13px;">Share your personal referral links or QR codes directly to invite new team members.</p>
                            </div>
                            <!-- Social Media Links -->
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted small font-weight-bold mr-1">Social:</span>
                                <a href="https://youtube.com/@anantamelodyverse?si=qIDQyBt9kS0s4A0F" target="_blank" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #FF0000;" title="YouTube">
                                    <i class="fa fa-youtube"></i>
                                </a>
                                <a href="https://www.instagram.com/anantamelodyverses?igsh=NmQ1NGltY3VqZGhw&utm_source=qr" target="_blank" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);" title="Instagram">
                                    <i class="fa fa-instagram"></i>
                                </a>
                                <a href="https://www.facebook.com/profile.php?id=61585786533006" target="_blank" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #1877F2;" title="Facebook">
                                    <i class="fa fa-facebook"></i>
                                </a>
                                <a href="https://wa.me/?text=<?php echo urlencode('Register on Ananta: ' . $left_link); ?>" target="_blank" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #25D366;" title="WhatsApp">
                                    <i class="fa fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>

                        <div class="row align-items-stretch">
                            <!-- Left Referral Card -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="p-3 bg-white h-100 d-flex flex-column justify-content-between" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold small text-uppercase mb-0" style="color: #0284c7; letter-spacing: 0.5px;"><i class="fa fa-arrow-left me-1"></i> Left Placement Link</label>
                                            <span class="badge px-2 py-1" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; border-radius: 6px; font-size: 11px;">LEFT SIDE</span>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 my-3">
                                            <div class="p-2 bg-white rounded-lg shadow-sm text-center" style="border: 1px solid #cbd5e1; border-radius: 12px !important; flex-shrink: 0;">
                                                <img src="<?php echo $left_qr_api; ?>" alt="Left QR Code" style="width: 100px; height: 100px; border-radius: 8px;">
                                                <span class="d-block small text-muted mt-1 font-weight-bold" style="font-size: 10px;">SCAN TO REGISTER</span>
                                            </div>
                                            <div class="flex-grow-1 w-100">
                                                <div class="input-group mb-2">
                                                    <input type="text" id="leftLinkInputProfile" class="form-control form-control-sm" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; background: #ffffff !important; color: #0f172a !important; font-size: 12.5px; font-weight: 600;" value="<?php echo $left_link; ?>" readonly>
                                                    <button type="button" onclick="copyTextProfile('leftLinkInputProfile')" class="btn btn-sm font-weight-bold text-white" style="border-radius: 0 8px 8px 0; background: #0284c7; border: none; padding: 6px 14px;">Copy</button>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" onclick="shareLinkProfile('Left Placement Link', '<?php echo $left_link; ?>')" class="btn btn-sm btn-outline-info font-weight-bold flex-grow-1" style="border-radius: 8px;">
                                                        <i class="fa fa-share-alt me-1"></i> Share Link
                                                    </button>
                                                    <a href="https://wa.me/?text=<?php echo urlencode('Register on Ananta (Left Side): ' . $left_link); ?>" target="_blank" class="btn btn-sm text-white font-weight-bold" style="background: #25D366; border-radius: 8px;" title="Share to WhatsApp">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Referral Card -->
                            <div class="col-md-6">
                                <div class="p-3 bg-white h-100 d-flex flex-column justify-content-between" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold small text-uppercase mb-0" style="color: #16a34a; letter-spacing: 0.5px;">Right Placement Link <i class="fa fa-arrow-right ms-1"></i></label>
                                            <span class="badge px-2 py-1" style="background: rgba(22, 163, 74, 0.1); color: #16a34a; border-radius: 6px; font-size: 11px;">RIGHT SIDE</span>
                                        </div>

                                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 my-3">
                                            <div class="p-2 bg-white rounded-lg shadow-sm text-center" style="border: 1px solid #cbd5e1; border-radius: 12px !important; flex-shrink: 0;">
                                                <img src="<?php echo $right_qr_api; ?>" alt="Right QR Code" style="width: 100px; height: 100px; border-radius: 8px;">
                                                <span class="d-block small text-muted mt-1 font-weight-bold" style="font-size: 10px;">SCAN TO REGISTER</span>
                                            </div>
                                            <div class="flex-grow-1 w-100">
                                                <div class="input-group mb-2">
                                                    <input type="text" id="rightLinkInputProfile" class="form-control form-control-sm" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; background: #ffffff !important; color: #0f172a !important; font-size: 12.5px; font-weight: 600;" value="<?php echo $right_link; ?>" readonly>
                                                    <button type="button" onclick="copyTextProfile('rightLinkInputProfile')" class="btn btn-sm font-weight-bold text-white" style="border-radius: 0 8px 8px 0; background: #16a34a; border: none; padding: 6px 14px;">Copy</button>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" onclick="shareLinkProfile('Right Placement Link', '<?php echo $right_link; ?>')" class="btn btn-sm btn-outline-success font-weight-bold flex-grow-1" style="border-radius: 8px;">
                                                        <i class="fa fa-share-alt me-1"></i> Share Link
                                                    </button>
                                                    <a href="https://wa.me/?text=<?php echo urlencode('Register on Ananta (Right Side): ' . $right_link); ?>" target="_blank" class="btn btn-sm text-white font-weight-bold" style="background: #25D366; border-radius: 8px;" title="Share to WhatsApp">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    function toggleProfileReferralSection() {
                        const sec = document.getElementById('profileReferralSection');
                        if (sec) {
                            if (sec.style.display === 'none') {
                                sec.style.display = 'block';
                                sec.scrollIntoView({ behavior: 'smooth' });
                            } else {
                                sec.style.display = 'none';
                            }
                        }
                    }

                    function copyTextProfile(inputId) {
                        const input = document.getElementById(inputId);
                        if (input) {
                            input.select();
                            input.setSelectionRange(0, 99999);
                            navigator.clipboard.writeText(input.value);
                            alert('Referral link copied to clipboard!');
                        }
                    }

                    function shareLinkProfile(title, url) {
                        if (navigator.share) {
                            navigator.share({
                                title: title,
                                text: 'Join Ananta Multi Trade platform using my referral link:',
                                url: url
                            }).catch(err => console.log('Error sharing:', err));
                        } else {
                            navigator.clipboard.writeText(url);
                            alert('Referral link copied to clipboard: ' + url);
                        }
                    }
                    </script>

<style>
.profile-header-avatar {
    width: 64px;
    height: 64px;
    min-width: 64px;
    padding: 3px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0B5ED7, #22A447);
    box-shadow: 0 6px 18px rgba(11, 94, 215, 0.18);
    overflow: hidden;
}

.profile-header-avatar img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    border-radius: 50%;
    background: #f8fafc;
    border: 2px solid #ffffff;
}

.profile-header-content {
    min-width: 0;
}

@media (max-width: 767px) {
    .profile-header-avatar {
        width: 52px;
        height: 52px;
        min-width: 52px;
    }

    .profile-header-title {
        font-size: 18px;
    }

    .profile-header-subtitle {
        font-size: 11px;
    }
}
</style>

                </div>


                <!-- =================================================
                     PROFILE FORM
                ================================================== -->

                <div class="profile-form-area">

                    <form method="POST" enctype="multipart/form-data">

                        <div class="profile-grid">

                            <!-- ===============================
                                 USER ID
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    User ID
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="userid"
                                    id="userid"
                                    value="<?php echo $hmpre; ?><?php echo $userid; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 JOINING DATE
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Joining Date
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="joining_date"
                                    id="joining_date"
                                    value="<?php echo $dateofjoining; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 FULL NAME
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Full Name
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="<?php echo $username; ?>"
                                >

                            </div>


                            <!-- ===============================
                                 SPONSOR ID
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Sponsor ID
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="sponserid"
                                    id="sponserid"
                                    value="<?php echo $hmpre; ?><?php echo $usersponser; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 MOBILE NUMBER
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Mobile Number
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="mobile"
                                    id="mobile"
                                    value="<?php echo $usermobile; ?>"
                                >

                            </div>


                            <!-- ===============================
                                 SPONSOR NAME
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Sponsor Name
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="sponsername"
                                    id="sponsername"
                                    value="<?php echo $usersponsername; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 EMAIL
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Email Address
                                </label>

                                <input
                                    class="profile-input"
                                    type="text"
                                    name="email"
                                    id="email"
                                    value="<?php echo $useremail; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 ACCOUNT STATUS
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Account Status
                                </label>

                                <input
                                    class="profile-input <?php echo ($idactive == 1) ? 'profile-status-active' : 'profile-status-pending'; ?>"
                                    type="text"
                                    name="status"
                                    id="status"
                                    value="<?php echo ($idactive == 1) ? 'Active' : 'Pending'; ?>"
                                    readonly
                                >

                            </div>


                            <!-- ===============================
                                 PROFILE PHOTO
                            ================================ -->

                            <div class="profile-field">

                                <label class="profile-label">
                                    Profile Photo
                                </label>

                                <input
                                    class="profile-file-input"
                                    type="file"
                                    name="image"
                                    id="image"
                                >

                                <small class="profile-upload-note">
                                    Please upload image up to 200 KB only
                                </small>

                            </div>


                            <!-- ===============================
                                 UPDATE BUTTON
                            ================================ -->

                            <div class="profile-submit-area">

                                <button
                                    type="submit"
                                    name="submit"
                                    class="profile-update-btn"
                                >

                                    <i class="fa fa-check-circle me-1"></i>

                                    UPDATE PROFILE DETAILS

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <!-- =================================================
                 SECURITY & TRANSACTION KEY RESET CARD
            ================================================== -->
            <div class="profile-main-card mt-4" id="securityTxnSection">
                <div class="profile-card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-header-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                            <i class="fa fa-key" style="font-size: 20px;"></i>
                        </div>
                        <div>
                            <h2 class="profile-header-title" style="font-size: 20px;">Security &amp; Transaction Key</h2>
                            <p class="profile-header-subtitle mb-0">Reset or change your Transaction Key using Email OTP Verification</p>
                        </div>
                    </div>
                    <span class="badge" style="background: rgba(22, 163, 74, 0.12); color: #16a34a; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 12px;">
                        <i class="fa fa-shield me-1"></i> OTP Protected
                    </span>
                </div>

                <div class="profile-form-area p-4">
                    <?php if (!empty($txn_msg)): ?>
                        <div class="alert alert-<?php echo $txn_msg_type; ?> border-0 mb-4" style="border-radius: 12px; font-weight: 600;">
                            <i class="fa fa-info-circle me-2"></i> <?php echo htmlspecialchars($txn_msg); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Left Column: Send & Verify OTP -->
                        <div class="col-md-6 border-end-md pr-md-4">
                            <h5 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Forgot Transaction Key?</h5>
                            <p class="text-muted small mb-4" style="font-size: 13px;">Click below to receive a 6-digit OTP on your registered email address.</p>

                            <form method="POST" class="mb-4">
                                <div class="form-group mb-3">
                                    <label class="profile-label">Registered Email Address</label>
                                    <input type="email" class="profile-input" value="<?php echo htmlspecialchars($useremail); ?>" readonly style="background: #f8fafc;">
                                </div>

                                <button type="submit" name="send_txn_otp" class="btn text-white font-weight-bold w-100" style="height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0284c7 0%, #00b4d8 100%); border: none; font-size: 14px;">
                                    <i class="fa fa-paper-plane me-1"></i> Send OTP to Registered Email
                                </button>
                            </form>

                            <hr class="my-4">

                            <!-- Step 2: Verify OTP -->
                            <form method="POST">
                                <div class="form-group mb-3">
                                    <label class="profile-label">Enter 6-Digit OTP <span class="text-danger">*</span></label>
                                    <input type="text" name="otp_code" class="profile-input" placeholder="Enter OTP received in email" maxlength="6" required style="font-family: monospace; font-size: 16px; letter-spacing: 3px;">
                                    <small class="text-muted" style="font-size: 11.5px;">OTP expires in 10 minutes and is valid for single use.</small>
                                </div>

                                <button type="submit" name="verify_txn_otp" class="btn text-white font-weight-bold w-100" style="height: 48px; border-radius: 12px; background: #0f172a; border: none; font-size: 14px;">
                                    <i class="fa fa-check-circle me-1"></i> Verify OTP
                                </button>
                            </form>
                        </div>

                        <!-- Right Column: Set New Transaction Key -->
                        <div class="col-md-6 pl-md-4 mt-4 mt-md-0">
                            <h5 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Set New Transaction Key</h5>
                            <p class="text-muted small mb-4" style="font-size: 13px;">
                                <?php if (!empty($_SESSION['txn_otp_verified'])): ?>
                                    <span class="text-success font-weight-bold"><i class="fa fa-check-circle me-1"></i> OTP Verified! Enter your new Transaction Key below.</span>
                                <?php else: ?>
                                    <span>Please verify OTP on the left before setting a new Transaction Key.</span>
                                <?php endif; ?>
                            </p>

                            <form method="POST">
                                <div class="form-group mb-3">
                                    <label class="profile-label">New Transaction Key <span class="text-danger">*</span></label>
                                    <input type="password" name="new_txn_key" class="profile-input" placeholder="Enter new Transaction Key (min 4 chars)" required <?php echo empty($_SESSION['txn_otp_verified']) ? 'disabled' : ''; ?>>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="profile-label">Confirm New Transaction Key <span class="text-danger">*</span></label>
                                    <input type="password" name="confirm_txn_key" class="profile-input" placeholder="Re-enter new Transaction Key" required <?php echo empty($_SESSION['txn_otp_verified']) ? 'disabled' : ''; ?>>
                                </div>

                                <button type="submit" name="reset_txn_key" class="btn text-white font-weight-bold w-100" style="height: 48px; border-radius: 12px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); border: none; font-size: 14px;" <?php echo empty($_SESSION['txn_otp_verified']) ? 'disabled' : ''; ?>>
                                    <i class="fa fa-save me-1"></i> Save New Transaction Key
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Space before footer -->
            <div class="profile-footer-space"></div>

        </div>

    </div>


    <!-- =====================================================
         BACK TO TOP
    ====================================================== -->

    <a
        href="javaScript:void(0);"
        class="back-to-top"
    >
        <i class="fa fa-angle-double-up"></i>
    </a>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <?php include 'common/footer.php'; ?>


</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/app-script.js"></script>

</body>
</html>