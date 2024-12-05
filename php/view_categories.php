<?php
include('db_connection.php');

// Fetch data from the categories table
$sql = "SELECT category_id, category_name FROM categories"; // Use actual column names from your database
$result = mysqli_query($conn, $sql);

echo "<h2>Categories List</h2>";
echo '<a href="dashboard.php">Return to Dashboard</a><br><br>';

if (mysqli_num_rows($result) > 0) {
    echo "<style>
    body { font-family: 'Bahnschrift Condensed', sans-serif; }
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        background-color: #fff;
    }
    .styled-table thead { background-color: #6a2e9d; color: #fff; }
    .styled-table th, .styled-table td { padding: 10px; border: 1px solid #ddd; }
    .styled-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
    .styled-table tbody tr:hover { background-color: #e0e0e0; }
    .styled-table td a { color: #ff3385; text-decoration: none; }
    .styled-table td a:hover { color: #4CAF50; }
    h2 { color: #6a2e9d; font-size: 28px; text-align: center; margin-top: 20px; }
    a { color: #4CAF50; font-size: 16px; text-decoration: none; margin: 10px; }
    a:hover { color: #6a2e9d; }
    </style>";

    echo "<table class='styled-table'>
    <thead>
        <tr>
            <th>Category ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        // Access column names based on the actual database schema
        $category_id = $row['category_id'];
        $category_name = $row['category_name'];

        echo "<tr>
        <td>" . htmlspecialchars($category_id) . "</td>
        <td>" . htmlspecialchars($category_name) . "</td>
        <td><a href='delete_category.php?category_id=" . htmlspecialchars($category_id) . "' onclick=\"return confirm('Are you sure you want to delete this category?');\">Delete</a></td>
        </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No categories found.</p>";
}

mysqli_close($conn);
?>
