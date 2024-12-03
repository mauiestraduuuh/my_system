<?php
include('db_connection.php');

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    
    $sql = "SELECT product_name, default_price FROM products WHERE product_name LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $suggestions = [];
    
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    
    echo json_encode($suggestions);
}
?>
