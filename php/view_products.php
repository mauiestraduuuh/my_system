<?php
include('db_connection.php');

// Fetch data from the inventory table
$sql = "SELECT * FROM inventory";
$result = mysqli_query($conn, $sql);

echo "<h2>Products List</h2>";
echo '<a href="../html/dashboard.html">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Unit Price</th>
        <th>Quantity</th>
        <th>Date Added</th>
        <th>Expiry Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $unit_price = $row['unit_price'];
        $quantity = $row['quantity'];
        $date_added = $row['date_added'];
        $expiry_date = $row['expiry_date'];

        $status = 'Expired';
        $current_date = date("Y-m-d");
        $expiring_soon_date = date("Y-m-d", strtotime("+30 days"));

        if ($expiry_date > $current_date) {
            $status = 'Active';
        } elseif ($expiry_date <= $expiring_soon_date && $expiry_date > $current_date) {
            $status = 'Expiring Soon';
        }

        echo "<tr>
        <td>$product_id</td>
        <td>$product_name</td>
        <td>$unit_price</td>
        <td>$quantity</td>
        <td>$date_added</td>
        <td>$expiry_date</td>
        <td>$status</td>
        <td><a href='../php/delete_product.php?product_id=$product_id' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a></td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No products found.</p>";
}

mysqli_close($conn);
?>
