<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';

$uid = $_GET['uid'] ?? '1290';
$cleanUid = preg_replace('/^(AN|ANANTA)/i', '', $uid);

if (isset($_POST['submit'])) {
    $sponsername = $_POST['sponsername'] ?? '';
    $name        = $_POST['name'] ?? '';
    $email       = $_POST['email'] ?? '';
    $mobile      = $_POST['mobile'] ?? '';
    $pass        = $_POST['pass'] ?? '';

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

    if ($update) {
        echo '<script>alert("Profile Updated Successfully");</script>'; 
    }
}

$select = $pdo->prepare("SELECT * FROM user WHERE userid = :userid OR userid = :clean LIMIT 1");
$select->execute([':userid' => $uid, ':clean' => $cleanUid]);
$row = $select->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    // Fallback if not in user table
    $row = [
        'userid'       => '1290',
        'name'         => 'Ananta Admin',
        'mobile'       => '9999999999',
        'email'        => 'admin@anantamtptl.com',
        'pass'         => '••••••••',
        'joining_date' => date('Y-m-d'),
        'sponserid'    => 'SYSTEM',
        'sponsername'  => 'System Master',
        'status'       => 1
    ];
}

$status = ($row['status'] == 1) ? "Active" : "Inactive";
$actStatus = getUserAccountActivationStatus($row['userid'], $pdo);
?>

<body class="ananta-admin-dashboard bg-theme bg-theme1">

<style>
/* =========================================================
   ANANTA FINTECH THEME - USER / ADMIN PROFILE REDESIGN
========================================================= */
html, body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-admin-dashboard,
body.bg-theme,
body.bg-theme1 {
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
</style>

<div id="wrapper">
<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Card -->
        <div class="card profile-header-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-header-icon">
                    <i class="fa fa-user-circle-o"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="font-weight: 800; color: #0f172a;">User Profile Management</h3>
                    <p class="mb-0" style="color: #64748b; font-weight: 600; font-size: 14px;">View & update account details for <?php echo htmlspecialchars($row['name']); ?> (<?php echo $hmpre . htmlspecialchars($row['userid']); ?>)</p>
                </div>
            </div>
        </div>

        <form method="POST">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <div class="card ananta-fintech-card">
                        <div class="card-header-bar">
                            <h4>Account Information</h4>
                        </div>
                        <div class="card-body p-4">
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

                <!-- Right Column -->
                <div class="col-lg-6">
                    <div class="card ananta-fintech-card">
                        <div class="card-header-bar">
                            <h4>Membership & Sponsor Details</h4>
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
                                <label>Sponsor ID</label>
                                <input type="text" value="<?php echo $hmpre . htmlspecialchars($row['sponserid'] ?? 'SYSTEM'); ?>" readonly class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Account Status</label>
                                <input type="text" value="<?php echo $status; ?>" readonly class="form-control" style="font-weight: 800; color: <?php echo ($status === 'Active') ? '#16a34a' : '#ef4444'; ?>;">
                            </div>
                            <div class="form-group mb-3">
                                <label>Account Activation Status ($11)</label>
                                <input type="text" value="<?php echo $actStatus['status']; ?> (<?php echo $actStatus['remaining_days']; ?> Days Remaining)" readonly class="form-control" style="font-weight: 800; color: <?php echo ($actStatus['status'] === 'ACTIVE') ? '#16a34a' : '#dc2626'; ?>;">
                            </div>
                            <div class="form-group mb-3">
                                <label>Activated On / Valid Until</label>
                                <input type="text" value="<?php echo $actStatus['start_date'] ? (date('d-M-Y', strtotime($actStatus['start_date'])) . ' to ' . date('d-M-Y', strtotime($actStatus['expiry_date']))) : 'N/A'; ?>" readonly class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label>Sponsor Name</label>
                                <input type="text" name="sponsername" value="<?php echo htmlspecialchars($row['sponsername'] ?? 'System Master'); ?>" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <button class="btn text-white font-weight-bold px-5 py-3" type="submit" name="submit" style="border-radius: 14px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); font-size: 15px; border: none; box-shadow: 0 4px 16px rgba(22, 163, 74, 0.3);">
                        <i class="fa fa-save mr-2"></i> Update Profile Details
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<?php include 'common/footer.php'; ?>
</div>
</body>
</html>