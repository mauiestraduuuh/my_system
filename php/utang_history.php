<?php
include('db_connection.php');

// Fetch utang history joined with customer names
$query = "
    SELECT 
        c.name AS customer_name,
        u.utang_amount,
        u.date_recorded,
        u.paid_amount,
        u.payment_status,
        u.payment_date,
        u.paid_by,
        u.rating,
        u.reason
    FROM utang u
    JOIN customers c ON u.id = c.id
    ORDER BY u.date_recorded DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Utang History</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f8fafc;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #6A0DAD;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #6A0DAD;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f3f4f6;
        }

        .reason-bad {
            color: red;
        }
        .btn-dashboard {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #45a049;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-dashboard:hover {
            background-color: #5c0e9f;
        }
    </style>
</head>
<body>
<a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>
    <h1>Utang History</h1>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Utang Amount</th>
                <th>Date Recorded</th>
                <th>Paid Amount</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Paid By</th>
                <th>Rating</th>
                <th>Reason (if Bad)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['customer_name']}</td>
                        <td>₱" . number_format($row['utang_amount'], 2) . "</td>
                        <td>{$row['date_recorded']}</td>
                        <td>₱" . number_format($row['paid_amount'], 2) . "</td>
                        <td>{$row['payment_status']}</td>
                        <td>" . ($row['payment_date'] ? $row['payment_date'] : '-') . "</td>
                        <td>" . ($row['paid_by'] ?? '-') . "</td>
                        <td>" . ($row['rating'] ?? '-') . "</td>
                        <td class='" . ($row['rating'] == 'Bad' ? 'reason-bad' : '') . "'>" . ($row['reason'] ?? '-') . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No utang records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
