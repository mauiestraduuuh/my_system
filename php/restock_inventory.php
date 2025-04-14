<?php
include('db_connection.php');
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // The logged-in user
} else {
    header("Location: login.php");
    exit();
}

// Check if an update was successful
$update_success = isset($_GET['update']) && $_GET['update'] === 'success';

// Fetch all categories
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

// Check for errors in the query execution
if (!$category_result) {
    die("Error in query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock Inventory</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #333;
        }

        .container {
            max-width: 1200px;
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

        a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        a:hover {
            color: #6a2e9d;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            background-color: #fff;
        }

        .styled-table thead {
            background-color: #6a2e9d;
            color: #fff;
        }

        .styled-table th,
        .styled-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:hover {
            background-color: #e0e0e0;
        }

        .tooltip {
            position: relative;
            cursor: pointer;
        }

        .tooltip:hover .tooltip-image {
            display: block;
        }

        .tooltip-image {
            display: none;
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 5px;
            z-index: 10;
            max-width: 120px;
        }

        .tooltip-image img {
            max-width: 100%;
            border-radius: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-content h3 {
            color: #6a2e9d;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .modal-content p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .modal-content button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .modal-content button:hover {
            background-color: #6a2e9d;
        }

        .btn-prd {
            position: absolute;
            top: 75px;
            right: 175px;
            background-color: #45a049;
            color: white;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-prd:hover {
            background-color: #6a2e9d;
        }

        .low-stock {
            background-color: #fff3cd; /* light yellow */
        }

        .out-of-stock {
            background-color: #f8d7da; /* light red */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Restock Products List by Category</h2>
        <a href="dashboard.php">Return to Dashboard</a><br><br>
        <a href="add_product.php" class="btn-prd">Add Product</a>
        <?php if (mysqli_num_rows($category_result) > 0): ?>
            <?php while ($category_row = mysqli_fetch_assoc($category_result)): ?>
                <h3><?= htmlspecialchars($category_row['category_name']) ?></h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Unit Price</th>
                            <th>Stock Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $category_id = $category_row['category_id'];
                        $product_sql = "SELECT * FROM products WHERE category_id = $category_id";
                        $product_result = mysqli_query($conn, $product_sql);

                        if (!$product_result) {
                            die("Error in query: " . mysqli_error($conn));
                        }

                        if (mysqli_num_rows($product_result) > 0):
                            while ($product_row = mysqli_fetch_assoc($product_result)):
                                $product_id = $product_row['product_id'];
                                $product_name = htmlspecialchars($product_row['product_name']);
                                $unit_price = $product_row['default_price'];
                                $stock_quantity = $product_row['stock_quantity'];
                                $minimum_stock = $product_row['minimum_stock'];
                                $image_path = $product_row['product_image'];
                                $product_image = $image_path ? "<img src='../images/$image_path' alt='$product_name'>" : "No Image";

                                $stock_status_class = '';
                                $stock_note = '';
                                if ($stock_quantity == 0) {
                                    $stock_status_class = 'out-of-stock';
                                    $stock_note = 'Out of Stock!';
                                } elseif ($stock_quantity < $minimum_stock) {
                                    $stock_status_class = 'low-stock';
                                    $stock_note = 'Low Stock!';
                                }
                        ?>
                            <tr class="<?= $stock_status_class ?>">
                                <td><?= $product_id ?></td>
                                <td class="tooltip">
                                    <?= $product_name ?>
                                    <?php if ($image_path): ?>
                                        <div class="tooltip-image">
                                            <img src="../images/<?= htmlspecialchars($image_path) ?>" alt="<?= $product_name ?>">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input class="product-price" type="number" value="<?= $unit_price ?>" min="0.01" step="0.01" data-product-id="<?= $product_id ?>">
                                </td>
                                <td>
                                    <span id="quantity-<?= $product_id ?>"><?= $stock_quantity ?></span>
                                    <?php if ($stock_note): ?>
                                        <span style="color: red; font-weight: bold;"> - <?= $stock_note ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><a href="update_product.php?product_id=<?= $product_id ?>">Update</a></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No products found in this category.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h3>Update Successful!</h3>
            <p>The product inventory has been successfully updated.</p>
            <button onclick="redirectToProducts()">Go to Products List</button>
        </div>
    </div>

    <script>
        const updateSuccess = <?php echo json_encode($update_success); ?>;
        if (updateSuccess) {
            document.getElementById('successModal').style.display = 'flex';
        }

        function redirectToProducts() {
            window.location.href = 'view_products.php';
        }
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>
