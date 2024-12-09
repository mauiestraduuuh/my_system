<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Check if filters are set
$role_filter = isset($_POST['role']) ? $_POST['role'] : '';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Default transaction condition for the logged-in user
$transaction_condition = ($role === 'Owner') ? "" : "WHERE t.user_id = '$user_id'";

// Database connection
include('db_connection.php');

// Constructing the WHERE clause based on filter conditions
$filter_conditions = [];

if ($role_filter) {
    $filter_conditions[] = "u.role = '$role_filter'";
}

if ($start_date && $end_date) {
    $filter_conditions[] = "t.transaction_date BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($filter_conditions)) {
    $transaction_condition .= " AND " . implode(" AND ", $filter_conditions);
}

// Query to fetch transaction records based on filters
$query = "SELECT t.transaction_id, p.product_name, t.transaction_type, t.quantity, t.total_amount, t.transaction_date, u.username AS inserted_by
          FROM transactions t
          JOIN products p ON t.product_id = p.product_id
          JOIN users u ON t.user_id = u.user_id
          $transaction_condition
          ORDER BY t.transaction_date DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching transactions: " . mysqli_error($conn));
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

        .filter-form {
            text-align: center;
            margin: 20px;
        }
        .filter-form input, .filter-form select {
            padding: 8px;
            margin: 5px;
            font-size: 14px;
        }
        .filter-form button {
            padding: 8px 16px;
            background-color: #6a2e9d;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>
    <h1>Transaction Records</h1>

    <!-- Filter Form -->
    <form method="POST" class="filter-form">
        <select name="role">
            <option value="">--Select Role--</option>
            <option value="Owner" <?= $role_filter === 'Owner' ? 'selected' : ''; ?>>Owner</option>
            <option value="Assistant_1" <?= $role_filter === 'Assistant_1' ? 'selected' : ''; ?>>Assistant 1</option>
            <option value="Assistant_2" <?= $role_filter === 'Assistant_2' ? 'selected' : ''; ?>>Assistant 2</option>
        </select>

        <input type="date" name="start_date" value="<?= $start_date; ?>" placeholder="Start Date">
        <input type="date" name="end_date" value="<?= $end_date; ?>" placeholder="End Date">
        
        <button type="submit" class="btn-filter">Filter</button>
        <a href="transaction.php" class="btn-filter">Clear Filter</a>
    </form>

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
                        <td><?= $row['product_name']; ?></td>
                        <td><?= ucfirst($row['transaction_type']); ?></td>
                        <td><?= $row['quantity']; ?></td>
                        <td><?= $row['total_amount']; ?></td>
                        <td><?= $row['transaction_date']; ?></td>
                        <td><?= $row['inserted_by']; ?></td>
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

<?php mysqli_close($conn); ?>
