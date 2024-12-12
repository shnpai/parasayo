<?php
// Include the database configuration file and models
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['application_id'])) {
    echo "Application ID not provided!";
    exit();
}

$applicationId = intval($_GET['application_id']);
$application = getApplicationById($pdo, $applicationId);

if (!$application) {
    echo "Application not found!";
    exit();
}

// Fetch job applications
$applications = getApplications($pdo);
$jobPostId = $application['job_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #EDE8DC;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border: 1px solid #A5B68D;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header {
            background-color: #C1CFA1;
            padding: 10px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            font-size: 18px;
            color: #fff;
        }

        .details {
            margin: 20px 0;
        }

        .details p {
            margin: 5px 0;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        button {
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .accept {
            background-color: #A5B68D;
            color: white;
        }

        .reject {
            background-color: #E7CCCC;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Application Details
        </div>
        <div class="details">
            <p><strong>Application ID:</strong> <?= $application['application_id'] ?></p>
            <p><strong>First Name:</strong> <?= htmlspecialchars($application['first_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
            <p><strong>Position Applied:</strong> <?= htmlspecialchars($application['position_applied']) ?></p>
            <p><strong>Profile Description:</strong> <?= htmlspecialchars($application['description'])?></p>
            <p><strong>Resume:</strong> 
                <a href="<?= htmlspecialchars($application['resume_path']) ?>" download>Download</a>
            </p>
        </div>
        <form class="actions" method="POST" action="core/handleForms.php">
            <input type="hidden" name="application_id" value="<?= $application['application_id'] ?>">
            <input type="hidden" name="job_post_id" value="<?php echo htmlspecialchars($jobPostId); ?>">
            <button type="submit" name="acceptApplication" class="accept">Accept</button>
            <button type="submit" name="rejectApplication" class="reject">Reject</button>
        </form>
    </div>
</body>
</html>