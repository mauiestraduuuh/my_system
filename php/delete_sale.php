<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];
    
    $sql = "DELETE FROM sales WHERE sale_id = $sale_id";

    if ($conn->query($sql) === TRUE) {
        // Notify user and redirect to the dashboard
        echo "<script>
                alert('Sale deleted successfully!');
                window.location.href = '../php/dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting sale: " . $conn->error . "');
                window.location.href = '../php/dashboard.php';
              </script>";
    }
}

$conn->close();
?>
