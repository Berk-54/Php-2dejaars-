<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
</head>
<body>
  <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
  <p>Je bent ingelogd met 2FA.</p>
  <p><a href="logout.php">Logout</a> | <a href="index.php">Home</a></p>
</body>
</html>
