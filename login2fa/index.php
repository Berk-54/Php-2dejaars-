<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Login2FA - Overzicht</title>
</head>
<body>
  <h1>Login2FA - Overzicht</h1>

  <ul>
    <li><a href="registreren.php">Registreren (QR + secret)</a></li>
    <li><a href="login.php">Login</a></li>
    <li><a href="dashboard.php">Dashboard (na login)</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>

  <h3>Checklist</h3>
  <ol>
    <li>Importeer <code>login2fa.sql</code> in phpMyAdmin</li>
    <li>Controleer DB-gegevens in <code>Db.php</code> (XAMPP: root + leeg wachtwoord)</li>
    <li>Ga naar <code>registreren.php</code> en maak een account</li>
    <li>Scan QR met Google Authenticator</li>
    <li>Log in via <code>login.php</code> en voer daarna je 2FA code in</li>
  </ol>
</body>
</html>
