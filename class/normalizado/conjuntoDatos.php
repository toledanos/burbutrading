<?php 

/*  Entrada: archivo de datos CSV de cualquier tipo
 *  Salida:  datos con estructura DatoSalida, más ricos en contenido
 *           estadístico
 * 
 *  Los datos que suministra el exchange han de ser agrupados 
 *  en franjas temporales de igual longitud, es decir, monotonizadas. 
 *  Esto facilita que distintas series sean comparadas entre sí y 
 *  ayuda a disminuir el número de datos sobre el que se actúa.
 *  
 *  $bufferDatos es el almacén temporal, de datos de tipo DatoCSV
 *  $datos es el almacén de datos de salida, de tipo DatoSalida
 * 
 *  Esta clase es abstracta, no se puede instanciar. Se ha de definir 
 *  una nueva clase extendida de ésta para cada exchange, y definir la 
 *  función procesaLinea($linea), específica de esa nueva clase.
 * 
 */


abstract class conjuntoDatos {
    public $mono_segundos; 	// duración de cada tramo temporal, en segundos
    public $archivo;		// ruta del archivo que contiene los datos
    public $mono;		// actual límite de tramo a superar
    public $tms_direccion; 	// sentido ascendente TRUE  descendente FALSE
    public $maximos_datos; // max datos a procesar del archivo ( -1 para todos)
    public $bufferDatos; // array de datos de tipo DatoCSV, temporal
    public $datos;   // array de datos de tipo DatoSalida de este conjunto
    
    abstract function procesaLinea($linea);
    
