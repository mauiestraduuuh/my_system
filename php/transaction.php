<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Only allow access if the user is the Owner
if ($_SESSION['role'] !== 'Owner') {
    echo "<script>
        alert('Access Denied: Only the Owner can view transactions.');
        window.location.href = 'dashboard.php';
    </script>";
    exit;
}

// Database connection
include('db_connection.php');

// Fetch transaction data, join products to get product name
$sql = "SELECT t.transaction_id, t.product_id, t.transaction_type, t.quantity, t.total_amount, t.transaction_date, t.inserted_by, p.product_name
        FROM transactions t
        JOIN products p ON t.product_id = p.product_id
        ORDER BY t.transaction_date DESC";
$result = mysqli_query($conn, $sql);

// Check for errors in query
if (!$result) {
    die("Error retrieving transactions: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #6a2e9d;
            margin-top: 20px;
        }

        .transaction-table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .transaction-table th {
            background-color: #6a2e9d;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .transaction-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .transaction-table tr:nth-child(even) {
            background-color: #e3e3e3;
        }

        .transaction-table tr:hover {
            background-color: #d1ffd6;
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

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #6a2e9d;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>
    <h1>Transaction Records</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="transaction-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Product Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Date</th>
                    <th>Inserted By</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['transaction_id']; ?></td>
                        <td><?= $row['product_name']; ?></td> <!-- Show product name -->
                        <td><?= ucfirst($row['transaction_type']); ?></td>
                        <td><?= $row['quantity']; ?></td>
                        <td><?= $row['total_amount']; ?></td>
                        <td><?= $row['transaction_date']; ?></td>
                        <td><?= $row['inserted_by']; ?></td> <!-- Show inserted by -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:#6a2e9d;">No transaction records found.</p>
    <?php endif; ?>

    <div class="footer">
        <p>Transaction Management System</p>
    </div>
</body>
</html>
