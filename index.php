<?php
session_start();
include 'templates/header.html';
include 'db.php'; // Database connection

// Show the welcoming page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    ?>
    <div class="welcome-page">
        <div class="welcome-section">
            <h1>Welcome to Online Voting</h1>
            <p>This system allows registered users to vote for their favorite candidates and view live results.</p>
            <div class="actions">
                <a class="button" href="login.php">Login</a>
                <a class="button register" href="register.php">Register</a>
            </div>
        </div>
    </div>
    <?php
} else {
    // Redirect to the menu if the user is already logged in
    header("Location: menu.php");
    exit;  // Ensure no further code is executed after the redirect
}

?>

<?php include 'templates/footer.html'; ?>
