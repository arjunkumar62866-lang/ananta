<?php
include 'common/header.php';

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'set_txn_key') {
        $newKey     = $_POST['new_key'] ?? '';
        $confirmKey = $_POST['confirm_key'] ?? '';

        if ($newKey !== $confirmKey) {
            $message = 'New Transaction Key and Confirm Key do not match.';
            $msgType = 'danger';
        } else {
            $res = setTransactionKey($userid, $newKey, $pdo);
            $message = $res['message'];
            $msgType = ($res['status'] === 'success') ? 'success' : 'danger';
        }
    } elseif ($action === 'change_txn_key') {
        $oldKey     = $_POST['old_key'] ?? '';
        $newKey     = $_POST['new_key'] ?? '';
        $confirmKey = $_POST['confirm_key'] ?? '';

        if ($newKey !== $confirmKey) {
            $message = 'New Transaction Key and Confirm Key do not match.';
            $msgType = 'danger';
        } else {
            $res = changeTransactionKey($userid, $oldKey, $newKey, $pdo);
            $message = $res['message'];
            $msgType = ($res['status'] === 'success') ? 'success' : 'danger';
        }
    }
}

// Check if user has key set
$stmtHash = $pdo->prepare("SELECT txn_pass FROM user WHERE userid = :uid");
$stmtHash->execute([':uid' => $userid]);
$hasKeySet = !empty($stmtHash->fetchColumn());
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-3">
            <div class="col-12">
                <h4 class="text-white">Transaction Key (Security PIN) Settings</h4>
                <p class="text-muted">Your Transaction Key is required for sensitive operations including Withdrawals, P2P Transfers, Profile Changes, and Security verifications.</p>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header"><?php echo $hasKeySet ? 'Change Transaction Key' : 'Set Transaction Key'; ?></div>
                    <div class="card-body">
                        <form action="txn_key_settings.php" method="POST">
                            <input type="hidden" name="action" value="<?php echo $hasKeySet ? 'change_txn_key' : 'set_txn_key'; ?>">

                            <?php if ($hasKeySet): ?>
                                <div class="form-group">
                                    <label>Current Transaction Key</label>
                                    <input type="password" name="old_key" class="form-control" placeholder="Current Key" required>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>New Transaction Key (min 4 chars)</label>
                                <input type="password" name="new_key" class="form-control" placeholder="New Key" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Transaction Key</label>
                                <input type="password" name="confirm_key" class="form-control" placeholder="Confirm New Key" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <?php echo $hasKeySet ? 'Update Transaction Key' : 'Set Transaction Key'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'common/footer.php'; ?>
