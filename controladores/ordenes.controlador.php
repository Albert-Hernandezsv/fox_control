<?php

class ControladorOrdenes{

	/*=============================================
	REGISTRO DE ORDENES
	=============================================*/

	static public function ctrCrearOrden(){
		if(isset($_POST["productosOrden"])){

			if (isset($_POST["productosOrden"])) {

				$tabla = "ordenes_compra";

				$datos = array("fecha" => $_POST["nuevaFechaOrden"],
								"id_proveedor" => $_POST["nuevoIdProveedor"],
								"productos" => $_POST["productosOrden"]
							   );

				$respuesta = ModeloOrdenes::mdlIngresarOrden($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La orden ha sido creado correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "ordenes-compra";

						}

					});
				

					</script>';


				}	


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La orden no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "ordenes-compra";

						}

					});
				

				</script>';

			}


		}
    }

    /*=============================================
	MOSTRAR ORDENES
	=============================================*/

	static public function ctrMostrarOrdenes($item, $valor, $orden){

		$tabla = "ordenes_compra";

		$respuesta = ModeloOrdenes::MdlMostrarOrdenes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	BORRAR ORDEN
	=============================================*/

	static public function ctrBorrarOrden(){

		if(isset($_GET["idOrdenEliminar"])){

			$tabla ="ordenes_compra";
			$datos = $_GET["idOrdenEliminar"];

			$respuesta = ModeloOrdenes::mdlBorrarOrden($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La orden de compra ha sido borrada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "ordenes-compra";

								}
							})

				</script>';

			}		

		}

	}
}