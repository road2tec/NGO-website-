-- =====================================================================
--  Migration 007: Homepage custom buttons
--  Admin-managed buttons shown on the homepage that can link either to an
--  internal page or an external site (opens in a new tab automatically -
--  see is_external_url()/link_target_attrs() in app/helpers/functions.php).
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS homepage_buttons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(255) NOT NULL,
  style ENUM('primary','outline') NOT NULL DEFAULT 'outline',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ======================= End of migration ==============================
