<div class="navbar" style="text-align: center; margin-bottom: 50px;">
	<h1>Welcome to Applicant's Page, <span style="color: blue;"><?php echo $_SESSION['username']; ?></span></h1>
	<a href="index.php">Home</a>
	<a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">Your Profile</a>
	<a href="viewapplications.php">View Applications</a>
	<a href="core/handleForms.php?logoutUserBtn=1">Logout</a>
	
</div>