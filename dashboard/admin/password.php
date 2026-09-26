<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';
include 'common/connection.php'; // contains $pdo

$msg = '';
$msgType = '';

// ---------------- 1. CHANGE ADMIN PASSWORD LOGIC ----------------
$stmt1 = $pdo->prepare("SELECT pass FROM admin WHERE id=1");
$stmt1->execute();
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

$current_pass = $row1['pass'] ?? '';

if (isset($_POST['submit'])) {
    $old = trim($_POST['old_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (empty($old) || empty($new) || empty($confirm)) {
        $msg = "All password fields are mandatory.";
        $msgType = "danger";
    } elseif ($new !== $confirm) {
        $msg = "New Password & Confirm Password do not match.";
        $msgType = "danger";
    } elseif ($old !== $current_pass) {
        $msg = "Invalid Old Password. Please verify and try again.";
        $msgType = "danger";
    } else {
        // Update password
        $stmt2 = $pdo->prepare("UPDATE admin SET pass = :newpass WHERE id=1");
        $stmt2->execute([':newpass' => $new]);
        $current_pass = $new;
        
        $msg = "Admin password updated successfully!";
        $msgType = "success";
    }
}

// ---------------- 2. GLOBAL DEFAULT PROFILE PICTURE LOGIC ----------------
// Ensure default_user_image column exists in tbl_homest
try {
    $pdo->exec("ALTER TABLE tbl_homest ADD COLUMN default_user_image VARCHAR(255) NULL");
} catch (Exception $e) {
    // Column already exists or permission caught safely
}

// Handle Global Profile Picture Upload & Bulk Update to All Users
if (isset($_POST['update_global_profile'])) {
    if (isset($_FILES['global_profile_pic']) && $_FILES['global_profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['global_profile_pic']['tmp_name'];
        $fileName = $_FILES['global_profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'global-profile-' . time() . '.' . $fileExtension;
            
            // Upload to user dashboard images folder (public_html/dashboard/user1/images/)
            $user1ImgDir = __DIR__ . '/../user1/images/';
            if (!is_dir($user1ImgDir)) {
                @mkdir($user1ImgDir, 0755, true);
            }
            $dest_path_user1 = $user1ImgDir . $newFileName;

            // Also copy to assets/images/
            $assetsImgDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/';
            if (!is_dir($assetsImgDir)) {
                @mkdir($assetsImgDir, 0755, true);
            }
            $dest_path_assets = $assetsImgDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path_user1)) {
                @copy($dest_path_user1, $dest_path_assets);

                // Store relative path for tbl_homest
                $relative_path = '/assets/images/' . $newFileName;
                $stmtUpdHome = $pdo->prepare("UPDATE tbl_homest SET default_user_image = :img WHERE id = 1");
                $stmtUpdHome->execute([':img' => $relative_path]);
                if ($stmtUpdHome->rowCount() == 0) {
                    $pdo->prepare("UPDATE tbl_homest SET default_user_image = :img")->execute([':img' => $relative_path]);
                }

                // Store image filename for user.user_image so header echo 'images/' . $userimage works cleanly
                $stmtUpdAllUsers = $pdo->prepare("UPDATE user SET user_image = :img");
                $stmtUpdAllUsers->execute([':img' => $newFileName]);
                $affectedUsers = $stmtUpdAllUsers->rowCount();

                $msg = "Success! Profile picture updated for ALL ({$affectedUsers}) existing users & set as active default for future registrations!";
                $msgType = "success";
            } else {
                $msg = "Error moving uploaded file to destination directory.";
                $msgType = "danger";
            }
        } else {
            $msg = "Invalid file format. Only JPG, JPEG, PNG, WEBP images are allowed.";
            $msgType = "danger";
        }
    } else {
        $msg = "Please select a valid image file to upload.";
        $msgType = "danger";
    }
}

// Fetch Active Default Profile Picture
$activeProfilePic = '';
try {
    $stmtHome = $pdo->query("SELECT default_user_image FROM tbl_homest LIMIT 1");
    if ($stmtHome && $rowH = $stmtHome->fetch(PDO::FETCH_ASSOC)) {
        $activeProfilePic = $rowH['default_user_image'] ?? '';
    }
} catch (Exception $e) {
    $activeProfilePic = '';
}
?>

