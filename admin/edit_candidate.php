<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "Not logged in. Redirecting to login page.";
    exit;
}

if (!isset($_GET['candidate_id'])) {
    echo "No candidate ID provided.";
    exit;
}

$candidate_id = $_GET['candidate_id'];

// Fetch the candidate details
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE candidate_id = :candidate_id");
$stmt->bindParam(':candidate_id', $candidate_id);
if (!$stmt->execute()) {
    echo "Error executing query: " . implode(", ", $stmt->errorInfo());
    exit;
}

$candidate = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$candidate) {
    echo "No candidate found with ID: " . htmlspecialchars($candidate_id) . "<br>";
    exit;
}

// Handle the form submission to update the candidate
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['candidate_name'];
    $faculty = $_POST['faculty'];
    $manifesto = $_POST['manifesto'];

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_temp_name = $_FILES['image']['tmp_name'];
        $image_name = uniqid() . '.png';
        $upload_dir = '../uploads/';
        $upload_path = $upload_dir . $image_name;

        // Move uploaded file
        if (move_uploaded_file($image_temp_name, $upload_path)) {
            $image_name = $image_name;
        } else {
            $error_message = "Error uploading the new image.";
        }
    } else {
        $image_name = $candidate['image']; // Keep the old image
    }

    try {
        // Update the candidate's details in the database
        $stmt = $pdo->prepare("UPDATE candidates SET name = :name, faculty = :faculty, manifesto = :manifesto, image = :image WHERE candidate_id = :candidate_id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':faculty', $faculty);
        $stmt->bindParam(':manifesto', $manifesto);
        $stmt->bindParam(':image', $image_name);
        $stmt->bindParam(':candidate_id', $candidate_id);

        if ($stmt->execute()) {
            // Redirect to the dashboard after successful update
            header("Location: admin_dashboard.php");
            exit; // Always call exit after header redirection
        } else {
            throw new Exception("Failed to update candidate.");
        }
    } catch (Exception $e) {
        $error_message = "Error updating candidate: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
    <link rel="stylesheet" href="/css/edit_candidate.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="results.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">View Voting Results</a></li>
                <li><a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">Manage User</a></li>
                <li><a href="logout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2>Edit Candidate</h2>
            <!-- Display success or error messages -->
            <?php if ($success_message) { ?>
                <div class="alert success"><?php echo $success_message; ?></div>
            <?php } ?>
            <?php if ($error_message) { ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php } ?>

            <!-- Candidate Edit Form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="candidate_id">Candidate ID:</label>
                    <input type="text" id="candidate_id" value="<?php echo htmlspecialchars($candidate['candidate_id']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="candidate_name">Candidate Name:</label>
                    <input type="text" name="candidate_name" id ="candidate_name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty:</label>
                    <input type="text" name="faculty" id="faculty" value="<?php echo htmlspecialchars($candidate['faculty']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="manifesto">Manifesto:</label>
                    <textarea name="manifesto" id="manifesto" rows="4" required><?php echo htmlspecialchars($candidate['manifesto']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Candidate Image:</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <p>Current Image: <img src="../uploads/<?php echo htmlspecialchars($candidate['image']); ?>" alt="Candidate Image" style="max-width: 100px; max-height: 100px;"></p>
                </div>

                <input type="submit" value="Update Candidate">
            </form>
        </div>
    </div>
</body>
</html>