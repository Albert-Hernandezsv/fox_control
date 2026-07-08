<?php

require_once "../../../controladores/facturas.controlador.php";
require_once "../../../modelos/facturas.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/productos.controlador.php";
require_once "../../../modelos/productos.modelo.php";

require_once "../../../controladores/usuarios.controlador.php";
require_once "../../../modelos/usuarios.modelo.php";

require_once '../../phpqrcode/qrlib.php';


if(isset($_GET["tipoFolio"]) && isset($_GET["nuevaFechaFolio"])){

    $item = null;
    $orden = "id";
    $valor = null;
    $optimizacion = "no";

    // Obtiene los datos de la factura
    $facturas = ControladorFacturas::ctrMostrarFacturas($item, $valor, $orden, $optimizacion);

    

    $item = "id";
    $orden = "id";
    $valor = "1";

    // Obtiene los datos de la factura
    $empresa = ControladorClientes::ctrMostrarEmpresas($item, $valor, $orden);

  }

require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
    }
}

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setTitle('Folio de ventas');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);



// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT); // Reducir margen superior a 5
$pdf->setHeaderMargin(5); // Reducir margen del encabezado a 5

$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

$filtroFecha = $_GET["nuevaFechaFolio"]; // Fecha en formato YYYY-MM

// Separar año y mes
list($anio, $mes) = explode("-", $filtroFecha);

// Array de nombres de meses
$meses = [
    "01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril",
    "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto",
    "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"
];

// Convertir el mes a texto
$mesTexto = $meses[$mes];

// create some HTML content
$html = '<div style="text-align: center; font-size: 20px; font-weight: bold">Libro de operaciones de ventas</div>
<div style="text-align: center">'.$empresa["nombre"].'</div>
<div style="text-align: center">Registro No'.$empresa["nrc"].' NIT: '.$empresa["nit"].'</div>
<div style="text-align: center">Mes: '.$mesTexto.' Año: '.$anio.'</div><br>';

