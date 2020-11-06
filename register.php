<?php
// Include config file
require_once "config.php";

// Initialize variables with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing logic when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Validate username
	if (empty(trim($_POST["username"]))) {
		$username_err = "Please enter username.";
	}else{
		$sql = "SELECT id FROM users WHERE username = ?";

		if($stmt = $mysqli->prepare($sql)){
			// Bind variables to the prepared statement as parameters
			$stmt->bind_param("s", $param_username);

			// Set parameters
			$param_username = trim($_POST["username"]);

			// Attempt to execute the prepared statement
			if($stmt->execute()){
				$stmt->store_result();


				if ($stmt->num_rows == 1) {
					$username_err = "This username is already taken.";
				}else{
					$username = trim($_POST["username"]);
				}
			}else{
				echo "Oops! Something went wrong. Please try again later.";
			}

			$stmt->close();
		}
	}

	// Validate password
	if (empty(trim($_POST["password"]))) {
		$password_err = "Please enter a password.";
	}elseif (strlen(trim($_POST["password"])) < 6) {
		$password_err = "Password must have atleast 6 characters.";
	}else{
		$password = trim($_POST["password"]);
	}

	// Validate confirm password
	if (empty(trim($_POST["confirm_password"]))) {
		$confirm_password_err = "Please confirm password";
	}else{
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($password_err) && ($password != $confirm_password)) {
			$confirm_password_err = "Password did not match.";
		}
	}

	// Check input errors before inserting in database
	if (empty($username_err) && empty($password_err)  && empty($confirm_password_err)) {
		$sql = "INSERT INTO users (username, password) VALUES (?, ?)";

		if ($stmt = $mysqli->prepare($sql)) {
			// Bind variables to the prepared statement as parameters
			$stmt->bind_param("ss", $param_username, $param_password);

			// Set parameters
			$param_username = $username;
			$param_password = password_hash($password, PASSWORD_DEFAULT);
		}

		// Attempt to execute the prepared statement
		if ($stmt->execute()) {
			// Redirect to login page
			header("location: login.php");
		}else{
			echo "Something went wrong. Please try again later.";
		}

		$stmt->close();
	}
}
$mysqli->close();
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
			<div class="mb-2">
				<div class="mb-1">
					<label for="password" class="text-sm font-semibold">Password</label>
				</div>
				<div class="mb-2">
					<input name="password" type="password" class="w-full border border-gray-400 rounded-md p-2 focus:outline-none focus:shadow-outline <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
					<span class="text-red-500 text-sm font-semibold"> <?php echo $password_err; ?> </span>
				</div>
			</div>
			<div class="mb-4">
				<div class="mb-1">
					<label for="confirm_password" class="text-sm font-semibold">Confirm Password</label>
				</div>
				<div class="mb-2">
					<input name="confirm_password" type="password" class="w-full border border-gray-400 rounded-md p-2 focus:outline-none focus:shadow-outline <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>">
					<span class="text-red-500 text-sm font-semibold"><?php echo $confirm_password_err; ?></span>
				</div>
			</div>
			<div class="space-x-1 flex items-center mb-2">
				<button type="submit" class="px-8 py-2 bg-blue-600 border border-blue-600 text-white rounded-md hover:bg-blue-700 focus:bg-blue-800">Submit</button>
				<button type="reset" class="px-8 py-2 border border-gray-500 rounded-md hover:border-gray-700 focus:border-gray-900">Reset</button>
			</div>
			<div class="text-sm">
				<h1>Already have an account? <a href="login.php" class="text-blue-600 font-semibold hover:text-blue-800 hover:underline">Login here.</a></h1>
			</div>
		</form>
	</div>
</body>
</html>