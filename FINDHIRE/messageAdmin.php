<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
if ($job_id <= 0) {
    header("Location: index.php");
}

$jobDetails = getJobDetails($pdo, $job_id);
$admin_id = $jobDetails['posted_by']; // Admin user who posted the job
$applicant_id = $_SESSION['user_id']; // Current logged-in user (applicant)

$messages = getMessages($pdo, $applicant_id, $admin_id); // Fetch messages between applicant and HR

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages with HR</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        /* Chatbox container */
        .chat-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f4f4f4;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Individual message styling */
        .message {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f1f1f1;
            position: relative;
        }

        .message p {
            margin: 0;
        }

        .sent {
            background-color: #C1CFA1;
            align-self: flex-end;
        }

        .received {
            background-color: #E7CCCC;
            align-self: flex-start;
        }

        /* Sender's name styling */
        .message strong {
            font-weight: bold;
        }

        /* Date/Time of message */
        .message small {
            font-size: 0.8em;
            color: #888;
        }

        /* Textarea and send button styling */
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            resize: none;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Header styling */
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="chat-container">
        <h1>Messages with HR</h1>

        <!-- Message Display Section -->
        <div class="message-box">
            <?php if (empty($messages)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo ($message['sender_id'] == $applicant_id) ? 'sent' : 'received'; ?>">
                        <p><strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                        <p><small><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($message['timestamp']))); ?></small></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Message Sending Form -->
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
            <input type="hidden" name="receiver_id" value="<?php echo $admin_id; ?>">
            <textarea name="message" required placeholder="Write your message here..."></textarea><br>
            <input type="submit" name="sendMessage" value="Send Message">
        </form>
    </div>
</body>
</html>
