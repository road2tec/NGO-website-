-- =====================================================================
--  NGO Website - MySQL 8 schema + seed data
--  Import via cPanel > phpMyAdmin > Import, or:  mysql -u user -p db < ngo_website.sql
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
