<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'common/header.php';
include 'common/connection.php'; // must contain $pdo

// ---------------- CHANGE PASSWORD LOGIC ----------------
$stmt1 = $pdo->prepare("SELECT pass FROM user WHERE userid = :userid LIMIT 1");
$stmt1->execute([':userid' => $userid]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

if (!$row1) {
    echo "<script>alert('User not found');window.location.assign('update-password.php');</script>";
    exit;
}

$current_pass = $row1['pass'];

if (isset($_POST['submit'])) {

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new != $confirm) {
        echo "<script>alert('New Password & Confirm Password do not match');window.location.assign('update-password.php');</script>";
        exit;
    }

    if ($old != $current_pass) {
        echo "<script>alert('Invalid Old Password');window.location.assign('update-password.php');</script>";
        exit;
    }

    // Update password
    $stmt2 = $pdo->prepare("UPDATE user SET pass = :newpass WHERE userid = :userid");
    $stmt2->execute([
        ':newpass' => $new,
        ':userid'  => $userid
    ]);

    echo "<script>alert('Password changed successfully');window.location.assign('update-password.php');</script>";
    exit;
}
?>


<style>

/* =========================================================
   CHANGE PASSWORD — PREMIUM ANANTA UI
========================================================= */

html,
body {
    min-height: 100%;
}

body.ananta-user-dashboard {
    margin: 0;
    background: #f6f8fb !important;
    color: #111827 !important;
}

/* Main page */
.password-page {
    width: 100%;
    min-height: calc(100vh - 80px);

    padding: 22px 24px 100px;

    background: #f6f8fb;
}

/* =========================================================
   MAIN CARD
========================================================= */

.password-card {
    width: 100%;
    max-width: none;

    margin: 0;

    background: #ffffff;

    border: 1px solid #e2e8f0;

    border-radius: 22px;

    box-shadow:
        0 10px 35px rgba(15, 23, 42, 0.07),
        0 2px 8px rgba(15, 23, 42, 0.03);

    overflow: hidden;
}

/* =========================================================
   HEADER
========================================================= */

.password-header {
    display: flex;
    align-items: center;

    gap: 15px;

    padding: 25px 30px;

    border-bottom: 1px solid #e5eaf0;

    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #fbfdff 65%,
        #f4fbf7 100%
    );
}

.password-header-icon {
    width: 54px;
    height: 54px;

    min-width: 54px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 16px;

    background: linear-gradient(
        135deg,
        rgba(11, 94, 215, 0.10),
        rgba(34, 164, 71, 0.12)
    );

    border: 1px solid rgba(11, 94, 215, 0.10);

    color: #0B5ED7;

    font-size: 21px;
}

.password-title {
    margin: 0;

    color: #111827 !important;

    font-size: 22px;

    font-weight: 800;

    letter-spacing: -0.3px;
}

.password-subtitle {
    margin: 4px 0 0;

    color: #64748b !important;

    font-size: 13px;

    font-weight: 500;
}

/* =========================================================
   FORM AREA
========================================================= */

.password-form-area {
    padding: 30px;

    max-width: 720px;

    margin: 0 auto;
}

/* =========================================================
   FIELD
========================================================= */

.password-field {
    margin-bottom: 20px;
}

.password-label {
    display: block;

    margin-bottom: 8px;

    color: #111827 !important;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 0.6px;

    text-transform: uppercase;
}

/* Input wrapper */
.password-input-wrap {
    position: relative;
}

/* Inputs */
.password-input {
    width: 100%;

    height: 51px;

    padding: 0 48px 0 15px;

    border-radius: 11px !important;

    border: 1px solid #d7dee8 !important;

    background: #ffffff !important;

    color: #111827 !important;

    font-size: 14px;

    font-weight: 600;

    outline: none;

    box-shadow: none !important;

    transition: all 0.2s ease;
}

.password-input:focus {
    border-color: #0B5ED7 !important;

    box-shadow:
        0 0 0 3px rgba(11, 94, 215, 0.08) !important;
}

.password-input::placeholder {
    color: #94a3b8 !important;
}

/* Eye button */
.password-toggle {
    position: absolute;

    right: 12px;
    top: 50%;

    transform: translateY(-50%);

    width: 32px;
    height: 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 0;

    border-radius: 8px;

    background: transparent;

    color: #64748b;

    cursor: pointer;

    transition: all 0.2s ease;
}

.password-toggle:hover {
    background: #eff6ff;

    color: #0B5ED7;
}

/* =========================================================
   PASSWORD REQUIREMENT BOX
========================================================= */

.password-info {
    display: flex;

    align-items: flex-start;

    gap: 10px;

    margin: 5px 0 24px;

    padding: 13px 15px;

    border-radius: 11px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    color: #64748b;

    font-size: 11px;

    line-height: 1.5;
}

.password-info i {
    color: #0B5ED7;

    margin-top: 2px;
}

/* =========================================================
   BUTTON
========================================================= */

.password-submit {
    width: 100%;

    height: 50px;

    border: 0 !important;

    border-radius: 12px !important;

    background: linear-gradient(
        135deg,
        #0B5ED7 0%,
        #0788c9 50%,
        #22A447 100%
    ) !important;

    color: #ffffff !important;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 0.3px;

    box-shadow:
        0 9px 22px rgba(11, 94, 215, 0.20);

    transition: all 0.2s ease;
}

