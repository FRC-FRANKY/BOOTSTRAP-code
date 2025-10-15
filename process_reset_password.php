<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot-password.php');
    exit();
}

if (empty($_SESSION['reset_token']) || empty($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit();
}

$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';
if ($password === '' || strlen($password) < 6 || $password !== $confirm) {
    $_SESSION['reset_error'] = 'Passwords must match and be at least 6 characters.';
    header('Location: reset-password.php');
    exit();
}

$email = $_SESSION['reset_email'];
$token = $_SESSION['reset_token'];
$hash = password_hash($password, PASSWORD_DEFAULT);

// Update user password
$u = $conn->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
$u->bind_param('ss', $hash, $email);
if (!$u->execute()) {
    $_SESSION['reset_error'] = 'Failed to update password.';
    header('Location: reset-password.php');
    exit();
}
$u->close();

// Mark token as used
$m = $conn->prepare('UPDATE password_resets SET used = 1 WHERE token = ?');
$m->bind_param('s', $token);
$m->execute();
$m->close();

unset($_SESSION['reset_email'], $_SESSION['reset_token']);
$_SESSION['registration_success'] = 'Password updated. Please log in.';
header('Location: login.php');
exit();
?>


