-- Migration: 0004_profit_sharing_idempotency.sql
-- Description: Add source_investment_id, closing_month, level_num columns and composite unique key for Profit Sharing idempotency.

ALTER TABLE `tbl_daily_levelinc`
  ADD COLUMN `source_investment_id` INT NULL AFTER `user_id`,
  ADD COLUMN `closing_month` VARCHAR(20) NULL AFTER `source_investment_id`,
  ADD COLUMN `level_num` TINYINT NULL AFTER `closing_month`;

ALTER TABLE `tbl_daily_levelinc`
  ADD UNIQUE KEY `unique_profit_sharing_event` (`source_investment_id`, `closing_month`, `user_id`, `level_num`);
