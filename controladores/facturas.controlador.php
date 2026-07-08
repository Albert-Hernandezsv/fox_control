<?php
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	use \Firebase\JWT\JWT;
	date_default_timezone_set('America/El_Salvador');

class ControladorFacturas{

	static public function ctrOpcionesExportacion($nombreSelect){

		static $catalogos = null;
		$nombresPermitidos = array("recintoFiscal", "tipo_regimen", "regimen");
		if(!in_array($nombreSelect, $nombresPermitidos, true)){
			return array();
		}

		if($catalogos === null){
			$catalogos = array(
				"recintoFiscal" => array(),
				"tipo_regimen" => array(),
				"regimen" => array()
			);
			$rutaFormulario = __DIR__ . "/../vistas/modulos/crear-factura-exportacion.php";
			if(!file_exists($rutaFormulario)){
				return array();
			}

			$contenido = file_get_contents($rutaFormulario);
			$dom = new DOMDocument();
			$estadoErrores = libxml_use_internal_errors(true);
			$dom->loadHTML('<?xml encoding="UTF-8">' . $contenido);
			libxml_clear_errors();
			libxml_use_internal_errors($estadoErrores);
			$xpath = new DOMXPath($dom);

			foreach($nombresPermitidos as $nombre){
				$opciones = $xpath->query('//select[@name="'.$nombre.'"]/option[@value]');
				foreach($opciones as $opcion){
					$valor = trim($opcion->getAttribute("value"));
					if($valor === ""){
						continue;
					}
					$texto = trim(preg_replace('/\s+/', ' ', $opcion->textContent));
					$catalogos[$nombre][$valor] = $texto;
				}
			}
		}

		return $catalogos[$nombreSelect];
	}

	static public function ctrOpcionesRegimenRetorno(){

		static $opciones = null;
		if($opciones !== null){
			return $opciones;
		}

		$opciones = array();
		$rutaCatalogo = __DIR__ . "/../vistas/modulos/catalogo-regimen-retorno.html";
		if(!file_exists($rutaCatalogo)){
			return $opciones;
		}

		$contenido = file_get_contents($rutaCatalogo);
		$dom = new DOMDocument();
		$estadoErrores = libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8"><select>' . $contenido . '</select>');
		libxml_clear_errors();
		libxml_use_internal_errors($estadoErrores);

		foreach($dom->getElementsByTagName("option") as $opcion){
			$valor = trim($opcion->getAttribute("value"));
			if($valor === ""){
				continue;
			}
			$texto = trim(preg_replace('/\s+/', ' ', $opcion->textContent));
			$opciones[$valor] = $texto;
		}

		return $opciones;
	}

	/*=============================================
	MOSTRAR COTIZACIONES AUTORIZADAS
	=============================================*/

