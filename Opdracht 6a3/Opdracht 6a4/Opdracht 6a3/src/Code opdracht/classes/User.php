<?php
// Functie: classdefinitie User (PDO, password_hash, sessions)
// Auteur: Berke Yildirim

// Optioneel: centrale DB-config (als je db.php gebruikt, laat require staan)
require_once __DIR__ . '/../db.php';

class User
{
    // Attributen
    public string $username = '';
    public string $email    = '';
    public string $role     = 'user';

    private string $password = '';        // plaintext tijdens setten; daarna gehasht opslaan
    private ?\PDO  $_conn    = null;      // PDO connectie

    // -------------------------------
    // Helpers
    // -------------------------------
    private function conn(): \PDO
    {
        // Gebruik gedeelde PDO uit db.php, of maak eigen connectie
        if ($this->_conn instanceof \PDO) {
            return $this->_conn;
        }

        // 1) Via db.php (singleton):
        if (function_exists('getPDO')) {
            $this->_conn = getPDO();
            return $this->_conn;
        }

        // 2) Losse connectie (fallback)
        $host = 'localhost';
        $dbname = 'Login';
        $user = 'root';
        $pass = '';
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $opts = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->_conn = new \PDO($dsn, $user, $pass, $opts);
        return $this->_conn;
    }

    public function setPassword(string $password): void
    {
        $this->password = trim($password);
    }

    // Handig bij debug/voorbeeld
    public function showUser(): void
    {
        echo htmlspecialchars($this->username) . " (" . htmlspecialchars($this->role) . ")";
    }

    // -------------------------------
    // Validatie
    // -------------------------------
    /** Valideer login velden (username + password verplicht) */
    public function validateLogin(array $data = []): array
    {
        $errors = [];

        $u = $data['username'] ?? $this->username;
        $p = $data['password'] ?? $this->password;

        if (!is_string($u) || trim($u) === '') {
            $errors[] = 'Gebruikersnaam is verplicht.';
        }
        if (!is_string($p) || trim($p) === '') {
            $errors[] = 'Wachtwoord is verplicht.';
        }
        return $errors;
    }

    /** Valideer registratie velden */
    public function validateRegister(): array
    {
        $errors = [];
        if ($this->username === '' || strlen($this->username) < 3) {
            $errors[] = 'Gebruikersnaam moet minimaal 3 tekens zijn.';
        }
        if ($this->password === '' || strlen($this->password) < 6) {
            $errors[] = 'Wachtwoord moet minimaal 6 tekens zijn.';
        }
        if ($this->email !== '' && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mailadres is ongeldig.';
        }
        return $errors;
    }

    // -------------------------------
    // Database acties
    // -------------------------------
    /** Haal 1 user op (array of null) */
    public function getUser(string $username): ?array
    {
        $sql = "SELECT id, username, password, email, role FROM users WHERE username = :u LIMIT 1";
        $stmt = $this->conn()->prepare($sql);
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Registreer een gebruiker. Retourneert: [] als gelukt, anders fouten. */
    public function registerUser(string $type = 'user'): array
    {
        $this->role = $type ?: 'user';

        // Validatie
        $errors = $this->validateRegister();
        if (!empty($errors)) {
            return $errors;
        }

        // Bestaat username al?
        if ($this->getUser($this->username)) {
            return ['Gebruikersnaam bestaat al.'];
        }

        // Hash wachtwoord en invoegen
        $hash = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, role, created_at)
                VALUES (:u, :p, :e, :r, NOW())";
        $stmt = $this->conn()->prepare($sql);

        try {
            $stmt->execute([
                ':u' => $this->username,
                ':p' => $hash,
                ':e' => $this->email,
                ':r' => $this->role,
            ]);
        } catch (\PDOException $e) {
            // Bij unieke index fouten e.d.
            return ['Kon gebruiker niet registreren: ' . $e->getMessage()];
        }

        return []; // geen fouten => gelukt
    }

    /** Inloggen. Retourneert true/false */
    public function loginUser(): bool
    {
        $errors = $this->validateLogin();
        if (!empty($errors)) {
            return false;
        }

        $row = $this->getUser($this->username);
        if (!$row) {
            return false;
        }

        if (!password_verify($this->password, $row['password'])) {
            return false;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['user_id']  = (int)$row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];

        return true;
    }

    /** Uitloggen en sessie opruimen */
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Leeg sessie-array
        $_SESSION = [];

        // Verwijder sessie-cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Vernietig sessie
        session_destroy();
    }

    /** Is er een gebruiker ingelogd? */
    public function isLoggedIn(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
    }
}
