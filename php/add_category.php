<?php
include('db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the input
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    // Correct column name in the query based on the database schema
    $sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";

    // Execute the query and provide feedback
    if (mysqli_query($conn, $sql)) {
        echo "<p>Category added successfully!</p>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
        }
        h1 {
            text-align: center;
            color: #6A0DAD; 
            font-size: 2.5em;
            margin-top: 20px;
        }

        .form-container {
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container input,
        .form-container button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        .form-container button {
            background-color: #6A0DAD; 
            color: white;
            border: none;
        }

        .form-container button:hover {
            background-color: #5c0e9f; 
        }
    </style>
</head>
<body>
    <h1>Add Category</h1>

    <div class="form-container">
        <form method="POST" action="add_category.php">
            <label for="category_name">Category Name</label>
            <input type="text" id="category_name" name="category_name" required>

            <button type="submit">Add Category</button>
        </form>
    </div>
</body>
</html>
