<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    $sql = "DELETE FROM inventory WHERE product_id = $product_id";

    if ($conn->query($sql) === TRUE) {
        // Notify user and redirect to the dashboard
        echo "<script>
                alert('Product deleted successfully!');
                window.location.href = '../php/dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting product: " . $conn->error . "');
                window.location.href = '../php/dashboard.php';
              </script>";
    }
}

$conn->close();
?>
