-- =====================================================================
--  Migration 012: Production launch cleanup
--  Replaces the demo/placeholder org identity ("Seva Sankalp Foundation")
--  with the real org name, and removes fabricated demo content (fake
--  board members, fake bank details, fake events/campaigns/news/
--  testimonials/sponsors/documents/gallery items etc.) that must not be
--  shown on the live public site. Table structures are untouched -
--  content tables are left empty so the admin panel shows genuine empty
--  states, to be filled in with real content after launch.
--  Safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

-- ---------------------------------------------------------------
-- Org identity / contact / SEO / financial settings
-- ---------------------------------------------------------------
UPDATE settings SET setting_value = 'Swajin Welfare Foundation' WHERE setting_key = 'site_name';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'site_email';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'site_phone';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'site_whatsapp';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'site_address';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'map_embed';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'facebook_url';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'instagram_url';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'twitter_url';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'youtube_url';
UPDATE settings SET setting_value = 'Swajin Welfare Foundation' WHERE setting_key = 'seo_title';
UPDATE settings SET setting_value = 'Swajin Welfare Foundation works to uplift communities through education, health and livelihood programs.' WHERE setting_key = 'seo_description';
UPDATE settings SET setting_value = 'Swajin Welfare Foundation, NGO, charity, donate' WHERE setting_key = 'seo_keywords';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'donate_upi';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'donate_bank';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'registration_no';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'pan_80g';
UPDATE settings SET setting_value = '0' WHERE setting_key = 'stat_members';
UPDATE settings SET setting_value = '0' WHERE setting_key = 'stat_projects';
UPDATE settings SET setting_value = '0' WHERE setting_key = 'stat_beneficiaries';
UPDATE settings SET setting_value = '0' WHERE setting_key = 'stat_villages';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'membership_fee_note';
UPDATE settings SET setting_value = ''  WHERE setting_key = 'announcement';

-- Structured bank-transfer fields (migration 004) - fake demo account
UPDATE settings SET setting_value = '' WHERE setting_key = 'bank_account_name';
UPDATE settings SET setting_value = '' WHERE setting_key = 'bank_name';
UPDATE settings SET setting_value = '' WHERE setting_key = 'bank_account_number';
UPDATE settings SET setting_value = '' WHERE setting_key = 'bank_ifsc';
UPDATE settings SET setting_value = '' WHERE setting_key = 'bank_branch';
UPDATE settings SET setting_value = '' WHERE setting_key = 'crowdfunding_banner_title';
UPDATE settings SET setting_value = '' WHERE setting_key = 'crowdfunding_banner_text';

-- ---------------------------------------------------------------
-- Legal pages (migration 009): swap the demo org name only, keep the
-- real legal boilerplate text as a starting draft for the client to review
-- ---------------------------------------------------------------
UPDATE about_sections
SET content = REPLACE(content, 'Seva Sankalp Foundation', 'Swajin Welfare Foundation')
WHERE slug IN ('privacy-policy', 'terms-conditions', 'refund-policy', 'disclaimer');

-- ---------------------------------------------------------------
-- About: keep Who We Are / Mission / Vision as short, honest, generic
-- copy with no invented facts; drop History and Legal Information, which
-- were fabricated founding dates / milestones / registration numbers
-- ---------------------------------------------------------------
UPDATE about_sections SET content = 'Swajin Welfare Foundation works with communities to improve access to education, healthcare and sustainable livelihoods.' WHERE slug = 'who-we-are';
UPDATE about_sections SET content = 'To enable underserved communities to access quality education, healthcare and sustainable livelihoods, through programs designed and delivered with the community, not merely for it.' WHERE slug = 'mission';
UPDATE about_sections SET content = 'A society where every child completes school, every family can reach basic healthcare, and every community can sustain its own livelihood and environment.' WHERE slug = 'vision';
DELETE FROM about_sections WHERE slug IN ('history', 'legal');

-- ---------------------------------------------------------------
-- Homepage hero: replace the three demo banners (with fabricated
-- membership/village/tree counts) with one honest, generic welcome
-- banner using no invented statistics
-- ---------------------------------------------------------------
DELETE FROM banners;
INSERT INTO banners (title, subtitle, button_text, button_link, sort_order) VALUES
  ('Every hand can lift a life', 'Join us in building stronger, healthier communities across Maharashtra.', 'Become a member', 'membership/apply', 1);

-- ---------------------------------------------------------------
-- Remove fabricated demo content: fake named board/team members,
-- fake awards, fake certificates, fake projects, fake dated events
-- (which could mislead people into expecting a real event), fake
-- crowdfunding campaigns with fake raised amounts, fake CSR sponsors,
-- fake documents with no real file attached, fake testimonials from
-- fictional people, and fake news items
-- ---------------------------------------------------------------
DELETE FROM people;
DELETE FROM achievements;
DELETE FROM org_certificates;
DELETE FROM project_images;
DELETE FROM projects;
DELETE FROM event_registrations;
DELETE FROM events;
DELETE FROM gallery_items;
DELETE FROM campaigns;
DELETE FROM sponsors;
DELETE FROM documents;
DELETE FROM testimonials;
DELETE FROM news;
-- ======================= End of migration ==============================
