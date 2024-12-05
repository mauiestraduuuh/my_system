<?php
session_start(); // Start the session to check login status

include('db_connection.php');

// Check if the logged-in user is the owner
if ($_SESSION['role'] !== 'Owner') {
    echo "<script>alert('Access Denied. Only the Owner can view the sales list.'); window.location.href='dashboard.php';</script>";
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
