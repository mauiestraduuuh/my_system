<?php

include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Permit</title>
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
        .btn-dashboard {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #45a049;
            color: white;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-dashboard:hover {
            background-color: #6a2e9d;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>
    <h1>Add Permit</h1>

    <div class="form-container">
        <form method="POST" action="add_permit_action.php">
            <label for="permit_name">Permit Name</label>
            <input type="text" id="permit_name" name="permit_name" required>

            <label for="issue_date">Issue Date</label>
            <input type="date" id="issue_date" name="issue_date" required>

            <label for="expiry_date">Expiry Date</label>
            <input type="date" id="expiry_date" name="expiry_date" required>

            <button type="submit">Add Permit</button>
        </form>
    </div>
</body>
</html>
