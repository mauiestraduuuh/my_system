<?php
include('db_connection.php');

// Check if the form is submitted to add a new permit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $permit_name = $_POST['permit_name'];
    $issue_date = $_POST['issue_date'];
    $expiry_date = $_POST['expiry_date'];

    // Insert the permit into the database
    $sql = "INSERT INTO permits (permit_name, issue_date, expiry_date)
            VALUES ('$permit_name', '$issue_date', '$expiry_date')";

    if (mysqli_query($conn, $sql)) {
        echo "New permit added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Permit</title>
</head>
<body>
    <h1>Add Permit</h1>
    <form method="POST" action="">
        <label for="permit_name">Permit Name:</label>
        <input type="text" id="permit_name" name="permit_name" required><br><br>
        
        <label for="issue_date">Issue Date:</label>
        <input type="date" id="issue_date" name="issue_date" required><br><br>

        <label for="expiry_date">Expiry Date:</label>
        <input type="date" id="expiry_date" name="expiry_date" required><br><br>

        <input type="submit" value="Add Permit">
    </form>
    
    <br><br>
    <a href="view_permits.php">View Permits</a> <!-- Link to view permits -->
</body>
</html>
