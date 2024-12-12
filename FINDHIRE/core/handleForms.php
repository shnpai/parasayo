<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}

if (isset($_POST['applyJobBtn'])) {
    // Retrieve job ID from POST data
    $job_id = trim($_POST['job_id']);

    if (!empty($job_id)) {
        // Redirect the user to applyJob.php with the job ID as a query parameter
        header("Location: ../applyJob.php?job_id=" . urlencode($job_id));
        exit();
    } else {
        // Set session message for invalid job ID
        $_SESSION['message'] = "Invalid job ID";
        $_SESSION['status'] = '400';
        header("Location: ../index.php"); // Redirect back to the job listings page
        exit();
    }
}



if (isset($_POST['submitApplication'])) {
    $job_id = intval($_POST['job_id']);
    $applicant_id = intval($_POST['applicant_id']);

    $existingApplication = checkExistingApplication($pdo, $applicant_id, $job_id);

    if ($existingApplication) {
        $_SESSION['applicationMessage'] = "You have already applied for this job.";
        $_SESSION['applicationStatus'] = '200';
        header("Location: ../viewapplications.php");
        exit();
    }

    $first_name = trim($_POST['first_name']);
    $email = trim($_POST['email']);
    $position_applied = trim($_POST['position_applied']);
    $resume = $_FILES['resume'];
    $description = trim($_POST['description']); // New field

    // Validate inputs
    if (empty($first_name) || empty($email) || empty($position_applied) || empty($resume['name']) || empty($description)) {
        $_SESSION['applicationMessage'] = "All fields are required.";
        $_SESSION['applicationStatus'] = '400';
        header("Location: ../applyJob.php?job_id={$job_id}");
        exit;
    }

    // File upload validation
    $allowed_extensions = ['pdf'];
    $file_extension = pathinfo($resume['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        $_SESSION['applicationMessage'] = "Only PDF files are allowed.";
        $_SESSION['applicationStatus'] = '400';
        header("Location: ../applyJob.php?job_id={$job_id}");
        exit;
    }

    // Save the resume file
    $upload_dir = '../uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $file_name = uniqid() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($resume['tmp_name'], $file_path)) {
        // Submit the application
        $result = submitJobApplication($pdo, $job_id, $applicant_id, $first_name, $email, $position_applied, $file_path, $description);

        $_SESSION['applicationMessage'] = $result['message'];
        $_SESSION['applicationStatus'] = $result['status'];
    } else {
        $_SESSION['applicationMessage'] = "Failed to upload the resume.";
        $_SESSION['applicationStatus'] = '400';
    }

    header("Location: ../applyJob.php?job_id={$job_id}");
    exit;
}


if (isset($_POST['sendMessage'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id']; // Admin user ID (could be fetched from the job post details)
    $message = $_POST['message'];

    // Save the message in the database
    $result = sendMessage($pdo, $sender_id, $receiver_id, $message);

    if ($result['status'] === '200') {
        $_SESSION['message'] = "Message sent successfully!";
        $_SESSION['status'] = '200';
    } else {
        $_SESSION['message'] = "Failed to send the message.";
        $_SESSION['status'] = '400';
    }

    header("Location: messageAdmin.php?job_id=" . $_POST['job_id']);
    exit();
}
if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}