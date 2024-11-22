<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a password reset token
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->execute([$token, date('Y-m-d H:i:s', strtotime('+1 hour')), $email]);

        // Send email with reset link
        $resetLink = "http://yourwebsite.com/new_password.php?token=" . $token;
        mail($email, "Password Reset", "Click the following link to reset your password: $resetLink");

        $message = "Password reset instructions have been sent to your email.";
    } else {
        $error = "No account associated with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>
    <form method="post">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
