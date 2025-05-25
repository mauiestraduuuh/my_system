<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        h1 {
            text-align: center;
            color: #6A0DAD;
            font-size: 2.5em;
            margin-top: 20px;
        }
        .form-container {
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container input,
        .form-container button,
        .form-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .form-container button {
            background-color: #6A0DAD;
            color: white;
            border: none;
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
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-dashboard:hover {
            background-color: #6a2e9d;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn-dashboard">Return to Dashboard</a>
    <h1>Add Customer</h1>
    <div class="form-container">
        <form method="POST" action="add_customer_action.php" onsubmit="return validateCustomerForm()">
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="contact">Contact</label>
            <input type="text" id="contact" name="contact" required>

            <label for="credit_limit">Credit Limit</label>
            <input type="number" step="0.01" id="credit_limit" name="credit_limit" required>

            <button type="submit">Add Customer</button>
        </form>
    </div>

    <script>
        function validateCustomerForm() {
            var customerName = document.getElementById('customer_name').value;
            var contact = document.getElementById('contact').value;
            var creditLimit = parseFloat(document.getElementById('credit_limit').value) || 0;

            if (!customerName || !contact || creditLimit <= 0) {
                alert("Please fill out all fields correctly.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
