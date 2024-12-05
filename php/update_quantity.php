<?php
include('db_connection.php');

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    $update_sql = "UPDATE products SET stock_quantity = $quantity WHERE product_id = $product_id";
    if (mysqli_query($conn, $update_sql)) {
        echo "Quantity updated successfully!";
    } else {
        echo "Error updating quantity: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
