-- Migration: 0013_ananta_package_system.sql
-- Description: Create package master table (tbl_ananta_package_config), snapshot fields in tbl_roi_one, 30% bonus wallet column, and investment/capital withdrawal transaction logs.

-- Fix closingdate default in tbl_roi_one if needed to prevent MySQL strict mode error
ALTER TABLE tbl_roi_one MODIFY COLUMN closingdate DATE NULL DEFAULT NULL;

-- 1. Create Ananta Package Configuration Master Table
CREATE TABLE IF NOT EXISTS `tbl_ananta_package_config` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `package_id` VARCHAR(50) NOT NULL UNIQUE,
  `package_name` VARCHAR(100) NOT NULL,
  `package_type` VARCHAR(50) NOT NULL DEFAULT 'ANANTA_NIVESH', -- ANANTA_NIVESH, BONUS_30, TOUR
  `min_investment_usd` DECIMAL(15,2) NOT NULL DEFAULT 145.00,
  `max_investment_usd` DECIMAL(15,2) NULL, -- NULL = No Max Limit (Unlimited)
  `bonus_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `lock_period_months` INT NOT NULL DEFAULT 48,
  `withdrawal_deduction_percent` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
  `status` TINYINT(1) NOT NULL DEFAULT 1, -- 1 = Active, 0 = Inactive
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Seed default packages as required by Requirement #20
INSERT IGNORE INTO `tbl_ananta_package_config`
(`package_id`, `package_name`, `package_type`, `min_investment_usd`, `max_investment_usd`, `bonus_percentage`, `lock_period_months`, `withdrawal_deduction_percent`, `status`)
VALUES
('BASIC',      'Basic Package',        'ANANTA_NIVESH', 145.00,    1000.00,  0.00,  48, 15.00, 1),
('ADVANCE',    'Advance Package',      'ANANTA_NIVESH', 1001.00,   NULL,     0.00,  48, 15.00, 1),
('PREMIUM',    'Premium Package',      'ANANTA_NIVESH', 12501.00,  NULL,     0.00,  48, 15.00, 1),
('BONUS_30',   '30% Bonus Package',    'BONUS_30',      145.00,    NULL,     30.00, 6,  15.00, 1),
('TOUR',       'Tour Package',         'TOUR',          145.00,    NULL,     0.00,  48, 15.00, 1);

-- 2. Add 30% Bonus Wallet column to user table if not exists
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'bonus_30_wallet';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN bonus_30_wallet DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER vip_club_wallet'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Add Snapshot fields to tbl_roi_one table for immutable historical investment contracts
SET @tablename = 'tbl_roi_one';

-- Field: package_code
SET @columnname = 'package_code';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN package_code VARCHAR(50) NULL AFTER name'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: real_fund_usd
SET @columnname = 'real_fund_usd';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN real_fund_usd DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER package_code'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: bonus_percent_snapshot
SET @columnname = 'bonus_percent_snapshot';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN bonus_percent_snapshot DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER real_fund_usd'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: bonus_amount_usd
SET @columnname = 'bonus_amount_usd';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN bonus_amount_usd DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER bonus_percent_snapshot'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: lock_period_months
SET @columnname = 'lock_period_months';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN lock_period_months INT NOT NULL DEFAULT 48 AFTER bonus_amount_usd'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: maturity_date
SET @columnname = 'maturity_date';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN maturity_date DATE NULL AFTER lock_period_months'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: deduction_percent_snapshot
SET @columnname = 'deduction_percent_snapshot';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN deduction_percent_snapshot DECIMAL(5,2) NOT NULL DEFAULT 15.00 AFTER maturity_date'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- Field: capital_withdrawal_status
SET @columnname = 'capital_withdrawal_status';
SET @preparedStatement = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0, 'SELECT 1', 'ALTER TABLE tbl_roi_one ADD COLUMN capital_withdrawal_status VARCHAR(20) NOT NULL DEFAULT \'LOCKED\' AFTER deduction_percent_snapshot'));
PREPARE alterIfNotExists FROM @preparedStatement; EXECUTE alterIfNotExists; DEALLOCATE PREPARE alterIfNotExists;

-- 4. Capital Withdrawal Request Log Table
CREATE TABLE IF NOT EXISTS `tbl_capital_withdrawal_request` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(100) NOT NULL,
  `investment_id` INT NOT NULL,
  `package_code` VARCHAR(50) NOT NULL,
  `real_fund_usd` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `deduction_percent` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
  `deduction_amount_usd` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `net_withdrawal_usd` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `bonus_reconciled_usd` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(20) NOT NULL DEFAULT 'PAID', -- PENDING, APPROVED, PAID, REJECTED
  `requested_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `processed_at` DATETIME NULL,
  KEY `idx_user_id` (`user_id`),
  KEY `idx_investment` (`investment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
