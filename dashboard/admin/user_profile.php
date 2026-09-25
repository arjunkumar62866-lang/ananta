<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';

$uid = $_GET['uid'] ?? '';
if (empty($uid)) {
    echo "<div class='content-wrapper'><div class='container-fluid'><div class='alert alert-warning mt-4'>Please select a user from member list to view profile.</div></div></div>";
    include 'common/footer.php';
    exit;
}
$cleanUid = preg_replace('/^(AN|ANANTA)/i', '', $uid);

// Handle basic profile update submit
if (isset($_POST['submit'])) {
    $sponsername = $_POST['sponsername'] ?? '';
    $name        = $_POST['name'] ?? '';
    $email       = $_POST['email'] ?? '';
    $mobile      = $_POST['mobile'] ?? '';
    $pass        = $_POST['pass'] ?? '';

    // Handle profile picture upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'profile_' . preg_replace('/[^A-Za-z0-9]/', '', $uid) . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imagePath = 'images/' . $newFileName;
            }
        }
    }

    if ($imagePath !== null) {
        $update = $pdo->prepare("UPDATE user SET sponsername=:sponsername, name=:name, mobile=:mobile, email=:email, pass=:pass, image=:image WHERE userid=:userid OR userid=:clean");
        $update->execute([
            ':sponsername' => $sponsername,
            ':name'        => $name,
            ':mobile'      => $mobile,
            ':email'       => $email,
            ':pass'        => $pass,
            ':image'       => $imagePath,
            ':userid'      => $uid,
            ':clean'       => $cleanUid
        ]);

        // Also sync to admin table if updating admin profile (1290 / AN1290 / admin)
        if ($uid === '1290' || $uid === 'AN1290' || strtolower($uid) === 'admin') {
            try {
                $updateAdmin = $pdo->prepare("UPDATE admin SET name=:name, mobile=:mobile, email=:email, pass=:pass, image=:image WHERE auserid IN ('admin', '1290', 'AN1290') OR id = 1");
                $updateAdmin->execute([
                    ':name'   => $name,
                    ':mobile' => $mobile,
                    ':email'  => $email,
                    ':pass'   => $pass,
                    ':image'  => $imagePath
                ]);
            } catch (Exception $e) {
                // Ignore if admin columns differ
            }
        }
    } else {
        $update = $pdo->prepare("UPDATE user SET sponsername=:sponsername, name=:name, mobile=:mobile, email=:email, pass=:pass WHERE userid=:userid OR userid=:clean");
        $update->execute([
            ':sponsername' => $sponsername,
            ':name'        => $name,
            ':mobile'      => $mobile,
            ':email'       => $email,
            ':pass'        => $pass,
            ':userid'      => $uid,
            ':clean'       => $cleanUid
        ]);

        if ($uid === '1290' || $uid === 'AN1290' || strtolower($uid) === 'admin') {
            try {
                $updateAdmin = $pdo->prepare("UPDATE admin SET name=:name, mobile=:mobile, email=:email, pass=:pass WHERE auserid IN ('admin', '1290', 'AN1290') OR id = 1");
                $updateAdmin->execute([
                    ':name'   => $name,
                    ':mobile' => $mobile,
                    ':email'  => $email,
                    ':pass'   => $pass
                ]);
            } catch (Exception $e) {
                // Ignore if admin columns differ
            }
        }
    }

    echo '<script>alert("Profile Updated Successfully"); window.location.href="user_profile.php?uid=' . urlencode($uid) . '";</script>';
    exit;
}

// Fetch target user record
$select = $pdo->prepare("SELECT * FROM user WHERE userid = :userid OR userid = :clean LIMIT 1");
$select->execute([':userid' => $uid, ':clean' => $cleanUid]);
$row = $select->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<div class='content-wrapper'><div class='container-fluid'><div class='alert alert-danger mt-4'>User account ({$uid}) not found.</div></div></div>";
    include 'common/footer.php';
    exit;
}

$targetUserId = $row['userid'];

$accountStatus = ($row['active'] == 1) ? "Active" : (($row['status'] == 2) ? "Blocked" : "Inactive");
$actStatus = getUserAccountActivationStatus($targetUserId, $pdo);

// Determine KYC Status
$kycVal = (int)($row['kyc'] ?? 0);
if ($kycVal === 2 || $kycVal === 1) {
    $kycStatusText = "Approved";
    $kycBadgeClass = "badge-success";
} elseif ($kycVal === 3) {
    $kycStatusText = "Rejected";
    $kycBadgeClass = "badge-danger";
} else {
    $kycStatusText = "Not Submitted";
    $kycBadgeClass = "badge-warning text-dark";
}

