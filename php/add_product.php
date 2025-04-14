<?php
include('db_connection.php');

// Fetch categories for the dropdown menu
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

// Check for errors in the query execution
if (!$category_result) {
    die("Error in query: " . mysqli_error($conn));
}

$success = isset($_GET['success']) && $_GET['success'] === 'true';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category']);
    $unit_price = mysqli_real_escape_string($conn, $_POST['unit_price']);
    $stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);
    $minimum_stock = mysqli_real_escape_string($conn, $_POST['minimum_stock']);
    $product_image = null;

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image_tmp_name = $_FILES['product_image']['tmp_name'];
        $image_name = basename($_FILES['product_image']['name']);
        $image_path = ''. $image_name;

        if (move_uploaded_file($image_tmp_name, "../" . $image_path)) {
            $product_image = $image_path;
        }
    }

    // Insert new product into the database with minimum_stock
    $insert_sql = "INSERT INTO products (product_name, category_id, default_price, stock_quantity, product_image, minimum_stock) 
                   VALUES ('$product_name', '$category_id', '$unit_price', '$stock_quantity', '$product_image', '$minimum_stock')";
    
    if (mysqli_query($conn, $insert_sql)) {
        header("Location: add_product.php?success=true");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            color: #6a2e9d;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #6a2e9d;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #6a2e9d;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link a:hover {
            color: #4CAF50;
        }

        .alert {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Product</h2>

        <?php if ($success): ?>
            <div class="alert">Product added successfully!</div>
        <?php endif; ?>

        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" name="product_name" id="product_name" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    <?php while ($category_row = mysqli_fetch_assoc($category_result)): ?>
                        <option value="<?= $category_row['category_id'] ?>"><?= htmlspecialchars($category_row['category_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price</label>
                <input type="number" name="unit_price" id="unit_price" min="0.01" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" name="stock_quantity" id="stock_quantity" min="1" required>
            </div>

            <div class="form-group">
                <label for="minimum_stock">Minimum Stock</label>
                <input type="number" name="minimum_stock" id="minimum_stock" min="0" required>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image (Optional)</label>
                <input type="file" name="product_image" id="product_image">
            </div>

            <button type="submit">Add Product</button>
        </form>

        <div class="back-link">
            <a href="dashboard.php">Return to Dashboard</a>
        </div>
    </div>

</body>
</html>
