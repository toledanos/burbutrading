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

class CSV_Bitfinex extends DatoCSV{
	
	function CSV_Bitfinex($line){
		$this->convierteLinea($line);
	}

    function convierteLinea($line){
		$line=rtrim($line); // borramos el \r\n
		$campos=explode(",",$line);
		$cuenta=0;
		foreach($campos as $campo){
			if($cuenta==0) $this->tms = $campo;
			if($cuenta==2) $this->precio = $campo;
			if($cuenta==3) $this->cantidad  = $campo;
			if($cuenta==4){  
				if($campo === 'buy') $this->es_compra = true;
				if($campo === 'sell') $this->es_compra = false;
			}
			$cuenta++;
		}
		//$this->imprime($line);
    }
    
    function imprime($line){
		echo $line."\r\n";
		echo "\t $this->tms $this->precio $this->cantidad $this->es_compra \r\n";
	}
}

abstract class conjuntoDatos {
    public $mono_segundos; 	// duración de cada tramo temporal, en segundos
    public $archivo;		// ruta del archivo que contiene los datos
    private $mono;		// actual límite de tramo a superar
    public $tms_direccion; 	// sentido ascendente TRUE  descendente FALSE
    private $maximos_datos; // max datos a procesar del archivo ( -1 para todos)
    public $bufferDatos; // almacén de datos
        
}

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

class DatoSalida {
	public $tms_inicio;   	// timestamp unix de inicio de bloque temporal
    public $tms_final;   	// timestamp unix de final de bloque temporal
    public $cnt_compras=0;    // número de compras
    public $cnt_ventas=0;	    // número de ventas
    public $vol_max_compra=0; // volumen máximo comprado
    public $vol_min_compra=0; // volumen mínimo comprado
    public $vol_comprado=0; // volumen comprado en tramo
    public $vol_max_venta=0; // volumen máximo vendido
    public $vol_min_venta=0; // volumen mínimo vendido
    public $vol_vendido=0; // volumen vendido en tramo
    public $p_inicio=0;      // precio inicio
    public $p_med=0;         // precio medio
    public $p_med_prom=0;    // precio medio promediado en volumen
    public $p_med_signo=0;    // precio medio promediado en volumen y signo
    public $p_final=0;       // precio cierre

	public function DatoSalida($bloquedatos, $inicio, $fin, $imprime){  // bloquedatos es array de DatoCSV 
		$cuenta=sizeof($bloquedatos);
		$this->tms_inicio = $inicio;
		$this->tms_final = $fin;
			
		$ult_precio=0;	
		$adicion=0;   	// aquí sumamos para precio medio
		$adicion_prom=0; // aquí sumamos para precio medio promediado con volumen
		$adicion_signo=0; // aquí sumamos para precio medio promediado con volumen y con signo
		foreach($bloquedatos as $datocsv){
			$adicion += $datocsv->precio;
			$adicion_prom += $datocsv->precio * $datocsv->cantidad;
			if( $this->cnt_compras==0 && $this->cnt_ventas==0 ) $this->p_inicio=$datocsv->precio;
			if($datocsv->es_compra){
				$adicion_signo += $datocsv->precio * $datocsv->cantidad;
				$this->cnt_compras++; // incrementamos contador de compras
				if( $this->cnt_compras == 1 ){
					$this->vol_max_compra=$datocsv->cantidad;
					$this->vol_min_compra=$datocsv->cantidad;
				}else{
					if( $datocsv->cantidad > $this->vol_max_compra ) $this->vol_max_compra=$datocsv->cantidad;
					if( $datocsv->cantidad < $this->vol_min_compra ) $this->vol_min_compra=$datocsv->cantidad;
				}
				$this->vol_comprado += $datocsv->cantidad;
			} else{    // el dato es de venta
				$adicion_signo -= $datocsv->precio * $datocsv->cantidad;
				$this->cnt_ventas++;  // incrementamos contador de ventas
				if( $this->cnt_ventas == 1 ){
					$this->vol_max_venta=$datocsv->cantidad;
					$this->vol_min_venta=$datocsv->cantidad;
				}else{
					if( $datocsv->cantidad > $this->vol_max_venta ) $this->vol_max_venta=$datocsv->cantidad;
					if( $datocsv->cantidad < $this->vol_min_venta ) $this->vol_min_venta=$datocsv->cantidad;
				}
				$this->vol_vendido += $datocsv->cantidad;
			}
			$ult_precio = $datocsv->precio;
			//echo "$dato->tms \r\n";
		}
		$this->p_final = $ult_precio;
		
		if( $cuenta>0 ){
			$this->p_med = $adicion / $cuenta;
			$this->p_med_prom = $adicion_prom / $cuenta; // / $this->p_med;
			$this->p_med_signo = $adicion_signo / $cuenta; // / $this->p_med;; 
		}
				
		echo "----- $inicio ----- $cuenta -($this->cnt_compras,$this->cnt_ventas)-------- $fin --------\r\n";
		echo "\t\t----- VOLC: $this->vol_max_compra  , $this->vol_min_compra --------------------\r\n";
		echo "\t\t----- VOLV: $this->vol_max_venta  , $this->vol_min_venta --------------------\r\n";
		echo "\t\t----- VOLT C/V  $this->vol_comprado / $this->vol_vendido\r\n";
		echo "\t\t-----[P ini|med|prom|signo|fin]  $this->p_inicio | $this->p_med | $this->p_med_prom | $this->p_med_signo | $this->p_final \r\n";
	}
}

