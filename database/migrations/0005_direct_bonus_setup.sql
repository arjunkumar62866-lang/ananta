-- Migration: 0005_direct_bonus_setup.sql
-- Description: Add direct_bonus_wallet to user table and create tbl_direct_bonus_schedule table for 10-month direct bonus distribution.

-- Safe column addition for direct_bonus_wallet in user table
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'direct_bonus_wallet';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE `user` ADD COLUMN `direct_bonus_wallet` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `profit_sharing_wallet`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Create schedule table for 10-month Direct Bonus tracking
CREATE TABLE IF NOT EXISTS `tbl_direct_bonus_schedule` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `investment_id` INT NOT NULL,
  `beneficiary_id` VARCHAR(100) NOT NULL,
  `source_user_id` VARCHAR(100) NOT NULL,
  `investment_amount` DECIMAL(15,2) NOT NULL,
  `total_bonus` DECIMAL(15,2) NOT NULL,
  `installment_amount` DECIMAL(15,2) NOT NULL,
  `installment_number` TINYINT NOT NULL,
  `installment_month` VARCHAR(7) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'PENDING',
  `credited_at` DATETIME NULL,
  `withdrawal_status` VARCHAR(20) NULL DEFAULT 'NOT_WITHDRAWN',
  `withdrawal_date` DATETIME NULL,
  `closing_id` VARCHAR(100) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_inv_inst_ben` (`investment_id`, `installment_number`, `beneficiary_id`),
  KEY `idx_ben_status_month` (`beneficiary_id`, `status`, `installment_month`),
  KEY `idx_source_user` (`source_user_id`),
  KEY `idx_inst_month` (`installment_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
