<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get current Login & Registration Control State
 */
function getLoginRegControlState($pdo) {
    try {
        $stmt = $pdo->query("SELECT status, message_type, message_text FROM tbl_login_registration_control WHERE id = 1 LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return [
                'status' => strtoupper($row['status'] ?? 'ON'),
                'message_type' => strtoupper($row['message_type'] ?? 'WARNING'),
                'message_text' => $row['message_text'] ?? ''
            ];
        }
    } catch (Exception $e) {
        // Fallback default if table missing or query fails
    }
    return [
        'status' => 'ON',
        'message_type' => 'WARNING',
        'message_text' => ''
    ];
}

/**
 * Generate CSRF token for Login/Reg control
 */
function getLoginRegCsrfToken() {
    if (empty($_SESSION['login_reg_control_csrf'])) {
        $_SESSION['login_reg_control_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['login_reg_control_csrf'];
}

/**
 * Verify CSRF token for Login/Reg control
 */
function verifyLoginRegCsrfToken($token) {
    if (empty($_SESSION['login_reg_control_csrf']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['login_reg_control_csrf'], $token);
}
