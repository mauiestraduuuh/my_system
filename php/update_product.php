<?php
include('db_connection.php');

if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    // Fetch the product details from the database
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    if ($product = mysqli_fetch_assoc($result)) {
        // Display a styled form pre-filled with the product data for editing
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Update Product</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #6a2e9d;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: #fff;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            color: #6a2e9d;
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }
        input[type='text'], input[type='number'], input[type='file'] {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #6a2e9d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4CAF50;
        }
        .cancel-btn {
            background-color: #ff3385;
            margin-left: 10px;
        }
        .cancel-btn:hover {
            background-color: #e6005c;
        }
        .form-footer {
            text-align: center;
        }
        .form-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .form-footer a:hover {
            color: #6a2e9d;
        }
    </style>
</head>
<body>
    <h2>Update Product</h2>
    <div class='form-container'>
        <form action='update_product_submit.php' method='POST' enctype='multipart/form-data'>
            <input type='hidden' name='product_id' value='{$product['product_id']}'>
            <label for='product_name'>Product Name:</label>
            <input type='text' id='product_name' name='product_name' value='{$product['product_name']}' required>
            
            <label for='default_price'>Unit Price:</label>
            <input type='number' id='default_price' name='default_price' step='0.01' value='{$product['default_price']}' required>
            
            <label for='stock_quantity'>Quantity:</label>
            <input type='number' id='stock_quantity' name='stock_quantity' value='{$product['stock_quantity']}' required>
            
            <label for='product_image'>Product Image:</label>
            <input type='file' id='product_image' name='product_image'>
            
            <div style='text-align: center;'>
                <button type='submit'>Save Changes</button>
                <a href='restock_inventory.php'><button type='button' class='cancel-btn'>Cancel</button></a>
            </div>
        </form>
    </div>
    <div class='form-footer'>
        <a href='restock_inventory.php'>Back to Inventory</a>
    </div>
</body>
</html>";
    } else {
        echo "Product not found!";
    }
} else {
    echo "No product ID provided!";
}

mysqli_close($conn);
?>
