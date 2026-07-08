<?php

class ControladorCategorias{

	/*=============================================
	REGISTRO DE CATEGORIA
	=============================================*/

	static public function ctrCrearCategoria(){

		if(isset($_POST["nuevoNombreCategoria"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoNombreCategoria"])){

				$tabla = "categorias";

				$datos = array("nombre" => $_POST["nuevoNombreCategoria"],
					           "descripcion" => $_POST["nuevaDescripcionCategoria"]
					           );

				$respuesta = ModeloCategorias::mdlIngresarCategoria($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La categoría ha sido creada correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "inventario";

						}

					});
				

					</script>';


				}	


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La categoría no se pudo crear!",
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
	MOSTRAR CATEGORIAS
	=============================================*/

	static public function ctrMostrarCategorias($item, $valor){

		$tabla = "categorias";

		$respuesta = ModeloCategorias::MdlMostrarCategorias($tabla, $item, $valor);

		return $respuesta;
	}

	/*=============================================
	EDITAR CATEGORÍAS
	=============================================*/

	static public function ctrEditarCategoria(){

		if(isset($_POST["editarDescripcionCategoria"])){  // Verifica que el campo exista

			if(trim($_POST["editarDescripcionCategoria"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡La categoría no puede ir vacía!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "inventario";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos
				$tabla = "categorias";
		
				$datos = array("descripcion" => $_POST["editarDescripcionCategoria"],
								"id" => $_POST["editarIdCategoria"]);
		
				$respuesta = ModeloCategorias::mdlEditarCategoria($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "La categoría ha sido editada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
								if (result.value) {
		
								window.location = "inventario";
		
								}
							})
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	BORRAR CATEGORIA
	=============================================*/

	static public function ctrBorrarCategoria(){

		if(isset($_GET["idCategoriaEliminar"])){

			$tabla ="categorias";
			$datos = $_GET["idCategoriaEliminar"];

			$respuesta = ModeloCategorias::mdlBorrarCategoria($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La categoria ha sido borrada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "inventario";

								}
							})

				</script>';

			}		

		}

	}


}
	


