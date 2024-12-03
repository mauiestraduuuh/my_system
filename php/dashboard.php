<?php
include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT 
            SUM(CASE WHEN expiry_date > CURDATE() THEN 1 ELSE 0 END) AS active,
            SUM(CASE WHEN expiry_date <= CURDATE() AND expiry_date > DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS expiring_soon,
            SUM(CASE WHEN expiry_date <= CURDATE() THEN 1 ELSE 0 END) AS expired
        FROM permits";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$active = $row['active'];
$expiringSoon = $row['expiring_soon'];
$expired = $row['expired'];

$sql_inventory = "SELECT 
                    SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) AS in_stock,
                    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
                  FROM inventory";
$result_inventory = $conn->query($sql_inventory);
$row_inventory = $result_inventory->fetch_assoc();
$inStock = $row_inventory['in_stock'];
$outOfStock = $row_inventory['out_of_stock'];

$sql_sales = "SELECT 
                SUM(total_amount) AS total_sales,
                product_name,
                SUM(quantity) AS total_quantity_sold
              FROM sales
              GROUP BY product_name
              ORDER BY total_quantity_sold DESC
              LIMIT 5";
$result_sales = $conn->query($sql_sales);
$total_sales = 0;
$best_selling_products = [];
$product_sales = [];

if ($result_sales->num_rows > 0) {
    while ($row = $result_sales->fetch_assoc()) {
        $total_sales += $row['total_sales'];
        $best_selling_products[] = $row['product_name'];
        $product_sales[] = $row['total_quantity_sold'];
    }
}

$sql_sales_time = "SELECT 
                    DATE(sales_date) AS date, 
                    SUM(total_amount) AS daily_sales
                  FROM sales
                  WHERE sales_date >= CURDATE() - INTERVAL 30 DAY
                  GROUP BY DATE(sales_date)
                  ORDER BY DATE(sales_date)";
$result_sales_time = $conn->query($sql_sales_time);

$daily_sales_dates = [];
$daily_sales = [];

if ($result_sales_time->num_rows > 0) {
    while ($row = $result_sales_time->fetch_assoc()) {
        $daily_sales_dates[] = $row['date'];
        $daily_sales[] = $row['daily_sales'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Bahnschrift', sans-serif;
        }

        canvas {
            max-width: 600px;
            margin: 10px auto;
        }

        .dashboard-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-buttons a {
            margin: 10px;
            padding: 10px;
            text-decoration: none;
            color: white;
            background-color: #6A0DAD;
            border-radius: 5px;
            font-size: 16px;
        }

        .dashboard-buttons a.logout {
            background-color: #f44336;
        }

        .chart-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .chart-container h3 {
            font-size: 1.5em;
            color: #6A0DAD;
            margin-bottom: 10px;
        }

        .indicator-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }

        .indicator-container div {
            font-size: 1.1em;
            color: #4CAF50;
        }

        .indicator-container .expired {
            color: #F44336;
        }

        h1 {
            text-align: center;
            font-family: 'Bahnschrift', sans-serif;
            font-size: 2em;
            color: #6A0DAD;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to Sari-sari Store Permit and Inventory Management</h1>

    <div class="dashboard-buttons">
        <a href="../php/view_permits.php" class="btn">View Permits</a>
        <a href="../php/add_permit.php" class="btn">Add Permit</a>
        <a href="../php/view_products.php" class="btn">View Inventory</a>
        <a href="../php/add_product.php" class="btn">Add Product</a>
        <a href="../php/view_categories.php" class="btn">View Categories</a>
        <a href="../php/add_category.php" class="btn">Add Category</a>
        <a href="../php/view_sales.php" class="btn">View Sales</a>
        <a href="../php/add_sale.php" class="btn">Add Sale</a>
        <a href="../php/logout.php" class="logout">Logout</a>
    </div>

    <div class="chart-container">
        <h3>Permit Status</h3>
        <div class="indicator-container">
            <div>Active: <?php echo $active; ?></div>
            <div>Expiring Soon: <?php echo $expiringSoon; ?></div>
            <div class="expired">Expired: <?php echo $expired; ?></div>
        </div>
        <canvas id="permitChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Inventory Status</h3>
        <div class="indicator-container">
            <div>In Stock: <?php echo $inStock; ?></div>
            <div class="expired">Out of Stock: <?php echo $outOfStock; ?></div>
        </div>
        <canvas id="inventoryChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Sales Over Time (Last 30 Days)</h3>
        <canvas id="salesTimeChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Best Selling Products</h3>
        <canvas id="salesProductChart"></canvas>
    </div>

    <script>
        var ctxPermit = document.getElementById('permitChart').getContext('2d');
        var permitChart = new Chart(ctxPermit, {
            type: 'pie',
            data: {
                labels: ['Active', 'Expiring Soon', 'Expired'],
                datasets: [{
                    label: 'Permits Status',
                    data: [<?php echo $active; ?>, <?php echo $expiringSoon; ?>, <?php echo $expired; ?>],
                    backgroundColor: ['#6A0DAD', '#FFEB3B', '#F44336'],
                    borderColor: ['#fff', '#fff', '#fff'],
                    borderWidth: 1
                }]
            }
        });

        var ctxInventory = document.getElementById('inventoryChart').getContext('2d');
        var inventoryChart = new Chart(ctxInventory, {
            type: 'pie',
            data: {
                labels: ['In Stock', 'Out of Stock'],
                datasets: [{
                    label: 'Inventory Status',
                    data: [<?php echo $inStock; ?>, <?php echo $outOfStock; ?>],
                    backgroundColor: ['#4CAF50', '#F44336'],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 1
                }]
            }
        });

        var ctxSalesTime = document.getElementById('salesTimeChart').getContext('2d');
        var salesTimeChart = new Chart(ctxSalesTime, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($daily_sales_dates); ?>,
                datasets: [{
                    label: 'Daily Sales',
                    data: <?php echo json_encode($daily_sales); ?>,
                    borderColor: '#6A0DAD',
                    fill: false
                }]
            }
        });

        var ctxSalesProduct = document.getElementById('salesProductChart').getContext('2d');
        var salesProductChart = new Chart(ctxSalesProduct, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($best_selling_products); ?>,
                datasets: [{
                    label: 'Best Selling Products',
                    data: <?php echo json_encode($product_sales); ?>,
                    backgroundColor: '#FFEB3B',
                    borderColor: '#F44336',
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>
