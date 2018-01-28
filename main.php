<?php 

$mono_segundos=360; // los datos se agrupan por tiempo fijo, monotónicamente

include_once('class/csv/CSV_Bitfinex.php'); 
include_once('class/normalizado/Bitfinex.php'); 

//include("mono.php");

// listar ficheros del directorio DATA
$temp_files = glob(__dir__.'/data/*'); 
foreach($temp_files as $file) {
    echo "|||||||||||||||||||||||||  [Procesando:  $file]  ||||||||||||||||||||||\r\n\r\n";
    new Bitfinex($mono_segundos,$file);
}

?>

