-- =====================================================================
--  Migration 006: Donation certificate + receipt delivery
--  - donations: cert_code (public QR-verification code, same pattern as
--    event_registrations.cert_code) and cert_sent_at (when the admin last
--    emailed the certificate/receipt PDFs to the donor).
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE donations
    ADD COLUMN IF NOT EXISTS cert_code VARCHAR(20) DEFAULT NULL AFTER receipt_no,
    ADD COLUMN IF NOT EXISTS cert_sent_at DATETIME DEFAULT NULL AFTER cert_code,
    ADD INDEX IF NOT EXISTS idx_donations_cert_code (cert_code);
-- ======================= End of migration ==============================
