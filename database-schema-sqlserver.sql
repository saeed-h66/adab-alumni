-- Adab Alumni / Microsoft SQL Server 2022
-- Run this script while the adab_alumni database is selected.

IF OBJECT_ID('dbo.users', 'U') IS NULL
BEGIN
CREATE TABLE dbo.users (
  id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
  mobile NVARCHAR(20) NOT NULL UNIQUE,
  full_name NVARCHAR(160) NULL,
  national_code NVARCHAR(20) NULL,
  graduation_year SMALLINT NULL,
  field_of_study NVARCHAR(160) NULL,
  school NVARCHAR(160) NULL,
  city NVARCHAR(120) NULL,
  email NVARCHAR(190) NULL,
  avatar_path NVARCHAR(255) NULL,
  role NVARCHAR(20) NOT NULL CONSTRAINT df_users_role DEFAULT N'member',
  membership_status NVARCHAR(20) NOT NULL CONSTRAINT df_users_status DEFAULT N'incomplete',
  membership_code NVARCHAR(40) NULL UNIQUE,
  created_at DATETIME2 NOT NULL CONSTRAINT df_users_created DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 NOT NULL CONSTRAINT df_users_updated DEFAULT SYSUTCDATETIME(),
  CONSTRAINT ck_users_role CHECK (role IN (N'member', N'admin')),
  CONSTRAINT ck_users_status CHECK (membership_status IN (N'incomplete',N'pending',N'approved',N'revision',N'rejected'))
);
CREATE INDEX idx_users_status ON dbo.users(membership_status);
CREATE INDEX idx_users_school ON dbo.users(school);
CREATE INDEX idx_users_city ON dbo.users(city);
END;

IF OBJECT_ID('dbo.otp_requests', 'U') IS NULL
BEGIN
CREATE TABLE dbo.otp_requests (
  id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
  mobile NVARCHAR(20) NOT NULL,
  code_hash NVARCHAR(255) NOT NULL,
  purpose NVARCHAR(20) NOT NULL,
  attempts TINYINT NOT NULL CONSTRAINT df_otp_attempts DEFAULT 0,
  expires_at DATETIME2 NOT NULL,
  consumed_at DATETIME2 NULL,
  created_at DATETIME2 NOT NULL CONSTRAINT df_otp_created DEFAULT SYSUTCDATETIME(),
  CONSTRAINT ck_otp_purpose CHECK (purpose IN (N'login', N'signup'))
);
CREATE INDEX idx_otp_mobile_created ON dbo.otp_requests(mobile, created_at);
CREATE INDEX idx_otp_expiry ON dbo.otp_requests(expires_at);
END;

IF OBJECT_ID('dbo.membership_reviews', 'U') IS NULL
BEGIN
CREATE TABLE dbo.membership_reviews (
  id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
  user_id BIGINT NOT NULL,
  admin_id BIGINT NOT NULL,
  decision NVARCHAR(20) NOT NULL,
  note NVARCHAR(MAX) NULL,
  created_at DATETIME2 NOT NULL CONSTRAINT df_reviews_created DEFAULT SYSUTCDATETIME(),
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES dbo.users(id),
  CONSTRAINT fk_reviews_admin FOREIGN KEY (admin_id) REFERENCES dbo.users(id),
  CONSTRAINT ck_reviews_decision CHECK (decision IN (N'approved',N'revision',N'rejected'))
);
CREATE INDEX idx_reviews_user ON dbo.membership_reviews(user_id);
END;

IF OBJECT_ID('dbo.sessions', 'U') IS NULL
BEGIN
CREATE TABLE dbo.sessions (
  id CHAR(64) NOT NULL PRIMARY KEY,
  user_id BIGINT NOT NULL,
  expires_at DATETIME2 NOT NULL,
  created_at DATETIME2 NOT NULL CONSTRAINT df_sessions_created DEFAULT SYSUTCDATETIME(),
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
);
CREATE INDEX idx_sessions_expiry ON dbo.sessions(expires_at);
END;

IF OBJECT_ID('dbo.audit_logs', 'U') IS NULL
BEGIN
CREATE TABLE dbo.audit_logs (
  id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
  user_id BIGINT NULL,
  action NVARCHAR(100) NOT NULL,
  entity_type NVARCHAR(80) NULL,
  entity_id BIGINT NULL,
  metadata NVARCHAR(MAX) NULL,
  created_at DATETIME2 NOT NULL CONSTRAINT df_audit_created DEFAULT SYSUTCDATETIME(),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE SET NULL
);
CREATE INDEX idx_audit_action_created ON dbo.audit_logs(action, created_at);
END;
