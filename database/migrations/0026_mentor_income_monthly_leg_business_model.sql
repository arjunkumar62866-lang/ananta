-- Migration: 0026_mentor_income_monthly_leg_business_model.sql
-- Description: Replace fixed contribution percentage model with Monthly Leg Business Contribution Model.
-- Adds mentor income dynamic rate configuration table and extends schedule table for business and audit snapshots.

-- 1. Create Mentor Income Rate and Policy Configuration Table
CREATE TABLE IF NOT EXISTS `tbl_mentor_income_config` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `rate_percent` DECIMAL(5,2) NOT NULL DEFAULT 2.00,
  `is_auto_closing` TINYINT(1) NOT NULL DEFAULT 0,
  `effective_from_month` VARCHAR(7) NOT NULL DEFAULT '2026-01',
  `updated_by` VARCHAR(100) NOT NULL DEFAULT 'SYSTEM',
  `remarks` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_effective_month` (`effective_from_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Initialize default 2.00% rate if empty
INSERT INTO `tbl_mentor_income_config` (`rate_percent`, `is_auto_closing`, `effective_from_month`, `updated_by`, `remarks`)
SELECT 2.00, 0, '2026-01', 'SYSTEM', 'Default canonical 2.00% Mentor Income rate'
WHERE NOT EXISTS (SELECT 1 FROM `tbl_mentor_income_config`);

-- 2. Create Mentor Income Rate Change Audit History Table
CREATE TABLE IF NOT EXISTS `tbl_mentor_income_rate_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `old_rate` DECIMAL(5,2) NOT NULL,
  `new_rate` DECIMAL(5,2) NOT NULL,
  `effective_from_month` VARCHAR(7) NOT NULL,
  `admin_id` VARCHAR(100) NOT NULL,
  `reason` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_eff_month` (`effective_from_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 3. Extend tbl_mentor_income_schedule with calculation snapshot columns safely
SET @dbname = DATABASE();

-- 3.1 leg_monthly_business
SET @col1 = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'tbl_mentor_income_schedule' AND COLUMN_NAME = 'leg_monthly_business') > 0, 'SELECT 1', 'ALTER TABLE `tbl_mentor_income_schedule` ADD COLUMN `leg_monthly_business` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `mentor_income_rate`'));
PREPARE stmt1 FROM @col1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

-- 3.2 total_eligible_legs_business
SET @col2 = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'tbl_mentor_income_schedule' AND COLUMN_NAME = 'total_eligible_legs_business') > 0, 'SELECT 1', 'ALTER TABLE `tbl_mentor_income_schedule` ADD COLUMN `total_eligible_legs_business` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `leg_monthly_business`'));
PREPARE stmt2 FROM @col2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- 3.3 is_auto_closing
SET @col3 = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'tbl_mentor_income_schedule' AND COLUMN_NAME = 'is_auto_closing') > 0, 'SELECT 1', 'ALTER TABLE `tbl_mentor_income_schedule` ADD COLUMN `is_auto_closing` TINYINT(1) NOT NULL DEFAULT 0 AFTER `closing_id`'));
PREPARE stmt3 FROM @col3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- 3.4 calculation_version
SET @col4 = (SELECT IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'tbl_mentor_income_schedule' AND COLUMN_NAME = 'calculation_version') > 0, 'SELECT 1', 'ALTER TABLE `tbl_mentor_income_schedule` ADD COLUMN `calculation_version` VARCHAR(20) NOT NULL DEFAULT \'LEG_BUSINESS_V2\' AFTER `is_auto_closing`'));
PREPARE stmt4 FROM @col4;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;
