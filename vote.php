<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if the user exists in the database
$userQuery = $pdo->prepare("SELECT has_voted FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Check if a valid user was returned
if ($user === false) {
    // If no user was found, handle the error
    $message = "User not found.";
    $message_type = "error";
    // Display the message (optional, depending on your error handling)
    echo "<script>alert('$message');</script>";
    exit;
}

if ($user['has_voted']) {
    $message = "You have already voted!";
    $message_type = "error";  // Assign a type for styling
} else {
    $message = "You can now vote!";
    $message_type = "success";
    $candidates = $pdo->query("SELECT * FROM candidates")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
    <link rel="stylesheet" href="css/vote.css">
    <script>
        // This will be used to display the message dynamically without navigating to another page
        function showMessage(message, messageType) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', messageType);
            messageDiv.innerText = message;
            document.body.insertBefore(messageDiv, document.body.firstChild); // Insert message at the top
            setTimeout(() => {
                messageDiv.style.display = 'none'; // Hide the message after 3 seconds
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="vote-container">
        <?php if (isset($message)): ?>
            <script>
                window.onload = function() {
                    showMessage("<?php echo addslashes($message); ?>", "<?php echo htmlspecialchars($message_type); ?>");
                };
            </script>
        <?php endif; ?>

        <?php if (!isset($user['has_voted']) || !$user['has_voted']): ?>
            <h2>Cast Your Vote</h2>
            <form action="submit_vote.php" method="POST" class="vote-form">
                <div class="candidates">
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="candidate-card">
                            <!-- Displaying the candidate's image -->
                            <img src="uploads/<?php echo htmlspecialchars($candidate['image']); ?>" 
                                alt="<?php echo htmlspecialchars($candidate['name']); ?>" 
                                class="candidate-image">

                            <!-- Candidate information container -->
                            <div class="candidate-info">
                                <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                                

                                <!-- Faculty container -->
                                <div class="candidate-faculty">
                                    <p><strong>Faculty:</strong> <?= htmlspecialchars($candidate['faculty']) ?></p>
                                </div>
                                
                                <!-- Manifesto container -->
                                <div class="candidate-manifesto">
                                    <p><strong>Manifesto:</strong> <?= htmlspecialchars($candidate['manifesto']) ?></p>
                                </div>

                                <!-- Vote button -->
                                <button type="submit" name="candidate_id" 
                                        value="<?= htmlspecialchars($candidate['candidate_id']) ?>" 
                                        class="vote-button">Vote</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
