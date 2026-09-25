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
.notifications-container {
    padding: 24px 15px;
    max-width: 900px;
    margin: 0 auto;
}

.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 24px;
    background: #ffffff;
    padding: 20px 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
}

.notif-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1f36;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notif-badge-pill {
    background: #ef4444;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

.notif-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 5px;
    margin-bottom: 20px;
}

.notif-tab {
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.notif-tab:hover, .notif-tab.active {
    background: #0f172a;
    color: #ffffff;
}

.notif-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    display: flex;
    gap: 16px;
    position: relative;
    transition: all 0.2s ease;
}

.notif-card.unread {
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
}

.notif-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
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
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.notif-time {
    font-size: 12px;
    color: #94a3b8;
    white-space: nowrap;
}

.notif-item-msg {
    font-size: 14px;
    color: #475569;
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
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 6px;
    font-family: monospace;
}

.btn-mark-read {
    background: none;
    border: none;
    color: #3b82f6;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
}

.btn-mark-read:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    background: #ffffff;
    border-radius: 16px;
    color: #64748b;
    border: 1px dashed #cbd5e1;
}
</style>

<div class="notifications-container">

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?php echo $msgType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="notif-header">
        <h1 class="notif-title">
            <i class="bi bi-bell-fill text-warning"></i> Notifications
            <?php if ($unreadCount > 0): ?>
                <span class="notif-badge-pill"><?php echo $unreadCount; ?> Unread</span>
            <?php endif; ?>
        </h1>

        <?php if ($unreadCount > 0): ?>
            <form method="POST" action="" class="m-0">
                <input type="hidden" name="action" value="mark_all_read">
                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-check-all me-1"></i> Mark All as Read
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Filter Tabs -->
    <div class="notif-tabs">
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
            <i class="bi bi-bell-slash fs-1 d-block mb-2 text-muted"></i>
            <h5>No notifications found</h5>
            <p class="mb-0">You don't have any notifications in this category yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <?php
            $typeClass = 'icon-default';
            $icon = 'bi-info-circle';
            switch (strtoupper($n['type'])) {
                case 'DEPOSIT':
                    $typeClass = 'icon-deposit';
                    $icon = 'bi-wallet2';
                    break;
                case 'WITHDRAWAL':
                    $typeClass = 'icon-withdrawal';
                    $icon = 'bi-arrow-up-right-circle';
                    break;
                case 'P2P':
                    $typeClass = 'icon-p2p';
                    $icon = 'bi-arrow-left-right';
                    break;
                case 'KYC':
                    $typeClass = 'icon-kyc';
                    $icon = 'bi-shield-check';
                    break;
                case 'ADMIN':
                    $typeClass = 'icon-admin';
                    $icon = 'bi-megaphone';
                    break;
            }
            $isUnread = intval($n['is_read']) === 0;
            ?>
            <div class="notif-card <?php echo $isUnread ? 'unread' : ''; ?>">
                <div class="notif-icon <?php echo $typeClass; ?>">
                    <i class="bi <?php echo $icon; ?>"></i>
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
                            <span class="badge bg-light text-muted border ms-auto"><i class="bi bi-check2"></i> Read</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php require_once 'common/footer.php'; ?>
