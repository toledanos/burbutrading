<?php 
$mono_segundos=3600; // los datos se agrupan por tiempo fijo, monotónicamente

include('mono.php'); // funciones monotónicas

// listar ficheros del directorio DATA
$temp_files = glob(__dir__.'/data/*'); 
foreach($temp_files as $file) {
    echo "|||||||||||||||||||||||||  [Procesando:  $file]  ||||||||||||||||||||||\r\n\r\n";
    new Bitfinex($mono_segundos,$file);
}

?>

