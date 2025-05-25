<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Credit Transaction</title>
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
        #suggestions {
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            background-color: white;
        }
        #suggestions div {
            padding: 8px;
            cursor: pointer;
        }
        #suggestions div:hover {
            background-color: #e0e0e0;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .modal-content h3 {
            color: #6A0DAD;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .modal-content p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .modal-content button {
            background-color: #6A0DAD;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .modal-content button:hover {
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
    <h1>Add Credit Transaction</h1>
    <div class="form-container">
        <form method="POST" action="credit_transaction_action.php" onsubmit="return validateCreditForm()">
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" required onkeyup="autocompleteCustomers()">
            <div id="suggestions"></div>

            <label for="credit_amount">Credit Amount</label>
            <input type="number" step="0.01" id="credit_amount" name="credit_amount" required>

            <label for="credit_date">Credit Date</label>
            <input type="date" id="credit_date" name="credit_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="due_date">Due Date</label>
            <input type="date" id="due_date" name="due_date" required>

            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3"></textarea>

            <button type="submit">Add Credit Transaction</button>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h3>Credit Added Successfully!</h3>
            <p>Your credit record has been added successfully.</p>
            <button onclick="closeModal('successModal')">OK</button>
        </div>
    </div>

    <div class="modal" id="errorModal">
        <div class="modal-content">
            <h3>Error</h3>
            <p id="errorMessage">An error occurred while adding the credit. Please try again.</p>
            <button onclick="closeModal('errorModal')">OK</button>
        </div>
    </div>

    <script>
        function autocompleteCustomers() {
            var query = document.getElementById('customer_name').value;

            if (query.length >= 1) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "autocomplete_customers.php?query=" + encodeURIComponent(query), true);
                xhr.onload = function () {
                    if (xhr.status == 200) {
                        var suggestions = JSON.parse(xhr.responseText);
                        var suggestionsList = document.getElementById('suggestions');
                        suggestionsList.innerHTML = "";

                        if (suggestions.length > 0) {
                            suggestions.forEach(function(customer) {
                                var div = document.createElement('div');
                                div.innerHTML = customer.customer_name;
                                div.onclick = function() {
                                    document.getElementById('customer_name').value = customer.customer_name;
                                    suggestionsList.innerHTML = "";
                                };
                                suggestionsList.appendChild(div);
                            });
                        } else {
                            var noResult = document.createElement('div');
                            noResult.innerHTML = "No results found";
                            suggestionsList.appendChild(noResult);
                        }
                    }
                };
                xhr.send();
            } else {
                document.getElementById('suggestions').innerHTML = "";
            }
        }

        function validateCreditForm() {
            var customerName = document.getElementById('customer_name').value;
            var creditAmount = parseFloat(document.getElementById('credit_amount').value) || 0;

            if (!customerName || creditAmount <= 0) {
                document.getElementById('errorMessage').innerText = "Please fill out all fields correctly.";
                showModal('errorModal');
                return false;
            }
            showModal('successModal');
            return true;
        }

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
