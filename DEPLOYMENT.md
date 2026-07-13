# Deployment Guide (cPanel go-live checklist)

## 1. Before uploading
- [ ] Replace all sample/placeholder content (banners, about text, board
      members, projects, events, gallery) via the admin panel once live,
      or directly in `database/ngo_website.sql` before import if you
      prefer to ship with your real content from day one.
- [ ] Update `database/ngo_website.sql` seed rows for `settings` (site
      name, email, phone, address, social links, UPI ID, bank details,
      registration numbers) — or edit them later from **Admin → Website
      Settings**.
- [ ] Decide your real domain / subfolder so `BASE_URL` can be set
      correctly on first upload.

## 2. Upload & database
- [ ] Zip the project **contents** (index.php, config/, app/, admin/,
      assets/, uploads/, database/, .htaccess, robots.txt, sitemap.php —
      not a wrapping folder) and upload via cPanel File Manager, or use
      cPanel's Git/SSH if available.
- [ ] Extract into `public_html` (or the correct addon/subdomain folder).
- [ ] Create the MySQL database + user in cPanel → MySQL Databases, grant
      **All Privileges**.
- [ ] Import `database/ngo_website.sql` via phpMyAdmin.
- [ ] Edit `config/config.php`: `DB_NAME`, `DB_USER`, `DB_PASS`,
      `BASE_URL`, and set `APP_ENV` to `'production'`.

## 3. SSL / HTTPS
- [ ] In cPanel → SSL/TLS Status, enable **AutoSSL** (Let's Encrypt) for
      your domain if not already active.
- [ ] Force HTTPS: cPanel's "Force HTTPS Redirect" toggle, or add to
      `.htaccess`:
      ```
      RewriteCond %{HTTPS} off
      RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
      ```
      (add this near the top of the existing `<IfModule mod_rewrite.c>`
      block).
- [ ] Update `BASE_URL` in `config/config.php` to the `https://` version.

## 4. File permissions & security
- [ ] `uploads/` and its subfolders: writable by PHP (755 typically
      sufficient on shared hosting).
- [ ] Confirm `config/`, `app/`, and `database/` are blocked from direct
      web access (already enforced by the root `.htaccess`
      `RewriteRule ^(config|app|database)/ - [F,L]` line — verify by
      visiting `https://yourdomain.org/config/config.php` and confirming
      you get a 403, not the file contents).
- [ ] Log into `/admin/login.php` with `admin` / `password123` **once**,
      then immediately go to **Admin → Change Password** and set a strong
      password. The seeded plaintext password is auto-upgraded to a
      bcrypt hash on that first login.
- [ ] Consider renaming `admin/login.php`'s effective URL by adding your
      own `.htaccess` rule if you want extra obscurity (optional; the
      real protection is the strong password + session timeout, already
      in place).

## 5. Email
- [ ] The Contact form uses PHP's built-in `mail()` as a best-effort
      notification to `settings.site_email`; on most cPanel hosts this
      works out of the box because mail is routed through the server's
      local MTA. If messages don't arrive, check cPanel → Email Deliverability,
      or switch to SMTP via your host's mail account (would require adding
      PHPMailer manually since this build avoids Composer — see comment in
      `app/controllers/ContactController.php`).

## 6. SEO
- [ ] Visit `https://yourdomain.org/sitemap.php` to confirm it renders
      valid XML, then submit it in Google Search Console.
- [ ] `robots.txt` already points to `sitemap.php` — update the domain
      placeholder inside it to your real domain.
- [ ] Update **Admin → Website Settings → SEO** (title, description,
      keywords) to match your real NGO's messaging.

## 7. Backups
- [ ] cPanel → Backup Wizard (or ask your host) — schedule a weekly full
      backup (files + database).
- [ ] Before any major update, manually export the database via
      phpMyAdmin → Export as a quick safety net.

## 8. Post-launch content checklist
- [ ] Replace all sample images (banners, projects, gallery, team photos)
      with real photos via **Admin** upload fields.
- [ ] Replace the sample Google Map embed in **Admin → Website Settings**
      with your actual location (Google Maps → Share → Embed a map →
      copy the `<iframe>` code).
- [ ] Add your real 80G / registration / 12A certificate files under
      **Admin → Org. Certificates** and **Admin → Documents**.
- [ ] Update the footer legal pages (`app/views/pages/*.php`) if your
      NGO's actual policies differ from the provided sample text.
