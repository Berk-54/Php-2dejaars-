<?php
// auteur: Vul hier je naam in
// functie: PDO database connectie als class (minimaal, zodat je CRUD niet breekt)

class Database
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;

    public function __construct(string $servername, string $username, string $password, string $dbname)
    {
        $this->servername = $servername;
        $this->username   = $username;
        $this->password   = $password;
        $this->dbname     = $dbname;
    }

    public function connect(): PDO
    {
        $conn = new PDO(
            "mysql:host={$this->servername};dbname={$this->dbname}",
            $this->username,
            $this->password
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    }
}
