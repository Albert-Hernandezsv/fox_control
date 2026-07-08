<?php

require_once "../../../controladores/carros.controlador.php";
require_once "../../../modelos/carros.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/contratos.controlador.php";
require_once "../../../modelos/contratos.modelo.php";

require_once "../../../controladores/coberturas.controlador.php";
require_once "../../../modelos/coberturas.modelo.php";

require_once "../../../controladores/paquetes.controlador.php";
require_once "../../../modelos/paquetes.modelo.php";

if(isset($_GET["idContrato"]) && isset($_GET["idContrato"])){

		$item = "id";
	  	$valor = $_GET["idContrato"];
	  	$orden = "id";

		$contrato = ControladorContratos::ctrMostrarContratos($item, $valor, $orden);

		$item = "numero_placa";
	  	$valor = $contrato["numero_placa"];
	  	$orden = "id";

		$carro = ControladorCarros::ctrMostrarCarros($item, $valor, $orden);

        $itemCL = "id";
	  	$valorCL = $contrato["id_cliente"];
	  	$ordenCL = "id";

		$cliente = ControladorClientes::ctrMostrarClientes($itemCL, $valorCL, $ordenCL);

  }

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Title
        $this->Cell(28, 0, "+503-2362-7388 Oficina", 0, true, 'C', 0, ' ', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(38, 0, "+503-7308-4876 Reservaciones", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(38, 0, "+503-7420-3455 Emergencias", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(34, 0, "www.rentalyelsalvador.com", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(40, 0, " ", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        $this->Cell(34, 0, "info@rentalyelsalvador.com", 0, true, 'C', 0, '', 1, false, 'M', 'M');
        // Logo
        $image_file = K_PATH_IMAGES.'tcpdf_logo.jpg';
        $this->Image($image_file, 130, 10, 75, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);


// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Rentaly El Salvador');
$pdf->setTitle('Contrato '.$contrato["nombre_cliente"].'');
$pdf->setSubject('Reservación Rentaly El Salvador');
$pdf->setKeywords('	TCPDF, PDF, example, test, guide');


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);



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
$fechaEntregaOriginal = $contrato["fecha_entrega"];
$fechaFormateadaEntrega = fechaEnEspanol($fechaEntregaOriginal);

// Obtener la fecha de recogida en formato bonito
$fechaRecogidaOriginal = $contrato["fecha_recogida"];
$fechaFormateadareRecogida = fechaEnEspanol($fechaRecogidaOriginal);

// Obtengo el día total
$fechaInicio = new DateTime($contrato["fecha_entrega"]);
$fechaFin = new DateTime($contrato["fecha_recogida"]);
$diasTotales = $fechaInicio->diff($fechaFin);

$fechaEntrega = $contrato["fecha_entrega"];
$partesFechaEntrega = explode(" ", $fechaEntrega);

$fechaRecogida = $contrato["fecha_recogida"];
$partesFechaRecogida = explode(" ", $fechaRecogida);

// Coberturas
if($contrato["coberturas"] != "[]"){
    $coberturasCorchetesComas = $contrato["coberturas"];
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
    $coberturasTitulos = ["Sin coberturas adicionales a los paquetes", ""];
    $coberturasPrecios = [0,0];
    $totalCoberturas = 0;
}


// Paquetes
if($contrato["paquetes"] != "[]"){
    $paquetesCorchetesComas = $contrato["paquetes"];
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
$totalAlquiler = ($carro["precio_dia"]+$total)*$diasTotales->days;

$fechaVenceDocumento;
$numeroLicencia;
$fechaVenceLicencia;
$direccion;

if($cliente["fecha_vence_documento"] == "0000-00-00"){
    $fechaVenceDocumento = "Aún no ingresada";
} else {
    $fechaVenceDocumento = $cliente["fecha_vence_documento"];
}
if($cliente["fecha_vence_licencia"] == "0000-00-00"){
    $fechaVenceLicencia = "Aún no ingresada";
} else {
    $fechaVenceLicencia = $cliente["fecha_vence_licencia"];
}
if($cliente["numero_licencia"] == null){
    $numeroLicencia = "Aún no ingresado";
} else {
    $numeroLicencia = $cliente["numero_licencia"];
}

$conductorA;
$conductorAlicencia;
$conductoAvence;

if($contrato["conductor_adicional_nombre"] == null){
    $conductorA = "Sin asignar";
}
if($contrato["conductor_adicional_licencia"] == null){
    $conductorAlicencia = "Sin asignar";
}
if($contrato["conductor_adicional_licencia_vence"] == "0000-00-00 00:00:00"){
    $conductorAvence = "Sin asignar";
}

$firmarC = "";
if($contrato["firma"] == null){
    $firmarC = "Sin firmar&nbsp;&nbsp;&nbsp;&nbsp;";
} else {
    $firmarC = '<img src="'.$contrato["firma"].'">';
}
// create some HTML content
$html = '<br><br><div style="font-family: Times New Roman, Times, serif; font-size: 10px;">
<hr style="background-color:black">
<div style="text-align:center">
    <b>I-DATOS DEL CLIENTE(ARRENDATARIO)</b>
</div>
<hr style="background-color:gray">
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            <b>NOMBRE COMPLETO:</b> '.$contrato["nombre_cliente"].'
        </th>
        <th>
            <b>NACIONALIDAD:</b> '.$cliente["nacionalidad"].'
        </th>
    </tr>
    <tr>
        <th>
            <b>DOCUMENTO DE IDENTIDAD:</b> '.$cliente["documento"].'
        </th>
        <th>
            <b>FECHA DE EXPIRACIÓN:</b> '.$fechaVenceDocumento.'
        </th>
    </tr>
    <tr>
        <th>
            <b>DIRECCIÓN Y DOMICILIO:</b> '.$cliente["direccion"].'
        </th>
    </tr>
    <tr>
        <th>
            <b>LICENCIA:</b> '.$numeroLicencia.'
        </th>
        <th>
            <b>FECHA DE EXPIRACIÓN:</b> '.$fechaVenceLicencia.'
        </th>
    </tr>
    <tr>
        <th>
            <b>NÚMERO TELEFÓNICO:</b> '.$cliente["telefono"].'
        </th>
    </tr>
</table>
</div>
<hr style="background-color:gray"/>
<div style="text-align:center">
<b>II-CONDUCTORES AUTORIZADOS</b>
</div>
<hr style="color:black">
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            <b>NOMBRE COMPLETO:</b> '.$contrato["nombre_cliente"].'
        </th>
        <th>
            <b>LICENCIA N°:</b> '.$numeroLicencia.'
        </th>
        <th>
            <b>EXP:</b> '.$fechaVenceLicencia.'
        </th>
    </tr>
    <tr>
        <th>
            <b>NOMBRE COMPLETO:</b> '.$conductorA.'
        </th>
        <th>
            <b>LICENCIA N°:</b> '.$conductorAlicencia.'
        </th>
        <th>
            <b>EXP:</b> '.$conductorAvence.'
        </th>
    </tr>
</table>
</div>
<hr style="background-color:gray"/>
<div style="text-align:center">
<b>III-DATOS DE LA EMPRESA(ARRENDADORA)</b>
</div>
<hr style="color:black">
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            RENTALY EL SALVADOR, S.A. DE C.V.
        </th>
        <th>
            NIT: 
        </th>
    </tr>
    <tr>
        <th>
            DIRECCIÓN:
        </th>
    </tr>
</table>
</div>
<hr style="background-color:gray"/>
<div style="text-align:center">
<b>IV-DATOS DEL VEHÍCULO</b>
</div>
<hr style="color:black">
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            <b>PLACA:</b> '.$carro["numero_placa"].'
        </th>
        <th>
            <b>MODELO:</b> '.$carro["modelo"].'
        </th>
    </tr>
    <tr>
        <th>
            <b>TIPO:</b> '.$carro["especial"].'
        </th>
        <th>
            <b>AÑO:</b> '.$carro["ano"].'
        </th>
    </tr>
    <tr>
        <th>
            <b>MARCA:</b> '.$carro["marca"].'
        </th>
    </tr>
</table>
</div>
<hr style="background-color:gray"/>
<div style="text-align:center">
<b>V-DATOS MONETARIOS</b>
</div>
<hr style="color:black">
<table border="0" cellspacing="0" cellpadding="4">
<br><br>
    <tr>
        <th>
            <b>COSTO TOTAL:</b> $'.$contrato["monto_total"].'
        </th>
        <th>
            <b>METOD PAGO:</b> '.$contrato["forma_pago"].'
        </th>
        <th>
            <b>ABONO:</b> $'.$contrato["abono"].'
        </th>
    </tr>
    <tr>
        <th>
            <b>DEDUCIBLE:</b> $'.$contrato["deducible"].'
        </th>
        <th>
            <b>DEPÓSITO:</b> $'.$contrato["deposito"].'
        </th>
        <th>
            <b>RESTANTE:</b> $'.$contrato["pendiente"].'
        </th>
    </tr>
</table>
</div>
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
1: El arrendatario acepta todos los términos del contrato anteriormente firmado referente al vehículo entregado, siendo de su conocimiento
las consecuencias del incumplimiento de dichas dispocisiones por lo que firma a su completa satisfacción
</div>
<div style="text-align:center">
'.$firmarC.'
<br>
F: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
<br>
<div style="font-size:10px; font-family: Times New Roman, Times, serif">
Firma '.$contrato["nombre_cliente"].'
</div>
</div>
';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// create some HTML content
$html = '<br><br><div style="font-family: Times New Roman, Times, serif; font-size: 10px;">
<br><br><br><br>
Documentos del cliente:
<br><br>
<table>
    <tr>
        <th>
        Documento de identidad frente
        <br>';
            if($cliente["dui_foto_1"] == null){
                $html .= 'Sin documento registrado';
            } else {
                $html .= '<img width="250px" src="../../../'.$cliente["dui_foto_1"].'">';
            }
            $html .= '
        </th>
        <th>
        Documento de identidad atrás
        <br>';
            if($cliente["dui_foto_2"] == null){
                $html .= 'Sin documento registrado';
            } else {
                $html .= '<img width="250px" src="../../../'.$cliente["dui_foto_2"].'">';
            }
            $html .= '
        </th>
        
    </tr>
    <tr>
        <th>
        Licencia frente
        <br>';
                if($cliente["licencia_foto_1"] == null){
                    $html .= 'Sin documento registrado';
                } else {
                    $html .='<img width="250px" src="../../../'.$cliente["licencia_foto_1"].'">';
                }
                $html .= '
        </th>
        <th>
        Licencia atrás
        <br>';
                if($cliente["licencia_foto_2"] == null){
                    $html .= 'Sin documento registrado';
                } else {
                    $html .= '<img width="250px" src="../../../'.$cliente["licencia_foto_2"].'">';
                }
                $html .= '
        </th>
        
    </tr>
</table>
</div>';
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Contrato de '.$contrato["nombre_cliente"], 'I');

//============================================================+
// END OF FILE
//============================================================+
}
