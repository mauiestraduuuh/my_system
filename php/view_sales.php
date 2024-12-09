<?php
session_start(); // Start the session to check login status

include('db_connection.php');

// Check if the logged-in user is the owner
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
            <p>Only the Owner can view the sales list.</p>
            <button onclick=\"window.location.href='dashboard.php'\">Return to Dashboard</button>
        </div>
    </div>";
    exit;
}

// Fetch data from the sales table
$sql = "SELECT * FROM sales";
$result = $conn->query($sql);

echo "<h2>Sales List</h2>";
echo '<a href="dashboard.php">Return to Dashboard</a><br><br>';

if ($result->num_rows > 0) {
    echo "<style>
    body { font-family: 'Bahnschrift Condensed', sans-serif; }
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        background-color: #fff;
    }
    .styled-table thead { background-color: #6a2e9d; color: #fff; }
    .styled-table th, .styled-table td { padding: 10px; border: 1px solid #ddd; }
    .styled-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
    .styled-table tbody tr:hover { background-color: #e0e0e0; }
    .styled-table td a { color: #ff3385; text-decoration: none; }
    .styled-table td a:hover { color: #4CAF50; }
    h2 { color: #6a2e9d; font-size: 28px; text-align: center; margin-top: 20px; }
    a { color: #4CAF50; font-size: 16px; text-decoration: none; margin: 10px; }
    a:hover { color: #6a2e9d; }
    </style>";

    echo "<table class='styled-table'>
    <thead>
        <tr>
            <th>Sale ID</th>
            <th>Product Name</th>
            <th>Sales Date</th>
            <th>Quantity</th>
            <th>Total Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>";

    // Loop through all sales records and display them
    while ($row = $result->fetch_assoc()) {
        $sale_id = $row['sale_id'];
        $product_name = $row['product_name'];
        $sales_date = $row['sales_date'];
        $quantity = $row['quantity'];
        $total_amount = $row['total_amount'];

        echo "<tr>
        <td>$sale_id</td>
        <td>$product_name</td>
        <td>$sales_date</td>
        <td>$quantity</td>
        <td>$total_amount</td>
        <td><a href='../php/delete_sale.php?sale_id=$sale_id' onclick=\"return confirm('Are you sure you want to delete this sale?');\">Delete</a></td>
        </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No sales found.</p>";
}

$conn->close();
?>
