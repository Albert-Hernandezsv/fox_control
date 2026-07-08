<?php

require_once "../controladores/facturas.controlador.php";
require_once "../modelos/facturas.modelo.php";

require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

session_start();

class AjaxFacturas {

    /*=============================================
	SELLAR FACTURA
	=============================================*/	
	public $idFacturaS;
	public $idFacturaVerFirma;
	public $idFacturaLimpiarFirma;
	public $idAnulacionVerFirma;
	public $idAnulacionLimpiarFirma;
	public $idFacturaJsonGuardado;

	private function decodificarBase64Url($valor){

		$valor = str_replace(array('-', '_'), array('+', '/'), $valor);
		$relleno = strlen($valor) % 4;
		if($relleno){
			$valor .= str_repeat('=', 4 - $relleno);
		}

		return base64_decode($valor);
	}

	private function actualizarJsonGuardadoFactura($idFactura, $campos){

		$factura = ControladorFacturas::ctrMostrarFacturas("id", $idFactura, "id", "no");
		$jsonGuardado = array();

		if($factura && isset($factura["json_guardado"]) && $factura["json_guardado"] !== ""){
			$jsonGuardado = json_decode($factura["json_guardado"], true);
			if(!is_array($jsonGuardado)){
				$jsonGuardado = array();
			}
		}

		foreach($campos as $campo => $valor){
			$jsonGuardado[$campo] = $valor;
		}

		return ModeloFacturas::mdlActualizarFactura(
			"facturas_locales",
			"json_guardado",
			json_encode($jsonGuardado, JSON_UNESCAPED_UNICODE),
			"id",
			$idFactura
		);
	}

	
	

