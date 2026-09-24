-- Migration 0019: Login & Registration Access Control (Requirement #24)
-- Creates isolated configuration and admin authentication tables for Login & Registration Control

CREATE TABLE IF NOT EXISTS `tbl_login_registration_control` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `status` ENUM('ON', 'OFF') NOT NULL DEFAULT 'ON',
    `message_type` ENUM('WARNING', 'ERROR') NOT NULL DEFAULT 'WARNING',
    `message_text` TEXT NOT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_login_reg_control_auth` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default control state row if not exists
INSERT INTO `tbl_login_registration_control` (`id`, `status`, `message_type`, `message_text`)
SELECT 1, 'ON', 'WARNING', 'Website login and registration service notice: Please note maintenance is scheduled.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tbl_login_registration_control` WHERE `id` = 1);

-- Seed default auth credentials for #sumit7366 if not exists (using secure password_hash for #sumit7366)
INSERT INTO `tbl_login_reg_control_auth` (`id`, `admin_id`, `password_hash`)
SELECT 1, '#sumit7366', '$2y$12$hwZA9YMdjPWMUF9CxwcpluWD4n2xl2KsyEnjR83tPIHBhN26f86F2'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tbl_login_reg_control_auth` WHERE `admin_id` = '#sumit7366');
