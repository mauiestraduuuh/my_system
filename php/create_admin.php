<?php
include('db_connection.php');

$hashed_password = password_hash('password123', PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password, role) 
        VALUES ('admin', '$hashed_password', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
