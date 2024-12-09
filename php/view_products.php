<?php
include('db_connection.php');

// Fetch all categories
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

// Check for errors in the query execution
if (!$category_result) {
    die("Error in query: " . mysqli_error($conn));
}

echo "<div class='container'>";
echo "<h2>Products List by Category</h2>";
echo '<a href="dashboard.php">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($category_result) > 0) {
    echo "<style>
    body { font-family: 'Bahnschrift Condensed', sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
    .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
    h2 { color: #6a2e9d; font-size: 26px; text-align: center; margin-top: 20px; }
    a { color: #4CAF50; font-size: 14px; text-decoration: none; margin: 10px; }
    a:hover { color: #6a2e9d; }
    
    .category-section { margin-top: 15px; padding: 10px; }
    .category-heading { background-color: #6a2e9d; color: white; padding: 8px; cursor: pointer; border-radius: 5px; font-size: 18px; }
    .category-products { display: none; padding: 10px; background-color: #fff; margin-top: 10px; border-radius: 5px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
    
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
        background-color: #fff;
        border-radius: 5px;
        overflow: hidden;
    }
    .styled-table thead {
        background-color: #6a2e9d;
        color: #fff;
    }
    .styled-table th, .styled-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .styled-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .styled-table tbody tr:hover {
        background-color: #e0e0e0;
        cursor: pointer;
    }

    /* Hover effect for product names */
    .product-name {
        color: #6a2e9d;
        text-decoration: none;
        font-weight: bold;
    }
    .product-name:hover {
        color: #ff3385;
    }

    /* Styling for the product image on hover */
    .product-container {
        position: relative;
        display: inline-block;
        margin-right: 20px;
        margin-bottom: 20px;
        width: 200px;
        text-align: center;
        border-radius: 5px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        padding: 10px;
        transition: all 0.3s ease-in-out;
    }
    .product-container:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .product-container img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        display: none;
    }
    .product-container:hover img {
        display: block;
    }
    
    .product-details {
        margin-top: 10px;
    }
    .product-details p {
        font-size: 14px;
        color: #333;
        margin: 5px 0;
    }

    /* Layout improvements */
    .product-table {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
    }
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
            echo "<div class='product-table'>";

            while ($product_row = mysqli_fetch_assoc($product_result)) {
                $product_id = $product_row['product_id'];
                $product_name = $product_row['product_name'];
                $unit_price = $product_row['default_price'];
                $stock_quantity = $product_row['stock_quantity'];
                $image_path = $product_row['product_image']; // Fetch the product image column

                echo "<div class='product-container'>
                    <a href='#' class='product-name'>$product_name</a>
                    <img src='../images/$image_path' alt='Product Image'>
                    <div class='product-details'>
                        <p>Price: $unit_price</p>
                        <p>Stock: $stock_quantity</p>
                    </div>
                </div>";
            }

            echo "</div>"; // Close product-table div
        } else {
            echo "<p>No products found in this category.</p>";
        }

        echo "</div></div>";
    }
} else {
    echo "<p>No categories found.</p>";
}

mysqli_close($conn);
echo "</div>"; // Close container
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
