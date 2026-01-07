<?php
// auteur: Vul hier je naam in
// functie: CRUD queries voor fietsen als class (nog niet gekoppeld aan je pagina's)

class FietsRepository
{
    private PDO $conn;
    private string $table;

    public function __construct(PDO $conn, string $table)
    {
        $this->conn  = $conn;
        $this->table = $table;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function insert(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (merk, type, prijs) VALUES (:merk, :type, :prijs)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':merk'  => $data['merk'],
            ':type'  => $data['type'],
            ':prijs' => $data['prijs'],
        ]);
        return $stmt->rowCount() === 1;
    }

    public function update(array $data): bool
    {
        $sql = "UPDATE {$this->table} SET merk = :merk, type = :type, prijs = :prijs WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':merk'  => $data['merk'],
            ':type'  => $data['type'],
            ':prijs' => $data['prijs'],
            ':id'    => $data['id'],
        ]);
        return $stmt->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() === 1;
    }
}
