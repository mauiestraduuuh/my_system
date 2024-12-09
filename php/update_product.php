<?php
include('db_connection.php');

// Fetch product details based on product_id
if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];
    $product_sql = "SELECT * FROM products WHERE product_id = $product_id";
    $product_result = mysqli_query($conn, $product_sql);

    if ($product_result && mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}

// Handle form submission for updating the product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $default_price = (float)$_POST['default_price'];
    $stock_quantity = (int)$_POST['stock_quantity'];

    // Handle file upload for product image
    $product_image = null;
    if (!empty($_FILES['product_image']['name'])) {
        $target_dir = "../images/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $product_image;

        // Move the uploaded file to the server's directory
        if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            die("Error uploading file.");
        }
    }

    // Update the product in the database
    $sql = "UPDATE products 
            SET product_name = '$product_name', 
                default_price = $default_price, 
                stock_quantity = $stock_quantity" . 
                ($product_image ? ", product_image = '$product_image'" : "") . 
            " WHERE product_id = $product_id";

    if (mysqli_query($conn, $sql)) {
        // Successful update, show the modal with success message
        echo "<script>
                window.onload = function() {
                    document.getElementById('updateModal').style.display = 'block';
                }
              </script>";
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #6a2e9d;
            font-size: 28px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            font-weight: bold;
            color: #6a2e9d;
        }

        input[type="text"], input[type="number"], input[type="file"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #6a2e9d;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #4CAF50;
        }

        a {
            color: #6a2e9d;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            color: #6a2e9d;
            margin-bottom: 10px;
        }

        .modal-body {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .modal-footer {
            text-align: center;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .button {
            padding: 10px 20px;
            background-color: #6a2e9d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Product Details</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>

            <label for="default_price">New Unit Price</label>
            <input type="number" id="default_price" name="default_price" value="<?= $product['default_price'] ?>" min="0.01" step="0.01" required>

            <label for="stock_quantity">New Stock Quantity</label>
            <input type="number" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" min="1" required>

            <label for="product_image">Product Image (optional)</label>
            <input type="file" id="product_image" name="product_image">

            <button type="submit">Update Product</button>
        </form>
        <br>
        <a href="restock_inventory.php">Return to Restock Inventory</a>
    </div>

    <!-- Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                Product Updated Successfully!
                <span class="close" onclick="document.getElementById('updateModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                The product details have been successfully updated. You can either go back to the restocking page or view the updated product.
            </div>
            <div class="modal-footer">
                <button class="button" onclick="window.location.href='restock_inventory.php'">Go Back to Restocking</button>
                <button class="button" onclick="window.location.href='view_products.php?'">View Updated Product</button>
            </div>
        </div>
    </div>

    <script>
        // Close the modal when the user clicks anywhere outside the modal
        window.onclick = function(event) {
            if (event.target == document.getElementById('updateModal')) {
                document.getElementById('updateModal').style.display = "none";
            }
        }
    </script>
</body>
</html>
