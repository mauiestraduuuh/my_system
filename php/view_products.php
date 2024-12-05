<?php
include('db_connection.php');

// Fetch all categories
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

// Check for errors in the query execution
if (!$category_result) {
    die("Error in query: " . mysqli_error($conn));
}

echo "<h2>Products List by Category</h2>";
echo '<a href="dashboard.php">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($category_result) > 0) {
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
    .category-section { margin-top: 20px; }
    .category-heading { background-color: #6a2e9d; color: white; padding: 10px; cursor: pointer; }
    .category-products { display: none; padding: 10px; background-color: #f9f9f9; }
    </style>";

    // Loop through each category
    while ($category_row = mysqli_fetch_assoc($category_result)) {
        $category_id = $category_row['category_id'];
        $category_name = $category_row['category_name'];

        // Fetch products for the current category
        $product_sql = "SELECT * FROM products WHERE category_id = $category_id";
        $product_result = mysqli_query($conn, $product_sql);

        if (!$product_result) {
            die("Error in query: " . mysqli_error($conn));
        }

        echo "<div class='category-section'>";
        echo "<div class='category-heading' onclick='toggleCategory($category_id)'>$category_name</div>";
        echo "<div class='category-products' id='category-products-$category_id'>";

        if (mysqli_num_rows($product_result) > 0) {
            echo "<table class='styled-table'>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Stock Quantity</th>
                    <th>Product Image</th>
                </tr>
            </thead>
            <tbody>";

            while ($product_row = mysqli_fetch_assoc($product_result)) {
                $product_id = $product_row['product_id'];
                $product_name = $product_row['product_name'];
                $unit_price = $product_row['default_price'];
                $stock_quantity = $product_row['stock_quantity'];
                $image_path = $product_row['product_image']; // Fetch the product image column

                // Display the image, or "No Image" if no image exists
                $product_image = $image_path ? "<img src='../images/$image_path' alt='Product Image' width='50'>" : 'No Image';

                echo "<tr>
                <td>$product_id</td>
                <td>$product_name</td>
                <td>$unit_price</td>
                <td>$stock_quantity</td>
                <td>$product_image</td>
                </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No products found in this category.</p>";
        }

        echo "</div></div>";
    }
} else {
    echo "<p>No categories found.</p>";
}

mysqli_close($conn);
?>

<script>
// Function to toggle visibility of the products for each category
function toggleCategory(categoryId) {
    var products = document.getElementById('category-products-' + categoryId);
    if (products.style.display === 'none' || products.style.display === '') {
        products.style.display = 'block';
    } else {
        products.style.display = 'none';
    }
}
</script>
