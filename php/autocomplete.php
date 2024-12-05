<?php
include('db_connection.php');

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Query to fetch matching product names, their default prices, and category names
    $sql = "SELECT p.product_name, p.default_price, c.category_name 
            FROM products p 
            INNER JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_name LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $param = "%$query%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'product_name' => $row['product_name'],
            'default_price' => $row['default_price'],
            'category_name' => $row['category_name']
        ];
    }

    // Return results as JSON
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
