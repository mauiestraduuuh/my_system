<?php
session_start();

// Check if the user is logged in and role exists in the session
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit;
}

// Get the logged-in user (username and role)
$inserted_by = $_SESSION['username']; // This will store the current logged-in user (Owner, Assistant_1, Assistant_2)
$role = $_SESSION['role'];  // Get the role of the user (Owner, Assistant_1, Assistant_2)

// Database connection
include('db_connection.php');

// Get form data (adjust as per your form input names)
$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
$transaction_type = mysqli_real_escape_string($conn, $_POST['transaction_type']);
$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
$total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);

// Insert the transaction with the current user's details (inserted_by)
$sql = "INSERT INTO transactions (product_id, transaction_type, quantity, total_amount, transaction_date, inserted_by)
        VALUES ('$product_id', '$transaction_type', '$quantity', '$total_amount', NOW(), '$inserted_by')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Transaction added successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
