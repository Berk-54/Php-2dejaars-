<?php
// Functie: classdefinitie User 
// Auteur: Berke

require_once __DIR__ . '/../config/db.php';

class User{

    // Eigenschappen 
    public string $username = "";
    public string $email = "";
    public string $role = "user";
    private string $password = "";
    private ?\PDO $_conn = null;

    // Setters/Getters
    public function setPassword(string $password): void {
        $this->password = $password;
    }
    public function getPassword(): string {
        return $this->password;
    }

    // DB Connect: gebruikt de gedeelde getPDO() uit config/db.php
    public function dbConnect(): \PDO {
        if ($this->_conn instanceof \PDO) {
            return $this->_conn;
        }
        $this->_conn = getPDO();
        return $this->_conn;
    }

    // Validatie: username 3..50 tekens
    public function validateLogin(array $data): array {
        $errors = [];
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

    // Inloggen
    public function loginUser(string $username, string $password): bool {
        $pdo = $this->dbConnect();
        $stmt = $pdo->prepare("SELECT username, password, email, role FROM `User` WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($password, $row['password'])) {
            return false;
        }

        $this->username = $row['username'];
        $this->email    = (string)$row['email'];
        $this->role     = (string)$row['role'];

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

    public function isLoggedIn(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    // Registreren m.b.v. objecteigenschappen (sluit aan op je formulieren)
    public function registerUser(): array {
        $data = ['username' => $this->username];
        $errors = $this->validateLogin($data);

        if (empty($this->password) || strlen($this->password) < 6) {
            $errors['password'] = 'Wachtwoord moet minimaal 6 tekens zijn.';
        }

        if ($errors) {
            return $errors;
        }

        $pdo = $this->dbConnect();

        // Bestaat username al?
        $check = $pdo->prepare("SELECT 1 FROM `User` WHERE username = ?");
        $check->execute([$this->username]);
        if ($check->fetch()) {
            $errors['exists'] = 'Gebruikersnaam bestaat al.';
            return $errors;
        }

        $hash = password_hash($this->password, PASSWORD_DEFAULT);
        $email = $this->email !== '' ? $this->email : null;
        $role  = $this->role !== '' ? $this->role : 'user';

        $ins = $pdo->prepare("INSERT INTO `User` (username, password, email, role) VALUES (?, ?, ?, ?)");
        $ins->execute([$this->username, $hash, $email, $role]);

        return [];
    }

    public function getUser(): array {
        if (!$this->isLoggedIn()) {
            return [];
        }
        return $_SESSION['user'];
    }

    public function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
}
?>
