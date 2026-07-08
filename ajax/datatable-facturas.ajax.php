<?php

require_once "../controladores/facturas.controlador.php";
require_once "../modelos/facturas.modelo.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

session_start();

if(!function_exists("puedeInvalidarDtePorPlazo")){
  function puedeInvalidarDtePorPlazo($tipoDte, $fecEmi, $horEmi = "00:00:00"){
    $fechaEmision = new DateTime(trim($fecEmi . ' ' . $horEmi));
    $fechaLimite = clone $fechaEmision;

    if(in_array($tipoDte, array("03", "05", "06"), true)){
      $fechaLimite->modify('+10 days')->setTime(23, 59, 59);
    } elseif(in_array($tipoDte, array("01", "11", "14"), true)){
      $fechaLimite->modify('+3 months')->setTime(23, 59, 59);
    } else {
      return false;
    }

    return new DateTime() <= $fechaLimite;
  }
}

class TablaFacturas{

 	/*=============================================
 	 MOSTRAR LA TABLA DE FACTURAS
  	=============================================*/ 

	public function mostrarTablaFacturas(){

		    $item = null;
        $valor = null;
        $orden = "fecEmi";
        $optimizacion = "no";

        $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

  		if(count($facturas) == 0){

  			echo '{"data": []}';

		  	return;
  		}   
		
  		$datosJson = '{
		  "data": [';


		  for($i = 0; $i < count($facturas); $i++){

			if($facturas[$i]["tipoDte"] == "05" || $facturas[$i]["tipoDte"] == "06" || $facturas[$i]["modo"] == "Contingencia"){

            } else {

                $item = "id";
                $valor = $facturas[$i]["id_cliente"];
                $orden = "id";

                $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);
                // Suponiendo que $facturas[$i]["fecha"] tiene el valor '2024-10-19 22:36:44'
                $fechaOriginal = new DateTime($facturas[$i]["fecEmi"]);
                $fechaFormateada = $fechaOriginal->format('d \d\e F \d\e Y'); // Formato deseado
                $firmaDigi = "";
                $sello = "";
                if($facturas[$i]["firmaDigital"] == ""){
                  $firmaDigi = "No";
                } else {
                  $firmaDigi = "Si";
                }

                if($facturas[$i]["sello"] ===  ""){
                  $sello = "No";
                } else {
                  $sello = "Si";
                }

                $tipoFacturaTexto = "";
                switch ($facturas[$i]["tipoDte"]) {
                  case "01":
                      $tipoFacturaTexto = "Factura";
                      break;
                  case "03":
                      $tipoFacturaTexto = "Comprobante de crédito fiscal";
                      break;
                  case "04":
                      $tipoFacturaTexto = "Nota de remisión";
                      break;
                  case "05":
                      $tipoFacturaTexto = "Nota de crédito";
                      break;
                  case "06":
                      $tipoFacturaTexto = "Nota de débito";
                      break;
                  case "07":
                      $tipoFacturaTexto = "Comprobante de retención";
                      break;
                  case "08":
                      $tipoFacturaTexto = "Comprobante de liquidación";
                      break;
                  case "09":
                      $tipoFacturaTexto = "Documento contable de liquidación";
                      break;
                  case "11":
                      $tipoFacturaTexto = "Factura de exportación";
                      break;
                  case "14":
                      $tipoFacturaTexto = "Factura de sujeto excluido";
                      break;
                  case "15":
                      $tipoFacturaTexto = "Comprobante de donación";
                      break;
      
                  default:
                      $tipoFacturaTexto = "Factura no válida";
                      break;
              }

              $tipo = "";
              if($cliente["tipo_cliente"] == "00"){
                $tipo = "Persona normal";
              }
              if($cliente["tipo_cliente"] == "01"){
                $tipo = "Declarante IVA";
              }
              if($cliente["tipo_cliente"] == "02"){
                $tipo = "Empresa con beneficios fiscales";
              }
              if($cliente["tipo_cliente"] == "03"){
                $tipo = "Diplomático";
              }

              $totalF = "";
              if($cliente["tipo_cliente"] ==  "00" && $facturas[$i]["tipoDte"] == "01"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "00" && $facturas[$i]["tipoDte"] == "03"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "00" && $facturas[$i]["tipoDte"] == "11"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "00" && $facturas[$i]["tipoDte"] == "14"){
                $totalF = $facturas[$i]["total"];
              }

              if($cliente["tipo_cliente"] ==  "01" && $facturas[$i]["tipoDte"] == "01"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "01" && $facturas[$i]["tipoDte"] == "03"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "01" && $facturas[$i]["tipoDte"] == "11"){
                $totalF = $facturas[$i]["totalSinIva"];
              }

              if($cliente["tipo_cliente"] ==  "02" && $facturas[$i]["tipoDte"] == "01"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "02" && $facturas[$i]["tipoDte"] == "03"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "02" && $facturas[$i]["tipoDte"] == "11"){
                $totalF = $facturas[$i]["totalSinIva"];
              }

              if($cliente["tipo_cliente"] ==  "03" && $facturas[$i]["tipoDte"] == "01"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "03" && $facturas[$i]["tipoDte"] == "03"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "03" && $facturas[$i]["tipoDte"] == "11"){
                $totalF = $facturas[$i]["totalSinIva"];
              }

              if($cliente["tipo_cliente"] ==  "01" && $facturas[$i]["tipoDte"] == "05"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "02" && $facturas[$i]["tipoDte"] == "05"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "03" && $facturas[$i]["tipoDte"] == "05"){
                $totalF = $facturas[$i]["totalSinIva"];
              }

              if($cliente["tipo_cliente"] ==  "01" && $facturas[$i]["tipoDte"] == "04"){
                $totalF = $facturas[$i]["total"];
              }
              if($cliente["tipo_cliente"] ==  "02" && $facturas[$i]["tipoDte"] == "04"){
                $totalF = $facturas[$i]["totalSinIva"];
              }
              if($cliente["tipo_cliente"] ==  "03" && $facturas[$i]["tipoDte"] == "04"){
                $totalF = $facturas[$i]["totalSinIva"];
              }

              $botones = "<div class='btn-group'><button class='btn btn-warning btnVerFactura' idFactura='".$facturas[$i]['id']."'><i class='fa fa-eye'></i></button></div>";

              // Configurar la zona horaria de El Salvador
              date_default_timezone_set('America/El_Salvador');

              // Fecha de emisión
              $fecEmi = $facturas[$i]["fecEmi"];

              // Obtener la fecha actual en el formato "Y-m-d"
              $fechaActual = date('Y-m-d');
              $puedeInvalidarDte = puedeInvalidarDtePorPlazo($facturas[$i]["tipoDte"], $facturas[$i]["fecEmi"], isset($facturas[$i]["horEmi"]) ? $facturas[$i]["horEmi"] : "00:00:00");

              // Crear un objeto DateTime con la fecha de emisión
              $fechaEmision = new DateTime($fecEmi);

              // Sumar 3 meses a la fecha de emisión
              $fechaLimite = $fechaEmision->modify('+3 months');

              // Verificar si la fecha actual es anterior a la fecha límite
              if ($fechaActual <= $fechaLimite) {
                // Aún no han pasado 3 meses
                // Verificar si la fecha actual coincide con `fecEmi`
                if ($fechaActual === $fecEmi) {
                    // Ejecutar la acción si la fecha coincide
                    
                    if($facturas[$i]["firmaDigital"] === ""){
                    $botones .= "<button class='btn btn-info btnFirmarDte' idFactura='".$facturas[$i]['id']."' ><i class='fa fa-pencil-square-o'></i></button><button class='btn btn-danger btnEliminarFactura' idFactura='".$facturas[$i]['id']."'><i class='fa fa-times'></i></button>";
                    } else {
                        if($facturas[$i]["sello"] === ""){
                            $botones .= "<button class='btn btn-success btnSellarDte' idFactura='".$facturas[$i]['id']."' ><i class='fa fa-arrow-circle-right'></i></button><button class='btn btn-danger btnEliminarFactura' idFactura='".$facturas[$i]['id']."'><i class='fa fa-times'></i></button>";
                        } else {

                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                            if($facturas[$i]["estado"] != "Anulada" && $puedeInvalidarDte){
                                $botones .= "<button class='btn btn-danger btnEliminarFacturaHacienda' idFactura='".$facturas[$i]['id']."'><i class='fa fa-times'></i></button>";
                            } else {

                            }
                        } else {

                        }
                        }
                        
                    }
                    if($facturas[$i]["sello"] != "" && $facturas[$i]["estado"] != "Anulada"){

                    if($facturas[$i]["tipoDte"] == "03") {
                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                            $botones .= "<button class='btn btn-info btnNotaCredito' idFactura='".$facturas[$i]['id']."'>NC</button><button class='btn btn-success btnNotaDebito' idFactura='".$facturas[$i]['id']."'>ND</button>";
                        }
                    }
                        if($facturas[$i]["notaRemision"] == "" && ($facturas[$i]["tipoDte"] == "03" || $facturas[$i]["tipoDte"] == "11")){
                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                            $botones .= "<button class='btn btn-dark btnNotaRemision' idFactura='".$facturas[$i]['id']."'>NR</button>";
                        }
                        }
            
                    
                    }
                } else {
                    if($facturas[$i]["sello"] != "" && $facturas[$i]["estado"] != "Anulada"){

                        if($facturas[$i]["tipoDte"] == "03") {
                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                            $botones .= "<button class='btn btn-info btnNotaCredito' idFactura='".$facturas[$i]['id']."'>NC</button><button class='btn btn-success btnNotaDebito' idFactura='".$facturas[$i]['id']."'>ND</button>";
                        }
                        }
                        if($facturas[$i]["notaRemision"] == "" && ($facturas[$i]["tipoDte"] == "03" || $facturas[$i]["tipoDte"] == "11")){
                            if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                                $botones .= "<button class='btn btn-dark btnNotaRemision' idFactura='".$facturas[$i]['id']."'>NR</button>";
                            }
                        }

                        if($_SESSION["rol"] == "Admin" || $_SESSION["rol"] == "Contabilidad"){
                            if($facturas[$i]["estado"] != "Anulada" && $puedeInvalidarDte){
                                $botones .= "<button class='btn btn-danger btnEliminarFacturaHacienda' idFactura='".$facturas[$i]['id']."'><i class='fa fa-times'></i></button>";
                            } else {

                            }
                        } else {

                        }                     
                        
                    } else {
                        $botones .= "<button class='btn btn-danger btnEliminarFactura' idFactura='".$facturas[$i]['id']."'><i class='fa fa-times'></i></button>";
                    }
                }
                } else {
                    // Han pasado más de 3 meses
                    
                }

              

                $datosJson .='[
                    "'.($i+1).'",
                    "'.$cliente["nombre"].'",
                    "'.$facturas[$i]["numeroControl"].'",
                    "'.$facturas[$i]["codigoGeneracion"].'",
                    "'.$firmaDigi.'",
                    "'.$sello.'",
                    "'.$tipoFacturaTexto.'",
                    "$'.$totalF.'",
                    "'.$facturas[$i]["estado"].'",
                    "'.$fechaFormateada.'",
                    "'.$botones.'"
                    ],';

                }

            

		  }

		  $datosJson = substr($datosJson, 0, -1);

		 $datosJson .=   '] 

		 }';
		
		echo $datosJson;


	}


}

/*=============================================
ACTIVAR TABLA DE COTIZACIONES
=============================================*/ 
$activarFacturas = new TablaFacturas();
$activarFacturas -> mostrarTablaFacturas();
