<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "system_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_name = $_POST['expense_name'];
    $expense_category = $_POST['expense_category'];
    $expense_date = $_POST['expense_date'];
    $amount = $_POST['amount'];

    // Insert expense into the database
    $sql = "INSERT INTO expenses (expense_name, expense_category, expense_date, amount) 
            VALUES ('$expense_name', '$expense_category', '$expense_date', '$amount')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Expense added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
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
        .form-container button,
        .form-container select {
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
    <h1>Add Expense</h1>
    <div class="form-container">
        <form method="POST" action="">
            <label for="expense_name">Expense Name</label>
            <input type="text" id="expense_name" name="expense_name" required>

            <label for="expense_category">Expense Category</label>
            <select id="expense_category" name="expense_category" required>
                <option value="">Select Category</option>
                <option value="Office Supplies">Office Supplies</option>
                <option value="Utilities">Utilities</option>
                <option value="Salaries">Salaries</option>
                <option value="Marketing">Marketing</option>
                <option value="Travel">Travel</option>
                <!-- Add more categories as needed -->
            </select>

            <label for="expense_date">Expense Date</label>
            <input type="date" id="expense_date" name="expense_date" required>

            <label for="amount">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" required>

            <button type="submit">Add Expense</button>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="message" style="text-align: center; color: green; margin-top: 20px;">
            <?= $message ?>
        </div>
    <?php endif; ?>
</body>
</html>
