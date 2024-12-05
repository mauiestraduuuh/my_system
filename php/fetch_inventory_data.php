<?php
include('db_connection.php');

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Calculate In Stock and Out of Stock products
    $sql = "SELECT 
                SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) AS in_stock,
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
            FROM products
            WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode([
        'in_stock' => $data['in_stock'] ?? 0,
        'out_of_stock' => $data['out_of_stock'] ?? 0
    ]);
}
?>
