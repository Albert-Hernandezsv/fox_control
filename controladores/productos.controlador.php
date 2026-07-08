<?php

class ControladorProductos{

	/*=============================================
	REGISTRO DE PRODUCTO
	=============================================*/

	static public function ctrCrearProducto(){

		if(isset($_POST["nuevoNombreProducto"])){

			if(isset($_POST["nuevoNombreProducto"])){

				/*=============================================
                    VALIDAR IMAGEN
                    =============================================*/

                    $ruta1 = "vistas/img/anonimo.jpg";


                    /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL BLOG
                    =============================================*/

                    $directorio = "vistas/img/productos/".$_POST["nuevoCodigoProducto"]."";

                    mkdir($directorio, 0755);

                    if (isset($_FILES['nuevaImagenProducto']['tmp_name']) && $_FILES['nuevaImagenProducto']['error'] === UPLOAD_ERR_OK) {

                        list($ancho, $alto) = getimagesize($_FILES["nuevaImagenProducto"]["tmp_name"]);

                        $nuevoAncho = 500;
                        $nuevoAlto = 400;

                        /*=============================================
                        IMAGEN 1
                        =============================================*/

                        if($_FILES["nuevaImagenProducto"]["type"] == "image/jpeg"){

                            /*=============================================
                            GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                            =============================================*/

                            $aleatorio = mt_rand(100,999);

                            $ruta1 = "vistas/img/productos/".$_POST["nuevoCodigoProducto"]."/".$aleatorio.".jpg";

                            $origen = imagecreatefromjpeg($_FILES["nuevaImagenProducto"]["tmp_name"]);						

                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                            imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                            imagejpeg($destino, $ruta1);

                        }

                        if($_FILES["nuevaImagenProducto"]["type"] == "image/png"){

                            /*=============================================
                            GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                            =============================================*/

                            $aleatorio = mt_rand(100,999);

                            $ruta1 = "vistas/img/productos/".$_POST["nuevoCodigoProducto"]."/".$aleatorio.".png";

                            $origen = imagecreatefrompng($_FILES["nuevaImagenProducto"]["tmp_name"]);						

                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                            imagealphablending($destino, false);
                            imagesavealpha($destino, true);
                            $transparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
                            imagefill($destino, 0, 0, $transparente);
                            
                            // Redimensionar y guardar la imagen
                            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                            imagepng($destino, $ruta1);
                        }
                    }

				$tabla = "inventario";
				$fecha_vencimiento = "000-00-00";

				if($_POST["nuevaFechaVencimiento"]){
					$fecha_vencimiento = $_POST["nuevaFechaVencimiento"];
				} else {
					$fecha_vencimiento = "000-00-00";	
				}

				$datos = array("nombre" => $_POST["nuevoNombreProducto"],
							   "tipo" => $_POST["nuevoNombreTipo"],
							   "categoria_id" => $_POST["nuevaCategoriaProducto"],
							   "precio_compra" => $_POST["nuevoPrecioCompraProducto"],
							   "precio_venta" => $_POST["nuevoPrecioVentaProducto"],
							   "stock" => $_POST["nuevoStockProducto"],
							   "descripcion" => $_POST["nuevaDescripcionProducto"],
							   "codigo" => $_POST["nuevoCodigoProducto"],
							   "unidadMedida" => $_POST["nuevaUnidadMedida"],
							   "exento_iva" => $_POST["nuevoExentoIva"],
							   "imagen" => $ruta1,
							   "peso" => $_POST["peso"],
							   "origen" => $_POST["origen"],
							   "marca" => $_POST["marca"],
							   "modelo" => $_POST["modelo"],
							   "fecha_vencimiento" => $fecha_vencimiento
					           );

				$respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);
				if($respuesta != "error"){
					$tabla1 = "ingreso_stock";
					date_default_timezone_set('America/El_Salvador'); // Establecer zona horaria de El Salvador
                  	$fechaActual = date("Y-m-d H:i:s");; // Obtener la fecha actual en formato YYYY-MM-DD
					$datos1 = array("cantidad" => $_POST["nuevoStockProducto"],
									"proveedor" => "",
									"fecha" => $fechaActual,
									"comentarios" => "Stock Inicial",
									"precio_compra" => $_POST["nuevoPrecioCompraProducto"],
							   		"precio_venta" => $_POST["nuevoPrecioVentaProducto"],
									"id_producto" => $respuesta);

					$respuesta1 = ModeloStocks::mdlIngresarStock($tabla1, $datos1);
					echo '<script>

					swal({

						type: "success",
						title: "¡El producto ha sido creado correctamente!",
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
						
							window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function ctrMostrarProductos($item, $valor){

		$tabla = "inventario";

		$respuesta = ModeloProductos::MdlMostrarProductos($tabla, $item, $valor);

		return $respuesta;
	}

	static public function ctrMostrarProductosPaginados($filtros, $limite, $offset){

		return ModeloProductos::mdlMostrarProductosPaginados($filtros, $limite, $offset);
	}

	static public function ctrContarProductosFiltrados($filtros){

		return ModeloProductos::mdlContarProductosFiltrados($filtros);
	}

	/*=============================================
	EDITAR PRODUCTOS
	=============================================*/

	static public function ctrEditarProducto(){

		if(isset($_POST["editarCodigoProducto"])){  // Verifica que el campo exista

			if(trim($_POST["editarCodigoProducto"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El código no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos


				/*=============================================
				VALIDAR IMAGEN
				=============================================*/

				$ruta = $_POST["imagenActualProducto"];

				if(isset($_FILES["editarImagenProducto"]["tmp_name"]) && !empty($_FILES["editarImagenProducto"]["tmp_name"])){

					list($ancho, $alto) = getimagesize($_FILES["editarImagenProducto"]["tmp_name"]);

					$nuevoAncho = 500;
					$nuevoAlto = 500;

					/*=============================================
					CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL USUARIO
					=============================================*/

					$directorio = "vistas/img/productos/".$_POST["editarCodigoProducto"];

					/*=============================================
					PRIMERO PREGUNTAMOS SI EXISTE OTRA IMAGEN EN LA BD
					=============================================*/
					if(!empty($_POST["imagenActualProducto"]) && basename($_POST["imagenActualProducto"]) != "anonimo.jpg"){

						// Elimina la imagen si no es "anonimo.jpg"
						unlink($_POST["imagenActualProducto"]);
				
					}else{
						// Crea el directorio si no existe
						if(!is_dir($directorio)){
							mkdir($directorio, 0755);
						}
					}

					/*=============================================
					DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
					=============================================*/

					if($_FILES["editarImagenProducto"]["type"] == "image/jpeg"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/productos/".$_POST["editarCodigoProducto"]."/".$aleatorio.".jpg";

						$origen = imagecreatefromjpeg($_FILES["editarImagenProducto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagejpeg($destino, $ruta);

					}

					if($_FILES["editarImagenProducto"]["type"] == "image/png"){

						/*=============================================
						GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						=============================================*/

						$aleatorio = mt_rand(100,999);

						$ruta = "vistas/img/productos/".$_POST["editarCodigoProducto"]."/".$aleatorio.".png";

						$origen = imagecreatefrompng($_FILES["editarImagenProducto"]["tmp_name"]);						

						$destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

						imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

						imagepng($destino, $ruta);

					}

				}

				$tabla = "inventario";

				$fecha_vencimiento = "000-00-00";

				if($_POST["editarFechaVencimiento"]){
					$fecha_vencimiento = $_POST["editarFechaVencimiento"];
				} else {
					$fecha_vencimiento = "000-00-00";	
				}
		
				$datos = array("nombre" => $_POST["editarNombreProducto"],
							   "tipo" => $_POST["editarNombreTipo"],
							   "unidadMedida" => $_POST["editarUnidadMedida"],
							   "categoria_id" => $_POST["editarCategoriaProducto"],
							   "precio_compra" => $_POST["editarPrecioCompraProducto"],
							   "precio_venta" => $_POST["editarPrecioVentaProducto"],
							   "stock" => $_POST["editarStockProducto"],
							   "descripcion" => $_POST["editarDescripcionProducto"],
							   "codigo" => $_POST["editarCodigoProducto"],
							   "exento_iva" => $_POST["editarExentoIva"],
							   "imagen" => $ruta,
							   "origen" => $_POST["editarOrigen"],
							   "peso" => $_POST["editarPeso"],
							   "marca" => $_POST["editarMarca"],
							   "modelo" => $_POST["editarModelo"],
								"id" => $_POST["editarIdProducto"],
								"fecha_vencimiento" => $fecha_vencimiento);
		
				$respuesta = ModeloProductos::mdlEditarProducto($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "El producto ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
								if (result.value) {
		
								window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";
		
								}
							})
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	DUPLICAR PRODUCTOS
	=============================================*/

	static public function ctrDuplicarProducto(){

		if(isset($_POST["editarNombreProducto"])){

			if(isset($_POST["editarNombreProducto"])){

				/*=============================================
                    VALIDAR IMAGEN
                    =============================================*/

                    $ruta1 = "vistas/img/anonimo.jpg";


                    /*=============================================
                    CREAMOS EL DIRECTORIO DONDE VAMOS A GUARDAR LA FOTO DEL BLOG
                    =============================================*/

                    $directorio = "vistas/img/productos/".$_POST["editarCodigoProductoDuplicar"]."";

                    mkdir($directorio, 0755);

                    if (isset($_FILES['imagenActualProducto']['tmp_name']) && $_FILES['imagenActualProducto']['error'] === UPLOAD_ERR_OK) {

                        list($ancho, $alto) = getimagesize($_FILES["imagenActualProducto"]["tmp_name"]);

                        $nuevoAncho = 500;
                        $nuevoAlto = 400;

                        /*=============================================
                        IMAGEN 1
                        =============================================*/

                        if($_FILES["imagenActualProducto"]["type"] == "image/jpeg"){

                            /*=============================================
                            GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                            =============================================*/

                            $aleatorio = mt_rand(100,999);

                            $ruta1 = "vistas/img/productos/".$_POST["editarCodigoProductoDuplicar"]."/".$aleatorio.".jpg";

                            $origen = imagecreatefromjpeg($_FILES["imagenActualProducto"]["tmp_name"]);						

                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                            imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

                            imagejpeg($destino, $ruta1);

                        }

                        if($_FILES["imagenActualProducto"]["type"] == "image/png"){

                            /*=============================================
                            GUARDAMOS LA IMAGEN EN EL DIRECTORIO
                            =============================================*/

                            $aleatorio = mt_rand(100,999);

                            $ruta1 = "vistas/img/productos/".$_POST["editarCodigoProductoDuplicar"]."/".$aleatorio.".png";

                            $origen = imagecreatefrompng($_FILES["imagenActualProducto"]["tmp_name"]);						

                            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

                            imagealphablending($destino, false);
                            imagesavealpha($destino, true);
                            $transparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
                            imagefill($destino, 0, 0, $transparente);
                            
                            // Redimensionar y guardar la imagen
                            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                            imagepng($destino, $ruta1);
                        }
                    }

				$tabla = "inventario";
				$fecha_vencimiento = "000-00-00";

				if($_POST["editarFechaVencimiento"]){
					$fecha_vencimiento = $_POST["editarFechaVencimiento"];
				} else {
					$fecha_vencimiento = "000-00-00";	
				}

				$datos = array("nombre" => $_POST["editarNombreProducto"],
							   "tipo" => $_POST["editarNombreTipo"],
							   "categoria_id" => $_POST["editarCategoriaProducto"],
							   "precio_compra" => $_POST["editarPrecioCompraProducto"],
							   "precio_venta" => $_POST["editarPrecioVentaProducto"],
							   "stock" => $_POST["editarStockProducto"],
							   "descripcion" => $_POST["editarDescripcionProducto"],
							   "codigo" => $_POST["editarCodigoProductoDuplicar"],
							   "unidadMedida" => $_POST["editarUnidadMedida"],
							   "exento_iva" => $_POST["editarExentoIva"],
							   "imagen" => $ruta1,
							   "peso" => $_POST["editarPeso"],
							   "origen" => $_POST["editarOrigen"],
							   "marca" => $_POST["editarMarca"],
							   "modelo" => $_POST["editarModelo"],
							   "fecha_vencimiento" => $fecha_vencimiento
					           );

				$respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);
				if($respuesta != "error"){
					$tabla1 = "ingreso_stock";
					date_default_timezone_set('America/El_Salvador'); // Establecer zona horaria de El Salvador
                  	$fechaActual = date("Y-m-d H:i:s");; // Obtener la fecha actual en formato YYYY-MM-DD
					$datos1 = array("cantidad" => $_POST["editarStockProducto"],
									"proveedor" => "",
									"fecha" => $fechaActual,
									"comentarios" => "Stock Inicial",
									"precio_compra" => $_POST["editarPrecioCompraProducto"],
							   		"precio_venta" => $_POST["editarPrecioVentaProducto"],
									"id_producto" => $respuesta);

					$respuesta1 = ModeloStocks::mdlIngresarStock($tabla1, $datos1);
					echo '<script>

					swal({

						type: "success",
						title: "¡El producto ha sido creado correctamente!",
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
						
							window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	BORRAR PRODUCTO
	=============================================*/

	static public function ctrBorrarProducto(){

		if(isset($_GET["idProductoEliminar"])){

			/*=============================================
			ELIMINAR CARPETA DE IMAGEN
			=============================================*/

			function eliminarCarpetaConImagenes($carpeta) {
				// Verificar si la carpeta existe
				if (is_dir($carpeta)) {
					// Obtener todos los archivos de la carpeta
					$archivos = scandir($carpeta);
					foreach ($archivos as $archivo) {
						// Omitir '.' y '..'
						if ($archivo != "." && $archivo != "..") {
							$rutaArchivo = $carpeta . "/" . $archivo;
							// Eliminar el archivo
							unlink($rutaArchivo);
						}
					}
					// Eliminar la carpeta una vez que esté vacía
					if (rmdir($carpeta)) {
						echo "La carpeta y sus imágenes han sido eliminadas correctamente.";
					} else {
						echo "Error al intentar eliminar la carpeta.";
					}
				} else {
					echo "La carpeta no existe.";
				}
			}
			
			eliminarCarpetaConImagenes("vistas/img/productos/" . $_GET["codigoProductoEliminar"]);

			$tabla ="inventario";
			$datos = $_GET["idProductoEliminar"];

			$respuesta = ModeloProductos::mdlBorrarProducto($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El producto ha sido borrado correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "index.php?ruta=inventario&filtroNombre=todos&filtroTipo=todos&filtroCategoria=todos&filtroCodigo=todos&filtroStock=todos";

								}
							})

				</script>';

			}		

		}

	}


}
	


