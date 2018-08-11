<?php

	session_start();

	$message = "";

	$shell = shell_exec('ps -x');

	$array = explode("\n", $shell);

	for ($i = 1; $i < count($array); $i++) {
		
		$command = substr($array[$i], 0, strpos($array[$i], '/dep'));
	    	$idps = substr($command, 0, strpos($command, ' ?'));
	    	shell_exec('kill '.$idps);
	}

	if (array_key_exists("submit", $_POST)) {

		// $link = mysqli_connect('db', 'user', 'test', "myDb");

		$link = mysqli_connect('localhost', 'root', 'Admin2015', 'myDb');

		if (mysqli_connect_error()) {

			die ("Database Connection Error");

		}

		$email = mysqli_real_escape_string($link, $_POST["email"]);

		$passwordInserted = mysqli_real_escape_string($link, $_POST["password"]);

		$query = "SELECT * FROM users WHERE email = '".$email."'";

		$result = mysqli_query($link, $query);

		$numberOfRows = mysqli_num_rows($result);

		if ($numberOfRows == 1) {

			$row = mysqli_fetch_array($result);

			$password = $row["password"];

			$id = $row["id"];

			$passwordInserted = SHA1($email.$passwordInserted);

			if ($passwordInserted == $password) {

				$_SESSION["id"] = $id;

				header("Location: control.php");

				exit;

			} else {

				$message = "<p class='bold err'>La password inserita non è corretta<p>";

			}

		} else {

			$message = "<p class='bold err'>L'email inserita non esiste<p>";

		}

	} else {

		session_unset();

		session_destroy();

	}

?>

<!DOCTYPE html>
<html>

	<head>

		<title>Log In</title>

		<style>

			p.bold {
				font-weight: bold;
			}

			p.err {
				color: red;
			}

		</style>

	</head>

	<body>
				
		<p class='bold'>Inserire email e password per connettersi al depuratore</p>
		
		<form method="post">

		    <input type="email" name="email" placeholder="Email" required>

		    <input type="password" name="password" placeholder="Password" required>

		    <input type="submit" name="submit" value="Log In">

		    <?php 

	    		if ($message != "") {

	    			echo $message;

	    		}

	    	?>

		</form>

	</body>

</html>
