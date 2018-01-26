<?php 

abstract class DatoCSV{
    public $tms; // es tiempo unix de la operación
    public $precio; // es el precio de tradeo
    public $cantidad; // es la cantidad negociada al precio citado
    public $es_compra; // true para compra, false para venta

    /*  FUNCION  abstracta
	Convierte una línea de texto del archivo CVS en un 
	conjunto de datos estructurados
    */
    abstract function convierteLinea($line); // cada exchange tiene archivo CSV diferente
}


?>

