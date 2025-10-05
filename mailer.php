<?php
// Gmail SMTP mailer using PHPMailer
// Requirements:
// - Enable 2-Step Verification on your Google account
// - Create a Gmail App Password (16 chars) and set as SMTP_PASS
// - Install PHPMailer via Composer: composer require phpmailer/phpmailer

function send_email(string $toEmail, string $toName, string $subject, string $htmlBody, string $plainBody = ''): array {
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $port = getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : 587;
    $user = getenv('SMTP_USER') ?: '';
    $pass = getenv('SMTP_PASS') ?: '';
    $from = getenv('MAIL_FROM') ?: $user;
    $fromName = getenv('MAIL_FROM_NAME') ?: 'JobFilter';

    if ($user === '' || $pass === '') {
        return ['ok' => false, 'error' => 'SMTP not configured (set SMTP_USER and SMTP_PASS).'];
    }

    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        return ['ok' => false, 'error' => 'PHPMailer not installed. Run: composer require phpmailer/phpmailer'];
    }
    require_once $autoload;

    if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        return ['ok' => false, 'error' => 'PHPMailer class missing after install.'];
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass; // Gmail App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;

        $mail->setFrom($from, $fromName);
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody !== '' ? $plainBody : strip_tags($htmlBody);
        $mail->send();
        return ['ok' => true];
    } catch (Exception $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}



