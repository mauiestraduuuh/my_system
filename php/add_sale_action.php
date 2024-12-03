<?php
include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get form data
$product_name = $_POST['product_name'];
$quantity = $_POST['quantity'];
$total_amount = $_POST['total_amount'];
$sales_date = $_POST['sales_date'];
$category = $_POST['category'];
$data_inserted_by = $_POST['data_inserted_by'];
$password = $_POST['password'];

// Password confirmation
$stored_password = 'your-password'; // Replace with actual stored password
if ($password != $stored_password) {
    die("Password confirmation failed.");
}

// Fetch the unit price from the product table
$sql = "SELECT default_price FROM products WHERE product_name = '$product_name'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $unit_price = $row['default_price'];

    // Insert sale into the sales table
    $sql_insert = "INSERT INTO sales (product_name, quantity, total_amount, sales_date, category, unit_price, data_inserted_by) 
                   VALUES ('$product_name', '$quantity', '$total_amount', '$sales_date', '$category', '$unit_price', '$data_inserted_by')";
    if ($conn->query($sql_insert) === TRUE) {
        echo "Sale added successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Product not found.";
}

$conn->close();
?>
