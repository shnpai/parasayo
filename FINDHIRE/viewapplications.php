<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$applicant_id = $_SESSION['user_id'];
$applicationsResponse = getApplicationsByUser($pdo, $applicant_id);

if ($applicationsResponse['status'] === "200") {
    $applications = $applicationsResponse['applications'];
} else {
    $applications = [];
    $message = $applicationsResponse['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="application-container">
        <h1>Your Job Applications</h1>

        <?php if (!empty($applications)): ?>
            <table>
                <tr>
                    <th>Application ID</th>
                    <th>Job Title</th>
                    <th>Position Applied</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['application_id']); ?></td>
                        <td><?php echo htmlspecialchars($application['title']); ?></td>
                        <td><?php echo htmlspecialchars($application['position_applied']); ?></td>
                        <td><?php echo htmlspecialchars($application['date_applied']); ?></td>
                        <td><?php echo htmlspecialchars($application['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p><?php echo isset($message) ? htmlspecialchars($message) : "You have no applications submitted." ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
