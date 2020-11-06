<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
	header("location: welcome.php");
	exit;
}

// Include the config file
require_once "config.php";

// Initialize the variables with empty values
$username = $password = "";
$username_err = $password_err = "";

// Process logic when form is submitted
if ($_SERVER["REQUEST_METHOD"] == POST) {

	// Check if the username is empty
	if (empty(trim($_POST["username"]))) {
		$username_err = "Please enter username.";
	}else{
		$username = trim($_POST["username"]);
	}

	// Check if the password is empty
	if (empty(trim($_POST["password"]))) {
		$password_err = "Please enter your password";
	}else{
		$password = trim($_POST["password"]);
	}

	// Validate credentials
	if (empty($username_err) && empty($password_err)) {
		$sql = "SELECT id, username, password FROM users WHERE username = ?";

		if($stmt = $mysqli->prepare($sql)){
			// Bind variables to the prepared statement as parameters
			$stmt->bind_param("s", $param_username);

			// Set parameters
			$param_username = $username;

			// Attempt to execute prepared statement
			if($stmt->execute()){
				$stmt->store_result();

				// Check if the username exists, if yes then verify password
				if ($stmt->num_rows == 1) {
					$stmt->bind_result($id, $username, $hashed_password);
					if ($stmt->fetch()) {
						if (password_verify($password, $hashed_password)) {
							// Password is correct, start a new session
							session_start();

							// Store data in session variables
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["username"] = $username;

							// Redirect to welcome page
							header("location: welcome.php");
						}else{
							// Display an error message if password is not valid
							$password_err = "The password you entered was not valid.";
						}
					}
				}else{
					//Display an error message if username does not exists
					$username_err = "No account found with that username.";
				}
			}else{
				echo "Oops! Something went wrong. Please try again later.";
			}
			$stmt->close();
		}
	}
	$mysqli->close();
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
	<div class="w-full max-w-lg shadow-lg rounded-xl bg-white px-6 py-4">
		<h1 class="text-2xl mb-2">Sign Up</h1>
		<div class="bg-gradient-to-r from-blue-400 to-blue-700 h-1 rounded-full mb-2"></div>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<div class="mb-2">
				<div class="mb-1">
					<label class="text-sm font-semibold" for="username">Username</label>
				</div>
				<div>
					<input name="username" type="text" class="w-full border border-gray-400 rounded-md p-2 focus:outline-none focus:shadow-outline <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>">
					<span class="text-red-500 text-sm font-semibold"><?php echo $username_err; ?></span>
				</div>
			</div>
			<div class="mb-4">
				<div class="mb-1">
					<label for="password" class="text-sm font-semibold">Password</label>
				</div>
				<div class="mb-2">
					<input name="password" type="password" class="w-full border border-gray-400 rounded-md p-2 focus:outline-none focus:shadow-outline <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
					<span class="text-red-500 text-sm font-semibold"> <?php echo $password_err; ?> </span>
				</div>
			</div>
			<div class="space-x-1 flex items-center mb-2">
				<button class="px-8 py-2 bg-blue-600 border border-blue-600 text-white rounded-md hover:bg-blue-700 focus:bg-blue-800">Submit</button>
				<button type="reset" class="px-8 py-2 border border-gray-500 rounded-md hover:border-gray-700 focus:border-gray-900">Reset</button>
			</div>
			<div class="text-sm">
				<h1>Don't have an account yet? <a href="register.php" class="text-blue-600 font-semibold hover:text-blue-800 hover:underline">Register here</a></h1>
			</div>
		</form>
	</div>
</body>
</html>