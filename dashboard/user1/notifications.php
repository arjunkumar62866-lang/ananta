<?php
ob_start();
session_start();
require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];

// Handle Mark as Read / Mark All as Read actions
$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'mark_read' && !empty($_POST['notification_id'])) {
        $notifId = intval($_POST['notification_id']);
        if (markNotificationAsRead($notifId, $userid, $pdo)) {
            $msg = "Notification marked as read.";
            $msgType = "success";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'mark_all_read') {
        markAllNotificationsAsRead($userid, $pdo);
        $msg = "All notifications marked as read.";
        $msgType = "success";
    }
}

$filterType = $_GET['type'] ?? '';
$unreadOnly = isset($_GET['unread']) && $_GET['unread'] == '1';

// Fetch user notifications
$notifications = getUserNotifications($userid, 100, 0, $pdo);

// Filter by type or unread if requested
if (!empty($filterType)) {
    $notifications = array_filter($notifications, function($n) use ($filterType) {
        return strtoupper($n['type']) === strtoupper($filterType);
    });
}
if ($unreadOnly) {
    $notifications = array_filter($notifications, function($n) {
        return intval($n['is_read']) === 0;
    });
}

$unreadCount = getUnreadNotificationCount($userid, $pdo);
?>

<style>
.content-wrapper {
    background-color: #f8fafc !important;
    padding-top: 105px !important;
    padding-bottom: 85px !important;
    min-height: calc(100vh - 70px);
}

@media (max-width: 768px) {
    .content-wrapper {
        padding-top: 92px !important;
        padding-bottom: 75px !important;
    }
}

.notifications-container {
    max-width: 950px;
    margin: 0 auto;
}

.notif-box-card {
    background: #ffffff !important;
    border-radius: 20px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06) !important;
}

.notif-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 5px;
    margin-bottom: 20px;
}

