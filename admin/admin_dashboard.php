<?php
session_start();
include '../db.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Initialize success and error message variables
$success_message = "";
$error_message = "";

// Handle form submission and file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_name']) && isset($_POST['faculty']) && isset($_POST['manifesto'])) {
    $candidate_name = $_POST['candidate_name'];
    $faculty = $_POST['faculty'];
    $manifesto = $_POST['manifesto']; 
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get the uploaded file's temporary name and target file name
        $image_temp_name = $_FILES['image']['tmp_name'];
        $image_name = uniqid() . '.png'; // Create a unique name for the image
        $upload_dir = '../uploads/'; // Upload directory path
        $upload_path = $upload_dir . $image_name; // Full path where the file will be saved

        // Check if the uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }

        // Move the file from temp location to the uploads directory
        if (move_uploaded_file($image_temp_name, $upload_path)) {
            // File upload was successful
            $image_name = $image_name; // Store the file name in the database

            // Insert candidate information into the database
            try {
                $stmt = $pdo->prepare("INSERT INTO candidates (name, faculty, manifesto, image) VALUES (:candidate_name, :faculty, :manifesto, :image)");
                $stmt->bindParam(':candidate_name', $candidate_name);
                $stmt->bindParam(':faculty', $faculty);
                $stmt->bindParam(':manifesto', $manifesto); // Bind the manifesto
                $stmt->bindParam(':image', $image_name); // Bind the image name to save in the database

                if ($stmt->execute()) {
                    $success_message = "Candidate added successfully!";
                } else {
                    throw new Exception("Error executing SQL query.");
                }
            } catch (Exception $e) {
                $error_message = "Error adding candidate: " . $e->getMessage();
            }
        } else {
            $error_message = "Error uploading file. Please try again.";
        }
    } else {
        $error_message = "No file uploaded or an error occurred.";
    }
}

// Fetch existing candidates
$stmt = $pdo->query("SELECT * FROM candidates");
$candidates = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin_dashboard_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Admin Menu</h3>
            <ul class="sidebar-links">
                <li><a href="admin_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="results.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">View Voting Results</a></li>
                <li><a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">Manage User</a></li>
                <li><a href="logout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Add a Candidate</h2>

            <!-- Display success or error messages -->
<?php if ($success_message) { ?>
    <div class="alert success"><?php echo $success_message; ?></div>
<?php } ?>
<?php if ($error_message) { ?>
    <div class="alert error"><?php echo $error_message; ?></div>
<?php } ?>


            <form method="POST" id="candidateForm" enctype="multipart/form-data" class="modern-form">
    <div class="form-group">
        <label for="candidate_name">Candidate Name:</label>
        <input type="text" name="candidate_name" id="candidate_name" placeholder="Enter candidate's name" required>
    </div>

    <div class="form-group">
        <label for="faculty">Faculty:</label>
        <input type="text" name="faculty" id="faculty" placeholder="Enter faculty name" required>
    </div>

    <div class="form-group">
    <label for="manifesto">Manifesto:</label>
    <textarea name="manifesto" id="manifesto" rows="6" placeholder="Enter candidate's manifesto" required></textarea>
</div>


<div class="form-group file-upload-group">
    <label for="image">Upload Image:</label>
    <input type="file" name="image" id="imageInput" accept="image/*" required>
    <span class="custom-file-label" id="fileLabel">No file chosen</span>
</div>


    <div class="form-group">
        <input type="submit" value="Add Candidate" class="submit-button">
    </div>
</form>


            <h3>Current Candidates:</h3>
            <ul class="candidate-list">
    <?php foreach ($candidates as $candidate) { ?>
        <li id="candidate-<?php echo $candidate['candidate_id']; ?>">
            <div class="candidate-name">
                <strong><?php echo htmlspecialchars($candidate['name']); ?></strong>
            </div>

            <div class="candidate-actions">
                <!-- Edit Button -->
                <a href="edit_candidate.php?candidate_id=<?php echo $candidate['candidate_id']; ?>" class="edit-button">Edit</a>
                <!-- Remove button form -->
                <form method="POST" action="remove_candidate.php" style="margin: 0; display: inline;">
                    <input type="hidden" name="remove_candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
                    <button type="button" class="remove-button">Remove</button> <!-- Use type="button" -->
                </form>
            </div>
        </li>
    <?php } ?>
</ul>


<div id="message"></div>
        </div>
    </div>

    <script>
    // Get the file input and label elements
const fileInput = document.getElementById('imageInput');
const fileLabel = document.getElementById('fileLabel');

// Update the label text and color when a file is selected
fileInput.addEventListener('change', function () {
    const fileName = fileInput.files[0] ? fileInput.files[0].name : 'No file chosen';
    fileLabel.textContent = fileName;

    // Apply green color when a file is selected
    if (fileInput.files.length > 0) {
        fileLabel.style.backgroundColor = '#28a745';
        fileLabel.style.color = 'white';
        fileLabel.style.borderColor = '#28a745';
    } else {
        fileLabel.style.backgroundColor = '#f0f0f0';
        fileLabel.style.color = '#333';
        fileLabel.style.borderColor = '#ccc';
    }
});


$(document).ready(function() {
    // Event listener for add candidate success message
    if ($('.alert.success').length > 0) {
        setTimeout(function() {
            $('.alert.success').fadeOut();  // Fade out the success message
        }, 3000);  // 3000ms = 3 seconds
    }

    $(document).ready(function() {
    // Event listener for remove button click
    $('.remove-button').on('click', function(event) {
        event.preventDefault();  // Prevent the default form submission

        var candidateId = $(this).closest('form').find('input[name="remove_candidate_id"]').val(); // Get candidate ID from hidden input

        // Confirmation dialog before proceeding with deletion
        if (confirm("Are you sure you want to remove this candidate?")) {
            $.ajax({
                url: 'remove_candidate.php', // PHP script for handling deletion
                type: 'POST',
                data: {
                    remove_candidate_id: candidateId
                },
                success: function(response) {
                    // Parse the JSON response
                    var data = JSON.parse(response);

                    // Show success or error message
                    if (data.success) {
                        // Remove the candidate from the list (fade-out the candidate)
                        $('#candidate-' + candidateId).fadeOut();

                        // Show success message
                        $('#message').html('<div class="alert success">' + data.message + '</div>');

                        // Hide the success message after 3 seconds
                        setTimeout(function() {
                            $('#message').html('');
                        }, 3000);  // 3000ms = 3 seconds
                    } else {
                        $('#message').html('<div class="alert error">Error: ' + data.error + '</div>');
                    }
                },
                error: function() {
                    $('#message').html('<div class="alert error">Something went wrong. Please try again.</div>');
                }
            });
        }
    });
});


});


</script>

</body>
</html>