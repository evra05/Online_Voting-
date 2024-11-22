<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE user_id = ?");
        $stmt->execute([$token, $expiry, $user['user_id']]);

        $resetLink = "http://yourwebsite.com/reset_password.php?token=$token";
        $message = "Click here to reset your password: $resetLink";

        mail($email, "Password Reset Request", $message, "From: no-reply@yourwebsite.com");

        echo "An email with a password reset link has been sent to your email address.";
    } else {
        echo "No account found with that email address.";
    }
}
?>

<form action="request_reset.php" method="POST">
    Enter your email: <input type="email" name="email" required>
    <button type="submit">Send Reset Link</button>
</form>
