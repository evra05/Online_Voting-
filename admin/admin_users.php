<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$faculty_filter = isset($_GET['faculty']) ? $_GET['faculty'] : '';

// Base SQL query with filters
$sql = "
    SELECT u.user_id, u.username, u.firstname, u.lastname, u.faculty, u.semester, u.has_voted, v.candidate_id, c.name AS candidate_name
    FROM users u
    LEFT JOIN votes v ON u.user_id = v.user_id
    LEFT JOIN candidates c ON v.candidate_id = c.candidate_id
    WHERE u.username LIKE ? OR u.has_voted LIKE ?
";

// Prepare an array to hold the bound parameters
$params = ['%' . $search . '%', '%' . $search . '%'];

// Apply semester filter if provided
if ($semester_filter) {
    $sql .= " AND u.semester = ?";
    $params[] = $semester_filter;
}

// Apply faculty filter if provided
if ($faculty_filter) {
    $sql .= " AND u.faculty LIKE ?";
    $params[] = '%' . $faculty_filter . '%';
}

// Order by username
$sql .= " ORDER BY u.username";

// Prepare and execute the query with the correct number of parameters
$query = $pdo->prepare($sql);
$query->execute($params);

// Fetch the results
$users = $query->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User Management</title>
    <link rel="stylesheet" href="/css/admin_users.css"> 
<body>
    <div class="container">
        <div class="sidebar">
            <h3>User Management</h3>
            <ul>
                <li><a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="results.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">View Voting Results</a></li>
                <li><a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active' : ''; ?>">Manage User</a></li>
                <li><a href="logout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h1>User Management</h1>

            <!-- Search Form with Filters -->
<form action="admin_users.php" method="GET" class="modern-form">
    <input type="text" name="search" placeholder="Search by username, faculty, or voting status" value="<?php echo htmlspecialchars($search); ?>" class="search-input">
    
    <!-- Semester Filter Dropdown -->
    <select name="semester" class="filter-select">
        <option value="">Select Semester</option>
        <option value="1" <?php echo $semester_filter == '1' ? 'selected' : ''; ?>>1</option>
        <option value="2" <?php echo $semester_filter == '2' ? 'selected' : ''; ?>>2</option>
        <option value="3" <?php echo $semester_filter == '3' ? 'selected' : ''; ?>>3</option>
        <option value="4" <?php echo $semester_filter == '4' ? 'selected' : ''; ?>>4</option>
        <option value="5" <?php echo $semester_filter == '5' ? 'selected' : ''; ?>>5</option>
        <option value="6" <?php echo $semester_filter == '6' ? 'selected' : ''; ?>>6</option>
    </select>
    
    <!-- Faculty Filter Dropdown -->
    <select name="faculty" class="filter-select">
        <option value="">Select Faculty</option>
        <option value="Engineering" <?php echo $faculty_filter == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
        <option value="Science" <?php echo $faculty_filter == 'Science' ? 'selected' : ''; ?>>Science</option>
        <option value="Arts" <?php echo $faculty_filter == 'Arts' ? 'selected' : ''; ?>>Arts</option>
        <!-- Add more faculties here as needed -->
    </select>
    
    <button type="submit" class="submit-button">Search</button>
</form>



<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Faculty</th>
            <th>Semester</th>
            <th>Voting Status</th>
            <th>Voted Candidate</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['faculty']); ?></td>
                    <td><?php echo htmlspecialchars($user['semester']); ?></td>
                    <td><?php echo $user['has_voted'] ? 'Voted' : 'Not Voted'; ?></td>
                    <td>
                        <?php 
                        // Show the candidate name if the user has voted, otherwise display "N/A"
                        echo $user['candidate_name'] ? htmlspecialchars($user['candidate_name']) : 'N/A'; 
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


        </div>
    </div>
</body>
</html>
