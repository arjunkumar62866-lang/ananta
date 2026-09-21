-- Migration: 0006_direct_bonus_admin_management.sql
-- Description: Create tbl_direct_bonus_admin_audit table for logging admin Direct Bonus financial adjustments and admin actions.

CREATE TABLE IF NOT EXISTS `tbl_direct_bonus_admin_audit` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` VARCHAR(100) NOT NULL,
  `action` VARCHAR(50) NOT NULL, -- CREDIT, DEBIT, ADJUSTMENT, STATUS_CHANGE
  `user_id` VARCHAR(100) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `wallet` VARCHAR(50) NOT NULL DEFAULT 'direct_bonus_wallet',
  `previous_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `new_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reason` TEXT NOT NULL,
  `reference` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
