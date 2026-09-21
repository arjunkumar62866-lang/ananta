-- Migration: 0003_profit_sharing_setup.sql
-- Description: Add profit_sharing_wallet to user table for dedicated Profit Sharing storage.

ALTER TABLE `user` ADD `profit_sharing_wallet` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `profit_income_wallet`;
