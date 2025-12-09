<?php

require_once 'vendor/autoload.php';

// IMPORTEREN MET JOUW NIEUWE NAMESPACE
use Abyil\Rekenmachine\classes\Rekenmachine;

$calc = new Rekenmachine();

echo "" . $calc->optellen(5, 3) ."<br>";
echo "" . $calc->optellen(10, 8). "<br>";
