<?php  
require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM user_accounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}

function insertNewUser($pdo, $username, $first_name, $last_name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO user_accounts (username, first_name, last_name, password) 
		VALUES (?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_admin = 0";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllAdmins($pdo) {
	$sql = "SELECT * FROM user_accounts 
			WHERE is_admin = 1";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getUserByID($pdo, $user_id) {
	$sql = "SELECT * FROM user_accounts WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}

// Function to create a new job post
function createJobPost($pdo, $title, $description, $posted_by) {
    $response = array();

    $sql = "INSERT INTO job_posts (title, description, posted_by) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $description, $posted_by])) {
        $response = array(
            "status" => "200",
            "message" => "Job post created successfully!"
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "An error occurred while creating the job post!"
        );
    }

    return $response;
}

// Function to get all job posts
function getAllJobPosts($pdo) {
    $sql = "SELECT job_posts.*, user_accounts.username, user_accounts.first_name, user_accounts.last_name 
            FROM job_posts 
            JOIN user_accounts ON job_posts.posted_by = user_accounts.user_id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        return $stmt->fetchAll();
    }

    return array();
}



function getApplications($pdo) {
    $response = array();
    $sql = "SELECT * FROM job_applications";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();

    if ($executeQuery) {
        $response = $stmt->fetchAll();
    } else {
        $response = array(
            "status" => "400",
            "message" => "Failed to retrieve applications."
        );
    }

    return $response;
}

// Function to get applicants for a specific job
function getApplicantsByJob($pdo, $job_id) {
    $sql = "SELECT * FROM job_applications WHERE job_id = ? AND status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$job_id]);
    return $stmt->fetchAll();
}

// Function to delete a job post by its ID
function deleteJobPost($pdo, $postId, $userId) {
    $response = array();

    // Check if the job post belongs to the user
    $sql = "SELECT posted_by FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    if ($post && $post['posted_by'] == $userId) {
        $sql = "DELETE FROM job_posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$postId])) {
            $response = array(
                "status" => "200",
                "message" => "Job post deleted successfully."
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "Failed to delete job post."
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "You are not authorized to delete this job post."
        );
    }

    return $response;
}

// Function to get a job post by its ID
function getJobPostById($pdo, $jobId) {
    $sql = "SELECT * FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobId]);
    return $stmt->fetch();
}

function getApplicantsByJobId($pdo, $jobId) {
    $sql = "SELECT application_id, first_name, email, position_applied, resume_path, status
            FROM job_applications
            WHERE job_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationById($pdo, $applicationId) {
    $sql = "SELECT * FROM job_applications WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicationId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getHiredApplicantsByJobId($pdo, $jobId) {
    $sql = "SELECT application_id, first_name FROM job_applications WHERE job_id = ? AND status = 'accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobId]);
    return $stmt->fetchAll();
}

function updateApplicantStatus($pdo, $applicationId, $status) {
    $sql = "UPDATE job_applications SET status = ? WHERE application_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $applicationId]);
}



function sendMessage($pdo, $sender_id, $receiver_id, $message) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, timestamp, status) 
            VALUES (?, ?, ?, NOW(), 'sent')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$sender_id, $receiver_id, $message])) {
        return [
            'status' => '200', 
            'message' => 'Message sent successfully.'
        ];
    } else {
        return [
            'status' => '400', 
            'message' => 'Failed to send the message.'
        ];
    }
}




function getAllMessagesByJob($pdo, $job_id) {
    $sql = "SELECT messages.id, messages.sender_id, messages.receiver_id, messages.message, messages.timestamp, messages.status,
                   user_accounts.username AS sender_name
            FROM messages
            JOIN user_accounts ON messages.sender_id = user_accounts.user_id
            WHERE messages.job_id = :job_id
            ORDER BY messages.timestamp ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':job_id' => $job_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMessagesByApplicationId($pdo, $applicationId) {
    $query = "
        SELECT 
            messages.id AS id,
            messages.sender_id AS sender_id,
            messages.receiver_id AS receiver_id,
            messages.message AS message,
            messages.timestamp AS timestamp,
            user_accounts.username AS sender_username
        FROM 
            messages
        INNER JOIN 
            user_accounts ON messages.sender_id = user_accounts.user_id
        INNER JOIN 
            job_applications ON job_applications.applicant_id = messages.sender_id
        WHERE 
            job_applications.application_id = :application_id
        ORDER BY 
            messages.timestamp;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':application_id', $applicationId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMessages($pdo, $sender_id, $receiver_id) {
    $sql = "SELECT messages.*, user_accounts.username AS sender_name 
            FROM messages
            JOIN user_accounts ON messages.sender_id = user_accounts.user_id
            WHERE (messages.sender_id = :sender_id AND messages.receiver_id = :receiver_id)
               OR (messages.sender_id = :receiver_id AND messages.receiver_id = :sender_id)
            ORDER BY timestamp ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getJobApplicationById($pdo, $applicationId) {
    // Prepare the SQL query to fetch job application details
    $query = "
        SELECT 
            job_applications.application_id AS application_id,
            job_applications.first_name AS first_name,
            job_applications.position_applied AS position_applied
        FROM 
            job_applications
        WHERE 
            job_applications.application_id = :application_id
    ";

    // Prepare the statement
    $stmt = $pdo->prepare($query);

    // Bind the application_id parameter to the prepared statement
    $stmt->bindParam(':application_id', $applicationId, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the result as an associative array
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the result set
    return $result;
}


function markMessagesAsRead($pdo, $admin_id, $applicant_id, $job_id) {
    $sql = "UPDATE messages 
            SET status = 'read' 
            WHERE receiver_id = :admin_id 
            AND sender_id = :applicant_id 
            AND job_id = :job_id 
            AND status = 'sent'";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':admin_id' => $admin_id,
        ':applicant_id' => $applicant_id,
        ':job_id' => $job_id,
    ]);
}

function getUnreadMessagesCount($pdo, $admin_id) {
    $sql = "SELECT COUNT(*) AS unread_count 
            FROM messages 
            WHERE receiver_id = :admin_id AND status = 'sent'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':admin_id' => $admin_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
}




function suspendAccount($pdo, $user_id) {
	$sql = "UPDATE user_accounts SET is_suspended = 1
			WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return true;
	}
}

function unsuspendAccount($pdo, $user_id) {
	$sql = "UPDATE user_accounts SET is_suspended = 0
			WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return true;
	}
}

?>