<?php
include('db_connection.php');

// Fetch sale_id from URL
$sale_id = $_GET['sale_id'];

// Fetch the sale details to get product name (we will need this to prevent stock deduction)
$sql = "SELECT * FROM sales WHERE sale_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$sale = $result->fetch_assoc();

if ($sale) {
    $product_name = $sale['product_name'];
    $quantity = $sale['quantity'];
    // Do not deduct stock when deleting
    $delete_sql = "DELETE FROM sales WHERE sale_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $sale_id);
    
    if ($delete_stmt->execute()) {
        echo "<script>alert('Sale deleted successfully'); window.location.href = 'sales_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting sale'); window.location.href = 'sales_list.php';</script>";
    }
} else {
    echo "<script>alert('Sale not found'); window.location.href = 'sales_list.php';</script>";
}

$conn->close();
?>
