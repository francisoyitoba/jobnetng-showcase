-- sql/schema.sql (simplified for showcase)

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('seeker','employer','admin') NOT NULL DEFAULT 'seeker',
  name VARCHAR(191) DEFAULT '',
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS employers (
  user_id INT PRIMARY KEY,
  company_name VARCHAR(191) DEFAULT '',
  website VARCHAR(255) DEFAULT '',
  verified TINYINT(1) NOT NULL DEFAULT 0,
  verification_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_emp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employer_user_id INT NOT NULL,
  title VARCHAR(191) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(191) DEFAULT '',
  category VARCHAR(191) DEFAULT '',
  status ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_job_emp FOREIGN KEY (employer_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS job_apps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  seeker_user_id INT NOT NULL,
  status ENUM('pending','shortlisted','rejected') NOT NULL DEFAULT 'pending',
  cover_letter_text TEXT,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_app_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  CONSTRAINT fk_app_user FOREIGN KEY (seeker_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
