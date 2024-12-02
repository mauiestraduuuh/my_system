<?php
include('db_connection.php');

// Fetch data from the permits table
$sql = "SELECT * FROM permits";
$result = mysqli_query($conn, $sql);

echo "<h2>Permits List</h2>";
echo '<a href="../html/dashboard.html">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>
    <tr>
        <th>Permit ID</th>
        <th>Permit Name</th>
        <th>Issue Date</th>
        <th>Expiry Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $permit_id = $row['permit_id'];
        $permit_name = $row['permit_name'];
        $issue_date = $row['issue_date'];
        $expiry_date = $row['expiry_date'];
        
        $status = 'Expired';
        $current_date = date("Y-m-d");
        $expiring_soon_date = date("Y-m-d", strtotime("+30 days"));

        if ($expiry_date > $current_date) {
            $status = 'Active';
        } elseif ($expiry_date <= $expiring_soon_date && $expiry_date > $current_date) {
            $status = 'Expiring Soon';
        }

        echo "<tr>
        <td>$permit_id</td>
        <td>$permit_name</td>
        <td>$issue_date</td>
        <td>$expiry_date</td>
        <td>$status</td>
        <td><a href='../php/delete_permit.php?permit_id=$permit_id' onclick=\"return confirm('Are you sure you want to delete this permit?');\">Delete</a></td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No permits found.</p>";
}

mysqli_close($conn);
?>
