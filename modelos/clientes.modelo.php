<?php

require_once "conexion.php";

class ModeloClientes{

	/*=============================================
	MOSTRAR CLIENTES
	=============================================*/

	static public function mdlMostrarClientes($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarClientesPaginados($tabla, $busqueda, $limite, $offset){

		$condicion = "";
		if($busqueda !== ""){
			$condicion = "WHERE nombre LIKE :busqueda";
		}

		$stmt = Conexion::conectar()->prepare(
			"SELECT id, nombre, correo, telefono, departamento, municipio, distrito, direccion, NIT, DUI, NRC, tipo_cliente, nombrePais, tipoPersona
			 FROM $tabla
			 $condicion
			 ORDER BY id DESC
			 LIMIT :limite OFFSET :offset"
		);

		if($busqueda !== ""){
			$stmt->bindValue(":busqueda", "%".$busqueda."%", PDO::PARAM_STR);
		}

		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarClientesPaginados($tabla, $busqueda){

		$condicion = "";
		if($busqueda !== ""){
			$condicion = "WHERE nombre LIKE :busqueda";
		}

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM $tabla $condicion");

		if($busqueda !== ""){
			$stmt->bindValue(":busqueda", "%".$busqueda."%", PDO::PARAM_STR);
		}

		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	/*=============================================
	MOSTRAR CLIENTES MIOS
	=============================================*/

	static public function mdlMostrarClientesMios($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar1()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar1()->prepare("SELECT * FROM $tabla ORDER BY $orden DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE CLIENTE
	=============================================*/

	static public function mdlIngresarCliente($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, direccion, NIT, DUI, NRC, codPais, nombrePais, tipoPersona, codActividad, descActividad, tipo_cliente, correo, departamento, municipio, distrito, telefono) VALUES (:nombre, :direccion, :NIT, :DUI, :NRC, :codPais, :nombrePais, :tipoPersona,:codActividad, :descActividad, :tipo_cliente, :correo, :departamento, :municipio, :distrito, :telefono)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":NIT", $datos["NIT"], PDO::PARAM_STR);
		$stmt->bindParam(":DUI", $datos["DUI"], PDO::PARAM_STR);
		$stmt->bindParam(":NRC", $datos["NRC"], PDO::PARAM_STR);
		$stmt->bindParam(":codPais", $datos["codPais"], PDO::PARAM_STR);
		$stmt->bindParam(":nombrePais", $datos["nombrePais"], PDO::PARAM_STR);
		$stmt->bindParam(":tipoPersona", $datos["tipoPersona"], PDO::PARAM_INT);
		$stmt->bindParam(":codActividad", $datos["codActividad"], PDO::PARAM_STR);
		$stmt->bindParam(":descActividad", $datos["descActividad"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_cliente", $datos["tipo_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":departamento", $datos["departamento"], PDO::PARAM_STR);
		$stmt->bindParam(":municipio", $datos["municipio"], PDO::PARAM_STR);
		$stmt->bindParam(":distrito", $datos["distrito"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}

	/*=============================================
	REGISTRO DE MOTORISTA
	=============================================*/

	static public function mdlIngresarMotorista($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, duiMotorista, placaMotorista) VALUES (:nombre, :duiMotorista, :placaMotorista)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":duiMotorista", $datos["duiMotorista"], PDO::PARAM_STR);
		$stmt->bindParam(":placaMotorista", $datos["placaMotorista"], PDO::PARAM_STR);		

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}

	/*=============================================
	REGISTRO DE PROVEEDOR
	=============================================*/

	static public function mdlIngresarProveedor($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, nit, telefono, correo, direccion, condicion_pago) VALUES (:nombre, :nit, :telefono, :correo, :direccion, :condicion_pago)");

		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":nit", $datos["nit"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt->bindParam(":condicion_pago", $datos["condicion_pago"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		$stmt->close();
		
		$stmt = null;

	}


	/*=============================================
	EDITAR CLIENTE
	=============================================*/

	static public function mdlEditarCliente($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, direccion = :direccion, NIT = :NIT, DUI = :DUI, NRC = :NRC, codPais = :codPais, nombrePais = :nombrePais, tipoPersona = :tipoPersona, codActividad = :codActividad, descActividad = :descActividad, tipo_cliente = :tipo_cliente, correo = :correo, departamento = :departamento, municipio = :municipio, distrito = :distrito, telefono = :telefono WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":NIT", $datos["NIT"], PDO::PARAM_STR);
		$stmt -> bindParam(":DUI", $datos["DUI"], PDO::PARAM_STR);
		$stmt -> bindParam(":NRC", $datos["NRC"], PDO::PARAM_STR);
		$stmt -> bindParam(":codPais", $datos["codPais"], PDO::PARAM_STR);
		$stmt -> bindParam(":nombrePais", $datos["nombrePais"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipoPersona", $datos["tipoPersona"], PDO::PARAM_INT);
		$stmt -> bindParam(":codActividad", $datos["codActividad"], PDO::PARAM_STR);
		$stmt -> bindParam(":descActividad", $datos["descActividad"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipo_cliente", $datos["tipo_cliente"], PDO::PARAM_STR);
		$stmt -> bindParam(":departamento", $datos["departamento"], PDO::PARAM_STR);
		$stmt -> bindParam(":municipio", $datos["municipio"], PDO::PARAM_STR);
		$stmt -> bindParam(":distrito", $datos["distrito"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
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
	EDITAR MOTORISTA
	=============================================*/

	static public function mdlEditarMotorista($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, duiMotorista = :duiMotorista, placaMotorista = :placaMotorista WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":duiMotorista", $datos["duiMotorista"], PDO::PARAM_STR);
		$stmt -> bindParam(":placaMotorista", $datos["placaMotorista"], PDO::PARAM_STR);
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
	EDITAR PROVEEDOR
	=============================================*/

	static public function mdlEditarProveedor($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, nit = :nit, telefono = :telefono, correo = :correo, direccion = :direccion, condicion_pago = :condicion_pago WHERE id = :id");

		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":nit", $datos["nit"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt -> bindParam(":condicion_pago", $datos["condicion_pago"], PDO::PARAM_STR);
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
	EDITAR DATOS EMPRESARIALES
	=============================================*/

	static public function mdlEditarEmpresa($tabla, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nit = :nit, nrc = :nrc, passwordPri = :passwordPri, nombre = :nombre, codActividad = :codActividad,
		desActividad = :desActividad, tipoEstablecimiento = :tipoEstablecimiento, departamento = :departamento, municipio = :municipio, distrito = :distrito, direccion = :direccion,
		telefono = :telefono, correo = :correo, contra_descuentos = :contra_descuentos, tipo_pdf = :tipo_pdf WHERE id = :id");

		$stmt -> bindParam(":nit", $datos["nit"], PDO::PARAM_STR);
		$stmt -> bindParam(":nrc", $datos["nrc"], PDO::PARAM_STR);
		$stmt -> bindParam(":passwordPri", $datos["passwordPri"], PDO::PARAM_STR);
		$stmt -> bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt -> bindParam(":codActividad", $datos["codActividad"], PDO::PARAM_STR);
		$stmt -> bindParam(":desActividad", $datos["desActividad"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipoEstablecimiento", $datos["tipoEstablecimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":departamento", $datos["departamento"], PDO::PARAM_STR);
		$stmt -> bindParam(":municipio", $datos["municipio"], PDO::PARAM_STR);
		$stmt -> bindParam(":distrito", $datos["distrito"], PDO::PARAM_STR);
		$stmt -> bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":contra_descuentos", $datos["contra_descuentos"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipo_pdf", $datos["tipo_pdf"], PDO::PARAM_STR);
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
	BORRAR CLIENTE
	=============================================*/

	static public function mdlBorrarCliente($tabla, $datos){

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

}
