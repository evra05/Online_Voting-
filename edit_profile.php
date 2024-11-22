<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch the user's current data from the database
$stmt = $pdo->prepare("SELECT username, password, firstname, lastname, faculty, semester FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $newUsername = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $faculty = $_POST['faculty'];
    $semester = $_POST['semester'];

    // Check if the new username is already taken
    $usernameCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?");
    $usernameCheckStmt->execute([$newUsername, $userId]);
    $usernameExists = $usernameCheckStmt->fetchColumn() > 0;

    if ($usernameExists) {
        $error = "Username is already taken.";
    } else {
        // Verify current password
        if (password_verify($currentPassword, $user['password'])) {
            // Update password if new password is provided
            if (!empty($newPassword) && $newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $updateStmt->execute([$hashedPassword, $userId]);
                $message = "Password updated successfully!";
            } elseif (!empty($newPassword)) {
                $error = "New passwords do not match.";
            }

            // Update username and other details
            if ($newUsername !== $user['username']) {
                $updateUsernameStmt = $pdo->prepare("UPDATE users SET username = ? WHERE user_id = ?");
                $updateUsernameStmt->execute([$newUsername, $userId]);
                $message = "Username updated successfully!";
            }

            // Update other personal information (firstname, lastname, faculty, semester)
            $updateInfoStmt = $pdo->prepare("UPDATE users SET firstname = ?, lastname = ?, faculty = ?, semester = ? WHERE user_id = ?");
            $updateInfoStmt->execute([$firstname, $lastname, $faculty, $semester, $userId]);
            $message = "Profile updated successfully!";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/css/edit_profile.css">
</head>
<body>
    <h1>Edit Profile</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <form method="post">
        <label for="user_id">User ID:</label>
        <input type="text" name="user_id" value="<?php echo htmlspecialchars($userId); ?>" readonly>

        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

        <label for="faculty">Faculty:</label>
        <input type="text" name="faculty" value="<?php echo htmlspecialchars($user['faculty']); ?>" required>

        <label for="semester">Semester:</label>
        <input type="text" name="semester" value="<?php echo htmlspecialchars($user['semester']); ?>" required>

        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password">

        <button type="submit">Update Profile</button>
    </form>

    <div class="back-button">
        <a href="menu.php" class="button">Back</a>
    </div>

</body>
</html>
