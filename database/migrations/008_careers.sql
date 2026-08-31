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
