<?php
include('db_connection.php');

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if ($startDate && $endDate) {
    $sql = "SELECT 
                DATE(sales_date) AS date, 
                SUM(total_amount) AS daily_sales
            FROM sales
            WHERE sales_date BETWEEN ? AND ?
            GROUP BY DATE(sales_date)
            ORDER BY DATE(sales_date)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data['dates'][] = $row['date'];
        $data['sales'][] = $row['daily_sales'];
    }

    echo json_encode($data);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid date range']);
}
$conn->close();
?>