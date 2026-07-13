# Software Requirements Specification (SRS)
## NGO Management Website — Seva Sankalp Foundation (sample content)

### 1. Purpose
This document specifies the requirements for a public NGO website with a
custom admin panel, built in PHP 8.3 + MySQL 8 + Bootstrap 5, deployable on
shared (cPanel) hosting without Composer or Node.js.

Source requirements: client-provided scope-of-work PDF and detailed feature
brief. Reference sites reviewed for style direction only (jvahini.in,
zuards.org) — no code or content was copied from either; the visual design
in this build is original (see "Design system" in `README.md`).

### 2. Scope
A public-facing site (navigation: Home, Membership, Projects, Activities,
Gallery, Donate, About Us, Contact Us) plus a separate admin panel for
managing all content, memberships, donations, events and messages.

### 3. User roles
| Role | Capabilities |
|---|---|
| Visitor | Browse all public pages, submit enquiry/contact/volunteer forms, register for events, donate/pledge, apply for membership, look up member ID card & event certificate |
| Member (logged in) | View dashboard, check application status, download ID card once approved |
| Admin (superadmin/editor) | Full CRUD over all website content, approve/reject members & volunteers, mark donations received, mark event attendance, manage settings |

### 4. Functional requirements
1. **Navigation** — top navbar in the specified order with dropdowns for
   Membership, Projects, Activities, Donate, About Us; sticky "Donate Now"
   button; floating donate button; footer with legal links, social links,
   Google Translate widget (English/Hindi/Marathi + more).
2. **Home** — hero banner slider, animated stats, who-we-are, mission/vision,
   achievements, featured projects, donation/crowdfunding banner, activities,
   volunteer CTA, gallery preview, testimonials, news, sponsors/CSR/government
   recognition, enquiry form (placed just before the footer), embedded map.
3. **About** — Who We Are, Mission & Vision, History, Board Members, Team,
   Achievements, Certificates, Legal Information (registration/80G/12A).
4. **Membership** — categories, online application with photo/document
   upload, email+password login, status check, member directory search,
   printable ID card with QR code, admin approval workflow that assigns a
   unique member number and validity period.
5. **Projects** — Ongoing / Completed / Upcoming / Government / CSR types,
   detail pages with image gallery, budget, partner, downloadable report.
6. **Activities** — Recent / Upcoming events, online registration capturing
   name/email/phone, unique Event ID per event.
7. **Gallery** — albums, image lightbox, category filter, YouTube-embedded
   videos.
8. **Donate** — UPI QR, bank details, pledge-recording form (receipt number
   generated, admin reconciles and marks "received"), Crowdfunding campaigns
   with progress bars and per-campaign pledge form, Sponsorship program page.
9. **Documents** — public legal/financial documents, searchable, download
   counter.
10. **Event Participation Certificate** — visitor enters name, email and
    Event ID; system validates the matching, attended registration and
    issues a printable certificate with a QR-verifiable code.
11. **Contact** — address/phone/email/WhatsApp, contact form, Google Map.
12. **Legal pages** — Privacy Policy, Terms & Conditions, Refund Policy,
    Disclaimer.
13. **Admin panel** — dashboard with key counters; CRUD for banners, about
    sections, board/team, achievements, org. certificates, testimonials,
    news, sponsors, membership categories, projects, events (+ registration
    list & attendance marking), gallery (albums + items), campaigns; member
    approval/rejection; donation status management; volunteer application
    review; contact-message inbox; newsletter export; website & SEO
    settings; change password.

### 5. Non-functional requirements
- **Security** — CSRF tokens on every state-changing request, prepared
  statements everywhere (PDO, no string-built SQL), password hashing
  (bcrypt via `password_hash`), input validation, session timeout, math
  captcha on public forms, MIME-verified file uploads.
- **SEO** — meta title/description/keywords, canonical URL, Open Graph,
  Twitter Card, Schema.org (`NGO` type), dynamic `sitemap.php`,
  `robots.txt`, friendly URLs via `.htaccess` rewrite.
- **Performance** — lazy-loaded images, CSS/JS from CDN with browser
  caching headers, gzip/deflate via `.htaccess`.
- **Accessibility** — semantic landmarks, visible focus states, alt text on
  images, `prefers-reduced-motion` respected.
- **Responsiveness** — Bootstrap 5 grid, tested breakpoints down to 360px.
- **Portability** — no Composer, no Node build step; works on PHP 8.3 with
  the `pdo_mysql`, `fileinfo`, and `gd`/`mbstring` extensions typically
  enabled by default on cPanel shared hosting.

### 6. Data design
See `database/ngo_website.sql` for the full schema (20+ normalized tables
with foreign keys, seeded with realistic sample content). Entity summary:
`settings`, `admins`, `banners`, `about_sections`, `people`, `achievements`,
`org_certificates`, `membership_categories`, `members`, `projects`,
`project_images`, `events`, `event_registrations`, `gallery_albums`,
`gallery_items`, `campaigns`, `donations`, `sponsors`, `documents`,
`testimonials`, `news`, `contact_messages`, `volunteers`,
`newsletter_subscribers`.

### 7. Architecture
Custom lightweight MVC (no framework):
- `index.php` — single public front controller / router.
- `app/core` — `Database` (PDO singleton + helpers), `Controller` (base
  class with `render()`).
- `app/controllers` — one controller per navbar area.
- `app/views` — one folder per controller, plus `layouts/header.php` &
  `layouts/footer.php`.
- `app/helpers/functions.php` — escaping, CSRF, uploads, settings cache,
  flash messages, pagination, captcha, slugs.
- `admin/` — self-contained admin panel with its own router
  (`admin/index.php?page=...`), a config-driven `SimpleCrud` helper for
  straightforward tables, and hand-written modules for members, donations,
  events+registrations, gallery, settings and password.

### 8. Out of scope / noted assumptions
- No live payment gateway integration is wired in (Razorpay/PayU etc. are
  commonly used in India but require merchant KYC); the Donate flow instead
  records a pledge with UPI/bank details shown, which the admin reconciles
  manually — a common interim pattern for small NGOs. A gateway can be
  added later inside `DonateController::handlePledge()`.
- Google Translate's official widget is used for the Hindi/Marathi/English
  switcher (client-side, no server translation infrastructure needed).