.password-submit:hover {
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 13px 28px rgba(11, 94, 215, 0.28);
}

.password-submit:active {
    transform: translateY(0);
}

/* =========================================================
   FORCE TEXT VISIBILITY
========================================================= */

.password-card h1,
.password-card h2,
.password-card h3,
.password-card h4,
.password-card p,
.password-card label {
    color: #111827 !important;
}

.password-card .password-subtitle {
    color: #64748b !important;
}

.password-card input {
    color: #111827 !important;
}

/* =========================================================
   REMOVE OLD COLORFUL BACKGROUND
========================================================= */

.content-wrapper:has(.password-page),
.content-wrapper:has(.password-page) .container-fluid {
    background: #f6f8fb !important;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 991px) {

    .password-page {
        padding: 18px 18px 95px;
    }

    .password-header {
        padding: 22px;
    }

    .password-form-area {
        padding: 24px;
    }
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 767px) {

    .password-page {
        padding: 12px 10px 90px;
    }

    .password-card {
        border-radius: 17px;
    }

    .password-header {
        padding: 18px;
    }

    .password-header-icon {
        width: 46px;
        height: 46px;

        min-width: 46px;

        border-radius: 13px;

        font-size: 18px;
    }

    .password-title {
        font-size: 18px;
    }

    .password-subtitle {
        font-size: 11px;
    }

    .password-form-area {
        padding: 18px;
    }

    .password-input {
        height: 48px;

        font-size: 13px;
    }

    .password-submit {
        height: 48px;
    }
}

/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .password-page {
        padding: 8px 7px 85px;
    }

    .password-header {
        padding: 16px;
    }

    .password-form-area {
        padding: 14px;
    }

    .password-title {
        font-size: 17px;
    }

    .password-label {
        font-size: 10px;
    }
}

</style>


<body class="ananta-user-dashboard">

<div id="wrapper">

    <div class="clearfix"></div>


    <!-- =====================================================
         PASSWORD PAGE
    ====================================================== -->

    <div class="content-wrapper">

        <div class="password-page">

            <div class="password-card">


                <!-- =================================================
                     HEADER
                ================================================== -->

                <div class="password-header">

                    <div class="password-header-icon">
                        <i class="fa fa-key"></i>
                    </div>

                    <div>

                        <h3 class="password-title">
                            Change Password
                        </h3>

                        <p class="password-subtitle">
                            Update your login credentials securely
                        </p>

                    </div>

                </div>


                <!-- =================================================
                     FORM
                ================================================== -->

                <div class="password-form-area">

                    <form method="POST">


                        <!-- OLD PASSWORD -->

                        <div class="password-field">

                            <label class="password-label">
                                Current / Old Password
                            </label>

                            <div class="password-input-wrap">

                                <input
                                    type="password"
                                    name="old_password"
                                    id="old_password"
                                    required
                                    class="password-input"
                                    placeholder="Enter your current password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('old_password', this)"
                                    aria-label="Show password"
                                >
                                    <i class="fa fa-eye"></i>
                                </button>

                            </div>

                        </div>


                        <!-- NEW PASSWORD -->

                        <div class="password-field">

                            <label class="password-label">
                                New Password
                            </label>

                            <div class="password-input-wrap">

                                <input
                                    type="password"
                                    name="new_password"
                                    id="new_password"
                                    required
                                    class="password-input"
                                    placeholder="Enter your new password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('new_password', this)"
                                    aria-label="Show password"
                                >
                                    <i class="fa fa-eye"></i>
                                </button>

                            </div>

                        </div>


                        <!-- CONFIRM PASSWORD -->

                        <div class="password-field">

                            <label class="password-label">
                                Confirm New Password
                            </label>

                            <div class="password-input-wrap">

                                <input
                                    type="password"
                                    name="confirm_password"
                                    id="confirm_password"
                                    required
                                    class="password-input"
                                    placeholder="Confirm your new password"
                                >

                                <button
                                    type="button"
                                    class="password-toggle"
                                    onclick="togglePassword('confirm_password', this)"
                                    aria-label="Show password"
                                >
                                    <i class="fa fa-eye"></i>
                                </button>

                            </div>

                        </div>


                        <!-- INFO -->

                        <div class="password-info">

                            <i class="fa fa-shield"></i>

                            <span>
                                For better account security, use a strong password
                                that you do not use on other websites.
                            </span>

                        </div>


                        <!-- SUBMIT -->

                        <button
                            type="submit"
                            name="submit"
                            class="password-submit"
                        >

                            <i class="fa fa-lock me-1"></i>

                            Update Password Now

                        </button>


                    </form>

                </div>

            </div>

        </div>

    </div>


    <!-- BACK TO TOP -->

    <a
        href="javaScript:void();"
        class="back-to-top"
    >
        <i class="fa fa-angle-double-up"></i>
    </a>


    <!-- FOOTER -->

    <?php include 'common/footer.php'; ?>


</div>


<script>

/* =========================================================
   SHOW / HIDE PASSWORD
========================================================= */

function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    const icon = button.querySelector('i');

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('fa-eye');

        icon.classList.add('fa-eye-slash');

    } else {

        input.type = 'password';

        icon.classList.remove('fa-eye-slash');

        icon.classList.add('fa-eye');
    }
}

</script>


</body>
</html>