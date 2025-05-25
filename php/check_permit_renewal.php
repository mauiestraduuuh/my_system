<?php
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "system_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dates to check: expired, expiring in 30 days, and in 7 days
$today = new DateTime();
$check30 = $today->modify('+30 days')->format('Y-m-d');
$today->modify('-30 days'); // Reset to today
$check7 = $today->modify('+7 days')->format('Y-m-d');

// Run the query
$sql = "SELECT permit_name, expiry_date 
        FROM permits 
        WHERE expiry_date <= CURDATE() 
           OR expiry_date = '$check30' 
           OR expiry_date = '$check7'";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$expiringPermits = [];

while ($row = $result->fetch_assoc()) {
    $expiringPermits[] = $row;
}

if (count($expiringPermits) > 0) {
    $body = "<h3>📢 Permit Renewal Reminder</h3><ul>";

    foreach ($expiringPermits as $permit) {
        $body .= "<li><strong>" . htmlspecialchars($permit['permit_name']) . "</strong> — Expiry Date: " . htmlspecialchars($permit['expiry_date']) . "</li>";
    }

    $body .= "</ul><p>Please renew these permits as soon as possible.</p>";

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maureen2220lucky@gmail.com';      // Your Gmail
        $mail->Password   = 'uabq nmrr soxs rhdk';              // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('maureen2220lucky@gmail.com', 'Maureen');
        $mail->addAddress('wilmamalagu1976@gmail.com', 'Wilma');

        $mail->isHTML(true);
        $mail->Subject = 'Permit Renewal Reminder';
        $mail->Body    = $body;

        $mail->send();
        echo '✅ Permit renewal reminder email sent successfully!';
    } catch (Exception $e) {
        echo "❌ Email failed. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "✅ No permits due for renewal in 30 or 7 days.";
}

$conn->close();
?>
