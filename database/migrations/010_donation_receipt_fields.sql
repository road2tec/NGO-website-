-- =====================================================================
--  Migration 010: Fields needed for the official donation receipt format
--  - donations.address: donor's postal address (required on the receipt,
--    wasn't previously collected).
--  - donations.cheque_no / donor_bank_name: only used when method='cheque'.
--  - method ENUM gains 'cheque' as a valid payment mode.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE donations
    ADD COLUMN IF NOT EXISTS address VARCHAR(255) DEFAULT NULL AFTER phone,
    ADD COLUMN IF NOT EXISTS cheque_no VARCHAR(50) DEFAULT NULL AFTER txn_ref,
    ADD COLUMN IF NOT EXISTS donor_bank_name VARCHAR(100) DEFAULT NULL AFTER cheque_no;

ALTER TABLE donations MODIFY COLUMN method ENUM('upi','bank','cash','online','cheque') NOT NULL DEFAULT 'upi';
-- ======================= End of migration ==============================
