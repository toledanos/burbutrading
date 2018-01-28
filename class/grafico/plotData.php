<?php 

/*   Clase abstracta para definir la salida a gráfico de los datos
 
 *   Para cada librería o tipología de representación que se pueda usar
 *   se definirá una clase extendida de ésta.
 */

abstract class plotData{
	public $series; // array con las series x-y a representar
	public $filedata; // archivo del que procede el objeto
	public $conjuntos; // array con los conjuntos de datos obtenidos 
				// de los archivos json que ingresan por leeJson()

	abstract function cabeceraPlot();
	abstract function seriePlot();
	abstract function cierraPlot();
	
	/* Lee desde un archivo zip un conjunto de datos
	 * creando el objeto a partir de una serialización 
	 * json.
	 * 
	 */
	public function leeJSON($archivo){
		if( !$this->conjuntos ){ // array conjuntos no inicializado
			$this->conjuntos = array();
		}
		$zip = new ZipArchive;
		if ($zip->open( $archivo ) === TRUE) {
			for ($i = 0; $i < $zip->numFiles; $i++) { // recorremos los diferentes archivos que pueda contener el zip
				$filename = $zip->getNameIndex($i);
				echo "Encontrado: $filename comprimido dentro de $archivo\r\n";
				$this->filedata=$filename;         // cambiamos el nombre del archivo por el que hemos encontrado dentro
				$dentro = json_decode($zip->getFromIndex($i));  // unmarshalling, $dentro es objeto de tipo conjuntoDatos
				array_push($this->conjuntos,$dentro); 
			}
			$zip->close();
		} else {
			echo "failed";
		}	
	}
	

    public function __toString(){
	}

}


?>

