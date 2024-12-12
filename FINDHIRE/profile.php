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

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>

	<?php $getUserByID = getUserByID($pdo, $_GET['user_id']); ?>
	<div class="container" style="display: flex; justify-content: center;">
		<div class="userInfo" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%; text-align: center;">
			<h3>Username: <span style="color: blue"><?php echo $getUserByID['username']; ?></span></h3>
			<h3>First Name: <span style="color: blue"><?php echo $getUserByID['first_name']; ?></span></h3>
			<h3>Last Name: <span style="color: blue"><?php echo $getUserByID['last_name']; ?></span></h3>
			<h3>Date Joined: <span style="color: blue"><?php echo $getUserByID['date_added']; ?></span></h3>
		</div>
	</div>
</body>
</html>