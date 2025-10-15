<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot-password.php');
    exit();
}

$token = $_POST['token'] ?? '';
$code  = trim($_POST['code'] ?? '');

$stmt = $conn->prepare('SELECT email, code, expires_at, used FROM password_resets WHERE token = ? LIMIT 1');
$stmt->bind_param('s', $token);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    $_SESSION['reset_error'] = 'Invalid token.';
    header('Location: forgot-password.php');
    exit();
}
if ((int)$row['used'] === 1) {
    $_SESSION['reset_error'] = 'This reset link has already been used.';
    header('Location: forgot-password.php');
    exit();
}
if ($code !== $row['code']) {
    $_SESSION['reset_error'] = 'Invalid code.';
    header('Location: verify-reset-code.php?token=' . urlencode($token));
    exit();
}

$now = new DateTime();
if ($now > new DateTime($row['expires_at'])) {
    $_SESSION['reset_error'] = 'The code has expired.';
    header('Location: forgot-password.php');
    exit();
}

$_SESSION['reset_email'] = $row['email'];
$_SESSION['reset_token'] = $token;

header('Location: reset-password.php');
exit();
?>


