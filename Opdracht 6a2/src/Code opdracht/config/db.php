<?php
// Functie: centrale databaseconnectie
// Auteur: Berke

// Pas deze waarden aan indien nodig
define('DB_HOST', 'localhost');
define('DB_NAME', 'Login');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Haal een gedeelde PDO-verbinding op (singleton).
 */
function getPDO(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    return $pdo;
}