<head>
<style>
.content-wrapper {
    background-color: #f8fafc !important;
    padding-top: 95px !important;
    padding-bottom: 80px !important;
    min-height: calc(100vh - 70px);
}

.password-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06) !important;
    max-width: 650px;
    margin: 0 auto;
}

.password-header-banner {
    background: #ffffff !important;
    border-bottom: 2px solid #f1f5f9;
    padding: 24px;
    border-radius: 20px 20px 0 0;
}

.neon-purple-badge {
    background: rgba(147, 51, 234, 0.08) !important;
    color: #9333ea !important;
    border: 1px solid rgba(147, 51, 234, 0.3) !important;
    font-weight: 800;
    font-size: 11px;
    letter-spacing: 0.5px;
    padding: 4px 12px;
    border-radius: 100px;
    box-shadow: 0 0 10px rgba(147, 51, 234, 0.15);
}

.neon-green-badge {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #059669 !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
    font-weight: 800;
    font-size: 11px;
    padding: 4px 12px;
    border-radius: 100px;
}

.form-label-custom {
    color: #000000 !important;
    font-weight: 800 !important;
    font-size: 13.5px !important;
    margin-bottom: 8px;
    display: block;
}

.input-password-wrapper {
    position: relative;
}

.input-password-wrapper .form-control {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    height: 48px !important;
    font-size: 14.5px !important;
    color: #000000 !important;
    font-weight: 700 !important;
    padding-right: 45px !important;
    transition: all 0.25s ease !important;
}

.input-password-wrapper .form-control:focus {
    border-color: #9333ea !important;
    box-shadow: 0 0 12px rgba(147, 51, 234, 0.25) !important;
    outline: none !important;
}

.btn-toggle-pwd {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #64748b;
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    z-index: 5;
}
.btn-toggle-pwd:hover {
    color: #9333ea;
}

.btn-update-password {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 48px !important;
    font-weight: 800 !important;
    font-size: 15px !important;
    letter-spacing: 0.3px;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3) !important;
    transition: all 0.3s ease !important;
}

.btn-update-password:hover {
    background: linear-gradient(135deg, #059669 0%, #0f172a 100%) !important;
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.35) !important;
    transform: translateY(-1px);
}
</style>

<script>
function togglePwdVis(fieldId, iconId) {
    var field = document.getElementById(fieldId);
    var icon = document.getElementById(iconId);
    if (field && icon) {
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fa fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fa fa-eye';
        }
    }
}
</script>
</head>

<body class="bg-theme bg-theme1">

