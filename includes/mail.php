<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function buildEmailTemplate(string $title, string $message, string $buttonText, string $buttonUrl): string
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $safeButtonText = htmlspecialchars($buttonText, ENT_QUOTES, 'UTF-8');
    $safeButtonUrl = htmlspecialchars($buttonUrl, ENT_QUOTES, 'UTF-8');

    return '
<!doctype html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $safeTitle . '</title>
</head>
<body style="margin:0; padding:24px 0; background-color:#f4f1ea; font-family:Arial, Helvetica, sans-serif; color:#22332d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding:0 12px;">
                <table role="presentation" width="640" style="max-width:640px; width:100%; background:#ffffff; border-radius:14px; border:1px solid #dde8e2;" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="background:linear-gradient(135deg, #0e7c66, #145a86); padding:26px 30px; border-radius:14px 14px 0 0; color:#ffffff;">
                            <p style="margin:0; font-size:12px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.9;">ePiata</p>
                            <h1 style="margin:10px 0 0; font-size:26px; line-height:1.2;">' . $safeTitle . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 30px 12px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.65; color:#30443d;">' . $safeMessage . '</p>
                            <p style="margin:0 0 28px; font-size:15px; line-height:1.65; color:#30443d;">Iti multumim ca faci parte din comunitatea ePiata. Daca ai nevoie de ajutor, raspunde direct la acest email.</p>
                            <a href="' . $safeButtonUrl . '" style="display:inline-block; background:#0e7c66; color:#ffffff; text-decoration:none; font-weight:700; font-size:14px; padding:12px 18px; border-radius:999px;">' . $safeButtonText . '</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 30px 28px;">
                            <p style="margin:0; font-size:12px; line-height:1.5; color:#6a7e76;">Acest mesaj a fost trimis automat de platforma ePiata.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

function sendEmail(string $to, string $subject, string $content): bool
{
    $mail = new PHPMailer(true);

    $username = getenv('MAIL_USERNAME') ?: '';
    $password = getenv('MAIL_PASSWORD') ?: '';
    $fromEmail = getenv('MAIL_FROM_EMAIL') ?: 'noreply@example.com';
    $fromName = getenv('MAIL_FROM_NAME') ?: 'ePiata';

    if ($username === '' || $password === '') {
        error_log('Email credentials are not configured. Set MAIL_USERNAME and MAIL_PASSWORD environment variables.');
        return false;
    }

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int) (getenv('MAIL_PORT') ?: 587);

        $mail->CharSet='UTF-8';

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = strip_tags($content);
        return $mail->send();
    }
    
    catch (Exception $e) {
        error_log("Email could not be sent: {$mail->ErrorInfo}");
        return false;
    }
}

