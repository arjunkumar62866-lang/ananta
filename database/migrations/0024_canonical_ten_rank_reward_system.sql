-- Migration: 0024_canonical_ten_rank_reward_system.sql
-- Description: Align tbl_vip_level_config with Canonical 10-Rank Specification (PRIME to ANANT)
-- Author: Ananta Core Engineering
-- Date: 2026-10-12

-- 1. Update tbl_vip_level_config with canonical names, business thresholds, rewards, and repeat requirements

UPDATE `tbl_vip_level_config`
SET `name` = 'PRIME',
    `req_left_ids` = 30,
    `req_right_ids` = 30,
    `req_left_business` = 4000.00,
    `req_right_business` = 4000.00,
    `reward_amount` = 200.00,
    `vip_income_rate` = 0.50,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 4000.00
WHERE `level_id` = 1;

UPDATE `tbl_vip_level_config`
SET `name` = 'PRO',
    `req_left_ids` = 60,
    `req_right_ids` = 60,
    `req_left_business` = 10000.00,
    `req_right_business` = 10000.00,
    `reward_amount` = 500.00,
    `vip_income_rate` = 1.00,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 10000.00
WHERE `level_id` = 2;

UPDATE `tbl_vip_level_config`
SET `name` = 'ELITE',
    `req_left_ids` = 100,
    `req_right_ids` = 100,
    `req_left_business` = 16000.00,
    `req_right_business` = 16000.00,
    `reward_amount` = 800.00,
    `vip_income_rate` = 1.00,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 16000.00
WHERE `level_id` = 3;

UPDATE `tbl_vip_level_config`
SET `name` = 'CROWN',
    `req_left_ids` = 200,
    `req_right_ids` = 200,
    `req_left_business` = 30000.00,
    `req_right_business` = 30000.00,
    `reward_amount` = 2000.00,
    `vip_income_rate` = 1.00,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 30000.00
WHERE `level_id` = 4;

UPDATE `tbl_vip_level_config`
SET `name` = 'ROYAL',
    `req_left_ids` = 500,
    `req_right_ids` = 500,
    `req_left_business` = 100000.00,
    `req_right_business` = 100000.00,
    `reward_amount` = 4000.00,
    `vip_income_rate` = 1.00,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 5;

UPDATE `tbl_vip_level_config`
SET `name` = 'TITAN',
    `req_left_ids` = 1200,
    `req_right_ids` = 1200,
    `req_left_business` = 300000.00,
    `req_right_business` = 300000.00,
    `reward_amount` = 10000.00,
    `vip_income_rate` = 1.00,
    `has_turnover_share` = 0,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 6;

UPDATE `tbl_vip_level_config`
SET `name` = 'PEAK',
    `req_left_ids` = 5000,
    `req_right_ids` = 5000,
    `req_left_business` = 1000000.00,
    `req_right_business` = 1000000.00,
    `reward_amount` = 30000.00,
    `vip_income_rate` = 0.50,
    `has_turnover_share` = 1,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 7;

UPDATE `tbl_vip_level_config`
SET `name` = 'APEX',
    `req_left_ids` = 12000,
    `req_right_ids` = 12000,
    `req_left_business` = 2000000.00,
    `req_right_business` = 2000000.00,
    `reward_amount` = 70000.00,
    `vip_income_rate` = 0.50,
    `has_turnover_share` = 1,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 8;

UPDATE `tbl_vip_level_config`
SET `name` = 'ICON',
    `req_left_ids` = 20000,
    `req_right_ids` = 20000,
    `req_left_business` = 4000000.00,
    `req_right_business` = 4000000.00,
    `reward_amount` = 170000.00,
    `vip_income_rate` = 0.50,
    `has_turnover_share` = 1,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 9;

UPDATE `tbl_vip_level_config`
SET `name` = 'ANANT',
    `req_left_ids` = 50000,
    `req_right_ids` = 50000,
    `req_left_business` = 10000000.00,
    `req_right_business` = 10000000.00,
    `reward_amount` = 350000.00,
    `vip_income_rate` = 0.50,
    `has_turnover_share` = 1,
    `monthly_repeat_business` = 100000.00
WHERE `level_id` = 10;
