<?php
// add_sale.php

include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Your form handling code here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale</title>
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
    <h1>Add Sale</h1>

    <div class="form-container">
        <form method="POST" action="add_sale_action.php">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="sales_date">Sales Date</label>
            <input type="date" id="sales_date" name="sales_date" required>

            <label for="price">Price</label>
            <input type="number" step="0.01" id="price" name="price" required>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" required>

            <button type="submit">Add Sale</button>
        </form>
    </div>
</body>
</html>
