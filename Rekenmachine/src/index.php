<?php

// Zonder autoloading: we includen de class handmatig
require_once 'classes/Rekenmachine.php';

$calc = new Rekenmachine();

echo "Uitkomst: " . $calc->optellen(3, 4);

// Debuggen? Dan kun je dit gebruiken:
// var_dump($calc);


//require_once 'classes/Rekenmachine.php';

//$calc = new Rekenmachine();

//echo"Uitkomst:" . $calc->optellen(3,4);

//var_dump($calc);
