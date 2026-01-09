<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Auth2FA.php';

$db = new Db();
$auth = new Auth2FA($db->pdo());

$result = ['ok' => false, 'error' => null, 'qr' => null, 'secret' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->registerFromPost($_POST);
}
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Registreren - 2FA</title>
</head>
<body>
  <h1>Register</h1>

  <?php if (!empty($result['error'])): ?>
    <p style="color:red;"><?php echo htmlspecialchars($result['error']); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Registreren</button>
  </form>

  <?php if (!empty($result['qr'])): ?>
    <h3>Registratie succesvol! Scan deze QR code met Google Authenticator:</h3>
    <img src="<?php echo htmlspecialchars($result['qr']); ?>" alt="QR Code"><br>
    <p>Sla de geheime sleutel op: <strong><?php echo htmlspecialchars($result['secret']); ?></strong></p>
  <?php endif; ?>

  <p><a href="login.php">Login</a> | <a href="index.php">Terug</a></p>
</body>
</html>
