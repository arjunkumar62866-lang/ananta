-- Migration: 0012_user_wallet_dashboard.sql
-- Description: Indexes and optimization for Requirement #19 User Wallet Dashboard.

-- 1. Ensure user table has index on userid for fast single-user dashboard aggregations
SET @dbname = DATABASE();
SET @tablename = 'user';
SET @indexname = 'idx_user_userid_fast';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND INDEX_NAME = @indexname
    ) > 0,
    'SELECT 1',
    'CREATE INDEX idx_user_userid_fast ON user(userid)'
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists;

-- 2. Ensure tbl_transaction has proper composite index on user_id, created_date for fast wallet history queries
SET @dbname = DATABASE();
SET @tablename = 'tbl_transaction';
SET @indexname = 'idx_txn_user_date';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND INDEX_NAME = @indexname
    ) > 0,
    'SELECT 1',
    'CREATE INDEX idx_txn_user_date ON tbl_transaction(user_id, created_date)'
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists;
