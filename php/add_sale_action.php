<?php
include('db_connection.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$success = false; // To determine the type of message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['product_name'], $_POST['unit_price'], $_POST['quantity'], $_POST['sales_date'])) {
        $message = "Missing required form data.";
    } else {
        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $unit_price = mysqli_real_escape_string($conn, $_POST['unit_price']);
        $quantity_sold = mysqli_real_escape_string($conn, $_POST['quantity']);
        $sales_date = mysqli_real_escape_string($conn, $_POST['sales_date']);

        $product_query = "SELECT product_id, stock_quantity FROM products WHERE product_name = '$product_name'";
        $product_result = mysqli_query($conn, $product_query);

        if (mysqli_num_rows($product_result) > 0) {
            $product_row = mysqli_fetch_assoc($product_result);
            $product_id = $product_row['product_id'];
            $product_stock_quantity = $product_row['stock_quantity'];

            if ($product_stock_quantity >= $quantity_sold) {
                $new_stock_quantity = $product_stock_quantity - $quantity_sold;

                $update_query = "UPDATE products SET stock_quantity = $new_stock_quantity WHERE product_id = $product_id";
                if (mysqli_query($conn, $update_query)) {
                    $total_amount = $unit_price * $quantity_sold;
                    $user_id = $_SESSION['user_id'];

                    $insert_sale_query = "INSERT INTO sales (product_name, quantity, total_amount, sales_date) 
                                          VALUES ('$product_name', '$quantity_sold', '$total_amount', '$sales_date')";
                    if (mysqli_query($conn, $insert_sale_query)) {
                        $transaction_query = "INSERT INTO transactions (transaction_type, product_id, quantity, total_amount, user_id) 
                                              VALUES ('Sale', '$product_id', '$quantity_sold', '$total_amount', '$user_id')";
                        if (mysqli_query($conn, $transaction_query)) {
                            $message = "Sale added and transaction recorded successfully!";
                            $success = true;
                        } else {
                            $message = "Error inserting transaction record: " . mysqli_error($conn);
                        }
                    } else {
                        $message = "Error inserting sale record: " . mysqli_error($conn);
                    }
                } else {
                    $message = "Error updating product stock: " . mysqli_error($conn);
                }
            } else {
                $message = "Not enough stock available!";
            }
        } else {
            $message = "Product not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Status</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
        }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-content h2 {
            margin: 0 0 20px;
            color: <?php echo $success ? '#28a745' : '#dc3545'; ?>;
        }
        .modal-content p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .modal-content button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-ok {
            background: #6A0DAD;
            color: white;
        }
        .btn-ok:hover {
            background: #5c0e9f;
        }
        .btn-add-more {
            background: #28a745;
            color: white;
        }
        .btn-add-more:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <h2><?php echo $success ? 'Success!' : 'Error!'; ?></h2>
            <p><?php echo $message; ?></p>
            <button class="btn-ok" onclick="window.location.href='view_products.php'">View Inventroy</button>
            <button class="btn-add-more" onclick="window.location.href='add_sale.php'">Add More Sale</button>
        </div>
    </div>
</body>
</html>
