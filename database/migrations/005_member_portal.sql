-- =====================================================================
--  Migration 005: Member portal support
--  - password_reset_tokens: secure, single-use, expiring reset tokens
--    for the member "forgot password" flow (no third-party service).
--  - member_notifications: admin -> member notifications shown on the
--    member dashboard.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reset_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_token_hash (token_hash),
  KEY idx_reset_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS member_notifications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  KEY idx_notif_member (member_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ======================= End of migration ==============================
