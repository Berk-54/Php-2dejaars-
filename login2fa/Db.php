<?php
declare(strict_types=1);

final class Db
{
    private PDO $pdo;

    public function __construct()
    {
        // ✅ PAS DIT AAN NAAR JOUW DB (XAMPP standaard hieronder)
        $host = 'localhost';
        $db   = 'login2fa';
        $user = 'root';
        $pass = '';

        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
