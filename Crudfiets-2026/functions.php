<?php
// auteur: Vul hier je naam in
// functie: algemene functies tbv hergebruik

include_once "config.php";

 function connectDb(){
    $servername = SERVERNAME;
    $username = USERNAME;
    $password = PASSWORD;
    $dbname = DATABASE;
   
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //echo "Connected successfully";
        return $conn;
    } 
    catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

 }

 function crudMain(){

    // Menu-item   insert
    $txt = "
    <h1>Crud Fietsen</h1>
    <nav>
		<a href='insert.php'>Toevoegen nieuwe fiets</a>
    </nav><br>";
    echo $txt;

    // Haal alle fietsen record uit de tabel 
    $result = getData(CRUD_TABLE);

    //print table
    printCrudTabel($result);
    
 }

 // selecteer de data uit de opgeven table
 function getData($table): array {
    // Connect database
    $conn = connectDb();

    // Select data uit de opgegeven table methode query
    // query: is een prepare en execute in 1 zonder placeholders
    // $result = $conn->query("SELECT * FROM $table")->fetchAll();

    // Select data uit de opgegeven table methode prepare
    $sql = "SELECT * FROM $table";
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll();

    return $result;
 }

 // selecteer de rij van de opgeven id uit de table fietsen
 function getRecord($id){
    // Connect database
    $conn = connectDb();

    // Select data uit de opgegeven table methode prepare
    $sql = "SELECT * FROM " . CRUD_TABLE . " WHERE id = :id";
    $query = $conn->prepare($sql);
    $query->execute([':id'=>$id]);
    $result = $query->fetch();

    return $result;
 }


// Function 'printCrudTabel' print een HTML-table met data uit $result 
// en een wzg- en -verwijder-knop.
function printCrudTabel($result){
    // Zet de hele table in een variable en print hem 1 keer 
    $table = "<table>";

    // Print header table

    // haal de kolommen uit de eerste rij [0] van het array $result mbv array_keys
    $headers = array_keys($result[0]);
    $table .= "<tr>";
    foreach($headers as $header){
        $table .= "<th>" . $header . "</th>";   
    }
    // Voeg actie kopregel toe
    $table .= "<th colspan=2>Actie</th>";
    $table .= "</th>";

    // print elke rij
    foreach ($result as $row) {
        
        $table .= "<tr>";
        // print elke kolom
        foreach ($row as $cell) {
            $table .= "<td>" . $cell . "</td>";  
        }
        
        // Wijzig knopje
        $table .= "<td>
            <form method='post' action='update.php?id=$row[id]' >       
                <button>Wzg</button>	 
            </form></td>";

        // Delete knopje
        $table .= "<td>
            <form method='post' action='delete.php?id=$row[id]' >       
                <button>Verwijder</button>	 
            </form></td>";

        $table .= "</tr>";
    }
    $table.= "</table>";

    echo $table;
}


function updateRecord(array $row){

    // Maak database connectie
    $conn = connectDb();

    // Maak een query 
    $sql = "UPDATE " . CRUD_TABLE .
    " SET 
        merk = :merk, 
        type = :type, 
        prijs = :prijs
    WHERE id = :id
    ";

    // De waarden die worden doorgegeven aan de query
    $values = [
        ':merk' => $row['merk'],
        ':type' => $row['type'],
        ':prijs' => $row['prijs'],
        ':id'=>$row['id']
    ];

    // Prepare query
    $stmt = $conn->prepare($sql);
    // Uitvoeren
    $stmt->execute($values);

    // test of database actie is gelukt
    $retVal = ($stmt->rowCount() == 1) ? true : false ;
    return $retVal;
}

function insertRecord($post): bool  {
    $retVal = false;

    // Maak database connectie
    $conn = connectDb();

    // Maak een query
    // Merk dat de id niet in de insert query staat, deze wordt automatisch door de database aangemaakt
    $sql = "
        INSERT INTO " . CRUD_TABLE . " (merk, type, prijs)
        VALUES (:merk, :type, :prijs) 
    ";
 
    // De waarden die worden doorgegeven aan de query
    $values = [
        ':merk' => $post['merk'],
        ':type' => $post['type'],
        ':prijs' => $post['prijs']
    ];

    try {
          
        // Prepare query
        $stmt = $conn->prepare($sql);
        // Uitvoeren
        $stmt->execute($values);

        // test of database actie is gelukt
        $retVal = ($stmt->rowCount() == 1) ? true : false ;
    } catch (PDOException $e) {
        sql_error($e, $sql, $values);
        $retVal = false;
    }
    
    return $retVal;  
}

function sql_error(PDOException $e, string $sql, array $values): void {
    // Print de foutmelding naar de browser
    $err = "
    <h2>Foutmelding</h2>
    Fout op bestand: " . $e->getFile() . " op regel " . $e->getLine() . "<br>" .
    "SQL-fout: " . $e->getMessage() . "<br>" .
    "Foute SQL: " . $sql . "<br>" .
    "Opgegeven waarden: " . print_r($values, true) . "<br><br>";
    echo $err;
}

function deleteRecord($id){

    // Connect database
    $conn = connectDb();
    
    // Maak een query 
    $sql = "
    DELETE FROM " . CRUD_TABLE . 
    " WHERE id = :id";

    // Prepare query
    $stmt = $conn->prepare($sql);

    // Uitvoeren
    $stmt->execute([
    ':id'=>$_GET['id']
    ]);

    // test of database actie is gelukt
    $retVal = ($stmt->rowCount() == 1) ? true : false ;
    return $retVal;
}

?>