<?php
// Functie: classdefinitie User 
// Auteur: Berke

class User {

    // Eigenschappen 
    public string $username = "";
    public string $email = "";
    public string $role = "user";
    private string $password = "";
    private ?\PDO $_conn = null;

    // --- Getters/Setters ---
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getPassword(): string {
        return $this->password;
    }

    // --- DB CONNECTIE ---
    // Maakt verbinding met de database 'Login'
    public function dbConnect(): \PDO {
        if ($this->_conn instanceof \PDO) {
            return $this->_conn;
        }

        $host = 'localhost';
        $dbname = 'Login';     // <--- Zorg dat je database zo heet
        $user = 'root';        // <--- Pas aan als nodig
        $pass = '';            // <--- Pas aan als nodig

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->_conn = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            die('Database connectie mislukt: ' . $e->getMessage());
        }

        return $this->_conn;
    }

    // --- VALIDATIE ---
    public function validateLogin(array $data): array {
        $errors = [];

        // Username aanwezig en lengte 3..50
        $username = isset($data['username']) ? trim($data['username']) : '';
        if ($username === '') {
            $errors['username'] = 'Gebruikersnaam is verplicht.';
        } else {
            $len = function_exists('mb_strlen') ? mb_strlen($username) : strlen($username);
            if ($len < 3 || $len > 50) {
                $errors['username'] = 'Gebruikersnaam moet tussen 3 en 50 tekens zijn.';
            }
        }

        return $errors;
    }

    // --- LOGIN ---
    public function loginUser(string $username, string $password): bool {
        $pdo = $this->dbConnect();

        $stmt = $pdo->prepare("SELECT username, password, email, role FROM `User` WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        if (!password_verify($password, $row['password'])) {
            return false;
        }

        // Sla gegevens op in het object
        $this->username = $row['username'];
        $this->email    = (string)$row['email'];
        $this->role     = (string)$row['role'];

        // Sessie starten
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user'] = [
            'username' => $this->username,
            'email'    => $this->email,
            'role'     => $this->role,
        ];

        return true;
    }

    // --- CONTROLEREN OF IEMAND INGELOGD IS ---
    public function isLoggedIn(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    // --- REGISTREREN ---
    public function registerUser(array $data): array {
        $errors = [];

        // Validatie
        $errors = array_merge($errors, $this->validateLogin($data));

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Wachtwoord moet minimaal 6 tekens zijn.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geen geldig e-mailadres.';
        }

        if ($errors) {
            return $errors;
        }

        $pdo = $this->dbConnect();

        // Check of username of email al bestaat
        $check = $pdo->prepare("SELECT 1 FROM `User` WHERE username = ? OR email = ?");
        $check->execute([$data['username'], $data['email']]);
        if ($check->fetch()) {
            $errors['exists'] = 'Gebruikersnaam of e-mail bestaat al.';
            return $errors;
        }

        // Hash wachtwoord en sla op
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = isset($data['role']) && $data['role'] !== '' ? $data['role'] : 'user';

        $ins = $pdo->prepare("INSERT INTO `User` (username, password, email, role) VALUES (?, ?, ?, ?)");
        $ins->execute([$data['username'], $hash, $data['email'], $role]);

        return []; // geen fouten
    }

    // --- GEBRUIKER OPHALEN ---
    public function getUser(): array {
        if (!$this->isLoggedIn()) {
            return [];
        }
        return $_SESSION['user'];
    }

    // --- UITLOGGEN ---
    public function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}

?>
