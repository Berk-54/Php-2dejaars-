<?php
	// Functie: programma login OOP 
    // Auteur: Studentnaam
	require_once('classes/User.php');

	$user = new User();
	$errors = [];

	// Is de register-knop aangeklikt?
	if (isset($_POST['register-btn'])) {
		// Gegevens uit formulier halen
		$user->username = trim($_POST['username']);
		$user->setPassword((string)($_POST['password'] ?? ''));
		
		// Valideer invoer
		$errors = $user->validateRegister();
		
		// Indien geen validatie-fouten → registreer gebruiker
		if (empty($errors)) {
			$errors = $user->registerUser();
		}
		
		// Toon fouten of succesbericht
		if (!empty($errors)) {
			$message = implode("\\n", $errors);
			echo "<script>alert('" . $message . "');</script>";
			echo "<script>window.location = 'register_form.php';</script>";
		} else {
			echo "<script>alert('Gebruiker geregistreerd');</script>";
			echo "<script>window.location = 'login_form.php';</script>";
		}
	}
?>

<!DOCTYPE html>
<html lang="en">

<body>
	

		<h3>PHP - PDO Login and Registration</h3>
		<hr/>

			<form action="" method="POST">	
				<h4>Register here...</h4>
				<hr>
				
				<div>
					<label>Username</label>
					<input type="text"  name="username" />
				</div>
				<div >
					<label>Password</label>
					<input type="password"  name="password" />
				</div>
				<br />
				<div>
					<button type="submit" name="register-btn">Register</button>
				</div>
				<a href="index.php">Home</a>
			</form>


</body>
</html>