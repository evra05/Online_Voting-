<?php
// Start the session and include the database connection
session_start();
include 'db.php';

// Initialize $results as an empty array to avoid the "undefined variable" warning
$results = [];

// Query to count votes for each candidate
$query = "
    SELECT candidates.name, candidates.faculty, COUNT(votes.candidate_id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON candidates.candidate_id = votes.candidate_id
    GROUP BY candidates.candidate_id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include the results template to display the results
include __DIR__ . '/templates/results.html';
