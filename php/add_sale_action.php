<?php
include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form data exists
    if (!isset($_POST['product_name']) || !isset($_POST['unit_price']) || !isset($_POST['quantity']) || !isset($_POST['sales_date'])) {
        die("Missing required form data.");
    }

    // Get form data
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $unit_price = mysqli_real_escape_string($conn, $_POST['unit_price']);
    $quantity_sold = mysqli_real_escape_string($conn, $_POST['quantity']); // Quantity sold
    $sales_date = mysqli_real_escape_string($conn, $_POST['sales_date']);

    // Get product details from the products table based on product_name
    $product_query = "SELECT product_id, stock_quantity FROM products WHERE product_name = '$product_name'";
    $product_result = mysqli_query($conn, $product_query);

    if (mysqli_num_rows($product_result) > 0) {
        // Fetch product details
        $product_row = mysqli_fetch_assoc($product_result);
        $product_id = $product_row['product_id'];
        $product_stock_quantity = $product_row['stock_quantity'];

        // Check if there is enough stock for the sale
        if ($product_stock_quantity >= $quantity_sold) {
            // Calculate new stock quantity
            $new_stock_quantity = $product_stock_quantity - $quantity_sold;

            // Update product stock in the products table
            $update_query = "UPDATE products SET stock_quantity = $new_stock_quantity WHERE product_id = $product_id";
            if (mysqli_query($conn, $update_query)) {
                // Calculate total amount for the sale
                $total_amount = $unit_price * $quantity_sold;

                // Get the logged-in user ID
                $user_id = $_SESSION['user_id']; // Ensure this is set during login, based on your session management

                // Insert sale record into the sales table
                $insert_sale_query = "INSERT INTO sales (product_name, sale_id, quantity, total_amount, sales_date) 
                                      VALUES ('$product_name', '$sale_id', '$quantity_sold', '$total_amount', '$sales_date')";
                if (mysqli_query($conn, $insert_sale_query)) {
                    // Insert transaction record into the transactions table
                    $transaction_query = "INSERT INTO transactions (transaction_type, product_id, quantity, total_amount, user_id) 
                                          VALUES ('Sale', '$product_id', '$quantity_sold', '$total_amount', '$user_id')";
                    if (mysqli_query($conn, $transaction_query)) {
                        // Success message and redirection
                        echo "<script>
                                alert('Sale added and transaction recorded successfully!');
                                window.location.href = 'view_products.php';
                              </script>";
                    } else {
                        echo "Error inserting transaction record: " . mysqli_error($conn);
                    }
                } else {
                    echo "Error inserting sale record: " . mysqli_error($conn);
                }

            } else {
                echo "Error updating product stock: " . mysqli_error($conn);
            }
        } else {
            echo "Not enough stock available!";
        }
    } else {
        echo "Product not found!";
    }
}
?>
