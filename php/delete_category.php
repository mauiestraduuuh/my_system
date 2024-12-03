<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    $sql = "DELETE FROM categories WHERE id = $category_id";

    if ($conn->query($sql) === TRUE) {
        // Notify user and redirect to the categories view page
        echo "<script>
                alert('Category deleted successfully!');
                window.location.href = 'view_categories.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting category: " . $conn->error . "');
                window.location.href = 'view_categories.php';
              </script>";
    }
}

$conn->close();
?>
