-- database/migrations/0017_user_account_activation_11usd.sql
-- Additive Schema Updates for Requirement #23 User Account Activation / $11 Unlock Access

SET @dbname = DATABASE();

-- 1. Create tbl_account_activation table for activation and renewal tracking
CREATE TABLE IF NOT EXISTS tbl_account_activation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(100) NOT NULL UNIQUE,
    activator_user_id VARCHAR(100) NOT NULL,
    target_user_id VARCHAR(100) NOT NULL,
    activation_type VARCHAR(50) NOT NULL, -- SELF_ACTIVATION, OTHER_USER_ACTIVATION, RENEWAL, OTHER_USER_RENEWAL
    amount_usd DECIMAL(15,2) NOT NULL DEFAULT 11.00,
    amount_inr DECIMAL(15,2) NOT NULL DEFAULT 990.00,
    activation_start_date DATETIME NOT NULL,
    activation_expiry_date DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_act_activator (activator_user_id),
    INDEX idx_act_target (target_user_id),
    INDEX idx_act_expiry (activation_expiry_date),
    INDEX idx_act_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 2. Add activation tracking columns to user table if not present
SET @tablename = 'user';

-- Column: activation_start_date
SET @columnname = 'activation_start_date';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN activation_start_date DATETIME NULL DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Column: activation_expiry_date
SET @columnname = 'activation_expiry_date';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN activation_expiry_date DATETIME NULL DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
