<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>

<?php  
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}

$getUserByID = getUserByID($pdo, $_SESSION['user_id']);

if ($getUserByID['is_admin'] == 0) {
	header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
	<?php include 'navbar.php'; ?>
	<?php $getUserByID = getUserByID($pdo, $_GET['user_id']); ?>

	<?php if ($getUserByID['is_suspended'] == 0) { ?>
		<h1 style="text-align: center;">Are you sure you want to suspend user?</h1>
	<?php } else { ?>
		<h1 style="text-align: center;">Are you sure to want to allow the user to get back?</h1>		
	<?php } ?>

	<div class="container" style="display: flex; justify-content: center;">
		<div class="userInfo" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%; margin-top: 25px; padding: 50px;">
			<h3>Username: <span style="color: blue"><?php echo $getUserByID['username']; ?></span></h3>
			<h3>First Name: <span style="color: blue"><?php echo $getUserByID['first_name']; ?></span></h3>
			<h3>Last Name: <span style="color: blue"><?php echo $getUserByID['last_name']; ?></span></h3>
			<h3>Date Joined: <span style="color: blue"><?php echo $getUserByID['date_added']; ?></span></h3>

			<?php if ($getUserByID['is_suspended'] == 0) { ?>
				<form action="core/handleForms.php" method="POST">
					<input type="hidden" name="user_id" value="<?php echo $getUserByID['user_id']; ?>">
					<input type="submit" value="Suspend" style="float:right; width:25%; padding:10px;" name="suspendAccountBtn">
				</form>
			<?php } else {  ?>
				<form action="core/handleForms.php" method="POST">
					<input type="hidden" name="user_id" value="<?php echo $getUserByID['user_id']; ?>">
					<input type="submit" value="Unsuspend" style="float:right; width:25%; padding:10px;" name="unsuspendAccountBtn">
				</form>
			<?php } ?>
		</div>
	</div>
</body>
</html>