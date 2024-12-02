<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $sales_date = $_POST['sales_date'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    $total_amount = $quantity * $price;

    $sql = "INSERT INTO sales (product_name, sales_date, quantity, price, total_amount) 
            VALUES ('$product_name', '$sales_date', '$quantity', '$price', '$total_amount')";

    if ($conn->query($sql) === TRUE) {
        echo "Sale added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<form method="POST">
    <label>Product Name:</label>
    <input type="text" name="product_name" required><br>
    <label>Sales Date:</label>
    <input type="date" name="sales_date" required><br>
    <label>Quantity:</label>
    <input type="number" name="quantity" required><br>
    <label>Price:</label>
    <input type="number" name="price" required><br>
    <input type="submit" value="Add Sale">
</form>