// Map the 12 Wallets to DB columns
$walletsConfig = [
    'amount' => [
        'name' => 'Main Wallet',
        'key'  => 'amount',
        'val'  => (float)($row['amount'] ?? 0.00),
        'icon' => 'fa-wallet',
        'color' => '#0284c7'
    ],
    'net_balance' => [
        'name' => 'Net Balance',
        'key'  => 'net_balance',
        'val'  => (float)($row['net_balance'] ?? 0.00),
        'icon' => 'fa-balance-scale',
        'color' => '#0d9488'
    ],
    'active_investment' => [
        'name' => 'Active Investment',
        'key'  => 'active_investment',
        'val'  => (float)($row['active_investment'] ?? 0.00),
        'icon' => 'fa-line-chart',
        'color' => '#16a34a'
    ],
    'total_withdrawal' => [
        'name' => 'All Withdrawal',
        'key'  => 'total_withdrawal',
        'val'  => (float)($row['total_withdrawal'] ?? 0.00),
        'icon' => 'fa-arrow-circle-down',
        'color' => '#ef4444'
    ],
    'profit_income_wallet' => [
        'name' => 'Profit Income',
        'key'  => 'profit_income_wallet',
        'val'  => (float)($row['profit_income_wallet'] ?? 0.00),
        'icon' => 'fa-money',
        'color' => '#8b5cf6'
    ],
    'profit_sharing_wallet' => [
        'name' => 'Profit Sharing',
        'key'  => 'profit_sharing_wallet',
        'val'  => (float)($row['profit_sharing_wallet'] ?? 0.00),
        'icon' => 'fa-share-alt',
        'color' => '#ec4899'
    ],
    'direct_bonus_wallet' => [
        'name' => 'Direct Bonus',
        'key'  => 'direct_bonus_wallet',
        'val'  => (float)($row['direct_bonus_wallet'] ?? 0.00),
        'icon' => 'fa-gift',
        'color' => '#f59e0b'
    ],
    'mentor_income_wallet' => [
        'name' => 'Mentor Income',
        'key'  => 'mentor_income_wallet',
        'val'  => (float)($row['mentor_income_wallet'] ?? 0.00),
        'icon' => 'fa-user-secret',
        'color' => '#6366f1'
    ],
    'rank_reward_wallet' => [
        'name' => 'Rank Reward',
        'key'  => 'rank_reward_wallet',
        'val'  => (float)($row['rank_reward_wallet'] ?? 0.00),
        'icon' => 'fa-trophy',
        'color' => '#eab308'
    ],
    'vip_club_wallet' => [
        'name' => 'VIP Club Income',
        'key'  => 'vip_club_wallet',
        'val'  => (float)($row['vip_club_wallet'] ?? 0.00),
        'icon' => 'fa-star',
        'color' => '#3b82f6'
    ],
    'user_growth_wallet' => [
        'name' => 'User Growth',
        'key'  => 'user_growth_wallet',
        'val'  => (float)($row['user_growth_wallet'] ?? 0.00),
        'icon' => 'fa-level-up',
        'color' => '#10b981'
    ],
    'company_turnover_wallet' => [
        'name' => 'Company Turnover Income',
        'key'  => 'company_turnover_wallet',
        'val'  => (float)($row['company_turnover_wallet'] ?? 0.00),
        'icon' => 'fa-building',
        'color' => '#14b8a6'
    ]
];

// Fetch Admin Audit History for this user
$stmtAudit = $pdo->prepare("
    SELECT * FROM tbl_admin_audit_log
    WHERE target_user_id = :uid
    ORDER BY id DESC
");
$stmtAudit->execute([':uid' => $targetUserId]);
$auditHistory = $stmtAudit->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* Modern Admin User Profile Styling */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}
body.ananta-admin-dashboard, body.bg-theme, body.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}
#wrapper {
    background: #f4f6f8 !important;
    min-height: 100vh !important;
}
.content-wrapper {
    background-color: #f4f6f8 !important;
    padding-top: 85px !important;
    padding-bottom: 60px !important;
}
.profile-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}
.profile-header-icon {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden;
    margin-bottom: 24px;
}
.card-header-bar {
    padding: 20px 24px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.card-header-bar h4 {
    margin: 0;
    font-weight: 800;
    color: #0f172a;
    font-size: 18px;
}
.form-group label {
    font-weight: 700;
    color: #475569;
    font-size: 13px;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-control {
    border-radius: 12px !important;
    border: 1.5px solid #cbd5e1 !important;
    padding: 10px 16px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
}
.form-control[readonly] {
    background-color: #f8fafc !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
}
.wallet-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
}
.wallet-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
}
.wallet-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 18px;
    flex-shrink: 0;
}
.btn-wallet-credit {
    background: #10b981;
    color: #ffffff;
    border: none;
    font-weight: 700;
    font-size: 13px;
    border-radius: 10px;
    padding: 8px 16px;
    transition: background 0.15s ease;
}
.btn-wallet-credit:hover {
    background: #059669;
    color: #ffffff;
}
.btn-wallet-debit {
    background: #ef4444;
    color: #ffffff;
    border: none;
    font-weight: 700;
    font-size: 13px;
    border-radius: 10px;
    padding: 8px 16px;
    transition: background 0.15s ease;
}
.btn-wallet-debit:hover {
    background: #dc2626;
    color: #ffffff;
}

