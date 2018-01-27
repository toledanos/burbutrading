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
    private $mono;		// actual límite de tramo a superar
    public $tms_direccion; 	// sentido ascendente TRUE  descendente FALSE
    private $maximos_datos; // max datos a procesar del archivo ( -1 para todos)
    public $bufferDatos; // array de datos de tipo DatoCSV, temporal
    public $datos;   // array de datos de tipo DatoSalida de este conjunto
    
    abstract function procesaLinea($linea);
    
    public function procesaArchivo(){
		$this->bufferDatos = array(); // aquí guardamos los datos CSV
	
		$cuenta=0;  // cuenta las líneas
		$tms_inicio=0;
		$handle = fopen($this->archivo, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				if($cuenta>0){ //la primera linea del archivo es cabecera, la saltamos
					$dato =  $this->procesaLinea($line);  // ojo, función abstracta, varía con cada exchange
					// iniciamos el contador de tiempo 
					if($cuenta==1){
						$this->mono = $dato->tms - $this->mono_segundos;  // establecemos el límite
						$tms_inicio = $this->mono + $this->mono_segundos;
					}
					// cálculos monotónicos 
					if( $dato->tms <= $this->mono ){  // nos hemos pasado del límite en segundos
						new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
						$tms_inicio = $this->mono; // guardamos inicio del siguiente tramo
						$this->mono -=  $this->mono_segundos; // vamos al siguiente tramo
						$this->bufferDatos=array(); // reinicio de array
					}
					//mono_procesa_lineas($dato); // proceso normal de línea (dentro del tramo actual)
					array_push($this->bufferDatos,$dato);  // almacena el dato
				}
				$cuenta++;
				if($this->maximos_datos > 0){ // si es -1, no sigue
					if($cuenta > $this->maximos_datos) break;
				}
			}
			//mono_cierra_tramo($dato); // cierre de este tramo final, porque no hay más datos
			new DatoSalida($this->bufferDatos, $tms_inicio, $this->mono, true);
			fclose($handle);
			echo "$cuenta ok!!  Tramos: ".sizeof($this->bufferDatos)."\r\n";
		} else {
			echo "Error con $file/r/n";
		} 
	}
    
}

?>

