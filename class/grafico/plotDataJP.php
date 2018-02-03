<?php 

include_once("plotData.php");
require_once ('../../../jpgraph/src/jpgraph.php');
require_once ('../../../jpgraph/src/jpgraph_line.php');

/*  Este es el objeto grafico bajo la librería jpgraph
 * 
 * */

class plotDataJP extends plotData{
	public $graph; // el objeto Graph de la libreria jpgraph 
	public $datax;  // array con los datos del eje X
	public $datay;  // array con los datos del eje Y
	
	public function cabeceraPlot(){
		$this->limites = "1600|1000";
		$this->margenes = "40|40|30|70";
		
		$this->dameGraph();  // crea objeto graph
		
	}
	
	public function seriePlot(){
		$this->chequeaConjuntos();  // Esto ha de estar siempre en esta función
		$this->datax = array(); 
		$this->datay = array(); 
		
		$datos = $this->conjuntos[$this->num_serie]->datos; // cogemos los datos de su array
		foreach($datos as $dato){
			$datarray = (array) $dato; // necesario para referenciar variable por su nombre.
			//echo "tms_final: ".$dato->tms_final."\r\n";
			array_push($this->datax, $dato->tms_final);
			//echo $datarray[$this->variable]."\r\n";
			array_push($this->datay, $datarray[$this->variable]);
		}
		$lineplot=new LinePlot($this->datay);
		$this->graph->Add($lineplot);
		
		$margenes = explode("|",$this->margenes);
		$this->graph->img->SetMargin(intval($margenes[0]),intval($margenes[1]),intval($margenes[2]),intval($margenes[3]));	
		//$this->graph->img->SetMargin(40,20,20,40);
		
		
		$this->graph->title->Set($this->titulo);
		$this->graph->xaxis->title->Set("X-title");
		$this->graph->xaxis->SetPos("min");
		$this->graph->yaxis->title->Set("Y-title");
		
	}
	
	public function cierraPlot(){
		//print_r($this->graph);
		$this->graph->Stroke("../../img/imagen-".time().".png");
	}
	
	
	private function dameGraph(){
		$limites = explode("|",$this->limites);
		
		$this->graph = new Graph( intval($limites[0]), intval($limites[1]) );
		//$this->graph = new Graph(300,200);
		$this->graph->clearTheme(); // establece el tema
		$this->graph->SetScale("textlin");
		
		
	}
	
	
}

?>
	
