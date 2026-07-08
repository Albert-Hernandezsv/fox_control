<?php

require_once "../../../controladores/carros.controlador.php";
require_once "../../../modelos/carros.modelo.php";

require_once "../../../controladores/cotizaciones.controlador.php";
require_once "../../../modelos/cotizaciones.modelo.php";

require_once "../../../controladores/coberturas.controlador.php";
require_once "../../../modelos/coberturas.modelo.php";

require_once "../../../controladores/paquetes.controlador.php";
require_once "../../../modelos/paquetes.modelo.php";

if(isset($_GET["idCotizacion"])){

		$item = "id";
	  	$valor = $_GET["idCotizacion"];
	  	$orden = "id";

		$cotizacion = ControladorCotizaciones::ctrMostrarCotizaciones($item, $valor, $orden);

		$item = "numero_placa";
	  	$valor = $cotizacion["numero_placa"];
	  	$orden = "id";

		$carro = ControladorCarros::ctrMostrarCarros($item, $valor, $orden);

        

  }

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('asdasd');
$pdf->setTitle('Cotización '.$cotizacion["nombre_cliente"].'');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, '28', $cotizacion["lugar_entrega"],  'Teléfono: '.$cotizacion["numero_cliente"]);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
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

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// Obtener la fecha de ahora
$dia = date('d');
$mes = date('m');
$ano = date('Y');

function fechaEnEspanol($fecha) {
    $meses = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    );

    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);

    return "$dia de $mes del $anio";
}
if($carro == null){
    $html = 'Carro eliminado';
    $pdf->writeHTML($html, true, false, true, false, '');
    
    //Close and output PDF document
    $pdf->Output('example_006.pdf', 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

} else {
// Obtener la fecha de entrega en formato bonito
$fechaEntregaOriginal = $cotizacion["fecha_entrega"];
$fechaFormateadaEntrega = fechaEnEspanol($fechaEntregaOriginal);

// Obtener la fecha de recogida en formato bonito
$fechaRecogidaOriginal = $cotizacion["fecha_recogida"];
$fechaFormateadareRecogida = fechaEnEspanol($fechaRecogidaOriginal);

// Obtengo el día total
$fechaInicio = new DateTime($cotizacion["fecha_entrega"]);
$fechaFin = new DateTime($cotizacion["fecha_recogida"]);
$diasTotales = $fechaInicio->diff($fechaFin);
// create some HTML content
$html = '<br><br><br><br><br><br><br><br><br><br><br>San Salvador '.$dia.' del '.$mes.' del '.$ano.'
<br>
<br>
Señor '.$cotizacion["nombre_cliente"].'
<br>
<br>
Presente
<br>
<br>
Para nosotros es un gusto poder saludarle y a la vez ofrecerle nuestro excelente servicio de
transporte detallando nuestras tarifas preferenciales y valores agregados.<br>
A continuación presentamos dos opciones para su mayor comodidad y satisfacción según solicitud
<br>
<br>
Periodo del '.$fechaFormateadaEntrega.' al '.$fechaFormateadareRecogida.': '.($diasTotales->days+1).' días (por cada 24 horas)
<br>
1 vehículo con capacidad de '.$carro["asientos"].' personas
';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Coberturas
if($cotizacion["coberturas"] != "[]"){
    $coberturasCorchetesComas = $cotizacion["coberturas"];
    // Remueve los corchetes y luego divide la cadena por la coma
    $coberturasComas = str_replace(["[", "]"], "", $coberturasCorchetesComas);
    $coberturasArray = explode(",", $coberturasComas);
    $coberturasTitulos;
    $coberturasPrecios;
    $totalCoberturas = 0;
    for($i=0; $i<count($coberturasArray); $i++){
    $item = "id";
    $valor = $coberturasArray[$i];
    $orden = "id";

    $cobertura = ControladorCoberturas::ctrMostrarCoberturas($item, $valor, $orden);
    $coberturasTitulos[$i] = $cobertura["nombre"];
    $coberturasPrecios[$i] = $cobertura["precio"];
    $totalCoberturas += $cobertura["precio"];
    }
} else {
    $coberturasTitulos = ["Sin coberturas seleccionadas", ""];
    $coberturasPrecios = [0,0];
    $totalCoberturas = 0;
}


// Paquetes
if($cotizacion["paquetes"] != "[]"){
    $paquetesCorchetesComas = $cotizacion["paquetes"];
    // Remueve los corchetes y luego divide la cadena por la coma
    $paquetesComas = str_replace(["[", "]"], "", $paquetesCorchetesComas);
    $paquetesArray = explode(",", $paquetesComas);
    $paquetesTitulos;
    $paquetesPrecios;
    $totalPaquetes = 0;
    for($i=0; $i<count($paquetesArray); $i++){
        $item = "id";
        $valor = $paquetesArray[$i];
        $orden = "id";

        $paquetes = ControladorPaquetes::ctrMostrarPaquetes($item, $valor, $orden);
        $paquetesTitulos[$i] = $paquetes["nombre"];
        $paquetesPrecios[$i] = $paquetes["precio"];
        $totalPaquetes += $paquetes["precio"];
    }
} else {
    $paquetesTitulos = ["Sin paquetes seleccionados", ""];
    $paquetesPrecios = [0,0];
    $totalPaquetes = 0;
}   

$total = $totalCoberturas + $totalPaquetes;
// Print a table

$html = '<br><br>
<table border="0.5" cellspacing="0" cellpadding="4">
	<tr>
		<th align="center"><b>'.$carro["marca"].' '.$carro["modelo"].' '.$carro["ano"].'</b></th>
		<th align="center"><b>Coberturas y paquetes</b></th>
		<th align="center"><b>Precio sin IVA</b></th>
	</tr>
	<tr>
		<th align="center"><img src="../../../'.$carro["imagen1"].'"></th>
		<th align="left"><b>Coberturas individuales:</b><br>'.implode('<br>', $coberturasTitulos).'<br><br><b>Paquetes:</b><br>'.implode('<br>', $paquetesTitulos).'</th>
		<th align="left"><b>Coberturas:</b><br>$'.implode('<br>$', $coberturasPrecios).'<br><br><b>Paquetes:</b><br>$'.implode('<br>', $paquetesPrecios).'<br><br><b>Total coberturas por los '.($diasTotales->days+1).' días:</b><br>$'.($total*($diasTotales->days+1)).'<br><br><b>Total alquiler por los '.($diasTotales->days+1).' días</b><br>$'.($carro["precio_dia"]*($diasTotales->days+1))+($total*($diasTotales->days+1)).'</th>
	</tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

$html = ' <br><br><br>Quedamos atentos a su elección del paquete que desee tomar y de antemano agradecemos su
preferencia<br><br>Contacto personalizado:<br>'.$cotizacion["nombre_vendedor"].'<br>Tel: +'.$cotizacion["numero_vendedor"].'<br>Correo electrónico: '.$cotizacion["correo_vendedor"].'';
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
}
