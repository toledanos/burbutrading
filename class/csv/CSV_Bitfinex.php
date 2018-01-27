<?php 

include_once("class/csv/DatoCSV.php");

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


?>

