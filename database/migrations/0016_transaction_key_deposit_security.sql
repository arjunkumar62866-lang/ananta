-- database/migrations/0016_transaction_key_deposit_security.sql
-- Additive Schema Updates for Requirement #23 Transaction Key, Deposit & Security

SET @dbname = DATABASE();

-- 1. Modify txn_pass column on user table to VARCHAR(255) so password_hash (BCRYPT 60 chars) fits
ALTER TABLE user MODIFY COLUMN txn_pass VARCHAR(255) NULL DEFAULT NULL;

-- 2. Create tbl_inr_deposits table for deposit requests and proof tracking
CREATE TABLE IF NOT EXISTS tbl_inr_deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deposit_ref VARCHAR(100) NOT NULL UNIQUE,
    user_id VARCHAR(100) NOT NULL,
    amount_inr DECIMAL(15,2) NOT NULL,
    amount_usd DECIMAL(15,2) NOT NULL,
    deposit_method VARCHAR(50) NOT NULL DEFAULT 'INR',
    payment_ref VARCHAR(255) NULL,
    proof_file VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    rejection_reason TEXT NULL,
    reviewed_by VARCHAR(100) NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dep_user (user_id),
    INDEX idx_dep_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- 3. Create tbl_user_welcome table for tracking one-time welcome messages
CREATE TABLE IF NOT EXISTS tbl_user_welcome (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL DEFAULT 'Welcome to Ananta Fintech',
    message TEXT NOT NULL,
    is_seen TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_welcome_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
