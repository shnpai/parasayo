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
	<div class="container" style="width: 75%; margin: auto;">
		<h1 style="text-align: center;">Sorry, <span style="color: red"><?php echo $_SESSION['username']; ?></span>, but your account is currently suspended. Please contact an admin. Please <a href="core/handleForms.php?logoutUserBtn=1">logout</a> immediately.</h1>		
	</div>


</body>
</html>