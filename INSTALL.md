# Installation Guide

## Option A — Local testing (XAMPP / WAMP / MAMP / Laragon)

1. Copy the whole project folder into your server's web root, e.g.
   `C:\xampp\htdocs\ngo-website` or `/opt/lampp/htdocs/ngo-website`.
2. Start Apache and MySQL from your control panel.
3. Open phpMyAdmin → **Databases** → create a database, e.g. `ngo_website`
   (collation `utf8mb4_general_ci` or `utf8mb4_unicode_ci`).
4. Open the new database → **Import** → choose
   `database/ngo_website.sql` → Go.
5. Edit `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'ngo_website');
   define('DB_USER', 'root');
   define('DB_PASS', '');                 // your MySQL root password, if any
   define('BASE_URL', 'http://localhost/ngo-website');
   define('APP_ENV', 'development');
   ```
6. Make sure `uploads/` is writable (`chmod -R 755 uploads` on
   Linux/Mac; on Windows this is not usually needed).
7. Visit `http://localhost/ngo-website/` — the homepage should load with
   sample content.
8. Visit `http://localhost/ngo-website/admin/login.php` and log in with
   `admin` / `password123`, then change the password immediately.

### Requirements
- PHP 8.1+ (built and tested against 8.3) with `pdo_mysql`, `fileinfo`,
  `mbstring` extensions enabled (all on by default in XAMPP/cPanel).
- MySQL 5.7+ / MySQL 8 / MariaDB 10.3+.
- Apache with `mod_rewrite` and `mod_expires`/`mod_deflate` enabled (all
  standard on shared hosting and XAMPP).

## Option B — cPanel shared hosting

See `DEPLOYMENT.md` for the full go-live checklist (SSL, cron, backups).
Short version:

1. **Database**: cPanel → MySQL Databases → create a database and a
   database user, add the user to the database with **All Privileges**.
   Note the full database name (usually prefixed like `cpaneluser_ngo`).
2. **Import schema**: cPanel → phpMyAdmin → select the new database →
   Import → upload `database/ngo_website.sql`.
3. **Upload files**: zip the project folder's *contents* (not the folder
   itself) and upload via cPanel File Manager to `public_html` (root
   domain) or `public_html/your-subfolder` (subfolder/addon domain), then
   extract.
4. **Configure**: edit `config/config.php` via File Manager's code editor:
   - `DB_HOST` — usually stays `localhost` on shared hosting
   - `DB_NAME`, `DB_USER`, `DB_PASS` — from step 1
   - `BASE_URL` — your live domain, e.g. `https://yourngo.org` (no
     trailing slash)
   - `APP_ENV` — set to `'production'` to hide PHP errors
5. **Permissions**: ensure `uploads/` and its subfolders are writable
   (755 is usually sufficient on shared hosting; avoid 777 unless your
   host specifically requires it).
6. **Rewrite base** (only if installed in a subfolder, not the domain
   root): open `.htaccess` and uncomment/set
   `RewriteBase /your-subfolder/`.
7. Visit your domain, confirm the homepage loads, then log into
   `/admin/login.php` and change the default password right away.

## Troubleshooting

| Symptom | Likely cause |
|---|---|
| Blank white page | `APP_ENV` is `production` and an error is being hidden — temporarily set it to `development` to see the message, or check your host's PHP error log |
| "Database connection failed" | Wrong `DB_NAME`/`DB_USER`/`DB_PASS`, or the DB user wasn't added to the database with privileges |
| CSS/JS not loading, links look broken | `BASE_URL` in `config/config.php` doesn't match the actual URL (protocol, domain, or subfolder mismatch) |
| Uploads fail ("Could not save the uploaded file") | `uploads/` (or a subfolder) is not writable — fix permissions |
| Pretty URLs give a 404 | `mod_rewrite` is disabled, or `AllowOverride` is off for your directory — ask your host to enable both, or contact cPanel support |
