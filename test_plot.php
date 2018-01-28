<?php 

/* 
 * Prueba de la clase plotData
 * Ejecutar:
 * $ php test_plot.php
 */

include_once('class/grafico/plotDataJP.php'); 
include_once('class/normalizado/vacioDatos.php'); 


// archivo procesado de Bitfinex que contiene json
// ese json, a su vez, fue obtenido por el proceso de 
// un archivo raw
$archivo = "out/2018-01-21-trades-EOSBTC.csv.zip";

// apertura de archivo
$plotdata = new plotDataJP(); // creación del objeto
echo "Entrando en $archivo en busca de json's con conjunto de datos.\r\n";
$plotdata->leeJSON($archivo); // relleno con datos json (unmarshalling) a partir del archivo

// comprobamos cuantos conjuntos de datos hay en ese archivo y su resumen:
$cuenta=0;
foreach($plotdata->conjuntos as $objeto){ 
	$cuenta++;
	echo "[Encontrado objeto conjuntoDatos n. $cuenta.]\r\n";
	// Ojo, en PHP no se hace cast sobre  objetos. Trataremos el objeto como un 
	// objeto de tipo conjuntoDatos genérico. Veamos si es así:
	echo count($objeto->datos)." bloques DatoSalida.\r\n";
	echo "En intervalos de ". $objeto->mono_segundos. " segundos.\r\n";
}


?>

