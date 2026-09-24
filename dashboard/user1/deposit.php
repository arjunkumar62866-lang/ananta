<?php
include 'common/header.php';

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_inr_deposit') {
    $amountINR  = $_POST['amount_inr'] ?? 0;
    $paymentRef = $_POST['payment_ref'] ?? '';
    $txnKey     = $_POST['txn_key'] ?? '';

    // Handle uploaded file proof if provided
    $proofFileName = '';
    if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
        $upRes = validateAndUploadProofFile($_FILES['proof_file']);
        if ($upRes['status'] === 'success') {
            $proofFileName = $upRes['file_name'];
        } else {
            $message = $upRes['message'];
            $msgType = 'danger';
        }
    }

    if (empty($message)) {
        $res = createINRDepositRequest($userid, $amountINR, $paymentRef, $proofFileName, $txnKey, $pdo);
        if ($res['status'] === 'success') {
            $message = $res['message'];
            $msgType = 'success';
        } else {
            $message = $res['message'];
            $msgType = 'danger';
        }
    }
}

$userDeposits = getUserDeposits($userid, $pdo);
$bep20Address = "0x71C7656EC7ab88b098defB751B7401B5f6d8976F"; // Configured project deposit address
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-3">
            <div class="col-12">
                <h4 class="text-white">Fund Deposit (BEP20 & INR)</h4>
                <p class="text-muted">Deposit funds securely into your wallet. Admin verification is required for INR deposits.</p>
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
            <!-- BEP20 Deposit Card -->
            <div class="col-12 col-lg-6">
                <div class="card border-info">
                    <div class="card-header text-white bg-info">1. BEP20 USDT/BUSD Deposit</div>
                    <div class="card-body">
                        <p class="text-white">Send BEP20 USDT/BUSD to the official project deposit wallet address:</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="<?php echo $bep20Address; ?>" id="bep20AddrInput" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-light" type="button" onclick="navigator.clipboard.writeText('<?php echo $bep20Address; ?>'); alert('BEP20 Address copied!');">Copy</button>
                            </div>
                        </div>
                        <small class="text-muted">Network: Binance Smart Chain (BEP20). Deposits automatically track once confirmed on-chain.</small>
                    </div>
                </div>
            </div>

            <!-- INR Deposit Card -->
            <div class="col-12 col-lg-6">
                <div class="card border-success">
                    <div class="card-header text-white bg-success">2. INR Deposit (Bank / UPI)</div>
                    <div class="card-body">
                        <form action="deposit.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="submit_inr_deposit">
                            <div class="form-group">
                                <label>Deposit Amount (₹ INR)</label>
                                <input type="number" step="0.01" name="amount_inr" class="form-control" placeholder="e.g. 9000" required>
                                <small class="text-muted">Conversion Rate: 1 USD = ₹90.00</small>
                            </div>
                            <div class="form-group">
                                <label>Payment Reference / UTR Number</label>
                                <input type="text" name="payment_ref" class="form-control" placeholder="12-digit UTR / Payment Ref" required>
                            </div>
                            <div class="form-group">
                                <label>Upload Payment Proof / Screenshot (JPG, PNG, PDF max 5MB)</label>
                                <input type="file" name="proof_file" class="form-control-file text-white" accept="image/jpeg,image/png,image/webp,application/pdf" required>
                            </div>
                            <div class="form-group">
                                <label>Transaction Key (PIN)</label>
                                <input type="password" name="txn_key" class="form-control" placeholder="Enter Transaction Key" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Submit INR Deposit Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposit History Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Deposit Request History</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>Ref ID</th>
                                        <th>INR Amount</th>
                                        <th>USD Base</th>
                                        <th>Payment Ref</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($userDeposits)): ?>
                                        <?php foreach ($userDeposits as $d): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($d['deposit_ref']); ?></td>
                                                <td>₹<?php echo number_format($d['amount_inr'], 2); ?></td>
                                                <td>$<?php echo number_format($d['amount_usd'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($d['payment_ref'] ?: 'N/A'); ?></td>
                                                <td>
                                                    <?php
                                                    $st = $d['status'];
                                                    $badge = ($st === 'APPROVED') ? 'badge-success' : (($st === 'REJECTED') ? 'badge-danger' : 'badge-warning');
                                                    ?>
                                                    <span class="badge <?php echo $badge; ?>"><?php echo $st; ?></span>
                                                </td>
                                                <td><?php echo $d['created_at']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-muted">No deposit history found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'common/footer.php'; ?>
