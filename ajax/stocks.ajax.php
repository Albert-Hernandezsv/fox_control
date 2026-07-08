<?php

require_once "../controladores/stocks.controlador.php";
require_once "../modelos/stocks.modelo.php";

class AjaxStocks{

    /*=============================================
    MOSTRAR STOCKS
    =============================================*/    

    public $idProducto;

    public function ajaxMostrarStocks(){

        $item = null;
        $orden = "id";
        $valor = null;

        $respuesta = ControladorStocks::ctrMostrarStocks($item, $valor, $orden);

        echo json_encode($respuesta);

    }

}

/*=============================================
MOSTRAR STOCKS
=============================================*/

if(isset($_POST["idProducto"])){

    $mostrar = new AjaxStocks();
    $mostrar -> idProducto = $_POST["idProducto"];
    $mostrar -> ajaxMostrarStocks();

}