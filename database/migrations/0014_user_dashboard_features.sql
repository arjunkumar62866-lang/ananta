-- database/migrations/0014_user_dashboard_features.sql
-- Additive Schema Updates for Requirement #21 User Side Dashboard Features

-- 1. Add bep20_address to user table if not present
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @columnname = 'bep20_address';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN bep20_address VARCHAR(255) NULL DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Add withdrawal_method to tbl_transaction table if not present
SET @tablename = 'tbl_transaction';
SET @columnname = 'withdrawal_method';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE tbl_transaction ADD COLUMN withdrawal_method VARCHAR(50) NULL DEFAULT NULL;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. Create tbl_p2p_transfer table for P2P Transfer & Received tracking
CREATE TABLE IF NOT EXISTS tbl_p2p_transfer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_ref VARCHAR(100) NOT NULL,
    sender_id VARCHAR(50) NOT NULL,
    receiver_id VARCHAR(50) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'COMPLETED',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_p2p_sender (sender_id),
    INDEX idx_p2p_receiver (receiver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
