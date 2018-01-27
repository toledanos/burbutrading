<?php 

/*   Esta clase representa el tipo de estructura de datos
 *   que es más frecuente que entregue un exchange.
 * 
 *   Como exchanges hay muchos y puede que el archivo que
 *   entregan es diferente, esta clase ha de ser abstracta y así 
 *   adaptarse a cada exchange con una nueva clase extendida
 */

abstract class DatoCSV{
    public $tms; // es tiempo unix de la operación
    public $precio; // es el precio de tradeo
    public $cantidad; // es la cantidad negociada al precio citado
    public $es_compra; // true para compra, false para venta

    /*  FUNCION  abstracta a definir para cada exchange
	Convierte una línea de texto del archivo CVS en un 
	conjunto de datos estructurados estandarizado
    */
    abstract function convierteLinea($line); // cada exchange tiene archivo CSV diferente
    
    public function __toString(){
		if($this->es_compra)
		return date("Y-m-d H:i:s",$this->tms).", $this->cantidad, $this->precio, compra";
		if(!$this->es_compra)
		return date("Y-m-d H:i:s",$this->tms).", $this->cantidad, $this->precio, venta";
	}
}


?>

