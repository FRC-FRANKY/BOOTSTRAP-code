<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot-password.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reset_error'] = 'Enter a valid email address.';
    header('Location: forgot-password.php');
    exit();
}

// Check if email exists in users table
$existsStmt = $conn->prepare('SELECT id, name FROM users WHERE email = ? LIMIT 1');
$existsStmt->bind_param('s', $email);
$existsStmt->execute();
$existsResult = $existsStmt->get_result();
$user = $existsResult->fetch_assoc();
$existsStmt->close();

if (!$user) {
    $_SESSION['reset_error'] = 'No account found with this email address. Please check your email or create a new account.';
    header('Location: forgot-password.php');
    exit();
}

// Basic rate limit: allow at most 3 active (unused, unexpired) tokens per email
$rlStmt = $conn->prepare("SELECT COUNT(*) AS c FROM password_resets WHERE email = ? AND used = 0 AND expires_at > NOW()");
$rlStmt->bind_param('s', $email);
$rlStmt->execute();
$rlRes = $rlStmt->get_result()->fetch_assoc();
$rlStmt->close();
if ($rlRes && (int)$rlRes['c'] >= 3) {
    $_SESSION['reset_error'] = 'Too many recent reset attempts. Please wait 15 minutes before trying again.';
    header('Location: forgot-password.php');
    exit();
}

$code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$token = bin2hex(random_bytes(32));
$expiresAt = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');

$ins = $conn->prepare('INSERT INTO password_resets (email, token, code, expires_at) VALUES (?, ?, ?, ?)');
$ins->bind_param('ssss', $email, $token, $code, $expiresAt);
if (!$ins->execute()) {
    $_SESSION['reset_error'] = 'Could not start password reset.';
    header('Location: forgot-password.php');
    exit();
}
$ins->close();

// Send email via Gmail SMTP using PHPMailer
$appName = 'JobFilter';
$userName = $user['name'] ?? $email;
$subject = "$appName password reset code";
$html = '<p>Hello ' . htmlspecialchars($userName) . ',</p><p>Here is your password reset code:</p><h2 style="letter-spacing:3px;">' . htmlspecialchars($code) . '</h2><p>This code expires in 15 minutes.</p><p>If you didn\'t request this, please ignore this email.</p>';
$plain = "Hello $userName,\n\nYour $appName password reset code: $code\nThis code expires in 15 minutes.\n\nIf you didn't request this, please ignore this email.";

$sendResult = send_email($email, $userName, $subject, $html, $plain);

// Temporary debugging - remove this later
error_log("Email send result: " . print_r($sendResult, true));
error_log("SMTP_USER: " . getenv('SMTP_USER'));
error_log("SMTP_PASS set: " . (getenv('SMTP_PASS') ? 'YES' : 'NO'));

if ($sendResult['ok'] ?? false) {
    $_SESSION['reset_notice'] = 'We sent a password reset code to your email address.';
} else {
    $_SESSION['reset_error'] = 'Failed to send email: ' . ($sendResult['error'] ?? 'Unknown error');
    header('Location: forgot-password.php');
    exit();
}

header('Location: verify-reset-code.php?token=' . urlencode($token));
exit();
?>


