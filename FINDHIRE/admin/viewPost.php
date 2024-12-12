<?php
// Include the database configuration file and models
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}


// Get the job post ID from the URL
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "No job post specified!";
    header("Location: viewyourJobPost.php");
    exit;
}
$unreadCount = getUnreadMessagesCount($pdo, $_SESSION['user_id']);

$jobId = intval($_GET['id']);

// Fetch job post details
$jobPost = getJobPostById($pdo, $jobId);

if (!$jobPost) {
    $_SESSION['message'] = "Job post not found!";
    header("Location: viewyourJobPost.php");
    exit;
}

// Fetch applicants for the job
$applicants = getApplicantsByJobId($pdo, $jobId);

// Fetch hired applicants for the job
$hiredApplicants = getHiredApplicantsByJobId($pdo, $jobId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Post</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    
    <?php include 'navbar.php'; ?>
    <div class="notification">
    <p>You have <?php echo $unreadCount; ?> unread message(s).</p>
</div>
    <div class="job-post-container">
        <h1>Job Title: <?php echo htmlspecialchars($jobPost['title']); ?></h1>
        <h2>Job Description</h2>
        <p><?php echo nl2br(htmlspecialchars($jobPost['description'])); ?></p>

        <h2>Applicants</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Applicant Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        <?php if (empty($applicants)): ?>
            <tr>
                <td colspan="6">No applicants for this job.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($applicants as $applicant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($applicant['application_id']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['status']); ?></td>
                    <td>
                        <a href="viewApplicationofAp.php?application_id=<?php echo $applicant['application_id']; ?>">View Application | </a>
                        <a href="see-all-messages.php?application_id=<?php echo $applicant['application_id']; ?>">Messages <?php if ($unreadCount > 0): ?>
                <span class="notification-badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?></a> 
                    </td>
                    
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

        <h2>Hired Applicants</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Applicant Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hiredApplicants)): ?>
                    <tr>
                        <td colspan="2">No hired applicants.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hiredApplicants as $applicant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($applicant['application_id']); ?></td>
                            <td><?php echo htmlspecialchars($applicant['first_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
</body>
</html>
