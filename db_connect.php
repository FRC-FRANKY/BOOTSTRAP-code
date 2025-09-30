<?php
$host = "localhost";
$user = "root";   // default for XAMPP/WAMP
$pass = "";      // default is empty for XAMPP/WAMP
$db   = "JobFilter_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
