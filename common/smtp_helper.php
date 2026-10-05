<?php
/**
 * Hostinger Authenticated SMTP Transport Helper for ANANTA
 *
 * Dedicated strictly to secure transactional email delivery (e.g. Transaction Key OTP)
 * Compatible with Hostinger Business/Titan SMTP (smtp.hostinger.com).
 */

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('getAnantaSmtpConfig')) {
    function getAnantaSmtpConfig() {
        static $cachedConfig = null;
        if ($cachedConfig !== null) {
            return $cachedConfig;
        }

        $config = [
            'host'       => 'smtp.hostinger.com',
            'port'       => 465,
            'secure'     => 'ssl',
            'username'   => '',
            'password'   => '',
            'from_email' => 'no-reply@anantamtptl.com',
            'from_name'  => 'ANANTA Security',
            'reply_to'   => 'anantamultitread@gmail.com',
        ];

        // 1. Primary secure location: outside public_html on Hostinger production
        $serverConfigFile = '/home/u914531711/smtp_config.php';
        if (file_exists($serverConfigFile)) {
            $serverConfig = @include $serverConfigFile;
            if (is_array($serverConfig)) {
                $config = array_merge($config, $serverConfig);
            }
        } elseif (file_exists(__DIR__ . '/smtp_config.php')) {
            // 2. Local/staging override in common/smtp_config.php
            $localConfig = @include __DIR__ . '/smtp_config.php';
            if (is_array($localConfig)) {
                $config = array_merge($config, $localConfig);
            }
        } elseif (file_exists(dirname(__DIR__) . '/smtp_config.php')) {
            // 3. Project root override
            $rootConfig = @include dirname(__DIR__) . '/smtp_config.php';
            if (is_array($rootConfig)) {
                $config = array_merge($config, $rootConfig);
            }
        }

        // 4. Server Environment Variables override
        if (!empty(getenv('SMTP_HOST')))       $config['host']       = trim(getenv('SMTP_HOST'));
        if (!empty(getenv('SMTP_PORT')))       $config['port']       = (int)getenv('SMTP_PORT');
        if (!empty(getenv('SMTP_SECURE')))     $config['secure']     = trim(getenv('SMTP_SECURE'));
        if (!empty(getenv('SMTP_USER')))       $config['username']   = trim(getenv('SMTP_USER'));
        if (!empty(getenv('SMTP_PASS')))       $config['password']   = trim(getenv('SMTP_PASS'));
        if (!empty(getenv('SMTP_FROM_EMAIL'))) $config['from_email'] = trim(getenv('SMTP_FROM_EMAIL'));
        if (!empty(getenv('SMTP_FROM_NAME')))  $config['from_name']  = trim(getenv('SMTP_FROM_NAME'));
        if (!empty(getenv('SMTP_REPLY_TO')))   $config['reply_to']   = trim(getenv('SMTP_REPLY_TO'));

        $cachedConfig = $config;
        return $cachedConfig;
    }
}

if (!function_exists('sendAnantaSmtpMail')) {
    function sendAnantaSmtpMail($toEmail, $toName, $subject, $htmlContent, $replyToEmail = null) {
        $toEmail = trim((string)$toEmail);
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Invalid recipient email address.'];
        }

        $config = getAnantaSmtpConfig();

        $hasCredentials = !empty($config['host'])
            && !empty($config['username'])
            && !empty($config['password'])
            && strpos($config['password'], 'YOUR_HOSTINGER_EMAIL_PASSWORD') === false
            && strpos($config['password'], 'CHANGE_ME') === false;

        if ($hasCredentials) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $config['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $config['username'];
                $mail->Password   = $config['password'];

                $secureType = strtolower($config['secure'] ?? 'ssl');
                if ($secureType === 'tls' || (int)$config['port'] === 587) {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
                $mail->Port       = (int)($config['port'] ?: 465);
                $mail->Timeout    = 10;
                $mail->CharSet    = 'UTF-8';

                $fromEmail = !empty($config['from_email']) ? $config['from_email'] : $config['username'];
                $fromName  = !empty($config['from_name']) ? $config['from_name'] : 'ANANTA Security';
                $mail->setFrom($fromEmail, $fromName);

                $replyTo = $replyToEmail ?: ($config['reply_to'] ?: $fromEmail);
                if (!empty($replyTo)) {
                    $mail->addReplyTo($replyTo);
                }

                $mail->addAddress($toEmail, $toName ?: 'User');

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlContent;
                $mail->AltBody = strip_tags($htmlContent);

                $mail->send();
                return ['status' => 'success', 'transport' => 'smtp'];
            } catch (Exception $e) {
                // Log safe error without passwords or OTP values
                error_log("Transaction Key OTP SMTP Delivery Failed to {$toEmail}: " . $mail->ErrorInfo);
                return [
                    'status'  => 'error',
                    'message' => 'Unable to send OTP via mail server right now. Please try again later.',
                    'error'   => $mail->ErrorInfo
                ];
            }
        }

        // Fallback: If SMTP credentials have not been saved in /home/u914531711/smtp_config.php yet,
        // log a clear administrative warning and attempt standard sendmail with envelope -f
        error_log("Transaction Key OTP Warning: Authenticated SMTP credentials are not yet configured in /home/u914531711/smtp_config.php. Falling back to MTA.");

        $fromEmail = !empty($config['from_email']) ? $config['from_email'] : 'no-reply@anantamtptl.com';
        $fromName  = !empty($config['from_name']) ? $config['from_name'] : 'ANANTA Security';
        $replyTo   = $replyToEmail ?: ($config['reply_to'] ?: 'anantamultitread@gmail.com');

        $headers  = "From: " . strip_tags($fromName) . " <" . strip_tags($fromEmail) . ">\r\n";
        $headers .= "Reply-To: " . strip_tags($replyTo) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $sent = @mail($toEmail, $subject, $htmlContent, $headers, "-f" . escapeshellarg($fromEmail));
        if ($sent) {
            return ['status' => 'success', 'transport' => 'mta_fallback'];
        }

        return ['status' => 'error', 'message' => 'Unable to send OTP email right now. Please try again later.'];
    }
}
