<?php
// Include TCPDF
require_once('../tcpdf/tcpdf.php');

// Get the start and end dates from the GET request (if provided)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Get the current date for the "as of" statement
$current_date = date('Y-m-d');

// Create a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Database Connection
$conn = new mysqli("localhost", "root", "", "system_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Inventory Data ---
$query_inventory = "SELECT product_name, stock_quantity, default_price FROM products";
$result_inventory = $conn->query($query_inventory);
if ($result_inventory === false) {
    die('Error in inventory query: ' . $conn->error);
}

$inventory_data = [];
while ($row = $result_inventory->fetch_assoc()) {
    $inventory_data[] = $row;
}

// --- Sales Data ---
$query_sales = "SELECT product_name, sales_date, quantity, price, (quantity * price) AS total_amount FROM sales";

// Apply date range filter to sales if required
if ($start_date && $end_date) {
    $query_sales .= " WHERE sales_date BETWEEN '$start_date' AND '$end_date'";
}

$result_sales = $conn->query($query_sales);
if ($result_sales === false) {
    die('Error in sales query: ' . $conn->error);
}

$sales_data = [];
$total_sales = 0;
$product_sales = [];

while ($row = $result_sales->fetch_assoc()) {
    $sales_data[] = $row;
    $total_sales += $row['total_amount'];

    // Track total sales per product
    if (isset($product_sales[$row['product_name']])) {
        $product_sales[$row['product_name']] += $row['total_amount'];
    } else {
        $product_sales[$row['product_name']] = $row['total_amount'];
    }
}

// Find best selling product
$best_selling_product = array_keys($product_sales, max($product_sales))[0];

// Add Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Sales and Inventory Report', 0, 1, 'C');

// Add Date Range or Current Date
$pdf->SetFont('helvetica', '', 12);
if ($start_date && $end_date) {
    $pdf->Cell(0, 10, "For the period of $start_date to $end_date", 0, 1, 'C');
} else {
    $pdf->Cell(0, 10, "As of $current_date", 0, 1, 'C');
}

// --- Inventory Section ---
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Inventory Data (as of $current_date)", 0, 1, 'C');
$pdf->Ln(5);

// Add table header for inventory
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(70, 10, 'Product Name', 1, 0, 'C');
$pdf->Cell(50, 10, 'Stock Quantity', 1, 0, 'C');
$pdf->Cell(50, 10, 'Price', 1, 1, 'C');

// Add inventory data rows
$pdf->SetFont('helvetica', '', 12);
foreach ($inventory_data as $inventory) {
    $pdf->Cell(70, 10, $inventory['product_name'], 1, 0, 'C');
    $pdf->Cell(50, 10, $inventory['stock_quantity'], 1, 0, 'C');
    $pdf->Cell(50, 10, number_format($inventory['default_price'], 2), 1, 1, 'C');
}

// --- Sales Section ---
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
if ($start_date && $end_date) {
    $pdf->Cell(0, 10, "Sales Data (for the period of $start_date to $end_date)", 0, 1, 'C');
} else {
    $pdf->Cell(0, 10, "Sales Data", 0, 1, 'C');
}
$pdf->Ln(5);

// Add table header for sales
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(50, 10, 'Product Name', 1, 0, 'C');
$pdf->Cell(40, 10, 'Sales Date', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(30, 10, 'Price', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total Amount', 1, 1, 'C');

// Add sales data rows
$pdf->SetFont('helvetica', '', 12);
foreach ($sales_data as $sale) {
    $pdf->Cell(50, 10, $sale['product_name'], 1, 0, 'C');
    $pdf->Cell(40, 10, $sale['sales_date'], 1, 0, 'C');
    $pdf->Cell(30, 10, $sale['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($sale['price'], 2), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($sale['total_amount'], 2), 1, 1, 'C');
}

// Add Total Sales
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Total Sales: ' . number_format($total_sales, 2), 0, 1, 'C');

// Add Best Selling Product
$pdf->Cell(0, 10, 'Best Selling Product: ' . $best_selling_product, 0, 1, 'C');

// Output PDF
$pdf->Output('sales_and_inventory_report.pdf', 'D'); // 'D' forces download
?>
