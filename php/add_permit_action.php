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
    $permit_name = $_POST['permit_name'];
    $issue_date = $_POST['issue_date'];
    $expiry_date = $_POST['expiry_date'];

    // Insert data into the database
    $sql = "INSERT INTO permits (permit_name, issue_date, expiry_date) 
            VALUES ('$permit_name', '$issue_date', '$expiry_date')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to a success page or dashboard
        header("Location: dashboard.php?message=Permit+added+successfully");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
