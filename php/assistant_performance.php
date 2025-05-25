<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

include('db_connection.php');

$today = date('Y-m-d');

// Fetch assistants' performance for today
$query = "
    SELECT 
        u.username AS assistant_name,
        SUM(t.total_amount) AS total_sales,
        COUNT(t.transaction_id) AS total_transactions,
        AVG(t.total_amount) AS avg_sales
    FROM transactions t
    JOIN users u ON t.user_id = u.user_id
    WHERE t.transaction_type = 'sale'
        AND DATE(t.transaction_date) = '$today'
        AND u.role IN ('Assistant_1', 'Assistant_2')
    GROUP BY u.username
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching assistant performance: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistant Performance</title>
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

        .performance-table {
            margin: 20px auto;
            width: 80%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .performance-table th {
            background-color: #6a2e9d;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .performance-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .performance-table tr:nth-child(even) {
            background-color: #e3e3e3;
        }

        .performance-table tr:hover {
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
    <h1>Assistant Performance (Today)</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="performance-table">
            <thead>
                <tr>
                    <th>Assistant Name</th>
                    <th>Total Sales (₱)</th>
                    <th>Number of Transactions</th>
                    <th>Average Sale (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['assistant_name']; ?></td>
                        <td><?= number_format($row['total_sales'], 2); ?></td>
                        <td><?= $row['total_transactions']; ?></td>
                        <td><?= number_format($row['avg_sales'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:#6a2e9d;">No sales made by assistants today.</p>
    <?php endif; ?>

    <div class="footer">
        <p>Assistant Performance Tracker</p>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