	public function ajaxVerFirmaFactura(){

		$factura = ControladorFacturas::ctrMostrarFacturas("id", $this->idFacturaVerFirma, "id", "no");
		if(!$factura || $factura["firmaDigital"] === ""){
			echo json_encode(array("ok" => false, "mensaje" => "Factura sin firma digital."));
			return;
		}

		$firma = $factura["firmaDigital"];
		$partes = explode(".", $firma);
		$contenido = count($partes) >= 2 ? $this->decodificarBase64Url($partes[1]) : base64_decode($firma);
		$json = json_decode($contenido, true);

		if($json === null){
			echo json_encode(array("ok" => false, "mensaje" => "No se pudo decodificar la firma."));
			return;
		}

		echo json_encode(array("ok" => true, "json" => json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)));
	}

	public function ajaxLimpiarFirmaFactura(){

		$factura = ControladorFacturas::ctrMostrarFacturas("id", $this->idFacturaLimpiarFirma, "id", "no");
		if(!$factura){
			echo json_encode(array("ok" => false, "mensaje" => "Factura no encontrada."));
			return;
		}
		if($factura["sello"] !== "" && $factura["sello"] !== null){
			echo json_encode(array("ok" => false, "mensaje" => "No se puede limpiar una firma cuando la factura ya tiene sello."));
			return;
		}
		if($factura["firmaDigital"] === "" || $factura["firmaDigital"] === null){
			echo json_encode(array("ok" => false, "mensaje" => "La factura no tiene firma digital."));
			return;
		}

		$respuesta = ModeloFacturas::mdlActualizarFactura(
			"facturas_locales",
			"firmaDigital",
			"",
			"id",
			$this->idFacturaLimpiarFirma
		);

		echo json_encode(array(
			"ok" => $respuesta === "ok",
			"mensaje" => $respuesta === "ok" ? "Firma eliminada correctamente." : "No se pudo limpiar la firma."
		));
	}

	public function ajaxVerFirmaAnulacion(){

		$anulacion = ControladorFacturas::ctrMostrarAnulaciones("id", $this->idAnulacionVerFirma, "id");
		if(!$anulacion || $anulacion["firmaDigital"] === ""){
			echo json_encode(array("ok" => false, "mensaje" => "Invalidacion sin firma digital."));
			return;
		}

		$firma = $anulacion["firmaDigital"];
		$partes = explode(".", $firma);
		$contenido = count($partes) >= 2 ? $this->decodificarBase64Url($partes[1]) : base64_decode($firma);
		$json = json_decode($contenido, true);

		if($json === null){
			echo json_encode(array("ok" => false, "mensaje" => "No se pudo decodificar la firma."));
			return;
		}

		echo json_encode(array("ok" => true, "json" => json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)));
	}

	public function ajaxLimpiarFirmaAnulacion(){

		$anulacion = ControladorFacturas::ctrMostrarAnulaciones("id", $this->idAnulacionLimpiarFirma, "id");
		if(!$anulacion){
			echo json_encode(array("ok" => false, "mensaje" => "Evento de invalidacion no encontrado."));
			return;
		}
		if($anulacion["sello"] !== "" && $anulacion["sello"] !== null){
			echo json_encode(array("ok" => false, "mensaje" => "No se puede limpiar una firma cuando el evento ya tiene sello."));
			return;
		}
		if($anulacion["firmaDigital"] === "" || $anulacion["firmaDigital"] === null){
			echo json_encode(array("ok" => false, "mensaje" => "El evento no tiene firma digital."));
			return;
		}

		$respuesta = ModeloFacturas::mdlActualizarFactura(
			"anuladas",
			"firmaDigital",
			"",
			"id",
			$this->idAnulacionLimpiarFirma
		);

		echo json_encode(array(
			"ok" => $respuesta === "ok",
			"mensaje" => $respuesta === "ok" ? "Firma eliminada correctamente." : "No se pudo limpiar la firma."
		));
	}

	public function ajaxJsonGuardadoFactura(){

		$factura = ControladorFacturas::ctrMostrarFacturas("id", $this->idFacturaJsonGuardado, "id", "no");
		if(!$factura || $factura["json_guardado"] === ""){
			echo json_encode(array("ok" => false, "mensaje" => "Factura sin JSON guardado."));
			return;
		}

		$json = json_decode($factura["json_guardado"], true);
		$contenido = $json === null ? $factura["json_guardado"] : json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo json_encode(array(
			"ok" => true,
			"nombre" => ($factura["tipoDte"] === "18" ? "evento-retorno-" : "factura-").$factura["codigoGeneracion"].".json",
			"json" => $contenido
		));
	}

	public function ajaxSellarFactura() {
    
        $item = "id";
        $orden = "id";
        $valor = $this->idFacturaS;
        $optimizacion = "no";
    
        // Obtiene los datos de la factura
        $factura1 = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;
        }
    
        // Datos de la factura
        $factura = [
            "codigoGeneracion" => $factura1["codigoGeneracion"],
            "firmaDigital" => $factura1["firmaDigital"],
            "tipoDte" => $factura1["tipoDte"]
        ];
    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/recepciondte";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
    
        if($factura["tipoDte"] == "01"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 2
            ];
        }
        if($factura["tipoDte"] == "03"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 4
            ];
        }

        if($factura["tipoDte"] == "04"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 4
            ];
        }

        if($factura["tipoDte"] == "05"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 4
            ];
        }

        if($factura["tipoDte"] == "06"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 4
            ];
        }

        if($factura["tipoDte"] == "11"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 3
            ];
        }
        
        if($factura["tipoDte"] == "14"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 2
            ];
        }

        if($factura["tipoDte"] == "18"){
            // Cuerpo de la solicitud en JSON
            $data = [
                "ambiente" => "01",
                "codigoGeneracion" => $factura["codigoGeneracion"],
                "documento" => $factura["firmaDigital"],
                "idEnvio" => 1,
                "tipoDte" => $factura["tipoDte"],
                "version" => 1
            ];
        }
            
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "facturas_locales";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idFacturaS;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            $respuestaJson = $this->actualizarJsonGuardadoFactura($this->idFacturaS, array(
                "selloRecibido" => $selloRecibido
            ));
    
            if($respuesta1 == "ok" && $respuestaJson == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }

    /*=============================================
	SELLAR EVENTO CONTINGENCIA
	=============================================*/	
	public $idEventoH;

	public function ajaxSellarEvento() {
    
        
        $item = "id";
        $orden = "id";
        $valor = $this->idEventoH;
    
        // Obtiene los datos de la factura
        $factura = ControladorFacturas::ctrMostrarEventosContingencias($item, $valor, $orden);
        
        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;

        }
    
        
        
    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/contingencia";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
        
        
        // Cuerpo de la solicitud en JSON
        $data = [
            "nit" => $empresa["nit"],
            "documento" => $factura["firmaDigital"]
        ];

        
        
    
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "contingencias";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idEventoH;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $jsonGuardado = json_decode($factura1["json_guardado"], true);
            if(!is_array($jsonGuardado)){
                $jsonGuardado = [];
            }
            $jsonGuardado["selloRecibido"] = $selloRecibido;

            $tabla = "anuladas";
            $item1 = "json_guardado";
            $valor1 = json_encode($jsonGuardado);
            $item2 = "id";
            $valor2 = $this->idFacturaSA;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
    
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }


    /*=============================================
	SELLAR ANULACION
	=============================================*/	
	public $idFacturaSA;

	public function ajaxSellarAnulacion() {

    
        $item = "id";
        $orden = "id";
        $valor = $this->idFacturaSA;
    
        // Obtiene los datos de la factura
        $factura1 = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden);
    
        // Asegúrate de que el token esté en la sesión
        if (!isset($_SESSION["tokenInicioSesionMh"])) {
            echo json_encode("Token no encontrado en la sesión");
            return;
        }

    
        // URL de la API a la que estás haciendo el posteo
        $url = "https://api.dtes.mh.gob.sv/fesv/anulardte";
    
        // Configuración de los headers
        $headers = [
            "Authorization: " . $_SESSION["tokenInicioSesionMh"],
            "User-Agent: facturacion",
            "Content-Type: application/json"
        ];
    
        // Cuerpo de la solicitud en JSON
        $data = [
            "ambiente" => "01",
            "idEnvio" => 1,
            "version" => 2,
            "documento" => $factura1["firmaDigital"]
        ];
        
        // Inicialización de cURL
        $ch2 = curl_init($url);
    
        // Configuración de cURL
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    
        // Ejecución de la solicitud y captura de la respuesta
        $response = curl_exec($ch2);
        
        // Verifica si hubo algún error
        if (curl_errno($ch2)) {
            echo json_encode(['error' => curl_error($ch2)]);
            curl_close($ch2);
            return;
        }
        
        // Decodifica la respuesta JSON a un array asociativo
        $decoded_response = json_decode($response, true);
    
        // Verifica si el campo "selloRecibido" existe en la respuesta
        if (isset($decoded_response["selloRecibido"])) {
            $selloRecibido = $decoded_response["selloRecibido"];
    
            $tabla = "anuladas";
            $item1 = "sello";
            $valor1 = $selloRecibido;
            $item2 = "id";
            $valor2 = $this->idFacturaSA;
    
            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $jsonGuardado = json_decode($factura1["json_guardado"], true);
            if(!is_array($jsonGuardado)){
                $jsonGuardado = [];
            }
            $jsonGuardado["selloRecibido"] = $selloRecibido;

            $tabla = "anuladas";
            $item1 = "json_guardado";
            $valor1 = json_encode($jsonGuardado);
            $item2 = "id";
            $valor2 = $this->idFacturaSA;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no");
            }
    
        } else {
            echo json_encode($response);
        }
    
        // Cierre de la conexión cURL
        curl_close($ch2);
    }


    /*=============================================
	FIRMAR FACTURA ANULAR DTE
	=============================================*/	
	public $idFacturaA;

	public function ajaxAnularDTE() {
        
        
        $item = "id";
        $orden = "id";
		$valor = $this->idFacturaA;

		$factura = ControladorFacturas::ctrMostrarAnulaciones($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $factura["facturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $facturaOriginal["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);


        // Decodificar los productos de la factura
        $productos = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;

        $montoIva = 0.0;
        $tipoDocu = "";
        $docu = "";
        if((($facturaOriginal["tipoDte"] == "01" || $facturaOriginal["tipoDte"] == "14") && $cliente["tipo_cliente"] == "00")){
            $tipoDocu = "13";
            $docu = $cliente["DUI"];
        } else {
            $tipoDocu = "36";
            $docu = $cliente["NIT"];
        }

        if($facturaOriginal["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01")){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));

        }

        if($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){ // Factura, diplomáticos
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;


        }

        if($facturaOriginal["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));


        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de crédito, CCF Declarante IVA - Empresa

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){ // Nota de crédito, CCF Beneficios fiscales
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){ // Nota de crédito, CCF Diplomáticos

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de débito, CCF Declarante IVA - Empresa

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){ // Nota de débito, CCF Empresa con beneficios fiscales

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){ // Nota de débito, CCF Diplomáticos

            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = floatval(number_format($ivaSacar, 2, '.', ''));
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Empresa con beneficios fiscales
            
           // Recorrer cada producto y mapear los datos
           $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

           // Formatea el resultado a 8 decimales
           $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03" && $facturaOriginal["tipoDte"] == "03"){ // Nota de remisión, CCF Diplomaticos
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "01" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "02" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Empresa beneficios fiscales
            
           // Recorrer cada producto y mapear los datos
           $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

           // Formatea el resultado a 8 decimales
           $montoIva = 0.0;
            
        }

        if($facturaOriginal["tipoDte"] === "04" && $cliente["tipo_cliente"] === "03" && $facturaOriginal["tipoDte"] === "11"){ // Nota de remisión, XPORT Diplomaticos
            
            // Recorrer cada producto y mapear los datos
            $ivaSacar = $facturaOriginal["total"] - $facturaOriginal["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $montoIva = 0.0;
        }

        

            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "fecAnula" => $factura["fecEmi"],
                        "horAnula" => $factura["horEmi"]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nombre" => $empresa["nombre"],
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "nomEstablecimiento" => $empresa["nombre"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]                    
                    ],
                    "documento" => [
                        "tipoDte"=> $facturaOriginal["tipoDte"],
                        "codigoGeneracion"=> $facturaOriginal["codigoGeneracion"],
                        "selloRecibido"=> $facturaOriginal["sello"],
                        "numeroControl"=> $facturaOriginal["numeroControl"],
                        "fecEmi"=> $facturaOriginal["fecEmi"],
                        "montoIva"=> $montoIva,
                        "codigoGeneracionR"=> null,
                        "tipoDocumento"=> $tipoDocu,
                        "numDocumento"=> $docu,
                        "nombre"=> $cliente["nombre"],
                        "telefono"=> $cliente["telefono"],  
                        "correo"=> $cliente["correo"]
                    ],
                    "motivo" => [
                        "tipoAnulacion" => 2, // Siempre reicindir de la compra
                        "motivoAnulacion" => $factura["motivoAnulacion"],
                        "nombreResponsable" => $empresa["nombre"],
                        "tipDocResponsable" => "36",
                        "numDocResponsable" => $empresa["nit"],
                        "nombreSolicita" => $cliente["nombre"],
                        "tipDocSolicita" => "36",
                        "numDocSolicita" => $cliente["NIT"]
                    ]
                ]
            ];
        

        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "anuladas";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFacturaA;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $data["firmaElectronica"] = $bodyContent;

            $tabla = "anuladas";
            $item1 = "json_guardado";
            $valor1 = json_encode($data);
            $item2 = "id";
            $valor2 = $this->idFacturaA;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}
    

    /*=============================================
	FIRMAR FACTURA
	=============================================*/	
	public $idFactura;

	public function ajaxEnviarFactura() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idFactura;
        $optimizacion = "no";

		$factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $factura["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $factura["idMotorista"];

        $motorista = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

        // Decodificar los productos de la factura
        $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;
        $descuentoGobal = 0;

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "00"){ // Factura, persona normal
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                if($productoLei["exento_iva"] == "no"){
                    $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                } else {
                    $ivaItem = 0.0;
                }
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 4, '.', ''));
                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => $ivaItemTotalFormateado
                ];
                
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = 0.0;
                    $item["ventaGravada"] = $totalProF;
                } else {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = $totalProF;
                    $item["ventaGravada"] = 0.0;
                }
                
                // Agregar el item al array final
                $cuerpoDocumento[] = $item;                

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["total"] - $retencionGranContribuyente));

            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }


            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 2,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $cliente["DUI"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete" => 0.0,
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["total"] - $retencionGranContribuyente, 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["total"] - $retencionGranContribuyente, 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "01"){ // Factura, persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                if($factura["gran_contribuyente"] == "Si"){
                    $nuevoPrecioSinImpuestos = round(($producto["precioSinImpuestos"] - ($producto["precioSinImpuestos"] * 0.01)), 2);
                    $nuevoPrecioConIva = round(($nuevoPrecioSinImpuestos + ($nuevoPrecioSinImpuestos * 0.13)), 2);
                    $nuevoTotalProducto = round(($nuevoPrecioConIva * $producto["cantidad"]), 2);
                    $nuevoDescuento = round(($producto["descuento"] - ($producto["descuento"] * 0.01)), 2);
                    $nuevoDescuentoConIva = round(($nuevoDescuento +  ($nuevoDescuento * 0.13)), 2);

                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($nuevoDescuentoConIva * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($nuevoTotalProducto - $descTotal) - (($nuevoTotalProducto - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($nuevoPrecioConIva - $nuevoDescuentoConIva) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $nuevoPrecioConIva, // Precio con impuestos del producto
                        "montoDescu" => $nuevoDescuentoConIva * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  

                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $nuevoDescuentoConIva * $producto["cantidad"];
                } else {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));
    
                    $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));
    
                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
                }
                
            }
            if($factura["gran_contribuyente"] == "Si"){
                $nuevoTotalSinIva = round(($factura["totalSinIva"] - ($factura["totalSinIva"] * 0.01)), 2);
                $nuevoTotal = round(($nuevoTotalSinIva + ($nuevoTotalSinIva * 0.13)), 2);
            } else {
                $nuevoTotalSinIva = $factura["totalSinIva"];
                $nuevoTotal = $factura["total"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $nuevoTotal - $nuevoTotalSinIva;

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($nuevoTotal));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 2,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($nuevoTotal),
                        "observaciones" => null,
                        "subTotalVentas" => floatval($nuevoTotal),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($nuevoTotal),
                        "ivaRete" => 0.0,
                        "montoTotalOperacion" => floatval($nuevoTotal),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($nuevoTotal - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($nuevoTotal - $retencionGranContribuyente, 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($nuevoTotal - $retencionGranContribuyente, 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 2,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"] - $retencionGranContribuyente, 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"] - $retencionGranContribuyente, 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){ // Factura, diplomáticos
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 2,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => floatval($factura["totalSinIva"]),
                        "totalGravada" => 0.0,
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"] - $retencionGranContribuyente, 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"] - $retencionGranContribuyente, 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  


                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["total"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["totalSinIva"]),
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete" => $retencionGranContribuyente,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($factura["totalSinIva"]-$sinIva) * 0.13, 2)
                                ]
                        ],
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round(($factura["total"] - $retencionGranContribuyente), 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round(($factura["total"] - $retencionGranContribuyente), 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete" => 0.0,
                        "tributos" => null,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"], 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"], 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => floatval($factura["totalSinIva"]),
                        "totalGravada" => 0.0,
                        "observaciones" => null,
                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete" => 0.0,
                        "tributos" => null,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"])),
                        "totalLetras" => $totalLetras,
                        "ivaPerci" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"], 2),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"], 2),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "numPagoElectronico" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "02"){ // Nota de remisión, CCF Empresa con beneficios fiscales
            
            $totalGravado = 0.0;
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => [
                                "20"
                    ],
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];
            
            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => 0.0
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "04" && $cliente["tipo_cliente"] == "03"){ // Nota de remisión, CCF Diplomaticos
            
            $totalGravado = 0.0;
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => [
                                "20"
                    ],
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];
            
            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => 0.0
                                ]
                        ],
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] === "04" && $cliente["tipo_cliente"] === "01"){ // Nota de remisión, XPORT Declarante IVA - Empresa
            
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalGravado += $totalProF;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            $opera = $totalGravado;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($totalGravado));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => null,
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "nrc" => $ncrCliente,
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"],
                        "bienTitulo" => "04"
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => $totalGravado,
                        "montoTotalOperacion" => $operaR,
                        "totalLetras" => $totalLetras
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]),
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos"=> [
                        "C3"
                        ],
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "numeroDocumento" => null,
                    "codTributo" => null
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $tipoDocumento = "";
            if($cliente["departamento"] == "00" || $cliente["municipio"] == "00"){
                $tipoDocumento = "37";
            } else {
                $tipoDocumento = "36";
            }

            $codIncoterms = null;
            $descIncoterms = null;

            if ($factura["cod_incoterms"] != null) {
                $codIncoterms = $factura["cod_incoterms"];
            }

            if ($factura["desc_incoterms"] != null) {
                $descIncoterms = $factura["desc_incoterms"];
            }

            
            
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];

            $totalLetras = convertirMontoALetras(floatval($totalOpera));

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "tipoItemExpor" => 1, // Solo para vender bienes
                        "recintoFiscal" => $factura["recintoFiscal"],
                        "tipoRegimen" => $factura["tipo_regimen"],
                        "regimen" => $factura["regimen"],
                    ],
                    "receptor" => [
                        "nombre" => $cliente["nombre"],
                        "tipoDocumento" => $tipoDocumento,
                        "numDocumento" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "codPais" => $cliente["codPais"],
                        "nombrePais" => $cliente["nombrePais"],
                        "complemento" => $cliente["direccion"],
                        "tipoPersona" => intval($cliente["tipoPersona"]),
                        "descActividad" => $cliente["descActividad"],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => [
                        [
                            "codDocAsociado" => 4, // de transporte
                            "descDocumento" => null,
                            "detalleDocumento" => null,
                            "placaTrans" => $motorista["placaMotorista"],
                            "modoTransp" => intval($factura["modoTransporte"]),
                            "numConductor" => $motorista["duiMotorista"],
                            "nombreConductor" => $motorista["nombre"]
                        ]
                    ],
                    "documentoRelacionado" => null,
                    "compraTercero" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalGravada" => floatval($factura["totalSinIva"]),
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "seguro" => floatval($factura["seguro"]),
                        "flete" => floatval($factura["flete"]),
                        "montoTotalOperacion" => $totalOpera,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => $totalOpera,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => round(($factura["condicionOperacion"]), 2),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"]),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"]),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "tributos"=> [
                                [
                                    "codigo"=> "C3",
                                    "descripcion"=> "IVA EXPORTACIÓN",
                                    "valor" => 0
                                ]
                            ],
                        "totalNoOnerosas" => 0.0,
                        "saldoFavor" => 0.0,
                        "numPagoElectronico" => null,
                        "observaciones" => null,
                        "codIncoterms" => $codIncoterms,
                        "descIncoterms" => $descIncoterms
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto                    
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "compra" => $totalProF, // Valor de venta gravada
                    
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            
            $totalProF = floatval(number_format($totalPro, 2, '.', ''));
            $renta = floatval(number_format(($factura["totalSinIva"] * 0.10), 2, '.', ''));
            $totalSinRenta = floatval(number_format(($factura["totalSinIva"] - $renta), 2, '.', ''));
            $totalLetras = convertirMontoALetras(floatval($totalSinRenta));
            
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }

            $numeroDUI = $cliente["DUI"]; // Tu número original
            $modificadoDUI = $numeroDUI;

            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 2,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $modificadoDUI,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalCompra" => floatval($factura["totalSinIva"]),
                        "descu" => 0.0,
                        "totalDescu" => floatval($descuentoGobalF),
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "reteRenta" => $renta,
                        "totalPagar" => $totalSinRenta,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => $factura["condicionOperacion"] == 1
                            ? [
                                [
                                    "codigo" => "01",
                                    "montoPago" => round($factura["totalSinIva"]),
                                    "referencia" => null,
                                    "plazo" => null,
                                    "periodo" => null
                                ]
                            ]
                            : (
                                $factura["condicionOperacion"] == 2
                                ? [
                                    [
                                        "codigo" => "05",
                                        "montoPago" => round($factura["totalSinIva"]),
                                        "referencia" => null,
                                        "plazo" => $factura["plazo_pago"],
                                        "periodo" => (int) $factura["periodo_pago"]
                                    ]
                                ]
                                : null
                            ),
                        "observaciones" => null,

                    ],
                    "apendice" => null
                ]
            ];


        }
        $item = "id";
        $orden = "id";
        $valor = $factura["idFacturaRelacionada"];
        $optimizacion = "no";

        $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de crédito, CCF Declarante IVA - Empresa

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;

            $sinIva = 0;

            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $item = [
                    "noGravado" => 0.0,
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0,
                    "totalIva" => 0.0
                ];
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = isset($partes[1]) ? str_pad($partes[1], 2, '0', STR_PAD_RIGHT) : '00'; // Siempre dos decimales
                
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
                
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                // Si el número es 0, devolvemos "cero"
                if ($numero == 0) {
                    return "cero";
                }
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = ($totalGravado - $retencionGranContribuyente) + ($totalGravado * 0.13);
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "totalDescu" => 0.0,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($totalGravado - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado + round(($totalGravado - $sinIva) * 0.13, 2),
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "totalIva" => 0.0,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,
                        "totalPagar" => $totalGravado + round(($totalGravado - $sinIva) * 0.13, 2) - $retencionGranContribuyente
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "02"){ // Nota de crédito, CCF Beneficios fiscales

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "noGravado" => 0.0,
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "totalIva" => 0.0,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }
            
            

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = $totalGravado - $retencionGranContribuyente;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "totalIva" => 0.0,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,                        
                        "totalPagar" => $totalGravado - $retencionGranContribuyente
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "05" && $cliente["tipo_cliente"] == "03"){ // Nota de crédito, CCF Diplomáticos

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "noGravado" => 0.0,
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "totalIva" => 0.0,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = $totalGravado - $retencionGranContribuyente;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "totalIva" => 0.0,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,
                        "totalPagar" => $totalGravado - $retencionGranContribuyente
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "01" && $facturaOriginal["tipoDte"] != "11"){ // Nota de débito, CCF Declarante IVA - Empresa

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;

            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "noGravado" => 0.0,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0,
                    "totalIva" => 0.0
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  


                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = ($totalGravado - $retencionGranContribuyente) + ($totalGravado * 0.13);
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => $totalGravado,
                        "subTotalVentas" => $totalGravado,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,
                        "totalDescu" => 0.0,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($totalGravado - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado + round(($totalGravado - $sinIva) * 0.13, 2),
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null,
                        "totalPagar" => $totalGravado + round(($totalGravado - $sinIva) * 0.13, 2) - $retencionGranContribuyente,
                        "totalIva" => 0.0
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "02"){ // Nota de débito, CCF Empresa con beneficios fiscales

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "noGravado" => 0.0,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0,
                    "totalIva" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = $totalGravado - $retencionGranContribuyente;
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null,
                        "totalPagar" => $totalGravado - $retencionGranContribuyente,
                        "totalIva" => 0.0
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        if($factura["tipoDte"] == "06" && $cliente["tipo_cliente"] == "03"){ // Nota de débito, CCF Diplomáticos

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                
                $des = $producto["descuento"];
                $desR = floatval(number_format($des, 2, '.', ''));

                $totalProD = (($producto["descuento"] * $producto["cantidad"]));
                $totalProF = floatval(number_format($totalProD, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "precioUni" => $producto["descuento"], // Precio con impuestos del producto
                    "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "noGravado" => 0.0,
                    "ivaPerci" => 0.0,
                    "ivaRete" => 0.0,
                    "totalIva" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $totalDescuento += $desR;
                $totalGravado += $totalProF;
            }

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = $totalGravado - $retencionGranContribuyente;
            $operaR = floatval(number_format($opera - $retencionGranContribuyente, 2, '.', ''));

            $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "fusion" => null,
                        "version" => 4,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "tipoGeneracion" => 2,
                        "numeroDocumento" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "nombreComercial" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "observaciones" => null,
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "totalNoGravado" => 0.0,
                        "codigoRetencionMH" => null,
                        "totalDescu" => 0.0,
                        "tributos" => null,
                        "ivaPerci" => 0.0,
                        "ivaRete" => $retencionGranContribuyente,
                        "montoTotalOperacion" => $totalGravado,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "numPagoElectronico" => null,
                        "totalPagar" => $totalGravado - $retencionGranContribuyente,
                        "totalIva" => 0.0
                    ],
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }
        
        if($factura["tipoDte"] == "18"){ // Evento de retorno persona normal

            $item = "id";
            $orden = "id";
            $valor = $factura["idFacturaRelacionada"];
            $optimizacion = "no";

            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            // Decodificar los productos de la factura
            $productos1 = json_decode($facturaOriginal["productos"], true); // true para obtener un array asociativo
            
            $totalDescuento = 0.0;
            $totalGravado = 0.0;

            $sinIva = 0;

            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.00;
            if($facturaOriginal["gran_contribuyente"] != "No"){
                $retencionGranContribuyente = $totalGravado * 0.01;
            }

            $opera = ($totalGravado - $retencionGranContribuyente) + ($totalGravado * 0.13);
            $operaR = floatval(number_format($opera, 2, '.', ''));

            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            $descu2 = $facturaOriginal["total"] - $totalGravado;

            $resumen = [];

            $tipoDocumento = "36";
            $numDocumento = $cliente["NIT"];

            if ($facturaOriginal["tipoDte"] == "01" && ($cliente["tipo_cliente"] == "00" || $cliente["tipo_cliente"] == "01")){
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);

                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en l
                        "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                        "codTributo" => null,
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => $totalProF, // Valor de venta gravada
                        "compra" => 0.0,
                        "psv" => 0.0,
                        "ivaItem" => $ivaItemTotalFormateado,
                        "noGravado" => 0.0,
                        "seguro" => 0.0,
                        "flete" => 0.0,
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0
                    ];

                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["tributos"] = null;
                    } else {
                        $item["tributos"] = null;
                        $sinIva += $totalProF;
                    }

                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  


                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                }

                $totalLetras = convertirMontoALetras(floatval($totalGravado - $retencionGranContribuyente));

                $resumen = [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval(number_format($totalGravado, 2)),
                        "totalCompraExcluidos" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "tributos" => null,
                        "totalSeguro" => 0.0,
                        "totalFlete" => 0.0,
                        "montoTotalOperacion" => floatval(number_format($totalGravado, 2)),
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => floatval(number_format($totalGravado, 2)),
                        "totalLetras" => $totalLetras,
                        "totalNoOnerosas" => 0.0,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0
                ];
            }

            if ($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);

                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en l
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                        "codTributo" => null,
                        "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "compra" => 0.0,
                        "psv" => 0.0,
                        "ivaItem" => 0.0,
                        "noGravado" => 0.0,
                        "seguro" => 0.0,
                        "flete" => 0.0,
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "tributos" => null
                    ];

                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  


                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                }

                $totalLetras = convertirMontoALetras(floatval($totalGravado));

                $resumen = [
                        "totalNoSuj" => $totalGravado,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "totalCompraExcluidos" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "tributos" => null,
                        "totalSeguro" => 0.0,
                        "totalFlete" => 0.0,
                        "montoTotalOperacion" => floatval(number_format($totalGravado, 2)),
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => floatval(number_format($totalGravado, 2)),
                        "totalLetras" => $totalLetras,
                        "totalNoOnerosas" => 0.0,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0
                ];
            }

            if ($facturaOriginal["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);

                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en l
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                        "codTributo" => null,
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "compra" => 0.0,
                        "psv" => 0.0,
                        "ivaItem" => 0.0,
                        "noGravado" => 0.0,
                        "seguro" => 0.0,
                        "flete" => 0.0,
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "tributos" => null
                    ];

                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  


                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                }

                $totalLetras = convertirMontoALetras(floatval($totalGravado));

                $resumen = [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => $totalGravado,
                        "totalGravada" => 0.0,
                        "totalCompraExcluidos" => 0.0,
                        "subTotalVentas" => $totalGravado,
                        "tributos" => null,
                        "totalSeguro" => 0.0,
                        "totalFlete" => 0.0,
                        "montoTotalOperacion" => $totalGravado,
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => $totalGravado,
                        "totalLetras" => $totalLetras,
                        "totalNoOnerosas" => 0.0,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0
                ];
            }

            if ($facturaOriginal["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);

                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en l
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                        "codTributo" => null,
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => $totalProF, // Valor de venta gravada
                        "compra" => 0.0,
                        "psv" => 0.0,
                        "ivaItem" => 0.0,
                        "noGravado" => 0.0,
                        "seguro" => $factura["seguro"],
                        "flete" => $factura["flete"],
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "tributos"=> [
                            "C3"
                        ],
                    ];

                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  


                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                }

                $totalLetras = convertirMontoALetras(floatval($totalGravado + $factura["seguro"] + $factura["flete"]));

                $resumen = [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval(number_format($totalGravado + $factura["flete"] + $factura["seguro"], 2)),
                        "totalCompraExcluidos" => 0.0,
                        "subTotalVentas" => floatval(number_format($totalGravado + $factura["flete"] + $factura["seguro"], 2)),
                        "tributos"=> [
                                [
                                    "codigo"=> "C3",
                                    "descripcion"=> "IVA EXPORTACIÓN",
                                    "valor" => 0
                                ]
                            ],
                        "totalSeguro" => $factura["seguro"],
                        "totalFlete" => $factura["flete"],
                        "montoTotalOperacion" => floatval(number_format($totalGravado + $factura["flete"] + $factura["seguro"], 2)),
                        "ivaRete" => 0.0,
                        "reteRenta" => 0.0,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => floatval(number_format($totalGravado + $factura["flete"] + $factura["seguro"], 2)),
                        "totalLetras" => $totalLetras,
                        "totalNoOnerosas" => 0.0,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0
                ];
            }

            if ($facturaOriginal["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){
                // Recorrer cada producto y mapear los datos
                foreach ($productos as $producto) {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $ivaItem = $producto["totalProducto"] - ($producto["totalProducto"] / 1.13);

                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($producto["precioSinImpuestos"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en l
                        "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "montoDescu" => 0.0, // Si no hay descuentos, puedes dejarlo en 0
                        "codTributo" => null,
                        "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                        "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                        "ventaGravada" => 0.0, // Valor de venta gravada
                        "compra" => $totalProF,
                        "psv" => 0.0,
                        "ivaItem" => 0.0,
                        "noGravado" => 0.0,
                        "seguro" => 0.0,
                        "flete" => 0.0,
                        "ivaRete" => 0.0,
                        "reteRenta" => floatval(number_format($totalProF * 0.10, 2)),
                        "tributos" => null
                    ];

                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  


                    // Incrementar el número de ítem
                    $numItem++;
                    $totalGravado += $totalProF;
                }

                $renta = floatval(number_format($totalGravado * 0.10, 2));
                $totalLetras = convertirMontoALetras(floatval($totalGravado - $renta));

                $resumen = [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,
                        "totalCompraExcluidos" => floatval(number_format($totalGravado, 2)),
                        "subTotalVentas" => $totalGravado,
                        "tributos" => null,
                        "totalSeguro" => 0.0,
                        "totalFlete" => 0.0,
                        "montoTotalOperacion" => floatval(number_format($totalGravado, 2)),
                        "ivaRete" => 0.0,
                        "reteRenta" => $renta,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => floatval(number_format($totalGravado - $renta, 2)),
                        "totalLetras" => $totalLetras,
                        "totalNoOnerosas" => 0.0,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0
                ];

                $tipoDocumento = "13";
                $numDocumento = $cliente["DUI"];
            }
            

            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoEvento" => "18",
                        "tipoContingencia" => null,
                        "motivoContin" => null,
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "fusion" => null,
                        "tipoMoneda" => "USD"
                    ],
                    "documentoRelacionado" => [
                        [
                        "tipoDocumento" => $facturaOriginal["tipoDte"],
                        "codigoGeneracion" => $facturaOriginal["codigoGeneracion"],
                        "fechaEmision" => $facturaOriginal["fecEmi"]
                        ]
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nombre" => $empresa["nombre"],
                        "codEstableMH" => "M001",
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => "P001",
                        "codPuntoVenta" => "P001",
                        "recintoFiscal" => empty($factura["recintoFiscal"]) ? null : $factura["recintoFiscal"],
                        "tipoRegimen" => empty($factura["tipo_regimen"]) ? null : $factura["tipo_regimen"],
                        "regimen" => empty($factura["regimen"]) ? null : $factura["regimen"],
                        "tipoItemExpor" => 1 // Solo para vender bienes
                    ],
                    "documento" => [
                        "tipoDocumento" => $tipoDocumento,
                        "numDocumento" => $numDocumento,
                        "nombre" => $cliente["nombre"],
                        "codPais" => $cliente["codPais"],
                        "nombrePais" => $cliente["nombrePais"],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "ventaTercero" => null,
                    "compraTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => $resumen,
                    "apendice" => null
                ]
            ];
            //echo json_encode($data);
            //return;
            
        }

        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "facturas_locales";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFactura;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $data["firmaElectronica"] = $bodyContent;

            $tabla = "facturas_locales";
            $item1 = "json_guardado";
            $valor1 = json_encode($data);
            $item2 = "id";
            $valor2 = $this->idFactura;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);



	}

    /*=============================================
	FIRMAR FACTURA CONTINGENCIA
	=============================================*/	
	public $idFacturaContingencia;

	public function ajaxEnviarFacturaContingencia() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idFacturaContingencia;
        $optimizacion = "no";

		$factura = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        
        
        $item = "id";
        $orden = "id";
        $valor = $factura["id_cliente"];

        $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = $factura["idMotorista"];

        $motorista = ControladorClientes::ctrMostrarMotoristas($item, $valor, $orden);

        // Decodificar los productos de la factura
        $productos = json_decode($factura["productos"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $cuerpoDocumento = [];

        // Número de ítem inicial
        $numItem = 1;
        $descuentoGobal = 0;
        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "00"){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                // Calcular el IVA individual del producto
                $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                if($productoLei["exento_iva"] == "no"){
                    $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                } else {
                    $ivaItem = 0.0;
                }
                
                // Formatea el resultado a 2 decimales
                $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => $ivaItemTotalFormateado
                ];
                
                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = 0.0;
                    $item["ventaGravada"] = $totalProF;
                } else {
                    $item["ventaNoSuj"] = 0.0;
                    $item["ventaExenta"] = $totalProF;
                    $item["ventaGravada"] = 0.0;
                }
                
                // Agregar el item al array final
                $cuerpoDocumento[] = $item;    

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
            }

            $descuentoGobalF = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["total"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["total"]),

                        "subTotalVentas" => floatval($factura["total"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["total"]),
                        "ivaRete1" => 0.0,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"]), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" &&  $cliente["tipo_cliente"] == "01"){ // Factura, persona normal y persona que declara IVA - empresa
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                if($factura["gran_contribuyente"] == "Si"){
                    $nuevoPrecioSinImpuestos = round(($producto["precioSinImpuestos"] - ($producto["precioSinImpuestos"] * 0.01)), 2);
                    $nuevoPrecioConIva = round(($nuevoPrecioSinImpuestos + ($nuevoPrecioSinImpuestos * 0.13)), 2);
                    $nuevoTotalProducto = round(($nuevoPrecioConIva * $producto["cantidad"]), 2);
                    $nuevoDescuento = round(($producto["descuento"] - ($producto["descuento"] * 0.01)), 2);
                    $nuevoDescuentoConIva = round(($nuevoDescuento +  ($nuevoDescuento * 0.13)), 2);

                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($nuevoDescuentoConIva * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($nuevoTotalProducto - $descTotal) - (($nuevoTotalProducto - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));

                    $totalPro = ($nuevoPrecioConIva - $nuevoDescuentoConIva) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $nuevoPrecioConIva, // Precio con impuestos del producto
                        "montoDescu" => $nuevoDescuentoConIva * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  

                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $nuevoDescuentoConIva * $producto["cantidad"];
                } else {
                    $item = "id";
                    $valor = $producto["idProducto"];
                
                    $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                    // Calcular el IVA individual del producto
                    $descTotal = ($producto["descuentoConIva"] * $producto["cantidad"]);
                    if($productoLei["exento_iva"] == "no"){
                        $ivaItem = ($producto["totalProducto"] - $descTotal) - (($producto["totalProducto"] - $descTotal) / 1.13);
                    } else {
                        $ivaItem = 0.0;
                    }
                    
                    // Formatea el resultado a 2 decimales
                    $ivaItemTotalFormateado = floatval(number_format($ivaItem, 2, '.', ''));
    
                    $totalPro = ($producto["precioConIva"] - $producto["descuentoConIva"]) * $producto["cantidad"];
                    $totalProF = floatval(number_format($totalPro, 2, '.', ''));
    
                    $item = [
                        "numItem" => $numItem,
                        "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                        "numeroDocumento" => null,
                        "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                        "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                        "codTributo" => null,
                        "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                        "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                        "precioUni" => $producto["precioConIva"], // Precio con impuestos del producto
                        "montoDescu" => $producto["descuentoConIva"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                        "tributos" => null,
                        "psv" => 0,
                        "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                        "ivaItem" => $ivaItemTotalFormateado
                    ];
                    
                    // Agregar las claves según la condición
                    if ($productoLei["exento_iva"] == "no") {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = 0.0;
                        $item["ventaGravada"] = $totalProF;
                    } else {
                        $item["ventaNoSuj"] = 0.0;
                        $item["ventaExenta"] = $totalProF;
                        $item["ventaGravada"] = 0.0;
                    }
                    
                    // Agregar el item al array final
                    $cuerpoDocumento[] = $item;  
    
                    // Incrementar el número de ítem
                    $numItem++;
                    $descuentoGobal += $producto["descuentoConIva"] * $producto["cantidad"];
                }
                
            }
            if($factura["gran_contribuyente"] == "Si"){
                $nuevoTotalSinIva = round(($factura["totalSinIva"] - ($factura["totalSinIva"] * 0.01)), 2);
                $nuevoTotal = round(($nuevoTotalSinIva + ($nuevoTotalSinIva * 0.13)), 2);
            } else {
                $nuevoTotalSinIva = $factura["totalSinIva"];
                $nuevoTotal = $factura["total"];
            }

            $descuentoGobalF = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($nuevoTotal));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($nuevoTotal),

                        "subTotalVentas" => floatval($nuevoTotal),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($nuevoTotal),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($nuevoTotal),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($nuevoTotal - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => $ivaTotalF,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "02"){ // Factura, empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "01" && $cliente["tipo_cliente"] == "03"){ // Factura, diplomáticos
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                    "ivaItem" => 0.0
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));

            $ivaSacar = $factura["total"] - $factura["totalSinIva"];

            // Formatea el resultado a 8 decimales
            $ivaTotalF = floatval(number_format($ivaSacar, 2, '.', ''));

            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingenca"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nrc" => $ncrCliente,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => floatval($factura["totalSinIva"]),
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "tributos" => null,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "totalIva" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "01"){ // CCF, Declarante IVA - Empresa
            $sinIva = 0;
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);

                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $item = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Agregar las claves según la condición
                if ($productoLei["exento_iva"] == "no") {
                    $item["tributos"] = ["20"];
                } else {
                    $item["tributos"] = null;
                    $sinIva += $totalProF;
                }

                // Agregar el item al array final
                $cuerpoDocumento[] = $item;  
                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $retencionGranContribuyente = 0.0;
            if($factura["gran_contribuyente"] == "Si"){
                $retencionGranContribuyente = round(($factura["totalSinIva"] * 0.01), 2);
            }

            $totalLetras = convertirMontoALetras(floatval($factura["total"] - $retencionGranContribuyente));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => 0.0,
                        "totalGravada" => floatval($factura["totalSinIva"]),

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => $retencionGranContribuyente,
                        "tributos" => [
                                [
                                    "codigo" => "20",
                                    "descripcion" => "Impuesto al Valor Agregado 13%",
                                    "valor" => round(($factura["totalSinIva"] - $sinIva) * 0.13, 2)
                                ]
                        ],
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["total"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["total"] - $retencionGranContribuyente), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "02"){ // CCF, Empresa con beneficios fiscales
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => $totalProF, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => 0.0, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => floatval($factura["totalSinIva"]),
                        "totalExenta" => 0.0,
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "03" && $cliente["tipo_cliente"] == "03"){ // CCF, Diplomáticos
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "numeroDocumento" => null,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "codTributo" => null,
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaNoSuj" => 0.0, // Suponiendo que el producto no tiene venta no sujeta
                    "ventaExenta" => $totalProF, // Suponiendo que el producto no tiene venta exenta
                    "ventaGravada" => 0.0, // Valor de venta gravada
                    "tributos" => null,
                    "psv" => 0,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }
            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "receptor" => [
                        "nrc" => $ncrCliente,
                        "nit" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "nombre" => $cliente["nombre"],
                        "codActividad" => $cliente["codActividad"],
                        "descActividad" => $cliente["descActividad"],
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => null,
                    "documentoRelacionado" => null,
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalNoSuj" => 0.0,
                        "totalExenta" => floatval($factura["totalSinIva"]),
                        "totalGravada" => 0.0,

                        "subTotalVentas" => floatval($factura["totalSinIva"]),
                        "descuNoSuj" => 0.0,
                        "descuExenta" => 0.0,
                        "descuGravada" => 0.0,
                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "tributos" => null,
                        "reteRenta" => 0.0,
                        "montoTotalOperacion" => floatval($factura["totalSinIva"]),
                        "totalNoGravado" => 0.0,
                        "totalPagar" => round(($factura["totalSinIva"]), 2),
                        "totalLetras" => $totalLetras,
                        "ivaPerci1" => 0.0,
                        "saldoFavor" => 0.0,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "numPagoElectronico" => null
                    ],
                    "extension" => [
                        "nombEntrega" => null,
                        "docuEntrega" => null,
                        "nombRecibe" => null,
                        "docuRecibe" => null,
                        "observaciones" => null,
                        "placaVehiculo" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "11" && ($cliente["tipo_cliente"] == "01" || $cliente["tipo_cliente"] == "02" || $cliente["tipo_cliente"] == "03")){ // Exportación, Declarante IVA - Empresa
            
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));
                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "ventaGravada" => $totalProF, // Valor de venta gravada
                    "tributos" => null,
                    "noGravado" => 0.0, // Suponiendo que el producto no tiene no gravado
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            $totalLetras = convertirMontoALetras(floatval($factura["totalSinIva"]));
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }

            
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            $totalOpera = $factura["flete"] + $factura["seguro"] + $factura["totalSinIva"];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => intval($factura["tipo_contingencia"]),
                        "motivoContigencia" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "nombreComercial" => null,
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"],
                        "codEstableMH" => null,
                        "codEstable" => "M001",
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "tipoItemExpor" => 1, // Solo para vender bienes
                        "recintoFiscal" => $factura["recintoFiscal"],
                        "regimen" => $factura["regimen"],
                    ],
                    "receptor" => [
                        "nombre" => $cliente["nombre"],
                        "tipoDocumento" => "36",
                        "numDocumento" => $cliente["NIT"],
                        "nombreComercial" => null,
                        "codPais" => $cliente["codPais"],
                        "nombrePais" => $cliente["nombrePais"],
                        "complemento" => $cliente["direccion"],
                        "tipoPersona" => intval($cliente["tipoPersona"]),
                        "descActividad" => $cliente["descActividad"],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "otrosDocumentos" => [
                        [
                            "codDocAsociado" => 4, // de transporte
                            "descDocumento" => null,
                            "detalleDocumento" => null,
                            "placaTrans" => $motorista["placaMotorista"],
                            "modoTransp" => intval($factura["modoTransporte"]),
                            "numConductor" => $motorista["duiMotorista"],
                            "nombreConductor" => $motorista["nombre"]
                        ]
                    ],
                    "ventaTercero" => null,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalGravada" => floatval($factura["totalSinIva"]),
                        "descuento" => 0.0,

                        "porcentajeDescuento" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "seguro" => floatval($factura["seguro"]),
                        "flete" => floatval($factura["flete"]),
                        "montoTotalOperacion" => $totalOpera,
                        "totalNoGravado" => 0.0,
                        "totalPagar" => $totalOpera,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "codIncoterms" => isset($factura["cod_incoterms"]) && $factura["cod_incoterms"] !== "" ? $factura["cod_incoterms"] : null,
                        "descIncoterms" => isset($factura["desc_incoterms"]) && $factura["desc_incoterms"] !== "" ? $factura["desc_incoterms"] : null,
                        "numPagoElectronico" => null,
                        "observaciones" => null
                    ],
                    "apendice" => null
                ]
            ];


        }

        if($factura["tipoDte"] == "14" && $cliente["tipo_cliente"] == "00"){ // Factura sujeto excluido, persona normal
            // Recorrer cada producto y mapear los datos
            foreach ($productos as $producto) {
                $item = "id";
                $valor = $producto["idProducto"];
            
                $productoLei = ControladorProductos::ctrMostrarProductos($item, $valor);
                
                $totalPro = ($producto["precioSinImpuestos"] - $producto["descuento"]) * $producto["cantidad"];
                $totalProF = floatval(number_format($totalPro, 2, '.', ''));

                $cuerpoDocumento[] = [
                    "numItem" => $numItem,
                    "tipoItem" => intval($productoLei["tipo"]), // Puedes ajustarlo según sea necesario
                    "cantidad" => $producto["cantidad"], // Asumiendo que el campo "cantidad" está en los datos del producto
                    "codigo" => strval($producto["codigo"]), // Asumiendo que el campo "codigo" está en los datos del producto                    
                    "uniMedida" => intval($productoLei["unidadMedida"]), // Puedes ajustar el valor si es diferente
                    "descripcion" => $productoLei["descripcion"], // Asumiendo que el campo "descripcion" está en los datos del producto
                    "precioUni" => $producto["precioSinImpuestos"], // Precio con impuestos del producto
                    "montoDescu" => $producto["descuento"] * $producto["cantidad"], // Si no hay descuentos, puedes dejarlo en 0
                    "compra" => $totalProF, // Valor de venta gravada
                    
                ];

                // Incrementar el número de ítem
                $numItem++;
                $descuentoGobal += $producto["descuento"] * $producto["cantidad"];
            }

            $descuentoGobalF  = floatval(number_format($descuentoGobal, 2, '.', ''));
            
            function convertirMontoALetras($monto) {
                // Separar la parte entera y la parte decimal
                $partes = explode('.', number_format($monto, 2, '.', ''));
                $parteEntera = (int)$partes[0];
                $parteDecimal = str_pad($partes[1], 2, '0', STR_PAD_RIGHT); // Siempre dos decimales
            
                // Convertir la parte entera a letras
                $parteEnteraLetras = convertirNumeroALetras($parteEntera);
            
                // Formato final "UNO 67/100"
                return strtoupper("{$parteEnteraLetras} {$parteDecimal}/100");
            }
            
            function convertirNumeroALetras($numero) {
                $unidades = ["cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
                $decenas = [
                    "", "diez", "veinte", "treinta", "cuarenta", "cincuenta", 
                    "sesenta", "setenta", "ochenta", "noventa"
                ];
                $especiales = [
                    10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 
                    14 => "catorce", 15 => "quince", 16 => "dieciséis", 
                    17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
                ];
            
                if ($numero < 10) {
                    return $unidades[$numero];
                } elseif ($numero < 20) {
                    return $especiales[$numero];
                } elseif ($numero < 100) {
                    $decena = (int)($numero / 10);
                    $unidad = $numero % 10;
                    return $unidad ? "{$decenas[$decena]} y {$unidades[$unidad]}" : $decenas[$decena];
                } elseif ($numero < 1000) {
                    $centena = (int)($numero / 100);
                    $resto = $numero % 100;
                    $centenaLetras = $centena == 1 ? "ciento" : ($centena == 5 ? "quinientos" : "{$unidades[$centena]}cientos");
                    return $resto ? "{$centenaLetras} " . convertirNumeroALetras($resto) : ($centena == 1 ? "cien" : $centenaLetras);
                } elseif ($numero < 1000000) {
                    $miles = (int)($numero / 1000);
                    $resto = $numero % 1000;
                    $milesLetras = $miles == 1 ? "mil" : convertirNumeroALetras($miles) . " mil";
                    return $resto ? "{$milesLetras} " . convertirNumeroALetras($resto) : $milesLetras;
                } else {
                    return "Número demasiado grande";
                }
            }

            
            $totalProF = floatval(number_format($totalPro, 2, '.', ''));
            $renta = floatval(number_format(($factura["totalSinIva"] * 0.10), 2, '.', ''));
            $totalSinRenta = floatval(number_format(($factura["totalSinIva"] - $renta), 2, '.', ''));
            $totalLetras = convertirMontoALetras(floatval($totalSinRenta));
            
            $ncrCliente = "";
            if($cliente["NRC"] == "") {
                $ncrCliente = null;
            } else {
                $ncrCliente = $cliente["NRC"];
            }
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 1,
                        "ambiente" => "01",
                        "tipoDte" => $factura["tipoDte"],
                        "numeroControl" => $factura["numeroControl"],
                        "codigoGeneracion" => $factura["codigoGeneracion"],
                        "tipoModelo" => 1,
                        "tipoOperacion" => 1,
                        "tipoContingencia" => $factura["tipo_contingencia"],
                        "motivoContin" => $factura["motivo_contingencia"],
                        "fecEmi" => $factura["fecEmi"],
                        "horEmi" => $factura["horEmi"],
                        "tipoMoneda" => "USD"
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nrc" => $empresa["nrc"],
                        "nombre" => $empresa["nombre"],
                        "codActividad" => $empresa["codActividad"],
                        "descActividad" => $empresa["desActividad"],
                        "direccion" => [
                            "departamento" => $empresa["departamento"],
                            "municipio" => $empresa["municipio"],
                            "distrito" => $empresa["distrito"],
                            "complemento" => $empresa["direccion"]
                        ],
                        "telefono" => $empresa["telefono"],
                        "codEstable" => "M001",
                        "codEstableMH" => null,
                        "codPuntoVentaMH" => null,
                        "codPuntoVenta" => "P001",
                        "correo" => $empresa["correo"]
                    ],
                    "sujetoExcluido" => [
                        "tipoDocumento" => "13",
                        "numDocumento" => $cliente["DUI"],
                        "nombre" => $cliente["nombre"],
                        "codActividad" => null,
                        "descActividad" => null,
                        "direccion" => [
                            "departamento" => $cliente["departamento"],
                            "municipio" => $cliente["municipio"],
                            "distrito" => $cliente["distrito"],
                            "complemento" => $cliente["direccion"]
                        ],
                        "telefono" => $cliente["telefono"],
                        "correo" => $cliente["correo"]
                    ],
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => [
                        "totalCompra" => floatval($factura["totalSinIva"]),
                        "descu" => 0.0,
                        "totalDescu" => $descuentoGobalF,
                        "subTotal" => floatval($factura["totalSinIva"]),
                        "ivaRete1" => 0.0,
                        "reteRenta" => $renta,
                        "totalPagar" => $totalSinRenta,
                        "totalLetras" => $totalLetras,
                        "condicionOperacion" => floatval($factura["condicionOperacion"]),
                        "pagos" => null,
                        "observaciones" => null,

                    ],
                    "apendice" => null
                ]
            ];


        }
        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "facturas_locales";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idFacturaContingencia;

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);

            $data["firmaElectronica"] = $bodyContent;

            $tabla = "facturas_locales";
            $item1 = "json_guardado";
            $valor1 = json_encode($data);
            $item2 = "id";
            $valor2 = $this->idFacturaContingencia;

            $respuesta2 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}

    /*=============================================
	FIRMAR EVENTO CONTINGENCIA
	=============================================*/	
	public $idEventoContingencia;

	public function ajaxFirmarEventoContingencia() {
        

        $item = "id";
        $orden = "id";
		$valor = $this->idEventoContingencia;

		$evento = ControladorFacturas::ctrMostrarEventosContingencias($item, $valor, $orden);

        $item = "id";
        $orden = "id";
        $valor = "1";

        $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

        // Decodificar los ids de la factura
        $facturasIds = json_decode($evento["ids_facturas"], true); // true para obtener un array asociativo

        // Inicializar el array cuerpoDocumento
        $detalleDTE = [];

        // Número de ítem inicial
        $numItem = 1;
        
        
        // Recorrer cada producto y mapear los datos
        foreach ($facturasIds as $facturaId) {

            $item = "id";
            $valor = $facturaId;
            $orden = "id";
            $optimizacion = "no";
        
            $facturaOriginal = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

            $detalleDTE[] = [
                "noItem" => $numItem,
                "codigoGeneracion" => $facturaOriginal["codigoGeneracion"], // Puedes ajustarlo según sea necesario
                "tipoDoc" => $facturaOriginal["tipoDte"]
            ];

            // Incrementar el número de ítem
            $numItem++;
        }
        
            // URL de la solicitud
            $url = "http://localhost:8113/firmardocumento/";

            // Configuración de los encabezados
            $headers = [
                'User-Agent: facturacion',
                'Content-Type: application/json'
            ];

            // Establecer la zona horaria de El Salvador
	        date_default_timezone_set('America/El_Salvador');

            // Datos del JSON (estructura de ejemplo)
            $data = [
                "contentType" => "application/JSON",
                "nit" => $empresa["nit"],
                "activo" => true,
                "passwordPri" => $empresa["passwordPri"],
                "dteJson" => [
                    "identificacion" => [
                        "version" => 3,
                        "ambiente" => "01",
                        "codigoGeneracion" => $evento["codigoGeneracion"],
                        "fTransmision" => date("Y-m-d"), // Fecha actual en formato YYYY-MM-DD
                        "hTransmision" => date("H:i:s")
                    ],
                    "emisor" => [
                        "nit" => $empresa["nit"],
                        "nombre" => $empresa["nombre"],
                        "nombreResponsable" => $empresa["nombre"],
                        "tipoDocResponsable" => "36",
                        "numeroDocResponsable" => $empresa["nit"],
                        "tipoEstablecimiento" => $empresa["tipoEstablecimiento"],
                        "codEstableMH" => null,
                        "codPuntoVenta" => "P001",
                        "telefono" => $empresa["telefono"],
                        "correo" => $empresa["correo"]
                    ],
                    "detalleDTE" => $detalleDTE,
                    "motivo" => [
                        "fInicio" => $evento["fecha_inicio"], // Fecha de inicio de la contingencia
                        "fFin" => $evento["fecha_fin"],   // Fecha de fin de la contingencia
                        "hInicio" => $evento["hora_inicio"],  // Hora de inicio de la contingencia
                        "hFin" => $evento["hora_fin"],     // Hora de fin de la contingencia
                        "tipoContingencia" => intval($evento["tipo_contingencia"]),  // Tipo de contingencia
                        "motivoContingencia" => $evento["motivo_contingencia"] // Convertir a entero

                    ]
                ]
            ];

            
        
        
        // Convertir el array PHP a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar cURL para enviar datos JSON en una solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);
        
        // Verificar si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode(['error' => curl_error($ch)]);
        } else {
            
            // Decodificar la respuesta del servidor
            $decodedResponse = json_decode($response, true);

            // Acceder al campo "body" de la respuesta
            $bodyContent = $decodedResponse['body'] ?? null;

            $tabla = "contingencias";
            $item1 = "firmaDigital";
            $valor1 = $bodyContent;
            $item2 = "id";
            $valor2 = $this->idEventoContingencia;

            

            $respuesta1 = ModeloFacturas::mdlActualizarFactura($tabla, $item1, $valor1, $item2, $valor2);
            
            if($respuesta1 == "ok"){
                echo json_encode("si");
            } else {
                echo json_encode("no"); // Ahora ambos resultados están en formato JSON
            }
            


        }

        // Cerrar la sesión cURL
        curl_close($ch);


	}

    /*=============================================
	INICIAR SESIÓN EN MH
	=============================================*/	

    public function iniciarSesionMh() {
        // URL de la API a la que quieres enviar el POST
        $url = 'https://api.dtes.mh.gob.sv/seguridad/auth'; // Cambia esto a la URL de tu API
    
        // Datos en formato x-www-form-urlencoded
        $data = [
            'user' => '06172710001020',
            'pwd' => 'Mampi0712_'
        ];
        
        // Convertimos los datos a formato x-www-form-urlencoded
        $dataString = http_build_query($data);
    
        // Inicializamos cURL
        $ch = curl_init($url);
    
        // Configuramos la solicitud POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: facturacion'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        

        // Ejecutamos la solicitud
        $response = curl_exec($ch);
    
        // Verificamos si hubo algún error
        if (curl_errno($ch)) {
            echo json_encode("no");
        } else {
            // Decodificamos la respuesta JSON
            $responseData = json_decode($response, true); // Decodificamos la respuesta como un array asociativo
    
            // Verificamos si el token está en la respuesta
            if (isset($responseData['body']['token'])) {
                // Almacenamos el token en la sesión
                $_SESSION["tokenInicioSesionMh"] = $responseData['body']['token'];
                echo json_encode("si");
            } else {
                // Manejo de error si no se encuentra el token
                echo json_encode("no");
            }
        }
    
        // Cerramos cURL
        curl_close($ch);
    }
    


}

