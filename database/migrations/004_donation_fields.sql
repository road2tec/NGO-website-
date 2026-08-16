-- =====================================================================
--  Migration 004: Donation page overhaul support
--  - donation_amount_options: admin-managed preset donation amount cards
--  - donations: split donor name (first/middle/surname), same pattern as
--    members/volunteers; `donor_name` keeps being populated so existing
--    admin donations list and receipts need no changes.
--  - settings: structured bank-transfer fields (for individual copy
--    buttons on Account Number/IFSC - the old `donate_bank` free-text
--    blob can't support that) seeded from the existing demo values, plus
--    homepage crowdfunding banner title/text/campaign override.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS donation_amount_options (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  amount DECIMAL(10,2) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uniq_amount (amount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO donation_amount_options (amount, sort_order) VALUES
  (500, 1), (1000, 2), (2500, 3), (5000, 4);

ALTER TABLE donations
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER donor_name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name;

UPDATE donations
SET first_name = TRIM(SUBSTRING_INDEX(donor_name, ' ', 1)),
    surname = TRIM(SUBSTRING(donor_name, LENGTH(SUBSTRING_INDEX(donor_name, ' ', 1)) + 1))
WHERE first_name = '' AND donor_name IS NOT NULL AND donor_name != '';
UPDATE donations SET surname = first_name WHERE surname = '' AND first_name != '';

INSERT INTO settings (setting_key, setting_value) VALUES
  ('bank_account_name', 'Seva Sankalp Foundation'),
  ('bank_name', 'State Bank of India'),
  ('bank_account_number', '00000011112222'),
  ('bank_ifsc', 'SBIN0001234'),
  ('bank_branch', 'Karve Road, Pune'),
  ('crowdfunding_banner_title', 'Support Us: your ₹500 can fund a month of school supplies'),
  ('crowdfunding_banner_text', '100% of your donation is tracked to a project. Tax exemption available under 80G.'),
  ('crowdfunding_banner_campaign_id', '')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
-- ======================= End of migration ==============================
