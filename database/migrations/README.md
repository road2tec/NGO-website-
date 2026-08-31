# Migrations

Incremental changes applied **on top of** `database/ngo_website.sql`
(the base schema + seed data). Run in filename order after the base
import; each file is idempotent (`CREATE TABLE IF NOT EXISTS`,
`INSERT ... ON DUPLICATE KEY UPDATE`) so re-running it is harmless.

Apply via cPanel/hPanel → phpMyAdmin → select the database → Import →
upload the file, or:
```
mysql -u user -p database_name < database/migrations/001_add_locations.sql
```

**Before running against a live/production database, take a full
database backup first.**

| File | Adds |
|---|---|
| `001_add_locations.sql` | `states`, `districts`, `talukas` tables + complete India states/UTs + districts + Maharashtra talukas (see main report for sourcing and current coverage). |
| `006_donation_certificates.sql` | `donations.cert_code` / `cert_sent_at` - QR-verifiable certificate code and last-sent timestamp for the donation certificate/receipt email. |
| `007_homepage_buttons.sql` | `homepage_buttons` table - admin-managed homepage buttons linking to internal or external URLs. |
| `008_careers.sql` | `job_categories`, `job_subcategories`, `jobs`, `job_applications` - the Careers portal. |
| `009_legal_pages.sql` | Seeds Privacy/Terms/Refund/Disclaimer into `about_sections` so they're admin-editable there (Admin -> About Sections). |
| `010_donation_receipt_fields.sql` | `donations.address` / `cheque_no` / `donor_bank_name` + adds 'cheque' as a payment method - needed for the official receipt format. |
| `011_certificate_branding.sql` | Seeds default settings for certificate/receipt branding (logo, signature, PAN, 80G URN, signatory) and the ID card back page. |
