<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $default_price = (float)$_POST['default_price'];
    $stock_quantity = (int)$_POST['stock_quantity'];

    // Handle file upload
    $product_image = null;
    if (!empty($_FILES['product_image']['name'])) {
        $target_dir = "../images/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $product_image;

        // Move the uploaded file to the server's directory
        if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            die("Error uploading file.");
        }
    }

    // Update the product in the database
    $sql = "UPDATE products 
            SET product_name = '$product_name', 
                default_price = $default_price, 
                stock_quantity = $stock_quantity" .
                ($product_image ? ", product_image = '$product_image'" : "") .
            " WHERE product_id = $product_id";

    if (mysqli_query($conn, $sql)) {
        echo "Product updated successfully!";
        header("Location: restock_inventory.php"); // Redirect back to the inventory page
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request!";
}

mysqli_close($conn);
?>
