<?php
	// Functie: programma login OOP 
    // Auteur: Studentnaam

    // Initialisatie
	require_once('classes/User.php');
	$user = new User();
	$errors = [];

	// Is de login-knop aangeklikt?
	if (isset($_POST['login-btn'])) {
		// Formuliervelden ophalen en toewijzen
		$user->username = trim($_POST['username']);
		$user->setPassword((string)($_POST['password'] ?? ''));

		// Valideer invoer
		$errors = $user->validateLogin();

		// Indien geen fouten, probeer in te loggen
		if (empty($errors)) {
			if ($user->loginUser()) {
				// Login succesvol → doorsturen naar de index
				header('Location: index.php');
				exit;
			} else {
				$errors[] = 'Onjuiste gebruikersnaam of wachtwoord.';
			}
		}

		// Toon fouten via alert en laad de pagina opnieuw
		if (!empty($errors)) {
			$message = implode("\\n", $errors);
			echo "<script>alert('" . $message . "');</script>";
			echo "<script>window.location = 'login_form.php';</script>";
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	</head>
<body>

	<h3>PHP - PDO Login and Registration</h3>
	<hr/>
	
	<form action="" method="POST">	
		<h4>Login here...</h4>
		<hr>
		
		<label>Username</label>
		<input type="text" name="username" />
		<br>
		<label>Password</label>
		<input type="password" name="password" />
		<br>
		<button type="submit" name="login-btn">Login</button>
		<br>
		<a href="register_form.php">Registration</a>
	</form>
		
</body>
</html>