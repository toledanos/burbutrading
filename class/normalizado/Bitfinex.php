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

		// comprobar si el archivo es un zip
		$file_parts = pathinfo($this->archivo);
		print_r($file_parts);
		if( $file_parts["extension"] === "zip"){
			echo "es zip.... Descomprimimos...\r\n";
			$zip = new ZipArchive;
			if ($zip->open( $this->archivo ) === TRUE) {
				for ($i = 0; $i < $zip->numFiles; $i++) { // recorremos los diferentes archivos que pueda contener el zip
					$filename = $zip->getNameIndex($i);
					echo "Encontrado: $filename\r\n";
					$this->archivo=$filename;         // cambiamos el nombre del archivo por el que hemos encontrado dentro
					$dentro = $zip->getFromIndex($i);
					$this->procesaArchivoBuffered($dentro);
					$this->guardaArchivoBuffered();
				}
				$zip->close();
			} else {
				echo "failed";
			}
			
		}else{
			//$this->procesaArchivo();
			//echo $this;
			//$this->guardaArchivo();
		}
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