	static public function ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion){

		$tabla = "cotizaciones_autorizadas";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturas($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarCotizacionesAutorizadasFac($item, $valor, $orden, $optimizacion){

		$tabla = "cotizaciones_autorizadas";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturasFac($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarCotizacionesAutorizadasPaginadas($limite, $offset){

		return ModeloFacturas::mdlMostrarCotizacionesAutorizadasPaginadas($limite, $offset);
	}

	static public function ctrContarCotizacionesAutorizadas(){

		return ModeloFacturas::mdlContarCotizacionesAutorizadas();
	}

	static public function ctrMostrarCotizacionesDisponiblesPaginadas($limite, $offset){

		return ModeloFacturas::mdlMostrarCotizacionesDisponiblesPaginadas($limite, $offset);
	}

	static public function ctrContarCotizacionesDisponibles(){

		return ModeloFacturas::mdlContarCotizacionesDisponibles();
	}

	/*=============================================
	MOSTRAR FACTURAS
	=============================================*/

	static public function ctrMostrarFacturas($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturas($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarFacturasPaginadas($idUsuario, $limite, $offset){

		return ModeloFacturas::mdlMostrarFacturasPaginadas($idUsuario, $limite, $offset);
	}

	static public function ctrContarFacturasPaginadas($idUsuario){

		return ModeloFacturas::mdlContarFacturasPaginadas($idUsuario);
	}

	static public function ctrMostrarNotasContablesPaginadas($limite, $offset){

		return ModeloFacturas::mdlMostrarNotasContablesPaginadas($limite, $offset);
	}

	static public function ctrContarNotasContables(){

		return ModeloFacturas::mdlContarNotasContables();
	}

	static public function ctrMostrarAnulacionesPaginadas($limite, $offset){

		return ModeloFacturas::mdlMostrarAnulacionesPaginadas($limite, $offset);
	}

	static public function ctrContarAnulaciones(){

		return ModeloFacturas::mdlContarAnulaciones();
	}

	static public function ctrMostrarEliminadasPaginadas($limite, $offset){

		return ModeloFacturas::mdlMostrarEliminadasPaginadas($limite, $offset);
	}

	static public function ctrContarEliminadas(){

		return ModeloFacturas::mdlContarEliminadas();
	}

	static public function ctrMostrarFacturasVarias($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasVarias($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarFacturasDash($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasDash($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarFacturasAsc($item, $valor, $orden, $optimizacion){

		$tabla = "facturas_locales";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarFacturasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarFacturasAsc($tabla, $item, $valor, $orden);
		}		

		return $respuesta;
	}

	static public function ctrMostrarFacturasVentas($fechaInicio, $fechaFin){

		$tabla = "facturas_locales";

		$respuesta = ModeloFacturas::MdlMostrarFacturasVentas($tabla, $fechaInicio, $fechaFin);

		return $respuesta;
	}/*=============================================
	MOSTRAR EVENTOS CONTINGENCAS
	=============================================*/

	static public function ctrMostrarEventosContingencias($item, $valor, $orden){

		$tabla = "contingencias";

		$respuesta = ModeloFacturas::MdlMostrarEventosContingencias($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR ANULACIONES
	=============================================*/

	static public function ctrMostrarAnulaciones($item, $valor, $orden){

		$tabla = "anuladas";

		$respuesta = ModeloFacturas::MdlMostrarFacturas($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR COMPRAS
	=============================================*/

	static public function ctrMostrarCompras($item, $valor, $orden, $optimizacion){

		$tabla = "compras";

		if($optimizacion == "si"){
			$respuesta = ModeloFacturas::MdlMostrarComprasOptimizadas($tabla, $item, $valor, $orden);
		} else {
			$respuesta = ModeloFacturas::MdlMostrarCompras($tabla, $item, $valor, $orden);
		}

		return $respuesta;
	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL
	=============================================*/
	static public function ctrCrearEventoRetorno(){

		if(!isset($_POST["crearEventoRetorno"])){
			return;
		}

		$error = "";
		$idFacturaOriginal = isset($_POST["idFacturaRelacionada"]) ? (int) $_POST["idFacturaRelacionada"] : 0;
		$facturaOriginal = self::ctrMostrarFacturas("id", $idFacturaOriginal, "id", "no");

		if(!$facturaOriginal || !in_array($facturaOriginal["tipoDte"], array("01", "11", "14"), true)){
			$error = "La factura seleccionada no permite eventos de retorno.";
		}

		if($error === ""){
			$fechaLimite = new DateTime($facturaOriginal["fecEmi"] . " " . (isset($facturaOriginal["horEmi"]) ? $facturaOriginal["horEmi"] : "00:00:00"));
			$fechaLimite->modify("+3 months")->setTime(23, 59, 59);
			if(new DateTime() > $fechaLimite){
				$error = "El plazo de tres meses para crear el evento de retorno ya finalizo.";
			}
		}

		$fleteRetorno = 0.0;
		$seguroRetorno = 0.0;
		$recintoFiscalRetorno = "";
		$tipoRegimenRetorno = null;
		$regimenRetorno = "";
		if($error === "" && $facturaOriginal["tipoDte"] === "11"){
			$fletePost = isset($_POST["fleteRetorno"]) ? trim((string) $_POST["fleteRetorno"]) : "";
			$seguroPost = isset($_POST["seguroRetorno"]) ? trim((string) $_POST["seguroRetorno"]) : "";
			$recintoFiscalPost = isset($_POST["recintoFiscal"]) ? trim((string) $_POST["recintoFiscal"]) : "";
			$tipoRegimenPost = isset($_POST["tipo_regimen"]) ? trim((string) $_POST["tipo_regimen"]) : "";
			$regimenPost = isset($_POST["regimen"]) ? trim((string) $_POST["regimen"]) : "";
			$recintosPermitidos = self::ctrOpcionesExportacion("recintoFiscal");
			$tiposRegimenPermitidos = self::ctrOpcionesExportacion("tipo_regimen");
			$regimenesPermitidos = self::ctrOpcionesRegimenRetorno();

			if(!array_key_exists($recintoFiscalPost, $recintosPermitidos) || !array_key_exists($tipoRegimenPost, $tiposRegimenPermitidos) || !array_key_exists($regimenPost, $regimenesPermitidos)){
				$error = "Debe seleccionar un recinto fiscal, tipo de regimen y regimen validos.";
			} elseif($fletePost === "" || $seguroPost === "" || !is_numeric($fletePost) || !is_numeric($seguroPost) || (float) $fletePost < 0 || (float) $seguroPost < 0){
				$error = "Debe ingresar valores validos para el flete y el seguro.";
			} else {
				$fleteRetorno = round((float) $fletePost, 2);
				$seguroRetorno = round((float) $seguroPost, 2);
				$recintoFiscalRetorno = $recintoFiscalPost;
				$tipoRegimenRetorno = $tipoRegimenPost;
				$regimenRetorno = $regimenPost;
			}
		}

		$productosOriginales = $error === "" ? json_decode($facturaOriginal["productos"], true) : array();
		if($error === "" && !is_array($productosOriginales)){
			$error = "Los productos de la factura original no son validos.";
		}

		$cantidadesRetornadas = array();
		if($error === ""){
			$eventosAnteriores = self::ctrMostrarFacturasVarias("idFacturaRelacionada", $idFacturaOriginal, "id", "no");
			foreach($eventosAnteriores as $eventoAnterior){
				if($eventoAnterior["tipoDte"] !== "18"){
					continue;
				}
				$productosEvento = json_decode($eventoAnterior["productos"], true);
				if(!is_array($productosEvento)){
					continue;
				}
				foreach($productosEvento as $productoEvento){
					$idProducto = isset($productoEvento["idProducto"]) ? (string) $productoEvento["idProducto"] : "";
					$cantidadesRetornadas[$idProducto] = isset($cantidadesRetornadas[$idProducto]) ? $cantidadesRetornadas[$idProducto] + (int) $productoEvento["cantidad"] : (int) $productoEvento["cantidad"];
				}
			}
		}

		$productosRetorno = array();
		$movimientosInventario = array();
		$total = 0.0;
		$totalSinIva = 0.0;
		$cantidadesPost = isset($_POST["cantidadRetorno"]) && is_array($_POST["cantidadRetorno"]) ? $_POST["cantidadRetorno"] : array();

		if($error === ""){
			foreach($productosOriginales as $indice => $productoOriginal){
				$cantidadTexto = isset($cantidadesPost[$indice]) ? trim((string) $cantidadesPost[$indice]) : "0";
				if($cantidadTexto === ""){
					$cantidadTexto = "0";
				}
				if(!ctype_digit($cantidadTexto)){
					$error = "Las cantidades a retornar deben ser numeros enteros.";
					break;
				}

				$cantidad = (int) $cantidadTexto;
				$idProducto = isset($productoOriginal["idProducto"]) ? (string) $productoOriginal["idProducto"] : "";
				$cantidadOriginal = isset($productoOriginal["cantidad"]) ? (int) $productoOriginal["cantidad"] : 0;
				$yaRetornado = isset($cantidadesRetornadas[$idProducto]) ? (int) $cantidadesRetornadas[$idProducto] : 0;
				$disponible = max(0, $cantidadOriginal - $yaRetornado);

				if($cantidad > $disponible){
					$error = "Una cantidad supera lo disponible para retornar.";
					break;
				}
				if($cantidad === 0){
					continue;
				}

				$precioSinIva = isset($productoOriginal["precioSinImpuestos"]) ? (float) $productoOriginal["precioSinImpuestos"] : 0.0;
				$precioConIva = isset($productoOriginal["precioConIva"]) ? (float) $productoOriginal["precioConIva"] : $precioSinIva;
				$descuentoSinIva = isset($productoOriginal["descuento"]) ? (float) $productoOriginal["descuento"] : 0.0;
				$descuentoConIva = isset($productoOriginal["descuentoConIva"]) ? (float) $productoOriginal["descuentoConIva"] : $descuentoSinIva;

				$productoRetorno = $productoOriginal;
				$productoRetorno["cantidad"] = $cantidad;
				$productoRetorno["totalProducto"] = round(($precioConIva - $descuentoConIva) * $cantidad, 2);
				$productosRetorno[] = $productoRetorno;
				$totalSinIva += ($precioSinIva - $descuentoSinIva) * $cantidad;
				$total += ($precioConIva - $descuentoConIva) * $cantidad;
				$movimientosInventario[] = array("idProducto" => (int) $idProducto, "cantidad" => $cantidad);
			}
		}

		if($error === "" && count($productosRetorno) === 0){
			$error = "Debe seleccionar al menos una cantidad para retornar.";
		}

		if($error !== ""){
			echo '<script>swal({type:"error",title:"Evento de retorno",text:'.json_encode($error).',confirmButtonText:"Cerrar"});</script>';
			return;
		}

		$numeroControl = "No aplica";
		$hex = strtoupper(bin2hex(random_bytes(16)));
		$codigoGeneracion = substr($hex, 0, 8)."-".substr($hex, 8, 4)."-".substr($hex, 12, 4)."-".substr($hex, 16, 4)."-".substr($hex, 20, 12);

		$datos = array(
			"id_cliente" => $facturaOriginal["id_cliente"],
			"id_vendedor" => $facturaOriginal["id_vendedor"],
			"id_usuario" => $_SESSION["id"],
			"modo" => "Normal",
			"notaRemision" => "",
			"estado" => "Activa",
			"idFacturaRelacionada" => $idFacturaOriginal,
			"productos" => json_encode($productosRetorno, JSON_UNESCAPED_UNICODE),
			"total" => round($total, 2),
			"totalSinIva" => round($totalSinIva, 2),
			"tipoDte" => "18",
			"recintoFiscal" => $recintoFiscalRetorno,
			"regimen" => $regimenRetorno,
			"tipo_regimen" => $tipoRegimenRetorno,
			"cod_incoterms" => null,
			"desc_incoterms" => null,
			"modoTransporte" => "",
			"seguro" => $seguroRetorno,
			"flete" => $fleteRetorno,
			"idMotorista" => 0,
			"condicionOperacion" => $facturaOriginal["condicionOperacion"],
			"plazo_pago" => isset($facturaOriginal["plazo_pago"]) ? $facturaOriginal["plazo_pago"] : "",
			"periodo_pago" => isset($facturaOriginal["periodo_pago"]) ? $facturaOriginal["periodo_pago"] : 0,
			"numeroControl" => $numeroControl,
			"codigoGeneracion" => $codigoGeneracion,
			"horEmi" => date("H:i:s"),
			"fecEmi" => date("Y-m-d"),
			"gran_contribuyente" => isset($facturaOriginal["gran_contribuyente"]) ? $facturaOriginal["gran_contribuyente"] : "No"
		);

		$respuesta = ModeloFacturas::mdlIngresarEventoRetorno("facturas_locales", $datos, $movimientosInventario);
		if($respuesta === "ok"){
			echo '<script>swal({type:"success",title:"Evento de retorno creado",confirmButtonText:"Cerrar"}).then(function(){window.location="facturacion";});</script>';
		} else {
			echo '<script>swal({type:"error",title:"No se pudo crear el evento de retorno",confirmButtonText:"Cerrar"});</script>';
		}
	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL
	=============================================*/

	static public function ctrCrearFactura(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = isset($empresarial["numeroControlGeneral"]) ? $empresarial["numeroControlGeneral"] : "DTE-00-S001P001-000000000000000";

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$tipoRegimen = "";
				$codIncoterms = "";
				$descIncoterms = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$tipoRegimen = isset($_POST["tipo_regimen"]) ? $_POST["tipo_regimen"] : "";
					$tiposRegimenPermitidos = array("EX-1", "EX-2", "EX-3", "TA-1");

					if(!in_array($tipoRegimen, $tiposRegimenPermitidos, true)){
						echo json_encode("error_tipo_regimen");
						return;
					}

					$incotermsPermitidos = array(
						"01" => "EXW-En fabrica",
						"02" => "FCA-Libre transportista",
						"03" => "C PT -Transporte pagado hasta",
						"04" => "CIP-Transporte y seguro pagado hasta",
						"05" => "DAP-Entrega en el lugar",
						"06" => "DPU-Entregado en el lugar descargado",
						"07" => "DDP-Entrega con impuestos pagados",
						"08" => "FAS-Libre al costado del buque",
						"09" => "FOB-Libre a bordo",
						"10" => "C FR-Costo y flete",
						"11" => "CIF- Costo seguro y flete"
					);
					$codIncoterms = isset($_POST["cod_incoterms"]) ? $_POST["cod_incoterms"] : "";

					if($codIncoterms !== "" && !array_key_exists($codIncoterms, $incotermsPermitidos)){
						echo json_encode("error_incoterms");
						return;
					}

					if($codIncoterms !== ""){
						$descIncoterms = $incotermsPermitidos[$codIncoterms];
					}

					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";
				$plazoPago = "";
				$periodoPago = 0;

				if($_POST["condicionOperacion"] === "2" && $_POST["tipoDte"] !== "05" && $_POST["tipoDte"] !== "06"){
					$plazosPermitidos = array("01", "02", "03");
					$plazoPago = isset($_POST["plazo_pago"]) ? $_POST["plazo_pago"] : "";
					$periodoPagoPost = isset($_POST["periodo_pago"]) ? trim((string) $_POST["periodo_pago"]) : "";

					if(!in_array($plazoPago, $plazosPermitidos, true) || !ctype_digit($periodoPagoPost) || (int) $periodoPagoPost <= 0){
						echo json_encode("error_credito");
						return;
					}

					$periodoPago = (int) $periodoPagoPost;
				}

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "tipo_regimen" => $tipoRegimen,
							   "cod_incoterms" => $codIncoterms,
							   "desc_incoterms" => $descIncoterms,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "plazo_pago" => $plazoPago,
							   "periodo_pago" => $periodoPago,
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE FACTURA LOCAL CONTINGENCIA
	=============================================*/

	static public function ctrCrearFacturaContingencia(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = isset($empresarial["numeroControlGeneral"]) ? $empresarial["numeroControlGeneral"] : "DTE-00-S001P001-000000000000000";

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';
				

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$tipoRegimen = "";
				$codIncoterms = "";
				$descIncoterms = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$tipoRegimen = isset($_POST["tipo_regimen"]) ? $_POST["tipo_regimen"] : "";
					$tiposRegimenPermitidos = array("EX-1", "EX-2", "EX-3", "TA-1");

					if(!in_array($tipoRegimen, $tiposRegimenPermitidos, true)){
						echo json_encode("error_tipo_regimen");
						return;
					}

					$incotermsPermitidos = array(
						"01" => "EXW-En fabrica",
						"02" => "FCA-Libre transportista",
						"03" => "C PT -Transporte pagado hasta",
						"04" => "CIP-Transporte y seguro pagado hasta",
						"05" => "DAP-Entrega en el lugar",
						"06" => "DPU-Entregado en el lugar descargado",
						"07" => "DDP-Entrega con impuestos pagados",
						"08" => "FAS-Libre al costado del buque",
						"09" => "FOB-Libre a bordo",
						"10" => "C FR-Costo y flete",
						"11" => "CIF- Costo seguro y flete"
					);
					$codIncoterms = isset($_POST["cod_incoterms"]) ? $_POST["cod_incoterms"] : "";

					if($codIncoterms !== "" && !array_key_exists($codIncoterms, $incotermsPermitidos)){
						echo json_encode("error_incoterms");
						return;
					}

					if($codIncoterms !== ""){
						$descIncoterms = $incotermsPermitidos[$codIncoterms];
					}

					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "tipo_regimen" => $tipoRegimen,
							   "cod_incoterms" => $codIncoterms,
							   "desc_incoterms" => $descIncoterms,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Contingencia",
							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "tipo_contingencia" => $_POST["tipoContingencia"],
							   "motivo_contingencia" => $_POST["motivoContingencia"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFacturaContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFacturaContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion-contingencia";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE EVENTO DE CONTINGENCIA
	=============================================*/

	static public function ctrCrearEventoContingencia(){

		if(isset($_POST["nuevaFechaInicio"])){

			if(isset($_POST["nuevaFechaInicio"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = isset($empresarial["numeroControlGeneral"]) ? $empresarial["numeroControlGeneral"] : "DTE-00-S001P001-000000000000000";

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = '';
				$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				for ($i = 0; $i < 8; $i++) {
					$parteAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
				}

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				
				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$ids_facturas = $_POST['ids_facturas'] ?? [];
				$ids_facturas_json = json_encode($ids_facturas); // Convertir a JSON

				$datos = array("fecha_inicio" => $_POST["nuevaFechaInicio"],
							   "fecha_fin" => $_POST["nuevaFechaFin"],
							   "hora_inicio" => $_POST["nuevaHoraInicio"],
							   "hora_fin" => $_POST["nuevaHoraFin"],
							   "tipo_contingencia" => $_POST["tipoContingencia"],
							   "motivo_contingencia" => $_POST["motivoContingencia"],
							   "ids_facturas" => $ids_facturas_json,
								"codigoGeneracion" => $codigoGeneracion);
				
					$tabla = "contingencias";

					$respuesta = ModeloFacturas::mdlIngresarEventoContingencia($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡El evento ha sido creado correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "facturacion-contingencia";
	
							}
	
						});
					
	
						</script>';
	
	
					}

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡El evento no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion-contingencia";

						}

					});
				

				</script>';

			}


		}


	}
	
	/*=============================================
	REGISTRO DE NOTA DE REMISION EN FACTURA LOCAL
	=============================================*/

	static public function ctrCrearNotaRemision(){

		if(isset($_GET["idFacturaNotaRemision"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_GET["idFacturaNotaRemision"])){

				$item = "id";
				$orden = "id";
				$valor = $_GET["idFacturaNotaRemision"];
				$optimizacion = "no";

				$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = isset($empresarial["numeroControlGeneral"]) ? $empresarial["numeroControlGeneral"] : "DTE-00-S001P001-000000000000000";

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";

				// Construir el nuevo número de control
				$numeroControl = 'DTE-04-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($facturaOriginal["tipoDte"] == "11"){ // Exportación

					$recintoFiscal = $facturaOriginal["recintoFiscal"];
					$regimen = $facturaOriginal["regimen"];
					$modoTransporte = $facturaOriginal["modoTransporte"];
					$seguro = $facturaOriginal["seguro"];
					$flete = $facturaOriginal["flete"];
					$idMotorista = $facturaOriginal["idMotorista"];
				}


				$idFacturaRelacionada = "";

				if($facturaOriginal["tipoDte"] == "05"){ // Nota de crédito
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
				}

				if($facturaOriginal["tipoDte"] == "06"){ // Nota de débito
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
				}
				$estado = "Activa";
				$notaRemi = "";
				if($facturaOriginal["tipoDte"] == "04"){ // Nota de remisión
					$idFacturaRelacionada = $facturaOriginal["idFacturaRelacionada"];
					$notaRemi = "si";
				}
			
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$datos = array("id_cliente" => $facturaOriginal["id_cliente"],
								"id_vendedor" => $_SESSION["id"],
								"id_usuario" => $_SESSION["id"],
							   "productos" => $facturaOriginal["productos"],
							   "total" => $facturaOriginal["total"],
							   "totalSinIva" => $facturaOriginal["totalSinIva"],
							   "tipoDte" => "04",

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $facturaOriginal["recintoFiscal"],
							   "regimen" => $facturaOriginal["regimen"],
							   "modoTransporte" => $facturaOriginal["modoTransporte"],
							   "seguro" => $facturaOriginal["seguro"],
							   "flete" => $facturaOriginal["flete"],
							   "idMotorista" =>$facturaOriginal["idMotorista"],

							   "idFacturaRelacionada" => $facturaOriginal["id"],

							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "condicionOperacion" => $facturaOriginal["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);


					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						$tabla = "facturas_locales";
						$item1 = "notaRemision";
						$valor1 = "si";
						$item2 = "id";
						$valor2 = $facturaOriginal["id"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "facturacion";
	
							}
	
						});
					
	
						</script>';
	
	
					}	
				
			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE NOTA DE REMISION EN FACTURA MANUAL
	=============================================*/

	static public function ctrNotaRemisionManual(){

		if(isset($_POST["nuevoClienteFactura"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoClienteFactura"])){

				// Obtener el número de control general actual
				$item = "id";
				$valor = "1";
				$orden = "id";
				$empresarial = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

				// Obtener el valor actual de numeroControlGeneral
				$numeroControlGeneral = isset($empresarial["numeroControlGeneral"]) ? $empresarial["numeroControlGeneral"] : "DTE-00-S001P001-000000000000000";

				// Generar la parte aleatoria de 8 caracteres (A-Z, 0-9)
				$parteAleatoria = 'S001P001';

				// Extraer y aumentar el número secuencial de 15 dígitos
				$parteNumericaActual = substr($numeroControlGeneral, -15); // Últimos 15 dígitos
				$parteNumericaIncrementada = str_pad((int)$parteNumericaActual + 1, 15, '0', STR_PAD_LEFT);

				// Construir el nuevo número de control
				$numeroControl = "";
				
				if($_POST["tipoDte"] == "01"){ // Factura
					// Construir el nuevo número de control
					$numeroControl = 'DTE-01-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}
				if($_POST["tipoDte"] == "03"){ // CCF
					// Construir el nuevo número de control
					$numeroControl = 'DTE-03-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idMotorista = $_POST["idMotorista"];
				}
				
				$recintoFiscal = "";
				$regimen = "";
				$modoTransporte = "";
				$seguro = 0.0;
				$flete = 0.0;
				$idMotorista = "";

				if($_POST["tipoDte"] == "04"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-04-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "11"){ // Exportación
					// Construir el nuevo número de control
					$numeroControl = 'DTE-11-' . $parteAleatoria . '-' . $parteNumericaIncrementada;

					$recintoFiscal = $_POST["recintoFiscal"];
					$regimen = $_POST["regimen"];
					$modoTransporte = $_POST["modoTransporte"];
					$seguro = $_POST["seguro"];
					$flete = $_POST["flete"];
					$idMotorista = $_POST["idMotorista"];
				}

				if($_POST["tipoDte"] == "14"){ // Sujeto excluido
					// Construir el nuevo número de control
					$numeroControl = 'DTE-14-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
				}

				$idFacturaRelacionada = "";

				if($_POST["tipoDte"] == "05"){ // Nota de crédito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-05-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}

				if($_POST["tipoDte"] == "06"){ // Nota de débito
					// Construir el nuevo número de control
					$numeroControl = 'DTE-06-' . $parteAleatoria . '-' . $parteNumericaIncrementada;
					$idFacturaRelacionada = $_POST["idFacturaRelacionada"];
				}
			
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			
				$notaRemi = "";
				$estado = "Activa";

				$granContribuyente = "No";
				if($_POST["granContribuyente"]){
					$granContribuyente = $_POST["granContribuyente"];
				}

				$terminoVentaCif = "";
				$terminoVentaFob = "";

				if(isset($_POST["terminoVentaCif"])){
					$terminoVentaCif = $_POST["terminoVentaCif"];
				}
				if(isset($_POST["terminoVentaFob"])){
					$terminoVentaFob = $_POST["terminoVentaFob"];
				}


				$datos = array("id_cliente" => $_POST["nuevoClienteFactura"],
							   "productos" => $_POST["productos"],
							   "total" => $_POST["nuevoTotalFactura"],
							   "totalSinIva" => $_POST["nuevoTotalFacturaSin"],
							   "tipoDte" => $_POST["tipoDte"],

							   "gran_contribuyente" => $granContribuyente,

							   "recintoFiscal" => $recintoFiscal,
							   "regimen" => $regimen,
							   "modoTransporte" => $modoTransporte,
							   "seguro" => $seguro,
							   "flete" => $flete,
							   "idMotorista" => $idMotorista,

							   "idFacturaRelacionada" => $idFacturaRelacionada,
							   "notaRemision" => $notaRemi,
							   "estado" => $estado,

							   "modo" => "Normal",

							   "id_vendedor" => $_POST["nuevoVendedorId"],
							   "id_usuario" => $_POST["nuevoFacturadorId"],

							   "condicionOperacion" => $_POST["condicionOperacion"],
							   "numeroControl" => $numeroControl,
								"codigoGeneracion" => $codigoGeneracion,
								"horEmi" => $horEmi,
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				if($_POST["tipoDte"] == "05" || $_POST["tipoDte"] == "06"){
					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){
	
						echo '<script>
	
						swal({
	
							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
	
						}).then(function(result){
	
							if(result.value){
							
								window.location = "contabilidad";
	
							}
	
						});
					
	
						</script>';
	
	
					}
						
				} else {
					foreach ($productos as $producto) {

						// DESCARGAR STOCK FACTURADO
						$item = "id";
						$valor = $producto["idProducto"];
				
						$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
				
						$stockActual = $productoTraido["stock"];
						$stockNuevo = $stockActual - $producto["cantidad"];
				
						$tabla = "inventario";
						$item1 = "stock";
						$valor1 = $stockNuevo;
						$item2 = "id";
						$valor2 = $producto["idProducto"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						if($producto["peso"]){
							$pesoNuevo = $producto["peso"];

							$tabla = "inventario";
							$item1 = "peso";
							$valor1 = $pesoNuevo;
							$item2 = "id";
							$valor2 = $producto["idProducto"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}
						
					}

					$tabla = "facturas_locales";

					$respuesta = ModeloFacturas::mdlIngresarFactura($tabla, $datos);
					echo json_encode($respuesta);
				
					if($respuesta == "ok"){

						echo '<script>

						swal({

							type: "success",
							title: "¡La factura local ha sido creada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "facturacion";

							}

						});
					

						</script>';


					}
				}
				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGISTRO DE COTIZACION AUTORIZADA
	=============================================*/

	static public function ctrCrearCotizacion(){

		if(isset($_POST["productos"])){

			if(isset($_POST["productos"])){
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD


				$datos = array("id_cliente" => $_POST["nuevoCliente"],
								"codigo" => $_POST["identificadorCotizacion"],
							   "productos" => $_POST["productos"],
							   "estado" => "Bodega", //bodega, //facturacion, //facturada
							   "id_usuario" => $_SESSION["id"],
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				$tabla = "cotizaciones_autorizadas";

				$respuesta = ModeloFacturas::mdlIngresarCotizacion($tabla, $datos);
				echo json_encode($respuesta);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La cotización autorizada ha sido creada correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

					</script>';


				}
						

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La cotización no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	EDITAR COTIZACION AUTORIZADA
	=============================================*/

	static public function ctrEditarCotizacionAutorizada(){

		if(isset($_POST["productos"])){

			if(isset($_POST["productos"])){
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD


				$datos = array("id" => $_POST["idCotizacion"],
							   "productos" => $_POST["productos"],
								"fecEmi" => $fecEmi);

				

				$productos = json_decode($_POST["productos"], true);

				$tabla = "cotizaciones_autorizadas";

				$item = "id";
				$valor = $_POST["idCotizacion"];
				$orden = "fecEmi";
				$optimizacion = "no";
				

				$cotizacion = ControladorFacturas::ctrMostrarCotizacionesAutorizadas($item, $valor, $orden, $optimizacion);
				if($cotizacion["estado"] == "Bodega"){
					$respuesta = ModeloFacturas::mdlEditarCotizacion($tabla, $datos);
					echo json_encode($respuesta);

				} else {
					$respuesta = "no";
				}
				
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La cotización autorizada ha sido editada correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

					</script>';


				} else {
					echo '<script>

					swal({

						type: "error",
						title: "¡La cotización no se pudo editar!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

				</script>';
				}
						

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La cotización no se pudo editar!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	BORRAR COTIZACION AUTORIZADA
	=============================================*/

	static public function ctrBorrarCotizacionAutorizada(){

		if(isset($_GET["idCotizacionAutorizadaEliminar"])){

			$tabla ="cotizaciones_autorizadas";
			$datos = $_GET["idCotizacionAutorizadaEliminar"];

			$respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "La cotización ha sido borrada correctamente",
					  showConfirmButton: true,
					  confirmButtonText: "Cerrar",
					  closeOnConfirm: false
					  }).then(function(result) {
								if (result.value) {

								window.location = "cotizaciones-autorizadas";

								}
							})

				</script>';

			}		

		}

	}

	/*=============================================
	PASAR COTIZACION AUTORIZADA A FACTURACION
	=============================================*/

	static public function ctrPasarCotizacionAutorizada(){

		if(isset($_GET["idCotizacionAutorizadaPasar"])){

			if(isset($_GET["idCotizacionAutorizadaPasar"])){
				$tabla = "cotizacIones_autorizadas";
				$item1 = "estado";
				$valor1 = "Facturacion";
				$item2 = "id";
				$valor2 = $_GET["idCotizacionAutorizadaPasar"];
				

				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);;
				echo json_encode($respuesta);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La cotización autorizada ha sido pasada correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

					</script>';


				}
						

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La cotización no se pudo pasar!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "cotizaciones-autorizadas";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	REGRESAR COTIZACION AUTORIZADA A BODEGA
	=============================================*/

	static public function ctrRegresarCotizacionAutorizada(){

		if(isset($_GET["idCotizacionAutorizadaRegresar"])){

			if(isset($_GET["idCotizacionAutorizadaRegresar"])){
				$tabla = "cotizacIones_autorizadas";
				$item1 = "estado";
				$valor1 = "Bodega";
				$item2 = "id";
				$valor2 = $_GET["idCotizacionAutorizadaRegresar"];
				

				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);;
				echo json_encode($respuesta);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡La cotización autorizada ha sido regresada correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

					</script>';


				}
						

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La cotización no se pudo regresar!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}


	}


	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/

	static public function ctrCrearCompra(){

		if(isset($_POST["numero_documento"])){

			if(isset($_POST["numero_documento"])){

				// Obtener los datos del formulario
				$fechas = $_POST['fecha'];
				$clases_documento = $_POST['clase_documento'];
				$tipos_documento = $_POST['tipo_documento'];
				$numeros_documento = $_POST['numero_documento'];
				$nits_nrc = $_POST["nit_nrc"];
				$proveedores = $_POST['nombre_proveedor'];
				$comprass_internas_exentas = $_POST["compras_internas_exentas"];
				$internacioness_exentas_y_no_sujetas = $_POST["internaciones_exentas_y_no_sujetas"];
				$importacioness_exentas_y_no_sujetas = $_POST["importaciones_exentas_y_no_sujetas"];
				$comprass_internas_gravadas = $_POST["compras_internas_gravadas"];
				$internacioness_gravadas_de_bienes = $_POST["internaciones_gravadas_de_bienes"];
				$importacioness_gravadas_de_bienes = $_POST["importaciones_gravadas_de_bienes"];
				$importacioness_gravadas_de_servicios = $_POST["importaciones_gravadas_de_servicios"];
				$creditos_fiscal = $_POST["credito_fiscal"];
				$totals_de_compras = $_POST["total_de_compras"];
				$duis_del_proveedor = $_POST["dui_del_proveedor"];
				$tipos_de_operacion = $_POST["tipo_de_operacion"];
				$clasificacions = $_POST["clasificacion"];
				$sectors = $_POST["sector"];
				$tipos = $_POST["tipo"];
				$anexos = $_POST["anexo"];

				// Recorrer los registros y guardarlos uno por uno
				for ($i = 0; $i < count($numeros_documento); $i++) {
					$datos = array("fecha" => $fechas[$i],
								"clase_documento" => $clases_documento[$i],
								"tipo_documento" => $tipos_documento[$i],
								"numero_documento" => $numeros_documento[$i],
								"nit_nrc" => $nits_nrc[$i],
								"nombre_proveedor" => $proveedores[$i],
								"compras_internas_exentas" => $comprass_internas_exentas[$i],
								"internaciones_exentas_y_no_sujetas" => $internacioness_exentas_y_no_sujetas[$i],
								"importaciones_exentas_y_no_sujetas" => $importacioness_exentas_y_no_sujetas[$i],
								"compras_internas_gravadas" => $comprass_internas_gravadas[$i],
								"internaciones_gravadas_de_bienes" => $internacioness_gravadas_de_bienes[$i],
								"importaciones_gravadas_de_bienes" => $importacioness_gravadas_de_bienes[$i],
								"importaciones_gravadas_de_servicios" => $importacioness_gravadas_de_servicios[$i],
								"credito_fiscal" => $creditos_fiscal[$i],
								"total_de_compras" => $totals_de_compras[$i],
								"dui_del_proveedor" => $duis_del_proveedor[$i],
								"tipo_de_operacion" => $tipos_de_operacion[$i],
								"clasificacion" => $clasificacions[$i],
								"sector" => $sectors[$i],
								"tipo" => $tipos[$i],
								"anexo" => $anexos[$i]);

					$tabla = "compras";

					$respuesta = ModeloFacturas::mdlIngresarCompra($tabla, $datos);
				}

						echo '<script>

						swal({

							type: "success",
							title: "¡Las compras han sido creadas correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"

						}).then(function(result){

							if(result.value){
							
								window.location = "compras";

							}

						});
					

						</script>';

				
				


			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡Las compras no se pudieron crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "compras";

						}

					});
				

				</script>';

			}


		}


	}

	/*=============================================
	BORRAR FACTURA
	=============================================*/

	static public function ctrBorrarFactura(){

		if(isset($_GET["idFacturaEliminar"])){

			/*=============================================
			REGRESAR EL STOCK
			=============================================*/

			$item = "id";
			$valor = $_GET["idFacturaEliminar"];
			$orden = "fecEmi";
			$optimizacion = "no";

			$facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

			$productos = json_decode($facturas["productos"], true);

			if($facturas["tipoDte"] == "18"){
				$movimientosInventario = array();
				foreach($productos as $producto){
					$movimientosInventario[] = array(
						"idProducto" => (int) $producto["idProducto"],
						"cantidad" => (int) $producto["cantidad"]
					);
				}

				$respuesta = ModeloFacturas::mdlBorrarEventoRetorno((int) $_GET["idFacturaEliminar"], $movimientosInventario);
				if($respuesta == "ok"){
					ModeloFacturas::mdlIngresarEliminada("eliminadas", array(
						"numero_control" => $facturas["numeroControl"],
						"codigo_generacion" => $facturas["codigoGeneracion"]
					));
					echo '<script>swal({type:"success",title:"El evento de retorno fue eliminado y el inventario fue ajustado",confirmButtonText:"Cerrar"}).then(function(){window.location="facturacion";});</script>';
				} elseif($respuesta == "stock_insuficiente") {
					echo '<script>swal({type:"error",title:"No se puede eliminar",text:"El inventario actual no permite revertir las cantidades retornadas.",confirmButtonText:"Cerrar"});</script>';
				} else {
					echo '<script>swal({type:"error",title:"No se pudo eliminar el evento de retorno",confirmButtonText:"Cerrar"});</script>';
				}
			} elseif($facturas["tipoDte"] == "01" || $facturas["tipoDte"] == "03" || $facturas["tipoDte"] == "11" || $facturas["tipoDte"] == "14"){

				foreach ($productos as $producto) {

					// DESCARGAR STOCK FACTURADO
					$item = "id";
					$valor = $producto["idProducto"];
			
					$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
			
					$stockActual = $productoTraido["stock"];
					$stockNuevo = $stockActual + $producto["cantidad"];
			
					$tabla = "inventario";
					$item1 = "stock";
					$valor1 = $stockNuevo;
					$item2 = "id";
					$valor2 = $producto["idProducto"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				}

				$tabla ="facturas_locales";
				$datos = $_GET["idFacturaEliminar"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				$tabla = "eliminadas";

				$datos = array("numero_control" => $facturas["numeroControl"],
								"codigo_generacion" => $facturas["codigoGeneracion"]
							   );

				$guardarElimina = ModeloFacturas::mdlIngresarEliminada($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>
	
					swal({
							type: "success",
							title: "La factura ha sido borrada correctamente y el stock ha sido regresado",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
									if (result.value) {
	
									window.location = "facturacion";
	
									}
								})
	
					</script>';
	
				}				
			} else {
				if($facturas["tipDte"] == "04"){
					$item = "id";
					$orden = "id";
					$valor = $_GET["idFacturaEliminar"];
					$optimizacion = "no";

					$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

					$tabla = "facturas_locales";
					$item1 = "notaRemision";
					$valor1 = "";
					$item2 = "id";
					$valor2 = $facturaOriginal["idFacturaRelacionada"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				}


				

				$tabla ="facturas_locales";
				$datos = $_GET["idFacturaEliminar"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				$tabla = "eliminadas";

				$datos = array("numero_control" => $facturas["numeroControl"],
								"codigo_generacion" => $facturas["codigoGeneracion"]
							   );

				$guardarElimina = ModeloFacturas::mdlIngresarEliminada($tabla, $datos);
	
				if($respuesta == "ok"){
					
						echo'<script>
	
							swal({
								type: "success",
								title: "La factura ha sido borrado correctamente",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
									if (result.value) {

									window.location = "contabilidad";

									}
								})
			
							</script>';
					
				}
			}

		}

	}

	/*=============================================
	BORRAR EVENTO CONTINGENCIA
	=============================================*/

	static public function ctrBorrarEvento(){

		if(isset($_GET["idEventoEliminar"])){

			/*=============================================
			REGRESAR EL STOCK
			=============================================*/	

			$tabla ="contingencias";
			$datos = $_GET["idEventoEliminar"];

			$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

			if($respuesta == "ok"){
				
					echo'<script>

						swal({
							type: "success",
							title: "El evento ha sido borrado correctamente",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
								if (result.value) {

								window.location = "facturacion-contingencia";

								}
							})
		
						</script>';
				
			}
			

		}

	}

	/*=============================================
	ANULAR DTE
	=============================================*/

	static public function ctrCrearAnulacion(){

		if(isset($_POST["facturaRelacionadaAnular"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["facturaRelacionadaAnular"])){

				$item = "id";
				$valor = $_POST["facturaRelacionadaAnular"];
				$orden = "fecEmi";
				$optimizacion = "no";

				$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

				
				function generarParteHex($longitud) {
					$caracteresHex = '0123456789ABCDEF'; // Caracteres hexadecimales
					$parte = '';
					
					for ($i = 0; $i < $longitud; $i++) {
						$parte .= $caracteresHex[rand(0, strlen($caracteresHex) - 1)];
					}
					
					return $parte;
				}
				
				// Generar cada parte del código de generación
				$parte1 = generarParteHex(8);
				$parte2 = generarParteHex(4);
				$parte3 = generarParteHex(4);
				$parte4 = generarParteHex(4);
				$parte5 = generarParteHex(12);
				
				// Combinar todo para formar el código de generación
				$codigoGeneracion = $parte1 . '-' . $parte2 . '-' . $parte3 . '-' . $parte4 . '-' . $parte5;
			
				// Establecer la zona horaria de El Salvador
				date_default_timezone_set('America/El_Salvador');
			
				// Obtener la fecha y la hora actual
				$fecEmi = date("Y-m-d"); // Solo la fecha en formato: YYYY-MM-DD
				$horEmi = date("H:i:s");   // Solo la hora en formato: HH:MM:SS
			

				$datos = array("codigoGeneracion" => $codigoGeneracion,
							   "fecEmi" => $fecEmi,
							   "horEmi" => $horEmi,
							   "facturaRelacionada" => $facturaOriginal["id"],
							   "motivoAnulacion" => $_POST["motivoAnulacion"],);

				

				$tabla = "anuladas";

				$respuesta = ModeloFacturas::mdlIngresarAnulacion($tabla, $datos);
				
			
				if($respuesta == "ok"){

					if($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "03" || $facturaOriginal["tipoDte"] == "11" || $facturaOriginal["tipoDte"] == "14"){
						$productos = json_decode($facturaOriginal["productos"], true);

						foreach ($productos as $producto) {

							// DESCARGAR STOCK FACTURADO
							$item = "id";
							$valor = $producto["idProducto"];
					
							$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
					
							$stockActual = $productoTraido["stock"];
							$stockNuevo = $stockActual + $producto["cantidad"];
					
							$tabla = "inventario";
							$item1 = "stock";
							$valor1 = $stockNuevo;
							$item2 = "id";
							$valor2 = $producto["idProducto"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}

						$tabla = "facturas_locales";
						$item1 = "estado";
						$valor1 = "Anulada";
						$item2 = "id";
						$valor2 = $facturaOriginal["id"];
				
						$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

						if($respuesta == "ok"){
		
							echo'<script>
							
							swal({
								type: "success",
								title: "El DTE de anulación ha sido creado correctamente y el stock ha sido regresado",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
											if (result.value) {
			
											window.location = "contabilidad";
			
											}
										})
			
							</script>';
			
						}

						
						
					} else {
						if($facturaOriginal["tipoDte"] == "04" || $facturaOriginal["tipoDte"] == "05" || $facturaOriginal["tipoDte"] == "06" || $facturaOriginal["tipoDte"] == "14"){
							$tabla = "facturas_locales";
							$item1 = "estado";
							$valor1 = "Anulada";
							$item2 = "id";
							$valor2 = $facturaOriginal["id"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}

						if($facturaOriginal["tipoDte"] == "04"){
							$tabla = "facturas_locales";
							$item1 = "notaRemision";
							$valor1 = "";
							$item2 = "id";
							$valor2 = $facturaOriginal["id"];
					
							$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
						}
							echo '<script>
	
							swal({
		
								type: "success",
								title: "¡La factura local ha sido creada correctamente!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
		
							}).then(function(result){
		
								if(result.value){
								
									window.location = "contabilidad";
		
								}
		
							});
						
		
							</script>';						
					}	
				}
					
					

								

			}else{

				echo '<script>

					swal({

						type: "error",
						title: "¡La factura no se pudo crear!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "facturacion";

						}

					});
				

				</script>';

			}


		}
		
	}

	/*=============================================
	CANCELAR ANULACIÓN
	=============================================*/

	static public function ctrCancelarAnulacion(){

		if(isset($_GET["idFacturaCancelarAnulacion"])){

			/*=============================================
			RESTAR EL STOCK
			=============================================*/

			$item = "id";
			$valor = $_GET["idFacturaCancelarAnulacion"];
			$orden = "fecEmi";

			$facturas = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden);

			$item = "id";
			$valor = $facturas["facturaRelacionada"];
			$orden = "fecEmi";
			$optimizacion = "no";

			$facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

			$productos = json_decode($facturaOriginal["productos"], true);

			if($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "03" || $facturaOriginal["tipoDte"] == "11" || $facturaOriginal["tipoDte"] == "14"){

				foreach ($productos as $producto) {

					// DESCARGAR STOCK FACTURADO
					$item = "id";
					$valor = $producto["idProducto"];
			
					$productoTraido = ControladorProductos::ctrMostrarProductos($item, $valor);
			
					$stockActual = $productoTraido["stock"];
					$stockNuevo = $stockActual - $producto["cantidad"];
			
					$tabla = "inventario";
					$item1 = "stock";
					$valor1 = $stockNuevo;
					$item2 = "id";
					$valor2 = $producto["idProducto"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

				}

				$tabla = "facturas_locales";
				$item1 = "estado";
				$valor1 = "Activa";
				$item2 = "id";
				$valor2 = $facturaOriginal["id"];
		
				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);	
	
				$tabla ="anuladas";
				$datos = $_GET["idFacturaCancelarAnulacion"];

				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);

				if($respuesta == "ok"){

					echo'<script>
	
					swal({
							type: "success",
							title: "El DTE de anulación ha sido borrado correctamente y el stock ha sido regresado",
							showConfirmButton: true,
							confirmButtonText: "Cerrar",
							closeOnConfirm: false
							}).then(function(result) {
									if (result.value) {
	
									window.location = "contabilidad";
	
									}
								})
	
					</script>';
	
				}				
			} else {
				if($facturas["tipDte"] == "04"){

					$tabla = "facturas_locales";
					$item1 = "notaRemision";
					$valor1 = "";
					$item2 = "id";
					$valor2 = $facturaOriginal["idFacturaRelacionada"];
			
					$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
				}

				$tabla = "facturas_locales";
				$item1 = "estado";
				$valor1 = "Activa";
				$item2 = "id";
				$valor2 = $facturaOriginal["id"];
		
				$respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);	

				$tabla ="anuladas";
				$datos = $_GET["idFacturaCancelarAnulacion"];
	
				$respuesta = ModeloFacturas::mdlBorrarFactura($tabla, $datos);
	
				if($respuesta == "ok"){
					
						echo'<script>
	
							swal({
								type: "success",
								title: "El DTE de anulación ha sido borrado correctamente",
								showConfirmButton: true,
								confirmButtonText: "Cerrar",
								closeOnConfirm: false
								}).then(function(result) {
									if (result.value) {

									window.location = "contabilidad";

									}
								})
			
							</script>';
					
				}
			}

		}

	}

	/*=============================================
	ENVIAR FACTURA CORREO
	=============================================*/

	static public function ctrEnviarFacturaCorreo(){

		if (isset($_GET["idFacturaEnviarCorreo"])) {

			require 'extensiones/phpmailer/src/PHPMailer.php';
			require 'extensiones/phpmailer/src/SMTP.php';
			require 'extensiones/phpmailer/src/Exception.php';

			ini_set('display_errors', 1);
			error_reporting(E_ALL);

			// Obtener datos de la empresa, factura y cliente
			$empresa = ControladorClientes::ctrMostrarEmpresas("id", "1", "id");
			$factura = ControladorFacturas::ctrMostrarFacturas("id", $_GET["idFacturaEnviarCorreo"], "fecEmi", "no");
			$cliente = ControladorClientes::ctrMostrarClientes("id", $factura["id_cliente"], "id");
			
			// Determinar tipo de cliente y tipo de factura
			$tipoCliente = [
				"00" => "Consumidor final",
				"01" => "Contribuyente",
				"02" => "Empresa con beneficios fiscales",
				"03" => "Diplomático"
			][$cliente["tipo_cliente"]] ?? "Desconocido";
			
			$tipoFacturaTexto = [
				"18" => "Evento de retorno",
				"01" => "Factura",
				"03" => "Comprobante de crédito fiscal",
				"04" => "Nota de remisión",
				"05" => "Nota de crédito",
				"06" => "Nota de débito",
				"07" => "Comprobante de retención",
				"08" => "Comprobante de liquidación",
				"09" => "Documento contable de liquidación",
				"11" => "Factura de exportación",
				"14" => "Factura de sujeto excluido",
				"15" => "Comprobante de donación"
			][$factura["tipoDte"]] ?? "Factura no válida";

			// Configuración para correos
			$subject = "Emisión de Documento Tributario Electrónico - {$cliente['nombre']}";
			$message = "Estimado cliente: {$cliente['nombre']} - $tipoCliente \n";
			$message .= "Adjunto encontrará su documento $tipoFacturaTexto número:\n";
			$message .= "{$factura['codigoGeneracion']} \n\n";
			$message .= "Para nosotros es un placer servirle \n";
		
			
			// Enviar correos
			try {
				// URL del PDF
				$pdfUrl = "http://localhost/FOX-CONTROL-QWERTY-SYSTEMS/extensiones/TCPDF-main/examples/imprimir-factura.php?idFactura={$_GET['idFacturaEnviarCorreo']}";

				function decodeJWT($jwt) {
					// Dividir el JWT en sus tres partes: Header, Payload y Signature
					$parts = explode('.', $jwt);
				
					// Decodificar la carga útil (Payload) desde Base64URL a texto
					$payload = base64_url_decode($parts[1]);
				
					// Devolver la carga útil decodificada
					return json_decode($payload, true);
				}
				
				function base64_url_decode($data) {
					// Base64URL es una variante de Base64, se debe hacer un pequeño ajuste
					$data = str_replace(['-', '_'], ['+', '/'], $data);  // Reemplazar los caracteres URL-safe
					$padding = strlen($data) % 4;  // Agregar el relleno necesario (=)
					if ($padding) {
						$data .= str_repeat('=', 4 - $padding);
					}
				
					return base64_decode($data);  // Decodificar Base64
				}
				
				// JWT a decodificar (reemplaza esto con tu JWT real)
				if($factura["tipoDte"] === "18"){
					$jsonGuardado = isset($factura["json_guardado"]) ? json_decode($factura["json_guardado"], true) : null;
					$jsonContent = is_array($jsonGuardado)
						? json_encode($jsonGuardado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
						: ($factura["json_guardado"] !== "" ? $factura["json_guardado"] : "{}");
				} else {
				$jwt = $factura["firmaDigital"];  // El JWT completo
				
				// Decodificar el JWT
				$decoded = decodeJWT($jwt);

				// Ahora que tenemos el contenido decodificado, generamos el archivo JSON
				$jsonContent = json_encode($decoded, JSON_PRETTY_PRINT);
				}

				$jsonAdjunto = $factura["tipoDte"] === "18" ? $jsonContent : $jsonContent.$factura["firmaDigital"];
				$nombrePdf = $factura["tipoDte"] === "18" ? "EventoRetorno.pdf" : "Factura.pdf";
				$nombreJson = $factura["tipoDte"] === "18" ? "EventoRetornoData.json" : "FacturaData.json";
				
			

				self::enviarCorreo($empresa["correo"], $cliente["correo"], $empresa["correo"], $subject, $message, $pdfUrl, $jsonAdjunto, $nombrePdf, $nombreJson);

				

				// Redirigir
				echo '<script>
					swal({
						type: "success",
						title: "La factura ha sido enviada correctamente",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location.href = "' . ($factura["tipoDte"] === "18" ? "facturacion" : "index.php?ruta=ver-factura&idFacturaEditar=" . $factura["id"]) . '";
						}
					});
				</script>';
			
				exit();
			} catch (Exception $e) {
				error_log("Error al enviar correo: {$e->getMessage()}");
			}
		}
	}

	static public function ctrEnviarInvalidacionCorreo(){

		if(isset($_GET["idAnulacionEnviarCorreo"])){

			require_once 'extensiones/phpmailer/src/PHPMailer.php';
			require_once 'extensiones/phpmailer/src/SMTP.php';
			require_once 'extensiones/phpmailer/src/Exception.php';

			$empresa = ControladorClientes::ctrMostrarEmpresas("id", "1", "id");
			$anulacion = ControladorFacturas::ctrMostrarAnulaciones("id", $_GET["idAnulacionEnviarCorreo"], "id");

			if(!$anulacion){
				return;
			}

			$factura = ControladorFacturas::ctrMostrarFacturas("id", $anulacion["facturaRelacionada"], "fecEmi", "no");
			$cliente = ControladorClientes::ctrMostrarClientes("id", $factura["id_cliente"], "id");

			$subject = "Evento de invalidacion de DTE - {$cliente['nombre']}";
			$message = "Estimado cliente: {$cliente['nombre']} \n";
			$message .= "Adjunto encontrara el evento de invalidacion del documento:\n";
			$message .= "{$factura['codigoGeneracion']} \n\n";
			$message .= "Codigo de generacion del evento:\n";
			$message .= "{$anulacion['codigoGeneracion']} \n\n";

			$pdfUrl = "http://localhost/FOX-CONTROL-QWERTY-SYSTEMS/extensiones/TCPDF-main/examples/imprimir-invalidacion.php?idAnulacion={$_GET['idAnulacionEnviarCorreo']}";
			$jsonGuardado = isset($anulacion["json_guardado"]) ? json_decode($anulacion["json_guardado"], true) : null;
			$jsonContent = is_array($jsonGuardado) ? json_encode($jsonGuardado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $anulacion["json_guardado"];

			try {
				self::enviarCorreo($empresa["correo"], $cliente["correo"], $empresa["correo"], $subject, $message, $pdfUrl, $jsonContent, 'Invalidacion.pdf', 'InvalidacionData.json');

				echo '<script>
					swal({
						type: "success",
						title: "La invalidacion ha sido enviada correctamente",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location.href = "contabilidad";
						}
					});
				</script>';
			} catch (Exception $e) {
				error_log("Error al enviar invalidacion: {$e->getMessage()}");
				echo '<script>
					swal({
						type: "error",
						title: "La invalidacion no se pudo enviar",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location.href = "contabilidad";
						}
					});
				</script>';
			}
		}
	}

	private static function enviarCorreo($from, $to, $bcc, $subject, $message, $pdfUrl, $jsonContent, $nombrePdf = 'Factura.pdf', $nombreJson = 'FacturaData.json'){
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = 'smtp.hostinger.com';
		$mail->SMTPAuth = true;
		//$mail->SMTPDebug = 1;  // Nivel 3 de depuración para obtener más detalles
		// Establecer la codificación a UTF-8
		$mail->CharSet = 'UTF-8';

		$mail->Username = $from;
		$mail->Password = 'Mampi0712_'; // Usa variables seguras para contraseñas
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;

		$mail->setFrom($from, 'QwertySystems');
		$mail->addAddress($to);
		if ($bcc && strcasecmp(trim($bcc), trim($to)) !== 0) {
			$mail->addBCC($bcc);
		}
		$mail->Subject = $subject;
		$mail->Body = $message;

		 // Descargar y adjuntar el PDF si se proporciona una URL
			if ($pdfUrl) {
				$pdfContent = file_get_contents($pdfUrl);
				if ($pdfContent === false) {
					throw new Exception("No se pudo descargar el archivo PDF desde la URL: $pdfUrl");
				}
		
				$mail->addStringAttachment($pdfContent, $nombrePdf, 'base64', 'application/pdf');
			}

			 // Aquí agregamos el JSON recibido como archivo adjunto
			 $mail->addStringAttachment($jsonContent, $nombreJson, 'base64', 'application/json');
	 
			
			// Enviar correo
			$mail->send();
		
	}/*=============================================
	EDITAR COMPRA
	=============================================*/

	static public function ctrEditarCompra(){

		if(isset($_POST["editarnumero_documentoCompra"])){  // Verifica que el campo exista

			if(trim($_POST["editarnumero_documentoCompra"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo '<script>
					swal({
						type: "error",
						title: "¡El número de documento no puede ir vacío!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(result) {
						if (result.value) {
							window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";
						}
					})
				</script>';

		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				// Obtener los datos del formulario
				$fechas = $_POST['editarFechaCompra'];
				$clases_documento = $_POST['editarclase_documentoCompra'];
				$tipos_documento = $_POST['editartipo_documentoCompra'];
				$numeros_documento = $_POST['editarnumero_documentoCompra'];
				$nits_nrc = $_POST["editarnit_nrcCompra"];
				$proveedores = $_POST['editarnombre_proveedorCompra'];
				$comprass_internas_exentas = $_POST["editarcompras_internas_exentasCompra"];
				$internacioness_exentas_y_no_sujetas = $_POST["editarinternaciones_exentas_y_no_sujetasCompra"];
				$importacioness_exentas_y_no_sujetas = $_POST["editarimportaciones_exentas_y_no_sujetasCompra"];
				$comprass_internas_gravadas = $_POST["editarcompras_internas_gravadasCompra"];
				$internacioness_gravadas_de_bienes = $_POST["editarinternaciones_gravadas_de_bienesCompra"];
				$importacioness_gravadas_de_bienes = $_POST["editarimportaciones_gravadas_de_bienesCompra"];
				$importacioness_gravadas_de_servicios = $_POST["editarimportaciones_gravadas_de_serviciosCompra"];
				$creditos_fiscal = $_POST["editarcredito_fiscalCompra"];
				$totals_de_compras = $_POST["editartotal_de_comprasCompra"];
				$duis_del_proveedor = $_POST["editardui_del_proveedorCompra"];
				$tipos_de_operacion = $_POST["editartipo_de_operacionCompra"];
				$clasificacions = $_POST["editarclasificacionCompra"];
				$sectors = $_POST["editarsectorCompra"];
				$tipos = $_POST["editartipoCompra"];
				$anexos = $_POST["editaranexoCompra"];

				$tabla = "compras";

				$datos = array("fecha" => $fechas,
							"clase_documento" => $clases_documento,
							"tipo_documento" => $tipos_documento,
							"numero_documento" => $numeros_documento,
							"nit_nrc" => $nits_nrc,
							"nombre_proveedor" => $proveedores,
							"compras_internas_exentas" => $comprass_internas_exentas,
							"internaciones_exentas_y_no_sujetas" => $internacioness_exentas_y_no_sujetas,
							"importaciones_exentas_y_no_sujetas" => $importacioness_exentas_y_no_sujetas,
							"compras_internas_gravadas" => $comprass_internas_gravadas,
							"internaciones_gravadas_de_bienes" => $internacioness_gravadas_de_bienes,
							"importaciones_gravadas_de_bienes" => $importacioness_gravadas_de_bienes,
							"importaciones_gravadas_de_servicios" => $importacioness_gravadas_de_servicios,
							"credito_fiscal" => $creditos_fiscal,
							"total_de_compras" => $totals_de_compras,
							"dui_del_proveedor" => $duis_del_proveedor,
							"tipo_de_operacion" => $tipos_de_operacion,
							"clasificacion" => $clasificacions,
							"sector" => $sectors,
							"tipo" => $tipos,
							"anexo" => $anexos,
							"id" => $_POST["editarIdCompra"]);
		
				$respuesta = ModeloFacturas::mdlEditarCompra($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
								window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	BORRAR COMPRA
	=============================================*/

	static public function ctrBorrarCompra(){

		if(isset($_GET["idCompraEliminar"])){

			$tabla ="compras";
			$datos = $_GET["idCompraEliminar"];

			$respuesta = ModeloClientes::mdlBorrarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

								window.location = "index.php?ruta=ver-compras&filtroFactura=todos&filtroFechaInicio=&filtroFechaFin=' . date('Y-m-d') . '";

				</script>';

			}		

		}

	}
}
