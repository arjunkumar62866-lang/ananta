-- database/migrations/0025_backfill_historical_user_notifications.sql
-- Backfill historical user notifications from existing verified records:
-- 1. Account Welcome / Registration for all existing members
-- 2. Approved KYC notifications for all verified KYC accounts
-- 3. Package investment activation notifications for historical investments

-- 1. Backfill Account Registration / Welcome notifications
INSERT INTO `tbl_user_notifications` (`user_id`, `type`, `title`, `message`, `ref_id`, `is_read`, `created_at`)
SELECT 
    u.`userid` AS `user_id`,
    'GENERAL' AS `type`,
    'Welcome to Ananta Multi Trade' AS `title`,
    CONCAT('Welcome to Ananta Multi Trade Private Limited, ', TRIM(COALESCE(u.`name`, 'Member')), '! Your account (', u.`userid`, ') has been successfully registered.') AS `message`,
    'REGISTRATION' AS `ref_id`,
    0 AS `is_read`,
    CASE 
        WHEN u.`joining_date` > '2000-01-01' THEN CONCAT(u.`joining_date`, ' 12:00:00')
        ELSE NOW()
    END AS `created_at`
FROM `user` u
WHERE u.`userid` NOT LIKE 'TEST%' AND u.`userid` NOT LIKE 'TST%'
  AND NOT EXISTS (
      SELECT 1 FROM `tbl_user_notifications` n 
      WHERE (n.`user_id` = u.`userid` OR n.`user_id` = CONCAT('AN', u.`userid`)) 
        AND n.`ref_id` = 'REGISTRATION'
  );

-- 2. Backfill Verified KYC notifications
INSERT INTO `tbl_user_notifications` (`user_id`, `type`, `title`, `message`, `ref_id`, `is_read`, `created_at`)
SELECT 
    u.`userid` AS `user_id`,
    'KYC' AS `type`,
    'KYC Verification Approved' AS `title`,
    CONCAT('Congratulations ', TRIM(COALESCE(u.`name`, 'Member')), ', your KYC verification has been reviewed and approved successfully.') AS `message`,
    'KYC_VERIFIED' AS `ref_id`,
    0 AS `is_read`,
    NOW() AS `created_at`
FROM `user` u
WHERE u.`userid` NOT LIKE 'TEST%' AND u.`userid` NOT LIKE 'TST%'
  AND u.`kyc` = 2
  AND NOT EXISTS (
      SELECT 1 FROM `tbl_user_notifications` n 
      WHERE (n.`user_id` = u.`userid` OR n.`user_id` = CONCAT('AN', u.`userid`)) 
        AND n.`ref_id` = 'KYC_VERIFIED'
  );

-- 3. Backfill Historical Package Investment notifications
INSERT INTO `tbl_user_notifications` (`user_id`, `type`, `title`, `message`, `ref_id`, `is_read`, `created_at`)
SELECT 
    t.`user_id` AS `user_id`,
    'INVESTMENT' AS `type`,
    'Package Investment Activated' AS `title`,
    CONCAT('Your investment of $', FORMAT(t.`amount` / 90.0, 2), ' in Basic Package has been recorded and activated successfully.') AS `message`,
    CONCAT('TXN_', t.`id`) AS `ref_id`,
    0 AS `is_read`,
    CONCAT(t.`created_date`, ' ', t.`time`) AS `created_at`
FROM `tbl_transaction` t
WHERE t.`user_id` NOT LIKE 'TEST%' AND t.`user_id` NOT LIKE 'TST%'
  AND t.`subject` LIKE '%Package Investment%'
  AND NOT EXISTS (
      SELECT 1 FROM `tbl_user_notifications` n 
      WHERE (n.`user_id` = t.`user_id` OR n.`user_id` = CONCAT('AN', t.`user_id`)) 
        AND n.`ref_id` = CONCAT('TXN_', t.`id`)
  );
