<?php 

include_once("class/normalizado/DatoSalida.php");

/* 
 *   Esta clase se extiende de la clase abstracta conjuntoDatos
 */

class Bitfinex extends conjuntoDatos{

    public function Bitfinex($mono_segundos, $archivo){
		$this->archivo = $archivo;
		$this->mono_segundos = $mono_segundos;
		$this->tms_direccion = false; // los archivos de Bitfinex son descendentes en el tiempo,
										// primero viene el trade con tms más reciente
		$this->maximos_datos = -1;  // -1 para procesar todas las líneas del archivo
		
		$this->procesaArchivo();
    }
    /*   
     *   Función requerida por la clase abstracta conjuntoDatos
     * 	 Retorna un objeto de tipo DatoCSV
     */
	public function procesaLinea($linea){
		return new CSV_Bitfinex($linea);
	}
}

?>

