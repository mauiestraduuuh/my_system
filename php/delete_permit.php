<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['permit_id'])) {
    $permit_id = $_GET['permit_id'];
    
    $sql = "DELETE FROM permits WHERE permit_id = $permit_id";

    if ($conn->query($sql) === TRUE) {
        // Notify user and redirect to the dashboard
        echo "<script>
                alert('Permit deleted successfully!');
                window.location.href = '../php/dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting permit: " . $conn->error . "');
                window.location.href = '../php/dashboard.php';
              </script>";
    }
}

$conn->close();
?>
