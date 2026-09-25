<?php ob_start();?>
<!DOCTYPE html>
<html lang="en">

<?php 
include 'common/header.php';
include("common/connection.php");
date_default_timezone_set('Asia/kolkata');

$search = $userid;

// Search functionality for tree view
if (isset($_GET['search-id']) && !empty(trim($_GET['search-id']))) {
    $search_id = trim($_GET['search-id']);
    $stmt = $pdo->prepare("SELECT userid FROM user WHERE userid = :userid LIMIT 1");
    $stmt->execute([':userid' => $search_id]);

    if ($stmt->rowCount() > 0) {
        $search = $search_id;
    } else {
        echo "<script>alert('User ID not found in database');window.location.assign('tree.php');</script>";
        exit;
    }
}

// Function to fetch tree database row for a user
function tree_data($userid){
    global $pdo;
    $data = [
        'left'       => '',
        'right'      => '',
        'leftcount'  => 0,
        'rightcount' => 0,
        'lefttotal'  => 0,
        'righttotal' => 0
    ];
    
    if (empty($userid)) {
        return $data;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM tree WHERE userid = :userid LIMIT 1");
        $stmt->execute([':userid' => $userid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $data['left']       = $result['left_id'] ?? '';
            $data['right']      = $result['right_id'] ?? '';
            $data['leftcount']  = $result['leftcount'] ?? 0;
            $data['rightcount'] = $result['rightcount'] ?? 0;
            $data['lefttotal']  = $result['lefttotal'] ?? 0;
            $data['righttotal'] = $result['righttotal'] ?? 0;
        }
    } catch (PDOException $e) {
        // Silent catch
    }

    return $data;
}

// Function to fetch user details (name, active status, etc.)
function get_tree_user_info($nodeUserId) {
    global $pdo;
    if (empty($nodeUserId)) return null;
    try {
        $stmt = $pdo->prepare("SELECT userid, name, status, active, package FROM user WHERE userid = :id LIMIT 1");
        $stmt->execute([':id' => $nodeUserId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

// Helper to render tree node cell HTML
function render_tree_cell($userObj, $nodeId, $parentId, $side, $divId, $loggedUserId) {
    if (!empty($userObj) && !empty($userObj['userid'])) {
        $uId = htmlspecialchars($userObj['userid']);
        $uName = htmlspecialchars($userObj['name'] ?? 'User');
        $isActive = (($userObj['active'] ?? '0') == '1');
        $imgClass = $isActive ? 'userimg' : 'userimg2int';
        
        return "
        <a href='tree.php?search-id={$uId}' title='Click to expand tree of {$uName} ({$uId})'>
            <i class='{$imgClass}' onmouseover=\"bigImg('{$uId}',{$divId})\" onmouseout=\"normalImg({$divId})\"></i>
            <div style='margin-top: 6px;'>
                <b style='color: #000000 !important; font-size: 13px;'>{$uName}<br>({$uId})</b>
            </div>
        </a>
        <div id='demo{$divId}'></div>";
    } elseif (!empty($parentId)) {
        $pId = htmlspecialchars($parentId);
        $sId = htmlspecialchars($loggedUserId);
        $sideName = htmlspecialchars($side);
        return "
        <a href='add_user_binary_registration_form.php?sponsorid={$sId}&underuserid={$pId}&type={$sideName}' title='Add New User under {$pId} ({$sideName})'>
            <img src='images/red2a.png' style='width: 50px; height: 50px;'><br>
            <b style='color: #000000 !important; font-size: 12px;'>Add New User</b>
        </a>";
    } else {
        return "
        <div style='opacity: 0.35; padding: 5px;'>
            <img src='images/red2a.png' style='width: 40px; height: 40px; filter: grayscale(100%);'><br>
            <span style='color: #64748b !important; font-size: 11px; font-weight: 600;'>Empty</span>
        </div>";
    }
}
?>

<head>
<style>
body.bg-theme {
    background-color: #f8fafc !important;
}

.userimg {
    background: url(images/usera.png) no-repeat center;
    background-size: contain;
    display: block;
    width: 65px;
    height: 65px;
    margin: auto;
    transition: transform 0.2s ease;
}
.userimg:hover {
    transform: scale(1.1);
}

.userimg2int {
    background: url(images/inactive.png) no-repeat center;
    background-size: contain;
    display: block;
    width: 65px;
    height: 65px;
    margin: auto;
    transition: transform 0.2s ease;
}
.userimg2int:hover {
    transform: scale(1.1);
}

.usrdetail {
    background: #ffffff;
    border: 2px solid #0284c7;
    border-radius: 12px;
    font-size: 13px;
    width: 250px;
    text-align: center;
    padding: 12px;
    margin: 10px auto 0 auto;
    color: #000000 !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    position: absolute;
    z-index: 999;
    left: 50%;
    transform: translateX(-50%);
}

.mobinew { 
    overflow-x: auto; 
    padding: 10px 0;
}

.tree-table {
    width: 100%;
    min-width: 900px;
    margin-top: 15px;
    text-align: center;
    border-collapse: separate;
    border-spacing: 0 15px;
}

.tree-table td {
    vertical-align: top;
    position: relative;
    padding: 8px 4px;
}

/* Enforce black color for all text elements */
.card-title h3,
.tree-table td, 
.tree-table td a, 
.tree-table td b, 
.tree-table td div,
.tree-table td span {
    color: #000000 !important;
    text-decoration: none;
}
.tree-table td a:hover {
    color: #0284c7 !important;
    text-decoration: none;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
// AJAX Popup for user details
function bigImg(userid, divId) {
    $.ajax({
        url: "popup.php",
        type: "POST",
        data: { Assid: userid },
        dataType: "JSON",
        success: function (jsonStr) {
            if (jsonStr && jsonStr.result) {
                $.each(jsonStr.result, function (i, items) {
                    var html = "<div class='usrdetail'>"+
                    "<strong>" + items['name'] + "</strong><br>"+
                    "<span style='color:#0284c7;'>User ID: " + items['userid'] + "</span><br>"+
                    "Sponsor ID: " + items['sponserid'] + "<br>"+
                    "Joining: " + items['joining_date'] + "<br>"+
                    "Mobile: " + items['mobile'] + "<br>"+
                    "Left Count: " + items['leftcount'] + " | Right Count: " + items['rightcount'] +
                    "</div>";
                    $("#demo" + divId).html(html).show();
                });
            }
        }
    });
}
function normalImg(divId) {
    $("#demo" + divId).hide().html("");
}
</script>
</head>

<body class="bg-theme bg-theme1">

<div id="wrapper">
  <div class="content-wrapper py-4" style="background-color: #f8fafc !important;">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0" style="background:#ffffff; border-radius:18px; border:1px solid #cbd5e1; box-shadow:0 4px 20px rgba(0,0,0,0.06);">
                    <div class="card-body p-4">
                        
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 2px solid #e2e8f0;">
                            <div>
                                <h3 class="font-weight-bold mb-1" style="color: #000000 !important;">
                                    <i class="fa fa-sitemap mr-2" style="color: #0284c7;"></i> Binary Tree View
                                </h3>
                                <p class="mb-0 small" style="color: #000000 !important; font-weight: 600;">
                                    Click any user node to interactively expand their downline left & right team tree.
                                </p>
                            </div>

                            <!-- Search Form -->
                            <form action="tree.php" method="GET" class="d-flex align-items-center mt-3 mt-md-0 gap-2">
                                <div class="input-group" style="width: 280px;">
                                    <input type="text" name="search-id" class="form-control" placeholder="Enter User ID..." value="<?php echo htmlspecialchars($search); ?>" required style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1; color: #000000 !important; font-weight: 700;">
                                    <button type="submit" class="btn" style="background: #000000; color: #ffffff !important; border-radius: 0 10px 10px 0; font-weight: 700;">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <?php if (strtoupper($search) !== strtoupper($userid)): ?>
                                    <a href="tree.php" class="btn btn-secondary font-weight-bold px-3" style="border-radius: 10px; background: #475569; color: #ffffff !important; border: none;">
                                        <i class="fa fa-home mr-1"></i> My Root
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>

                        <?php
                        // Fetch 4 Levels of Nodes (Root, Level 1, Level 2, Level 3)
                        
                        // Level 0: Root
                        $root_id = $search;
                        $root_tree = tree_data($root_id);
                        $root_user = get_tree_user_info($root_id);

                        // Level 1 (2 nodes)
                        $L1_left_id   = $root_tree['left'];
                        $L1_left_tree = tree_data($L1_left_id);
                        $L1_left_user = get_tree_user_info($L1_left_id);

                        $L1_right_id   = $root_tree['right'];
                        $L1_right_tree = tree_data($L1_right_id);
                        $L1_right_user = get_tree_user_info($L1_right_id);

                        // Level 2 (4 nodes)
                        $L2_LL_id   = $L1_left_tree['left'];
                        $L2_LL_tree = tree_data($L2_LL_id);
                        $L2_LL_user = get_tree_user_info($L2_LL_id);

                        $L2_LR_id   = $L1_left_tree['right'];
                        $L2_LR_tree = tree_data($L2_LR_id);
                        $L2_LR_user = get_tree_user_info($L2_LR_id);

                        $L2_RL_id   = $L1_right_tree['left'];
                        $L2_RL_tree = tree_data($L2_RL_id);
                        $L2_RL_user = get_tree_user_info($L2_RL_id);

                        $L2_RR_id   = $L1_right_tree['right'];
                        $L2_RR_tree = tree_data($L2_RR_id);
                        $L2_RR_user = get_tree_user_info($L2_RR_id);

                        // Level 3 (8 nodes)
                        $L3_LLL_id   = $L2_LL_tree['left'];
                        $L3_LLL_user = get_tree_user_info($L3_LLL_id);

                        $L3_LLR_id   = $L2_LL_tree['right'];
                        $L3_LLR_user = get_tree_user_info($L3_LLR_id);

                        $L3_LRL_id   = $L2_LR_tree['left'];
                        $L3_LRL_user = get_tree_user_info($L3_LRL_id);

                        $L3_LRR_id   = $L2_LR_tree['right'];
                        $L3_LRR_user = get_tree_user_info($L3_LRR_id);

                        $L3_RLL_id   = $L2_RL_tree['left'];
                        $L3_RLL_user = get_tree_user_info($L3_RLL_id);

                        $L3_RLR_id   = $L2_RL_tree['right'];
                        $L3_RLR_user = get_tree_user_info($L3_RLR_id);

                        $L3_RRL_id   = $L2_RR_tree['left'];
                        $L3_RRL_user = get_tree_user_info($L3_RRL_id);

                        $L3_RRR_id   = $L2_RR_tree['right'];
                        $L3_RRR_user = get_tree_user_info($L3_RRR_id);
                        ?>

                        <div class="mobinew">
                            <table class="tree-table">
                                <!-- LEVEL 0: ROOT NODE -->
                                <tr>
                                    <td colspan="8">
                                        <?php echo render_tree_cell($root_user, $root_id, '', '', 1, $userid); ?>
                                    </td>
                                </tr>

                                <!-- LEVEL 1: LEFT & RIGHT -->
                                <tr>
                                    <td colspan="4">
                                        <?php echo render_tree_cell($L1_left_user, $L1_left_id, $root_id, 'left', 2, $userid); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php echo render_tree_cell($L1_right_user, $L1_right_id, $root_id, 'right', 3, $userid); ?>
                                    </td>
                                </tr>

                                <!-- LEVEL 2: 4 SUB-NODES -->
                                <tr>
                                    <td colspan="2">
                                        <?php echo render_tree_cell($L2_LL_user, $L2_LL_id, $L1_left_id, 'left', 4, $userid); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo render_tree_cell($L2_LR_user, $L2_LR_id, $L1_left_id, 'right', 5, $userid); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo render_tree_cell($L2_RL_user, $L2_RL_id, $L1_right_id, 'left', 6, $userid); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo render_tree_cell($L2_RR_user, $L2_RR_id, $L1_right_id, 'right', 7, $userid); ?>
                                    </td>
                                </tr>

                                <!-- LEVEL 3: 8 SUB-NODES -->
                                <tr>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_LLL_user, $L3_LLL_id, $L2_LL_id, 'left', 8, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_LLR_user, $L3_LLR_id, $L2_LL_id, 'right', 9, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_LRL_user, $L3_LRL_id, $L2_LR_id, 'left', 10, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_LRR_user, $L3_LRR_id, $L2_LR_id, 'right', 11, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_RLL_user, $L3_RLL_id, $L2_RL_id, 'left', 12, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_RLR_user, $L3_RLR_id, $L2_RL_id, 'right', 13, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_RRL_user, $L3_RRL_id, $L2_RR_id, 'left', 14, $userid); ?>
                                    </td>
                                    <td colspan="1">
                                        <?php echo render_tree_cell($L3_RRR_user, $L3_RRR_id, $L2_RR_id, 'right', 15, $userid); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>

</body>
</html>
