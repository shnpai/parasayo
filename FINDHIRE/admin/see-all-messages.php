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

// Check if the application ID is provided
if (!isset($_GET['application_id'])) {
    $_SESSION['message'] = "No application specified!";
    header("Location: viewyourJobPost.php");
    exit;
}

// Fetch unread messages count
$unreadCount = getUnreadMessagesCount($pdo, $_SESSION['user_id']);

// Get the application ID
$applicationId = intval($_GET['application_id']);

// Fetch messages related to the specific application
$messages = getMessagesByApplicationId($pdo, $applicationId);

// Fetch job application details to display the applicant's name and position
$applicationDetails = getJobApplicationById($pdo, $applicationId);

// Check if the user is an admin
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Get the current logged-in user ID
$applicant_id = $_SESSION['user_id'];

// Check if application details exist
if (!$applicationDetails) {
    $_SESSION['message'] = "Application not found!";
    header("Location: viewyourJobPost.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages for Application</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        /* Styling for the chatbox */
        .chat-container {
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f4f4f4;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .message-box {
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: scroll;
        }

        .message {
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            max-width: 80%;
        }

        .sent {
            background-color: #C1CFA1;
            align-self: flex-end;
        }

        .received {
            background-color: #E7CCCC;
            align-self: flex-start;
        }

        /* Textarea for sending messages */
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        /* Submit button */
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Heading styles */
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h1>Messages for Application: <?php echo htmlspecialchars($applicationDetails['first_name']); ?></h1>
    <h2>Position Applied: <?php echo htmlspecialchars($applicationDetails['position_applied']); ?></h2>

    <!-- Chatbox Layout -->
    <div class="chat-container">
    <div class="message-box">
    <?php if (empty($messages)): ?>
        <p style="text-align: center;">No messages found for this application.</p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo ($message['sender_id'] == $_SESSION['user_id']) ? 'sent' : 'received'; ?>">
                <p><strong><?php echo htmlspecialchars($message['sender_username']); ?>:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                <p><small><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($message['timestamp']))); ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>



        <!-- Admin Reply Form -->
        <form action="core/handleForms.php" method="POST" style="max-width: 800px; margin: 0 auto;">
            <input type="hidden" name="application_id" value="<?php echo $applicationId; ?>">
            <input type="hidden" name="receiver_id" value="<?php echo $applicant_id; ?>"> <!-- The receiver is the applicant -->

            <textarea name="message" required placeholder="Type your reply here..."></textarea>
            <input type="submit" name="sendMessage" value="Send Reply">
        </form>
    </div>

</body>
</html>