    /*   En esta función la entrada es un texto con líneas CSV
     *   Ese texto procede de la descompresión de un archivo ZIP
     */
    public function procesaArchivoBuffered($buffercsv){
		$this->bufferDatos = array(); // aquí guardamos los datos CSV
		$this->datos=array(); // inicializamos array de datos
	
		$cuenta=0;  // cuenta las líneas
		$fuera = true; // para salir del bucle
		$tms_inicio=0;
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $buffercsv) as $line){
			if($cuenta>0){ //la primera linea del archivo es cabecera, la saltamos
				$dato =  $this->procesaLinea($line);  // ojo, función abstracta, varía con cada exchange
				// iniciamos el contador de tiempo 
				if($cuenta==1){
					if($this->tms_direccion){ // si sentido del tiempo es ascendente en el archivo
						$this->mono = $dato->tms + $this->mono_segundos;  // establecemos el límite
						$tms_inicio = $this->mono + $this->mono_segundos;
					}else{         // si sentido del tiempo es descendente en el archivo
						$this->mono = $dato->tms - $this->mono_segundos;  // establecemos el límite
						$tms_inicio = $this->mono - $this->mono_segundos;
					}
				}
				// cálculos monotónicos 
				if( $dato->tms <= $this->mono ){  // nos hemos pasado del límite en segundos
					$salida=new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
					array_push($this->datos, $salida);
					$tms_inicio = $this->mono; // guardamos inicio del siguiente tramo
					$this->mono -=  $this->mono_segundos; // vamos al siguiente tramo
					$this->bufferDatos=array(); // reinicio de array
				}
				//mono_procesa_lineas($dato); // proceso normal de línea (dentro del tramo actual)
				array_push($this->bufferDatos,$dato);  // almacena el dato
			}
			$cuenta++;
			if($this->maximos_datos > 0){ // si es -1, no sigue
				if($cuenta > $this->maximos_datos) $fuera=false;
			}		
		}
		//mono_cierra_tramo($dato); // cierre de este tramo final, porque no hay más datos
		$salida=new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
		$this->bufferDatos=array(); // reinicio de array
		array_push($this->datos, $salida);
		echo "$cuenta ok!!  Tramos: ".sizeof($this->bufferDatos)."\r\n";
	}

    /*   En esta función la entrada es el nombre del archivo que contiene los csv 
     */
    public function procesaArchivo(){
		$this->bufferDatos = array(); // aquí guardamos los datos CSV
		$this->datos=array(); // inicializamos array de datos
	
		$cuenta=0;  // cuenta las líneas
		$fuera = true; // para salir del bucle
		$tms_inicio=0;
		$handle = fopen($this->archivo, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false  && $fuera) {
				if($cuenta>0){ //la primera linea del archivo es cabecera, la saltamos
					$dato =  $this->procesaLinea($line);  // ojo, función abstracta, varía con cada exchange
					// iniciamos el contador de tiempo 
					if($cuenta==1){
						if($this->tms_direccion){ // si sentido del tiempo es ascendente en el archivo
							$this->mono = $dato->tms + $this->mono_segundos;  // establecemos el límite
							$tms_inicio = $this->mono + $this->mono_segundos;
						}else{         // si sentido del tiempo es descendente en el archivo
							$this->mono = $dato->tms - $this->mono_segundos;  // establecemos el límite
							$tms_inicio = $this->mono - $this->mono_segundos;
						}
					}
					// cálculos monotónicos 
					if( $dato->tms <= $this->mono ){  // nos hemos pasado del límite en segundos
						$salida=new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
						array_push($this->datos, $salida);
						$tms_inicio = $this->mono; // guardamos inicio del siguiente tramo
						$this->mono -=  $this->mono_segundos; // vamos al siguiente tramo
						$this->bufferDatos=array(); // reinicio de array
					}
					//mono_procesa_lineas($dato); // proceso normal de línea (dentro del tramo actual)
					array_push($this->bufferDatos,$dato);  // almacena el dato
				}
				$cuenta++;
				if($this->maximos_datos > 0){ // si es -1, no sigue
					if($cuenta > $this->maximos_datos) $fuera=false;
				}
			}
			//mono_cierra_tramo($dato); // cierre de este tramo final, porque no hay más datos
			$salida=new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
			$this->bufferDatos=array(); // reinicio de array
			array_push($this->datos, $salida);
			fclose($handle);
			echo "$cuenta ok!!  Tramos: ".sizeof($this->bufferDatos)."\r\n";
		} else {
			echo "Error con $file/r/n";
		} 
	}
    
    public function guardaArchivo() {
		$txt = json_encode($this);  // serializamos este objeto enterito 
		
		$ruta = explode("/",$this->archivo);
		$c = count($ruta)-1;
		$filename =$ruta[$c];
				
		$zip = new ZipArchive();
		
		if ($zip->open($filename.".zip", ZipArchive::CREATE|ZipArchive::OVERWRITE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}else{
			$loop = $zip->numFiles ;
			for ( $i = 0; $i < $loop; $i++ ){
				var_dump($zip->deleteIndex( $i )) ;
				var_dump(deleteName($zip->getNameIndex( $i )). "/") ;
			} 
			$nombre2=$filename.".zip";
			$zip->addFromString($filename.".json", $txt);
			echo $zip->numFiles."--------\r\n";
			$zip->close();
			rename($nombre2, "out/".$nombre2);
		}
	}
	
	public function guardaArchivoBuffered() {
		$txt = json_encode($this); // serializamos este objeto enterito 
				
		$zip = new ZipArchive();
		
		if ($zip->open($this->archivo.".zip", ZipArchive::CREATE|ZipArchive::OVERWRITE)!==TRUE) {
			exit("cannot open ".$this->archivo.".zip\r\n");
		}else{
			$loop = $zip->numFiles ;
			for ( $i = 0; $i < $loop; $i++ ){
				var_dump($zip->deleteIndex( $i )) ;
				var_dump(deleteName($zip->getNameIndex( $i )). "/") ;
			} 
			$zip->addFromString($this->archivo.".json", $txt);
			echo $zip->numFiles."--------\r\n";
			$zip->close();
			rename($this->archivo.".zip", "out/".$this->archivo.".zip");
		}
	}
    
    public function __toString(){
		return "mono_segundos: $this->mono_segundos tms_direccion: $this->tms_direccion maximos_datos: $this->maximos_datos\r\n";
	}
    
}

?>

