<?php
// Functie: basisdatabaseklasse
// Auteur: Studentnaam

// Laad de centrale DB-config en getPDO()
require_once __DIR__ . '/../db.php';

/**
 * Klasse Database: verzorgt de verbinding met de database.
 * Andere klassen kunnen deze klasse uitbreiden om gebruik te maken van dezelfde connectie.
 */
class Database
{
    /** @var PDO|null Gedeelde databaseverbinding */
    protected ?PDO $_conn = null;

    /**
     * Haal een gedeelde PDO-verbinding op. Bij herhaald aanroepen wordt dezelfde connectie gebruikt.
     */
    protected function connectDb(): PDO
    {
        if ($this->_conn instanceof PDO) {
            return $this->_conn;
        }
        // Gebruik centrale functie getPDO() uit db.php
        $this->_conn = getPDO();
        return $this->_conn;
    }
}

?>