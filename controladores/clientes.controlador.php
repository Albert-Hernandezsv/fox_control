<?php

class ControladorClientes{

	/*=============================================
	REGISTRO DE CLIENTE
	=============================================*/

	static public function ctrCrearCliente(){

		if(isset($_POST["nuevoNombreCliente"])){

			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.]+$/', $_POST["nuevoNombreCliente"])) {
				// Array de códigos de país con sus nombres
				$paises = [
                            "AF" => "Afganistán",
                            "AX" => "Aland",
                            "AL" => "Albania",
                            "DE" => "Alemania",
                            "AD" => "Andorra",
                            "AO" => "Angola",
                            "AI" => "Anguila",
                            "AQ" => "Antártica",
                            "AG" => "Antigua y Barbuda",
                            "AW" => "Aruba",
                            "SA" => "Arabia Saudita",
                            "DZ" => "Argelia",
                            "AR" => "Argentina",
                            "AM" => "Armenia",
                            "AU" => "Australia",
                            "AT" => "Austria",
                            "AZ" => "Azerbaiyán",
                            "BS" => "Bahamas",
                            "BH" => "Bahrein",
                            "BD" => "Bangladesh",
                            "BB" => "Barbados",
                            "BE" => "Bélgica",
                            "BZ" => "Belice",
                            "BJ" => "Benin",
                            "BM" => "Bermudas",
                            "BY" => "Bielorrusia",
                            "BO" => "Bolivia",
                            "BQ" => "Bonaire, Sint Eustatius and Saba",
                            "BA" => "Bosnia-Herzegovina",
                            "BW" => "Botswana",
                            "BR" => "Brasil",
                            "BN" => "Brunei",
                            "BG" => "Bulgaria",
                            "BF" => "Burkina Faso",
                            "BI" => "Burundi",
                            "BT" => "Bután",
                            "CV" => "Cabo Verde",
                            "KY" => "Caimán, Islas",
                            "KH" => "Camboya",
                            "CM" => "Camerún",
                            "CA" => "Canadá",
                            "CF" => "Centroafricana, República",
                            "TD" => "Chad",
                            "CL" => "Chile",
                            "CN" => "China",
                            "CY" => "Chipre",
                            "VA" => "Ciudad del Vaticano",
                            "CO" => "Colombia",
                            "KM" => "Comoras",
                            "CG" => "Congo",
                            "CI" => "Costa de Marfil",
                            "CR" => "Costa Rica",
                            "HR" => "Croacia",
                            "CU" => "Cuba",
                            "CW" => "Curazao",
                            "DK" => "Dinamarca",
                            "DM" => "Dominica",
                            "DJ" => "Djiboutí",
                            "EC" => "Ecuador",
                            "EG" => "Egipto",
                            "SV" => "El Salvador",
                            "AE" => "Emiratos Árabes Unidos",
                            "ER" => "Eritrea",
                            "SK" => "Eslovaquia",
                            "SI" => "Eslovenia",
                            "ES" => "España",
                            "US" => "Estados Unidos",
                            "EE" => "Estonia",
                            "ET" => "Etiopía",
                            "FJ" => "Fiji",
                            "PH" => "Filipinas",
                            "FI" => "Finlandia",
                            "FR" => "Francia",
                            "GA" => "Gabón",
                            "GM" => "Gambia",
                            "GE" => "Georgia",
                            "GH" => "Ghana",
                            "GI" => "Gibraltar",
                            "GD" => "Granada",
                            "GR" => "Grecia",
                            "GL" => "Groenlandia",
                            "GP" => "Guadalupe",
                            "GU" => "Guam",
                            "GT" => "Guatemala",
                            "GF" => "Guayana Francesa",
                            "GG" => "Guernsey",
                            "GN" => "Guinea",
                            "GQ" => "Guinea Ecuatorial",
                            "GW" => "Guinea-Bissau",
                            "GY" => "Guyana",
                            "HT" => "Haití",
                            "HN" => "Honduras",
                            "HK" => "Hong Kong",
                            "HU" => "Hungría",
                            "IN" => "India",
                            "ID" => "Indonesia",
                            "IQ" => "Irak",
                            "IE" => "Irlanda",
                            "BV" => "Isla Bouvet",
                            "IM" => "Isla de Man",
                            "NF" => "Isla Norfolk",
                            "IS" => "Islandia",
                            "CX" => "Islas Navidad",
                            "CC" => "Islas Cocos",
                            "CK" => "Islas Cook",
                            "FO" => "Islas Faroe",
                            "GS" => "Islas Georgias del Sur y Sandwich del Sur",
                            "HM" => "Islas Heard y McDonald",
                            "FK" => "Islas Malvinas",
                            "MP" => "Islas Marianas del Norte",
                            "MH" => "Islas Marshall",
                            "PN" => "Islas Pitcairn",
                            "TC" => "Islas Turcas y Caicos",
                            "UM" => "Islas Ultramarinas de E.E.U.U",
                            "VI" => "Islas Vírgenes",
                            "IL" => "Israel",
                            "IT" => "Italia",
                            "JM" => "Jamaica",
                            "JP" => "Japón",
                            "JE" => "Jersey",
                            "JO" => "Jordania",
                            "KZ" => "Kazajistán",
                            "KE" => "Kenia",
                            "KG" => "Kirguistán",
                            "KI" => "Kiribati",
                            "KW" => "Kuwait",
                            "LA" => "Laos, República Democrática",
                            "LS" => "Lesotho",
                            "LV" => "Letonia",
                            "LB" => "Líbano",
                            "LR" => "Liberia",
                            "LY" => "Libia",
                            "LI" => "Liechtenstein",
                            "LT" => "Lituania",
                            "LU" => "Luxemburgo",
                            "MO" => "Macao",
                            "MK" => "Macedonia",
                            "MG" => "Madagascar",
                            "MY" => "Malasia",
                            "MW" => "Malawi",
                            "MV" => "Maldivas",
                            "ML" => "Malí",
                            "MT" => "Malta",
                            "MA" => "Marruecos",
                            "MQ" => "Martinica",
                            "MU" => "Mauricio",
                            "MR" => "Mauritania",
                            "YT" => "Mayotte",
                            "MX" => "México",
                            "FM" => "Micronesia",
                            "MD" => "Moldavia",
                            "MC" => "Mónaco",
                            "MN" => "Mongolia",
                            "ME" => "Montenegro",
                            "MS" => "Montserrat",
                            "MZ" => "Mozambique",
                            "MM" => "Myanmar",
                            "NA" => "Namibia",
                            "NR" => "Nauru",
                            "NP" => "Nepal",
                            "NI" => "Nicaragua",
                            "NE" => "Níger",
                            "NG" => "Nigeria",
                            "NU" => "Niue",
                            "NO" => "Noruega",
                            "NC" => "Nueva Caledonia",
                            "NZ" => "Nueva Zelanda",
                            "OM" => "Omán",
                            "NL" => "Países Bajos",
                            "PK" => "Pakistán",
                            "PW" => "Palaos",
                            "PS" => "Palestina",
                            "PA" => "Panamá",
                            "PG" => "Papúa Nueva Guinea",
                            "PY" => "Paraguay",
                            "PE" => "Perú",
                            "PF" => "Polinesia Francesa",
                            "PL" => "Polonia",
                            "PT" => "Portugal",
                            "PR" => "Puerto Rico",
                            "QA" => "Qatar",
                            "GB" => "Reino Unido",
                            "KR" => "República de Corea",
                            "CZ" => "República Checa",
                            "DO" => "República Dominicana",
                            "IR" => "República Islámica de Irán",
                            "RE" => "Reunión",
                            "RW" => "Ruanda",
                            "RO" => "Rumania",
                            "RU" => "Rusia",
                            "EH" => "Sahara Occidental",
                            "BL" => "Saint Barthélemy",
                            "MF" => "Saint Martin (French part)",
                            "SB" => "Salomón, Islas",
                            "WS" => "Samoa",
                            "AS" => "Samoa Americana",
                            "KN" => "San Cristóbal y Nieves",
                            "SM" => "San Marino",
                            "PM" => "San Pedro y Miquelón",
                            "VC" => "San Vicente y las Granadinas",
                            "SH" => "Santa Elena",
                            "LC" => "Santa Lucía",
                            "ST" => "Santo Tomé y Príncipe",
                            "SN" => "Senegal",
                            "RS" => "Serbia",
                            "SC" => "Seychelles",
                            "SL" => "Sierra Leona",
                            "SG" => "Singapur",
                            "SX" => "Sint Maarten (Dutch part)",
                            "SY" => "Siria",
                            "SO" => "Somalía",
                            "SS" => "South Sudan",
                            "LK" => "Sri Lanka",
                            "ZA" => "Sudáfrica",
                            "SD" => "Sudán",
                            "SE" => "Suecia",
                            "CH" => "Suiza",
                            "SR" => "Surinam",
                            "SJ" => "Svalbard y Jan Mayen",
                            "SZ" => "Swazilandia",
                            "TH" => "Tailandia",
                            "TW" => "Taiwán",
                            "TZ" => "Tanzania",
                            "TJ" => "Tayikistán",
                            "IO" => "Territorio Británico Océano Índico",
                            "TF" => "Territorios Australes Franceses",
                            "TL" => "Timor Oriental",
                            "TG" => "Togo",
                            "TK" => "Tokelau",
                            "TO" => "Tonga",
                            "TT" => "Trinidad y Tobago",
                            "TN" => "Túnez",
                            "TM" => "Turkmenistán",
                            "TR" => "Turquía",
                            "TV" => "Tuvalu",
                            "UA" => "Ucrania",
                            "UG" => "Uganda",
                            "UY" => "Uruguay",
                            "UZ" => "Uzbekistán",
                            "VU" => "Vanuatu",
                            "VE" => "Venezuela",
                            "VN" => "Vietnam",
                            "VG" => "Islas Vírgenes Británicas",
                            "WF" => "Wallis y Fortuna",
                            "YE" => "Yemen",
                            "ZM" => "Zambia",
                            "ZW" => "Zimbabue"
				];

				$codigoPais = $_POST["nuevoPaisRecibir"];
				$nombrePais = "";

				// Validamos si el código existe en el array
				if (array_key_exists($codigoPais, $paises)) {
					$nombrePais = $paises[$codigoPais];
					
				} else {
					$nombrePais = "Código de país no encontrado.";
				}


				$tabla = "clientes";

				$datos = array("nombre" => $_POST["nuevoNombreCliente"],
								"NIT" => $_POST["nuevoNITCliente"],
								"DUI" => $_POST["nuevoDUICliente"],
								"NRC" => $_POST["nuevoNRCCliente"],
								"codPais" => $_POST["nuevoPaisRecibir"],
								"nombrePais" => $nombrePais,
								"tipoPersona" => $_POST["nuevoTipoPersona"],
								"codActividad" => $_POST["nuevoCodActividad"],
								"descActividad" => $_POST["nuevoDescActividad"],
								"tipo_cliente" => $_POST["nuevoTipoContribuyentes"],
							   "direccion" => $_POST["nuevaDireccionCliente"],
							   "departamento" => $_POST["nuevoDepartamentoCliente"],
							   "distrito" => $_POST["nuevoDistritoCliente"],
								"municipio" => $_POST["nuevoMunicipioCliente"],
							   "correo" => $_POST["nuevoCorreoCliente"],
							   "telefono" => $_POST["nuevoNumeroCliente"]
							   );

				$respuesta = ModeloClientes::mdlIngresarCliente($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El cliente ha sido creado correctamente!",
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
						title: "¡El cliente no se pudo crear!",
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
	REGISTRO DE MOTORISTA
	=============================================*/

	static public function ctrCrearMotorista(){

		if(isset($_POST["nuevoNombreMotorista"])){

			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.]+$/', $_POST["nuevoNombreMotorista"])) {

				$tabla = "motoristas";

				$datos = array("nombre" => $_POST["nuevoNombreMotorista"],
								"duiMotorista" => $_POST["nuevoDuiMotorista"],
								"placaMotorista" => $_POST["nuevoPlacaMotorista"]
							   );

				$respuesta = ModeloClientes::mdlIngresarMotorista($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El motorista ha sido creado correctamente!",
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
						title: "¡El motorista no se pudo crear!",
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
	REGISTRO DE PROVEEDOR
	=============================================*/

	static public function ctrCrearProveedor(){

		if(isset($_POST["nuevoNombreProveedor"])){

			if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ,.]+$/', $_POST["nuevoNombreProveedor"])) {

				$tabla = "proveedores";

				$datos = array("nombre" => $_POST["nuevoNombreProveedor"],
								"nit" => $_POST["nuevoNitProveedor"],
								"telefono" => $_POST["nuevoNumeroProveedor"],
								"correo" => $_POST["nuevoCorreoProveedor"],
								"direccion" => $_POST["nuevaDireccionProveedor"],
								"condicion_pago" => $_POST["nuevaCondicionProveedor"]
							   );

				$respuesta = ModeloClientes::mdlIngresarProveedor($tabla, $datos);
			
				if($respuesta == "ok"){

					echo '<script>

					swal({

						type: "success",
						title: "¡El proveedor ha sido creado correctamente!",
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
						title: "¡El proveedor no se pudo crear!",
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
	MOSTRAR CLIENTES
	=============================================*/

	static public function ctrMostrarClientes($item, $valor, $orden){

		$tabla = "clientes";

		$respuesta = ModeloClientes::MdlMostrarClientes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	static public function ctrMostrarClientesPaginados($busqueda, $limite, $offset){

		$tabla = "clientes";

		return ModeloClientes::mdlMostrarClientesPaginados($tabla, $busqueda, $limite, $offset);
	}

	static public function ctrContarClientesPaginados($busqueda){

		$tabla = "clientes";

		return ModeloClientes::mdlContarClientesPaginados($tabla, $busqueda);
	}

	/*=============================================
	MOSTRAR CLIENTES MIOS
	=============================================*/

	static public function ctrMostrarClientesMios($item, $valor, $orden){

		$tabla = "monitoreo";

		$respuesta = ModeloClientes::MdlMostrarClientesMios($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR MOTORISTAS
	=============================================*/

	static public function ctrMostrarMotoristas($item, $valor, $orden){

		$tabla = "motoristas";

		$respuesta = ModeloClientes::MdlMostrarClientes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR PROVEEDORES
	=============================================*/

	static public function ctrMostrarProveedores($item, $valor, $orden){

		$tabla = "proveedores";

		$respuesta = ModeloClientes::MdlMostrarClientes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR DATOS EMPRESARIALES
	=============================================*/

	static public function ctrMostrarEmpresas($item, $valor, $orden){

		$tabla = "emisor";

		$respuesta = ModeloClientes::MdlMostrarClientes($tabla, $item, $valor, $orden);

		return $respuesta;
	}

	/*=============================================
	EDITAR CLIENTE
	=============================================*/

	static public function ctrEditarCliente(){

		if(isset($_POST["editarNITCliente"])){  // Verifica que el campo exista

			if(trim($_POST["editarNITCliente"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El NIT no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "facturacion";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				$paises = [
					"AF" => "Afganistán",
					"AX" => "Aland",
					"AL" => "Albania",
					"DE" => "Alemania",
					"AD" => "Andorra",
					"AO" => "Angola",
					"AI" => "Anguila",
					"AQ" => "Antártica",
					"AG" => "Antigua y Barbuda",
					"AW" => "Aruba",
					"SA" => "Arabia Saudita",
					"DZ" => "Argelia",
					"AR" => "Argentina",
					"AM" => "Armenia",
					"AU" => "Australia",
					"AT" => "Austria",
					"AZ" => "Azerbaiyán",
					"BS" => "Bahamas",
					"BH" => "Bahrein",
					"BD" => "Bangladesh",
					"BB" => "Barbados",
					"BE" => "Bélgica",
					"BZ" => "Belice",
					"BJ" => "Benin",
					"BM" => "Bermudas",
					"BY" => "Bielorrusia",
					"BO" => "Bolivia",
					"BQ" => "Bonaire, Sint Eustatius and Saba",
					"BA" => "Bosnia-Herzegovina",
					"BW" => "Botswana",
					"BR" => "Brasil",
					"BN" => "Brunei",
					"BG" => "Bulgaria",
					"BF" => "Burkina Faso",
					"BI" => "Burundi",
					"BT" => "Bután",
					"CV" => "Cabo Verde",
					"KY" => "Caimán, Islas",
					"KH" => "Camboya",
					"CM" => "Camerún",
					"CA" => "Canadá",
					"CF" => "Centroafricana, República",
					"TD" => "Chad",
					"CL" => "Chile",
					"CN" => "China",
					"CY" => "Chipre",
					"VA" => "Ciudad del Vaticano",
					"CO" => "Colombia",
					"KM" => "Comoras",
					"CG" => "Congo",
					"CI" => "Costa de Marfil",
					"CR" => "Costa Rica",
					"HR" => "Croacia",
					"CU" => "Cuba",
					"CW" => "Curazao",
					"DK" => "Dinamarca",
					"DM" => "Dominica",
					"DJ" => "Djiboutí",
					"EC" => "Ecuador",
					"EG" => "Egipto",
					"SV" => "El Salvador",
					"AE" => "Emiratos Árabes Unidos",
					"ER" => "Eritrea",
					"SK" => "Eslovaquia",
					"SI" => "Eslovenia",
					"ES" => "España",
					"US" => "Estados Unidos",
					"EE" => "Estonia",
					"ET" => "Etiopía",
					"FJ" => "Fiji",
					"PH" => "Filipinas",
					"FI" => "Finlandia",
					"FR" => "Francia",
					"GA" => "Gabón",
					"GM" => "Gambia",
					"GE" => "Georgia",
					"GH" => "Ghana",
					"GI" => "Gibraltar",
					"GD" => "Granada",
					"GR" => "Grecia",
					"GL" => "Groenlandia",
					"GP" => "Guadalupe",
					"GU" => "Guam",
					"GT" => "Guatemala",
					"GF" => "Guayana Francesa",
					"GG" => "Guernsey",
					"GN" => "Guinea",
					"GQ" => "Guinea Ecuatorial",
					"GW" => "Guinea-Bissau",
					"GY" => "Guyana",
					"HT" => "Haití",
					"HN" => "Honduras",
					"HK" => "Hong Kong",
					"HU" => "Hungría",
					"IN" => "India",
					"ID" => "Indonesia",
					"IQ" => "Irak",
					"IE" => "Irlanda",
					"BV" => "Isla Bouvet",
					"IM" => "Isla de Man",
					"NF" => "Isla Norfolk",
					"IS" => "Islandia",
					"CX" => "Islas Navidad",
					"CC" => "Islas Cocos",
					"CK" => "Islas Cook",
					"FO" => "Islas Faroe",
					"GS" => "Islas Georgias del Sur y Sandwich del Sur",
					"HM" => "Islas Heard y McDonald",
					"FK" => "Islas Malvinas",
					"MP" => "Islas Marianas del Norte",
					"MH" => "Islas Marshall",
					"PN" => "Islas Pitcairn",
					"TC" => "Islas Turcas y Caicos",
					"UM" => "Islas Ultramarinas de E.E.U.U",
					"VI" => "Islas Vírgenes",
					"IL" => "Israel",
					"IT" => "Italia",
					"JM" => "Jamaica",
					"JP" => "Japón",
					"JE" => "Jersey",
					"JO" => "Jordania",
					"KZ" => "Kazajistán",
					"KE" => "Kenia",
					"KG" => "Kirguistán",
					"KI" => "Kiribati",
					"KW" => "Kuwait",
					"LA" => "Laos, República Democrática",
					"LS" => "Lesotho",
					"LV" => "Letonia",
					"LB" => "Líbano",
					"LR" => "Liberia",
					"LY" => "Libia",
					"LI" => "Liechtenstein",
					"LT" => "Lituania",
					"LU" => "Luxemburgo",
					"MO" => "Macao",
					"MK" => "Macedonia",
					"MG" => "Madagascar",
					"MY" => "Malasia",
					"MW" => "Malawi",
					"MV" => "Maldivas",
					"ML" => "Malí",
					"MT" => "Malta",
					"MA" => "Marruecos",
					"MQ" => "Martinica",
					"MU" => "Mauricio",
					"MR" => "Mauritania",
					"YT" => "Mayotte",
					"MX" => "México",
					"FM" => "Micronesia",
					"MD" => "Moldavia",
					"MC" => "Mónaco",
					"MN" => "Mongolia",
					"ME" => "Montenegro",
					"MS" => "Montserrat",
					"MZ" => "Mozambique",
					"MM" => "Myanmar",
					"NA" => "Namibia",
					"NR" => "Nauru",
					"NP" => "Nepal",
					"NI" => "Nicaragua",
					"NE" => "Níger",
					"NG" => "Nigeria",
					"NU" => "Niue",
					"NO" => "Noruega",
					"NC" => "Nueva Caledonia",
					"NZ" => "Nueva Zelanda",
					"OM" => "Omán",
					"NL" => "Países Bajos",
					"PK" => "Pakistán",
					"PW" => "Palaos",
					"PS" => "Palestina",
					"PA" => "Panamá",
					"PG" => "Papúa Nueva Guinea",
					"PY" => "Paraguay",
					"PE" => "Perú",
					"PF" => "Polinesia Francesa",
					"PL" => "Polonia",
					"PT" => "Portugal",
					"PR" => "Puerto Rico",
					"QA" => "Qatar",
					"GB" => "Reino Unido",
					"KR" => "República de Corea",
					"CZ" => "República Checa",
					"DO" => "República Dominicana",
					"IR" => "República Islámica de Irán",
					"RE" => "Reunión",
					"RW" => "Ruanda",
					"RO" => "Rumania",
					"RU" => "Rusia",
					"EH" => "Sahara Occidental",
					"BL" => "Saint Barthélemy",
					"MF" => "Saint Martin (French part)",
					"SB" => "Salomón, Islas",
					"WS" => "Samoa",
					"AS" => "Samoa Americana",
					"KN" => "San Cristóbal y Nieves",
					"SM" => "San Marino",
					"PM" => "San Pedro y Miquelón",
					"VC" => "San Vicente y las Granadinas",
					"SH" => "Santa Elena",
					"LC" => "Santa Lucía",
					"ST" => "Santo Tomé y Príncipe",
					"SN" => "Senegal",
					"RS" => "Serbia",
					"SC" => "Seychelles",
					"SL" => "Sierra Leona",
					"SG" => "Singapur",
					"SX" => "Sint Maarten (Dutch part)",
					"SY" => "Siria",
					"SO" => "Somalía",
					"SS" => "South Sudan",
					"LK" => "Sri Lanka",
					"ZA" => "Sudáfrica",
					"SD" => "Sudán",
					"SE" => "Suecia",
					"CH" => "Suiza",
					"SR" => "Surinam",
					"SJ" => "Svalbard y Jan Mayen",
					"SZ" => "Swazilandia",
					"TH" => "Tailandia",
					"TW" => "Taiwán",
					"TZ" => "Tanzania",
					"TJ" => "Tayikistán",
					"IO" => "Territorio Británico Océano Índico",
					"TF" => "Territorios Australes Franceses",
					"TL" => "Timor Oriental",
					"TG" => "Togo",
					"TK" => "Tokelau",
					"TO" => "Tonga",
					"TT" => "Trinidad y Tobago",
					"TN" => "Túnez",
					"TM" => "Turkmenistán",
					"TR" => "Turquía",
					"TV" => "Tuvalu",
					"UA" => "Ucrania",
					"UG" => "Uganda",
					"UY" => "Uruguay",
					"UZ" => "Uzbekistán",
					"VU" => "Vanuatu",
					"VE" => "Venezuela",
					"VN" => "Vietnam",
					"VG" => "Islas Vírgenes Británicas",
					"WF" => "Wallis y Fortuna",
					"YE" => "Yemen",
					"ZM" => "Zambia",
					"ZW" => "Zimbabue"
		];

		$codigoPais = $_POST["editarPaisRecibir"];
		$nombrePais = "";

		// Validamos si el código existe en el array
		if (array_key_exists($codigoPais, $paises)) {
			$nombrePais = $paises[$codigoPais];
			
		} else {
			$nombrePais = "Código de país no encontrado.";
		}


				$tabla = "clientes";
		
				$datos = array("nombre" => $_POST["editarNombreCliente"],
							   "direccion" => $_POST["editarDireccionCliente"],
							   "correo" => $_POST["editarCorreoCliente"],
							   "NIT" => $_POST["editarNITCliente"],
							   "DUI" => $_POST["editarDUICliente"],
							   "NRC" => $_POST["editarNRCCliente"],
							   "codPais" => $_POST["editarPaisRecibir"],
								"nombrePais" => $nombrePais,
								"tipoPersona" => $_POST["editarTipoPersona"],
							   "codActividad" => $_POST["editarCodActividad"],
								"descActividad" => $_POST["editarDescActividad"],
							   "tipo_cliente" => $_POST["editarTipoContribuyentes"],
							   "id" => $_POST["editarIdCliente"],
							   "departamento" => $_POST["editarDepartamentoCliente"],
							   "distrito" => $_POST["editarDistritoCliente"],
								"municipio" => $_POST["editarMunicipioCliente"],
							   "telefono" => $_POST["editarNumeroCliente"]
							);
		
				$respuesta = ModeloClientes::mdlEditarCliente($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "El cliente ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
								if (result.value) {
		
								window.location = "facturacion";
		
								}
							})
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	EDITAR MOTORISTA
	=============================================*/

	static public function ctrEditarMotorista(){

		if(isset($_POST["editarnombreMotorista"])){  // Verifica que el campo exista

			if(trim($_POST["editarnombreMotorista"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "facturacion";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				$tabla = "motoristas";
		
				$datos = array("nombre" => $_POST["editarnombreMotorista"],
							   "duiMotorista" => $_POST["editarDuiMotorista"],
							   "placaMotorista" => $_POST["editarPlacaMotorista"],
							   "id" => $_POST["editarIdMotorista"]
							);
		
				$respuesta = ModeloClientes::mdlEditarMotorista($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "El motorista ha sido editado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
								if (result.value) {
		
								window.location = "facturacion";
		
								}
							})
		
					</script>';
				}
			}
		}
		

	}

	/*=============================================
	EDITAR PROVEEDOR
	=============================================*/

	static public function ctrEditarProveedor(){

		if(isset($_POST["editarnombreProveedor"])){  // Verifica que el campo exista

			if(trim($_POST["editarnombreProveedor"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "inventario";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				$tabla = "proveedores";
		
				$datos = array("nombre" => $_POST["editarnombreProveedor"],
							   "nit" => $_POST["editarNitProveedor"],
							   "id" => $_POST["editarIdProveedor"],
							   "telefono" => $_POST["editarNumeroProveedor"],
								"correo" => $_POST["editarCorreoProveedor"],
								"direccion" => $_POST["editarDireccionProveedor"],
								"condicion_pago" => $_POST["editarCondicionProveedor"]
							);
		
				$respuesta = ModeloClientes::mdlEditarProveedor($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "El proveedor ha sido editado correctamente",
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
	BORRAR CLIENTE
	=============================================*/

	static public function ctrBorrarCliente(){

		if(isset($_GET["idClienteEliminar"])){

			$tabla ="clientes";
			$datos = $_GET["idClienteEliminar"];

			$respuesta = ModeloClientes::mdlBorrarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El cliente ha sido borrado correctamente",
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

		}

	}

	/*=============================================
	BORRAR MOTORISTA
	=============================================*/

	static public function ctrBorrarMotorista(){

		if(isset($_GET["idMotoristaEliminar"])){

			$tabla ="motoristas";
			$datos = $_GET["idMotoristaEliminar"];

			$respuesta = ModeloClientes::mdlBorrarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El motorista ha sido borrado correctamente",
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

		}

	}

	/*=============================================
	BORRAR PROVEEDOR
	=============================================*/

	static public function ctrBorrarProveedor(){

		if(isset($_GET["idProveedorEliminar"])){

			$tabla ="proveedores";
			$datos = $_GET["idProveedorEliminar"];

			$respuesta = ModeloClientes::mdlBorrarCliente($tabla, $datos);

			if($respuesta == "ok"){

				echo'<script>

				swal({
					  type: "success",
					  title: "El proveedor ha sido borrado correctamente",
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

	/*=============================================
	EDITAR DATOS EMPRESA
	=============================================*/

	static public function ctrEditarEmpresa(){

		if(isset($_POST["editarNombreEmpresa"])){  // Verifica que el campo exista

			if(trim($_POST["editarNombreEmpresa"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El nombre no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
							window.location = "contabilidad";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				$tabla = "emisor";
		
				$datos = array("nit" => $_POST["editarNITEmpresa"],
							   "nrc" => $_POST["editarNRCEmpresa"],
							   "passwordPri" => $_POST["editarPasswordPriEmpresa"],
							   "nombre" => $_POST["editarNombreEmpresa"],
							   "codActividad" => $_POST["editarCodigoActividadEmpresa"],
							   "desActividad" => $_POST["editarActividadEmpresa"],
							   "tipoEstablecimiento" => $_POST["editarEstablecimientoEmpresa"],
							   "departamento" => $_POST["editarDepartamentoEmpresa"],
							   "distrito" => $_POST["editarDistritoEmpresa"],
							   "municipio" => $_POST["editarMunicipioEmpresa"],
							   "direccion" => $_POST["editarDireccionEmpresa"],
							   "telefono" => $_POST["editarNumeroEmpresa"],
							   "correo" => $_POST["editarCorreoEmpresa"],
							   "contra_descuentos" => $_POST["editarContraDescuento"],
							   "tipo_pdf" => in_array($_POST["editarTipoPdfEmpresa"], array("00", "01"), true) ? $_POST["editarTipoPdfEmpresa"] : "00",
							   "id" => $_POST["editarIdEmpresa"]
							);
		
				$respuesta = ModeloClientes::mdlEditarEmpresa($tabla, $datos);
		
				if($respuesta == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "Los datos empresariales han sido editados correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
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
	EDITAR ANCHO TICKET
	=============================================*/

	static public function ctrEditarTicket(){

		if(isset($_POST["editarAnchoTicket"])){  // Verifica que el campo exista

			if(trim($_POST["editarAnchoTicket"]) === ""){  // Verifica si el campo está vacío
				// Si el campo está vacío, muestra un error y evita el guardado
				echo'<script>
		
					swal({
						  type: "error",
						  title: "¡El ancho no puede ir vacío!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
							if (result.value) {
		
								window.location = "facturacion";
		
							}
						})
		
				  </script>';
		
			} else {  // Si el campo no está vacío, procede a guardar los datos

				$tabla = "emisor";
				$item1 = "ancho";
				$valor1 = $_POST["editarAnchoTicket"];
				$item2 = "id";
				$valor2 = "1";
				

				$actualizarNumeroControl = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
		
				if($actualizarNumeroControl == "ok"){
		
					echo'<script>
		
					swal({
						  type: "success",
						  title: "La configuración ha sido editada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result) {
								if (result.value) {
		
								window.location = "facturacion";
		
								}
							})
		
					</script>';
				}
			}
		}
		

	}

}
	


