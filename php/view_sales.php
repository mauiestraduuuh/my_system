<?php
include('db_connection.php');

// Fetch data from the sales table
$sql = "SELECT * FROM sales";
$result = $conn->query($sql);

echo "<h2>Sales List</h2>";
echo '<a href="../html/dashboard.html">Return to Dashboard</a><br><br>';

if ($result->num_rows > 0) {
    echo "<table border='1'>
    <tr>
        <th>Sale ID</th>
        <th>Product Name</th>
        <th>Sales Date</th>
        <th>Quantity</th>
        <th>Total Amount</th>
        <th>Action</th>
    </tr>";

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

    echo "</table>";
} else {
    echo "<p>No sales found.</p>";
}

$conn->close();
?>
