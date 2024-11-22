<?php
session_start();
include 'templates/header.html';
include 'db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Check if the user has voted already
$user_id = $_SESSION['user_id'];
$userQuery = $pdo->prepare("SELECT has_voted FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);
$user = $userQuery->fetch();

if ($user['has_voted']) {
    // Show the menu if the user has already voted
    ?>
    <div class="menu-container">
        <div class="welcome-section">
            <h1>Welcome to Online Voting</h1>
            <p>Cast your vote, view results, and manage your profile seamlessly.</p>
        </div>
        <div class="menu-options">
            <h2>Menu</h2>
            <ul>
                <li><a href="show_vote.php">View your candidate</a></li>
                <li><a href="results.php">View Results</a></li>
                <li><a href="edit_profile.php">Edit Profile</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </div>
    </div>
    <?php
} else {
    // Redirect to the voting page if the user hasn't voted yet
    header("Location: vote.php");
    exit;  // Make sure to exit after the redirect to prevent further code execution
}
?>

<?php include 'templates/footer.html'; ?>
