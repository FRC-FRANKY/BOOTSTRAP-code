<?php
// Gmail SMTP mailer using PHPMailer
// Requirements:
// - Enable 2-Step Verification on your Google account
// - Create a Gmail App Password (16 chars) and set as SMTP_PASS
// - Install PHPMailer via Composer: composer require phpmailer/phpmailer

// Load .env variables if present
require_once __DIR__ . '/env.php';

function send_email(string $toEmail, string $toName, string $subject, string $htmlBody, string $plainBody = ''): array {
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $port = getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : 587;
    $user = getenv('SMTP_USER') ?: '';
    $pass = getenv('SMTP_PASS') ?: '';
    $from = getenv('MAIL_FROM') ?: $user;
    $fromName = getenv('MAIL_FROM_NAME') ?: 'JobFilter';
    $secureEnv = strtolower(trim((string)getenv('SMTP_SECURE')));
    $enableDebug = (string)getenv('SMTP_DEBUG') === '1';
    $forceIpv4 = (string)getenv('SMTP_FORCE_IPV4') === '1';

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
        // Optional IPv4-only fallback can help on some Windows/DNS setups
        $mail->Host = $forceIpv4 ? gethostbyname($host) : $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass; // Gmail App Password
        // Security & port
        if ($secureEnv === 'smtps' || $port === 465) {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $port ?: 465;
        } else {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $port ?: 587;
        }

        // Optional debug to PHP error_log
        if ($enableDebug) {
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function ($str) {
                error_log('SMTP DEBUG: ' . $str);
            };
        }

        // Reasonable timeouts
        $mail->Timeout = 20; // seconds
        $mail->SMTPKeepAlive = false;

        // Loosen SSL verification if local CA store is missing (diagnostic; safe for Gmail)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom($from, $fromName);
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainBody !== '' ? $plainBody : strip_tags($htmlBody);
        $mail->send();
        return ['ok' => true];
    } catch (Exception $e) {
        error_log('MAILER ERROR: host=' . $host . ' resolved=' . ($forceIpv4 ? gethostbyname($host) : $host) . ' port=' . $port . ' secure=' . ($secureEnv ?: 'starttls') . ' msg=' . $e->getMessage());
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}



