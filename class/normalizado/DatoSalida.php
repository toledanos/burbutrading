<?php 

include_once("conjuntoDatos.php");

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

?>
