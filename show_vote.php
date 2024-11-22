<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to get the candidate that the user voted for
$voteQuery = $pdo->prepare("
    SELECT candidates.name, candidates.faculty, candidates.manifesto, candidates.image
    FROM votes
    JOIN candidates ON votes.candidate_id = candidates.candidate_id
    WHERE votes.user_id = ?
");
$voteQuery->execute([$user_id]);
$vote = $voteQuery->fetch();

// If no vote has been cast, redirect to vote.php
if (!$vote) {
    header("Location: vote.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Vote</title>
    <link rel="stylesheet" href="css/vote.css">
</head>
<body>
    <div class="vote-container">
        <h2>Your Vote</h2>
        <?php if ($vote): ?>
            <div class="candidate-card">
                <!-- Displaying the candidate's image -->
                <img src="uploads/<?php echo htmlspecialchars($vote['image']); ?>" 
                    alt="<?php echo htmlspecialchars($vote['name']); ?>" 
                    class="candidate-image">

                <!-- Candidate information container -->
                <div class="candidate-info">
                    <h3><?php echo htmlspecialchars($vote['name']); ?></h3>
                    
                    <!-- Faculty container -->
                    <div class="candidate-faculty">
                        <p><strong>Faculty:</strong> <?= htmlspecialchars($vote['faculty']) ?></p>
                    </div>
                    
                    <!-- Manifesto container -->
                    <div class="candidate-manifesto">
                        <p><strong>Manifesto:</strong> <?= htmlspecialchars($vote['manifesto']) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>You have not voted yet.</p>
        <?php endif; ?>

        <div class="menu-button">
            <a href="menu.php" class="button">Back to Menu</a>
        </div>
    </div>
</body>
</html>
