<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';             // Gmail SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'maureen2220lucky@gmail.com';       // Your Gmail
    $mail->Password   = 'uabq nmrr soxs rhdk';          // App Password (not your Gmail password!)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // Use SSL encryption
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('maureen2220lucky@gmail.com', 'maureen');
    $mail->addAddress('wilmamalagu1976@gmail.com', 'wilma');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from XAMPP';
    $mail->Body    = 'This is a test email sent from <b>PHPMailer</b> in your XAMPP project.';
    $mail->AltBody = 'This is a plain-text version of the email content.';

    $mail->send();
    echo '✅ Email has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
