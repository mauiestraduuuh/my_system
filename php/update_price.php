<?php
include('db_connection.php');

if (isset($_POST['product_id']) && isset($_POST['price'])) {
    $product_id = (int)$_POST['product_id'];
    $price = (float)$_POST['price
