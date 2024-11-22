<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's voting status
include 'db.php';
$userQuery = $pdo->prepare("SELECT has_voted FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);
$user = $userQuery->fetch();

// If the user has already voted, display a confirmation message
if ($user['has_voted']) {
    $message = "Thank you for voting!";
} else {
    $message = "Your vote has been recorded!";
}

// Optionally, you can also fetch details about the voted candidate
if (isset($_SESSION['voted_candidate_id'])) {
    $candidateQuery = $pdo->prepare("SELECT name FROM candidates WHERE candidate_id = ?");
    $candidateQuery->execute([$_SESSION['voted_candidate_id']]);
    $candidate = $candidateQuery->fetch();
    $candidateName = $candidate['name'];
} else {
    $candidateName = 'N/A';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Confirmation</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Add your stylesheet -->
</head>
<body>
    <div class="confirmation-container">
        <h1>Vote Confirmation</h1>
        <p><?php echo $message; ?></p>
        <p><strong>You voted for:</strong> <?php echo htmlspecialchars($candidateName); ?></p>
        <a href="index.php" class="back-button">Back to Menu</a>
    </div>
</body>
</html>
