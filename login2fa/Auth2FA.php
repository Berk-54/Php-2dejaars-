<?php
declare(strict_types=1);

require_once __DIR__ . '/GoogleAuthenticator.php';

final class Auth2FA
{
    private PDO $pdo;
    private GoogleAuthenticator $ga;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ga  = new GoogleAuthenticator();
    }

    /**
     * Registreren (POST):
     * - password_hash
     * - secret aanmaken
     * - insert user
     * - QR url teruggeven
     */
    public function registerFromPost(array $post): array
    {
        $username = trim($post['username'] ?? '');
        $passwordRaw = (string)($post['password'] ?? '');

        if ($username === '' || $passwordRaw === '') {
            return ['ok' => false, 'error' => 'Vul username en password in.', 'qrCodeUrl' => null, 'secret' => null];
        }

        $passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);
        $secret = $this->ga->createSecret();

        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password, 2fa_secret) VALUES (?, ?, ?)');
            $stmt->execute([$username, $passwordHash, $secret]);
        } catch (PDOException $e) {
            return ['ok' => false, 'error' => 'Username bestaat al of database fout.', 'qrCodeUrl' => null, 'secret' => null];
        }

        $qrCodeUrl = $this->ga->getQRCodeGoogleUrl('TCRHELDEN', $secret, $username);

        return ['ok' => true, 'error' => null, 'qrCodeUrl' => $qrCodeUrl, 'secret' => $secret];
    }

    /**
     * Login stap 1: username + password check
     */
    public function checkPassword(string $username, string $password): array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, password, 2fa_secret FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return ['ok' => false, 'user' => null];
        if (!password_verify($password, $user['password'])) return ['ok' => false, 'user' => null];

        return ['ok' => true, 'user' => $user];
    }

    /**
     * Login stap 2: 2FA code check
     */
    public function verify2fa(string $secret, string $code): bool
    {
        return $this->ga->verifyCode($secret, $code, 1);
    }
}
