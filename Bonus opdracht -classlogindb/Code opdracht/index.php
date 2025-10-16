<?php
    // Functie: programma login OOP 
    // Auteur: Studentnaam

    // Initialisatie
	require_once 'classes/User.php';
	
	$user = new User();
?>

<!DOCTYPE html>

<html lang="en">

<body>

	<h3>PDO Login and Registration</h3>
	<hr/>

	<h3>Welcome op de HOME-pagina!</h3>
	<br />

	<?php

    // Activeer de session indien nodig
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    // Indien logout geklikt
    if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
        $user->logout();
    }

    // Controleer of een gebruiker is ingelogd
    if (!$user->isLoggedIn()) {
        // Niet ingelogd → toon melding en link naar login
        echo 'U bent niet ingelogd. Log in om verder te gaan.<br><br>';
        echo '<a href="login_form.php">Login</a>';
    } else {
        // Ingelogd → laad gebruikersgegevens en toon welkomstbericht
        $username = $_SESSION['username'] ?? '';
        $user->getUser($username);
        echo '<h2>Het spel kan beginnen</h2>';
        echo 'Je bent ingelogd met:<br/>';
        $user->showUser();
        echo '<br><br>';
        echo '<a href="?logout=true">Logout</a>';
    }
	
	?>

</body>
</html>