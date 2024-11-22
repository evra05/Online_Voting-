<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $faculty = $_POST['faculty'];
    $semester = $_POST['semester'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Generate a new custom `user_id`
    $lastStudentQuery = $pdo->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
    $lastStudent = $lastStudentQuery->fetch(PDO::FETCH_ASSOC);

    if ($lastStudent && preg_match('/^STU(\d+)/', $lastStudent['user_id'], $matches)) {
        // Increment the numeric part of the last user_id
        $lastId = (int)$matches[1];
        $newUserId = 'STU' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    } else {
        // Start with the first user_id
        $newUserId = 'STU2024001';
    }

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // Username already exists
        $message = "Username already taken. Please choose another.";
    } else {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (user_id, firstname, lastname, faculty, semester, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$newUserId, $firstname, $lastname, $faculty, $semester, $username, $password]);

        // Success message with the generated `user_id`
        $message = "Registration successful! Your User ID is $newUserId. Redirecting to login page...";
        header("refresh:5;url=login.php"); // Redirect after 5 seconds
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <a href="index.php" class="back-icon">
            <img src="/icons/back.png" alt="Back">
        </a>

        <h2>Register</h2>

        <!-- Display error message if the username is taken -->
        <?php if (isset($message)): ?>
            <div class="error-message">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="form-wrapper">
            <form action="register.php" method="POST">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" required>

                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" required>

                <label for="faculty">Faculty:</label>
                <input type="text" name="faculty" id="faculty" required>

                <label for="semester">Semester:</label>
                <div class="modern-select">
                    <select name="semester" id="semester" required>
                        <option value="" disabled selected>Choose a semester</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </div>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>

                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const passwordFieldType = passwordField.getAttribute('type');
        const icon = this;

        if (passwordFieldType === 'password') {
            passwordField.setAttribute('type', 'text');
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.setAttribute('type', 'password');
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    </script>

</body>
</html>

<?php include 'templates/footer.html'; ?>
