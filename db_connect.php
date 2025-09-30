// student_lab_crud/db_connect.php
<?php
$host = "localhost";
$user = "root";   // default for XAMPP/WAMP
$pass = "";      // default is empty for XAMPP/WAMP
$db   = "student_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure users table exists (auto-migration)
$createUsersTableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'job_seeker',
    name VARCHAR(255) NOT NULL,
    firstname VARCHAR(100) DEFAULT NULL,
    lastname VARCHAR(100) DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($createUsersTableSql)) {
    error_log('Failed to ensure users table exists: ' . $conn->error);
}
?>
