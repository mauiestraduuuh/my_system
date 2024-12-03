<?php
include('db_connection.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get form data
$product_name = $_POST['product_name'];
$unit_price = $_POST['unit_price'];
$quantity = $_POST['quantity'];
$category = $_POST['category'];
$data_inserted_by = $_POST['data_inserted_by'];
$password = $_POST['password'];  // Add password confirmation if necessary

// Password validation (adjust as necessary)
if ($password !== 'correctPassword') {
    die('Invalid password');
}

// Insert data into the inventory table
$sql = "INSERT INTO inventory (product_name, unit_price, quantity, category_id, data_inserted_by) VALUES (?, ?, ?, ?, ?)";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdiss", $product_name, $unit_price, $quantity, $category, $data_inserted_by);

// Execute the query
if ($stmt->execute()) {
    echo "Product added successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
