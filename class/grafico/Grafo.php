<?php 

/*  Un grafo es una línea o una barra o una tarta, etc..
 *  Estará contenido dentro de un "plot" y sus datos son una extracción de 
 *  algún dato contenido en un conjuntoDatos.
 * 
 *  Es una clase abstracta.
*/

abstract class Grafo{
	public $tipo;    // 0 - Puntos, 1 - Línea 
	public $titulo;  // Nombre de este grafo, ej. "Valor $variable para $cambio en $exchange"
	public $cambio;  // El nombre del ticker, ej. "EOS/BTC"
	public $exchange; // Donde se negoció, ej. "Bitfinex"
	public $variable; // Variable a extraer de DatoSalida, ejemplo: "vol_max_compra"
	public $conjunto; // El conjunto de datos de donde extraeremos el grafo

	abstract function extractor(); // función extractora del dato

	public function Grafo($conjunto){
		$this->conjunto = $conjunto;
	}
	public function creaTituloGenerico(){
		$this->titulo = "Valor ".$this->variable." para ".$this->cambio." en ".$this->exchange;
	}

}

?>
