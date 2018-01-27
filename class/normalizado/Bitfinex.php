<?php 

include_once("class/normalizado/DatoSalida.php");

class Bitfinex extends conjuntoDatos{

    public function Bitfinex($mono_segundos, $archivo){
		$this->archivo = $archivo;
		$this->mono_segundos = $mono_segundos;
		$this->tms_direccion = false; // los archivos de Bitfinex son descendentes en el tiempo,
										// primero el trade con tms más reciente
		$this->maximos_datos = -1;  // -1 para procesar todas las líneas
		
		$this->procesaArchivo();
    }
    
    private function procesaArchivo(){
		$this->bufferDatos = array(); // aquí guardamos los datos CSV
	
		$cuenta=0;  // cuenta las líneas
		$tms_inicio=0;
		$handle = fopen($this->archivo, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				if($cuenta>0){ //la primera linea del archivo es cabecera, la saltamos
					$dato =  new CSV_Bitfinex($line);
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

