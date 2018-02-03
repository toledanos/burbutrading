<?php 

/* 
 * Prueba de la clase plotData, con la clase plotDataJP
 * Ejecutar:
 * $ php test_plot.php
 */

include_once('plotDataJP.php'); 

// archivo descargado de Bitfinex y luego convertido a json y enzipado
$archivo = "../../out/2018-01-21-trades-EOSBTC.csv.zip";

// creamos objeto 
$plot = new plotDataJP();
$plot->titulo = "Gráfico de ejemplo.";
$plot->variable = "p_med";
// cargamos datos json, para que $plot->conjunto tenga un array de datos
$plot->leeJSON($archivo);
//creamos la cabecera de un gráfico con librería jpgraph
$plot->cabeceraPlot();  
//$plot->num_serie=1;
$plot->seriePlot();
$plot->cierraPlot();




?>

