<?php
// JobFilter/db_connect.php
// Creates MySQL database and users table if they don't exist

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'jobfilter_db';
// Port support (default 3306)
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// 1) Connect to server (no DB yet)
$serverConn = new mysqli($host, $user, $pass, null, $port);
if ($serverConn->connect_error) {
    die('Connection failed: ' . $serverConn->connect_error);
}

// 2) Create database if not exists
$createDbSql = "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$serverConn->query($createDbSql)) {
    die('Failed to create database: ' . $serverConn->error);
}

// 3) Connect to target DB
$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die('Connection failed (db): ' . $conn->connect_error);
}

// 4) Create users table (basic auth fields + role)
$createUsersSql = "
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'job_seeker',
  name VARCHAR(255) NOT NULL,
  firstname VARCHAR(100) NULL,
  lastname VARCHAR(100) NULL,
  job_title VARCHAR(150) NULL,
  phone VARCHAR(30) NULL,
  dob DATE NULL,
  bio TEXT NULL,
  resume_path VARCHAR(255) NULL,
  company_name VARCHAR(255) NULL,
  company_reg_number VARCHAR(100) NULL,
  company_address VARCHAR(255) NULL,
  company_phone VARCHAR(30) NULL,
  company_linkedin VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createUsersSql)) {
    die('Failed to ensure users table: ' . $conn->error);
}

// Optional: index to speed up lookups by email
$conn->query("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");

// Add composite unique constraint on email and role to prevent duplicate accounts for same role
$result = $conn->query("SHOW INDEX FROM users WHERE Key_name = 'unique_email_role'");
if (!$result || $result->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD CONSTRAINT unique_email_role UNIQUE (email, role)");
}

// Ensure resume_path exists on older DBs
$col = $conn->query("SHOW COLUMNS FROM users LIKE 'resume_path'");
if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN resume_path VARCHAR(255) NULL AFTER bio");
}

// 5) Create password_resets table (for forgot password flow)
$createPasswordResetsSql = "
CREATE TABLE IF NOT EXISTS password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  code VARCHAR(6) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createPasswordResetsSql)) {
    die('Failed to ensure password_resets table: ' . $conn->error);
}

// 6) Create jobs table
$createJobsSql = "
CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(255) NOT NULL,
  job_type ENUM('full-time', 'part-time', 'contract', 'internship', 'temporary', 'freelance') NOT NULL,
  category VARCHAR(100) NOT NULL,
  salary_range VARCHAR(100) NULL,
  company_name VARCHAR(255) NOT NULL,
  company_website VARCHAR(255) NULL,
  company_logo VARCHAR(255) NULL,
  posted_by INT UNSIGNED NOT NULL,
  posted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE,
  FULLTEXT INDEX idx_fulltext (title, description, location, company_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createJobsSql)) {
    die('Failed to ensure jobs table: ' . $conn->error);
}
// Optional: index to speed up lookups by title and location
$conn->query("CREATE INDEX IF NOT EXISTS idx_jobs_title_location ON jobs(title, location)");


// 7) Create applications table
$createApplicationsSql = "
CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  cover_letter TEXT NULL,
  resume_path VARCHAR(255) NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('applied', 'under_review', 'interview', 'offered', 'rejected') NOT NULL DEFAULT 'applied',
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_application (job_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createApplicationsSql)) {
    die('Failed to ensure applications table: ' . $conn->error);
}

// Close server connection (keep $conn for app use)
$serverConn->close();


//For contact form
// 8) Create contact_submissions table
$createContactSql = "
CREATE TABLE IF NOT EXISTS contact_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createContactSql)) {
    die('Failed to ensure contact_submissions table: ' . $conn->error);
}
// Optional: index to speed up lookups by email
$conn->query("CREATE INDEX IF NOT EXISTS idx_contact_email ON contact_submissions(email)");

// Now $conn can be used for application queries
?>


