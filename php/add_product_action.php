<?php
include('db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $product_name = $_POST['product_name'];
    $unit_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    // Insert product data into the database
    $sql = "INSERT INTO inventory (product_name, unit_price, quantity, expiry_date) 
            VALUES ('$product_name', '$unit_price', '$quantity', '$expiry_date')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the dashboard after successful insertion
        header("Location: dashboard.php?message=Product+added+successfully");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
