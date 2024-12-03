<?php
include('db_connection.php');

// Fetch data from the categories table
$sql = "SELECT * FROM categories";
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
        $category_id = $row['id'];
        $category_name = $row['name'];

        echo "<tr>
        <td>$category_id</td>
        <td>$category_name</td>
        <td><a href='delete_category.php?category_id=$category_id' onclick=\"return confirm('Are you sure you want to delete this category?');\">Delete</a></td>
        </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No categories found.</p>";
}

mysqli_close($conn);
?>
