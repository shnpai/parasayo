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
	$sql = "SELECT * FROM user_accounts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllJobPosts($pdo) {
    $sql = "SELECT job_posts.*, user_accounts.username, user_accounts.first_name, user_accounts.last_name 
            FROM job_posts 
            JOIN user_accounts ON job_posts.posted_by = user_accounts.user_id
            ORDER BY job_posts.date_posted DESC";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        return $stmt->fetchAll();
    }

    return array();
}

function submitJobApplication($pdo, $job_id, $applicant_id, $first_name, $email, $position_applied, $resume_path, $description) {
    $response = array();

    $sql = "INSERT INTO job_applications (job_id, applicant_id, first_name, email, position_applied, resume_path, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$job_id, $applicant_id, $first_name, $email, $position_applied, $resume_path, $description])) {
        $response = array(
            "status" => "200",
            "message" => "Application submitted successfully!"
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "An error occurred while submitting the application."
        );
    }

    return $response;
}


function checkExistingApplication($pdo, $applicant_id, $job_id) {
    $sql = "SELECT * FROM job_applications WHERE applicant_id = ? AND job_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicant_id, $job_id]);
    return $stmt->fetch() !== false;
}

function getApplicationsByUser($pdo, $applicant_id) {
    $response = array();
    $sql = "SELECT job_applications.application_id, job_posts.title, job_applications.position_applied, job_applications.status, job_applications.date_applied
            FROM job_applications
            JOIN job_posts ON job_applications.job_id = job_posts.id
            WHERE job_applications.applicant_id = ?
            ORDER BY job_applications.date_applied DESC";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$applicant_id])) {
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($applications) > 0) {
            $response = array(
                "status" => "200",
                "applications" => $applications
            );
        } else {
            $response = array(
                "status" => "404",
                "message" => "No applications found for this user."
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "An error occurred while retrieving the applications."
        );
    }

    return $response;
}





function getJobDetails($pdo, $job_id) {
    $sql = "SELECT * FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$job_id])) {
        $jobDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($jobDetails) {
            return $jobDetails; // Return job details if found
        } else {
            return false; // Job not found
        }
    } else {
        return false; // Error executing the query
    }
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

function showMessages($pdo, $applicant_id, $admin_id) {
    $stmt = $pdo->prepare("SELECT m.*, u.username AS sender_name
                           FROM messages m
                           JOIN users u ON m.sender_id = u.id
                           WHERE (m.sender_id = ? AND m.receiver_id = ?)
                              OR (m.sender_id = ? AND m.receiver_id = ?)
                           ORDER BY m.timestamp ASC");
    $stmt->execute([$applicant_id, $admin_id, $admin_id, $applicant_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





function getUserByID($pdo, $user_id) {
	$sql = "SELECT * FROM user_accounts WHERE user_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$user_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}