/*
    Función que inicia el array global "actuales" 
    La "monotonización" consiste en agrupar los datos con tiempo unix aleatorio
    en tramos de tiempo de longitud constante, reflejada en la variable "mono_segundos"
    Llamada por: analiza_archivo()
    Devuelve:    nada, pero modifica el array "actuales"
*/
function mono_inicia($dato,$mono_segundos){
    global $actuales;

    $actuales=array(
	"mono_segundos"=>$mono_segundos,
	"mono"=>0,
	"vol_acum"=>0,
	"vol_max_venta"=>0,
	"vol_min_venta"=>0,
	"vol_max_compra"=>0,
	"vol_min_compra"=>0,
	"opers"=>0,
	"precio_max"=>$dato[1],
	"precio_min"=>$dato[1],
	"precioxvol_max"=>0,
	"precioxvol_min"=>0,
	"pvs"=>array(),
	"vcompras"=>array(),
	"vventas"=>array()
    );
    $actuales["mono"] = $dato[0] - $actuales["mono_segundos"];
    //echo "INICIO ############################################".$actuales["mono"]."####################################\r\n";
}

/*
    Función que procesa la monotonización 
    La "monotonización" consiste en agrupar los datos temporales.
    Llamada por: analiza_archivo()
    Devuelve:    nada, pero modifica el array "actuales"
*/
function mono_procesa_lineas($dato){
    global $actuales;

    if( $dato[1]>$actuales["precio_max"] ) $actuales["precio_max"]=$dato[1];
    if( $dato[1]<$actuales["precio_min"] ) $actuales["precio_min"]=$dato[1];

    $pv=$dato[1]*$dato[2]; 
    array_push($actuales["pvs"],$pv);
    if( $dato[3]===-1 ){ //si es venta
	if( $pv>$actuales["precioxvol_min"] ) $actuales["precioxvol_min"]=$pv;
    }
    if( $dato[3]===1 ){ // si es compra
	if( $pv>$actuales["precioxvol_max"] ) $actuales["precioxvol_max"]=$pv;
    }

    // volumenes ////////
    if($dato[3]>0){ // compra
	array_push($actuales["vcompras"],$dato[2]);
	if( $dato[2]>$actuales["vol_max_compra"] ) $actuales["vol_max_compra"] = $dato[2];
	if( $actuales["vol_min_compra"] == 0 ){ // al menos necesitamos el primero
	    $actuales["vol_min_compra"] = $dato[2];
	} else{
	    if( $dato[2]<$actuales["vol_min_compra"] ) $actuales["vol_min_compra"] = $dato[2];
	}
    }
    if($dato[3]<0){ // venta
	array_push($actuales["vventas"],$dato[2]);
	if( $dato[2]>$actuales["vol_max_venta"] ) $actuales["vol_max_venta"] = $dato[2];
	if($actuales["vol_min_venta"] == 0){ // al menos necesitamos el primero
	    $actuales["vol_min_venta"] = $dato[2];
	} else{
	    if( $dato[2]<$actuales["vol_min_venta"] ) $actuales["vol_min_venta"] = $dato[2];
	}
    }

    $actuales["opers"]++; // incrementa el contador deoperaciones en este tramo
}

