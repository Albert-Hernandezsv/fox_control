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
    $facturas = ControladorFacturas::ctrMostrarCompras($item, $valor, $orden, $optimizacion);

    

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
$html = '<div style="text-align: center; font-size: 20px; font-weight: bold">Libro de operaciones de compras</div>
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
      <th style="width: 150px">Número de comprobante</th>
      <th style="width: 100px">Número de registro</th>
      <th>Nit o Dui</th>
      <th>Nombre del proveedor</th>
      <th>Ventas exentas</th>
      <th>Ventas no sujetas</th>
      <th>Exportación</th>
      <th>Ventas gravadas</th>
      <th>Sujeto excluido</th>
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

  if (substr($factura["fecha"], 0, 7) == $_GET["nuevaFechaFolio"] && ($factura["tipo_documento"] == "01" || $factura["tipo_documento"] == "03" || $factura["tipo_documento"] == "11")) {

    $gravada += $factura["compras_internas_gravadas"];
    $totalGravadaFinal = number_format(($gravada/1.13), 2, '.', ',');

      $html .= '<tr>
          <td>'.$contador.'</td>
          <td>'.$factura["fecha"].'</td>
          <td>'.$factura["numero_documento"].'<br></td>
          <td>'.$factura["numero_resolucion"].'<br></td>
          <td>'.$factura["nit_nrc"].'</td>
          <td>'.$factura["nombre_proveedor"].'</td>
          <td>$'.$exenta.'</td>
          <td>$'.$no_sujeta.'</td>
          <td>$'.$exportacion.'</td>
          <td>$'.number_format(($gravada/1.13), 2, '.', ',').'</td>
          <td></td>
          <td>$'.number_format($gravada-($gravada/1.13), 2, '.', ',').'</td>
          <td>'.$exenta+$no_sujeta+$exportacion+($gravada).'</td>
        </tr>';
        $totalIva += number_format(($gravada-($gravada/1.13)), 2, '.', ',');
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
                <td></td>
                <td>$'.$totalExentaFinal+$totalExentaContribuyente.'</td>
                <td>$'.$totalNoSujetaFinal+$totalNoSujetaContribuyente.'</td>
                <td>$'.$totalExportacionFinal+$totalExportacionContribuyente.'</td>
                <td>$'.$totalGravadaFinal+$totalGravadaContribuyente.'</td>
                <td></td>
                <td>$'.$totalIva.'</td>
                <td>$'.$total.'</td>
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

