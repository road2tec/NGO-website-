# NGO Management Website

A complete, production-ready NGO website in **PHP 8.3 + MySQL 8 + Bootstrap 5
+ vanilla JS** — no framework, no Composer, no Node.js. Built to be zipped
and uploaded straight to cPanel shared hosting.

See also: `SRS.md` (requirements), `INSTALL.md` (local/cPanel setup),
`DEPLOYMENT.md` (going live checklist).

## Design system

- **Palette** — white base, civic blue (`#14549C`), marigold orange
  (`#F4772E`), field green (`#2E9E5C`).
- **Type** — Bricolage Grotesque (display) + Mulish (body).
- **Signature element** — the "seva band", a slim blue→orange→green ribbon
  that marks section headings, card tops, dropdown menus and the footer,
  tying every page back to the three-colour brief without relying on stock
  gradients.
- Inspired by the general warmth and clarity of jvahini.in and zuards.org
  (structure/tone only) — palette, type pairing, and the seva-band motif are
  original to this build, not copied from either reference.

## Folder structure

```
/                        front controller (index.php), .htaccess, robots.txt, sitemap.php
/config/config.php        DB credentials, base URL, session bootstrap  <-- edit this after upload
/app
  /core                   Database.php, Controller.php
  /controllers            one controller per nav area (Home, About, Membership, ...)
  /helpers/functions.php   escaping, CSRF, uploads, settings, captcha, pagination
  /views                   one folder per controller + /layouts (header/footer)
/admin
  index.php               admin router (?page=...)
  login.php / logout.php
  /includes               auth.php, CrudHelper.php (generic list/add/edit/delete), header/footer
  /modules                one file per admin section
/assets
  /css  style.css (site) + admin.css (admin panel)
  /js   main.js (AOS/Swiper init, counters, lightbox, gallery filter)
  /images
/uploads                  member photos, project/event/gallery images, documents, certificates...
/database/ngo_website.sql  full schema + seed data
```

## Tech stack

- PHP 8.3, PDO (prepared statements only), session-based auth
- MySQL 8 (InnoDB, foreign keys, `utf8mb4`)
- Bootstrap 5, vanilla JS, AOS (scroll animation), Swiper (sliders),
  Font Awesome — all from CDN, no build step
- Apache + `.htaccess` (mod_rewrite for friendly URLs, mod_deflate/expires
  for performance)

## Key features

- Full navbar per brief (Home | Membership▾ | Projects▾ | Activities▾ |
  Gallery | Donate▾ | About Us▾ | Contact Us), sticky Donate Now button,
  floating donate button, footer with Privacy/Terms/social links/Google
  Translate (EN/HI/MR + more).
- Membership application → admin approval → member number + printable
  QR-coded ID card; public member directory.
- Projects (5 types), Activities/Events with registration, Gallery with
  albums + YouTube embeds + lightbox.
- Donate Now (UPI/bank, pledge form) + Crowdfunding campaigns with progress
  bars + Sponsorship program page.
- Documents library with search + download counter.
- Event Participation Certificate: name + email + Event ID → printable,
  QR-verifiable certificate.
- Enquiry form directly above the footer on the homepage, plus a full
  Contact page.
- Full custom admin panel (see `SRS.md` §4 for the complete module list).
- Security: CSRF tokens, PDO prepared statements throughout, bcrypt
  password hashing (with automatic upgrade of the seeded plaintext admin
  password on first login), session timeout, math captcha, MIME-checked
  uploads.
- SEO: meta/OG/Twitter tags, Schema.org `NGO` markup, `sitemap.php`,
  `robots.txt`, friendly URLs.

## Default admin login

```
URL:      /admin/login.php
Username: admin
Password: password123
```

The seed password is stored as plain text in `admins.password` purely so
the very first login can succeed; `admin/login.php` automatically replaces
it with a bcrypt hash the moment you log in. **Change this password
immediately** from Admin → Change Password after your first login.

## Quick start

1. Import `database/ngo_website.sql` into a MySQL database.
2. Edit `config/config.php` — set `DB_NAME`, `DB_USER`, `DB_PASS`,
   `BASE_URL`.
3. Point your web server's document root at this folder (or upload
   everything to `public_html` / a subfolder on cPanel).
4. Visit the site, then log into `/admin/` and change the password.

Full instructions: `INSTALL.md` and `DEPLOYMENT.md`.
