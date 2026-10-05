<?php
/**
 * Hostinger Authenticated SMTP Configuration Template
 *
 * To activate authenticated SMTP delivery on Hostinger:
 * 1. Copy this file to /home/u914531711/smtp_config.php (OUTSIDE public_html)
 * 2. Update with your real Hostinger email account credentials
 *
 * NEVER commit your real passwords or credentials to Git.
 */
return [
    'host'       => 'smtp.hostinger.com',
    'port'       => 465,                  // 465 for SSL, or 587 for TLS
    'secure'     => 'ssl',                // 'ssl' or 'tls'
    'username'   => 'no-reply@anantamtptl.com', // Your Hostinger mailbox email
    'password'   => 'YOUR_HOSTINGER_EMAIL_PASSWORD',
    'from_email' => 'no-reply@anantamtptl.com',
    'from_name'  => 'ANANTA Security',
    'reply_to'   => 'anantamultitread@gmail.com',
];
