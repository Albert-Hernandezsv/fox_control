<?php

require_once "conexion.php";

class ModeloProductos{

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function mdlMostrarProductos($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}
		

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarProductosPaginados($filtros, $limite, $offset){

		$condiciones = array();
		$parametros = array();

		if($filtros["nombre"] !== ""){
			$condiciones[] = "i.nombre LIKE :nombre";
			$parametros[":nombre"] = "%".$filtros["nombre"]."%";
		}

		if($filtros["tipo"] !== ""){
			$condiciones[] = "i.tipo = :tipo";
			$parametros[":tipo"] = $filtros["tipo"];
		}

		if($filtros["categoria"] !== ""){
			$condiciones[] = "i.categoria_id = :categoria";
			$parametros[":categoria"] = $filtros["categoria"];
		}

		if($filtros["codigo"] !== ""){
			$condiciones[] = "i.codigo LIKE :codigo";
			$parametros[":codigo"] = "%".$filtros["codigo"]."%";
		}

		if($filtros["stock"] !== ""){
			$condiciones[] = "i.stock <= :stock";
			$parametros[":stock"] = $filtros["stock"];
		}

		$where = count($condiciones) ? " WHERE ".implode(" AND ", $condiciones) : "";
		$sql = "SELECT i.*, c.nombre AS categoria_nombre
				FROM inventario i
				LEFT JOIN categorias c ON c.id = i.categoria_id
				$where
				ORDER BY i.id DESC
				LIMIT :limite OFFSET :offset";

		$stmt = Conexion::conectar()->prepare($sql);

		foreach($parametros as $clave => $valor){
			$stmt->bindValue($clave, $valor, PDO::PARAM_STR);
		}

		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarProductosFiltrados($filtros){

		$condiciones = array();
		$parametros = array();

		if($filtros["nombre"] !== ""){
			$condiciones[] = "nombre LIKE :nombre";
			$parametros[":nombre"] = "%".$filtros["nombre"]."%";
		}

		if($filtros["tipo"] !== ""){
			$condiciones[] = "tipo = :tipo";
			$parametros[":tipo"] = $filtros["tipo"];
		}

		if($filtros["categoria"] !== ""){
			$condiciones[] = "categoria_id = :categoria";
			$parametros[":categoria"] = $filtros["categoria"];
		}

		if($filtros["codigo"] !== ""){
			$condiciones[] = "codigo LIKE :codigo";
			$parametros[":codigo"] = "%".$filtros["codigo"]."%";
		}

		if($filtros["stock"] !== ""){
			$condiciones[] = "stock <= :stock";
			$parametros[":stock"] = $filtros["stock"];
		}

		$where = count($condiciones) ? " WHERE ".implode(" AND ", $condiciones) : "";
		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM inventario".$where);

		foreach($parametros as $clave => $valor){
			$stmt->bindValue($clave, $valor, PDO::PARAM_STR);
		}

		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	/*=============================================
	REGISTRO DE PRODUCTO
	=============================================*/

	static public function mdlIngresarProducto($tabla, $datos){
		$pdo = Conexion::conectar();
		$stmt = $pdo->prepare("INSERT INTO $tabla(nombre, tipo, categoria_id, precio_compra, precio_venta, stock, descripcion, codigo, unidadMedida, imagen, peso, origen, exento_iva, fecha_vencimiento, marca, modelo) VALUES (:nombre, :tipo, :categoria_id, :precio_compra, :precio_venta, :stock, :descripcion, :codigo, :unidadMedida, :imagen, :peso, :origen, :exento_iva, :fecha_vencimiento, :marca, :modelo)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":categoria_id", $datos["categoria_id"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
		$stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":unidadMedida", $datos["unidadMedida"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
		$stmt->bindParam(":peso", $datos["peso"], PDO::PARAM_STR);
		$stmt->bindParam(":origen", $datos["origen"], PDO::PARAM_STR);
		$stmt->bindParam(":marca", $datos["marca"], PDO::PARAM_STR);
		$stmt->bindParam(":modelo", $datos["modelo"], PDO::PARAM_STR);
		$stmt->bindParam(":exento_iva", $datos["exento_iva"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);

		if($stmt->execute()){
			$idProducto = $pdo->lastInsertId();
			return $idProducto;
		} else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}


	/*=============================================
	EDITAR PRODUCTO
	=============================================*/

	static public function mdlEditarProducto($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, tipo = :tipo, categoria_id = :categoria_id, precio_compra = :precio_compra, precio_venta = :precio_venta, stock = :stock, descripcion = :descripcion, codigo = :codigo, unidadMedida = :unidadMedida, imagen = :imagen, peso = :peso, origen = :origen, exento_iva = :exento_iva, fecha_vencimiento = :fecha_vencimiento, marca = :marca, modelo = :modelo WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt -> bindParam(":categoria_id", $datos["categoria_id"], PDO::PARAM_INT);
		$stmt -> bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
		$stmt -> bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
		$stmt -> bindParam(":stock", $datos["stock"], PDO::PARAM_INT);
		$stmt -> bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt -> bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt -> bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
		$stmt -> bindParam(":origen", $datos["origen"], PDO::PARAM_STR);
		$stmt -> bindParam(":peso", $datos["peso"], PDO::PARAM_STR);
		$stmt -> bindParam(":marca", $datos["marca"], PDO::PARAM_STR);
		$stmt -> bindParam(":modelo", $datos["modelo"], PDO::PARAM_STR);
		$stmt -> bindParam(":unidadMedida", $datos["unidadMedida"], PDO::PARAM_STR);
		$stmt -> bindParam(":exento_iva", $datos["exento_iva"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
	

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
	
	/*=============================================
	BORRAR PRODUCTO
	=============================================*/

	static public function mdlBorrarProducto($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

		$stmt -> bindParam(":id", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;


	}
	
	/*=============================================
	ACTUALIZAR PRODUCTO
	=============================================*/

	static public function mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}
}
