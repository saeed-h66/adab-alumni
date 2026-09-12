-- Adab Alumni / انجمن دانش‌آموختگان بعثت
-- MySQL 8+ schema for the production backend

CREATE DATABASE IF NOT EXISTS adab_alumni
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adab_alumni;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mobile VARCHAR(20) NOT NULL UNIQUE,
  full_name VARCHAR(160) NULL,
  national_code VARCHAR(20) NULL,
  graduation_year SMALLINT NULL,
  field_of_study VARCHAR(160) NULL,
  school VARCHAR(160) NULL,
  city VARCHAR(120) NULL,
  email VARCHAR(190) NULL,
  avatar_path VARCHAR(255) NULL,
  role ENUM('member','admin') NOT NULL DEFAULT 'member',
  membership_status ENUM('incomplete','pending','approved','revision','rejected') NOT NULL DEFAULT 'incomplete',
  membership_code VARCHAR(40) NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_status (membership_status),
  INDEX idx_users_school (school),
  INDEX idx_users_city (city)
);

CREATE TABLE otp_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mobile VARCHAR(20) NOT NULL,
  code_hash VARCHAR(255) NOT NULL,
  purpose ENUM('login','signup') NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  expires_at DATETIME NOT NULL,
  consumed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_otp_mobile_created (mobile, created_at),
  INDEX idx_otp_expiry (expires_at)
);

CREATE TABLE membership_reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  admin_id BIGINT UNSIGNED NOT NULL,
  decision ENUM('approved','revision','rejected') NOT NULL,
  note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_reviews_admin FOREIGN KEY (admin_id) REFERENCES users(id),
  INDEX idx_reviews_user (user_id)
);

CREATE TABLE sessions (
  id CHAR(64) PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_sessions_expiry (expires_at)
);

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id BIGINT UNSIGNED NULL,
  metadata JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_audit_action_created (action, created_at)
);