/* Explicit High-Contrast Table Styling for Admin Audit History Table */
#adminAuditTable {
    background-color: #ffffff !important;
    color: #0f172a !important;
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
}
#adminAuditTable thead {
    background-color: #f8fafc !important;
}
#adminAuditTable thead th {
    color: #475569 !important;
    background-color: #f8fafc !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 14px 16px !important;
    border-bottom: 2px solid #cbd5e1 !important;
    white-space: nowrap !important;
}
#adminAuditTable tbody td {
    color: #0f172a !important;
    background-color: #ffffff !important;
    vertical-align: middle !important;
    padding: 14px 16px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    font-size: 14px !important;
}
#adminAuditTable tbody tr:hover td {
    background-color: #f8fafc !important;
}
#adminAuditTable .text-muted {
    color: #64748b !important;
}
#adminAuditTable .text-dark {
    color: #0f172a !important;
}
#adminAuditTable .text-secondary {
    color: #475569 !important;
}
#adminAuditTable .text-primary {
    color: #0284c7 !important;
}
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Card -->
        <style>
    .profile-header-card {
        overflow: hidden;
        width: 100%;
    }

    .profile-header-card .profile-content {
        min-width: 0;
        flex: 1;
    }

    .profile-header-card h3,
    .profile-header-card p {
        overflow-wrap: anywhere;
        word-break: break-word;
        max-width: 100%;
    }

    .profile-header-card .badge {
        white-space: nowrap;
    }

    .profile-header-card .profile-actions {
        flex-shrink: 0;
    }

    @media (max-width: 576px) {

        .profile-header-card {
            padding: 15px !important;
            border-radius: 12px;
        }

        .profile-header-card .profile-main {
            width: 100%;
            align-items: flex-start !important;
            flex-direction: column;
        }

        .profile-header-card .profile-info {
            width: 100%;
            min-width: 0;
        }

        .profile-header-card .profile-content {
            width: 100%;
            min-width: 0;
        }

        .profile-header-card h3 {
            font-size: 18px !important;
            line-height: 1.4;
        }

        .profile-header-card p {
            font-size: 12px !important;
            line-height: 1.7;
        }

        .profile-header-card .badge {
            font-size: 9px !important;
            padding: 5px 9px !important;
        }

        .profile-header-card .profile-badges {
            flex-wrap: wrap !important;
        }

        .profile-header-card .profile-header-icon {
            flex-shrink: 0;
        }

        .profile-header-card .profile-actions {
            width: 100%;
            margin-top: 5px;
        }

        .profile-header-card .profile-actions .btn {
            width: 100%;
            display: block;
            text-align: center;
            border-radius: 10px !important;
        }
    }
</style>


