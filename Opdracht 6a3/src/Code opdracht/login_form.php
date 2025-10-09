<?php
// Functie: programma login OOP
// Auteur: Berke

require_once('classes/User.php');

$user = new User();
$errors = [];

// Is de login button aangeklikt?
if (isset($_POST['login-btn'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Valideer invoer
    $errors = $user->validateLogin(['username' => $username]);

    if (empty($password)) {
        $errors['password'] = 'Wachtwoord is verplicht.';
    }

    // Indien geen fouten → probeer in te loggen
    if (count($errors) === 0) {
        if ($user->loginUser($username, $password)) {
            // Login geslaagd
            header("Location: index.php");
            exit;
        } else {
            $errors['login'] = 'Onjuiste gebruikersnaam of wachtwoord.';
        }
    }

    // Toon fouten (alertbox)
    if (count($errors) > 0) {
        $message = implode("\\n", $errors);
        echo "<script>alert('$message');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
</head>
<body>

<h3>Login Pagina</h3>
<hr/>

<form action="" method="POST">
    <label>Gebruikersnaam</label><br>
    <input type="text" name="username" required><br><br>

    <label>Wachtwoord</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login-btn">Inloggen</button>
</form>

<p>Nog geen account? <a href="register_form.php">Registreer hier</a></p>

</body>
</html>
