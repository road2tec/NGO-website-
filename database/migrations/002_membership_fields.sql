-- =====================================================================
--  Migration 002: Membership form fields
--  Adds split name, location (state/district/taluka), pincode, ID-proof
--  and terms-consent columns to `members`. All additive and nullable so
--  existing member rows and the existing `name`/`aadhar` columns are
--  left untouched - `name` keeps being populated (from the split fields)
--  so the member directory, ID card and dashboard views need no changes.
--
--  Uses `ADD COLUMN IF NOT EXISTS` (MySQL 8.0.29+ / MariaDB 10.0.2+) so
--  it is safe to re-run. If your database is older than that, run the
--  ALTER TABLE statements manually with the IF NOT EXISTS clauses removed.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE members
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name,
    ADD COLUMN IF NOT EXISTS state_id INT UNSIGNED DEFAULT NULL AFTER surname,
    ADD COLUMN IF NOT EXISTS district_id INT UNSIGNED DEFAULT NULL AFTER state_id,
    ADD COLUMN IF NOT EXISTS district_other VARCHAR(100) DEFAULT NULL AFTER district_id,
    ADD COLUMN IF NOT EXISTS taluka_id INT UNSIGNED DEFAULT NULL AFTER district_other,
    ADD COLUMN IF NOT EXISTS taluka_other VARCHAR(100) DEFAULT NULL AFTER taluka_id,
    ADD COLUMN IF NOT EXISTS pincode VARCHAR(6) DEFAULT NULL AFTER taluka_other,
    ADD COLUMN IF NOT EXISTS id_proof_type ENUM('aadhaar','voter_id','passport','driving_licence','pan_card') DEFAULT NULL AFTER aadhar,
    ADD COLUMN IF NOT EXISTS id_proof_number VARCHAR(50) DEFAULT NULL AFTER id_proof_type,
    ADD COLUMN IF NOT EXISTS id_proof_file VARCHAR(255) DEFAULT NULL AFTER id_proof_number,
    ADD COLUMN IF NOT EXISTS terms_accepted_at DATETIME DEFAULT NULL AFTER id_proof_file;

-- Foreign keys: separate statements, guarded, so a partial re-run never
-- errors on "duplicate key name". ON DELETE SET NULL so deactivating or
-- removing a location can never delete a member record.
SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_state');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_state FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_district');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_district FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_taluka');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_taluka FOREIGN KEY (taluka_id) REFERENCES talukas(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill first_name/surname for existing rows from the old single `name`
-- column, so the new required fields are never blank for pre-existing members.
UPDATE members
SET first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)),
    surname = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
WHERE first_name = '' AND name IS NOT NULL AND name != '';
UPDATE members SET surname = first_name WHERE surname = '' AND first_name != '';

INSERT INTO settings (setting_key, setting_value) VALUES ('member_no_prefix', 'MEM')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
-- ======================= End of migration ==============================
