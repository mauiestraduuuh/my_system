<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $unit_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    
    $sql = "INSERT INTO inventory (product_name, unit_price, quantity, expiry_date) 
            VALUES ('$product_name', '$unit_price', '$quantity', '$expiry_date')";

    if ($conn->query($sql) === TRUE) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<form method="POST">
    <label>Product Name:</label>
    <input type="text" name="product_name" required><br>
    <label>Unit Price:</label>
    <input type="number" name="unit_price" required><br>
    <label>Quantity:</label>
    <input type="number" name="quantity" required><br>
    <label>Expiry Date:</label>
    <input type="date" name="expiry_date" required><br>
    <input type="submit" value="Add Product">
</form>