<div id="wrapper">
<div class="content-wrapper">
  <div class="container-fluid">

      <div class="row justify-content-center">
          <div class="col-lg-8 col-md-10">

              <?php if (!empty($msg)): ?>
                  <div class="alert alert-<?php echo $msgType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                      <i class="fa fa-info-circle mr-2"></i> <?php echo htmlspecialchars($msg); ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              <?php endif; ?>

              <!-- CARD 1: CHANGE PASSWORD CONTAINER -->
              <div class="card password-card mb-4">
                  
                  <div class="password-header-banner text-center">
                      <div class="mb-2">
                          <span class="neon-purple-badge mr-2"><i class="fa fa-lock mr-1"></i> SECURITY CENTER</span>
                          <span class="neon-green-badge"><i class="fa fa-shield mr-1"></i> ADMIN ACCESS</span>
                      </div>
                      <h3 class="mb-1 font-weight-bold" style="color: #000000 !important; font-size: 24px;">Change Admin Password</h3>
                      <p class="mb-0 small" style="color: #475569 !important; font-weight: 600;">Update your administrator account password to keep system access secure.</p>
                  </div>

                  <div class="card-body p-4 p-md-5">

                      <!-- CHANGE PASSWORD FORM -->
                      <form method="POST">
                        
                        <!-- Old Password -->
                        <div class="form-group mb-4">
                            <label class="form-label-custom"><i class="fa fa-key text-success mr-2"></i> Old Password</label>
                            <div class="input-password-wrapper">
                                <input type="password" id="old_password" name="old_password" required class="form-control" placeholder="Enter current admin password">
                                <button type="button" class="btn-toggle-pwd" onclick="togglePwdVis('old_password', 'icon_old')">
                                    <i id="icon_old" class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="form-group mb-4">
                            <label class="form-label-custom"><i class="fa fa-lock text-primary mr-2"></i> New Password</label>
                            <div class="input-password-wrapper">
                                <input type="password" id="new_password" name="new_password" required class="form-control" placeholder="Enter new strong password">
                                <button type="button" class="btn-toggle-pwd" onclick="togglePwdVis('new_password', 'icon_new')">
                                    <i id="icon_new" class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mb-4">
                            <label class="form-label-custom" style="color: #9333ea !important;"><i class="fa fa-check-circle mr-2" style="color: #9333ea;"></i> Confirm Password</label>
                            <div class="input-password-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" required class="form-control" placeholder="Re-enter new password">
                                <button type="button" class="btn-toggle-pwd" onclick="togglePwdVis('confirm_password', 'icon_confirm')">
                                    <i id="icon_confirm" class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="submit" class="btn btn-update-password w-100 mt-2">
                            <i class="fa fa-refresh mr-2"></i> Update Password
                        </button>

                      </form>
                      <!-- END FORM -->

                  </div>
              </div>

              <!-- CARD 2: GLOBAL DEFAULT PROFILE PICTURE UPDATE CARD -->
              <div class="card password-card">
                  <div class="password-header-banner text-center">
                      <div class="mb-2">
                          <span class="neon-purple-badge mr-2"><i class="fa fa-users mr-1"></i> USER MANAGEMENT</span>
                          <span class="neon-green-badge"><i class="fa fa-picture-o mr-1"></i> GLOBAL PROFILE PIC</span>
                      </div>
                      <h3 class="mb-1 font-weight-bold" style="color: #000000 !important; font-size: 22px;">Update Profile Picture to All Users</h3>
                      <p class="mb-0 small" style="color: #475569 !important; font-weight: 600;">Upload a new profile image to replace all users' profile pictures at once & set active default for new registrations.</p>
                  </div>

                  <div class="card-body p-4 p-md-5 text-center">
                      <!-- Active Profile Picture Preview -->
                      <div class="mb-4">
                          <label class="form-label-custom mb-3"><i class="fa fa-eye text-primary mr-2"></i> Current Active Global Profile Picture</label>
                          <div class="d-flex flex-column align-items-center justify-content-center">
                              <?php if (!empty($activeProfilePic)): ?>
                                  <img src="<?php echo htmlspecialchars($activeProfilePic); ?>" alt="Active Default Profile Pic" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3.5px solid #10b981; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);" class="mb-2">
                                  <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 100px;">Active Profile Picture</span>
                              <?php else: ?>
                                  <div style="width: 100px; height: 100px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border: 2px dashed #cbd5e1; margin-bottom: 8px;">
                                      <i class="fa fa-user-circle-o text-muted" style="font-size: 55px;"></i>
                                  </div>
                                  <span class="badge badge-secondary px-3 py-1 font-weight-bold" style="border-radius: 100px;">Default System Profile</span>
                              <?php endif; ?>
                          </div>
                      </div>

                      <hr class="my-4" style="border-color: #e2e8f0;">

                      <!-- Upload & Replace Form -->
                      <form method="POST" enctype="multipart/form-data" class="text-left">
                          <div class="form-group mb-4">
                              <label class="form-label-custom"><i class="fa fa-upload text-success mr-2"></i> Select New Profile Picture to Replace All</label>
                              <input type="file" name="global_profile_pic" accept="image/*" required class="form-control p-1" style="height: 48px; border: 1.5px solid #cbd5e1; border-radius: 12px; background: #ffffff;">
                              <small class="text-muted font-weight-bold d-block mt-2"><i class="fa fa-info-circle mr-1"></i> Uploading a new picture will instantly update all existing users' profile pictures and set it as default for future signups.</small>
                          </div>

                          <button type="submit" name="update_global_profile" class="btn btn-update-password w-100" style="background: linear-gradient(135deg, #7c3aed 0%, #0284c7 100%) !important; box-shadow: 0 6px 20px rgba(124, 58, 237, 0.3) !important;">
                              <i class="fa fa-refresh mr-2"></i> Update Profile Picture For All Users
                          </button>
                      </form>
                  </div>
              </div>

          </div>
      </div>

  </div>
</div>

<?php include 'common/footer.php'; ?>

</div>

</body>
</html>