/*=============================================
SELLAR FACTURA
=============================================*/
if (isset($_POST["idFacturaS"])) {
    $sellar = new AjaxFacturas();
    $sellar->idFacturaS = $_POST["idFacturaS"];
    $sellar->ajaxSellarFactura();
}

/*=============================================
VER FIRMA FACTURA
=============================================*/
if (isset($_POST["idFacturaVerFirma"])) {
    $verFirma = new AjaxFacturas();
    $verFirma->idFacturaVerFirma = $_POST["idFacturaVerFirma"];
    $verFirma->ajaxVerFirmaFactura();
}

/*=============================================
LIMPIAR FIRMA FACTURA
=============================================*/
if (isset($_POST["idFacturaLimpiarFirma"])) {
    $limpiarFirma = new AjaxFacturas();
    $limpiarFirma->idFacturaLimpiarFirma = $_POST["idFacturaLimpiarFirma"];
    $limpiarFirma->ajaxLimpiarFirmaFactura();
}

/*=============================================
VER FIRMA INVALIDACION
=============================================*/
if (isset($_POST["idAnulacionVerFirma"])) {
    $verFirma = new AjaxFacturas();
    $verFirma->idAnulacionVerFirma = $_POST["idAnulacionVerFirma"];
    $verFirma->ajaxVerFirmaAnulacion();
}

