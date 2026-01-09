<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Auth2FA.php';

if (!isset($_SESSION['pending_2fa_user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Db();
$auth = new Auth2FA($db->pdo());

$error = null;
$userId = (int)$_SESSION['pending_2fa_user_id'];

$stmt = $db->pdo()->prepare('SELECT id, username, 2fa_secret FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    unset($_SESSION['pending_2fa_user_id']);
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    if ($auth->verifyCode($user['2fa_secret'], $code)) {
        unset($_SESSION['pending_2fa_user_id']);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = (string)$user['username'];

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Ongeldige 2FA code. Probeer opnieuw.';
    }
}
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>2FA Verify</title>
</head>
<body>
  <h1>2FA Code</h1>
  <p>Open Google Authenticator en vul je 6-cijferige code in.</p>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Authenticator code</label><br>
    <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" required><br><br>
    <button type="submit">Inloggen</button>
  </form>

  <p><a href="logout.php">Annuleren</a></p>
</body>
</html>
