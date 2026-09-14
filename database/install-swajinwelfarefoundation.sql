-- =====================================================================
--  Swajin Welfare Foundation - combined production install script
--  Generated from database/ngo_website.sql + all database/migrations/*.sql
--  in order, with the production content cleanup migration applied last.
--  Import this ONE file via phpMyAdmin into the freshly created database -
--  no other SQL files need to be imported separately.
-- =====================================================================

-- ============ BEGIN database/ngo_website.sql ============
-- =====================================================================
--  NGO Website - MySQL 8 schema + seed data
--  Import via cPanel > phpMyAdmin > Import, or:  mysql -u user -p db < ngo_website.sql
--
--  After importing this file, also run database/migrations/*.sql in
--  filename order (see database/migrations/README.md) to pick up
--  location master data (states/districts/talukas) and any later
--  schema changes.
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS
= 0;

-- ---------------------------------------------------------------
-- Settings (key/value store used across the site)
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS settings
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR
(100) NOT NULL UNIQUE,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings
  (setting_key, setting_value)
VALUES
  ('site_name', 'Seva Sankalp Foundation'),
  ('site_tagline', 'Serving communities with dignity'),
  ('site_email', 'info@example.org'),
  ('site_phone', '+91 98765 43210'),
  ('site_whatsapp', '+91 98765 43210'),
  ('site_address', '12, Karve Road, Pune, Maharashtra 411004'),
  ('map_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3783.2!2d73.83!3d18.51!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1" width="100%" height="320" style="border:0;border-radius:16px" allowfullscreen loading="lazy"></iframe>'),
  ('facebook_url', 'https://facebook.com/'),
  ('instagram_url', 'https://instagram.com/'),
  ('twitter_url', 'https://x.com/'),
  ('youtube_url', 'https://youtube.com/'),
  ('seo_title', 'Seva Sankalp Foundation - NGO in Maharashtra'),
  ('seo_description', 'A registered NGO working on education, health, environment and rural livelihoods across Maharashtra.'),
  ('seo_keywords', 'NGO, Maharashtra, donation, volunteering, CSR, crowdfunding'),
  ('donate_upi', 'sevasankalp@upi'),
  ('donate_bank', 'A/c Name: Seva Sankalp Foundation\nBank: State Bank of India\nA/c No: 00000011112222\nIFSC: SBIN0001234\nBranch: Karve Road, Pune'),
  ('donate_qr_image', ''),
  ('registration_no', 'MH/2012/0054321'),
  ('pan_80g', 'Registered under 80G & 12A'),
  ('stat_members', '850'),
  ('stat_projects', '42'),
  ('stat_beneficiaries', '15000'),
  ('stat_villages', '120'),
  ('certificate_bg', ''),
  ('membership_fee_note', 'Annual membership contribution: ₹500'),
  ('announcement', 'Blood donation camp on 26 January - register now from the Activities page!');

-- ---------------------------------------------------------------
-- Admin users
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS admins
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(100) NOT NULL,
  username VARCHAR
(50) NOT NULL UNIQUE,
  email VARCHAR
(150) DEFAULT NULL,
  password VARCHAR
(255) NOT NULL,
  role ENUM
('superadmin','editor') NOT NULL DEFAULT 'superadmin',
  last_login DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default login:  admin / password123
-- The stored value is upgraded to a bcrypt hash automatically on first login.
INSERT INTO admins
  (name, username, email, password)
VALUES
  ('Site Administrator', 'admin', 'admin@example.org', 'password123');

-- ---------------------------------------------------------------
-- Homepage banners / hero slides
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS banners
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  subtitle VARCHAR
(300) DEFAULT NULL,
  image VARCHAR
(255) DEFAULT NULL,
  button_text VARCHAR
(60) DEFAULT NULL,
  button_link VARCHAR
(255) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT
(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO banners
  (title, subtitle, button_text, button_link, sort_order)
VALUES
  ('Every hand can lift a life', 'Join 850+ members working across 120 villages in Maharashtra.', 'Become a member', 'membership/apply', 1),
  ('Education opens every door', 'Sponsor a child''s school year for the cost of one dinner out.', 'Donate now', 'donate', 2),
  ('Green today, alive tomorrow', '31,000 trees planted and counting. Help us reach 50,000.', 'See our projects', 'projects', 3);

-- ---------------------------------------------------------------
-- About sections (Who we are, Mission, Vision, History, Legal info)
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS about_sections
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR
(60) NOT NULL UNIQUE,
  title VARCHAR
(150) NOT NULL,
  content MEDIUMTEXT,
  image VARCHAR
(255) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO about_sections
  (slug, title, content, sort_order)
VALUES
  ('who-we-are', 'Who We Are', 'Seva Sankalp Foundation is a registered non-profit working since 2012 across Maharashtra. We began as a group of twelve volunteers running weekend study classes in one village school and have grown into a network of members, donors and partner institutions working on education, primary health, water and rural livelihoods.', 1),
  ('mission', 'Our Mission', 'To enable underserved rural and urban communities to access quality education, healthcare and sustainable livelihoods, through programs designed and delivered with the community, not merely for it.', 2),
  ('vision', 'Our Vision', 'A Maharashtra where every child completes school, every family can reach basic healthcare, and every village manages its own water and green cover.', 3),
  ('history', 'Our History', 'Founded in 2012 in Pune, registered under the Societies Registration Act. Milestones: first school adoption (2013), first blood donation camp (2015), 10,000th beneficiary (2019), CSR partnerships with three companies (2022), 42 active projects (2025).', 4),
  ('legal', 'Legal Information', 'Registration No: MH/2012/0054321. Registered under Societies Registration Act 1860 and Bombay Public Trusts Act 1950. Donations are eligible for exemption under Section 80G of the Income Tax Act. 12A registration held since 2014. Annual audited statements are published in the Documents section.', 5);

-- ---------------------------------------------------------------
-- People: board members & team
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS people
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  type ENUM
('board','team') NOT NULL DEFAULT 'team',
  name VARCHAR
(120) NOT NULL,
  designation VARCHAR
(120) DEFAULT NULL,
  photo VARCHAR
(255) DEFAULT NULL,
  bio TEXT,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT
(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO people
  (type, name, designation, bio, sort_order)
VALUES
  ('board', 'Dr. Anjali Deshmukh', 'President', 'Paediatrician and public-health advocate, leading the foundation since 2016.', 1),
  ('board', 'Ravindra Kulkarni', 'Secretary', 'Retired school principal; architect of the school adoption program.', 2),
  ('board', 'Sunita Pawar', 'Treasurer', 'Chartered accountant overseeing compliance and audited reporting.', 3),
  ('team', 'Amol Jadhav', 'Program Manager - Education', 'Coordinates 18 school programs across 4 districts.', 1),
  ('team', 'Farzana Shaikh', 'Program Manager - Health', 'Runs mobile health camps and the blood donation network.', 2);

-- ---------------------------------------------------------------
-- Achievements & awards
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS achievements
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  year VARCHAR
(10) DEFAULT NULL,
  description TEXT,
  image VARCHAR
(255) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO achievements
  (title, year, description, sort_order)
VALUES
  ('State Award for Rural Education', '2023', 'Recognised by the Government of Maharashtra for the school adoption model.', 1),
  ('31,000 trees planted', '2025', 'Cumulative plantation with 78% survival rate, audited by partner agronomists.', 2),
  ('4,200 units of blood collected', '2024', 'Through 55 camps organised with district blood banks.', 3);

-- ---------------------------------------------------------------
-- Organisation certificates (80G, 12A, registration scans)
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS org_certificates
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  file VARCHAR
(255) DEFAULT NULL,
  description VARCHAR
(300) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO org_certificates
  (title, description, sort_order)
VALUES
  ('Society Registration Certificate', 'Registered under Societies Registration Act, 1860.', 1),
  ('80G Exemption Certificate', 'Donations eligible for tax exemption.', 2),
  ('12A Registration', 'Income tax registration for charitable institutions.', 3);

-- ---------------------------------------------------------------
-- Membership
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS membership_categories
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(100) NOT NULL,
  fee DECIMAL
(10,2) NOT NULL DEFAULT 0,
  duration_months INT NOT NULL DEFAULT 12,
  benefits TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO membership_categories
  (name, fee, duration_months, benefits)
VALUES
  ('General Member', 500.00, 12, 'Voting rights in AGM, member ID card, event invitations.'),
  ('Life Member', 5000.00, 1200, 'Lifetime membership, ID card, priority volunteering, annual report by post.'),
  ('Student Member', 200.00, 12, 'ID card, volunteering certificates, training workshops.');

CREATE TABLE
IF NOT EXISTS members
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_no VARCHAR
(30) DEFAULT NULL UNIQUE,
  category_id INT UNSIGNED DEFAULT NULL,
  name VARCHAR
(120) NOT NULL,
  photo VARCHAR
(255) DEFAULT NULL,
  dob DATE DEFAULT NULL,
  gender ENUM
('Male','Female','Other') DEFAULT NULL,
  email VARCHAR
(150) NOT NULL UNIQUE,
  phone VARCHAR
(20) NOT NULL,
  address TEXT,
  occupation VARCHAR
(120) DEFAULT NULL,
  blood_group VARCHAR
(5) DEFAULT NULL,
  aadhar VARCHAR
(20) DEFAULT NULL,
  password VARCHAR
(255) NOT NULL,
  status ENUM
('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  valid_till DATE DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_members_category FOREIGN KEY
(category_id)
    REFERENCES membership_categories
(id) ON
DELETE
SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Projects
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS projects
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  slug VARCHAR
(220) NOT NULL UNIQUE,
  type ENUM
('ongoing','completed','upcoming','government','csr') NOT NULL DEFAULT 'ongoing',
  summary VARCHAR
(400) DEFAULT NULL,
  description MEDIUMTEXT,
  image VARCHAR
(255) DEFAULT NULL,
  location VARCHAR
(150) DEFAULT NULL,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  budget DECIMAL
(12,2) DEFAULT NULL,
  partner VARCHAR
(200) DEFAULT NULL,
  report_file VARCHAR
(255) DEFAULT NULL,
  is_featured TINYINT
(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO projects
  (title, slug, type, summary, description, location, start_date, is_featured)
VALUES
  ('School Adoption Program', 'school-adoption-program', 'ongoing', 'Full academic support for 18 zilla parishad schools: libraries, e-learning kits and remedial classes.', 'Since 2013 we adopt government schools and stay for at least five years. Each adopted school receives a library of 800+ books, a solar-powered e-learning kit, teacher training twice a year, and daily remedial classes run by paid local tutors. Learning outcomes are measured through ASER-style assessments every six months.', 'Pune, Satara & Ahmednagar districts', '2013-06-01', 1),
  ('Mobile Health Camps', 'mobile-health-camps', 'ongoing', 'Monthly doctor-led health camps reaching 40 villages without a primary health centre.', 'A doctor, a nurse and a pharmacist travel a fixed monthly route covering 40 villages. Services include general OPD, antenatal checkups, blood-pressure and diabetes screening, and free generic medicines. Serious cases are referred and transport is arranged.', 'Marathwada region', '2018-01-15', 1),
  ('Green Village Mission', 'green-village-mission', 'ongoing', 'Tree plantation with 5-year survival tracking; 31,000 planted so far.', 'We plant native species only - neem, banyan, tamarind, jamun - and pay village youth a small stipend to water and protect saplings for five years. GPS-tagged audits keep our survival rate at 78%.', 'Statewide', '2016-07-01', 1),
  ('Digital Literacy for Women', 'digital-literacy-for-women', 'upcoming', 'Smartphone and digital-banking training for 2,000 rural women, starting August 2026.', 'A 12-session curriculum covering smartphones, UPI payments, government service portals and online safety, delivered through village self-help groups.', 'Solapur district', '2026-08-01', 0),
  ('Anganwadi Nutrition Support', 'anganwadi-nutrition-support', 'government', 'Partnership with WCD department to supplement nutrition in 60 anganwadis.', 'Under a government MoU we supply weekly eggs, groundnut chikki and locally-procured fruit to 60 anganwadi centres, alongside growth-monitoring support.', 'Beed district', '2024-04-01', 0),
  ('Clean Water CSR Project', 'clean-water-csr-project', 'csr', 'RO plants and rainwater harvesting in 15 schools, funded by corporate CSR.', 'Funded by a manufacturing company''s CSR program: RO drinking-water plants, rooftop rainwater harvesting and hygiene education in 15 schools.', 'Nashik district', '2023-09-01', 0),
  ('Flood Relief 2021', 'flood-relief-2021', 'completed', 'Emergency ration kits and rebuilding support for 900 flood-affected families.', 'During the 2021 Konkan floods we distributed 900 ration kits, 400 tarpaulins and school kits for 1,200 children, and helped rebuild 35 homes.', 'Ratnagiri district', '2021-07-25', 0);

CREATE TABLE
IF NOT EXISTS project_images
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  image VARCHAR
(255) NOT NULL,
  caption VARCHAR
(200) DEFAULT NULL,
  CONSTRAINT fk_pimages_project FOREIGN KEY
(project_id)
    REFERENCES projects
(id) ON
DELETE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Events / activities
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS events
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  slug VARCHAR
(220) NOT NULL UNIQUE,
  event_code VARCHAR
(20) NOT NULL UNIQUE,
  type ENUM
('activity','workshop','awareness','training','blood_donation','tree_plantation') NOT NULL DEFAULT 'activity',
  description MEDIUMTEXT,
  image VARCHAR
(255) DEFAULT NULL,
  venue VARCHAR
(200) DEFAULT NULL,
  event_date DATE NOT NULL,
  event_time VARCHAR
(30) DEFAULT NULL,
  registration_open TINYINT
(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO events
  (title, slug, event_code, type, description, venue, event_date, event_time, registration_open)
VALUES
  ('Republic Day Blood Donation Camp', 'republic-day-blood-camp-2026', 'EVT-2026-BD01', 'blood_donation', 'Annual mega blood donation camp with the district blood bank. Donors receive a certificate, refreshments and a donor card. Ages 18-60, weight above 50kg.', 'Foundation Hall, Karve Road, Pune', '2026-01-26', '9:00 AM - 4:00 PM', 1),
  ('Monsoon Tree Plantation Drive', 'monsoon-plantation-2026', 'EVT-2026-TP01', 'tree_plantation', 'Plant 2,000 native saplings with us at the start of monsoon. Transport from Pune provided; wear field clothes.', 'Velhe taluka, Pune district', '2026-07-19', '7:00 AM onwards', 1),
  ('Teacher Training Workshop', 'teacher-training-jun-2026', 'EVT-2026-WS01', 'workshop', 'Two-day residential workshop on activity-based learning for teachers of adopted schools.', 'Training Centre, Satara', '2026-06-20', 'Full day', 0),
  ('Menstrual Health Awareness Program', 'mh-awareness-mar-2026', 'EVT-2026-AW01', 'awareness', 'Sessions in 12 high schools with a gynaecologist, plus free sanitary kit distribution.', 'Various schools, Ahmednagar', '2026-03-08', 'School hours', 0);

CREATE TABLE
IF NOT EXISTS event_registrations
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  name VARCHAR
(120) NOT NULL,
  email VARCHAR
(150) NOT NULL,
  phone VARCHAR
(20) NOT NULL,
  attended TINYINT
(1) NOT NULL DEFAULT 0,
  cert_code VARCHAR
(20) DEFAULT NULL UNIQUE,
  cert_issued_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ereg_event FOREIGN KEY
(event_id)
    REFERENCES events
(id) ON
DELETE CASCADE,
  UNIQUE KEY uniq_event_email (event_id, email
)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Gallery
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS gallery_albums
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(150) NOT NULL,
  cover VARCHAR
(255) DEFAULT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO gallery_albums
  (title, sort_order)
VALUES
  ('Education Programs', 1),
  ('Health Camps', 2),
  ('Plantation Drives', 3),
  ('Events & Celebrations', 4);

CREATE TABLE
IF NOT EXISTS gallery_items
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  album_id INT UNSIGNED DEFAULT NULL,
  type ENUM
('image','video') NOT NULL DEFAULT 'image',
  file VARCHAR
(255) DEFAULT NULL,          -- image path when type=image
  youtube_id VARCHAR
(20) DEFAULT NULL,     -- video id when type=video
  caption VARCHAR
(200) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gitems_album FOREIGN KEY
(album_id)
    REFERENCES gallery_albums
(id) ON
DELETE
SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO gallery_items
  (album_id, type, youtube_id, caption)
VALUES
  (1, 'video', 'dQw4w9WgXcQ', 'Inside our adopted schools (replace with your video)'),
  (3, 'video', 'dQw4w9WgXcQ', 'Plantation drive highlights (replace with your video)');

-- ---------------------------------------------------------------
-- Donations & crowdfunding
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS campaigns
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  slug VARCHAR
(220) NOT NULL UNIQUE,
  summary VARCHAR
(400) DEFAULT NULL,
  description MEDIUMTEXT,
  image VARCHAR
(255) DEFAULT NULL,
  goal_amount DECIMAL
(12,2) NOT NULL DEFAULT 0,
  raised_amount DECIMAL
(12,2) NOT NULL DEFAULT 0,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  is_active TINYINT
(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO campaigns
  (title, slug, summary, description, goal_amount, raised_amount, end_date)
VALUES
  ('Send 100 Girls Back to School', 'send-100-girls-back-to-school', 'Full-year school kits, fees and mentoring for 100 girls who dropped out during migration season. ₹6,000 supports one girl for a year.', 'Every year, seasonal migration pulls girls out of school - and many never return. This campaign funds the full re-enrolment package for 100 girls: school fees, uniform, books, a bicycle where the school is far, and monthly mentoring visits. ₹6,000 covers one girl for a full academic year.', 600000, 214500, '2026-09-30'),
  ('50,000 Trees by 2027', '50000-trees-by-2027', 'Help us grow from 31,000 to 50,000 surviving native trees. ₹150 plants and protects one tree for five years.', 'Each ₹150 covers a native sapling, planting, and five years of paid care by village youth - the reason our survival rate is 78% when the sector average is under 40%.', 2850000, 962000, '2027-06-30'),
  ('Winter Blanket Drive', 'winter-blanket-drive', 'Warm blankets for 1,500 elderly people in shelter homes and pavement dwellings. ₹350 per blanket.', 'Distributed through verified shelter homes and municipal night shelters before peak winter.', 525000, 525000, '2025-12-31');

UPDATE campaigns SET is_active = 0 WHERE slug = 'winter-blanket-drive';

CREATE TABLE
IF NOT EXISTS donations
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  receipt_no VARCHAR
(30) DEFAULT NULL UNIQUE,
  campaign_id INT UNSIGNED DEFAULT NULL,
  donor_name VARCHAR
(120) NOT NULL,
  email VARCHAR
(150) DEFAULT NULL,
  phone VARCHAR
(20) DEFAULT NULL,
  amount DECIMAL
(12,2) NOT NULL,
  method ENUM
('upi','bank','cash','online') NOT NULL DEFAULT 'upi',
  txn_ref VARCHAR
(100) DEFAULT NULL,
  pan VARCHAR
(15) DEFAULT NULL,
  message VARCHAR
(300) DEFAULT NULL,
  status ENUM
('pending','received','failed') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_donations_campaign FOREIGN KEY
(campaign_id)
    REFERENCES campaigns
(id) ON
DELETE
SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Sponsors / CSR partners
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS sponsors
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(150) NOT NULL,
  logo VARCHAR
(255) DEFAULT NULL,
  website VARCHAR
(255) DEFAULT NULL,
  type ENUM
('csr','sponsor','government') NOT NULL DEFAULT 'sponsor',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO sponsors
  (name, type, sort_order)
VALUES
  ('Sunrise Industries CSR', 'csr', 1),
  ('District Collector Office, Pune', 'government', 2),
  ('Bharat Steel Works CSR', 'csr', 3),
  ('Local Traders Association', 'sponsor', 4);

-- ---------------------------------------------------------------
-- Documents (public legal documents & reports)
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS documents
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  category VARCHAR
(80) NOT NULL DEFAULT 'General',
  file VARCHAR
(255) DEFAULT NULL,
  file_size INT DEFAULT NULL,
  downloads INT NOT NULL DEFAULT 0,
  is_visible TINYINT
(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO documents
  (title, category)
VALUES
  ('Society Registration Certificate', 'Legal'),
  ('80G Certificate', 'Legal'),
  ('Annual Report 2024-25', 'Reports'),
  ('Audited Financial Statement 2024-25', 'Financials');

-- ---------------------------------------------------------------
-- Testimonials
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS testimonials
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(120) NOT NULL,
  role VARCHAR
(120) DEFAULT NULL,
  photo VARCHAR
(255) DEFAULT NULL,
  message TEXT NOT NULL,
  is_active TINYINT
(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO testimonials
  (name, role, message, sort_order)
VALUES
  ('Sarpanch, Velhe village', 'Community partner', 'They did not plant trees and leave. Five years later the same team still comes to count and water them. That is the difference.', 1),
  ('Priya M.', 'Monthly donor since 2020', 'I get a photo update of the school library my donations built. I know exactly where every rupee went.', 2),
  ('Dr. S. Kale', 'Volunteer physician', 'The mobile health camp is the only doctor visit many of these villages get. Well organised, every single month.', 3);

-- ---------------------------------------------------------------
-- News
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS news
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR
(200) NOT NULL,
  slug VARCHAR
(220) NOT NULL UNIQUE,
  category VARCHAR
(80) NOT NULL DEFAULT 'Updates',
  image VARCHAR
(255) DEFAULT NULL,
  excerpt VARCHAR
(400) DEFAULT NULL,
  content MEDIUMTEXT,
  is_featured TINYINT
(1) NOT NULL DEFAULT 0,
  is_published TINYINT
(1) NOT NULL DEFAULT 1,
  published_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO news
  (title, slug, category, excerpt, content, is_featured)
VALUES
  ('Foundation crosses 15,000 beneficiaries', 'foundation-crosses-15000-beneficiaries', 'Milestones', 'Our combined education, health and livelihood programs have now reached 15,000 people across 120 villages.', 'This quarter our combined programs crossed 15,000 direct beneficiaries. The number is verified through program-wise registers audited annually. We thank every member, donor and partner institution.', 1),
  ('New CSR partnership for clean water', 'new-csr-partnership-clean-water', 'Partnerships', 'A new CSR partnership will fund RO plants in 15 more schools in Nashik district.', 'The partnership covers installation, three years of maintenance, and hygiene education sessions. Work begins in September.', 0),
  ('AGM 2026 notice to all members', 'agm-2026-notice', 'Announcements', 'The Annual General Meeting will be held on 15 August 2026 at the Foundation Hall, Pune.', 'All approved members are invited. Agenda: annual report adoption, audited accounts, board elections for two seats. Members must carry their ID card.', 0);

-- ---------------------------------------------------------------
-- Contact messages & enquiry form
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS contact_messages
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(120) NOT NULL,
  email VARCHAR
(150) NOT NULL,
  phone VARCHAR
(20) DEFAULT NULL,
  subject VARCHAR
(200) DEFAULT NULL,
  message TEXT NOT NULL,
  source ENUM
('contact','homepage') NOT NULL DEFAULT 'contact',
  is_read TINYINT
(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Volunteers
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS volunteers
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR
(120) NOT NULL,
  email VARCHAR
(150) NOT NULL,
  phone VARCHAR
(20) NOT NULL,
  city VARCHAR
(100) DEFAULT NULL,
  resume VARCHAR
(255) DEFAULT NULL,
  experience TEXT,
  availability VARCHAR
(150) DEFAULT NULL,
  status ENUM
('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- Newsletter
-- ---------------------------------------------------------------
CREATE TABLE
IF NOT EXISTS newsletter_subscribers
(
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR
(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS
= 1;
-- ======================= End of schema ==============================

-- ============ END database/ngo_website.sql ============

-- ============ BEGIN database/migrations\001_add_locations.sql ============
-- =====================================================================
--  Migration 001: Location master data (states, districts, talukas)
--  Sources:
--   States/Districts: github.com/sab99r/Indian-States-And-Districts
--     (corrected: J&K/Ladakh split, DNH+DD merger, Andaman & Nicobar added
--      to reflect the current 28 states + 8 UTs)
--   Maharashtra talukas: Wikipedia "List of talukas of Maharashtra"
--  All other states currently have districts only (no talukas seeded) --
--  see report for how to complete them. Safe to re-run (idempotent).
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS states (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  code VARCHAR(10) DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS districts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  state_id INT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  code VARCHAR(10) DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_districts_state FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_state_district (state_id, name),
  KEY idx_districts_state (state_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS talukas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  district_id INT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  code VARCHAR(10) DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_talukas_district FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_district_taluka (district_id, name),
  KEY idx_talukas_district (district_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO states (name, code, sort_order) VALUES ('Andaman and Nicobar Islands (UT)', 'AN', 1) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Andhra Pradesh', 'AP', 2) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Arunachal Pradesh', 'AR', 3) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Assam', 'AS', 4) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Bihar', 'BR', 5) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Chandigarh (UT)', 'CH', 6) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Chhattisgarh', 'CG', 7) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Dadra and Nagar Haveli and Daman and Diu (UT)', 'DH', 8) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Delhi (NCT)', 'DL', 9) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Goa', 'GA', 10) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Gujarat', 'GJ', 11) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Haryana', 'HR', 12) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Himachal Pradesh', 'HP', 13) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Jammu and Kashmir (UT)', 'JK', 14) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Jharkhand', 'JH', 15) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Karnataka', 'KA', 16) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Kerala', 'KL', 17) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Ladakh (UT)', 'LA', 18) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Lakshadweep (UT)', 'LD', 19) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Madhya Pradesh', 'MP', 20) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Maharashtra', 'MH', 21) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Manipur', 'MN', 22) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Meghalaya', 'ML', 23) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Mizoram', 'MZ', 24) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Nagaland', 'NL', 25) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Odisha', 'OD', 26) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Puducherry (UT)', 'PY', 27) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Punjab', 'PB', 28) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Rajasthan', 'RJ', 29) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Sikkim', 'SK', 30) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Tamil Nadu', 'TN', 31) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Telangana', 'TS', 32) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Tripura', 'TR', 33) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Uttar Pradesh', 'UP', 34) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('Uttarakhand', 'UK', 35) ON DUPLICATE KEY UPDATE code = VALUES(code);
INSERT INTO states (name, code, sort_order) VALUES ('West Bengal', 'WB', 36) ON DUPLICATE KEY UPDATE code = VALUES(code);

INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nicobar', 1 FROM states WHERE name = 'Andaman and Nicobar Islands (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North and Middle Andaman', 2 FROM states WHERE name = 'Andaman and Nicobar Islands (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Andaman', 3 FROM states WHERE name = 'Andaman and Nicobar Islands (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Anantapur', 1 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chittoor', 2 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Godavari', 3 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Guntur', 4 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Krishna', 5 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kurnool', 6 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nellore', 7 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Prakasam', 8 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Srikakulam', 9 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Visakhapatnam', 10 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vizianagaram', 11 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Godavari', 12 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'YSR Kadapa', 13 FROM states WHERE name = 'Andhra Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tawang', 1 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Kameng', 2 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Kameng', 3 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Papum Pare', 4 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kurung Kumey', 5 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kra Daadi', 6 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lower Subansiri', 7 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Upper Subansiri', 8 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Siang', 9 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Siang', 10 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Siang', 11 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Upper Siang', 12 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lower Siang', 13 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lower Dibang Valley', 14 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dibang Valley', 15 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Anjaw', 16 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lohit', 17 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Namsai', 18 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Changlang', 19 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tirap', 20 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Longding', 21 FROM states WHERE name = 'Arunachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Baksa', 1 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Barpeta', 2 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Biswanath', 3 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bongaigaon', 4 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Cachar', 5 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Charaideo', 6 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chirang', 7 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Darrang', 8 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhemaji', 9 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhubri', 10 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dibrugarh', 11 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Goalpara', 12 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Golaghat', 13 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hailakandi', 14 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hojai', 15 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jorhat', 16 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kamrup Metropolitan', 17 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kamrup', 18 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karbi Anglong', 19 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karimganj', 20 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kokrajhar', 21 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lakhimpur', 22 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Majuli', 23 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Morigaon', 24 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nagaon', 25 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nalbari', 26 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dima Hasao', 27 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sivasagar', 28 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sonitpur', 29 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Salmara-Mankachar', 30 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tinsukia', 31 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Udalguri', 32 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Karbi Anglong', 33 FROM states WHERE name = 'Assam' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Araria', 1 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Arwal', 2 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Aurangabad', 3 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Banka', 4 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Begusarai', 5 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhagalpur', 6 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhojpur', 7 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Buxar', 8 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Darbhanga', 9 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Champaran (Motihari)', 10 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gaya', 11 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gopalganj', 12 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jamui', 13 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jehanabad', 14 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kaimur (Bhabua)', 15 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Katihar', 16 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khagaria', 17 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kishanganj', 18 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lakhisarai', 19 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Madhepura', 20 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Madhubani', 21 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Munger (Monghyr)', 22 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Muzaffarpur', 23 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nalanda', 24 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nawada', 25 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Patna', 26 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Purnia (Purnea)', 27 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rohtas', 28 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Saharsa', 29 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Samastipur', 30 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Saran', 31 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sheikhpura', 32 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sheohar', 33 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sitamarhi', 34 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Siwan', 35 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Supaul', 36 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vaishali', 37 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Champaran', 38 FROM states WHERE name = 'Bihar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chandigarh', 1 FROM states WHERE name = 'Chandigarh (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balod', 1 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Baloda Bazar', 2 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balrampur', 3 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bastar', 4 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bemetara', 5 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bijapur', 6 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bilaspur', 7 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dantewada (South Bastar)', 8 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhamtari', 9 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Durg', 10 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gariyaband', 11 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Janjgir-Champa', 12 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jashpur', 13 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kabirdham (Kawardha)', 14 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanker (North Bastar)', 15 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kondagaon', 16 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Korba', 17 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Korea (Koriya)', 18 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahasamund', 19 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mungeli', 20 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Narayanpur', 21 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Raigarh', 22 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Raipur', 23 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajnandgaon', 24 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sukma', 25 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Surajpur  ', 26 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Surguja', 27 FROM states WHERE name = 'Chhattisgarh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dadra & Nagar Haveli', 1 FROM states WHERE name = 'Dadra and Nagar Haveli and Daman and Diu (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Daman', 2 FROM states WHERE name = 'Dadra and Nagar Haveli and Daman and Diu (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Diu', 3 FROM states WHERE name = 'Dadra and Nagar Haveli and Daman and Diu (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Central Delhi', 1 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Delhi', 2 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'New Delhi', 3 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North Delhi', 4 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North East  Delhi', 5 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North West  Delhi', 6 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shahdara', 7 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Delhi', 8 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South East Delhi', 9 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South West  Delhi', 10 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Delhi', 11 FROM states WHERE name = 'Delhi (NCT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North Goa', 1 FROM states WHERE name = 'Goa' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Goa', 2 FROM states WHERE name = 'Goa' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ahmedabad', 1 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amreli', 2 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Anand', 3 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Aravalli', 4 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Banaskantha (Palanpur)', 5 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bharuch', 6 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhavnagar', 7 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Botad', 8 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chhota Udepur', 9 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dahod', 10 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dangs (Ahwa)', 11 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Devbhoomi Dwarka', 12 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gandhinagar', 13 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gir Somnath', 14 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jamnagar', 15 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Junagadh', 16 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kachchh', 17 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kheda (Nadiad)', 18 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahisagar', 19 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mehsana', 20 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Morbi', 21 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Narmada (Rajpipla)', 22 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Navsari', 23 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Panchmahal (Godhra)', 24 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Patan', 25 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Porbandar', 26 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajkot', 27 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sabarkantha (Himmatnagar)', 28 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Surat', 29 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Surendranagar', 30 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tapi (Vyara)', 31 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vadodara', 32 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Valsad', 33 FROM states WHERE name = 'Gujarat' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ambala', 1 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhiwani', 2 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Charkhi Dadri', 3 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Faridabad', 4 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Fatehabad', 5 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gurgaon', 6 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hisar', 7 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jhajjar', 8 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jind', 9 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kaithal', 10 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karnal', 11 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kurukshetra', 12 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahendragarh', 13 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mewat', 14 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Palwal', 15 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Panchkula', 16 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Panipat', 17 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rewari', 18 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rohtak', 19 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sirsa', 20 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sonipat', 21 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Yamunanagar', 22 FROM states WHERE name = 'Haryana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bilaspur', 1 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chamba', 2 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hamirpur', 3 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kangra', 4 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kinnaur', 5 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kullu', 6 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lahaul & Spiti', 7 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mandi', 8 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shimla', 9 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sirmaur (Sirmour)', 10 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Solan', 11 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Una', 12 FROM states WHERE name = 'Himachal Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Anantnag', 1 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bandipore', 2 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Baramulla', 3 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Budgam', 4 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Doda', 5 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ganderbal', 6 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jammu', 7 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kathua', 8 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kishtwar', 9 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kulgam', 10 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kupwara', 11 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Poonch', 12 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pulwama', 13 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajouri', 14 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ramban', 15 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Reasi', 16 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Samba', 17 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shopian', 18 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Srinagar', 19 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Udhampur', 20 FROM states WHERE name = 'Jammu and Kashmir (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bokaro', 1 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chatra', 2 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Deoghar', 3 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhanbad', 4 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dumka', 5 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Singhbhum', 6 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Garhwa', 7 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Giridih', 8 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Godda', 9 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gumla', 10 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hazaribag', 11 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jamtara', 12 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khunti', 13 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Koderma', 14 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Latehar', 15 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lohardaga', 16 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pakur', 17 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Palamu', 18 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ramgarh', 19 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ranchi', 20 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sahibganj', 21 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Seraikela-Kharsawan', 22 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Simdega', 23 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Singhbhum', 24 FROM states WHERE name = 'Jharkhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bagalkot', 1 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ballari (Bellary)', 2 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Belagavi (Belgaum)', 3 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bengaluru (Bangalore) Rural', 4 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bengaluru (Bangalore) Urban', 5 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bidar', 6 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chamarajanagar', 7 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chikballapur', 8 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chikkamagaluru (Chikmagalur)', 9 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chitradurga', 10 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dakshina Kannada', 11 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Davangere', 12 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dharwad', 13 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gadag', 14 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hassan', 15 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Haveri', 16 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kalaburagi (Gulbarga)', 17 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kodagu', 18 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kolar', 19 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Koppal', 20 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mandya', 21 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mysuru (Mysore)', 22 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Raichur', 23 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ramanagara', 24 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shivamogga (Shimoga)', 25 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tumakuru (Tumkur)', 26 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Udupi', 27 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Uttara Kannada (Karwar)', 28 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vijayapura (Bijapur)', 29 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Yadgir', 30 FROM states WHERE name = 'Karnataka' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Alappuzha', 1 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ernakulam', 2 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Idukki', 3 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kannur', 4 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kasaragod', 5 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kollam', 6 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kottayam', 7 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kozhikode', 8 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Malappuram', 9 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Palakkad', 10 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pathanamthitta', 11 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thiruvananthapuram', 12 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thrissur', 13 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Wayanad', 14 FROM states WHERE name = 'Kerala' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kargil', 1 FROM states WHERE name = 'Ladakh (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Leh', 2 FROM states WHERE name = 'Ladakh (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Agatti', 1 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amini', 2 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Androth', 3 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bithra', 4 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chethlath', 5 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kavaratti', 6 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kadmath', 7 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kalpeni', 8 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kilthan', 9 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Minicoy', 10 FROM states WHERE name = 'Lakshadweep (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Agar Malwa', 1 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Alirajpur', 2 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Anuppur', 3 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ashoknagar', 4 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balaghat', 5 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Barwani', 6 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Betul', 7 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhind', 8 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhopal', 9 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Burhanpur', 10 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chhatarpur', 11 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chhindwara', 12 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Damoh', 13 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Datia', 14 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dewas', 15 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhar', 16 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dindori', 17 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Guna', 18 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gwalior', 19 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Harda', 20 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hoshangabad', 21 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Indore', 22 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jabalpur', 23 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jhabua', 24 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Katni', 25 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khandwa', 26 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khargone', 27 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mandla', 28 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mandsaur', 29 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Morena', 30 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Narsinghpur', 31 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Neemuch', 32 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Panna', 33 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Raisen', 34 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajgarh', 35 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ratlam', 36 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rewa', 37 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sagar', 38 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Satna', 39 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sehore', 40 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Seoni', 41 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shahdol', 42 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shajapur', 43 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sheopur', 44 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shivpuri', 45 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sidhi', 46 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Singrauli', 47 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tikamgarh', 48 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ujjain', 49 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Umaria', 50 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vidisha', 51 FROM states WHERE name = 'Madhya Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ahmednagar', 1 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Akola', 2 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amravati', 3 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Aurangabad', 4 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Beed', 5 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhandara', 6 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Buldhana', 7 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chandrapur', 8 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhule', 9 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gadchiroli', 10 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gondia', 11 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hingoli', 12 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalgaon', 13 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalna', 14 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kolhapur', 15 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Latur', 16 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mumbai City', 17 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mumbai Suburban', 18 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nagpur', 19 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nanded', 20 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nandurbar', 21 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nashik', 22 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Osmanabad', 23 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Palghar', 24 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Parbhani', 25 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pune', 26 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Raigad', 27 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ratnagiri', 28 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sangli', 29 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Satara', 30 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sindhudurg', 31 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Solapur', 32 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thane', 33 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Wardha', 34 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Washim', 35 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Yavatmal', 36 FROM states WHERE name = 'Maharashtra' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bishnupur', 1 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chandel', 2 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Churachandpur', 3 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Imphal East', 4 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Imphal West', 5 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jiribam', 6 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kakching', 7 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kamjong', 8 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kangpokpi', 9 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Noney', 10 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pherzawl', 11 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Senapati', 12 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tamenglong', 13 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tengnoupal', 14 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thoubal', 15 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ukhrul', 16 FROM states WHERE name = 'Manipur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Garo Hills', 1 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Jaintia Hills', 2 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Khasi Hills', 3 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North Garo Hills', 4 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ri Bhoi', 5 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Garo Hills', 6 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South West Garo Hills ', 7 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South West Khasi Hills', 8 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Garo Hills', 9 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Jaintia Hills', 10 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Khasi Hills', 11 FROM states WHERE name = 'Meghalaya' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Aizawl', 1 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Champhai', 2 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kolasib', 3 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lawngtlai', 4 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lunglei', 5 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mamit', 6 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Saiha', 7 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Serchhip', 8 FROM states WHERE name = 'Mizoram' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dimapur', 1 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kiphire', 2 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kohima', 3 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Longleng', 4 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mokokchung', 5 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mon', 6 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Peren', 7 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Phek', 8 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tuensang', 9 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Wokha', 10 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Zunheboto', 11 FROM states WHERE name = 'Nagaland' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Angul', 1 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balangir', 2 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balasore', 3 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bargarh', 4 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhadrak', 5 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Boudh', 6 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Cuttack', 7 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Deogarh', 8 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhenkanal', 9 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gajapati', 10 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ganjam', 11 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jagatsinghapur', 12 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jajpur', 13 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jharsuguda', 14 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kalahandi', 15 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kandhamal', 16 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kendrapara', 17 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kendujhar (Keonjhar)', 18 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khordha', 19 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Koraput', 20 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Malkangiri', 21 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mayurbhanj', 22 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nabarangpur', 23 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nayagarh', 24 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nuapada', 25 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Puri', 26 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rayagada', 27 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sambalpur', 28 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sonepur', 29 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sundargarh', 30 FROM states WHERE name = 'Odisha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karaikal', 1 FROM states WHERE name = 'Puducherry (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahe', 2 FROM states WHERE name = 'Puducherry (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pondicherry', 3 FROM states WHERE name = 'Puducherry (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Yanam', 4 FROM states WHERE name = 'Puducherry (UT)' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amritsar', 1 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Barnala', 2 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bathinda', 3 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Faridkot', 4 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Fatehgarh Sahib', 5 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Fazilka', 6 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ferozepur', 7 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gurdaspur', 8 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hoshiarpur', 9 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalandhar', 10 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kapurthala', 11 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ludhiana', 12 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mansa', 13 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Moga', 14 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Muktsar', 15 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nawanshahr (Shahid Bhagat Singh Nagar)', 16 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pathankot', 17 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Patiala', 18 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rupnagar', 19 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sahibzada Ajit Singh Nagar (Mohali)', 20 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sangrur', 21 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tarn Taran', 22 FROM states WHERE name = 'Punjab' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ajmer', 1 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Alwar', 2 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Banswara', 3 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Baran', 4 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Barmer', 5 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bharatpur', 6 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhilwara', 7 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bikaner', 8 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bundi', 9 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chittorgarh', 10 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Churu', 11 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dausa', 12 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dholpur', 13 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dungarpur', 14 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hanumangarh', 15 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jaipur', 16 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jaisalmer', 17 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalore', 18 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jhalawar', 19 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jhunjhunu', 20 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jodhpur', 21 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karauli', 22 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kota', 23 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nagaur', 24 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pali', 25 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pratapgarh', 26 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajsamand', 27 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sawai Madhopur', 28 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sikar', 29 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sirohi', 30 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sri Ganganagar', 31 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tonk', 32 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Udaipur', 33 FROM states WHERE name = 'Rajasthan' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'East Sikkim', 1 FROM states WHERE name = 'Sikkim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North Sikkim', 2 FROM states WHERE name = 'Sikkim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Sikkim', 3 FROM states WHERE name = 'Sikkim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Sikkim', 4 FROM states WHERE name = 'Sikkim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ariyalur', 1 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chennai', 2 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Coimbatore', 3 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Cuddalore', 4 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dharmapuri', 5 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dindigul', 6 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Erode', 7 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanchipuram', 8 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanyakumari', 9 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karur', 10 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Krishnagiri', 11 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Madurai', 12 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nagapattinam', 13 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Namakkal', 14 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nilgiris', 15 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Perambalur', 16 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pudukkottai', 17 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ramanathapuram', 18 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Salem', 19 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sivaganga', 20 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thanjavur', 21 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Theni', 22 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Thoothukudi (Tuticorin)', 23 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tiruchirappalli', 24 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tirunelveli', 25 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tiruppur', 26 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tiruvallur', 27 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tiruvannamalai', 28 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tiruvarur', 29 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vellore', 30 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Viluppuram', 31 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Virudhunagar', 32 FROM states WHERE name = 'Tamil Nadu' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Adilabad', 1 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhadradri Kothagudem', 2 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hyderabad', 3 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jagtial', 4 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jangaon', 5 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jayashankar Bhoopalpally', 6 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jogulamba Gadwal', 7 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kamareddy', 8 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Karimnagar', 9 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khammam', 10 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Komaram Bheem Asifabad', 11 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahabubabad', 12 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahabubnagar', 13 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mancherial', 14 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Medak', 15 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Medchal', 16 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nagarkurnool', 17 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nalgonda', 18 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nirmal', 19 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nizamabad', 20 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Peddapalli', 21 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rajanna Sircilla', 22 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rangareddy', 23 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sangareddy', 24 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Siddipet', 25 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Suryapet', 26 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Vikarabad', 27 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Wanaparthy', 28 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Warangal (Rural)', 29 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Warangal (Urban)', 30 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Yadadri Bhuvanagiri', 31 FROM states WHERE name = 'Telangana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dhalai', 1 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gomati', 2 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Khowai', 3 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North Tripura', 4 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sepahijala', 5 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South Tripura', 6 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Unakoti', 7 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'West Tripura', 8 FROM states WHERE name = 'Tripura' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Agra', 1 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Aligarh', 2 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Allahabad', 3 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ambedkar Nagar', 4 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amethi (Chatrapati Sahuji Mahraj Nagar)', 5 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Amroha (J.P. Nagar)', 6 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Auraiya', 7 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Azamgarh', 8 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Baghpat', 9 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bahraich', 10 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ballia', 11 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Balrampur', 12 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Banda', 13 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Barabanki', 14 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bareilly', 15 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Basti', 16 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bhadohi', 17 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bijnor', 18 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Budaun', 19 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bulandshahr', 20 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chandauli', 21 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chitrakoot', 22 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Deoria', 23 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Etah', 24 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Etawah', 25 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Faizabad', 26 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Farrukhabad', 27 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Fatehpur', 28 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Firozabad', 29 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gautam Buddha Nagar', 30 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ghaziabad', 31 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Ghazipur', 32 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gonda', 33 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Gorakhpur', 34 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hamirpur', 35 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hapur (Panchsheel Nagar)', 36 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hardoi', 37 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hathras', 38 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalaun', 39 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jaunpur', 40 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jhansi', 41 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kannauj', 42 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanpur Dehat', 43 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanpur Nagar', 44 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kanshiram Nagar (Kasganj)', 45 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kaushambi', 46 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kushinagar (Padrauna)', 47 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lakhimpur - Kheri', 48 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lalitpur', 49 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Lucknow', 50 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Maharajganj', 51 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mahoba', 52 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mainpuri', 53 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mathura', 54 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mau', 55 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Meerut', 56 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Mirzapur', 57 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Moradabad', 58 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Muzaffarnagar', 59 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pilibhit', 60 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pratapgarh', 61 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'RaeBareli', 62 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rampur', 63 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Saharanpur', 64 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sambhal (Bhim Nagar)', 65 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sant Kabir Nagar', 66 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shahjahanpur', 67 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shamali (Prabuddh Nagar)', 68 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Shravasti', 69 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Siddharth Nagar', 70 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sitapur', 71 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sonbhadra', 72 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Sultanpur', 73 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Unnao', 74 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Varanasi', 75 FROM states WHERE name = 'Uttar Pradesh' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Almora', 1 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bageshwar', 2 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Chamoli', 3 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Champawat', 4 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dehradun', 5 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Haridwar', 6 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nainital', 7 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pauri Garhwal', 8 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Pithoragarh', 9 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Rudraprayag', 10 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Tehri Garhwal', 11 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Udham Singh Nagar', 12 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Uttarkashi', 13 FROM states WHERE name = 'Uttarakhand' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Alipurduar', 1 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Bankura', 2 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Birbhum', 3 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Burdwan (Bardhaman)', 4 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Cooch Behar', 5 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Dakshin Dinajpur (South Dinajpur)', 6 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Darjeeling', 7 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Hooghly', 8 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Howrah', 9 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Jalpaiguri', 10 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kalimpong', 11 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Kolkata', 12 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Malda', 13 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Murshidabad', 14 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Nadia', 15 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'North 24 Parganas', 16 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Paschim Medinipur (West Medinipur)', 17 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Purba Medinipur (East Medinipur)', 18 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Purulia', 19 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'South 24 Parganas', 20 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO districts (state_id, name, sort_order) SELECT id, 'Uttar Dinajpur (North Dinajpur)', 21 FROM states WHERE name = 'West Bengal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);

INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kankavli', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vaibhavwadi', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Devgad', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Malwan', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sawantwadi', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kudal', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vengurla', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dodamarg', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sindhudurg' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ratnagiri', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Lanja', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Rajapur', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chiplun', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Guhagar', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dapoli', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mandangad', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khed', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ratnagiri' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Alibag', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Murud', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Panvel', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Uran', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karjat', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khalapur', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mangaon', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tala', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Roha', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sudhagad-Pali', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mahad', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Poladpur', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shrivardhan', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mhasala', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Raigad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bandra', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Mumbai Suburban' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kurla', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Mumbai Suburban' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Andheri', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Mumbai Suburban' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Borivali', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Mumbai Suburban' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Thane', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalyan', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Murbad', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shahapur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhiwandi', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ulhasnagar', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ambarnath', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Thane' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Palghar', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vasai', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dahanu', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Talasari', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jawhar', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mokhada', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vada', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vikramgad', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Palghar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nashik', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Igatpuri', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dindori', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Peth', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Trimbakeshwar', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalwan', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deola', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Surgana', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Baglan', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Malegaon', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nandgaon', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chandwad', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Niphad', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sinnar', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Yeola', 15 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nashik' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nandurbar', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Navapur', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shahada', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Talode', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Akkalkuwa', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dhadgaon', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nandurbar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dhule', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Dhule' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sakri', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Dhule' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sindkheda', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Dhule' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirpur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Dhule' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jalgaon', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jamner', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Erandol', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dharangaon', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhusawal', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Raver', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Muktainagar', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bodwad', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Yawal', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Amalner', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Parola', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chopda', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pachora', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhadgaon', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chalisgaon', 15 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalgaon' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Buldhana', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chikhli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deulgaon Raja', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jalgaon Jamod', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sangrampur', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Malkapur', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Motala', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nandura', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khamgaon', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shegaon', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mehkar', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sindkhed Raja', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Lonar', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Buldhana' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Akola', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Akot', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Telhara', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Balapur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Patur', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Murtajapur', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Barshitakli', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Akola' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Washim', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Malegaon', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Risod', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mangrulpir', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karanja', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Manora', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Washim' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Amravati', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhatkuli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nandgaon Khandeshwar', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dharni', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chikhaldara', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Achalpur', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chandurbazar', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Morshi', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Warud', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Daryapur', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Anjangaon-Surji', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chandur', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dhamangaon', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tiosa', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Amravati' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Wardha', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deoli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Seloo', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Arvi', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ashti', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karanja', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Hinganghat', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Samudrapur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Wardha' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nagpur Urban', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nagpur Rural', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kamptee', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Hingna', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Katol', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Narkhed', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Savner', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalameshwar', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ramtek', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mouda', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Parseoni', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Umred', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kuhi', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhiwapur', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nagpur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhandara', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tumsar', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pauni', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mohadi', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sakoli', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Lakhani', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Lakhandur', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Bhandara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gondia', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Goregaon', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Salekasa', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tiroda', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deori', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Amgaon', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Arjuni-Morgaon', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sadak-Arjuni', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gondia' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gadchiroli', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dhanora', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chamorshi', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mulchera', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Desaiganj', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Armori', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kurkheda', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Korchi', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Aheri', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Etapalli', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhamragad', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sironcha', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Gadchiroli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chandrapur', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Saoli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mul', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ballarpur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pombhurna', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gondpimpri', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Warora', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chimur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhadravati', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bramhapuri', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nagbhid', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sindewahi', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Rajura', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Korpana', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jiwati', 15 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Chandrapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Yavatmal', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Arni', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Babhulgaon', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalamb', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Darwha', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Digras', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ner', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pusad', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Umarkhed', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mahagaon', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kelapur', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ralegaon', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ghatanji', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Wani', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Maregaon', 15 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Zari Jamani', 16 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Yavatmal' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nanded', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ardhapur', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mudkhed', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhokar', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Umri', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Loha', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kandhar', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kinwat', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Himayatnagar', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Hadgaon', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mahur', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deglur', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mukhed', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dharmabad', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Biloli', 15 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Naigaon', 16 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Nanded' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Hingoli', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Hingoli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sengaon', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Hingoli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalamnuri', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Hingoli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Basmath', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Hingoli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Aundha Nagnath', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Hingoli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Parbhani', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sonpeth', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gangakhed', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Palam', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Purna', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sailu', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jintur', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Manwath', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pathri', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Parbhani' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jalna', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhokardan', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jafrabad', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Badnapur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Partur', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ambad', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ghansawangi', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mantha', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Jalna' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Aurangabad', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kannad', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Soegaon', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sillod', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Phulambri', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khuldabad', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Vaijapur', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gangapur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Paithan', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Aurangabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Beed', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Georai', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Patoda', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirur-Kasar', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ashti', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ambejogai', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Majalgaon', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Wadwani', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kaij', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Dharur', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Parli', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Beed' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Latur', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ausa', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Renapur', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ahmedpur', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jalkot', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chakur', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirur Anantpal', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nilanga', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Deoni', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Udgir', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Latur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Osmanabad', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tuljapur', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhum', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Paranda', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kalamb', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Washi', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Umarga', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Lohara', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Osmanabad' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Barshi', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Solapur North', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Solapur South', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Akkalkot', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kurduwadi', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Madha', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karmala', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pandharpur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mohol', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Malshiras', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mangalvedhe', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sangole', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Solapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nagar', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shevgaon', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pathardi', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Parner', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Sangamner', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kopargaon', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Akole', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shrirampur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Nevasa', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Rahata', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Rahuri', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shrigonda', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jamkhed', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Ahmednagar' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Pune City', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Haveli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khed', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Junnar', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ambegaon', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Maval', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mulshi', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirur', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhor', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Purandhar', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Velhe', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Baramati', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Indapur', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Daund', 14 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Pune' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Satara', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jaoli', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Koregaon', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Wai', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Mahabaleshwar', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khandala', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Phaltan', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Maan', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khatav', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karad', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Patan', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Satara' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Miraj', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kavathemahankal', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Tasgaon', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Jat', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Walwa', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirala', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Khanapur', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Atpadi', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Palus', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kadegaon', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Sangli' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Karvir', 1 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Panhala', 2 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shahuwadi', 3 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Kagal', 4 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ichalkaranji', 5 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Hatkanangale', 6 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Shirol', 7 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Radhanagari', 8 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gaganbawada', 9 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Bhudargad', 10 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Gadhinglaj', 11 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Chandgad', 12 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);
INSERT INTO talukas (district_id, name, sort_order) SELECT d.id, 'Ajra', 13 FROM districts d JOIN states s ON s.id=d.state_id WHERE s.name='Maharashtra' AND d.name = 'Kolhapur' ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);

SET FOREIGN_KEY_CHECKS = 1;
-- ======================= End of migration ==============================

-- ============ END database/migrations\001_add_locations.sql ============

-- ============ BEGIN database/migrations\002_membership_fields.sql ============
-- =====================================================================
--  Migration 002: Membership form fields
--  Adds split name, location (state/district/taluka), pincode, ID-proof
--  and terms-consent columns to `members`. All additive and nullable so
--  existing member rows and the existing `name`/`aadhar` columns are
--  left untouched - `name` keeps being populated (from the split fields)
--  so the member directory, ID card and dashboard views need no changes.
--
--  Uses `ADD COLUMN IF NOT EXISTS` (MySQL 8.0.29+ / MariaDB 10.0.2+) so
--  it is safe to re-run. If your database is older than that, run the
--  ALTER TABLE statements manually with the IF NOT EXISTS clauses removed.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE members
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name,
    ADD COLUMN IF NOT EXISTS state_id INT UNSIGNED DEFAULT NULL AFTER surname,
    ADD COLUMN IF NOT EXISTS district_id INT UNSIGNED DEFAULT NULL AFTER state_id,
    ADD COLUMN IF NOT EXISTS district_other VARCHAR(100) DEFAULT NULL AFTER district_id,
    ADD COLUMN IF NOT EXISTS taluka_id INT UNSIGNED DEFAULT NULL AFTER district_other,
    ADD COLUMN IF NOT EXISTS taluka_other VARCHAR(100) DEFAULT NULL AFTER taluka_id,
    ADD COLUMN IF NOT EXISTS pincode VARCHAR(6) DEFAULT NULL AFTER taluka_other,
    ADD COLUMN IF NOT EXISTS id_proof_type ENUM('aadhaar','voter_id','passport','driving_licence','pan_card') DEFAULT NULL AFTER aadhar,
    ADD COLUMN IF NOT EXISTS id_proof_number VARCHAR(50) DEFAULT NULL AFTER id_proof_type,
    ADD COLUMN IF NOT EXISTS id_proof_file VARCHAR(255) DEFAULT NULL AFTER id_proof_number,
    ADD COLUMN IF NOT EXISTS terms_accepted_at DATETIME DEFAULT NULL AFTER id_proof_file;

-- Foreign keys: separate statements, guarded, so a partial re-run never
-- errors on "duplicate key name". ON DELETE SET NULL so deactivating or
-- removing a location can never delete a member record.
SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_state');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_state FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_district');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_district FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND CONSTRAINT_NAME = 'fk_members_taluka');
SET @ddl := IF(@fk = 0,
    'ALTER TABLE members ADD CONSTRAINT fk_members_taluka FOREIGN KEY (taluka_id) REFERENCES talukas(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill first_name/surname for existing rows from the old single `name`
-- column, so the new required fields are never blank for pre-existing members.
UPDATE members
SET first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)),
    surname = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
WHERE first_name = '' AND name IS NOT NULL AND name != '';
UPDATE members SET surname = first_name WHERE surname = '' AND first_name != '';

INSERT INTO settings (setting_key, setting_value) VALUES ('member_no_prefix', 'MEM')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
-- ======================= End of migration ==============================

-- ============ END database/migrations\002_membership_fields.sql ============

-- ============ BEGIN database/migrations\003_volunteer_fields.sql ============
-- =====================================================================
--  Migration 003: Volunteer form fields
--  Adds split name and consent-timestamp columns to `volunteers`, same
--  pattern as migration 002 for members: `name` keeps being populated
--  (from the split fields) so the existing admin list needs no changes.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE volunteers
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name,
    ADD COLUMN IF NOT EXISTS consent_accepted_at DATETIME DEFAULT NULL AFTER availability;

-- Backfill first_name/surname for existing rows from the old single `name` column.
UPDATE volunteers
SET first_name = TRIM(SUBSTRING_INDEX(name, ' ', 1)),
    surname = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
WHERE first_name = '' AND name IS NOT NULL AND name != '';
UPDATE volunteers SET surname = first_name WHERE surname = '' AND first_name != '';
-- ======================= End of migration ==============================

-- ============ END database/migrations\003_volunteer_fields.sql ============

-- ============ BEGIN database/migrations\004_donation_fields.sql ============
-- =====================================================================
--  Migration 004: Donation page overhaul support
--  - donation_amount_options: admin-managed preset donation amount cards
--  - donations: split donor name (first/middle/surname), same pattern as
--    members/volunteers; `donor_name` keeps being populated so existing
--    admin donations list and receipts need no changes.
--  - settings: structured bank-transfer fields (for individual copy
--    buttons on Account Number/IFSC - the old `donate_bank` free-text
--    blob can't support that) seeded from the existing demo values, plus
--    homepage crowdfunding banner title/text/campaign override.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS donation_amount_options (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  amount DECIMAL(10,2) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uniq_amount (amount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO donation_amount_options (amount, sort_order) VALUES
  (500, 1), (1000, 2), (2500, 3), (5000, 4);

ALTER TABLE donations
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER donor_name,
    ADD COLUMN IF NOT EXISTS middle_name VARCHAR(60) DEFAULT NULL AFTER first_name,
    ADD COLUMN IF NOT EXISTS surname VARCHAR(60) NOT NULL DEFAULT '' AFTER middle_name;

UPDATE donations
SET first_name = TRIM(SUBSTRING_INDEX(donor_name, ' ', 1)),
    surname = TRIM(SUBSTRING(donor_name, LENGTH(SUBSTRING_INDEX(donor_name, ' ', 1)) + 1))
WHERE first_name = '' AND donor_name IS NOT NULL AND donor_name != '';
UPDATE donations SET surname = first_name WHERE surname = '' AND first_name != '';

INSERT INTO settings (setting_key, setting_value) VALUES
  ('bank_account_name', 'Seva Sankalp Foundation'),
  ('bank_name', 'State Bank of India'),
  ('bank_account_number', '00000011112222'),
  ('bank_ifsc', 'SBIN0001234'),
  ('bank_branch', 'Karve Road, Pune'),
  ('crowdfunding_banner_title', 'Support Us: your ₹500 can fund a month of school supplies'),
  ('crowdfunding_banner_text', '100% of your donation is tracked to a project. Tax exemption available under 80G.'),
  ('crowdfunding_banner_campaign_id', '')
ON DUPLICATE KEY UPDATE setting_value = setting_value;
-- ======================= End of migration ==============================

-- ============ END database/migrations\004_donation_fields.sql ============

-- ============ BEGIN database/migrations\005_member_portal.sql ============
-- =====================================================================
--  Migration 005: Member portal support
--  - password_reset_tokens: secure, single-use, expiring reset tokens
--    for the member "forgot password" flow (no third-party service).
--  - member_notifications: admin -> member notifications shown on the
--    member dashboard.
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reset_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_token_hash (token_hash),
  KEY idx_reset_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS member_notifications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  KEY idx_notif_member (member_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ======================= End of migration ==============================

-- ============ END database/migrations\005_member_portal.sql ============

-- ============ BEGIN database/migrations\006_donation_certificates.sql ============
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

-- ============ END database/migrations\006_donation_certificates.sql ============

-- ============ BEGIN database/migrations\007_homepage_buttons.sql ============
-- =====================================================================
--  Migration 007: Homepage custom buttons
--  Admin-managed buttons shown on the homepage that can link either to an
--  internal page or an external site (opens in a new tab automatically -
--  see is_external_url()/link_target_attrs() in app/helpers/functions.php).
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS homepage_buttons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(80) NOT NULL,
  url VARCHAR(255) NOT NULL,
  style ENUM('primary','outline') NOT NULL DEFAULT 'outline',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ======================= End of migration ==============================

-- ============ END database/migrations\007_homepage_buttons.sql ============

-- ============ BEGIN database/migrations\008_careers.sql ============
-- =====================================================================
--  Migration 008: Careers / job portal
--  - job_categories / job_subcategories: admin-managed filter taxonomy.
--  - jobs: admin-created openings.
--  - job_applications: public applications with resume upload (stored in
--    uploads/private/, same access-gated pattern as member ID proofs).
--  Additive only, safe to re-run.
-- =====================================================================
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS job_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_job_category_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS job_subcategories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_jobsubcat_category FOREIGN KEY (category_id) REFERENCES job_categories(id) ON DELETE CASCADE,
  KEY idx_jobsubcat_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  slug VARCHAR(180) DEFAULT NULL UNIQUE,
  category_id INT UNSIGNED DEFAULT NULL,
  subcategory_id INT UNSIGNED DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL,
  employment_type ENUM('full_time','part_time','contract','internship','volunteer') NOT NULL DEFAULT 'full_time',
  experience VARCHAR(80) DEFAULT NULL,
  education VARCHAR(150) DEFAULT NULL,
  salary_range VARCHAR(80) DEFAULT NULL,
  openings INT UNSIGNED NOT NULL DEFAULT 1,
  description TEXT,
  responsibilities TEXT,
  required_skills TEXT,
  preferred_skills TEXT,
  deadline DATE DEFAULT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_jobs_category FOREIGN KEY (category_id) REFERENCES job_categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_jobs_subcategory FOREIGN KEY (subcategory_id) REFERENCES job_subcategories(id) ON DELETE SET NULL,
  KEY idx_jobs_active (is_active, deadline)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS job_applications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL,
  education VARCHAR(150) DEFAULT NULL,
  experience VARCHAR(80) DEFAULT NULL,
  skills VARCHAR(300) DEFAULT NULL,
  cover_letter TEXT,
  resume_file VARCHAR(255) DEFAULT NULL,
  status ENUM('new','under_review','shortlisted','interview','selected','rejected','withdrawn') NOT NULL DEFAULT 'new',
  admin_notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_jobapp_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  KEY idx_jobapp_job (job_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- ======================= End of migration ==============================

-- ============ END database/migrations\008_careers.sql ============

-- ============ BEGIN database/migrations\009_legal_pages.sql ============
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

-- ============ END database/migrations\009_legal_pages.sql ============

-- ============ BEGIN database/migrations\010_donation_receipt_fields.sql ============
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

-- ============ END database/migrations\010_donation_receipt_fields.sql ============

-- ============ BEGIN database/migrations\011_certificate_branding.sql ============
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

-- ============ END database/migrations\011_certificate_branding.sql ============

-- ============ BEGIN database/migrations\012_production_launch_cleanup.sql ============
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

-- ============ END database/migrations\012_production_launch_cleanup.sql ============

