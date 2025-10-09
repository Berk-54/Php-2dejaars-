<?php
// Functie: classdefinitie User
// Auteur: Berke

class User
{
    // Eigenschappen (conform ERD)
    public string $username = "";
    public string $email    = "";
    public string $role     = "user";
    private string $password = "";
    private ?\PDO $_conn     = null; // _conn: PDO

    // -------------------------------
    // Setters / Getters
    // -------------------------------
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    // -------------------------------
    // ConnectDb(): PDO
    // -------------------------------
    // Periode 3/4-stijl: eigen connectie binnen de class
    public function dbConnect(): \PDO
    {
        if ($this->_conn instanceof \PDO) {
            return $this->_conn;
        }

        $host = 'localhost';
        $dbname = 'Login';
        $user = 'root';
        $pass = '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->_conn = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // In schoolcontext vaak voldoende om te stoppen met een melding:
            die('Database connectie mislukt: ' . $e->getMessage());
        }

        return $this->_conn;
    }

    // -------------------------------
    // ValidateLogin(): errors: array
    // -------------------------------
    public function validateLogin(array $data): array
    {
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

    // -------------------------------
    // LoginUser(): boolean
    // -------------------------------
    public function loginUser(string $username, string $password): bool
    {
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

        // Vul objecteigenschappen
        $this->username = $row['username'];
        $this->email    = (string)$row['email'];
        $this->role     = (string)$row['role'];

        // Sessie zetten
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

    // -------------------------------
    // IsLoggedIn(): boolean
    // -------------------------------
    public function isLoggedIn(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    // -------------------------------
    // RegisterUser(): errors: array
    // -------------------------------
    // Sluit aan op jouw formulieren: gebruikt objecteigenschappen
    public function registerUser(): array
    {
        $errors = $this->validateLogin(['username' => $this->username]);

        if ($this->password === '' || strlen($this->password) < 6) {
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

        $hash  = password_hash($this->password, PASSWORD_DEFAULT);
        $email = $this->email !== '' ? $this->email : null;
        $role  = $this->role !== '' ? $this->role : 'user';

        $ins = $pdo->prepare("INSERT INTO `User` (username, password, email, role) VALUES (?, ?, ?, ?)");
        $ins->execute([$this->username, $hash, $email, $role]);

        return []; // geen fouten
    }

    // Optioneel handig
    public function getUser(): array
    {
        if (!$this->isLoggedIn()) {
            return [];
        }
        return $_SESSION['user'];
    }

    public function logout(): void
    {
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
