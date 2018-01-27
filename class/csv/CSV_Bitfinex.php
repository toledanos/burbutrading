<?php 

/*   Clase esxtendida de DatoCSV para el 
 *   exchange Bitfinex.
 *   Contiene el método para convertir las líneas de texto 
 *   del archivo CVS de este exchange en clase de tipo DatoCSV
 */ 

include_once("DatoCSV.php");

class CSV_Bitfinex extends DatoCSV{
	
	/*  El metodo constructor inmediatamente convierte la línea 
	 *  de texto del archivo que es suministrada
	 */
	function CSV_Bitfinex($line){
		$this->convierteLinea($line);
	}

	/*  Esta función ha de ser llamada recursivamente dentro de un 
	 *  bucle de lectura línea a línea del archivo del exchange
	 */
    function convierteLinea($line){
		$line=rtrim($line); // borramos el \r\n
		$campos=explode(",",$line);
		$cuenta=0;
		foreach($campos as $campo){
			if($cuenta==0) $this->tms = $campo;
			if($cuenta==1) ; // ese es un código interno del exchange, no nos interesa 
			if($cuenta==2) $this->precio = $campo;
			if($cuenta==3) $this->cantidad  = $campo;
			if($cuenta==4){  
				if($campo === 'buy') $this->es_compra = true;
				if($campo === 'sell') $this->es_compra = false;
			}
			$cuenta++;
		}
    }
}

?>

