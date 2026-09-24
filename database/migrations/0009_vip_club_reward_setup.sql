-- Migration: 0009_vip_club_reward_setup.sql
-- Description: Create tables and columns for VIP Club & Reward System (Requirement #16).

-- 1. Add vip_club_wallet column to user table if not exists
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'vip_club_wallet';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN vip_club_wallet DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER mentor_income_wallet'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. VIP Level Configuration Table
CREATE TABLE IF NOT EXISTS `tbl_vip_level_config` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `level_id` INT NOT NULL UNIQUE,
  `name` VARCHAR(50) NOT NULL,
  `req_left_ids` INT NOT NULL,
  `req_right_ids` INT NOT NULL,
  `req_left_business` DECIMAL(15,2) NOT NULL, -- in USD
  `req_right_business` DECIMAL(15,2) NOT NULL, -- in USD
  `reward_amount` DECIMAL(15,2) NOT NULL, -- in USD
  `vip_income_rate` DECIMAL(5,2) NOT NULL, -- weaker leg rate %
  `has_turnover_share` TINYINT(1) NOT NULL DEFAULT 0, -- 1 for Levels 7-10 (0.5% turnover)
  `monthly_repeat_business` DECIMAL(15,2) NOT NULL, -- in USD
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Seed all 10 VIP Levels if table is empty
INSERT IGNORE INTO `tbl_vip_level_config`
(`level_id`, `name`, `req_left_ids`, `req_right_ids`, `req_left_business`, `req_right_business`, `reward_amount`, `vip_income_rate`, `has_turnover_share`, `monthly_repeat_business`)
VALUES
(1,  'VIP Level 1',  30,    30,    4000.00,       4000.00,       200.00,   0.50, 0, 4000.00),
(2,  'VIP Level 2',  60,    60,    10000.00,      10000.00,      500.00,   1.00, 0, 10000.00),
(3,  'VIP Level 3',  100,   100,   16000.00,      16000.00,      800.00,   1.00, 0, 16000.00),
(4,  'VIP Level 4',  200,   200,   30000.00,      30000.00,      2000.00,  1.00, 0, 30000.00),
(5,  'VIP Level 5',  500,   500,   100000.00,     100000.00,     4000.00,  1.00, 0, 100000.00),
(6,  'VIP Level 6',  1200,  1200,  30000000.00,   30000000.00,   10000.00, 1.00, 0, 30000000.00), -- $3 Crore
(7,  'VIP Level 7',  5000,  5000,  100000000.00,  100000000.00,  30000.00, 0.50, 1, 100000.00),   -- $10 Crore
(8,  'VIP Level 8',  12000, 12000, 160000000.00,  160000000.00,  70000.00, 0.50, 1, 100000.00),   -- $16 Crore
(9,  'VIP Level 9',  20000, 20000, 260000000.00,  260000000.00,  150000.00,0.50, 1, 100000.00),   -- $26 Crore
(10, 'VIP Level 10', 50000, 50000, 750000000.00,  750000000.00,  350000.00,0.50, 1, 100000.00);   -- $75 Crore

-- 3. User VIP Qualification & Reward Table
CREATE TABLE IF NOT EXISTS `tbl_vip_user_qualification` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(100) NOT NULL,
  `vip_level` INT NOT NULL,
  `left_ids_achieved` INT NOT NULL DEFAULT 0,
  `right_ids_achieved` INT NOT NULL DEFAULT 0,
  `left_business_achieved` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `right_business_achieved` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `weaker_leg_business` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reward_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reward_status` VARCHAR(20) NOT NULL DEFAULT 'CREDITED', -- PENDING, CREDITED, BLOCKED
  `qualified_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_vip` (`user_id`, `vip_level`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_vip_level` (`vip_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 4. VIP Monthly Income Payout Schedule Table
CREATE TABLE IF NOT EXISTS `tbl_vip_monthly_schedule` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` VARCHAR(100) NOT NULL,
  `vip_level` INT NOT NULL,
  `closing_month` VARCHAR(7) NOT NULL, -- e.g. '2026-09'
  `weaker_leg_business` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `weaker_leg_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `weaker_leg_payout` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `company_turnover` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `turnover_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `turnover_payout` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_payout` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(20) NOT NULL DEFAULT 'CREDITED',
  `credited_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `closing_id` VARCHAR(100) NULL,
  UNIQUE KEY `uk_user_vip_month` (`user_id`, `vip_level`, `closing_month`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_closing_month` (`closing_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 5. VIP Admin Adjustments Audit Trail Table
CREATE TABLE IF NOT EXISTS `tbl_vip_admin_audit` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` VARCHAR(100) NOT NULL,
  `action` VARCHAR(50) NOT NULL, -- CREDIT, DEBIT, ADJUSTMENT
  `user_id` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `wallet` VARCHAR(50) NOT NULL DEFAULT 'vip_club_wallet',
  `previous_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `new_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reason` TEXT NOT NULL,
  `reference` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
