<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    // Basic validation
    if ($email === '' || $password === '') {
        $_SESSION['error'] = 'Please fill in all fields';
        header('Location: login.php');
        exit();
    }

    // Fetch user from DB
    $sql = 'SELECT id, email, password_hash, role, name, firstname, lastname FROM users WHERE email = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Store in SESSION
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];

        // Remember me cookies (7 days)
        if ($remember) {
            setcookie('user_email', $user['email'], time() + (7 * 24 * 3600), '/');
            setcookie('user_role', $user['role'], time() + (7 * 24 * 3600), '/');
            setcookie('user_name', $user['name'], time() + (7 * 24 * 3600), '/');
        }

        unset($_SESSION['error']);
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
