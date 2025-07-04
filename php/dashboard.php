<?php
include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get permit status
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

// Get inventory status
$sql_inventory = "SELECT 
                    SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) AS in_stock,
                    SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
                  FROM products";
$result_inventory = $conn->query($sql_inventory);
$row_inventory = $result_inventory->fetch_assoc();
$inStock = $row_inventory['in_stock'] ?? 0;
$outOfStock = $row_inventory['out_of_stock'] ?? 0;

// Get sales data
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

// Get total expenses
$sql_expenses = "SELECT SUM(amount) AS total_expenses FROM expenses";
$result_expenses = $conn->query($sql_expenses);
$row_expenses = $result_expenses->fetch_assoc();
$total_expenses = $row_expenses['total_expenses'] ?? 0;

// Calculate net profit
$net_profit = $total_sales - $total_expenses;

// Get sales data for the last 30 days
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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .dashboard-buttons a {
            flex: 1 1 200px;
            max-width: 200px;
            padding: 10px;
            text-align: center;
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
        #customDates {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
        #customDates input {
            margin: 5px;
            padding: 5px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <h1>Welcome to Sari-sari Store Permit and Inventory Management</h1>

    <div class="dashboard-buttons">
        <a href="../php/view_permits.php" class="btn">View Permits</a>
        <a href="../php/add_permit.php" class="btn">Add Permit</a>
        <a href="../php/view_products.php" class="btn">View Inventory</a>
        <a href="../php/view_sales.php" class="btn">View Sales</a>
        <a href="../php/add_sale.php" class="btn">Add Sale</a>
        <a href="../php/restock_inventory.php" class="btn">Restock</a>
        <a href="../php/transaction.php" class="btn">Transaction History</a>
        <a href="../php/expenses.php" class="btn">Expenses</a>
        <a href="../php/assistant_performance.php" class="btn">Assistant Performance</a>
        <a href="../php/add_customer.php" class="btn">Add Customer</a>
        <a href="../php/add_utang.php" class="btn">Add Utang</a>
        <a href="../php/utang.php" class="btn">Pay Utang</a>
        <a href="../php/utang_history.php" class="btn">Utang History</a>
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

    <div class="dashboard-buttons">
        <label for="timeRange">Select Time Range:</label>
        <select id="timeRange" onchange="updateSalesChart()">
            <option value="today">Today</option>
            <option value="week">Last 7 Days</option>
            <option value="month" selected>Last 30 Days</option>
            <option value="custom">Custom Date Range</option>
        </select>
    </div>

    <div id="customDates">
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate" required>
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate" required>
        <button type="button" onclick="applyCustomDateRange()">Apply</button>
    </div>

    <div class="chart-container">
        <h3>Sales Over Time (Last 30 Days)</h3>
        <canvas id="salesTimeChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Best Selling Products</h3>
        <canvas id="salesProductChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Net Profit</h3>
        <div>Total Sales: ₱<?php echo number_format($total_sales, 2); ?></div>
        <div>Total Expenses: ₱<?php echo number_format($total_expenses, 2); ?></div>
        <div><strong>Net Profit: ₱<?php echo number_format($net_profit, 2); ?></strong></div>
    </div>

    <form action="pdf_report.php" method="GET" style="text-align: center; margin-top: 20px;">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required style="padding: 5px; margin: 10px; font-size: 1em; border-radius: 5px; border: 1px solid #ccc;">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required style="padding: 5px; margin: 10px; font-size: 1em; border-radius: 5px; border: 1px solid #ccc;">
        <button type="submit" style="
            margin: 10px;
            padding: 10px 20px;
            background-color: #6A0DAD;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Bahnschrift', sans-serif;
            cursor: pointer;
        ">
            Generate Report
        </button>
    </form>

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
                    label: 'Sales',
                    data: <?php echo json_encode($daily_sales); ?>,
                    borderColor: '#FF9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.2)',
                    fill: true,
                    borderWidth: 2
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
                    backgroundColor: '#4CAF50',
                    borderColor: '#388E3C',
                    borderWidth: 1
                }]
            }
        });

        function updateSalesChart() {
            var timeRange = document.getElementById('timeRange').value;
            if (timeRange === 'custom') {
                document.getElementById('customDates').style.display = 'block';
            } else {
                document.getElementById('customDates').style.display = 'none';
                // Implement logic to update chart based on the selected time range (today, week, month)
            }
        }

        function applyCustomDateRange() {
            var startDate = document.getElementById('startDate').value;
            var endDate = document.getElementById('endDate').value;
            if (startDate && endDate) {
                // Apply custom date range and update chart
            }
        }
    </script>
</body>
</html>
