-- =====================================================================
--  Migration 009: Admin-editable legal pages
--  Privacy Policy, Terms & Conditions, Refund Policy and Disclaimer move
--  from static PHP files into the existing about_sections table (same
--  slug + title + content structure already used for About Us content),
--  so they're editable from Admin -> About Sections without a new table.
--  Additive only, safe to re-run (slug is UNIQUE, so INSERT IGNORE just
--  skips rows that already exist rather than erroring or overwriting any
--  edits an admin has already made).
-- =====================================================================
SET NAMES utf8mb4;

INSERT IGNORE INTO about_sections (slug, title, content, sort_order) VALUES
  ('privacy', 'Privacy Policy',
   'This policy explains how Seva Sankalp Foundation collects, uses and protects information submitted through this website, including membership applications, donations, event registrations, volunteer applications and contact forms.\n\nInformation we collect: Name, email, phone, address and other details you voluntarily provide through our forms. Payment details for donations are handled by the respective payment provider and are not stored on our servers.\n\nHow we use it: To process membership applications, issue ID cards and certificates, record donations for 80G receipts, respond to enquiries, and share updates you opt into (newsletter).\n\nData sharing: We do not sell or rent personal data. Data may be shared with government authorities where legally required, or with auditors for statutory compliance.\n\nYour rights: You may request access to, correction of, or deletion of your data by writing to us using the contact details on this website.',
   100),
  ('terms', 'Terms & Conditions',
   'By using this website you agree to the following terms.\n\nUse of content: All text, images and media on this site belong to Seva Sankalp Foundation unless otherwise credited, and may not be reproduced commercially without written permission.\n\nMembership & donations: Membership applications are subject to admin approval. Donations are voluntary contributions to registered charitable programs and are non-refundable once utilised, except as described in our Refund Policy.\n\nAccuracy of information: We take reasonable care to keep project, event and financial information accurate but do not guarantee it is free of error at all times.\n\nGoverning law: These terms are governed by the laws of India, with courts in Pune, Maharashtra having jurisdiction.',
   101),
  ('refund', 'Refund Policy',
   'Donations made to Seva Sankalp Foundation are voluntary contributions towards our charitable programs and are generally non-refundable, since funds are often allocated to ongoing project activity soon after receipt.\n\nGenuine errors: If you made a donation in error (duplicate transaction, incorrect amount), please contact us within 7 days using the receipt number from your donation. Verified errors are refunded to the original payment source within 10-15 working days.\n\nMembership fees: Membership fees are refunded in full if an application is rejected by the admin. Fees are non-refundable once membership is approved and activated.',
   102),
  ('disclaimer', 'Disclaimer',
   'The information on this website is provided in good faith for general informational purposes about the activities of Seva Sankalp Foundation. We make no warranties about the completeness or reliability of this information.\n\nPhotographs of beneficiaries are used with appropriate consent for the purpose of program transparency and reporting. External links (social media, partner organisations) are provided for convenience; we are not responsible for their content.\n\nDonations may be eligible for tax exemption under applicable law. Please consult your tax advisor regarding eligibility for exemption.',
   103);
-- ======================= End of migration ==============================
