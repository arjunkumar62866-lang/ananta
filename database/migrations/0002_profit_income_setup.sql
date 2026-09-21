-- Migration: 0002_profit_income_setup.sql
-- Description: Add profit_income_wallet to user table and create tbl_monthly_closing table.

ALTER TABLE `user` ADD `profit_income_wallet` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `pending_geninc`;

CREATE TABLE IF NOT EXISTS `tbl_monthly_closing` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `closing_month` VARCHAR(7) NOT NULL,
  `closing_date` DATE NOT NULL,
  `profit_percentage` DECIMAL(5,2) NOT NULL,
  `total_eligible_investment` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_profit_paid` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `eligible_investment_count` INT NOT NULL DEFAULT 0,
  `eligible_user_count` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'COMPLETED',
  `processed_by` VARCHAR(100) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_closing_month` (`closing_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
