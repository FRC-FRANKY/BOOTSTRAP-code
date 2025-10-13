<?php
// JobFilter/db_connect.php
// Creates MySQL database and users table if they don't exist

// Load .env variables if present
require_once __DIR__ . '/env.php';

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

// This is Posting JOBS
// 5) Create jobs table(this is for job postings by employers)
$createJobsSql = "      
CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  company_name VARCHAR(255) NOT NULL,
  location VARCHAR(255) NOT NULL,
  salary DECIMAL(10, 2) NULL,
  posted_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createJobsSql)) {
    die('Failed to ensure jobs table: ' . $conn->error);
}
// Optional: index to speed up lookups by title and location
$conn->query("CREATE INDEX IF NOT EXISTS idx_jobs_title_location ON jobs(title, location)");

// Ensure uniqueness per employer, company, and title (posted_by, company_name, title)
// 1) Drop older narrower index if present
$oldUnique = $conn->query("SHOW INDEX FROM jobs WHERE Key_name = 'unique_postedby_title'");
if ($oldUnique && $oldUnique->num_rows > 0) {
	$conn->query("ALTER TABLE jobs DROP INDEX unique_postedby_title");
}

// 2) Create the new, stricter unique constraint
$haveUniquePct = $conn->query("SHOW INDEX FROM jobs WHERE Key_name = 'unique_postedby_company_title'");
if (!$haveUniquePct || $haveUniquePct->num_rows === 0) {
	try {
		$conn->query("ALTER TABLE jobs ADD CONSTRAINT unique_postedby_company_title UNIQUE (posted_by, company_name, title)");
	} catch (mysqli_sql_exception $e) {
		if ((int)$e->getCode() !== 1062) {
			throw $e;
		}
		// Duplicate data exists; ensure a non-unique index for performance instead
		$conn->query("CREATE INDEX IF NOT EXISTS idx_jobs_postedby_company_title ON jobs(posted_by, company_name, title)");
	}
}

// Ensure created_at and updated_at columns exist on older DBs
$jobsColCreated = $conn->query("SHOW COLUMNS FROM jobs LIKE 'created_at'");
if ($jobsColCreated && $jobsColCreated->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER posted_by");
}
$jobsColUpdated = $conn->query("SHOW COLUMNS FROM jobs LIKE 'updated_at'");
if ($jobsColUpdated && $jobsColUpdated->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
}

// Ensure salary column exists on older DBs
$jobsColSalary = $conn->query("SHOW COLUMNS FROM jobs LIKE 'salary'");
if ($jobsColSalary && $jobsColSalary->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN salary DECIMAL(10,2) NULL AFTER location");
}

// Ensure skills columns exist for jobs
$jobsColReqSkills = $conn->query("SHOW COLUMNS FROM jobs LIKE 'required_skills'");
if ($jobsColReqSkills && $jobsColReqSkills->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN required_skills VARCHAR(1000) NULL AFTER salary");
}
$jobsColPrefSkills = $conn->query("SHOW COLUMNS FROM jobs LIKE 'preferred_skills'");
if ($jobsColPrefSkills && $jobsColPrefSkills->num_rows === 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN preferred_skills VARCHAR(1000) NULL AFTER required_skills");
}


// 6) Create applications table
$createApplicationsSql = "
CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  applicant_id INT UNSIGNED NOT NULL,
  cover_letter TEXT NULL,
  resume_path VARCHAR(255) NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($createApplicationsSql)) {
    die('Failed to ensure applications table: ' . $conn->error);
}

// Ensure required columns exist on older DBs
$colJob = $conn->query("SHOW COLUMNS FROM applications LIKE 'job_id'");
if ($colJob && $colJob->num_rows === 0) {
    $conn->query("ALTER TABLE applications ADD COLUMN job_id INT UNSIGNED NOT NULL AFTER id");
    // Add FK if missing
    $conn->query("ALTER TABLE applications ADD CONSTRAINT fk_applications_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE");
}

$colApplicant = $conn->query("SHOW COLUMNS FROM applications LIKE 'applicant_id'");
if ($colApplicant && $colApplicant->num_rows === 0) {
    $conn->query("ALTER TABLE applications ADD COLUMN applicant_id INT UNSIGNED NOT NULL AFTER job_id");
    // Add FK if missing
    $conn->query("ALTER TABLE applications ADD CONSTRAINT fk_applications_applicant FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE");
}

// Optional: index to speed up lookups by job_id and applicant_id (create only if both columns exist)
$haveJob = $conn->query("SHOW COLUMNS FROM applications LIKE 'job_id'");
$haveApplicant = $conn->query("SHOW COLUMNS FROM applications LIKE 'applicant_id'");
if ($haveJob && $haveJob->num_rows > 0 && $haveApplicant && $haveApplicant->num_rows > 0) {
    $idx = $conn->query("SHOW INDEX FROM applications WHERE Key_name = 'idx_applications_job_applicant'");
    if (!$idx || $idx->num_rows === 0) {
        $conn->query("CREATE INDEX idx_applications_job_applicant ON applications(job_id, applicant_id)");
    }
}

// Close server connection (keep $conn for app use)
$serverConn->close();

//For contact form
// 7) Create contact_submissions table
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

?>


