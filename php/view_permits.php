<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Only allow access if the user is the Owner
if ($_SESSION['role'] !== 'Owner') {
    // Display a styled modal for Access Denied
    echo "
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-content h2 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .modal-content p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .modal-content button {
            padding: 10px 20px;
            background: #6A0DAD;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .modal-content button:hover {
            background: #5c0e9f;
        }
    </style>
    <div class='modal'>
        <div class='modal-content'>
            <h2>Access Denied</h2>
            <p>Only the Owner can view the permits.</p>
            <button onclick=\"window.location.href='dashboard.php'\">Return to Dashboard</button>
        </div>
    </div>";
    exit;
}

// Fetch data from the permits table
$sql = "SELECT * FROM permits";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permits List</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #6a2e9d;
            font-size: 28px;
            text-align: center;
            margin-top: 20px;
        }

        .styled-table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            font-size: 16px;
            text-align: left;
            background-color: #fff;
        }

        .styled-table thead {
            background-color: #6a2e9d;
            color: #fff;
        }

        .styled-table th,
        .styled-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:hover {
            background-color: #e0e0e0;
        }

        .styled-table td a {
            color: #ff3385;
            text-decoration: none;
        }

        .styled-table td a:hover {
            color: #4CAF50;
        }

        a {
            color: #4CAF50;
            font-size: 16px;
            text-decoration: none;
            margin: 10px;
        }

        a:hover {
            color: #6a2e9d;
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
    <h2>Permits List</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Permit ID</th>
                    <th>Permit Name</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $permit_id = $row['permit_id'];
                    $permit_name = $row['permit_name'];
                    $issue_date = $row['issue_date'];
                    $expiry_date = $row['expiry_date'];

                    $status = 'Expired';
                    $current_date = date("Y-m-d");
                    $expiring_soon_date = date("Y-m-d", strtotime("+30 days"));

                    if ($expiry_date > $current_date) {
                        $status = 'Active';
                    } elseif ($expiry_date <= $expiring_soon_date && $expiry_date > $current_date) {
                        $status = 'Expiring Soon';
                    }
                    ?>
                    <tr>
                        <td><?= $permit_id; ?></td>
                        <td><?= $permit_name; ?></td>
                        <td><?= $issue_date; ?></td>
                        <td><?= $expiry_date; ?></td>
                        <td><?= $status; ?></td>
                        <td>
                            <a href="../php/delete_permit.php?permit_id=<?= $permit_id; ?>" 
                               onclick="return confirm('Are you sure you want to delete this permit?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color:#6a2e9d;">No permits found.</p>
    <?php endif; ?>

    <?php mysqli_close($conn); ?>
</body>
</html>
