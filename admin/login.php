<?php
// Start the session
session_start();

// Initialize error message variable
$errorMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include '../db.php';

    // Hardcoded admin credentials (for example)
    $admin_username = "admin";
    $admin_password = "password123"; // In production, use hashed passwords

    // Get the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the credentials are correct
    if ($username == $admin_username && $password == $admin_password) {
        // Set session variable to mark the user as logged in
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_menu.php"); // Redirect to the admin dashboard
        exit;
    } else {
        // If credentials are incorrect, show an error message
        $errorMessage = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/admin_styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <!-- Display error message if credentials are incorrect -->
        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
