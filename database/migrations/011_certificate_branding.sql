-- =====================================================================
--  Migration 011: Certificate/receipt branding + ID card back-page defaults
--  New settings (Admin -> Website Settings): org_legal_status, org_pan,
--  org_80g_urn, org_website, cert_signatory_name, cert_signatory_designation,
--  membership_benefits, org_logo, cert_signature_image (the last two are
--  image uploads, set from the settings page, not seeded here).
--  Additive only, safe to re-run - only fills in defaults the admin hasn't
--  already set (setting_key is UNIQUE, INSERT IGNORE skips existing rows).
-- =====================================================================
SET NAMES utf8mb4;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
  ('org_legal_status', 'Section 8 Company / Registered NGO'),
  ('org_pan', ''),
  ('org_80g_urn', ''),
  ('org_website', ''),
  ('cert_signatory_name', ''),
  ('cert_signatory_designation', ''),
  ('membership_benefits', 'Participation in social and educational initiatives\nOpportunity to participate in training programs and workshops\nOpportunity to serve as a volunteer in the organisation''s social initiatives\nVarious membership benefits as per the rules and policies of the Foundation');
-- ======================= End of migration ==============================
