<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

include('db_connection.php');

// Fetch all customers
$query = "SELECT * FROM customers ORDER BY name ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching customer list: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #6A0DAD;
            font-size: 2.5em;
            margin-top: 20px;
        }
        .customer-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .customer-table th, .customer-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .customer-table th {
            background-color: #6A0DAD;
            color: white;
        }
        .customer-table tr:hover {
            background-color: #f4f4f4;
        }
        .btn-view {
            padding: 8px 12px;
            background-color: #6A0DAD;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-view:hover {
            background-color: #5c0e9f;
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
    <h1>Customer List</h1>

    <table class="customer-table">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Contact</th>
                <th>Credit Limit</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['contact_info']); ?></td>
                    <td><?= htmlspecialchars($row['credit_limit']); ?></td>
                    <td>
                    <a href="../php/utang_history.php" class="btn">Utang History</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
