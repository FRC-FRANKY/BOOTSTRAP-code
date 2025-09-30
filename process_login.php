<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Debug: Log form data
    error_log("Form data received: email='$email', password='$password'");
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
        header("Location: login.php");
        exit();
    }
    
    // Look up user in DB
    $stmt = $conn->prepare("SELECT id, email, password_hash, role, name, firstname, lastname, status FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userRow = $result->fetch_assoc();
    $stmt->close();
    
    // Debug: Log the attempt (remove this in production)
    error_log("Login attempt: email=$email, password=$password, found=" . ($user ? 'yes' : 'no'));
    
    if ($userRow && password_verify($password, $userRow['password_hash'])) {
        // Store in SESSION
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userRow['id'];
        $_SESSION['email'] = $userRow['email'];
        $_SESSION['name'] = $userRow['name'];
        $_SESSION['role'] = $userRow['role'];
        $_SESSION['firstname'] = $userRow['firstname'];
        $_SESSION['lastname'] = $userRow['lastname'];
        
        // Store in COOKIES if remember me is checked (valid 7 days)
        if ($remember) {
            setcookie("user_email", $userRow['email'], time() + (7 * 24 * 3600), "/");
            setcookie("user_role", $userRow['role'], time() + (7 * 24 * 3600), "/");
            setcookie("user_name", $userRow['name'], time() + (7 * 24 * 3600), "/");
        }
        
        // Clear any error messages
        unset($_SESSION['error']);
        
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header("Location: login.php");
        exit();
    }
} else {
    // If not POST request, redirect to login
    header("Location: login.php");
    exit();
}
?>
