<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$candidate_id = $_POST['candidate_id'] ?? null;

if ($candidate_id) {
    // Ensure the user has not voted already
    $userQuery = $pdo->prepare("SELECT has_voted FROM users WHERE user_id = ?");
    $userQuery->execute([$user_id]);
    $user = $userQuery->fetch();

    if ($user['has_voted']) {
        // If already voted, redirect with a message to the voting page
        $_SESSION['message'] = "You have already voted!";
        $_SESSION['message_type'] = "error";
        header("Location: vote.php");
        exit;
    }

    // Record the vote in the database
    $voteQuery = $pdo->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
    $voteQuery->execute([$user_id, $candidate_id]);

    // Update the user's status to "has voted"
    $updateUserQuery = $pdo->prepare("UPDATE users SET has_voted = 1 WHERE user_id = ?");
    $updateUserQuery->execute([$user_id]);

    // Set a confirmation message
    $_SESSION['message'] = "Your vote has been successfully cast!";
    $_SESSION['message_type'] = "success";

    // Redirect to the menu page
    header("Location: menu.php");
    exit;
}
?>
