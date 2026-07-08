<?php

class ControladorStocks{

	/*=============================================
	REGISTRO DE STOCK
	=============================================*/

	static public function ctrCrearStock(){

		if(isset($_POST["nuevaCantidadStock"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaCantidadStock"])){

				$stockActual = $_POST["cantidadStockActual"];
				$stockAgregado = $_POST["nuevaCantidadStock"];
				$stockNuevo = $stockActual + $stockAgregado;

				$tabla = "inventario";
				$item1 = "stock";
				$valor1 = $stockNuevo;
				$item2 = "id";
				$valor2 = $_POST["idProductoCantidadStock"];

				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				$tabla = "inventario";
				$item1 = "precio_compra";
				$valor1 = $_POST["nuevoPrecioCompraProducto1"];
				$item2 = "id";
				$valor2 = $_POST["idProductoCantidadStock"];

				$respuesta1 = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				$tabla = "inventario";
				$item1 = "precio_venta";
				$valor1 = $_POST["nuevoPrecioVentaProducto1"];
				$item2 = "id";
				$valor2 = $_POST["idProductoCantidadStock"];

				$respuesta2 = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				if($respuesta == "ok"){

					$tabla1 = "ingreso_stock";

					$datos1 = array("cantidad" => $_POST["nuevaCantidadStock"],
									"proveedor" => $_POST["nuevoProveedorStock"],
									"fecha" => $_POST["nuevaFechaStock"],
									"comentarios" => $_POST["nuevoComentarioStock"],
									"id_producto" => $_POST["idProductoCantidadStock"],
									"precio_compra" => $_POST["nuevoPrecioCompraProducto1"],
							   		"precio_venta" => $_POST["nuevoPrecioVentaProducto1"],);

					$respuesta3 = ModeloStocks::mdlIngresarStock($tabla1, $datos1);



					echo '<script>

					swal({

						type: "success",
						title: "¡El stock ha sido agregado al producto correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";

						}

					});
				

					</script>';


				}	


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El producto no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "inventario";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	MOSTRAR STOCKS
	=============================================*/

	static public function ctrMostrarStocks($item, $valor, $orden){

		$tabla = "ingreso_stock";

		$respuesta = ModeloStocks::MdlMostrarStocks($tabla, $item, $valor, $orden);

		return $respuesta;
	}

}
	


