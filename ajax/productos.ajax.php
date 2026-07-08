<?php

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

class AjaxProductos{

    /*=============================================
    EDITAR PRODUCTO
    =============================================*/    

    public $idProducto;

    public function ajaxEditarProducto(){

        $item = "id";
        $orden = "id";
        $valor = $this->idProducto;

        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

        echo json_encode($respuesta);

    }

    /*=============================================
    VALIDAR NO ELIMINAR CATEGORIA QUE TENGA PRODUCTO
    =============================================*/    

    public $validarCategoria;

    public function ajaxValidarCategoria(){

        $item = "categoria_id";
        $valor = $this->validarCategoria;

        // Obtener los productos relacionados con esta categoría
        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor);

        // Devolver la respuesta como JSON
        echo json_encode($respuesta);

    }

	/*=============================================
	VALIDAR NO REPETIR CODIGO
	=============================================*/	

	public $validarProducto;

	public function ajaxValidarProducto(){

		$item = "codigo";
		$valor = $this->validarProducto;

		$respuesta = ControladorProductos::ctrMostrarProductos($item, $valor);

		echo json_encode($respuesta);

	}

    /*=============================================
    VALIDAR NO ELIMINAR CATEGORIA QUE TENGA PRODUCTO
    =============================================*/    

    public $validarProductoStock;

    public function ajaxValidarProductoStock(){

        $item = "id";
        $valor = $this->validarProductoStock;

        // Obtener los productos relacionados con esta categoría
        $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor);

        // Devolver la respuesta como JSON
        echo json_encode($respuesta);

    }
}

/*=============================================
EDITAR PRODUCTO
=============================================*/
if(isset($_POST["idProducto"])){

    $editar = new AjaxProductos();
    $editar -> idProducto = $_POST["idProducto"];
    $editar -> ajaxEditarProducto();

}

/*=============================================
VALIDAR NO ELIMINAR CATEGORIA QUE TENGA PRODUCTO
=============================================*/

if(isset($_POST["idCategoriaValidar"])){

    $valCategoria = new AjaxProductos();
    $valCategoria -> validarCategoria = $_POST["idCategoriaValidar"];
    $valCategoria -> ajaxValidarCategoria();

}

/*=============================================
VALIDAR NO REPETIR EL CODIGO
=============================================*/

if(isset( $_POST["validarCodigo"])){

	$valProducto = new AjaxProductos();
	$valProducto -> validarProducto = $_POST["validarCodigo"];
	$valProducto -> ajaxValidarProducto();

}

/*=============================================
VALIDAR NO ELIMINAR PRODUCTO QUE TENGA STOCK
=============================================*/

if(isset($_POST["idProductoValidar"])){

    $valProductoStock = new AjaxProductos();
    $valProductoStock -> validarProductoStock = $_POST["idProductoValidar"];
    $valProductoStock -> ajaxValidarProductoStock();

}