/*=============================================
LIMPIAR FIRMA INVALIDACION
=============================================*/
if (isset($_POST["idAnulacionLimpiarFirma"])) {
    $limpiarFirma = new AjaxFacturas();
    $limpiarFirma->idAnulacionLimpiarFirma = $_POST["idAnulacionLimpiarFirma"];
    $limpiarFirma->ajaxLimpiarFirmaAnulacion();
}

/*=============================================
DESCARGAR JSON GUARDADO
=============================================*/
if (isset($_POST["idFacturaJsonGuardado"])) {
    $jsonGuardado = new AjaxFacturas();
    $jsonGuardado->idFacturaJsonGuardado = $_POST["idFacturaJsonGuardado"];
    $jsonGuardado->ajaxJsonGuardadoFactura();
}

/*=============================================
GENERAR EVENTOS DE RETORNO DE PRUEBA
=============================================*/
if (isset($_POST["generarEventosRetornoPrueba"])) {
    $pruebas = new AjaxFacturas();
    $pruebas->ajaxGenerarEventosRetornoPrueba();
}

/*=============================================
SELLAR EVENTO CONTINGENCIA
=============================================*/
if (isset($_POST["idEvento"])) {
    
    $sellar = new AjaxFacturas();
    $sellar->idEventoContingencia = $_POST["idEvento"];
    $sellar->ajaxFirmarEventoContingencia();
}

