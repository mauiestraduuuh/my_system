<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/SMTP.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "system_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get today's sales
$today = date('Y-m-d');
$sql = "SELECT * FROM sales WHERE DATE(sales_date) = '$today'";
$result = $conn->query($sql);

// Build sales report HTML
$report = "<h2>🧾 Daily Sales Report - $today</h2>";

if ($result->num_rows > 0) {
    $report .= "<table border='1' cellpadding='6' cellspacing='0'>";
    $report .= "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";

    $totalSales = 0;

    while ($row = $result->fetch_assoc()) {
        $product = htmlspecialchars($row['product_name']);
        $qty = $row['quantity'];
        $price = $row['price'];
        $total = $row['total_amount'];

        $report .= "<tr>
                        <td>$product</td>
                        <td>$qty</td>
                        <td>₱" . number_format($price, 2) . "</td>
                        <td>₱" . number_format($total, 2) . "</td>
                    </tr>";

        $totalSales += $total;
    }

    $report .= "<tr>
                    <td colspan='3'><strong>Total Sales</strong></td>
                    <td><strong>₱" . number_format($totalSales, 2) . "</strong></td>
                </tr>";
    $report .= "</table>";
} else {
    $report .= "<p>No sales were recorded today.</p>";
}

// Setup PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'maureen2220lucky@gmail.com';
    $mail->Password   = 'uabq nmrr soxs rhdk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('maureen2220lucky@gmail.com', 'maureen');
    $mail->addAddress('maureen.estrada@tup.edu.ph', 'wilma');

    $mail->isHTML(true);
    $mail->Subject = "Daily Sales Report - $today";
    $mail->Body    = $report;
    $mail->AltBody = strip_tags($report);

    $mail->send();
    echo '✅ Daily sales report has been sent successfully.';
} catch (Exception $e) {
    echo "❌ Failed to send report. Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();
?>
