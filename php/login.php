<?php
session_start();

// Check if the user is already logged in, redirect to dashboard if so
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Default username and password
    $username = 'admin';
    $password = 'password123';

    // Get the input username and password
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Check if the input matches the default credentials
    if ($input_username == $username && $input_password == $password) {
        // Set the session variables
        $_SESSION['username'] = $input_username;
        header("Location: dashboard.php"); // Redirect to dashboard after successful login
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Login</h1>
    
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
</body>
</html>
