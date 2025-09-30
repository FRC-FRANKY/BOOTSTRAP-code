<?php
// JobFilter/db_connect.php
// Creates MySQL database and users table if they don't exist

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'jobfilter_db';

// 1) Connect to server (no DB yet)
$serverConn = new mysqli($host, $user, $pass);
if ($serverConn->connect_error) {
    die('Connection failed: ' . $serverConn->connect_error);
}

// 2) Create database if not exists
$createDbSql = "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$serverConn->query($createDbSql)) {
    die('Failed to create database: ' . $serverConn->error);
}

// 3) Connect to target DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed (db): ' . $conn->connect_error);
}

// 4) Create users table (basic auth fields + role)
$createUsersSql = "
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'job_seeker',
  name VARCHAR(255) NOT NULL,
  firstname VARCHAR(100) NULL,
  lastname VARCHAR(100) NULL,
  job_title VARCHAR(150) NULL,
  phone VARCHAR(30) NULL,
  dob DATE NULL,
  bio TEXT NULL,
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

?>


