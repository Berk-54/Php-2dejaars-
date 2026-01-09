<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Auth2FA.php';

$db = new Db();
$auth = new Auth2FA($db->pdo());

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    $check = $auth->checkPassword($username, $password);

    if (!$check['ok']) {
        $error = 'Verkeerde username of password.';
    } else {
        $_SESSION['pending_2fa_user_id'] = (int)$check['user']['id'];
        header('Location: verify.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>Login - 2FA</title>
</head>
<body>
  <h1>Login</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Verder</button>
  </form>

  <p><a href="registreren.php">Registreren</a> | <a href="index.php">Terug</a></p>
</body>
</html>
