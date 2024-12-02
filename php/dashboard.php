<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Welcome to Sari-Sari Store Permit and Inventory Management</h1>
    <div class="dashboard">
        <!-- Permit Management -->
        <div class="section green">
            <h2>Permit Management</h2>
            <a href="../php/view_permits.php" class="btn green">View Permits</a>
            <a href="../php/add_permit.php" class="btn green">Add Permit</a>
        </div>

        <!-- Inventory Management -->
        <div class="section purple">
            <h2>Inventory Management</h2>
            <a href="../php/view_products.php" class="btn purple">View Products</a>
            <a href="../php/add_product.php" class="btn purple">Add Product</a>
        </div>

        <!-- Sales Management -->
        <div class="section blue">
            <h2>Sales Management</h2>
            <a href="../php/view_sales.php" class="btn blue">View Sales</a>
            <a href="../php/add_sale.php" class="btn blue">Add Sale</a>
        </div>

        <!-- Logout Button -->
        <a href="logout.php" class="btn logout">Logout</a>
    </div>
</body>
</html>
