-- Migration: 0011_admin_panel_final_structure.sql
-- Description: Additive migration for Requirement #18 Admin Panel Final Structure.
-- Provides persistent system settings/toggles, admin audit trail fields, login/device history table, user wallet controls, and support ticket structures.

-- 1. Create System Control Settings Table (tbl_system_control) for global ON/OFF toggles
CREATE TABLE IF NOT EXISTS `tbl_system_control` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` VARCHAR(255) NOT NULL DEFAULT '1', -- '1' = ON, '0' = OFF
  `description` VARCHAR(255) NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Seed default global controls if not exists
INSERT IGNORE INTO `tbl_system_control` (`setting_key`, `setting_value`, `description`) VALUES
('website_maintenance', '0', 'Website Maintenance Mode ON/OFF'),
('user_registration', '1', 'User Registration ON/OFF'),
('user_login', '1', 'User Login ON/OFF'),
('unlock_access', '1', 'Unlock Access / Activation ON/OFF'),
('investment_enable', '1', 'New Investment ON/OFF'),
('p2p_enable', '1', 'P2P Transfer & Investment ON/OFF'),
('deposit_enable', '1', 'Deposit Request ON/OFF'),
('withdrawal_enable', '1', 'Global Withdrawal ON/OFF'),
('global_income_enable', '1', 'Global Income Calculation ON/OFF');

-- 2. Add withdrawal_status column to user table for individual user withdrawal ON/OFF control
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'withdrawal_status';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN withdrawal_status TINYINT(1) NOT NULL DEFAULT 1 AFTER active'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Unified Financial & Action Admin Audit Table (tbl_admin_audit_log)
CREATE TABLE IF NOT EXISTS `tbl_admin_audit_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` VARCHAR(100) NOT NULL,
  `action` VARCHAR(100) NOT NULL, -- e.g. CREDIT, DEBIT, STATUS_CHANGE, TOGGLE_SETTING, USER_DELETE
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

-- 4. User Login & Device History Table (tbl_user_login_history)
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

-- 5. Support Ticket & Communication Table (tbl_support_tickets)
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

-- 6. System Notifications Table (tbl_system_notifications)
CREATE TABLE IF NOT EXISTS `tbl_system_notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `target_type` VARCHAR(20) NOT NULL DEFAULT 'GLOBAL', -- GLOBAL or USER
  `target_user_id` VARCHAR(100) NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_by` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_target_type` (`target_type`),
  KEY `idx_target_user` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