.notif-tab {
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a !important;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    text-decoration: none !important;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.notif-tab:hover, .notif-tab.active {
    background: linear-gradient(135deg, #0284c7 0%, #0f172a 100%) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
}

.notif-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 12px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    display: flex;
    gap: 16px;
    position: relative;
    transition: all 0.2s ease;
}

.notif-card.unread {
    border-left: 5px solid #0284c7 !important;
    background: #f0f9ff;
}

.notif-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.icon-deposit { background: #dcfce7; color: #15803d; }
.icon-withdrawal { background: #fee2e2; color: #b91c1c; }
.icon-p2p { background: #e0e7ff; color: #4338ca; }
.icon-kyc { background: #fef3c7; color: #b45309; }
.icon-admin { background: #f3e8ff; color: #7e22ce; }
.icon-default { background: #f1f5f9; color: #475569; }

.notif-body {
    flex-grow: 1;
}

.notif-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6px;
}

.notif-item-title {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a !important;
    margin: 0;
}

.notif-time {
    font-size: 12px;
    color: #64748b !important;
    font-weight: 600;
    white-space: nowrap;
}

.notif-item-msg {
    font-size: 14px;
    color: #334155 !important;
    font-weight: 500;
    margin: 4px 0 10px 0;
    line-height: 1.5;
}

.notif-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
}

.notif-ref {
    color: #0f172a !important;
    background: #e2e8f0;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 700;
    font-family: monospace;
}

.btn-mark-read {
    background: none;
    border: none;
    color: #0284c7 !important;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
}

.btn-mark-read:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    background: #f8fafc;
    border-radius: 16px;
    color: #334155 !important;
    border: 1px dashed #cbd5e1;
}

.empty-state h5 {
    color: #0f172a !important;
    font-weight: 800;
    margin-top: 10px;
}

.empty-state p {
    color: #475569 !important;
    font-weight: 600;
}
</style>

<div id="wrapper">
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="notifications-container">

            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?php echo $msgType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
                    <i class="zmdi zmdi-info-outline mr-2"></i> <?php echo htmlspecialchars($msg); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- MAIN CONTAINER CARD BOX -->
            <div class="card notif-box-card">
                <div class="card-body p-4">

                    <!-- Notification Header Inside Box -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3" style="border-bottom: 2px solid #f1f5f9;">
                        <div>
                            <h3 class="mb-1 font-weight-bold" style="color: #0f172a !important; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                                <i class="zmdi zmdi-notifications text-primary"></i> Notifications
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge badge-pill badge-danger" style="background: #ef4444; color: #ffffff !important; font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 100px;">
                                        <?php echo $unreadCount; ?> Unread
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <p class="mb-0 small" style="color: #475569 !important; font-weight: 600;">View and manage all system updates, transaction alerts, and activity notifications.</p>
                        </div>

                        <?php if ($unreadCount > 0): ?>
                            <form method="POST" action="" class="m-0">
                                <input type="hidden" name="action" value="mark_all_read">
                                <button type="submit" class="btn btn-sm btn-outline-primary font-weight-bold px-3 py-2" style="border-radius: 100px;">
                                    <i class="zmdi zmdi-check-all mr-1"></i> Mark All as Read
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="notif-tabs mb-4">
                        <a href="notifications.php" class="notif-tab <?php echo (empty($filterType) && !$unreadOnly) ? 'active' : ''; ?>">All</a>
                        <a href="notifications.php?unread=1" class="notif-tab <?php echo $unreadOnly ? 'active' : ''; ?>">Unread (<?php echo $unreadCount; ?>)</a>
                        <a href="notifications.php?type=DEPOSIT" class="notif-tab <?php echo $filterType === 'DEPOSIT' ? 'active' : ''; ?>">Deposit</a>
                        <a href="notifications.php?type=WITHDRAWAL" class="notif-tab <?php echo $filterType === 'WITHDRAWAL' ? 'active' : ''; ?>">Withdrawal</a>
                        <a href="notifications.php?type=P2P" class="notif-tab <?php echo $filterType === 'P2P' ? 'active' : ''; ?>">P2P</a>
                        <a href="notifications.php?type=KYC" class="notif-tab <?php echo $filterType === 'KYC' ? 'active' : ''; ?>">KYC</a>
                        <a href="notifications.php?type=ADMIN" class="notif-tab <?php echo $filterType === 'ADMIN' ? 'active' : ''; ?>">Admin</a>
                    </div>

                    <!-- Notification List -->
                    <?php if (empty($notifications)): ?>
                        <div class="empty-state">
                            <i class="zmdi zmdi-notifications-off mr-1" style="font-size: 48px; color: #94a3b8;"></i>
                            <h5>No Notifications Found</h5>
                            <p class="mb-0">You don't have any notifications in this category yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <?php
                            $typeClass = 'icon-default';
                            $icon = 'zmdi-info-outline';
                            switch (strtoupper($n['type'])) {
                                case 'DEPOSIT':
                                    $typeClass = 'icon-deposit';
                                    $icon = 'zmdi-balance-wallet';
                                    break;
                                case 'WITHDRAWAL':
                                    $typeClass = 'icon-withdrawal';
                                    $icon = 'zmdi-money-off';
                                    break;
                                case 'P2P':
                                    $typeClass = 'icon-p2p';
                                    $icon = 'zmdi-swap-vertical';
                                    break;
                                case 'KYC':
                                    $typeClass = 'icon-kyc';
                                    $icon = 'zmdi-shield-check';
                                    break;
                                case 'ADMIN':
                                    $typeClass = 'icon-admin';
                                    $icon = 'zmdi-speaker';
                                    break;
                            }
                            $isUnread = intval($n['is_read']) === 0;
                            ?>
                            <div class="notif-card <?php echo $isUnread ? 'unread' : ''; ?>">
                                <div class="notif-icon <?php echo $typeClass; ?>">
                                    <i class="zmdi <?php echo $icon; ?>"></i>
                                </div>
                                <div class="notif-body">
                                    <div class="notif-card-header">
                                        <h6 class="notif-item-title"><?php echo htmlspecialchars($n['title']); ?></h6>
                                        <span class="notif-time"><?php echo date('M d, Y H:i', strtotime($n['created_at'])); ?></span>
                                    </div>
                                    <div class="notif-item-msg"><?php echo htmlspecialchars($n['message']); ?></div>
                                    <div class="notif-meta">
                                        <?php if (!empty($n['ref_id'])): ?>
                                            <span class="notif-ref">Ref: <?php echo htmlspecialchars($n['ref_id']); ?></span>
                                        <?php endif; ?>
                                        <?php if ($isUnread): ?>
                                            <form method="POST" action="" class="d-inline m-0 ms-auto">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="notification_id" value="<?php echo $n['id']; ?>">
                                                <button type="submit" class="btn-mark-read">Mark as read</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border ms-auto px-2 py-1"><i class="zmdi zmdi-check mr-1"></i> Read</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>
</div>

<?php require_once 'common/footer.php'; ?>
