<?php 

/*	Test de las clases datoCSV, conjuntoDatos y DatoSalida
 * 	Se parte de un archivo CSV con datos de tiempo, cantidad, precio y bid/ask
 *  Ese archivo se encuentra en la carpeta "data".
 *  Tras la ejecución, aparece un archivo en la carpeta "out", con más datos
 *  y listo para usarse para obtener un gráfico o analizarse.
 * 
 *  Ejecutar: $ php test_csv.php
 *  
*/
include_once('../class/csv/CSV_Bitfinex.php'); 
include_once('../class/normalizado/Bitfinex.php'); 

$mono_segundos=360; // los datos se agrupan por tiempo fijo, monotónicamente



// listar ficheros del directorio DATA
// Si se pone un archivo raw de Bitfinex, es procesado

$temp_files = glob("../data/*"); 
foreach($temp_files as $file) {
    echo "|||||||||||||||||||||||||  [Procesando:  $file]  ||||||||||||||||||||||\r\n\r\n";
    $bfx = new Bitfinex($mono_segundos,$file);
    $bfx->debug = true; // activamos que nos muestre info detallada
    $bfx->ejecuta(); 
    
    // mostramos los datos de salida, procesados a partir de bid/ask
    $cuenta = 0; // para poder salir del bucle
    $datos = $bfx->datos;  
    foreach($datos as $dato){  // recorremos datos de tipo DatoSalida
		echo $dato;  // ejecutamos función "__toString()" de la clase DatoSalida.php
		$cuenta++;
		if($cuenta>20) break;  // no queremos demasiados datos, salimos
	}
    
}

?>

