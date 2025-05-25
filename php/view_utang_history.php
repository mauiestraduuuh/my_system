<?php
// Assuming the user is logged in
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

include('db_connection.php');

// Get customer ID (could be passed via GET or POST)
$customer_id = $_GET['customer_id'];

// Prepare the query to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM utang WHERE customer_id = ? ORDER BY date_recorded DESC");
$stmt->bind_param("i", $customer_id); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching customer credit history: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Credit History</title>
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
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #6A0DAD;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Credit History for Customer #<?= htmlspecialchars($customer_id) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Utang Amount</th>
                <th>Paid Amount</th>
                <th>Payment Status</th>
                <th>Rating</th>
                <th>Reason (if Bad)</th>
                <th>Date Recorded</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['utang_amount']); ?></td>
                    <td><?= htmlspecialchars($row['paid_amount']); ?></td>
                    <td><?= htmlspecialchars($row['payment_status']); ?></td>
                    <td><?= htmlspecialchars($row['rating']); ?></td>
                    <td><?= $row['rating'] == 'Bad' ? htmlspecialchars($row['reason']) : ''; ?></td>
                    <td><?= date('F j, Y, g:i a', strtotime($row['date_recorded'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
