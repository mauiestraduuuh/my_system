<?php
include('db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $utang_amount = floatval($_POST['utang_amount']);
    $date_recorded = date('Y-m-d H:i:s');
    $payment_status = 'Unpaid';
    $paid_amount = 0.00;
    $paid_by = NULL; // Not yet paid
    $payment_date = NULL;
    $rating = NULL;
    $reason = NULL;

    if ($utang_amount <= 0) {
        die("Invalid utang amount.");
    }

    $query = "INSERT INTO utang (id, utang_amount, date_recorded, paid_amount, payment_status, payment_date, paid_by, rating, reason)
              VALUES ('$customer_id', '$utang_amount', '$date_recorded', '$paid_amount', '$payment_status', NULL, NULL, NULL, NULL)";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Utang recorded successfully.');
                window.location.href = 'add_utang.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}
?>