// Obtener el mes y año del filtro
$filtro = "2024-12"; // $_GET["nuevaFechaFolio"];
list($anio, $mes) = explode("-", $filtro);
$diasDelMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

  // Generar la tabla HTML
  $html .= '<table border="1" style="text-align: center; font-size: 8px; width: 100%">
  <tr style="font-weight: bold">
      <th style="width: 20px">#</th>
      <th>Fecha de emisión</th>
      <th style="width: 150px">Número generación</th>
      <th style="width: 100px">CLiente</th>
      <th>Identificación</th>
      <th>Ventas exentas</th>
      <th>Ventas no sujetas</th>
      <th>Exportación</th>
      <th>Ventas gravadas</th>
      <th>IVA (13%)</th>
      <th>Total</th>
  </tr>
  </table>
  <table border="0" style="text-align: center; font-size: 8px; width: 100%">
  <tr style="font-weight: bold">
      <th style="width: 20px"></th>
      <th></th>
      <th style="width: 150px"></th>
      <th style="width: 100px"></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
  </tr>';
  $contador = 1;

  $totalGravadaFinal = 0.0;
  $totalExentaFinal = 0.0;
  $totalExportacionFinal = 0.0;
  $totalNoSujetaFinal = 0.0;

  $totalGravadaContribuyente = 0.0;
  $totalExentaContribuyente = 0.0;
  $totalExportacionContribuyente = 0.0;
  $totalNoSujetaContribuyente = 0.0;

  $totalIva = 0.0;
  $total = 0.0;
  foreach ($facturas as $key => $factura) {

  $gravada = 0.0;
  $exenta = 0.0;
  $exportacion = 0.0;
  $no_sujeta = 0.0;

  if (substr($factura["fecEmi"], 0, 7) == $_GET["nuevaFechaFolio"] && $factura["sello"] != "") {
    $item = "id";
    $valor = $factura["id_cliente"];
    $orden = "id";

    $cliente = ControladorClientes::ctrMostrarClientes($item, $valor, $orden);

    if($cliente["tipo_cliente"] ==  "00" && $factura["tipoDte"] == "01"){
      $gravada += $factura["total"];
      $totalGravadaFinal += $factura["total"];
    }
    if($cliente["tipo_cliente"] ==  "00" && $factura["tipoDte"] == "03"){
      
    }
    if($cliente["tipo_cliente"] ==  "00" && $factura["tipoDte"] == "11"){

    }
    if($cliente["tipo_cliente"] ==  "00" && $factura["tipoDte"] == "14"){
      
    }

    if($cliente["tipo_cliente"] ==  "01" && $factura["tipoDte"] == "01"){
      $gravada += $factura["total"];
      $totalGravadaContribuyente += $factura["total"];
    }
    if($cliente["tipo_cliente"] ==  "01" && $factura["tipoDte"] == "03"){
      $gravada += $factura["total"];
      $totalExentaContribuyente += $factura["total"];
    }
    if($cliente["tipo_cliente"] ==  "01" && $factura["tipoDte"] == "11"){
      $exportacion += $factura["totalSinIva"] + $factura["seguro"] + $factura["flete"];
      $totalExportacionContribuyente += $factura["total"];
    }

    if($cliente["tipo_cliente"] ==  "02" && $factura["tipoDte"] == "01"){
      $no_sujeta += $factura["totalSinIva"];
      $totalNoSujetaContribuyente += $factura["totalSinIva"];
    }
    if($cliente["tipo_cliente"] ==  "02" && $factura["tipoDte"] == "03"){
      $no_sujeta += $factura["totalSinIva"];
      $totalNoSujetaContribuyente += $factura["totalSinIva"];
    }
    if($cliente["tipo_cliente"] ==  "02" && $factura["tipoDte"] == "11"){
      $exportacion += $factura["totalSinIva"] + $factura["seguro"] + $factura["flete"];
      $totalExportacionContribuyente += $factura["totalSinIva"] + $factura["seguro"] + $factura["flete"];
    }

    if($cliente["tipo_cliente"] ==  "03" && $factura["tipoDte"] == "01"){
      $exenta += $factura["totalSinIva"];
      $totalExentaContribuyente += $factura["totalSinIva"];
    }
    if($cliente["tipo_cliente"] ==  "03" && $factura["tipoDte"] == "03"){
      $exenta += $factura["totalSinIva"];
      $totalExentaContribuyente += $factura["totalSinIva"];
    }
    if($cliente["tipo_cliente"] ==  "03" && $factura["tipoDte"] == "11"){
      $exportacion += $factura["totalSinIva"] + $factura["seguro"] + $factura["flete"];
      $totalExportacionContribuyente += $factura["totalSinIva"] + $factura["seguro"] + $factura["flete"];
    }
    $identificacionCliente = "";
    if($cliente["tipo_cliente"] == "00"){
    $identificacionCliente = $cliente["DUI"];
    } else {
    $identificacionCliente = $cliente["NIT"];
    }

      $html .= '<tr>
          <td>'.$contador.'</td>
          <td>'.$factura["fecEmi"].'</td>
          <td>'.$factura["codigoGeneracion"].'<br></td>
          <td>'.$cliente["nombre"].'<br></td>
          <td>'.$identificacionCliente.'</td>
          <td>$'.$exenta.'</td>
          <td>$'.$no_sujeta.'</td>
          <td>$'.$exportacion.'</td>
          <td>$'.($gravada/1.13).'</td>
          <td>$'.$gravada-($gravada/1.13).'</td>
          <td>'.$exenta+$no_sujeta+$exportacion+($gravada).'</td>
        </tr>';
        $totalIva += $gravada-($gravada/1.13);
        $total += $exenta+$no_sujeta+$exportacion+($gravada);
      $contador++;
  
  }


  }

  $html .= '<hr><br><br><br><tr style="font-weight: bold">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>$'.$totalExentaFinal+$totalExentaContribuyente.'</td>
                <td>$'.$totalNoSujetaFinal+$totalNoSujetaContribuyente.'</td>
                <td>$'.$totalExportacionFinal+$totalExportacionContribuyente.'</td>
                <td>$'.$totalGravadaFinal+$totalGravadaContribuyente.'</td>
                <td>$'.$totalIva.'</td>
                <td>$'.$total.'</td>
            </tr>
            
            <tr>
                <td><br><br></td>
                <td></td>
                <td>Ventas exentas</td>
                <td>Ventas gravadas</td>
                <td>Exportaciones</td>
                <td>No sujetas</td>
                <td>Debito fiscal</td>
            </tr>
            
            <tr>
                <td colspan="2">Consumidores finales<br></td>
                <td>$'.$totalExentaFinal.'</td>
                <td>$'.$totalGravadaFinal.'</td>
                <td>$'.$totalExportacionFinal.'</td>
                <td>$'.$totalNoSujetaFinal.'</td>
            </tr>
            
            <tr>
                <td colspan="2">Contribuyentes<br></td>
                <td>$'.$totalExentaContribuyente.'</td>
                <td>$'.$totalGravadaContribuyente.'</td>
                <td>$'.$totalExportacionContribuyente.'</td>
                <td>$'.$totalNoSujetaContribuyente.'</td>
            </tr>
            
            <tr>
                <td colspan="2">Totales</td>
                <td>$'.$totalExentaFinal+$totalExentaContribuyente.'</td>
                <td>$'.$totalGravadaFinal+$totalGravadaContribuyente.'</td>
                <td>$'.$totalExportacionFinal+$totalExportacionContribuyente.'</td>
                <td>$'.$totalNoSujetaFinal+$totalNoSujetaContribuyente.'</td>
                <td>$'.$totalIva.'</td>
            </tr>';

$html .= '</table>';




$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Folio de ventas.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

