<?php
session_start();
include 'db.php'; // Database connection

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, check if they have voted
    $user_id = $_SESSION['user_id'];
    $userQuery = $pdo->prepare("SELECT has_voted FROM users WHERE user_id = ?");
    $userQuery->execute([$user_id]);
    $user = $userQuery->fetch();

    if ($user['has_voted']) {
        // Redirect to the menu if the user has already voted
        header("Location: menu.php");
        exit;
    } else {
        // Redirect to the vote page if the user hasn't voted yet
        header("Location: vote.php");
        exit;
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database for user authentication
    $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $query->execute([$username]);
    $user = $query->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Start the session and set the user ID
        $_SESSION['user_id'] = $user['user_id'];

        // Check if the user has already voted
        if ($user['has_voted']) {
            // Redirect to the menu if the user has already voted
            header("Location: menu.php");
            exit;
        } else {
            // Redirect to the voting page if the user hasn't voted yet
            header("Location: vote.php");
            exit;
        }
    } else {
        // Invalid login - set error message
        $error_message = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Voting</title>
    <link rel="stylesheet" href="/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="login-container">
        <a href="index.php" class="back-icon">
            <img src="/icons/back.png" alt="Back">
        </a>

        <h2>Login to Vote</h2>

        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <div class="password-container">
                <input type="password" name="password" id="password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const passwordFieldType = passwordField.getAttribute('type');
            const icon = this;

            if (passwordFieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.setAttribute('type', 'password');
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Show error message if login fails
        <?php if (isset($error_message)): ?>
            alert("<?php echo $error_message; ?>");
        <?php endif; ?>
    </script>

</body>
</html>

<?php include 'templates/footer.html'; ?>
