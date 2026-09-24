-- Migration: 0018_admin_panel_complete_features.sql
-- Description: Additive migration for Complete Admin Panel Requirements.
-- Adds locked_balance to user table, ensures support tickets table, notifications table, p2p transfer table, and system controls exist.

-- 1. Add locked_balance column to user table if not exists
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'locked_balance';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN locked_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER amount'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Add locked_balance_reason column to user table if not exists
SET @tablename = 'user';
SET @columnname = 'locked_balance_reason';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN locked_balance_reason TEXT NULL AFTER locked_balance'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. System Control Settings Table (tbl_system_control)
CREATE TABLE IF NOT EXISTS `tbl_system_control` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` VARCHAR(255) NOT NULL DEFAULT '1', -- '1' = ON, '0' = OFF
  `description` VARCHAR(255) NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT IGNORE INTO `tbl_system_control` (`setting_key`, `setting_value`, `description`) VALUES
('website_maintenance', '0', 'Website Maintenance Mode ON/OFF'),
('user_registration', '1', 'User Registration ON/OFF'),
('user_login', '1', 'User Login ON/OFF'),
('unlock_access', '1', 'Unlock Access / Activation ON/OFF'),
('investment_enable', '1', 'New Investment ON/OFF'),
('p2p_enable', '1', 'P2P Transfer & Investment ON/OFF'),
('deposit_enable', '1', 'Deposit Request ON/OFF'),
('withdrawal_enable', '1', 'Global Withdrawal ON/OFF'),
('global_income_enable', '1', 'Global Income Calculation ON/OFF'),
('offer_popup_enable', '1', 'Offer Popup Display ON/OFF');

-- 4. Unified Admin Audit Table (tbl_admin_audit_log)
CREATE TABLE IF NOT EXISTS `tbl_admin_audit_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` VARCHAR(100) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `target_user_id` VARCHAR(100) NULL,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `wallet_type` VARCHAR(100) NULL,
  `previous_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `new_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reason` TEXT NULL,
  `reference_id` VARCHAR(100) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 5. User Login & Device History Table (tbl_user_login_history)
CREATE TABLE IF NOT EXISTS `tbl_user_login_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `device_type` VARCHAR(100) NULL,
  `browser` VARCHAR(100) NULL,
  `user_agent` TEXT NULL,
  `login_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 6. Support Tickets Table (tbl_support_tickets)
CREATE TABLE IF NOT EXISTS `tbl_support_tickets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_no` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'General',
  `message` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'OPEN', -- OPEN, PENDING, RESOLVED, CLOSED
  `admin_reply` TEXT NULL,
  `replied_by` VARCHAR(100) NULL,
  `replied_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 7. Ticket Replies Table (tbl_ticket_replies)
CREATE TABLE IF NOT EXISTS `tbl_ticket_replies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` INT NOT NULL,
  `sender_type` VARCHAR(20) NOT NULL, -- USER or ADMIN
  `sender_id` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 8. System Notifications Table (tbl_system_notifications)
CREATE TABLE IF NOT EXISTS `tbl_system_notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `target_type` VARCHAR(20) NOT NULL DEFAULT 'GLOBAL', -- GLOBAL or USER
  `target_user_id` VARCHAR(100) NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_by` VARCHAR(100) NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_target_type` (`target_type`),
  KEY `idx_target_user` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 9. P2P Transfer Table (tbl_p2p_transfer) if not exists
CREATE TABLE IF NOT EXISTS `tbl_p2p_transfer` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
  `sender_id` VARCHAR(100) NOT NULL,
  `receiver_id` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` DECIMAL(15,2) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'COMPLETED', -- PENDING, COMPLETED, CANCELLED
  `remarks` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
