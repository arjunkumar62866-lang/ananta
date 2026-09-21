<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">
<?php include 'common/header.php' ?>
<?php
include("common/connection.php");

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('h:i:s A');
$date = date('Y-m-d');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);

    if (!empty($subject) && !empty($description)) {
        try {
            $sql = "INSERT INTO tbl_query (userid, username, email, mobile, sub, message, status, or_date)
                    VALUES (:userid, :username, :email, :mobile, :subject, :message, '0', :or_date)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userid', $userid);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $useremail);
            $stmt->bindParam(':mobile', $usermobile);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $description);
            $stmt->bindParam(':or_date', $date);

            if ($stmt->execute()) {
                echo "<script>alert('Enquiry sent successfully! We will contact you soon.');</script>";
            } else {
                echo "<script>alert('Sorry, something went wrong. Please try again.');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}
?>


<style>
/* =========================================================
   ANANTA FINTECH THEME - SEND ENQUIRY REDESIGN
   Matches Dashboard (index.php) & Profile Styling
========================================================= */

html,
body {
    min-height: 100%;
    margin: 0;
    padding: 0;
}

body.ananta-user-dashboard,
body.bg-theme,
body.bg-theme1,
body.ananta-user-dashboard.bg-theme,
body.ananta-user-dashboard.bg-theme1 {
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    background-image: none !important;
    color: #0f172a !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
}

/* Remove old legacy dark overlays */
html::before,
html::after,
body::before,
body::after,
#wrapper::before,
#wrapper::after,
.content-wrapper::before,
.content-wrapper::after {
    content: none !important;
    display: none !important;
    background: none !important;
    background-color: transparent !important;
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

/* Header Banner */
.income-header-card {
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.10) 0%, rgba(22, 163, 74, 0.10) 100%), #ffffff !important;
    border-radius: 24px !important;
    border: 1px solid rgba(2, 132, 199, 0.18) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
    margin-bottom: 24px;
}

.income-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%);
    color: #ffffff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3);
    flex-shrink: 0;
}

/* Main Card Container */
.ananta-fintech-card {
    background: #ffffff !important;
    border-radius: 22px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06) !important;
    overflow: hidden;
}

.card-header-bar {
    padding: 24px 28px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #ffffff 0%, #fbfdff 60%, #f8fafc 100%);
}

.card-header-title h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.card-header-title p {
    margin: 4px 0 0;
    font-size: 13.5px;
    color: #64748b;
    font-weight: 500;
}

/* Form Controls Styling */
label.form-label,
label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin-bottom: 8px !important;
    display: block !important;
}

.form-control,
input.form-control,
textarea.form-control {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 14.5px !important;
    font-weight: 600 !important;
    padding: 12px 16px !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: none !important;
}

.form-control:focus,
input.form-control:focus,
textarea.form-control:focus {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    outline: none !important;
}

/* Submit Button */
.btn-ananta-submit {
    background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 12px !important;
    height: 52px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.25) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    width: 100% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

.btn-ananta-submit:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.35) !important;
    color: #ffffff !important;
}
</style>

<body class="ananta-user-dashboard">

<!-- loader -->
<div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
        <div class="loader-wrapper-inner"><div class="loader"></div></div>
    </div>
</div>
<!-- end loader -->

<div id="wrapper">

<div class="clearfix"></div>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Header Welcome Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card income-header-card border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="income-header-icon">
                                <i class="fa fa-envelope-o"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 4px 12px; letter-spacing: 0.5px;">HELP & SUPPORT</span>
                                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">ENQUIRY DESK</span>
                                </div>
                                <h4 class="mb-0" style="font-size: 22px; font-weight: 800; color: #0f172a;">
                                    Send <span style="background: linear-gradient(135deg, #0284c7 0%, #16a34a 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Support Enquiry</span> 💬
                                </h4>
                                <p class="mb-0 text-muted" style="font-size: 13.5px; margin-top: 3px;">
                                    Submit your question or concern to our dedicated customer support team.
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="enquery-history.php" class="btn btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: 12px; font-size: 13px;">
                                <i class="fa fa-history me-1"></i> Enquiry History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="ananta-fintech-card">
                    <div class="card-header-bar">
                        <div class="card-header-title">
                            <h4><i class="fa fa-paper-plane text-primary me-2"></i> Submit New Enquiry Ticket</h4>
                            <p>Fill out the details below and we will get back to you promptly</p>
                        </div>
                    </div>
                    
                    <div class="p-4 p-md-5">
                        <!-- Enquiry Form -->
                        <form method="POST">
                            <div class="form-group mb-4">
                                <label for="subject">Enquiry Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Brief subject of your query" required style="height: 48px;">
                            </div>

                            <div class="form-group mb-4">
                                <label for="description">Detailed Description</label>
                                <textarea name="description" id="description" rows="5" class="form-control" placeholder="Please describe your question or issue in detail..." required></textarea>
                            </div>

                            <button type="submit" name="submit" class="btn-ananta-submit mt-2">
                                <i class="fa fa-paper-plane me-1"></i> Submit Enquiry Ticket
                            </button>
                        </form>
                        <!-- End Form -->

                    </div>
                </div>
            </div>
        </div>

        <!-- overlay -->
        <div class="overlay toggle-menu"></div>

    </div>
</div>

<a href="javascript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<?php include 'common/footer.php'; ?>

</div><!-- End wrapper -->

</body>
</html>