/*=============================================
SELLAR EVENTO CONTINGENCIA
=============================================*/
if (isset($_POST["idEventoH"])) {
    
    $sellarA = new AjaxFacturas();
    $sellarA->idEventoH = $_POST["idEventoH"];
    $sellarA->ajaxSellarEvento();
}

/*=============================================
SELLAR ANULACION
=============================================*/
if (isset($_POST["idFacturaSA"])) {
    
    $sellarA = new AjaxFacturas();
    $sellarA->idFacturaSA = $_POST["idFacturaSA"];
    $sellarA->ajaxSellarAnulacion();
}


/*=============================================
FIRMAR FACTURA
=============================================*/
if (isset($_POST["idFacturaF"])) {
    $firmar = new AjaxFacturas();
    $firmar->idFactura = $_POST["idFacturaF"];
    $firmar->ajaxEnviarFactura();
}

/*=============================================
FIRMAR FACTURA CONTINGENCIA
=============================================*/
if (isset($_POST["idFacturaFContingencia"])) {
    
    $firmar = new AjaxFacturas();
    $firmar->idFacturaContingencia = $_POST["idFacturaFContingencia"];
    $firmar->ajaxEnviarFacturaContingencia();
}

/*=============================================
FIRMAR DTE A ANULAR
=============================================*/
if (isset($_POST["idFacturaFA"])) {
    
    $firmarA = new AjaxFacturas();
    $firmarA->idFacturaA = $_POST["idFacturaFA"];
    $firmarA->ajaxAnularDTE();
}

/*=============================================
INICIAR SESIÓN EN MH
=============================================*/
if (isset($_POST["iniciarSesionMh"])) {
    $iniciar = new AjaxFacturas();
    $iniciar->iniciarSesionMh();
}
