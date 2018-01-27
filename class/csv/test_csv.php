<?php 

/* 
 * Prueba de la clase DatoCSV, con la clase CSV_Bitfinex
 * Ejecutar:
 * $ php test_csv.php
 */

include_once('CSV_Bitfinex.php'); 

// archivo descargado de Bitfinex
$archivo = "../../data/2018-01-21-trades-EOSBTC.csv";
$cuenta=0;

// apertura de archivo
$handle = fopen($archivo, "r");
if ($handle) {
	while (($line = fgets($handle)) !== false) {
		if($cuenta>0){ //la primera linea del archivo es cabecera, la saltamos
			echo "Linea: $line";
			$dato =  new CSV_Bitfinex($line);
			echo "\tDatoCSV: $dato\r\n";
		}
		$cuenta++;
	}	
} else{
	echo "Error con $file/r/n";
} 

?>

