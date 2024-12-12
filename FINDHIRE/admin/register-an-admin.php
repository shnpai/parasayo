<?php  
require_once 'core/models.php'; 
require_once 'core/handleForms.php'; 

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
	<div class="centeredContainer" style="display: flex; justify-content: center; margin-top: 25px;">
		<div class="formContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%; padding: 25px;">
			<?php  
			if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

				if ($_SESSION['status'] == "200") {
					echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
				}

				else {
					echo "<h1 style='color: red; border-style:solid; padding:25px;'>{$_SESSION['message']}</h1>";	
				}

			}
			unset($_SESSION['message']);
			unset($_SESSION['status']);
			?>
			<h1 style="text-align: center;">Create an Admin Account</h1>
			<h1 style="background-color: #FFB6C1; padding: 25px; color: red;">Please take note of the admin's password</h1>
			<form action="core/handleForms.php" method="POST">
				<p>
					<label for="username">Username</label>
					<input type="text" name="username">
				</p>
				<p>
					<label for="username">First Name</label>
					<input type="text" name="first_name">
				</p>
				<p>
					<label for="username">Last Name</label>
					<input type="text" name="last_name">
				</p>
				<p>
					<label for="username">Password</label>
					<input type="password" name="password">
				</p>
				<p>
					<label for="username">Confirm Password</label>
					<input type="password" name="confirm_password">
					<input type="submit" name="insertNewAdminBtn" style="margin-top: 25px;">
				</p>
			</form>
		</div>
	</div>
</body>
</html>