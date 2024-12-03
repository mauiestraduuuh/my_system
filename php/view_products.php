<?php
include('db_connection.php');

// Fetch data from the products table, including the category
$sql = "SELECT products.*, categories.category_name FROM products 
        LEFT JOIN categories ON products.category_id = categories.category_id";
$result = mysqli_query($conn, $sql);

// Check for errors in the query execution
if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}

echo "<h2>Products List</h2>";
echo '<a href="dashboard.php">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($result) > 0) {
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
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Unit Price</th>
            <th>Stock Quantity</th>
            <th>Category</th>
            <th>Product Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $unit_price = $row['default_price']; // Get price from default_price column
        $stock_quantity = $row['stock_quantity']; // Get stock quantity from stock_quantity column
        $category_name = $row['category_name'];
        $image_path = $row['image_path']; // Assuming you have a column for image

        // Display image, if available
        $product_image = $image_path ? "<img src='../images/$image_path' alt='Product Image' width='50'>" : 'No Image';

        echo "<tr>
        <td>$product_id</td>
        <td>$product_name</td>
        <td>$unit_price</td>
        <td>$stock_quantity</td>
        <td>$category_name</td>
        <td>$product_image</td>
        <td><a href='../php/delete_product.php?product_id=$product_id' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a></td>
        </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No products found.</p>";
}

mysqli_close($conn);
?>
