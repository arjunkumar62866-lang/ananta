-- Migration: 0020_admin_wallet_control_12_wallets.sql
-- Description: Additive migration for Admin Wallet Control & 12 Distinct Wallets Architecture.
-- Ensures all 12 wallets have dedicated database columns in table `user` without touching existing data.

SET @dbname = DATABASE();
SET @tablename = 'user';

-- 1. Net Balance (net_balance)
SET @columnname = 'net_balance';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN net_balance DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER amount'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Active Investment (active_investment)
SET @columnname = 'active_investment';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN active_investment DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER net_balance'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. All Withdrawal (total_withdrawal)
SET @columnname = 'total_withdrawal';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN total_withdrawal DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER active_investment'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Rank Reward (rank_reward_wallet)
SET @columnname = 'rank_reward_wallet';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN rank_reward_wallet DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER vip_club_wallet'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 5. User Growth (user_growth_wallet)
SET @columnname = 'user_growth_wallet';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN user_growth_wallet DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER rank_reward_wallet'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 6. Company Turnover Income (company_turnover_wallet)
SET @columnname = 'company_turnover_wallet';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN company_turnover_wallet DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER user_growth_wallet'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 7. Ensure is_read exists in tbl_system_notifications
SET @tablename = 'tbl_system_notifications';
SET @columnname = 'is_read';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE tbl_system_notifications ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER created_by'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
