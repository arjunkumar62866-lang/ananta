-- Migration: 0021_fix_advance_package_max_limit.sql
-- Description: Ensure ADVANCE package max_investment_usd is NULL (No Limit) in tbl_ananta_package_config

UPDATE tbl_ananta_package_config 
SET max_investment_usd = NULL 
WHERE package_id = 'ADVANCE';
