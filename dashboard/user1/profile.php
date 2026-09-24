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

                <div class="profile-card-header">

                    <!-- Profile Header -->
<div class="profile-card-header">

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

        <p class="profile-header-subtitle">
            Manage your account details and profile photo
        </p>

    </div>

</div>

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