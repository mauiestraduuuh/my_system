<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale</title>
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
    </style>
</head>
<body>
    <h1>Add Sale</h1>
    <div class="form-container">
        <form method="POST" action="add_sale_action.php" onsubmit="return validateSaleForm()">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" required onkeyup="autocompleteProducts()">
            <div id="suggestions"></div>

            <label for="unit_price">Unit Price</label>
            <input type="number" step="0.01" id="unit_price" name="unit_price" readonly required>

            <label for="quantity">Quantity Sold</label>
            <input type="number" id="quantity" name="quantity" required oninput="calculateTotalAmount()">

            <label for="sales_date">Sales Date</label>
            <input type="date" id="sales_date" name="sales_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="total_amount">Total Amount</label>
            <input type="number" step="0.01" id="total_amount" name="total_amount" readonly required>

            <button type="submit">Add Sale</button>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <h3>Sale Added Successfully!</h3>
            <p>Your sale record has been added successfully.</p>
            <button onclick="closeModal('successModal')">OK</button>
        </div>
    </div>

    <div class="modal" id="errorModal">
        <div class="modal-content">
            <h3>Error</h3>
            <p id="errorMessage">An error occurred while adding the sale. Please try again.</p>
            <button onclick="closeModal('errorModal')">OK</button>
        </div>
    </div>

    <script>
        function autocompleteProducts() {
            var query = document.getElementById('product_name').value;

            if (query.length >= 1) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "autocomplete.php?query=" + encodeURIComponent(query), true);
                xhr.onload = function () {
                    if (xhr.status == 200) {
                        var suggestions = JSON.parse(xhr.responseText);
                        var suggestionsList = document.getElementById('suggestions');
                        suggestionsList.innerHTML = "";

                        if (suggestions.length > 0) {
                            suggestions.forEach(function(product) {
                                var div = document.createElement('div');
                                div.innerHTML = product.product_name;
                                div.onclick = function() {
                                    document.getElementById('product_name').value = product.product_name;
                                    document.getElementById('unit_price').value = product.default_price;
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

        function calculateTotalAmount() {
            var unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
            var quantity = parseInt(document.getElementById('quantity').value) || 0;
            var totalAmount = unitPrice * quantity;
            document.getElementById('total_amount').value = totalAmount.toFixed(2);
        }

        function validateSaleForm() {
            var productName = document.getElementById('product_name').value;
            var quantity = parseInt(document.getElementById('quantity').value) || 0;

            if (!productName || quantity <= 0) {
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
