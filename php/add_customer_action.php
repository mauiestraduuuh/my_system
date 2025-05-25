<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

include('db_connection.php');

// Sanitize & get form data
$customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$credit_limit = floatval($_POST['credit_limit']);
$added_by = $_SESSION['role'];  // Assuming user role (owner/assistant) is stored in session

// Validate required inputs
if (empty($customer_name) || empty($contact) || $credit_limit <= 0) {
    echo "Invalid input.";
    exit;
}

// Insert into customers table
$stmt = $conn->prepare("INSERT INTO customers (name, contact_info, credit_limit, added_by) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $customer_name, $contact, $credit_limit, $added_by);

if ($stmt->execute()) {
    header("Location: customer_list.php"); // Redirect to customer list after successful addition
    exit;
} else {
    echo "Error adding customer: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
