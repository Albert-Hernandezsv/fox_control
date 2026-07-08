<?php

require_once "../../../controladores/carros.controlador.php";
require_once "../../../modelos/carros.modelo.php";

require_once "../../../controladores/clientes.controlador.php";
require_once "../../../modelos/clientes.modelo.php";

require_once "../../../controladores/contratos.controlador.php";
require_once "../../../modelos/contratos.modelo.php";

require_once "../../../controladores/inspecciones.controlador.php";
require_once "../../../modelos/inspecciones.modelo.php";

if(isset($_GET["idContrato"]) && isset($_GET["tipo"])){

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

        $itemIns = null;
        $valorIns = null;

        $inspecciones = ControladorInspecciones::ctrMostrarInspecciones($itemIns, $valorIns);
        $inspeccion;

        foreach($inspecciones as $inspec){
            if($inspec["contrato_id"] == $_GET["idContrato"] && $inspec["tipo"] == $_GET["tipo"]){
            $inspeccion = $inspec;
            }
        }
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
$pdf->setTitle('Inspección de '.$_GET["tipo"].' '.$contrato["contrato"].'');
$pdf->setSubject('Inspección Rentaly El Salvador');
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

$firmarIns = "";
if($inspeccion["firma"] == null){
    $firmarIns = "Sin firmar&nbsp;&nbsp;&nbsp;&nbsp;";
} else {
    $firmarIns = '<img src="'.$inspeccion["firma"].'">';
}
// create some HTML content
$html = '<br><br><div style="font-family: "Arial", sans-serif;; font-size: 10px;">
<h4>Realizada el <div style="text-align:right">'.$inspeccion["fecha"].'</div></h4>
<hr>
<br><br>
<h4>Nombre del cliente <div style="text-align:right">'.$contrato["nombre_cliente"].'</div></h4>
<hr>
<b>Modelo y placa</b>
<br>
'.$carro["marca"].''.$carro["modelo"].'
<br>
'.$carro["numero_placa"].'
<br>
';

// Directorio donde se encuentran las imágenes
$directorio = "../../../vistas/img/fotos";

$idContrato = $_GET["idContrato"];
$tipo = $_GET["tipo"];


// Obtener todas las imágenes en el directorio con extensiones PNG y JPG
$imagenes = glob($directorio . "/*.{png,jpg}", GLOB_BRACE);

// Filtrar la lista para encontrar las imágenes específicas
$imagenesEncontradas = [];
foreach ($imagenes as $imagen) {
    $nombreArchivo = basename($imagen);

    // Utilizar una expresión regular para hacer coincidir el patrón deseado
    if (preg_match("/^[^#]+-$tipo+-$idContrato-([^#]+\.png|[^#]+\.jpg)$/i", $nombreArchivo)) {
        $imagenesEncontradas[] = $imagen;
    }
}

$html .= '
<hr>
<b>Fuel level y odometro</b>
<br><br>
<img src="'.$imagenesEncontradas[0].'" width="100px">
<hr>
<b>Se entregó tarjeta de circulación</b>
<br><br>
<img src="'.$imagenesEncontradas[2].'" width="100px">
<hr>';

// Verificar si se encontraron imágenes
if (!empty($imagenesEncontradas)) {
    $html .= '<b>Fotos obligatorias</b><br><br><table border="0" cellspacing="0" cellpadding="4">';
$rowCount = 0;

foreach ($imagenesEncontradas as $imagenEncontrada) {
    $indice = key($imagenesEncontradas);
    // Abre una nueva fila en cada cuarta imagen
    if ($rowCount % 4 == 0) {
        $html .= '<tr>';
    }

    $html .= '<td>
        <img src="'.$imagenEncontrada.'" width="100px">
        <p>Foto '.($indice + 1).'</p>
        <br>
    </td>';

    // Cierra la fila en cada cuarta imagen
    if ($rowCount % 4 == 3) {
        $html .= '</tr>';
    }
    next($imagenesEncontradas);
    $rowCount++;
}
// Si no se cerró la última fila completamente
if ($rowCount % 4 != 0) {
    $remainingCols = 4 - ($rowCount % 4);
    for ($i = 0; $i < $remainingCols; $i++) {
        $html .= '<td></td>';
    }
    $html .= '</tr>';
}

$html .= '</table>';
} else {
    echo "No se encontraron imágenes.";
}

$html .= '
<br><br><br><br><br><br>
<div style="text-align:center">
'.$firmarIns.'
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

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Inspección de '.$_GET["tipo"].''.$contrato["contrato"].'', 'I');

//============================================================+
// END OF FILE
//============================================================+
}
