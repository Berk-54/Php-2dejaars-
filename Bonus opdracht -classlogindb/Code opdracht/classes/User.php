<?php
    // Functie: classdefinitie User 
    // Auteur: Studentnaam

    require_once __DIR__ . '/Database.php';

    class User extends Database {

        // Eigenschappen
        public string $username = '';
        public string $email    = '';
        public string $role     = 'user';
        private string $password = '';

        /**
         * Stel het wachtwoord in (zonder hashing).
         */
        public function setPassword(string $password): void
        {
            $this->password = trim($password);
        }

        /**
         * Toon de huidige gebruiker (alleen username en rol) ter debug.
         */
        public function showUser(): void
        {
            echo htmlspecialchars($this->username) . ' (' . htmlspecialchars($this->role) . ')';
        }


        /**
         * Valideer de login-velden. Geeft een array met foutmeldingen terug.
         */
        public function validateLogin(): array
        {
            $errors = [];
            if (trim($this->username) === '') {
                $errors[] = 'Gebruikersnaam is verplicht.';
            }
            if (trim($this->password) === '') {
                $errors[] = 'Wachtwoord is verplicht.';
            }
            return $errors;
        }

        /**
         * Valideer de registratie-velden. Geeft een array met foutmeldingen terug.
         */
        public function validateRegister(): array
        {
            $errors = [];
            if ($this->username === '' || strlen($this->username) < 3) {
                $errors[] = 'Gebruikersnaam moet minimaal 3 tekens zijn.';
            }
            if ($this->password === '' || strlen($this->password) < 6) {
                $errors[] = 'Wachtwoord moet minimaal 6 tekens zijn.';
            }
            // E-mailadres is optioneel; enkel controleren indien ingevuld
            if ($this->email !== '' && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'E-mailadres is ongeldig.';
            }
            return $errors;
        }

        /**
         * Haal een gebruiker op uit de database op basis van de gebruikersnaam.
         * Retourneert een array met waarden of null indien niet gevonden.
         */
        public function getUser(string $username): ?array
        {
            $sql = 'SELECT id, username, password, email, role FROM users WHERE username = :u LIMIT 1';
            $stmt = $this->connectDb()->prepare($sql);
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();
            if ($row) {
                // Vul object-eigenschappen met gegevens uit de database
                $this->username = (string)$row['username'];
                $this->email    = (string)($row['email'] ?? '');
                $this->role     = (string)($row['role'] ?? 'user');
                return $row;
            }
            return null;
        }

        /**
         * Registreer een nieuwe gebruiker.
         * Geeft een lege array terug bij succes of een array met foutmeldingen.
         */
        public function registerUser(string $type = 'user'): array
        {
            // Standaard rol instellen
            $this->role = $type ?: 'user';

            // Valideer velden
            $errors = $this->validateRegister();
            if (!empty($errors)) {
                return $errors;
            }

            // Bestaat de gebruikersnaam al?
            if ($this->getUser($this->username)) {
                return ['Gebruikersnaam bestaat al.'];
            }

            // Sla gebruiker op met een gehasht wachtwoord
            $hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql  = 'INSERT INTO users (username, password, email, role, created_at)
                     VALUES (:u, :p, :e, :r, NOW())';
            $stmt = $this->connectDb()->prepare($sql);
            try {
                $stmt->execute([
                    ':u' => $this->username,
                    ':p' => $hash,
                    ':e' => $this->email,
                    ':r' => $this->role,
                ]);
            } catch (PDOException $e) {
                // Fout bij invoegen (bijvoorbeeld dubbele key)
                return ['Kon gebruiker niet registreren: ' . $e->getMessage()];
            }
            return [];
        }

        /**
         * Probeer gebruiker in te loggen. Retourneert true bij succes, anders false.
         */
        public function loginUser(): bool
        {
            // Controleer of velden aanwezig zijn
            $errors = $this->validateLogin();
            if (!empty($errors)) {
                return false;
            }

            // Zoek gebruiker op
            $row = $this->getUser($this->username);
            if (!$row) {
                return false;
            }

            // Controleer wachtwoord
            if (!password_verify($this->password, $row['password'])) {
                return false;
            }

            // Start een sessie indien nog niet actief
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['user_id']  = (int)$row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            return true;
        }

        /**
         * Uitloggen: verwijder sessie en cookie.
         */
        public function logout(): void
        {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            // Leeg alle sessievariabelen
            $_SESSION = [];

            // Verwijder sessiecookie
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
            // Vernietig de sessie
            session_destroy();
        }

        /**
         * Controleer of een gebruiker is ingelogd.
         */
        public function isLoggedIn(): bool
        {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
        }
    }

?>