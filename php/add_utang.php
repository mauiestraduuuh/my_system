<?php
// Connect to the database
include('db_connection.php');

// Fetch registered customers
$query = "SELECT id, name FROM customers ORDER BY name ASC";
$result = mysqli_query($conn, $query);

// Check if there was an error in the query
if (!$result) {
    die("Error fetching customer list: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Utang (Credit)</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #6A0DAD;
            font-size: 2.5em;
            margin-top: 20px;
        }
        .form-container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container label {
            margin-bottom: 8px;
            font-size: 1.2em;
            color: #333;
        }
        .form-container input, .form-container select {
            padding: 10px;
            font-size: 1em;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .form-container input:focus, .form-container select:focus {
            border-color: #6A0DAD;
        }
        .form-container button {
            padding: 10px;
            background-color: #6A0DAD;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
        }
        .form-container button:hover {
            background-color: #5c0e9f;
        }
        .btn-dashboard {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #45a049;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-dashboard:hover {
            background-color: #5c0e9f;
        }
    </style>
</head>
<body>
<a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>

    <h1>Record Utang (Credit)</h1>
    <div class="form-container">
        <form method="POST" action="add_utang_action.php" onsubmit="return validateUtangForm()">
            <label for="customer_id">Select Customer</label>
            <select id="customer_id" name="customer_id" required>
                <option value="">--Select Customer--</option>
                <?php
                // Fetch customers from the result set and populate dropdown
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                } else {
                    echo "<option value=''>No customers found</option>";
                }
                ?>
            </select>

            <label for="utang_amount">Utang Amount</label>
            <input type="number" step="0.01" id="utang_amount" name="utang_amount" required>

            <button type="submit">Add Utang</button>
        </form>
    </div>

    <script>
        function validateUtangForm() {
            var utangAmount = parseFloat(document.getElementById('utang_amount').value) || 0;

            // Validate that the utang amount is positive
            if (utangAmount <= 0) {
                alert("Utang amount must be greater than zero.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
