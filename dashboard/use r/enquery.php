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


<body class="bg-theme bg-theme1">

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

        <div class="row mt-3">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title text-center"><h3>Send Enquiry</h3></div>
                        <hr>
                        
                        <!-- Enquiry Form -->
                        <form method="POST">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter Subject" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter your enquiry description" required></textarea>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" name="submit" class="btn btn-primary shadow-primary px-5">
                                    <i class="fa fa-paper-plane"></i> Submit
                                </button>
                            </div>
                        </form>
                        <!-- End Form -->

                    </div>
                </div>
            </div>
        </div>

        <!-- overlay -->
        <div class="overlay toggle-menu"></div>
        <!-- end overlay -->

    </div>
</div>

<a href="javascript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<?php include 'common/footer.php'; ?>

</div><!-- End wrapper -->

</body>
</html>
