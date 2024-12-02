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
    $product_name = $_POST['product_name'];
    $sales_date = $_POST['sales_date'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Calculate total amount
    $total_amount = $price * $quantity;

    // Insert data into the database
    $sql = "INSERT INTO sales (product_name, sales_date, price, quantity, total_amount) 
            VALUES ('$product_name', '$sales_date', '$price', '$quantity', '$total_amount')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to a success page or dashboard
        header("Location: dashboard.php?message=Sale+added+successfully");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
