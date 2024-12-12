<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user data
$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

// Redirect if the user is not an admin
if ($getUserByID['is_admin'] == 0) {
    header("Location: ../index.php");
    exit;
}

$getAllUsers = getAllUsers($pdo);
$jobPosts = getAllJobPosts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <!-- Job Posts Section -->
    <h1 style="text-align: center;">Admin Dashboard - Job Posts</h1>

    <!-- Job Post Creation Form -->


    <div class="container">
        <form method="POST" action="core/handleForms.php" style="margin: 20px auto; width: 50%; padding: 20px; border: 1px solid #ccc; background-color: ghostwhite;">
            <h2>Create a Job Post</h2>
            <label for="title">Job Title:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="description">Job Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea><br>
            <button type="submit" name="createJobPostBtn">Post Job</button>
        </form>
    </div>
    <?php
// Display flash message for job post creation
if (isset($_SESSION['jobPostMessage'])) {
    $message = $_SESSION['jobPostMessage'];
    $status = $_SESSION['jobPostStatus'];

    // Define the CSS class for the message based on status
    $cssClass = ($status === '200') ? 'alert-success' : 'alert-danger';

    echo "<div class='alert {$cssClass}'>{$message}</div>";

    // Unset the session variables to prevent repeated messages
    unset($_SESSION['jobPostMessage'], $_SESSION['jobPostStatus']);
}
?>

<!-- Display Job Posts -->
<div class="container">
    <h2>All Job Posts</h2>
    <?php if (!empty($jobPosts)) : ?>
        <?php foreach ($jobPosts as $post) : ?>
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3>Job Title: <?php echo htmlspecialchars($post['title']); ?></h3>
                <p><strong>Job Description: </strong><?php echo htmlspecialchars($post['description']); ?></p>
                <p><em>Posted by: <?php echo htmlspecialchars($post['username']); ?></p>
                <p><em>Posted on: <?php echo htmlspecialchars($post['date_posted']); ?></em></p>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No job posts available.</p>
    <?php endif; ?>
</div>

</body>
</html>
