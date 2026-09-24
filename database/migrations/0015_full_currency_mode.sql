-- database/migrations/0015_full_currency_mode.sql
-- Additive Schema Updates for Requirement #22 Full Currency Mode System

SET @dbname = DATABASE();

-- 1. Add currency_preference to user table if not present
SET @tablename = 'user';
SET @columnname = 'currency_preference';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE user ADD COLUMN currency_preference VARCHAR(10) NOT NULL DEFAULT \'USD\';'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Ensure usd_to_inr setting exists in tbl_system_control
INSERT INTO tbl_system_control (setting_key, setting_value)
SELECT 'usd_to_inr', '90'
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_system_control WHERE setting_key = 'usd_to_inr'
);
