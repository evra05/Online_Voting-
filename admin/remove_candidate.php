<?php
session_start();
include '../db.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle candidate removal
if (isset($_POST['remove_candidate_id'])) {
    $remove_id = $_POST['remove_candidate_id'];

    // Start a transaction to ensure both deletions happen or none at all
    $pdo->beginTransaction();

    try {
        // First, delete associated votes from the votes table
        $stmt = $pdo->prepare("DELETE FROM votes WHERE candidate_id = :id");
        $stmt->bindParam(':id', $remove_id, PDO::PARAM_INT);
        $stmt->execute();

        // Now, delete the candidate from the candidates table
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE candidate_id = :id");
        $stmt->bindParam(':id', $remove_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Return a JSON response with success message
        echo json_encode(['success' => true, 'message' => 'Candidate and associated votes removed successfully.']);
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();

        // Return a JSON response with error message
        echo json_encode(['success' => false, 'error' => 'Error removing candidate: ' . $e->getMessage()]);
        exit;
    }
}
?>
