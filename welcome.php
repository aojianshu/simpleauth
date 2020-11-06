<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
	header("location: login.php");
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Simple Auth</title>
	<link rel="stylesheet" href="public/css/app.css">
</head>
<body class="h-screen w-full flex items-center justify-center bg-gray-200">
	<div class="text-center">
		<h1 class="text-2xl">Welcome <b><?php echo htmlspecialchars($_SESSION["username"]);?></b></h1>
		<a href="logout.php" class="text-blue-600 font-semibold hover:text-blue-800 hover:underline">Logout</a>
	</div>
</body>
</html>