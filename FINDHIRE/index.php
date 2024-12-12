<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php  
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);
if ($getUserByID['is_admin'] == 1) {
    header("Location: admin/index.php");
}
if ($getUserByID['is_suspended'] == 1) {
    header("Location: suspended-account-error.php");
}

// Get all job posts
$jobPosts = getAllJobPosts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Homepage</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h1 style="text-align: center;">Hello, <span style="color: blue"><?php echo $_SESSION['username']; ?></span>! Looking for a Job?</h1>
    <h2>Available Job Listings</h2>
    <div class="job-listings">
        <?php foreach ($jobPosts as $job): ?>
            <div class="job-post" style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3>Job Title: <?php echo htmlspecialchars($job['title']); ?></h3>
                <p><strong>Job Description: </strong><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                <p>Posted by: <?php echo htmlspecialchars($job['username']); ?></p>
                <p>Date Posted: <?php echo $job['date_posted']; ?></p>
                <form action="core/handleForms.php" method="POST">
                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                    <input type="submit" name="applyJobBtn" value="Apply for this job">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
