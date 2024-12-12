
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

// Get the current user's job posts
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM job_posts WHERE posted_by = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$jobPosts = $stmt->fetchAll();
?>