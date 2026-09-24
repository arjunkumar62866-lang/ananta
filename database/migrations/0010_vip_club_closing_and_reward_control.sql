-- Migration: 0010_vip_club_closing_and_reward_control.sql
-- Description: Indexes, columns and constraints for Requirement #17 VIP Club immediate reward release, 11th-date closing and monthly repeat control.

-- 1. Ensure reward_status column has proper index in tbl_vip_user_qualification
SET @dbname = DATABASE();
SET @tablename = 'tbl_vip_user_qualification';
SET @columnname = 'reward_status';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE tbl_vip_user_qualification ADD COLUMN reward_status VARCHAR(20) NOT NULL DEFAULT \'CREDITED\''
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Add closing_day column or verify closing_date in tbl_vip_monthly_schedule for audit
SET @dbname = DATABASE();
SET @tablename = 'tbl_vip_monthly_schedule';
SET @columnname = 'closing_date';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = @dbname
            AND TABLE_NAME = @tablename
            AND COLUMN_NAME = @columnname
    ) > 0,
    'SELECT 1',
    'ALTER TABLE tbl_vip_monthly_schedule ADD COLUMN closing_date DATE NULL AFTER closing_month'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
