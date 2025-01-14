<?php
// Include TCPDF
require_once('../tcpdf/tcpdf.php');

// Get the start and end dates from the GET request (if provided)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Create a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Fetching Sales Data
$conn = new mysqli("localhost", "root", "", "system_db");

// --- Sales Data ---
$query_sales = "SELECT product_name, sales_date, quantity, price, (quantity * price) AS total_amount FROM sales";

// Apply date range filter to sales if required
if ($start_date && $end_date) {
    $query_sales .= " WHERE sales_date BETWEEN '$start_date' AND '$end_date'";
}

$result_sales = $conn->query($query_sales);
if ($result_sales === false) {
    die('Error in query: ' . $conn->error);
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
$pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');

// Add Date Range to Title (if dates are provided)
if ($start_date && $end_date) {
    $pdf->Cell(0, 10, "Report from $start_date to $end_date", 0, 1, 'C');
}

// --- Sales Section ---
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Sales Data', 0, 1, 'L');
$pdf->Cell(40, 10, 'Product Name', 1);
$pdf->Cell(40, 10, 'Sales Date', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(40, 10, 'Price', 1);
$pdf->Cell(40, 10, 'Total Amount', 1);
$pdf->Ln();

foreach ($sales_data as $sale) {
    $pdf->Cell(40, 10, $sale['product_name'], 1);
    $pdf->Cell(40, 10, $sale['sales_date'], 1);
    $pdf->Cell(40, 10, $sale['quantity'], 1);
    $pdf->Cell(40, 10, $sale['price'], 1);
    $pdf->Cell(40, 10, $sale['total_amount'], 1);
    $pdf->Ln();
}

// Add Total Sales
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Total Sales: ' . number_format($total_sales, 2), 0, 1, 'L');

// Add Best Selling Product
$pdf->Cell(0, 10, 'Best Selling Product: ' . $best_selling_product, 0, 1, 'L');

// Output PDF
$pdf->Output('sales_report.pdf', 'D'); // 'D' forces download
?>
