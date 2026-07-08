<?php

require_once "conexion.php";

class ModeloFacturas{

	/*=============================================
	MOSTRAR FACTURAS
	=============================================*/

	static public function mdlMostrarFacturas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasFac($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 50");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 50");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasVarias($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetchAll();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasDashPorMes($anio, $mes, $orden){
		try {
			$conexion = Conexion::conectar();
			$ordenCampo = !empty($orden) ? $orden : "fecEmi";
	
			$stmt = $conexion->prepare("SELECT * FROM facturas_locales WHERE YEAR(fecEmi) = :anio AND MONTH(fecEmi) = :mes ORDER BY $ordenCampo DESC");
			$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
			$stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
	
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		} catch (PDOException $e) {
			error_log("Error en mdlMostrarFacturasDashPorMes: " . $e->getMessage());
			return [];
		} finally {
			$stmt = null;
			$conexion = null;
		}
	}
	
	static public function mdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 10");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 10");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarCotizacionesAutorizadasPaginadas($limite, $offset){

		$stmt = Conexion::conectar()->prepare(
			"SELECT ca.*, c.nombre AS cliente_nombre, u.nombre AS usuario_nombre
			 FROM cotizaciones_autorizadas ca
			 LEFT JOIN clientes c ON c.id = ca.id_cliente
			 LEFT JOIN usuarios u ON u.id = ca.id_usuario
			 ORDER BY ca.id DESC
			 LIMIT :limite OFFSET :offset"
		);
		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarCotizacionesAutorizadas(){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM cotizaciones_autorizadas");
		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarCotizacionesDisponiblesPaginadas($limite, $offset){

		$stmt = Conexion::conectar()->prepare(
			"SELECT ca.*, c.nombre AS cliente_nombre, u.nombre AS usuario_nombre
			 FROM cotizaciones_autorizadas ca
			 LEFT JOIN clientes c ON c.id = ca.id_cliente
			 LEFT JOIN usuarios u ON u.id = ca.id_usuario
			 WHERE ca.estado = 'Facturacion'
			 ORDER BY ca.id DESC
			 LIMIT :limite OFFSET :offset"
		);
		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarCotizacionesDisponibles(){

		$stmt = Conexion::conectar()->prepare(
			"SELECT COUNT(*) FROM cotizaciones_autorizadas WHERE estado = 'Facturacion'"
		);
		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarFacturasPaginadas($idUsuario, $limite, $offset){

		$condicionUsuario = $idUsuario === null ? "" : " AND f.id_usuario = :id_usuario";
		$sql = "SELECT f.*, c.nombre AS cliente_nombre, c.tipo_cliente AS cliente_tipo
				FROM facturas_locales f
				LEFT JOIN clientes c ON c.id = f.id_cliente
				WHERE f.tipoDte NOT IN ('05', '06')
				AND (f.modo IS NULL OR f.modo <> 'Contingencia')
				$condicionUsuario
				ORDER BY f.id DESC
				LIMIT :limite OFFSET :offset";

		$stmt = Conexion::conectar()->prepare($sql);

		if($idUsuario !== null){
			$stmt->bindValue(":id_usuario", (int) $idUsuario, PDO::PARAM_INT);
		}

		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarFacturasPaginadas($idUsuario){

		$condicionUsuario = $idUsuario === null ? "" : " AND id_usuario = :id_usuario";
		$sql = "SELECT COUNT(*)
				FROM facturas_locales
				WHERE tipoDte NOT IN ('05', '06')
				AND (modo IS NULL OR modo <> 'Contingencia')
				$condicionUsuario";

		$stmt = Conexion::conectar()->prepare($sql);

		if($idUsuario !== null){
			$stmt->bindValue(":id_usuario", (int) $idUsuario, PDO::PARAM_INT);
		}

		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarNotasContablesPaginadas($limite, $offset){

		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM facturas_locales
			 WHERE tipoDte IN ('05', '06')
			 ORDER BY id DESC
			 LIMIT :limite OFFSET :offset"
		);

		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarNotasContables(){

		$stmt = Conexion::conectar()->prepare(
			"SELECT COUNT(*) FROM facturas_locales WHERE tipoDte IN ('05', '06')"
		);
		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarAnulacionesPaginadas($limite, $offset){

		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM anuladas ORDER BY id DESC LIMIT :limite OFFSET :offset"
		);
		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarAnulaciones(){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM anuladas");
		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarEliminadasPaginadas($limite, $offset){

		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM eliminadas ORDER BY id DESC LIMIT :limite OFFSET :offset"
		);
		$stmt->bindValue(":limite", (int) $limite, PDO::PARAM_INT);
		$stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	static public function mdlContarEliminadas(){

		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM eliminadas");
		$stmt->execute();

		return (int) $stmt->fetchColumn();
	}

	static public function mdlMostrarFacturasAsc($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id ASC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id ASC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasEliminadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarFacturasVentas($tabla, $fechaInicio, $fechaFin){

		$stmt = Conexion::conectar()->prepare(
			"SELECT * FROM $tabla 
			 WHERE fecEmi BETWEEN :fechaInicio AND :fechaFin 
			 ORDER BY fecEmi DESC"
		);
	
		$stmt->bindParam(":fechaInicio", $fechaInicio, PDO::PARAM_STR);
		$stmt->bindParam(":fechaFin", $fechaFin, PDO::PARAM_STR);
	
		$stmt->execute();
	
		$resultado = $stmt->fetchAll();
	
		
		$stmt = null;
	
		return $resultado;
	}


	/*=============================================
	MOSTRAR FACTURAS CONTINGENCIA
	=============================================*/

	static public function mdlMostrarEventosContingencias($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}/*=============================================
	MOSTRAR COMPRAS
	=============================================*/

	static public function mdlMostrarCompras($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	static public function mdlMostrarComprasOptimizadas($tabla, $item, $valor, $orden){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC LIMIT 10");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC LIMIT 10");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL
	=============================================*/

	private static function generarNumeroControl($conexion, $tipoDte){

		$columnaControl = self::obtenerColumnaNumeroControl($tipoDte);
		if($columnaControl === ""){
			return isset($tipoDte) && $tipoDte === "18" ? "No aplica" : "";
		}

		$stmt = $conexion->prepare("SELECT $columnaControl FROM emisor WHERE id = 1 FOR UPDATE");
		if(!$stmt->execute()){
			throw new Exception("No se pudo bloquear el correlativo del emisor");
		}
		$numeroControlActual = $stmt->fetchColumn();

		if($numeroControlActual === false){
			throw new Exception("No se encontro el emisor para generar numero de control");
		}

		$parteAleatoria = "S001P001";
		$numeroControlActual = trim((string) $numeroControlActual);
		$parteNumerica = $numeroControlActual !== "" ? (int) substr($numeroControlActual, -15) : 0;

		do {
			$parteNumerica++;
			$parteNumericaIncrementada = str_pad($parteNumerica, 15, "0", STR_PAD_LEFT);
			$numeroControl = "DTE-" . $tipoDte . "-" . $parteAleatoria . "-" . $parteNumericaIncrementada;
		} while(self::existeNumeroControl($conexion, $numeroControl));

		return $numeroControl;
	}

	private static function obtenerColumnaNumeroControl($tipoDte){

		$columnas = array(
			"01" => "numeroControl01",
			"03" => "numeroControl03",
			"04" => "numeroControl04",
			"05" => "numeroControl05",
			"06" => "numeroControl06",
			"11" => "numeroControl11",
			"14" => "numeroControl14"
		);

		return isset($columnas[$tipoDte]) ? $columnas[$tipoDte] : "";
	}

	private static function actualizarNumeroControlPorTipo($conexion, $tipoDte, $numeroControl){

		if($numeroControl === "" || $numeroControl === "No aplica"){
			return;
		}

		$columnaControl = self::obtenerColumnaNumeroControl($tipoDte);
		if($columnaControl === ""){
			return;
		}

		$stmt = $conexion->prepare("UPDATE emisor SET $columnaControl = :numeroControl WHERE id = 1");
		if(!$stmt->execute(array(":numeroControl" => $numeroControl))){
			throw new Exception("No se pudo actualizar el correlativo del emisor");
		}
	}

	private static function existeNumeroControl($conexion, $numeroControl){

		$stmt = $conexion->prepare("SELECT COUNT(*) FROM facturas_locales WHERE numeroControl = :numeroControl");
		if(!$stmt->execute(array(":numeroControl" => $numeroControl))){
			throw new Exception("No se pudo validar el numero de control");
		}
		return (int) $stmt->fetchColumn() > 0;
	}

	static public function mdlIngresarFactura($tabla, $datos) {

		$plazoPago = isset($datos["plazo_pago"]) ? $datos["plazo_pago"] : "";
		$periodoPago = isset($datos["periodo_pago"]) ? $datos["periodo_pago"] : 0;
		$tipoRegimen = isset($datos["tipo_regimen"]) && $datos["tipo_regimen"] !== "" ? $datos["tipo_regimen"] : null;
		$codIncoterms = isset($datos["cod_incoterms"]) && $datos["cod_incoterms"] !== "" ? $datos["cod_incoterms"] : null;
		$descIncoterms = isset($datos["desc_incoterms"]) && $datos["desc_incoterms"] !== "" ? $datos["desc_incoterms"] : null;

		$conexion = Conexion::conectar();
		try {
			$conexion->beginTransaction();
			$datos["numeroControl"] = self::generarNumeroControl($conexion, $datos["tipoDte"]);

        	$stmt = $conexion->prepare("INSERT INTO $tabla(id_cliente, id_vendedor, id_usuario, modo, notaRemision, estado, idFacturaRelacionada, productos, total, totalSinIva, tipoDte, recintoFiscal, regimen, tipo_regimen, cod_incoterms, desc_incoterms, modoTransporte, seguro, flete, idMotorista, condicionOperacion, plazo_pago, periodo_pago, numeroControl, codigoGeneracion, horEmi, fecEmi, gran_contribuyente) VALUES (:id_cliente, :id_vendedor, :id_usuario, :modo, :notaRemision, :estado, :idFacturaRelacionada, :productos, :total, :totalSinIva, :tipoDte, :recintoFiscal, :regimen, :tipo_regimen, :cod_incoterms, :desc_incoterms, :modoTransporte, :seguro, :flete, :idMotorista, :condicionOperacion, :plazo_pago, :periodo_pago, :numeroControl, :codigoGeneracion, :horEmi, :fecEmi, :gran_contribuyente)");

		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":modo", $datos["modo"], PDO::PARAM_STR);
		$stmt->bindParam(":notaRemision", $datos["notaRemision"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt->bindParam(":idFacturaRelacionada", $datos["idFacturaRelacionada"], PDO::PARAM_STR);
        $stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR); // Cambié esto
        $stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":totalSinIva", $datos["totalSinIva"], PDO::PARAM_STR);
        $stmt->bindParam(":tipoDte", $datos["tipoDte"], PDO::PARAM_STR);
		$stmt->bindParam(":gran_contribuyente", $datos["gran_contribuyente"], PDO::PARAM_STR);
		
		$stmt->bindParam(":recintoFiscal", $datos["recintoFiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":regimen", $datos["regimen"], PDO::PARAM_STR);
		$stmt->bindValue(":tipo_regimen", $tipoRegimen, $tipoRegimen === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindValue(":cod_incoterms", $codIncoterms, $codIncoterms === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindValue(":desc_incoterms", $descIncoterms, $descIncoterms === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindParam(":modoTransporte", $datos["modoTransporte"], PDO::PARAM_STR);
		$stmt->bindParam(":seguro", $datos["seguro"], PDO::PARAM_STR);
		$stmt->bindParam(":flete", $datos["flete"], PDO::PARAM_STR);
		$stmt->bindParam(":idMotorista", $datos["idMotorista"], PDO::PARAM_INT);

		$stmt->bindParam(":condicionOperacion", $datos["condicionOperacion"], PDO::PARAM_STR);
		$stmt->bindParam(":plazo_pago", $plazoPago, PDO::PARAM_STR);
		$stmt->bindParam(":periodo_pago", $periodoPago, PDO::PARAM_STR);
		$stmt->bindParam(":numeroControl", $datos["numeroControl"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);

	        if(!$stmt->execute()) {
				throw new Exception("No se pudo insertar la factura");
	        }

			self::actualizarNumeroControlPorTipo($conexion, $datos["tipoDte"], $datos["numeroControl"]);
			$conexion->commit();
	        return "ok";
		} catch(Throwable $e) {
			if($conexion->inTransaction()){
				$conexion->rollBack();
			}
			error_log("Error al crear factura local: " . $e->getMessage());
			return "error";
		}
    }

	/*=============================================
	REGISTRO DE FACTURA LOCAL CONTINGENCIA
	=============================================*/
	static public function mdlBorrarEventoRetorno($idFactura, $movimientosInventario){

		$conexion = Conexion::conectar();
		try {
			$conexion->beginTransaction();
			$consultaStock = $conexion->prepare("SELECT stock FROM inventario WHERE id = :id FOR UPDATE");
			$actualizarStock = $conexion->prepare("UPDATE inventario SET stock = :stock WHERE id = :id");

			foreach($movimientosInventario as $movimiento){
				$consultaStock->execute(array(":id" => $movimiento["idProducto"]));
				$stockActual = $consultaStock->fetchColumn();
				if($stockActual === false){
					throw new Exception("Producto de inventario no encontrado");
				}
				$stockNuevo = (float) $stockActual - (int) $movimiento["cantidad"];
				if($stockNuevo < 0){
					$conexion->rollBack();
					return "stock_insuficiente";
				}
				$actualizarStock->execute(array(":stock" => $stockNuevo, ":id" => $movimiento["idProducto"]));
			}

			$stmt = $conexion->prepare("DELETE FROM facturas_locales WHERE id = :id AND tipoDte = '18'");
			$stmt->execute(array(":id" => $idFactura));
			if($stmt->rowCount() !== 1){
				throw new Exception("Evento de retorno no encontrado");
			}

			$conexion->commit();
			return "ok";
		} catch(Throwable $e) {
			if($conexion->inTransaction()){
				$conexion->rollBack();
			}
			error_log("Error al eliminar evento de retorno: " . $e->getMessage());
			return "error";
		}
	}

	static public function mdlIngresarEventoRetorno($tabla, $datos, $movimientosInventario){

		$conexion = Conexion::conectar();
		try {
			$conexion->beginTransaction();
			$sql = "INSERT INTO $tabla(id_cliente, id_vendedor, id_usuario, modo, notaRemision, estado, idFacturaRelacionada, productos, total, totalSinIva, tipoDte, recintoFiscal, regimen, tipo_regimen, cod_incoterms, desc_incoterms, modoTransporte, seguro, flete, idMotorista, condicionOperacion, plazo_pago, periodo_pago, numeroControl, codigoGeneracion, horEmi, fecEmi, gran_contribuyente) VALUES (:id_cliente, :id_vendedor, :id_usuario, :modo, :notaRemision, :estado, :idFacturaRelacionada, :productos, :total, :totalSinIva, :tipoDte, :recintoFiscal, :regimen, :tipo_regimen, :cod_incoterms, :desc_incoterms, :modoTransporte, :seguro, :flete, :idMotorista, :condicionOperacion, :plazo_pago, :periodo_pago, :numeroControl, :codigoGeneracion, :horEmi, :fecEmi, :gran_contribuyente)";
			$stmt = $conexion->prepare($sql);
			$stmt->execute(array(
				":id_cliente" => $datos["id_cliente"],
				":id_vendedor" => $datos["id_vendedor"],
				":id_usuario" => $datos["id_usuario"],
				":modo" => $datos["modo"],
				":notaRemision" => $datos["notaRemision"],
				":estado" => $datos["estado"],
				":idFacturaRelacionada" => $datos["idFacturaRelacionada"],
				":productos" => $datos["productos"],
				":total" => $datos["total"],
				":totalSinIva" => $datos["totalSinIva"],
				":tipoDte" => $datos["tipoDte"],
				":recintoFiscal" => $datos["recintoFiscal"],
				":regimen" => $datos["regimen"],
				":tipo_regimen" => $datos["tipo_regimen"],
				":cod_incoterms" => $datos["cod_incoterms"],
				":desc_incoterms" => $datos["desc_incoterms"],
				":modoTransporte" => $datos["modoTransporte"],
				":seguro" => $datos["seguro"],
				":flete" => $datos["flete"],
				":idMotorista" => $datos["idMotorista"],
				":condicionOperacion" => $datos["condicionOperacion"],
				":plazo_pago" => $datos["plazo_pago"],
				":periodo_pago" => $datos["periodo_pago"],
				":numeroControl" => $datos["numeroControl"],
				":codigoGeneracion" => $datos["codigoGeneracion"],
				":horEmi" => $datos["horEmi"],
				":fecEmi" => $datos["fecEmi"],
				":gran_contribuyente" => $datos["gran_contribuyente"]
			));

			$consultaStock = $conexion->prepare("SELECT stock FROM inventario WHERE id = :id FOR UPDATE");
			$actualizarStock = $conexion->prepare("UPDATE inventario SET stock = :stock WHERE id = :id");
			foreach($movimientosInventario as $movimiento){
				$consultaStock->execute(array(":id" => $movimiento["idProducto"]));
				$stockActual = $consultaStock->fetchColumn();
				if($stockActual === false){
					throw new Exception("Producto de inventario no encontrado");
				}
				$actualizarStock->execute(array(
					":stock" => (float) $stockActual + (int) $movimiento["cantidad"],
					":id" => $movimiento["idProducto"]
				));
			}

			$conexion->commit();
			return "ok";
		} catch(Throwable $e) {
			if($conexion->inTransaction()){
				$conexion->rollBack();
			}
			error_log("Error al crear evento de retorno: " . $e->getMessage());
			return "error";
		}
	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL CONTINGENCIA
	=============================================*/

	static public function mdlIngresarFacturaContingencia($tabla, $datos) {

		$plazoPago = isset($datos["plazo_pago"]) ? $datos["plazo_pago"] : "";
		$periodoPago = isset($datos["periodo_pago"]) ? $datos["periodo_pago"] : 0;
		$tipoRegimen = isset($datos["tipo_regimen"]) && $datos["tipo_regimen"] !== "" ? $datos["tipo_regimen"] : null;
		$codIncoterms = isset($datos["cod_incoterms"]) && $datos["cod_incoterms"] !== "" ? $datos["cod_incoterms"] : null;
		$descIncoterms = isset($datos["desc_incoterms"]) && $datos["desc_incoterms"] !== "" ? $datos["desc_incoterms"] : null;

		$conexion = Conexion::conectar();
		try {
			$conexion->beginTransaction();
			$datos["numeroControl"] = self::generarNumeroControl($conexion, $datos["tipoDte"]);

        	$stmt = $conexion->prepare("INSERT INTO $tabla(id_cliente, id_vendedor, id_usuario, modo, tipo_contingencia, motivo_contingencia, notaRemision, estado, idFacturaRelacionada, productos, total, totalSinIva, tipoDte, recintoFiscal, regimen, tipo_regimen, cod_incoterms, desc_incoterms, modoTransporte, seguro, flete, idMotorista, condicionOperacion, plazo_pago, periodo_pago, numeroControl, codigoGeneracion, horEmi, fecEmi, gran_contribuyente) VALUES (:id_cliente, :id_vendedor, :id_usuario, :modo, :tipo_contingencia, :motivo_contingencia, :notaRemision, :estado, :idFacturaRelacionada, :productos, :total, :totalSinIva, :tipoDte, :recintoFiscal, :regimen, :tipo_regimen, :cod_incoterms, :desc_incoterms, :modoTransporte, :seguro, :flete, :idMotorista, :condicionOperacion, :plazo_pago, :periodo_pago, :numeroControl, :codigoGeneracion, :horEmi, :fecEmi, :gran_contribuyente)");

        $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
		$stmt->bindParam(":id_vendedor", $datos["id_vendedor"], PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":modo", $datos["modo"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_contingencia", $datos["tipo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":motivo_contingencia", $datos["motivo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":notaRemision", $datos["notaRemision"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt->bindParam(":idFacturaRelacionada", $datos["idFacturaRelacionada"], PDO::PARAM_STR);
        $stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR); // Cambié esto
        $stmt->bindParam(":total", $datos["total"], PDO::PARAM_STR);
		$stmt->bindParam(":totalSinIva", $datos["totalSinIva"], PDO::PARAM_STR);
        $stmt->bindParam(":tipoDte", $datos["tipoDte"], PDO::PARAM_STR);
		$stmt->bindParam(":gran_contribuyente", $datos["gran_contribuyente"], PDO::PARAM_STR);
		
		$stmt->bindParam(":recintoFiscal", $datos["recintoFiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":regimen", $datos["regimen"], PDO::PARAM_STR);
		$stmt->bindValue(":tipo_regimen", $tipoRegimen, $tipoRegimen === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindValue(":cod_incoterms", $codIncoterms, $codIncoterms === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindValue(":desc_incoterms", $descIncoterms, $descIncoterms === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$stmt->bindParam(":modoTransporte", $datos["modoTransporte"], PDO::PARAM_STR);
		$stmt->bindParam(":seguro", $datos["seguro"], PDO::PARAM_STR);
		$stmt->bindParam(":flete", $datos["flete"], PDO::PARAM_STR);
		$stmt->bindParam(":idMotorista", $datos["idMotorista"], PDO::PARAM_INT);

		$stmt->bindParam(":condicionOperacion", $datos["condicionOperacion"], PDO::PARAM_STR);
		$stmt->bindParam(":plazo_pago", $plazoPago, PDO::PARAM_STR);
		$stmt->bindParam(":periodo_pago", $periodoPago, PDO::PARAM_STR);
		$stmt->bindParam(":numeroControl", $datos["numeroControl"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);

	        if(!$stmt->execute()) {
				throw new Exception("No se pudo insertar la factura en contingencia");
	        }

			self::actualizarNumeroControlPorTipo($conexion, $datos["tipoDte"], $datos["numeroControl"]);
			$conexion->commit();
	        return "ok";
		} catch(Throwable $e) {
			if($conexion->inTransaction()){
				$conexion->rollBack();
			}
			error_log("Error al crear factura local en contingencia: " . $e->getMessage());
			return "error";
		}
    }

	/*=============================================
	REGISTRO DE COTIZACION AUTORIZADA
	=============================================*/

	static public function mdlIngresarCotizacion($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_cliente, id_usuario, estado, productos, fecEmi, codigo) VALUES (:id_cliente, :id_usuario, :estado, :productos, :fecEmi, :codigo)");

		$stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
		$stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);		
        $stmt->bindParam(":productos", $datos["productos"], PDO::PARAM_STR); // Cambié esto);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	EDITAR COTIZACION AUTORIZADA
	=============================================*/

	static public function mdlEditarCotizacion($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET productos = :productos, fecEmi = :fecEmi WHERE id = :id");

		$stmt -> bindParam(":productos", $datos["productos"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);
		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

	/*=============================================
	REGISTRO DE EVENTO CONTINGENCIA
	=============================================*/

	static public function mdlIngresarEventoContingencia($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(fecha_inicio, fecha_fin, hora_inicio, hora_fin, tipo_contingencia, motivo_contingencia, ids_facturas, codigoGeneracion) 
		VALUES (:fecha_inicio, :fecha_fin, :hora_inicio, :hora_fin, :tipo_contingencia, :motivo_contingencia, :ids_facturas, :codigoGeneracion)");

        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
		$stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
		$stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);;
		$stmt->bindParam(":tipo_contingencia", $datos["tipo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":motivo_contingencia", $datos["motivo_contingencia"], PDO::PARAM_STR);
		$stmt->bindParam(":ids_facturas", $datos["ids_facturas"], PDO::PARAM_STR);
		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE ANULACION LOCAL
	=============================================*/

	static public function mdlIngresarAnulacion($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(codigoGeneracion, fecEmi, horEmi, facturaRelacionada, motivoAnulacion) VALUES (:codigoGeneracion, :fecEmi, :horEmi, :facturaRelacionada, :motivoAnulacion)");

		$stmt->bindParam(":codigoGeneracion", $datos["codigoGeneracion"], PDO::PARAM_STR);
		$stmt->bindParam(":fecEmi", $datos["fecEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":horEmi", $datos["horEmi"], PDO::PARAM_STR);
		$stmt->bindParam(":facturaRelacionada", $datos["facturaRelacionada"], PDO::PARAM_STR);
		$stmt->bindParam(":motivoAnulacion", $datos["motivoAnulacion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE ELIMINADA NUMERO CONTROL
	=============================================*/

	static public function mdlIngresarEliminada($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_control, codigo_generacion) VALUES (:numero_control, :codigo_generacion)");

		$stmt->bindParam(":numero_control", $datos["numero_control"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo_generacion", $datos["codigo_generacion"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/

	static public function mdlIngresarCompra($tabla, $datos) { 	

        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(fecha, clase_documento, tipo_documento, numero_documento, nit_nrc, nombre_proveedor, compras_internas_exentas, internaciones_exentas_y_no_sujetas, importaciones_exentas_y_no_sujetas, compras_internas_gravadas, internaciones_gravadas_de_bienes, importaciones_gravadas_de_bienes, importaciones_gravadas_de_servicios, credito_fiscal, total_de_compras, dui_del_proveedor, tipo_de_operacion, clasificacion, sector, tipo, anexo) VALUES (:fecha, :clase_documento, :tipo_documento, :numero_documento, :nit_nrc, :nombre_proveedor, :compras_internas_exentas, :internaciones_exentas_y_no_sujetas, :importaciones_exentas_y_no_sujetas, :compras_internas_gravadas, :internaciones_gravadas_de_bienes, :importaciones_gravadas_de_bienes, :importaciones_gravadas_de_servicios, :credito_fiscal, :total_de_compras, :dui_del_proveedor, :tipo_de_operacion, :clasificacion, :sector, :tipo, :anexo)");

		$stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
		$stmt->bindParam(":clase_documento", $datos["clase_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":nit_nrc", $datos["nit_nrc"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre_proveedor", $datos["nombre_proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":compras_internas_exentas", $datos["compras_internas_exentas"], PDO::PARAM_STR);
		$stmt->bindParam(":internaciones_exentas_y_no_sujetas", $datos["internaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_exentas_y_no_sujetas", $datos["importaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);
		$stmt->bindParam(":compras_internas_gravadas", $datos["compras_internas_gravadas"], PDO::PARAM_STR);
		$stmt->bindParam(":internaciones_gravadas_de_bienes", $datos["internaciones_gravadas_de_bienes"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_gravadas_de_bienes", $datos["importaciones_gravadas_de_bienes"], PDO::PARAM_STR);
		$stmt->bindParam(":importaciones_gravadas_de_servicios", $datos["importaciones_gravadas_de_servicios"], PDO::PARAM_STR);
		$stmt->bindParam(":credito_fiscal", $datos["credito_fiscal"], PDO::PARAM_STR);
		$stmt->bindParam(":total_de_compras", $datos["total_de_compras"], PDO::PARAM_STR);
		$stmt->bindParam(":dui_del_proveedor", $datos["dui_del_proveedor"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_de_operacion", $datos["tipo_de_operacion"], PDO::PARAM_STR);
		$stmt->bindParam(":clasificacion", $datos["clasificacion"], PDO::PARAM_STR);
		$stmt->bindParam(":sector", $datos["sector"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt->bindParam(":anexo", $datos["anexo"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";    
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }
	
	/*=============================================
	BORRAR FACTURA
	=============================================*/

	static public function mdlBorrarFactura($tabla, $datos){

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
	ACTUALIZAR FACTURA
	=============================================*/

	static public function mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2){

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

	}/*=============================================
	EDITAR COMPRA
	=============================================*/

	static public function mdlEditarCompra($tabla, $datos){
		
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha = :fecha, clase_documento = :clase_documento, tipo_documento = :tipo_documento, numero_documento = :numero_documento, nit_nrc = :nit_nrc, nombre_proveedor = :nombre_proveedor, compras_internas_exentas = :compras_internas_exentas, internaciones_exentas_y_no_sujetas = :internaciones_exentas_y_no_sujetas, importaciones_exentas_y_no_sujetas = :importaciones_exentas_y_no_sujetas, compras_internas_gravadas = :compras_internas_gravadas, internaciones_gravadas_de_bienes = :internaciones_gravadas_de_bienes, importaciones_gravadas_de_bienes = :importaciones_gravadas_de_bienes, importaciones_gravadas_de_servicios = :importaciones_gravadas_de_servicios, credito_fiscal = :credito_fiscal, total_de_compras = :total_de_compras, dui_del_proveedor = :dui_del_proveedor, tipo_de_operacion = :tipo_de_operacion, clasificacion = :clasificacion, sector = :sector, tipo = :tipo, anexo = :anexo WHERE id = :id");

		$stmt -> bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);  
		$stmt -> bindParam(":clase_documento", $datos["clase_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);  
		$stmt -> bindParam(":nit_nrc", $datos["nit_nrc"], PDO::PARAM_STR);  
		$stmt -> bindParam(":nombre_proveedor", $datos["nombre_proveedor"], PDO::PARAM_STR);  
		$stmt -> bindParam(":compras_internas_exentas", $datos["compras_internas_exentas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":internaciones_exentas_y_no_sujetas", $datos["internaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_exentas_y_no_sujetas", $datos["importaciones_exentas_y_no_sujetas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":compras_internas_gravadas", $datos["compras_internas_gravadas"], PDO::PARAM_STR);  
		$stmt -> bindParam(":internaciones_gravadas_de_bienes", $datos["internaciones_gravadas_de_bienes"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_gravadas_de_bienes", $datos["importaciones_gravadas_de_bienes"], PDO::PARAM_STR);  
		$stmt -> bindParam(":importaciones_gravadas_de_servicios", $datos["importaciones_gravadas_de_servicios"], PDO::PARAM_STR);  
		$stmt -> bindParam(":credito_fiscal", $datos["credito_fiscal"], PDO::PARAM_STR);  
		$stmt -> bindParam(":total_de_compras", $datos["total_de_compras"], PDO::PARAM_STR);  
		$stmt -> bindParam(":dui_del_proveedor", $datos["dui_del_proveedor"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo_de_operacion", $datos["tipo_de_operacion"], PDO::PARAM_STR);  
		$stmt -> bindParam(":clasificacion", $datos["clasificacion"], PDO::PARAM_STR);  
		$stmt -> bindParam(":sector", $datos["sector"], PDO::PARAM_STR);  
		$stmt -> bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);  
		$stmt -> bindParam(":anexo", $datos["anexo"], PDO::PARAM_STR); 

		$stmt -> bindParam(":id", $datos["id"], PDO::PARAM_INT);
	

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}

		$stmt -> close();

		$stmt = null;

	}

}
