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

if (isset($_POST['insertNewAdminBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);
	$is_admin = true;

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../alladmins.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register-an-admin.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register-an-admin.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register-an-admin.php");
	}
}

// Job Post Creation Handler
if (isset($_POST['createJobPostBtn'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $posted_by = $_SESSION['user_id'];

    if (!empty($title) && !empty($description)) {
        $createJobPostQuery = createJobPost($pdo, $title, $description, $posted_by);
        $_SESSION['jobPostMessage'] = $createJobPostQuery['message'];
        $_SESSION['jobPostStatus'] = $createJobPostQuery['status'];
        header("Location: ../index.php");
    } else {
        $_SESSION['jobPostMessage'] = "Please fill in all fields";
        $_SESSION['jobPostStatus'] = '400';
        header("Location: ../index.php");
    }
}

if (isset($_POST['post_id'])) {
    $postId = intval($_POST['post_id']);
    $userId = $_SESSION['user_id'];

    // Check if the job post belongs to the current user
    $sql = "SELECT posted_by FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    if ($post && $post['posted_by'] == $userId) {
        // Delete the job post
        $sql = "DELETE FROM job_posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$postId])) {
            $_SESSION['message'] = "Job post deleted successfully!";
            $_SESSION['status'] = '200';
        } else {
            $_SESSION['message'] = "An error occurred while deleting the job post.";
            $_SESSION['status'] = '400';
        }
    } else {
        $_SESSION['message'] = "You are not authorized to delete this job post.";
        $_SESSION['status'] = '400';
    }

    header("Location: ../viewyourJobPost.php");
}

if (isset($_GET['viewPostId'])) {
    $postId = intval($_GET['viewPostId']);
    $jobPost = getJobPostById($pdo, $postId);

    if ($jobPost) {
        // Store the job post data in a session or pass it to the view page directly
        $_SESSION['jobPost'] = $jobPost;
        header("Location: ../viewPost.php");
    } else {
        $_SESSION['message'] = "Job post not found!";
        $_SESSION['status'] = '400';
        header("Location: ../index.php");
    }
}

if (isset($_POST['acceptApplication']) || isset($_POST['rejectApplication'])) {
    $applicationId = intval($_POST['application_id']);
    $postId = intval($_POST['job_post_id']); // Use job post ID passed from the form
    $status = isset($_POST['acceptApplication']) ? 'Accepted' : 'Rejected';

    // Update application status in the database
    if (updateApplicantStatus($pdo, $applicationId, $status)) {
        $_SESSION['message'] = "Application status updated successfully!";
        $_SESSION['status'] = '200';
    } else {
        $_SESSION['message'] = "Failed to update application status!";
        $_SESSION['status'] = '400';
    }

    // Redirect to the job post page after updating the status
    header("Location: ../viewPost.php?id=" . $postId);
    exit();
}



if (isset($_POST['sendMessage'])) {
    // Get the admin's ID (sender)
    $sender_id = $_SESSION['user_id'];

    // Fetch the applicant ID (receiver) from the job applications table
    $application_id = $_POST['application_id'];  // Assuming you have this in the form
    $query = "SELECT applicant_id FROM job_applications WHERE application_id = :application_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':application_id' => $application_id]);
    $applicant = $stmt->fetch();

    // Check if an applicant exists for this application
    if ($applicant) {
        $receiver_id = $applicant['applicant_id'];  // Set the applicant's ID as receiver
    } else {
        // Handle case where no applicant was found
        $_SESSION['message'] = "No applicant found for this application.";
        $_SESSION['status'] = '400';
        header("Location: ../admin/see-all-messages.php?application_id=" . $application_id);
exit();

    }

    // Get the message content
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

    // Redirect to the messages page
    header("Location: see-all-messages.php?application_id=" . $application_id);
    exit();
}




if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}

?>