/*
    Función que procesa la monotonización cuando se puede cerrar el tramo temporal 
    La "monotonización" consiste en agrupar los datos temporales.
    Llamada por: analiza_archivo()
    Devuelve:    nada, pero modifica el array "actuales"
*/
function mono_cierra_tramo($dato){
    global $actuales;
    global $ntramos;

    $actuales["mono"] -=  $actuales["mono_segundos"]; // vamos al siguiente tramo

    // pagos en la moneda origen 
    $suma_pvs=0;
    foreach($actuales["pvs"] as $pv){
	$suma_pvs+=$pv;
    }
    $media_pvs=$suma_pvs/$actuales['opers'];

    // volumenes medios
    $sumcompra=0;
    $sumventa=0;
    $media_venta=0;
    $media_compra=0;
    $cnt_compras = count($actuales["vcompras"]);
    $cnt_ventas = count($actuales["vventas"]);

    foreach($actuales["vventas"] as $vendo){
	$sumventa+=$vendo;
    }
    if($cnt_ventas>0){
	$media_venta = $sumventa/$cnt_ventas;
    }

    foreach($actuales["vcompras"] as $compro){
	$sumcompra+=$compro;
    }
    if($cnt_compras>0){
	$media_compra = $sumcompra/$cnt_compras;
    }
    echo "[cnt_compras|cnt_ventas|media_compra|media_venta] $cnt_compras | $cnt_ventas | $media_compra | $media_venta\r\n";
    


    echo "[operaciones] ".$actuales["opers"]." [precio maximo] ". $actuales["precio_max"]." [precio mínimo] ". $actuales["precio_min"]."\r\n";
    echo "\t\t[volumen compra max/min - media] ". $actuales["vol_max_compra"]." / ".$actuales["vol_min_compra"]." - $media_compra\r\n";
    echo "\t\t[volumen venta max/min - media] ". $actuales["vol_max_venta"]." / ".$actuales["vol_min_venta"]." - $media_venta\r\n";
    echo "\t\t[pv max/min - media] ". $actuales["precioxvol_max"]." / ".$actuales["precioxvol_min"]." - $media_pvs\r\n";


    // reinicio
    $actuales["opers"]=0;
    $actuales["precioxvol_max"]=$dato[1]*$dato[2];
    $actuales["precioxvol_min"]=$dato[1]*$dato[2];
    $actuales["precio_max"]=$dato[1]; 
    $actuales["precio_min"]=$dato[1];
    $actuales["vol_max_compra"]=0; 
    $actuales["vol_min_compra"]=0;
    $actuales["vol_max_venta"]=0; 
    $actuales["vol_min_venta"]=0;
    $actuales["pvs"]=array(); // reinicio array
    $actuales["vcompras"]=array(); // reinicio array
    $actuales["vventas"]=array(); // reinicio array

    $ntramos++; // incrementamos el contador de tramos
    //echo "############################################".$actuales["mono"]."####################################\r\n";
}

?>

