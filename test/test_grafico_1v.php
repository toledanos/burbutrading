<?php 

/* 
 * Prueba de la clase plotData, con la clase plotDataJP
 * Este es el test de un gráfico con una variable.
 * 
 * Se toma como partida un archivo json que fue obtenido antes a partir de 
 * un archivo de texto (se les llama "raw") con datos de tiempo, volumen, precio, bid/ask
 * 
 * El archivo gráfico de tipo PNG se guarda en la carpeta "img"
 * 
 * Ejecutar:
 * $ php test_grafico_1v.php
 */

include_once('../class/grafico/plotDataJP.php'); 

// Este archivo fue descargado de Bitfinex y luego convertido a json y enzipado
// (ver test "test_csv.php" para ver este proceso)
$archivo = "../out/2018-01-21-trades-EOSBTC.csv.zip";

$plot = new plotDataJP(); // creamos objeto 
$plot->limites = "1200|1000"; // ancho x alto 
$plot->margenes = "80|30|30|200"; // márgenes 
$plot->titulo = "EOS/ETH";   // título del grafico
$plot->variable = "p_med";  // Esta variable ha de estar definida en la clase DatoSalida
$plot->leeJSON($archivo); // Cargamos datos json desde un archivo, para que $plot->conjunto tenga un array de datos
$plot->cabeceraPlot();  //creamos la cabecera de un gráfico con librería jpgraph
//$plot->num_serie=1; // puede haber varios archivos en el zip, el primero es el 0
$plot->seriePlot();  // crea la línea o barra
$plot->cierraPlot(); // crea el grafico, que se alnmacena en la carpeta "img"

?>

