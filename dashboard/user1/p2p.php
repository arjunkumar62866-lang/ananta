<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'common/connection.php';
require_once 'common/db_method.php';

ensureP2PTableExists($pdo);

// AJAX Live User Verification Handler
if (isset($_GET['action']) && $_GET['action'] === 'get_user_name') {
    header('Content-Type: application/json');
    $lookupInput = trim($_GET['receiver_id'] ?? '');
    $userid = $_SESSION['userid'] ?? '';

    if (empty($lookupInput)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter User ID']);
        exit();
    }

    // Strip optional "AN" prefix if user types AN1290 instead of 1290
    $cleanId = $lookupInput;
    if (strripos($cleanId, 'AN') === 0) {
        $cleanId = substr($cleanId, 2);
    }

    // Query both with raw input and cleaned ID
    $stmtLook = $pdo->prepare("SELECT userid, name FROM user WHERE userid = :uid OR userid = :cid LIMIT 1");
    $stmtLook->execute([':uid' => $lookupInput, ':cid' => $cleanId]);
    $uRow = $stmtLook->fetch(PDO::FETCH_ASSOC);

    if ($uRow) {
        echo json_encode(['status' => 'success', 'name' => trim($uRow['name']), 'userid' => $uRow['userid']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found']);
    }
    exit();
}

require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];

$msg = '';
$msgType = '';

// Generate CSRF Token for Double Submission & Security Protection
if (empty($_SESSION['p2p_csrf_token'])) {
    $_SESSION['p2p_csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch user current balances
$stmtUserBal = $pdo->prepare("SELECT pin_wallet, amount FROM user WHERE userid = :uid");
$stmtUserBal->execute([':uid' => $userid]);
$uBalData = $stmtUserBal->fetch(PDO::FETCH_ASSOC) ?: [];
$mainWalletBal = (float)($uBalData['pin_wallet'] ?? 0);
$netBalanceBal = (float)($uBalData['amount'] ?? 0);

// Handle P2P Transfer Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'p2p_transfer') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['p2p_csrf_token'], $submittedToken)) {
        $msg = "Invalid session token. Please refresh the page and try again.";
        $msgType = 'danger';
    } else {
        $fromWallet = trim($_POST['from_wallet'] ?? '');
        $toWallet   = trim($_POST['to_wallet'] ?? '');
        $receiverId = trim($_POST['receiver_id'] ?? '');
        $amount     = (float)($_POST['amount'] ?? 0);
        $txnKey     = trim($_POST['txn_key'] ?? '');

        // Process P2P Transfer with complete validations & atomic transaction
        $res = processP2PTransfer($userid, $receiverId, $amount, $fromWallet, $toWallet, $txnKey, $pdo);
        if ($res['status'] === 'success') {
            $msg = $res['message'];
            $msgType = 'success';
            // Regenerate CSRF token after successful transfer to prevent accidental duplicate submission
            $_SESSION['p2p_csrf_token'] = bin2hex(random_bytes(32));
            
            // Re-fetch updated user balances
            $stmtUserBal->execute([':uid' => $userid]);
            $uBalData = $stmtUserBal->fetch(PDO::FETCH_ASSOC) ?: [];
            $mainWalletBal = (float)($uBalData['pin_wallet'] ?? 0);
            $netBalanceBal = (float)($uBalData['amount'] ?? 0);
        } else {
            $msg = $res['message'];
            $msgType = 'danger';
        }
    }
}

$sentHistory = getUserP2PTransferHistory($userid, $pdo);
$receivedReport = getUserP2PReceivedReport($userid, $pdo);
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'received' ? 'received' : 'transfer';

$totalSentUSD = 0;
foreach ($sentHistory as $s) {
    $totalSentUSD += (float)($s['amount'] ?? 0);
}

$totalReceivedUSD = 0;
foreach ($receivedReport as $r) {
    $totalReceivedUSD += (float)($r['amount'] ?? 0);
}
?>

<div class="content-wrapper py-4" style="background-color: #faf9f6 !important;">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif;">P2P Fund Transfers</h4>
                <p class="text-muted small mb-0">Directly transfer funds to another member or view your incoming/outgoing transfers</p>
            </div>
            <nav aria-label="breadcrumb">
                <div style="display: inline-flex; flex-direction: row; align-items: center; gap: 8px; padding: 6px 14px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; font-weight: 600; white-space: nowrap;">
                    <a href="index.php" style="color: #9333ea; text-decoration: none; font-weight: 600;">Dashboard</a>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #475569; font-weight: 600;">P2P</span>
                    <span style="color: #94a3b8; font-weight: 400;">/</span>
                    <span style="color: #0f172a; font-weight: 700;">Transfer History</span>
                </div>
            </nav>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert" style="border-radius: 12px;">
                <i class="zmdi zmdi-info-outline mr-2"></i> <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Stat Overview Row -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 16px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                            <i class="zmdi zmdi-upload zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold">Total Transferred Out</span>
                            <h4 class="mb-0 font-weight-bold" style="color: #0f172a;">
                                <?php echo formatCurrency($totalSentUSD, $selectedCurrency); ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm rounded-lg p-3" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 16px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 mr-3" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                            <i class="zmdi zmdi-download zmdi-hc-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold">Total Received In</span>
                            <h4 class="mb-0 font-weight-bold" style="color: #16a34a;">
                                <?php echo formatCurrency($totalReceivedUSD, $selectedCurrency); ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Form Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                    <i class="zmdi zmdi-rotate-right mr-2" style="color: #0284c7;"></i> P2P Transfer (Main Wallet ↔ Net Balance)
                </h6>
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <span class="badge badge-pill badge-light px-3 py-2 border font-weight-semibold" style="color: #0284c7; font-size: 13px;">
                        Main Wallet: <?php echo formatCurrency($mainWalletBal, $selectedCurrency); ?>
                    </span>
                    <span class="badge badge-pill badge-light px-3 py-2 border font-weight-semibold" style="color: #16a34a; font-size: 13px;">
                        Net Balance: <?php echo formatCurrency($netBalanceBal, $selectedCurrency); ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="p2p.php" id="p2pTransferForm">
                    <input type="hidden" name="action" value="p2p_transfer">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['p2p_csrf_token']); ?>">
                    
                    <div class="form-row">
                        <!-- From Wallet -->
                        <div class="form-group col-md-6 mb-3">
                            <label for="from_wallet" class="font-weight-bold small text-uppercase" style="color: #475569;">From Wallet</label>
                            <select class="form-control form-control-lg" id="from_wallet" name="from_wallet" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;" onchange="updateToWalletOptions()">
                                <option value="Net Balance">Net Balance (Available: <?php echo formatCurrency($netBalanceBal, $selectedCurrency); ?>)</option>
                                <option value="Main Wallet">Main Wallet (Available: <?php echo formatCurrency($mainWalletBal, $selectedCurrency); ?>)</option>
                            </select>
                        </div>

                        <!-- To Wallet -->
                        <div class="form-group col-md-6 mb-3">
                            <label for="to_wallet" class="font-weight-bold small text-uppercase" style="color: #475569;">To Wallet</label>
                            <select class="form-control form-control-lg" id="to_wallet" name="to_wallet" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;" readonly tabindex="-1">
                                <option value="Main Wallet">Main Wallet</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Receiver User ID & Verified Name Display -->
                        <div class="form-group col-md-4 mb-3">
                            <label for="receiver_id" class="font-weight-bold small text-uppercase" style="color: #475569;">Receiver User ID</label>
                            <input type="text" class="form-control form-control-lg" id="receiver_id" name="receiver_id" placeholder="e.g. AN1002" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;" onkeyup="verifyReceiverUser()" onchange="verifyReceiverUser()">
                            <div id="receiver_name_status" class="mt-2" style="font-size: 13.5px; font-weight: 600;"></div>
                        </div>

                        <!-- Amount -->
                        <div class="form-group col-md-4 mb-3">
                            <label for="amount" class="font-weight-bold small text-uppercase" style="color: #475569;">Transfer Amount ($ USD)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg" id="amount" name="amount" placeholder="Enter amount" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;">
                        </div>

                        <!-- Transaction Key -->
                        <div class="form-group col-md-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="txn_key" class="font-weight-bold small text-uppercase mb-0" style="color: #475569;">Transaction Key</label>
                                <a href="profile.php#security_section" class="small font-weight-bold text-primary text-decoration-none"><i class="zmdi zmdi-lock-outline mr-1"></i>Forgot Key?</a>
                            </div>
                            <input type="password" class="form-control form-control-lg" id="txn_key" name="txn_key" placeholder="Enter Transaction Key" required style="border-radius: 10px; border: 1px solid #cbd5e1; font-size: 15px;">
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitP2P" class="btn px-4 py-2 font-weight-bold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                        <i class="zmdi zmdi-send mr-2"></i> Confirm P2P Transfer
                    </button>
                </form>
            </div>
        </div>

        <script>
        let lookupTimer = null;
        let isUserVerified = false;

        function updateToWalletOptions() {
            document.getElementById('to_wallet').value = 'Main Wallet';
        }

        function verifyReceiverUser() {
            const uId = document.getElementById('receiver_id').value.trim();
            const statusDiv = document.getElementById('receiver_name_status');
            
            clearTimeout(lookupTimer);
            if (uId.length === 0) {
                statusDiv.innerHTML = '';
                isUserVerified = false;
                return;
            }

            statusDiv.innerHTML = '<span style="color: #0284c7;"><i class="zmdi zmdi-spinner zmdi-hc-spin mr-1"></i> Verifying User ID...</span>';
            isUserVerified = false;

            lookupTimer = setTimeout(function() {
                fetch('p2p.php?action=get_user_name&receiver_id=' + encodeURIComponent(uId))
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            statusDiv.innerHTML = '<span class="badge badge-pill px-3 py-2" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 13px;"><i class="zmdi zmdi-check-circle mr-1"></i> Verified Name: ' + escapeHtml(data.name) + '</span>';
                            isUserVerified = true;
                        } else {
                            statusDiv.innerHTML = '<span class="badge badge-pill px-3 py-2" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 13px;"><i class="zmdi zmdi-close-circle mr-1"></i> ' + escapeHtml(data.message) + '</span>';
                            isUserVerified = false;
                        }
                    })
                    .catch(err => {
                        statusDiv.innerHTML = '<span style="color: #dc2626;"><i class="zmdi zmdi-alert-circle mr-1"></i> Verification failed</span>';
                        isUserVerified = false;
                    });
            }, 300);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        document.getElementById('p2pTransferForm').addEventListener('submit', function(e) {
            if (!isUserVerified) {
                e.preventDefault();
                alert('Please enter a valid and verified Receiver User ID before proceeding.');
                return false;
            }
            const btn = document.getElementById('btnSubmitP2P');
            btn.disabled = true;
            btn.innerHTML = '<i class="zmdi zmdi-spinner zmdi-hc-spin mr-2"></i> Processing...';
        });
        </script>

        <!-- Pill Navigation Tabs -->
        <ul class="nav nav-pills mb-3" role="tablist" style="gap: 10px;">
            <li class="nav-item">
                <a class="nav-link font-weight-bold <?php echo ($activeTab === 'transfer') ? 'active' : ''; ?>" data-toggle="tab" href="#tab-transfer" style="border-radius: 10px; padding: 10px 20px;">
                    <i class="zmdi zmdi-time-restore mr-2"></i> Transfer History (Sent)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold <?php echo ($activeTab === 'received') ? 'active' : ''; ?>" data-toggle="tab" href="#tab-received" style="border-radius: 10px; padding: 10px 20px;">
                    <i class="zmdi zmdi-inbox mr-2"></i> Received Report
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Transfer History (Sent) Tab -->
            <div id="tab-transfer" class="tab-pane <?php echo ($activeTab === 'transfer') ? 'active' : 'fade'; ?>">
                <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-format-list-bulleted mr-2" style="color: #0284c7;"></i> P2P Sent History
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                                <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-3 px-4 text-center">#</th>
                                        <th class="py-3 px-3">Transaction ID</th>
                                        <th class="py-3 px-3">From Wallet</th>
                                        <th class="py-3 px-3">To Wallet</th>
                                        <th class="py-3 px-3">Receiver ID</th>
                                        <th class="py-3 px-3">Receiver Name</th>
                                        <th class="py-3 px-3 text-right">Amount</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                        <th class="py-3 px-3 text-center">Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php if (!empty($sentHistory)): ?>
                                        <?php $sr = 1; foreach ($sentHistory as $s): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="py-3 px-4 text-center font-weight-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                                <td class="py-3 px-3">
                                                    <code style="background: #f1f5f9; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?php echo htmlspecialchars($s['transfer_ref']); ?></code>
                                                </td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #475569;"><?php echo htmlspecialchars($s['from_wallet']); ?></td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #475569;"><?php echo htmlspecialchars($s['to_wallet']); ?></td>
                                                <td class="py-3 px-3 font-weight-bold" style="color: #0f172a;"><?php echo htmlspecialchars($s['receiver_id']); ?></td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #334155;"><?php echo htmlspecialchars($s['receiver_name']); ?></td>
                                                <td class="py-3 px-3 text-right font-weight-bold" style="color: #0f172a;">
                                                    <?php echo formatCurrency((float)$s['amount'], $selectedCurrency); ?>
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;"><?php echo htmlspecialchars($s['status']); ?></span>
                                                </td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo htmlspecialchars($s['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="zmdi zmdi-swap-off zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                                No P2P transfers sent yet.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Received Report Tab -->
            <div id="tab-received" class="tab-pane <?php echo ($activeTab === 'received') ? 'active' : 'fade'; ?>">
                <div class="card border-0 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 18px;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9; border-radius: 18px 18px 0 0;">
                        <h6 class="m-0 font-weight-bold" style="color: #0f172a;">
                            <i class="zmdi zmdi-inbox mr-2" style="color: #16a34a;"></i> P2P Received Report
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="color: #0f172a;">
                                <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-3 px-4 text-center">#</th>
                                        <th class="py-3 px-3">Transaction ID</th>
                                        <th class="py-3 px-3">From Wallet</th>
                                        <th class="py-3 px-3">To Wallet</th>
                                        <th class="py-3 px-3">Sender User ID</th>
                                        <th class="py-3 px-3">Sender Name</th>
                                        <th class="py-3 px-3 text-right">Amount Received</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                        <th class="py-3 px-3 text-center">Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php if (!empty($receivedReport)): ?>
                                        <?php $sr = 1; foreach ($receivedReport as $r): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="py-3 px-4 text-center font-weight-bold" style="color: #64748b;"><?php echo $sr++; ?></td>
                                                <td class="py-3 px-3">
                                                    <code style="background: #f1f5f9; color: #16a34a; padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?php echo htmlspecialchars($r['transfer_ref']); ?></code>
                                                </td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #475569;"><?php echo htmlspecialchars($r['from_wallet']); ?></td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #475569;"><?php echo htmlspecialchars($r['to_wallet']); ?></td>
                                                <td class="py-3 px-3 font-weight-bold" style="color: #0f172a;"><?php echo htmlspecialchars($r['sender_id']); ?></td>
                                                <td class="py-3 px-3 font-weight-semibold" style="color: #334155;"><?php echo htmlspecialchars($r['sender_name']); ?></td>
                                                <td class="py-3 px-3 text-right font-weight-bold" style="color: #16a34a;">
                                                    <?php echo formatCurrency((float)$r['amount'], $selectedCurrency); ?>
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    <span class="badge badge-pill px-3 py-1" style="background: rgba(22, 163, 74, 0.15); color: #16a34a; font-weight: 700;"><?php echo htmlspecialchars($r['status']); ?></span>
                                                </td>
                                                <td class="py-3 px-3 text-center text-muted small"><?php echo htmlspecialchars($r['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="zmdi zmdi-inbox zmdi-hc-3x d-block mb-2" style="color: #cbd5e1;"></i>
                                                No P2P transfers received yet.
                                            </td>
                                        </tr>
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

