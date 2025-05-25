<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utang_id = $_POST['utang_id'];
    $payment_status = $_POST['payment_status'];
    $paid_by = $_POST['paid_by'];
    $rating = $_POST['rating'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    // Get original utang amount
    $getAmountQuery = "SELECT utang_amount FROM utang WHERE utang_id = ?";
    $stmt = $conn->prepare($getAmountQuery);
    $stmt->bind_param("i", $utang_id);
    $stmt->execute();
    $stmt->bind_result($utang_amount);
    $stmt->fetch();
    $stmt->close();

    if ($payment_status === 'Paid') {
        $paid_amount = $utang_amount;
    } elseif ($payment_status === 'Partially Paid') {
        $paid_amount = floatval($_POST['paid_amount']);
        if ($paid_amount <= 0 || $paid_amount >= $utang_amount) {
            echo "<script>alert('Invalid partial payment amount.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Invalid payment status.'); window.history.back();</script>";
        exit;
    }

    if ($rating === 'Bad' && empty($reason)) {
        echo "<script>alert('Reason is required for a bad payment rating.'); window.history.back();</script>";
        exit;
    }

    $updateQuery = "UPDATE utang 
                    SET payment_status = ?, 
                        paid_amount = ?, 
                        payment_date = NOW(), 
                        paid_by = ?, 
                        rating = ?, 
                        reason = ?
                    WHERE utang_id = ?";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sdsssi", $payment_status, $paid_amount, $paid_by, $rating, $reason, $utang_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment recorded successfully!'); window.location.href='utang_history.php';</script>";
    } else {
        echo "<script>alert('Error recording payment.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>
