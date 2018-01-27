<?php 



abstract class conjuntoDatos {
    public $mono_segundos; 	// duración de cada tramo temporal, en segundos
    public $archivo;		// ruta del archivo que contiene los datos
    private $mono;		// actual límite de tramo a superar
    public $tms_direccion; 	// sentido ascendente TRUE  descendente FALSE
    private $maximos_datos; // max datos a procesar del archivo ( -1 para todos)
    public $bufferDatos; // almacén de datos
}

?>

