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
	public $limites;  // alto y ancho del gráfico
	public $margenes; // márgenes
	public $titulo; // título del plot
	public $variable; // nombre de la variable a representar
	public $num_serie=0; // serie a representar (si carga varios json, pueden ser más de una)

	abstract function cabeceraPlot();
	abstract function seriePlot();
	abstract function cierraPlot();
	
	
	/*  Chequeo previo a crear los grafos.
	 */
	public function chequeaConjuntos(){
		// Hay un conjunto de datos?
		if(!isset($this->conjuntos)){
			echo "--- ERROR -- Se ha de cargar un conjunto de datos, por ejemplo con la función leeJSON";
			exit(0);
		}

		 // Se ha establecido variable a representar?
		 if(!isset($this->variable)){
			echo "--- ERROR -- Has de establecer qué variable del conjunto de datos se ha de representar ej.: \$plot->variable=\"p_med\"\r\n";
			exit(0);
		 }
		 
		// Ese array de conjuntos de datos contiene al menos un conjunto y ha sido elegido?
		$cuenta=0;
		$correcto=false; 
		foreach($this->conjuntos as $conjunto){ // iteramos los conjuntos
			// Posee datos este conjunto?
			if( isset($conjunto->datos) ){
				if( count($conjunto->datos) > 0){
					if($cuenta == $this->num_serie){
						echo "Datos[Serie: $this->num_serie]: ".count($conjunto->datos)."\r\n";
						$correcto = true; // hay un conjunto de datos 
					} else {
						echo "--- ADVERTENCIA -- La serie de datos $cuenta no se procesa.\r\n";
					}
				} else{
					echo "--- ADVERTENCIA -- La serie de datos $this->num_serie no contiene dato alguno.\r\n";
				}
			} else {
				echo "--- ERROR -- El conjunto de datos descargado o procesado no posee dato alguno.\r\n";
				exit(0);
			}
			$cuenta++;
		}
		if(!$correcto){
			echo "--- ERROR -- No se ha encontrado la serie $this->num_serie elegida.\r\n";
			exit(0);	
		}
		
		// Esa variable $this->variable existe?
		$existe=false;
		$datos = $this->conjuntos[$this->num_serie]->datos;
		$keys=array_keys(get_object_vars($datos[0]));
		foreach($keys as $nombredato){
			if($nombredato === $this->variable){
				$existe=true;
			}
		}
		if($existe){
			echo "--- OK --- Variable \"$this->variable\" existe en el set de datos.\r\n";
		} else{
			echo "--- ERROR -- No se ha encontrado la variable \"$this->variable\" en la serie elegida.\r\n";
			exit(0);	
		}
		
	}
	
	
	/* Lee desde un archivo zip un conjunto de datos
	 * creando el objeto a partir de una serialización 
	 * json.
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
				echo "Datos: ".count($dentro->datos)." Periodo: ".$dentro->mono_segundos." segundos.\r\n";
				array_push($this->conjuntos,$dentro); 
			}
			$zip->close();
		} else {
			echo "failed";
		}	
	}
	
	public function dameFecha($aVal) {
		//return date('Y-m-d H:i:s',$aVal);
		//return Date('Y-m-d',$aVal);
		return Date('H:i:s',$aVal);
	}
	

    public function __toString(){
	}

}


?>

