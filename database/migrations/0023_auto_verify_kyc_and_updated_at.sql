-- database/migrations/0023_auto_verify_kyc_and_updated_at.sql
-- Auto-verify KYC records upon submission and track KYC update timestamps

-- 1. Add updated_at column to kyc table if not present
SET @dbname = DATABASE();
SET @tablename = 'kyc';
SET @columnname = 'updated_at';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE (table_name = @tablename)
        AND (table_schema = @dbname)
        AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    'ALTER TABLE kyc ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Automatically mark all existing submitted KYC records as Verified (status = 1)
UPDATE `kyc`
SET `status` = '1'
WHERE `status` = '0'
  AND (
      TRIM(COALESCE(`holder_name`, '')) != ''
      OR TRIM(COALESCE(`ac_number`, '')) != ''
      OR TRIM(COALESCE(`mimo`, '')) != ''
      OR TRIM(COALESCE(`pan`, '')) != ''
  );

-- 3. Synchronize user table KYC status to 2 (Verified) for all members with verified KYC
UPDATE `user` u
JOIN `kyc` k ON (u.userid = k.userid OR u.id = k.userid)
SET u.`kyc` = 2
WHERE k.`status` = 1 AND u.`kyc` != 2;
