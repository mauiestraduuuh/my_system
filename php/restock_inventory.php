<?php
include('db_connection.php');

// Fetch all categories
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

// Check for errors in the query execution
if (!$category_result) {
    die("Error in query: " . mysqli_error($conn));
}

echo "<h2>Restock Products List by Category</h2>";
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
    .quantity-buttons { display: flex; gap: 10px; align-items: center; }
    .quantity-buttons button { padding: 5px 10px; font-size: 14px; cursor: pointer; }
    .product-price input { width: 60px; }
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
                    <th>Action</th>
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
                <td><input class='product-price' type='number' value='$unit_price' min='0.01' step='0.01' data-product-id='$product_id'></td>
                <td>
                    <div class='quantity-buttons'>
                        <button onclick=\"updateQuantity($product_id, 'increase')\">+</button>
                        <span id='quantity-$product_id'>$stock_quantity</span>
                        <button onclick=\"updateQuantity($product_id, 'decrease')\">-</button>
                    </div>
                </td>
                <td>$product_image</td>
                <td><a href='update_product.php?product_id=$product_id'>Update</a></td>
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

// Function to update quantity (increase or decrease)
function updateQuantity(productId, action) {
    var currentQuantity = parseInt(document.getElementById('quantity-' + productId).innerText);
    var newQuantity = (action === 'increase') ? currentQuantity + 1 : (currentQuantity > 0) ? currentQuantity - 1 : 0;

    // Update the displayed quantity
    document.getElementById('quantity-' + productId).innerText = newQuantity;

    // Prompt the user to select who is entering the data
    var insertedBy = prompt("Enter your name (Owner, Assistant_1, Assistant_2):");
    if (insertedBy) {
        // AJAX call to update the database
        updateProductQuantity(productId, newQuantity, insertedBy);
    }
}

// Function to update product quantity in the database
function updateProductQuantity(productId, newQuantity, insertedBy) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_quantity.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText); // Log success response
        }
    };
    xhr.send("product_id=" + productId + "&quantity=" + newQuantity + "&inserted_by=" + insertedBy);
}

// Function to update product price (on change of value)
document.querySelectorAll('.product-price').forEach(input => {
    input.addEventListener('change', function() {
        var productId = this.getAttribute('data-product-id');
        var newPrice = parseFloat(this.value);
        if (newPrice >= 0.01) {
            // Prompt the user to select who is entering the data
            var insertedBy = prompt("Enter your name (Owner, Assistant_1, Assistant_2):");
            if (insertedBy) {
                // Update the price in the database using AJAX
                updateProductPrice(productId, newPrice, insertedBy);
            }
        }
    });
});

// Function to update product price in the database
function updateProductPrice(productId, newPrice, insertedBy) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_price.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText); // Log success response
        }
    };
    xhr.send("product_id=" + productId + "&price=" + newPrice + "&inserted_by=" + insertedBy);
}
</script>
