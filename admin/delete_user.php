<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $deleteQuery = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteQuery->execute([$user_id]);

    // Optionally, delete the user's votes if applicable
    $deleteVotesQuery = $pdo->prepare("DELETE FROM votes WHERE user_id = ?");
    $deleteVotesQuery->execute([$user_id]);

    // Redirect back to admin_users.php (the page where the admin was before)
    header("Location: admin_users.php");
    exit;
} else {
    echo "User ID is not provided.";
    exit;
}
