-- Migration: 0007_mentor_income_setup.sql
-- Description: Add mentor_income_wallet to user table and create tables for Mentor Income contribution and payout schedules.

-- 1. Safe column addition for mentor_income_wallet in user table
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'mentor_income_wallet';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE `user` ADD COLUMN `mentor_income_wallet` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `direct_bonus_wallet`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Contribution Percentage Source Table (Mentor -> Direct User Contribution % Mapping)
CREATE TABLE IF NOT EXISTS `tbl_mentor_direct_contribution` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `mentor_id` VARCHAR(100) NOT NULL,
  `direct_user_id` VARCHAR(100) NOT NULL,
  `contribution_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_mentor_direct` (`mentor_id`, `direct_user_id`),
  KEY `idx_mentor_id` (`mentor_id`),
  KEY `idx_direct_user_id` (`direct_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 3. Monthly Mentor Income Schedule / Payout Ledger Table
CREATE TABLE IF NOT EXISTS `tbl_mentor_income_schedule` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `mentor_id` VARCHAR(100) NOT NULL,
  `direct_user_id` VARCHAR(100) NOT NULL,
  `closing_month` VARCHAR(7) NOT NULL,
  `mentor_monthly_income` DECIMAL(15,2) NOT NULL,
  `mentor_income_rate` DECIMAL(5,2) NOT NULL DEFAULT 2.00,
  `total_mentor_income` DECIMAL(15,2) NOT NULL,
  `contribution_percentage` DECIMAL(5,2) NOT NULL,
  `payout_amount` DECIMAL(15,2) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'CREDITED',
  `credited_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `closing_id` VARCHAR(100) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_mentor_direct_month` (`mentor_id`, `direct_user_id`, `closing_month`),
  KEY `idx_mentor_month` (`mentor_id`, `closing_month`),
  KEY `idx_direct_month` (`direct_user_id`, `closing_month`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Ensure table collations match baseline tbl_sponsor
ALTER TABLE `tbl_mentor_direct_contribution` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
ALTER TABLE `tbl_mentor_income_schedule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
