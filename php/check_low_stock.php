<?php
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connect to database
$conn = new mysqli("localhost", "root", "", "system_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch low stock items with stock_quantity ≤ 5
$sql = "SELECT product_name, stock_quantity FROM products WHERE stock_quantity <= 5";
$result = $conn->query($sql);

$lowStockItems = [];

while ($row = $result->fetch_assoc()) {
    $lowStockItems[] = $row;
}

if (count($lowStockItems) > 0) {
    $body = "<h3>🚨 Low Stock Alert!</h3><ul>";

    foreach ($lowStockItems as $item) {
        $body .= "<li><strong>" . htmlspecialchars($item['product_name']) . "</strong> — Qty: " . 
                 (int)$item['stock_quantity'] . "</li>";
    }

    $body .= "</ul>";

    // Send Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maureen2220lucky@gmail.com';
        $mail->Password   = 'uabq nmrr soxs rhdk'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('maureen2220lucky@gmail.com', 'maureen');
        $mail->addAddress('maureen.estrada@tup.edu.ph', 'wilma');

        $mail->isHTML(true);
        $mail->Subject = 'Low Stock Alert';
        $mail->Body    = $body;

        $mail->send();
        echo '✅ Low stock alert email sent successfully!';
    } catch (Exception $e) {
        echo "❌ Email failed. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "✅ All stock levels are sufficient.";
}

$conn->close();
?>
