-- =====================================================================
--  Migration 003: Volunteer form fields
--  Adds split name and consent-timestamp columns to `volunteers`, same
--  pattern as migration 002 for members: `name` keeps being populated
--  (from the split fields) so the existing admin list needs no changes.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE volunteers
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name,
    ADD COLUMN IF NOT EXISTS consent_accepted_at DATETIME DEFAULT NULL AFTER availability;

-- Backfill first_name/surname for existing rows from the old single `name` column.
UPDATE volunteers
SET first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)),
    surname = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
WHERE first_name = '' AND name IS NOT NULL AND name != '';
UPDATE volunteers SET surname = first_name WHERE surname = '' AND first_name != '';
-- ======================= End of migration ==============================
