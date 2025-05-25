<?php
session_start();
include('db_connection.php');

// Assuming $_SESSION['role'] contains Owner / Assistant_1 / Assistant_2
$paid_by = $_SESSION['role'] ?? 'Unknown';

// Get latest utang entries per customer
$query = "SELECT u.utang_id, u.id, c.name, u.utang_amount 
          FROM utang u 
          JOIN customers c ON u.id = c.id 
          WHERE u.payment_status = 'Unpaid' OR u.payment_status = 'Partially Paid'
          ORDER BY c.name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Utang</title>
    <style>
        body {
            font-family: 'Bahnschrift Condensed', sans-serif;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #6A0DAD;
        }
        .form-container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow: 0px 0px 10px #ccc;
        }
        label {
            font-size: 1.1em;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #6A0DAD;
            color: white;
            padding: 10px 20px;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
        }
        #paid_amount_group, #reason_group {
            display: none;
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
<h1>Pay Utang</h1>

<div class="form-container">
    <form method="POST" action="payment_utang_action.php" onsubmit="return validateForm()">
        <label for="utang_id">Select Customer</label>
        <select id="utang_id" name="utang_id" onchange="updateUtangAmount()" required>
            <option value="">-- Select Customer --</option>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?= $row['utang_id'] ?>" data-amount="<?= $row['utang_amount'] ?>">
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="utang_amount">Utang Amount</label>
        <input type="number" step="0.01" id="utang_amount" name="utang_amount" readonly required>

        <label for="payment_status">Payment Status</label>
        <select id="payment_status" name="payment_status" onchange="togglePaidAmount()" required>
            <option value="">-- Select Status --</option>
            <option value="Paid">Paid</option>
            <option value="Partially Paid">Partially Paid</option>
        </select>

        <div id="paid_amount_group">
            <label for="paid_amount">Paid Amount</label>
            <input type="number" step="0.01" id="paid_amount" name="paid_amount">
        </div>

        <label for="rating">Payment Rating</label>
        <select id="rating" name="rating" onchange="toggleReason()" required>
            <option value="">-- Select Rating --</option>
            <option value="Good">Good</option>
            <option value="Bad">Bad</option>
        </select>

        <div id="reason_group">
            <label for="reason">Reason (if Bad)</label>
            <textarea id="reason" name="reason" rows="4" placeholder="Reason for bad payment..."></textarea>
        </div>

        <input type="hidden" name="paid_by" value="<?= htmlspecialchars($paid_by); ?>">

        <button type="submit">Submit Payment</button>
    </form>
</div>

<script>
    function updateUtangAmount() {
        var dropdown = document.getElementById('utang_id');
        var selected = dropdown.options[dropdown.selectedIndex];
        var amount = selected.getAttribute('data-amount');
        document.getElementById('utang_amount').value = amount;
    }

    function togglePaidAmount() {
        var status = document.getElementById('payment_status').value;
        document.getElementById('paid_amount_group').style.display = (status === 'Partially Paid') ? 'block' : 'none';
    }

    function toggleReason() {
        var rating = document.getElementById('rating').value;
        document.getElementById('reason_group').style.display = (rating === 'Bad') ? 'block' : 'none';
    }

    function validateForm() {
        const status = document.getElementById('payment_status').value;
        const paid = parseFloat(document.getElementById('paid_amount').value);
        const utang = parseFloat(document.getElementById('utang_amount').value);
        const rating = document.getElementById('rating').value;
        const reason = document.getElementById('reason').value.trim();

        if (status === 'Partially Paid' && (isNaN(paid) || paid <= 0 || paid > utang)) {
            alert('Invalid paid amount.');
            return false;
        }

        if (rating === 'Bad' && reason === '') {
            alert('Please provide a reason for the bad rating.');
            return false;
        }

        return true;
    }
</script>

</body>
</html>