<div class="card profile-header-card p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 profile-main">

        <!-- Profile Left Section -->
        <div class="d-flex align-items-center gap-3 profile-info">

            <!-- Profile Icon / Avatar -->
            <div class="profile-header-icon overflow-hidden position-relative" style="width: 72px; height: 72px; border-radius: 50%; border: 3px solid #10b981; box-shadow: 0 4px 14px rgba(16,185,129,0.3);">
                <?php 
                $profilePic = !empty($row['image']) ? $row['image'] : '/assets/images/usera.png';
                ?>
                <img id="headerProfileAvatar" src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <!-- Profile Details -->
            <div class="profile-content">

                <!-- Badges -->
                <div class="d-flex align-items-center gap-2 mb-1 profile-badges">

                    <span class="badge badge-primary px-3 py-1"
                          style="border-radius:100px; font-size:11px; font-weight:700;">
                        MEMBER PROFILE
                    </span>

                    <span class="badge <?php echo ($accountStatus === 'Active') ? 'badge-success' : 'badge-danger'; ?> px-3 py-1"
                          style="border-radius:100px; font-size:11px; font-weight:700;">
                        <?php echo strtoupper($accountStatus); ?>
                    </span>

                    <span class="badge <?php echo $kycBadgeClass; ?> px-3 py-1"
                          style="border-radius:100px; font-size:11px; font-weight:700;">
                        KYC: <?php echo strtoupper($kycStatusText); ?>
                    </span>

                </div>

                <!-- Member Name -->
                <h3 class="mb-1"
                    style="font-weight:800; color:#0f172a;">
                    <?php echo htmlspecialchars($row['name']); ?>
                </h3>

                <!-- User / Sponsor Details -->
                <p class="mb-0"
                   style="color:#64748b; font-weight:600; font-size:14px;">

                    User ID:
                    <span class="text-primary font-weight-bold">
                        <?php echo $hmpre . htmlspecialchars($row['userid']); ?>
                    </span>

                    <span class="profile-separator"> | </span>

                    Sponsor:
                    <?php echo $hmpre . htmlspecialchars($row['sponserid'] ?? 'SYSTEM'); ?>

                    (
                    <?php echo htmlspecialchars($row['sponsername'] ?? 'System Master'); ?>
                    )

                </p>

            </div>
        </div>


        <!-- Action Buttons -->
        <div class="profile-actions d-flex gap-2 flex-wrap">
            <a href="move_team.php?user_id=<?php echo urlencode($row['userid']); ?>"
               class="btn btn-warning font-weight-bold px-3 py-2 text-dark"
               style="border-radius:12px; background:#f59e0b; border:none;">
                <i class="fa fa-sitemap me-1"></i>
                Move Team
            </a>
            <a href="all_user.php"
               class="btn btn-outline-secondary font-weight-bold px-3 py-2"
               style="border-radius:12px;">
                <i class="fa fa-arrow-left me-1"></i>
                Back to Members
            </a>
        </div>

    </div>
