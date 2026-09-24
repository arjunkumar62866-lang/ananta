<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/common/connection.php';
require_once __DIR__ . '/common/login_reg_control_helper.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    $adminId = trim($_POST['admin_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($adminId) || empty($password)) {
        echo json_encode(['status' => false, 'message' => 'Admin ID and Password are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT admin_id, password_hash FROM tbl_login_reg_control_auth WHERE admin_id = ? LIMIT 1");
        $stmt->execute([$adminId]);
        $auth = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($auth && password_verify($password, $auth['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['login_reg_control_authenticated'] = true;
            $_SESSION['login_reg_control_admin_id'] = $auth['admin_id'];

            echo json_encode([
                'status' => true,
                'message' => 'Authenticated successfully.',
                'csrf_token' => getLoginRegCsrfToken(),
                'control' => getLoginRegControlState($pdo)
            ]);
            exit;
        } else {
            echo json_encode(['status' => false, 'message' => 'Invalid Control Credentials.']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => false, 'message' => 'Database error during authentication.']);
        exit;
    }
}

if ($action === 'get_state') {
    if (empty($_SESSION['login_reg_control_authenticated'])) {
        echo json_encode(['status' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    echo json_encode([
        'status' => true,
        'control' => getLoginRegControlState($pdo),
        'csrf_token' => getLoginRegCsrfToken()
    ]);
    exit;
}

if ($action === 'save_state') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => false, 'message' => 'Invalid request method.']);
        exit;
    }

    if (empty($_SESSION['login_reg_control_authenticated'])) {
        echo json_encode(['status' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyLoginRegCsrfToken($csrfToken)) {
        echo json_encode(['status' => false, 'message' => 'CSRF validation failed.']);
        exit;
    }

    $status = strtoupper(trim($_POST['status'] ?? 'ON'));
    $messageType = strtoupper(trim($_POST['message_type'] ?? 'WARNING'));
    $messageText = trim($_POST['message_text'] ?? '');

    if (!in_array($status, ['ON', 'OFF'])) {
        $status = 'ON';
    }
    if (!in_array($messageType, ['WARNING', 'ERROR'])) {
        $messageType = 'WARNING';
    }

    // Escape/sanitize message text to prevent XSS/HTML injection when displayed
    $messageTextSanitized = htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $pdo->prepare("UPDATE tbl_login_registration_control SET status = ?, message_type = ?, message_text = ?, updated_at = NOW() WHERE id = 1");
        $stmt->execute([$status, $messageType, $messageTextSanitized]);

        echo json_encode([
            'status' => true,
            'message' => 'Control settings saved successfully.',
            'control' => [
                'status' => $status,
                'message_type' => $messageType,
                'message_text' => $messageTextSanitized
            ]
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => false, 'message' => 'Failed to save control settings.']);
        exit;
    }
}

if ($action === 'logout') {
    unset($_SESSION['login_reg_control_authenticated']);
    unset($_SESSION['login_reg_control_admin_id']);
    unset($_SESSION['login_reg_control_csrf']);
    echo json_encode(['status' => true, 'message' => 'Logged out successfully.']);
    exit;
}

echo json_encode(['status' => false, 'message' => 'Invalid action.']);
exit;
