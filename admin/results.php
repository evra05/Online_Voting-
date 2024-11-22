<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$query = "
    SELECT candidates.name, candidates.faculty, COUNT(votes.candidate_id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON candidates.candidate_id = votes.candidate_id
    GROUP BY candidates.candidate_id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results</title>
    <link rel="stylesheet" href="/css/results_styles.css"> <!-- Separate CSS file for results page -->
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="results.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">View Voting Results</a></li>
                <li><a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_users' ? 'active' : ''; ?>">Manage User</a></li>
                <li><a href="logout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Voting Results</h2>
            <table>
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Faculty</th> <!-- New column for Faculty -->
                        <th>Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($result['name']); ?></td>
                        <td><?php echo htmlspecialchars($result['faculty']); ?></td> <!-- Display candidate's faculty -->
                        <td><?php echo $result['vote_count']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>