</div>

        <!-- Section 1: User Account Information Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <div class="card ananta-fintech-card">
                        <div class="card-header-bar">
                            <h4><i class="fa fa-id-card-o text-primary me-2"></i> Account Information</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-3">
                                <label>Profile Picture</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="previewAvatar" src="<?php echo htmlspecialchars($profilePic); ?>" alt="Preview" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1;">
                                    <input type="file" name="image" accept="image/*" class="form-control" onchange="previewImage(this)">
                                </div>
                                <small class="text-muted mt-1 d-block">Supported formats: JPG, PNG, WEBP, GIF (Max size: 5MB)</small>
                            </div>
                            <div class="form-group mb-3">
                                <label>User ID</label>
                                <input type="text" value="<?php echo $hmpre . htmlspecialchars($row['userid']); ?>" readonly class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Mobile No</label>
                                <input type="text" name="mobile" value="<?php echo htmlspecialchars($row['mobile']); ?>" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Login Password</label>
                                <input type="text" name="pass" value="<?php echo htmlspecialchars($row['pass']); ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            document.getElementById('previewAvatar').src = e.target.result;
                            var headerAvatar = document.getElementById('headerProfileAvatar');
                            if (headerAvatar) {
                                headerAvatar.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
                </script>

                <!-- Right Column -->
                <div class="col-lg-6">
                    <div class="card ananta-fintech-card">
                        <div class="card-header-bar">
                            <h4><i class="fa fa-sitemap text-primary me-2"></i> Membership & Sponsor Details</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Joining Date</label>
                                <input type="text" value="<?php echo htmlspecialchars($row['joining_date'] ?? date('Y-m-d')); ?>" readonly class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Sponsor ID & Name</label>
                                <input type="text" value="<?php echo $hmpre . htmlspecialchars($row['sponserid'] ?? 'SYSTEM') . ' - ' . htmlspecialchars($row['sponsername'] ?? 'System Master'); ?>" readonly class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>KYC Verification Status</label>
                                <input type="text" value="<?php echo $kycStatusText; ?>" readonly class="form-control" style="font-weight: 800; color: <?php echo ($kycVal == 2 || $kycVal == 1) ? '#16a34a' : (($kycVal == 3) ? '#ef4444' : '#d97706'); ?>;">
                            </div>
                            <div class="form-group mb-3">
                                <label>Account Activation Status ($11)</label>
                                <input type="text" value="<?php echo $actStatus['status']; ?> (<?php echo $actStatus['remaining_days']; ?> Days Remaining)" readonly class="form-control" style="font-weight: 800; color: <?php echo ($actStatus['status'] === 'ACTIVE') ? '#16a34a' : '#dc2626'; ?>;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Profile Changes -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <button class="btn text-white font-weight-bold px-5 py-3" type="submit" name="submit" style="border-radius: 14px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); font-size: 15px; border: none; box-shadow: 0 4px 16px rgba(22, 163, 74, 0.3);">
                        <i class="fa fa-save me-2"></i> Update Profile Details
                    </button>
                </div>
            </div>
        </form>

        <!-- Section 2: All 12 User Wallets & Admin Credit / Debit Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="fa fa-google-wallet text-primary me-2"></i> User Wallet Management (12 Wallets)</h4>
                            <p class="mb-0 text-muted small">Real-time balances from database. Perform secure, atomic Credit/Debit operations with mandatory reasons & confirmation.</p>
                        </div>
                        <span class="badge badge-primary px-3 py-2" style="border-radius:100px; font-size:12px; font-weight:700;">ISOLATED BALANCES</span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            <?php foreach ($walletsConfig as $wKey => $wMeta): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="wallet-card">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="wallet-icon" style="background: <?php echo $wMeta['color']; ?>;">
                                                    <i class="fa <?php echo $wMeta['icon']; ?>"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 15px;"><?php echo htmlspecialchars($wMeta['name']); ?></h6>
                                                    <span class="text-muted small" style="font-size: 11.5px;"><?php echo $wMeta['key']; ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="text-muted small text-uppercase font-weight-bold">Current Balance</div>
                                            <div class="h3 font-weight-bold mb-0" style="color: #0f172a;">
                                                <?php echo formatCurrency($wMeta['val']); ?>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="button" 
                                                    class="btn btn-wallet-credit flex-grow-1"
                                                    onclick="openWalletModal('<?php echo $wMeta['key']; ?>', '<?php echo htmlspecialchars(addslashes($wMeta['name'])); ?>', 'CREDIT', <?php echo $wMeta['val']; ?>)">
                                                <i class="fa fa-plus-circle me-1"></i> CREDIT
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-wallet-debit flex-grow-1"
                                                    onclick="openWalletModal('<?php echo $wMeta['key']; ?>', '<?php echo htmlspecialchars(addslashes($wMeta['name'])); ?>', 'DEBIT', <?php echo $wMeta['val']; ?>)">
                                                <i class="fa fa-minus-circle me-1"></i> DEBIT
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Immutable Admin Credit / Debit Audit History -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card ananta-fintech-card">
                    <div class="card-header-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4><i class="fa fa-history text-primary me-2"></i> Admin Credit / Debit Audit History</h4>
                            <p class="mb-0 text-muted small">Permanent, append-only transaction ledger for <?php echo htmlspecialchars($row['name']); ?></p>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="auditWalletFilter" class="form-control form-control-sm" style="border-radius:8px; font-weight:600;">
                                <option value="">All Wallets</option>
                                <?php foreach ($walletsConfig as $wKey => $wMeta): ?>
                                    <option value="<?php echo $wKey; ?>"><?php echo htmlspecialchars($wMeta['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="auditTypeFilter" class="form-control form-control-sm" style="border-radius:8px; font-weight:600;">
                                <option value="">All Types</option>
                                <option value="CREDIT">CREDIT</option>
                                <option value="DEBIT">DEBIT</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="adminAuditTable">
                                <thead>
                                    <tr>
                                        <th>Txn ID</th>
                                        <th>Wallet</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th style="color: #0284c7; font-weight: 700;">Prev Balance</th>
                                        <th style="color: #16a34a; font-weight: 700;">New Balance</th>
                                        <th>Admin ID</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($auditHistory)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4 font-weight-bold">No manual Admin Credit / Debit transactions found for this user.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($auditHistory as $log): ?>
                                            <tr data-wallet="<?php echo htmlspecialchars($log['wallet_type'] ?? ''); ?>" data-type="<?php echo htmlspecialchars($log['action'] ?? ''); ?>">
                                                <td class="font-weight-bold text-primary"><?php echo htmlspecialchars($log['reference_id'] ?? ('ADM-' . $log['id'])); ?></td>
                                                <td>
                                                    <span class="font-weight-bold text-dark">
                                                        <?php echo htmlspecialchars($walletsConfig[$log['wallet_type']]['name'] ?? $log['wallet_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (strtoupper($log['action']) === 'CREDIT'): ?>
                                                        <span class="badge badge-success px-3 py-1" style="border-radius:100px;">CREDIT</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-3 py-1" style="border-radius:100px;">DEBIT</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="font-weight-bold" style="color: <?php echo (strtoupper($log['action']) === 'CREDIT') ? '#16a34a' : '#ef4444'; ?>;">
                                                    <?php echo formatCurrency($log['amount']); ?>
                                                </td>
                                                <td style="max-width: 260px; font-size: 13px;" class="text-muted">
                                                    <?php echo htmlspecialchars($log['reason']); ?>
                                                </td>
                                                <td class="font-weight-bold" style="color: #0284c7; font-size: 14px;"><?php echo formatCurrency($log['previous_balance'] ?? 0); ?></td>
                                                <td class="font-weight-bold" style="color: #16a34a; font-size: 14px;"><?php echo formatCurrency($log['new_balance'] ?? 0); ?></td>
                                                <td class="font-weight-bold text-secondary"><?php echo htmlspecialchars($log['admin_id']); ?></td>
                                                <td style="font-size: 13px;" class="text-muted"><?php echo date('d M Y h:i A', strtotime($log['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone: Permanent Account Deletion -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-danger" style="border-radius:16px; background:#fff5f5; border: 1.5px solid #fecaca; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="font-weight-bold text-danger mb-1">
                                <i class="fa fa-exclamation-triangle me-2"></i> Permanent Account Deletion
                            </h5>
                            <p class="text-muted mb-0 small" style="max-width: 700px; font-weight: 500;">
                                Permanently remove this member account and all associated user profiles, wallet balances, investment packages, referral records, and transaction histories from the database. <strong>This action is irreversible and cannot be recovered.</strong>
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger font-weight-bold px-4 py-2" onclick="openDeleteAccountModal()" style="border-radius:10px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                                <i class="fa fa-trash me-1"></i> Permanent Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- ================================================== -->
<!-- ADMIN WALLET CREDIT / DEBIT MODAL (WITH CONFIRMATION STEP) -->
<!-- ================================================== -->
<div class="modal fade" id="walletControlModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(15,23,42,0.25);">
            <div class="modal-header border-bottom p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fa fa-shield me-2 text-primary"></i> <span id="modalHeaderTitle">Admin Wallet Adjustment</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="outline:none; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- STEP 1: INPUT FORM -->
            <div id="modalStep1" class="modal-body p-4">
                <div id="modalAlert" class="alert alert-danger d-none" style="border-radius:10px; font-size:13.5px;"></div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Target User</label>
                    <input type="text" value="<?php echo htmlspecialchars($row['name']); ?> (<?php echo $hmpre . htmlspecialchars($row['userid']); ?>)" readonly class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Selected Wallet</label>
                    <input type="text" id="modalWalletName" readonly class="form-control font-weight-bold text-primary">
                    <input type="hidden" id="modalWalletKey">
                </div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Transaction Action</label>
                    <input type="text" id="modalActionType" readonly class="form-control font-weight-bold">
                </div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Current Wallet Balance</label>
                    <input type="text" id="modalCurrentBalance" readonly class="form-control font-weight-bold">
                </div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Adjustment Amount (₹)</label>
                    <input type="number" id="modalAmount" step="0.01" min="0.01" class="form-control font-weight-bold" placeholder="Enter amount > 0">
                </div>

                <div class="form-group mb-3">
                    <label class="text-muted font-weight-bold small uppercase">Mandatory Reason / Remarks</label>
                    <textarea id="modalReason" class="form-control" rows="3" placeholder="Enter detailed reason for manual credit/debit adjustment..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4 py-2" data-dismiss="modal" style="border-radius:10px;">Cancel</button>
                    <button type="button" class="btn btn-primary font-weight-bold px-4 py-2" onclick="proceedToConfirmation()" style="border-radius:10px;">
                        Proceed to Confirm <i class="fa fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: CONFIRMATION SUMMARY -->
            <div id="modalStep2" class="modal-body p-4 d-none">
                <div class="alert alert-warning border-0 p-3 mb-4" style="border-radius:12px; background:#fffbeb; color:#92400e; font-size:13.5px;">
                    <i class="fa fa-exclamation-triangle me-2 font-weight-bold"></i> Please review and confirm the transaction details below. This operation will atomically update the user balance and log an immutable audit trail.
                </div>

                <div class="card p-3 border mb-3" style="background:#f8fafc; border-radius:12px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">User ID:</span>
                        <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['userid']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">Wallet:</span>
                        <span id="confirmWallet" class="font-weight-bold text-primary"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">Action:</span>
                        <span id="confirmAction" class="font-weight-bold"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">Adjustment Amount:</span>
                        <span id="confirmAmount" class="font-weight-bold text-dark"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">Previous Balance:</span>
                        <span id="confirmPrevBal" class="font-weight-bold" style="color: #0284c7;"></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-dark font-weight-bold">New Calculated Balance:</span>
                        <span id="confirmNewBal" class="font-weight-bold" style="color: #16a34a; font-size:16px;"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="text-muted font-weight-bold small d-block mb-1">Reason:</span>
                    <div id="confirmReason" class="p-2 border rounded bg-white font-weight-semibold text-dark" style="font-size:13px;"></div>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold px-4 py-2" onclick="backToStep1()" style="border-radius:10px;">
                        <i class="fa fa-arrow-left me-1"></i> Edit Details
                    </button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-success font-weight-bold px-4 py-2" onclick="executeWalletTransaction()" style="border-radius:10px;">
                        <i class="fa fa-check-circle me-1"></i> Confirm & Process
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================== -->
<!-- PERMANENT ACCOUNT DELETE CONFIRMATION MODAL -->
<!-- ================================================== -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(220,38,38,0.3);">
            <div class="modal-header border-bottom p-4" style="background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%); color: #ffffff; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fa fa-exclamation-triangle me-2"></i> PERMANENT ACCOUNT DELETE
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="outline:none; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-danger p-3 mb-4" style="border-radius:12px; background:#fef2f2; color:#991b1b; font-size:13.5px; border:1px solid #fca5a5;">
                    <i class="fa fa-exclamation-circle me-1 font-weight-bold"></i> <strong>Warning:</strong> Are you sure? This account will be permanently deleted and cannot be recovered.
                </div>
                
                <div class="card p-3 border mb-4" style="background:#f8fafc; border-radius:12px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted font-weight-bold small">Target Member Name:</span>
                        <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted font-weight-bold small">Target User ID:</span>
                        <span class="font-weight-bold text-danger"><?php echo $hmpre . htmlspecialchars($row['userid']); ?></span>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4 py-2" data-dismiss="modal" style="border-radius:10px;">Cancel</button>
                    <button type="button" id="btnConfirmDelete" class="btn btn-danger font-weight-bold px-4 py-2" onclick="executePermanentDelete()" style="border-radius:10px;">
                        <i class="fa fa-trash me-1"></i> Permanently Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'common/footer.php'; ?>


<script>
var targetUser = "<?php echo $targetUserId; ?>";
var currentBalNum = 0;

function openWalletModal(walletKey, walletName, actionType, currentBal) {
    $('#modalWalletKey').val(walletKey);
    $('#modalWalletName').val(walletName);
    $('#modalActionType').val(actionType);
    $('#modalCurrentBalance').val(formatAdminCurrency(currentBal));
    currentBalNum = parseFloat(currentBal);

    $('#modalAmount').val('');
    $('#modalReason').val('');
    $('#modalAlert').addClass('d-none').text('');

    if (actionType === 'CREDIT') {
        $('#modalActionType').css('color', '#10b981');
    } else {
        $('#modalActionType').css('color', '#ef4444');
    }

    $('#modalStep1').removeClass('d-none');
    $('#modalStep2').addClass('d-none');

    if (typeof $.fn.modal === 'function') {
        $('#walletControlModal').modal('show');
    } else {
        // Fallback if Bootstrap JS modal plugin is not attached
        $('#walletControlModal').addClass('show').css({ display: 'block', zIndex: 1050 });
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade show"></div>');
        }
    }
}

function closeWalletModal() {
    if (typeof $.fn.modal === 'function') {
        $('#walletControlModal').modal('hide');
    }
    $('#walletControlModal').removeClass('show').css('display', 'none');
    $('.modal-backdrop').remove();
}

// Bind close button clicks safely
$(document).on('click', '#walletControlModal .close, #walletControlModal [data-dismiss="modal"]', function() {
    closeWalletModal();
});

function proceedToConfirmation() {
    var amount = parseFloat($('#modalAmount').val());
    var reason = $.trim($('#modalReason').val());
    var action = $('#modalActionType').val();

    if (isNaN(amount) || amount <= 0) {
        $('#modalAlert').removeClass('d-none').text('Please enter a valid numeric amount greater than zero (0).');
        return;
    }

    if (reason.length < 3) {
        $('#modalAlert').removeClass('d-none').text('Please enter a mandatory reason explaining the credit/debit adjustment.');
        return;
    }

    if (action === 'DEBIT' && currentBalNum < amount) {
        $('#modalAlert').removeClass('d-none').text('Insufficient balance! Current balance is ' + formatAdminCurrency(currentBalNum) + ', requested debit is ' + formatAdminCurrency(amount) + '. Negative balance is blocked.');
        return;
    }

    $('#modalAlert').addClass('d-none').text('');

    var newBal = (action === 'CREDIT') ? (currentBalNum + amount) : (currentBalNum - amount);

    $('#confirmWallet').text($('#modalWalletName').val());
    $('#confirmAction').text(action).css('color', (action === 'CREDIT') ? '#10b981' : '#ef4444');
    $('#confirmAmount').text(formatAdminCurrency(amount));
    $('#confirmPrevBal').text(formatAdminCurrency(currentBalNum));
    $('#confirmNewBal').text(formatAdminCurrency(newBal));
    $('#confirmReason').text(reason);

    $('#modalStep1').addClass('d-none');
    $('#modalStep2').removeClass('d-none');
}

function backToStep1() {
    $('#modalStep2').addClass('d-none');
    $('#modalStep1').removeClass('d-none');
}

function executeWalletTransaction() {
    var walletKey = $('#modalWalletKey').val();
    var action    = $('#modalActionType').val();
    var amount    = parseFloat($('#modalAmount').val());
    var reason    = $.trim($('#modalReason').val());

    $('#btnConfirmSubmit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing...');

    $.ajax({
        url: 'admin_control_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'universal_wallet_adjustment',
            user_id: targetUser,
            wallet_type: walletKey,
            type: action,
            amount: amount,
            reason: reason
        },
        success: function (res) {
            $('#btnConfirmSubmit').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Confirm & Process');

            if (res.status === 'success') {
                alert(res.message);
                window.location.reload();
            } else {
                alert('Error: ' + res.message);
                backToStep1();
                $('#modalAlert').removeClass('d-none').text(res.message);
            }
        },
        error: function () {
            $('#btnConfirmSubmit').prop('disabled', false).html('<i class="fa fa-check-circle me-1"></i> Confirm & Process');
            alert('An unexpected server error occurred. Please try again.');
        }
    });
}

// Client-side filtering for Audit Table
$(document).ready(function() {
    $('#auditWalletFilter, #auditTypeFilter').on('change', function() {
        var walletFilter = $('#auditWalletFilter').val();
        var typeFilter = $('#auditTypeFilter').val();

        $('#adminAuditTable tbody tr').each(function() {
            var rowWallet = $(this).attr('data-wallet');
            var rowType = $(this).attr('data-type');

            var matchWallet = (!walletFilter || rowWallet === walletFilter);
            var matchType = (!typeFilter || rowType === typeFilter);

            if (matchWallet && matchType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

function openDeleteAccountModal() {
    if (typeof $.fn.modal === 'function') {
        $('#deleteAccountModal').modal('show');
    } else {
        $('#deleteAccountModal').addClass('show').css({ display: 'block', zIndex: 1050 });
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade show"></div>');
        }
    }
}

function closeDeleteAccountModal() {
    if (typeof $.fn.modal === 'function') {
        $('#deleteAccountModal').modal('hide');
    }
    $('#deleteAccountModal').removeClass('show').css('display', 'none');
    $('.modal-backdrop').remove();
}

$(document).on('click', '#deleteAccountModal .close, #deleteAccountModal [data-dismiss="modal"]', function() {
    closeDeleteAccountModal();
});

function executePermanentDelete() {
    var userIdToDelete = targetUser || "<?php echo $targetUserId; ?>";
    if (!userIdToDelete) {
        alert("Error: Target User ID is missing.");
        return;
    }

    if (!confirm("FINAL CONFIRMATION: Are you absolutely sure you want to PERMANENTLY DELETE user " + userIdToDelete + "? This action is irreversible.")) {
        return;
    }

    $('#btnConfirmDelete').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Deleting...');

    $.ajax({
        url: 'admin_control_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'delete_user_account_permanent',
            user_id: userIdToDelete
        },
        success: function(res) {
            if (res && res.status === 'success') {
                alert(res.message);
                window.location.replace('all_user.php');
            } else {
                var errMsg = (res && res.message) ? res.message : 'Account deletion failed.';
                alert(errMsg);
                $('#btnConfirmDelete').prop('disabled', false).html('<i class="fa fa-trash me-1"></i> Permanently Delete');
            }
        },
        error: function(xhr, status, err) {
            console.error("Delete account AJAX error:", xhr.responseText);
            alert('Account deletion failed. Server response: ' + (xhr.responseText || status || err));
            $('#btnConfirmDelete').prop('disabled', false).html('<i class="fa fa-trash me-1"></i> Permanently Delete');
        }
    });
}
</script>


</body>
</html>